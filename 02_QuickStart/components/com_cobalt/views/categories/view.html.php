<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class CobaltViewCategories extends JViewLegacy
{
    function display ($tpl = null)
    {
        
        $model = JModelLegacy::getInstance('Usercategories', 'CobaltModel');
        
        $app = JFactory::getApplication();
		$this->section_id = $app->getUserStateFromRequest('com_cobalt.usercategories.section_id', 'section_id', null, 'int');
        $this->section = ItemsStore::getSection($this->section_id);
        $this->items = $model->getItems();
        $this->state = $model->getState();
        $this->pagination = $model->getPagination();

        parent::display($tpl);
    }
    
}
?>