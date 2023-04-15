<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'toolbar.php';

class MRHelper
{
	public static function getActions($aname = '', $categoryId = 0)
	{
		$user = JFactory::getUser ();
		$result = new JObject ();
		
		$assetName = 'com_cobalt';
		if($aname != '') $assetName .= '.' . $aname;
		if ($categoryId)
		{
			if($aname == '') $assetName .= '.category';
			$assetName .= '.' . ( int ) $categoryId;
		}	
		/*  if (empty ( $categoryId )) {
			$assetName = 'com_cobalt';
		} else {
			$assetName = 'com_cobalt.category.' . ( int ) $categoryId;
		}*/
		
		$actions = array ('core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete' );
		foreach ( $actions as $action )
		{
			$result->set ( $action, $user->authorise ( $action, $assetName ) );
		}
		
		return $result;
	}
	
	static public function str_limit($str, $limit)
	{
		if ($str && strlen ( $str ) > $limit)
			return substr ( $str, 0, $limit - 3 ) . '...';
		else
			return $str;
	}
}
?>