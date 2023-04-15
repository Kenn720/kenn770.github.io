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

class CobaltViewTypes extends JViewLegacy
{
    function display ($tpl = null)
    {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        
        if (!JFactory::getApplication()->input->getInt('section_id'))
        {
            JError::raiseWarning(404, JText::_('CNOSECTION'));
            return;
        }
        
        $model = JModelLegacy::getInstance('Section', 'CobaltModel'); 
        $this->types = $model->getSectionTypes(JFactory::getApplication()->input->getInt('section_id'));
        
        if($errors = $model->getErrors())
        {
        	foreach ($errors AS $error)
        		JError::raiseWarning(403, $error);
        	return FALSE;
        }
        
        if(count($this->types) == 1)
        {
        	$url = 'index.php?option=com_cobalt&view=form&type_id='.$this->types[0]->id.'&section_id='.JFactory::getApplication()->input->getInt('section_id');
        	$app->redirect(JRoute::_($url, FALSE));
        	return ;
        }
        
        parent::display($tpl);
    }
}
?>