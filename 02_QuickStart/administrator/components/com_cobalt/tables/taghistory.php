<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.table.table');

class CobaltTableTaghistory extends JTable
{
	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_tags_history', 'id', $_db);
	
	}
	
	public function bind($data, $ignore = '')
	{
		if (! isset($data['ctime']))
		{
			$data['ctime'] = JFactory::getDate()->toSql();
		}
		return parent::bind($data, $ignore);
	}
}
?>
