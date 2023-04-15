<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
class JHTMLCUsers
{
	static public function wheretopost($record)
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();

		if(empty($record->id))
		{
			$ids[] = $user->get('id');
			$isme = true;
		}
		else
		{
			$db->setQuery("SELECT host_id FROM `#__js_res_record_repost` WHERE record_id = {$record->id} AND is_reposted = 0");
			$ids = $db->loadColumn();
			$isme = $user->get('id') == @$record->user_id;
		}
		if(!$ids)
		{
			$ids[] = $user->get('id');
		}
		JArrayHelper::toInteger($ids);

		$db->setQuery("SELECT u.id, uo.params AS prm
			FROM `#__users` AS u
			LEFT JOIN `#__js_res_user_options` AS uo ON uo.user_id = u.id
			WHERE u.id IN (".implode(',', $ids).") GROUP BY u.id");

		$default = $db->loadObjectList();

		foreach ($default as $key => $value) {
			$default[$key]->params = new JRegistry($value->prm);
		}

		ob_start();
		include dirname(__FILE__).'/users/wheretopost.php';
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public static function checkboxes($section, $default = array())
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select($section->params->get('personalize.author_mode', 'username').' as name, id');
		$query->from('#__users');
		$query->where("id IN(SELECT user_id FROM #__js_res_record WHERE section_id = {$section->id})");
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;
		$key = 0;
		foreach ($list AS $user)
		{
			$chekced = (in_array($user->id, $default) ? ' checked="checked"' : NULL);
			if($key % 4 == 0) $li[] = '<div class="row-fluid">';
			$li[] = sprintf('<div class="span3"><label class="checkbox"><input type="checkbox" id="ctag-%d" class="inputbox" name="filters[tags][]" value="%d"%s /> <label for="ctag-%d">%s</label></label></div>', $user->id, $user->id, $chekced, $user->id, $user->name);
			if($key % 4 == 3) $li[] = '</div>';
			$key++;
		}
		if($key % 4 != 0) $li[] = '</div>';

		return '<div class="container-fluid">'.implode(' ', $li).'</div>';
	}

	public static function select($section, $default = array())
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select($section->params->get('personalize.author_mode', 'username').' AS text, id as value');
		$query->from('#__users');
		$query->where("id IN(SELECT user_id FROM #__js_res_record WHERE section_id = {$section->id})");
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;
		array_unshift($list, JHtml::_('select.option', '', JText::_('CSELECTAUTH')));

		return JHtml::_('select.genericlist', $list, 'filters[users][]', null, 'value', 'text', $default);
	}

	public static function form($section, $default = array(), $params = array())
	{
		if(!is_object($section))
		{
			$section = ItemsStore::getSection($section);
		}
		if (! is_array($default) && ! empty($default))
		{
			$default = explode(',', $default);
		}
		$id = 'users';
		if (!empty($params))
		{
			$id = isset($params['id']) ? $params['id'] : $id;
		}
		ArrayHelper::clean_r($default);
		JArrayHelper::toInteger($default);
		if($default)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, '.$section->params->get('personalize.author_mode', 'username').' AS plain, '.$section->params->get('personalize.author_mode', 'username').' AS html');
			$query->from('#__users');
			$query->where("id IN(".implode(',', $default).")");

			$db->setQuery($query);
			$default = $db->loadObjectList();
		}

		$options['coma_separate'] = 0;
		$options['only_values'] = 1;
		$options['min_length'] = 1;
		$options['max_result'] = 10;
		$options['case_sensitive'] = 0;
		$options['unique'] = 1;
		$options['highlight'] = 1;
		$options['max_items'] = 100;

		$options['ajax_url'] = 'index.php?option=com_cobalt&task=ajax.users_filter&section_id='.$section->id.'&tmpl=component';
		$options['ajax_data'] = '';

		return JHtml::_('mrelements.listautocomplete', 'filters[users]', $id, $default, array(), $options);
	}
}