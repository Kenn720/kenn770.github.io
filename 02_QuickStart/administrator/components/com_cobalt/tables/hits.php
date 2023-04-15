<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined( '_JEXEC' ) or die( 'Restricted access');

class CobaltTablehits extends JTable
{
	public function __construct( &$_db ) {
		parent::__construct( '#__js_res_hits', 'id', $_db );
	}
	
	public function check()
	{
		$user = JFactory::getUser();
		
		$this->ctime = JFactory::getDate()->toSql();
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->user_id = $user->get('id');
		
		return TRUE;
	}
	
}
?>
