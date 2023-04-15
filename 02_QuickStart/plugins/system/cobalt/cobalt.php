<?php
/**
 * Cobalt system plugin for Joomla
 * @version    $Id: jq_alphauserpoints.php 2009-11-16 17:30:15
 * @package    Coablt
 * @subpackage cobalt.php
 * @author     MintJoomla Team
 * @Copyright  Copyright (C) MintJoomla, www.mintjoomla.com
 * @license    GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemCobalt extends JPlugin
{
	function onJQuizFinished($params)
	{

		if(!$params['passed'])
		{
			return;
		}
		$db = JFactory::getDbo();
		$db->setQuery("SELECT record_id, field_id FROM #__js_res_record_values WHERE value_index = 'quiz' AND field_value = {$params['quiz_id']} AND field_type = 'dripcontent' GROUP BY record_id");
		$ids = $db->loadObjectList();

		if(empty($ids))
		{
			return;
		}

		$user = JFactory::getUser();

		JTable::addIncludePath(JPATH_ROOT . '/components/com_cobalt/fields/dripcontent/tables');
		$table = JTable::getInstance('Stepaccess', 'CobaltTable');

		foreach($ids AS $id)
		{

			$data['user_id']   = $user->get('id');
			$data['record_id'] = $id->record_id;
			$data['field_id']  = $id->field_id;

			$table->load($data);

			if(!$table->id)
			{
				$data['id']    = NULL;
				$data['ctime'] = JFactory::getDate()->toSql();
				$table->bind($data);
				$table->store();
			}

			$table->reset();
			$table->id = NULL;
		}
	}
	public function onUserAfterDelete($user, $success, $msg)
	{
		$this->db = JFactory::getDbo();

		$this->db->setQuery("SELECT id FROM `#__js_res_record` WHERE user_id = ".$user['id']);
		$records = $this->db->loadColumn();
		$records[] = 0;

		$this->db->setQuery("DELETE FROM `#__js_res_record`WHERE user_id = ".$user['id']);
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_audit_log` WHERE user_id = {$user['id']} OR record_id IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_audit_restore` WHERE record_id IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_audit_versions` WHERE user_id = {$user['id']} OR record_id IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_category_user` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_comments` WHERE user_id = {$user['id']} OR record_id IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_favorite` WHERE user_id = {$user['id']} OR record_id IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_hits` WHERE user_id = {$user['id']} OR record_id IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_import` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_moderators` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_notifications` WHERE user_id = {$user['id']} OR ref_1 IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_record_category` WHERE record_id IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_record_repost` WHERE record_id IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_record_values` WHERE record_id IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_sales` WHERE user_id = {$user['id']} OR record_id IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_subscribe` WHERE user_id = {$user['id']} OR (ref_id IN(".implode($records, ',').") AND `type` = 'record')");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_subscribe_cat` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_subscribe_user` WHERE user_id = {$user['id']} OR u_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_tags_history` WHERE user_id = {$user['id']} OR record_id IN(".implode($records, ',').")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_user_options` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_user_options_autofollow` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_user_post_map` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_vote` WHERE user_id = {$user['id']} OR (ref_id IN(".implode($records, ',').") AND `ref_type` = 'record')");
		$this->db->execute();

		return true;
	}
}
