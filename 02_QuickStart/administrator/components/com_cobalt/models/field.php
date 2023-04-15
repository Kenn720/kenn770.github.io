<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');

class CobaltBModelField extends JModelAdmin
{
	public function __construct($config = array())
	{
		$this->option = 'com_cobalt';
		parent::__construct($config);
	}

	public function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		$type = $app->getUserStateFromRequest('com_cobalt.fields.fields.type', 'type_id', 0, 'int');
		$this->setState('fields.type', $type);

		parent::populateState($ordering = null, $direction = null);
	}

	public function getTable($type = 'Field', $prefix = 'CobaltTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getFieldForm($field_type, $default = array())
	{
		$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . $field_type . DIRECTORY_SEPARATOR . $field_type . '.xml';
		if(! JFile::exists($file))
		{
			echo "File not found: {$file}";
		}

		$lang = JFactory::getLanguage();
		$tag = $lang->getTag();
		if($tag != 'en-GB')
		{
			if(! JFile::exists(JPATH_BASE . "/language/{$tag}/{$tag}.com_cobalt_field_{$field_type}.ini"))
			{
				$tag == 'en-GB';
			}
		}

		$lang->load('com_cobalt_field_' . $field_type, JPATH_ROOT, $tag, TRUE);

		$form = new JForm('params', array(
			'control' => 'params'
		));

		$form->loadFile($file, true, 'config');

		return MEFormHelper::renderGroup($form, $default, 'params', FORM_STYLE_TABLE, FORM_SEPARATOR_NONE);
	}

	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_cobalt.field', 'field', array(
			'control' => 'jform',
			'load_data' => $loadData
		));
		if(empty($form))
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_cobalt.edit.field.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function getItem($pk = null)
	{
		if($item = parent::getItem($pk))
		{
			//print_r($item->params);


			if(! is_array($item->params))
			{
				//echo $item->params;
				$registry = new JRegistry();
				$registry->loadString($item->params);
				$item->params = $registry->toArray();
			}
		}

		if(JRequest::getInt('group'))
		{
			$item->group_id = JRequest::getInt('group');
		}

		return $item;
	}

	protected function getReorderConditions($table)
	{
		return array('group_id = ' . $table->group_id, 'type_id = ' . $table->type_id);
	}

	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_cobalt.field.' . (int)$record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_cobalt.field.' . (int)$record->id);
	}

	public function changeState($task, &$pks, $value = 1)
	{
		$dispatcher = JEventDispatcher::getInstance();
		$user = JFactory::getUser();
		$table = $this->getTable();
		$pks = (array) $pks;


		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();

			if ($table->load($pk))
			{
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
					return false;
				}
			}
		}

		$params = new JRegistry($table->params);
		$param = str_replace('not', '', $task);
		$params->set('core.'.$param, $value);

		$table->params = $params->toString();
		$table->store();

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}