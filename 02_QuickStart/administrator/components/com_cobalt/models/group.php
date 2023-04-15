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

class CobaltBModelGroup extends JModelAdmin
{
	public function __construct($config = array())
	{
		$this->option = 'com_cobalt';
		parent::__construct($config);
	}

	public function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		$type = $app->getUserStateFromRequest('com_cobalt.groups.groups.type', 'type_id', 0, 'int');
		$this->setState('groups.type', $type);

		$r = $app->getUserStateFromRequest('com_cobalt.groups.groups.return', 'return', '', 'string');
		$this->setState('groups.return', $r);

		parent::populateState($ordering = null, $direction = null);
	}

	public function getTable($type = 'Group', $prefix = 'CobaltTable', $config = array())
	{
		$db = JFactory::getDbo();
		include_once __DIR__.'/../tables/group.php';
		return new CobaltTableGroup($db);
	}

	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_cobalt.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_cobalt.edit.group.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}


	protected function getReorderConditions($table)
	{
		return array('type_id = ' . $table->type_id);
	}

	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_cobalt.group.'.(int) $record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_cobalt.group.'.(int) $record->id);
	}
}