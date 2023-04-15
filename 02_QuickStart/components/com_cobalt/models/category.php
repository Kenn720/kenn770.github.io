<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');

class CobaltModelCategory extends JModelAdmin
{
	
	function getTable($name = 'CobCategory', $prefix = 'CobaltTable', $options = array())
	{
		include_once JPATH_ROOT.'/administrator/components/com_cobalt/tables/cobcategory.php';
		return new CobaltTableCobCategory($this->_db);
	}
	
	public function getForm($data = array(), $loadData = true)
	{
	
	}
	
	public function getItem($id = NULL)
	{

		$category = parent::getItem($id);

		if(!$category)
		{
			return $this->getEmpty();
		}
		$category->params = new JRegistry($category->params);
		
		$descr = JHtml::_('content.prepare', $category->description);
		$descr = preg_split('#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i', $descr, 2);
		$category->descr_before = @$descr[0];
		$category->descr_after = @$descr[1];
		$category->descr_full = implode($descr);
		$category->link = 'index.php?option=com_cobalt&view=records&section_id=' . $category->section_id.'&cat_id='.$category->id.':'.$category->alias;
		
		return $category;
	}
	
	public function getEmpty()
	{
		$o = new stdClass();
		$o->id = 0;
		$o->title = NULL;
		$o->params = new JRegistry();
		
		return $o;
	}
	
	public function getCategoryRecords($cat_id)
	{
	
	}

}