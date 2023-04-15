<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/commerce/mintpayabstract.php';
class MintPay
{

	/**
	 * 
	 * @param string $provider
	 * @return MintPayAbstract object
	 */
	public static function getInstance($field_id, $provider, $log)
	{
		static $instances = array();

		$key = "$field_id-$provider";
		
		if (isset($instances[$key]))
		{
			return $instances[$key];
		}
		$file = JPATH_ROOT . "/components/com_cobalt/gateways/{$provider}/{$provider}.php";
		if (!JFile::exists($file))
		{
			JError::raiseError(500, JText::sprintf('CGATEWAYNOTFOUND', $provider));
			return;
		}
		require_once $file;
		
		$class_name = 'MintPay' . ucfirst($provider);
		
		if (!class_exists($class_name))
		{
			JError::raiseError(500, JText::sprintf('CGATEWAYNOTFOUND', $provider));
			return;
		}	
		
		$lang = JFactory::getLanguage();
		$tag = $lang->getTag();
		if (!JFile::exists(JPATH_BASE . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $tag . DIRECTORY_SEPARATOR . $tag . '.com_cobalt_field_' . $provider . '.ini'))
		{
			$tag == 'en-GB';
		}		
		$lang->load('com_cobalt_gateway_' . $provider, JPATH_BASE, $tag);
		
		$instances[$key] = new $class_name();
		$instances[$key]->log = $log;
		$instances[$key]->provider = strtolower($provider);
		
		return $instances[$key];
	}
}