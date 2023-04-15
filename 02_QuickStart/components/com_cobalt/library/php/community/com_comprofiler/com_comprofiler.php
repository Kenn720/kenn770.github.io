<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

include_once dirname(dirname(__FILE__)) . '/com_cobalt/com_cobalt.php';

class CCommunityCom_comprofiler extends CCommunityCom_cobalt
{
	function getName($id, $name, $section)
	{
		$out[0]['url']   = JRoute::_("index.php?option=com_comprofiler&task=userProfile&user=" . $id);
		$out[0]['label'] = HTMLFormatHelper::icon('user-silhouette.png') . ' ' . JText::_('CUSERPOFILE');

		return $out;
	}

	function getAvatar($id)
	{
		$db = JFactory::getDBO();

		static $users = array();
		if(array_key_exists($id, $users))
		{
			return $users[$id];
		}

		$sql = "SELECT avatar FROM #__comprofiler WHERE user_id = " . $id . " AND avatarapproved = 1";
		$db->setQuery($sql);
		$fname = $db->loadResult();

		$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'images/comprofiler/tn' . $fname;
		if(JFile::exists($file))
		{
			return JPath::clean($file);
		}
	}

	public function getDefaultAvatar()
	{
		$path = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_comprofiler/plugin/templates/default/images/avatar/tnnophoto_n.png';
		if(JFile::exists($path))
		{
			return $path;
		}
	}
}
