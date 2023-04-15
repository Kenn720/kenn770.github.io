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
/**
 * View information about cobalt.
 *
 * @package		Cobalt
 * @subpackage	com_cobalt
 * @since		6.0
 */
class CobaltViewTags extends JViewLegacy
{

	function display($tpl = null)
	{
		JHtml::_('behavior.tooltip');
		
		$uri = JFactory::getURI();
		$this->action = $uri->toString();
		
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('CTAGS'), 'tags');
		MRToolBar::addSubmenu('tags');
		MRToolBar::deleteList('Are you sure, you want to delete tag permanently?', 'tags.remove');
		JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS');
	}
}