<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 2/11/15
 * Time: 11:49
 */

defined('JPATH_PLATFORM') or die;

class Mint
{
	static function _($text, $jsSafe = FALSE, $interpretBackSlashes = TRUE, $script = FALSE)
	{
		$key   = strtoupper(JFilterInput::getInstance(NULL, NULL, 1, 1)->clean(strip_tags($text), 'cmd'));
		$trans = JText::_($key, $jsSafe, $interpretBackSlashes, $script);

		if($key === $trans)
		{
			return $text;
		}

		return $trans;
	}
}