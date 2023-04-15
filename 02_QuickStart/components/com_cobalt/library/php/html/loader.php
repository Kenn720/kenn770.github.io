<?php
/**
 * Cobalt by MintJoomla
* a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
* Author Website: http://www.mintjoomla.com/
* @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die();
require_once JPATH_ROOT. DIRECTORY_SEPARATOR .'libraries'. DIRECTORY_SEPARATOR .'joomla'. DIRECTORY_SEPARATOR .'form'. DIRECTORY_SEPARATOR .'fields'. DIRECTORY_SEPARATOR .'groupedlist.php';

class JHTMLLoader
{
	public static function clickover($rel, $attr = array())
	{
		ArrayHelper::clean_r($attr);
		
		$options = json_encode($attr);
		JFactory::getDocument()->addScript(JUri::root(TRUE).'/media/mint/js/bootstrap/clickover/clickover.js');
		JFactory::getDocument()->addScriptDeclaration("jQuery(document).ready(function(){
			jQuery('*[rel=\"{$rel}\"]').clickover({$options});
		});");
	}
}
