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
class CobaltViewSections extends JViewLegacy
{

	public function display($tpl = null)
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
		MRToolBar::title(JText::_('CSECTIONS' ), 'sections');
		JToolBarHelper::addNew('section.add');
		JToolBarHelper::editList('section.edit');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('sections.publish');
		JToolBarHelper::unpublishList('sections.unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('Sure?', 'sections.delete','Delete');
		JToolBarHelper::divider();
		//MRToolBar::helpW('http://help.mintjoomla.com/cobalt/index.html?categories.htm', 1000, 500);
		
		MRToolBar::addSubmenu('sections');
		
		JSubMenuHelper::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array(
		'trash' => 0,
		'archived' => 0,
		'all' => 0
		)), 'value', 'text', $this->state->get('filter.state'), true));
	}
	
	protected function getSortFields()
	{
		return array(
		'a.published' => JText::_('JSTATUS'),
		'a.id' => JText::_('ID'),
		'a.name' => JText::_('CSECTIONNAME'),
		);
	}
}
