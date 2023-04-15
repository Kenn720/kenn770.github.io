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

class CobaltViewCategory extends JViewLegacy
{
    function display($tpl = null)
    {
        
        if (! JFactory::getApplication()->input->getInt('section_id'))
		{
			JError::raiseWarning(500, JText::_('CNOSECTION'));
			return FALSE;
		}
		
        $model = JModelLegacy::getInstance('Usercategory', 'CobaltModel');
        
		$this->section = ItemsStore::getSection(JFactory::getApplication()->input->getInt('section_id'));
        
        $this->item = $model->getItem();
        $this->form = $model->getForm();
        $this->user = JFactory::getUser();
        
        parent::display($tpl);
    }
    
}
?>