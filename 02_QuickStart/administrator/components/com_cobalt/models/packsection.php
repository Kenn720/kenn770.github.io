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

class CobaltBModelPacksection extends JModelAdmin
{
	public function __construct($config = array())
	{
		$this->option = 'com_cobalt';
		parent::__construct($config);
	}

	public function getTable($type = 'Packs_sections', $prefix = 'CobaltTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_cobalt.packsection', 'packsection', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_cobalt.edit.'.$this->getName().'.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data)
	{
		return parent::save($data);
	}

	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_cobalt.packsection.'.(int) $record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_cobalt.packsection.'.(int) $record->id);
	}

	public function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		$pack = $app->getUserStateFromRequest('com_cobalt.packsections.pack', 'pack_id', 0, 'int');
		$this->setState('pack', $pack);

		parent::populateState();
	}

	public function getSectionForm($section_id, $default = array())
	{
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_cobalt'. DIRECTORY_SEPARATOR .'models');
		$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . 'packtype.xml';
		if(! JFile::exists($file))
		{
			echo "File not found: {$file}";
		}
		$section = ItemsStore::getSection($section_id);

		if(!is_object($section->params))
		{
			$section->params = new JRegistry($section->params);
		}

		$types = $section->params->get('general.type');
		settype($types, 'array');

		if(!count($types))
		{
			return JText::_('CNOTYPES');
		}

		$active = JHtml::_('bootstrap.startPane', 'typetabs', array('active' => 'type'.$types[0]));

		$out = array();
		$i = 0;
		foreach ($types as $type_id)
		{
			if(!$type_id)
			{
				continue;
			}
			$def = !empty($default['types'][$type_id]) ? $default['types'][$type_id] : array();
			$type = ItemsStore::getType($type_id);

			$li_out[] = '<li'.($i == 0 ? ' class="active"' : '').'>'. '<a onclick="jQuery(this).tab(\'show\');return false;" href="#type'.$type->id.'">'.$type->name.'</a></li>';

			$form = new JForm('params', array(
					'control' => 'params[types]['.$type_id.']'
			));

			$form->loadFile($file, true, 'config');

			$f = new SimpleXMLElement('<field name="categoryselect_ss" type="radio" class="btn-group"  default="1" label="XML_LABEL_SP_CATEGORYSELECT"><option value="0">CNO</option><option value="1">CYES</option></field>');

			$form->setField($f, 'list_tmpl');
			$div = array();
			$div[] = JHtml::_('bootstrap.addPanel', 'typetabs', 'type'.$type_id);//'<div class="tab-pane'.($i == 0 ? ' active' : '').'" id="type'.$type->id.'">';
			$div[] = MEFormHelper::renderFieldset($form, 'sp_type_templates', $def, null, 2, 1);
			$div[] = MEFormHelper::renderFieldset($form, 'sp_type_content', $def, null, 2, 1);
			$div[] = MEFormHelper::renderFieldset($form, 'sp_type_fields', $def, null, 2, 1);
			$div[] = JHtml::_('bootstrap.endPanel');

			$divs[] = implode('', $div);
			$i++;
		}
		$out[] = '<ul class="nav nav-tabs" id="typetabs">';
		$out[] = implode('', $li_out);
		$out[] = '</ul>';
		$out[] = $active;
		$out[] = implode('', $divs);
		$out[] = JHtml::_('bootstrap.endPane', 'typetabs');


		return implode('', $out);
	}

	private function _getTypeFieldNames($type_id)
	{
		if(!$type_id)
			return array();

		$db = JFactory::getDbo();
		$query = 'SELECT label FROM #__js_res_fields WHERE type_id = '.$type_id;
		$db->setQuery($query);
		return $db->loadRowList();
	}
}