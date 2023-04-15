<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class CVideoAdapterScreencast extends CVideoAdapterAbstarct
{
	public function __construct($params)
	{
		$this->params = $params;
	}
	
	public function check($url)
	{
		//$i = preg_match('/.*screencast\.com\/(.*)/i', $url, $matches);
		//if($i)
		//{
			$this->key = $url;
			return true;
		//}
			
		//return false;
	}
	
	public function getHtml()
	{
		return $this->getObject($this->key);
	}
}