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

class CobaltModelNotification extends JModelAdmin
{

	const TYPE_NEWCOMMENT = 1;

	public function getTable($type = 'Notification', $prefix = 'CobaltTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		return FALSE;
	}

	public function getItem($pk = null)
	{
		return $item;
	}

	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_cobalt.comment.'.(int) $record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_cobalt.comment.'.(int) $record->id);
	}

	public function getSections()
	{
		$user = JFactory::getUser();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT(ref_2)');
		$query->from('#__js_res_notifications');
		$query->where('user_id = ' . $user->id);
		$db->setQuery($query);
		return $db->loadColumn();
	}
}