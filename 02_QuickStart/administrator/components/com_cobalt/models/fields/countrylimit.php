<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldCountrylimit extends JFormFieldList
{
	
	protected $type = 'countrylimit';

	protected function getOptions()
	{
		$db = JFactory::getDbo();
		$query = 'SELECT id AS value, name AS text' .
				' FROM #__js_res_country ORDER BY name ASC';
		$db->setQuery($query);
		$list = $db->loadObjectList();
		
		//$opt = JHtml::_('select.option', '', JText::_('CUNGROUPED'));
		//array_unshift($list, $opt);
		
		return $list;

	}

}