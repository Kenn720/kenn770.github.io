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

class CobaltBModelComment extends JModelAdmin
{
	public function __construct($config = array())
	{
		$this->option = 'com_cobalt';
		parent::__construct($config);
	}

	public function getTable($type = 'Cobcomments', $prefix = 'CobaltTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_cobalt.comment', 'comment', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_cobalt.edit.comment.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			if(!is_array($item->params))
			{
				$registry = new JRegistry;
				$registry->loadString($item->params);
				$item->params = $registry->toArray();
			}
		}

		return $item;
	}

	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_cobalt.filter.'.(int) $record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_cobalt.filter.'.(int) $record->id);
	}
}