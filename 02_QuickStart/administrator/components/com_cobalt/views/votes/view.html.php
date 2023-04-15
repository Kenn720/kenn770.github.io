<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined ( '_JEXEC' ) or die ( 'Restricted access');

jimport ( 'joomla.application.component.view');
jimport ( 'joomla.filesystem.folder');

JHTML::_ ( 'behavior.modal', 'a.modal');

class CobaltViewVotes extends JViewLegacy {
	
	public function display($tpl = null) {
		
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
	
	function prepareItems(&$items) {
		foreach ( $items as $key => $item ) {
			if (JFolder::exists ( JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_juser' )) {
				$item->user_href = JURI::root () . 'administrator/index.php?option=com_juser&view=user&task=edit&cid[]=' . $item->userid;
			} else {
				$item->user_href = JURI::root () . 'administrator/index.php?option=com_user&view=user&task=edit&cid[]=' . $item->userid;
			}
			
			$items [$key] = $item;
		}
	
	}
	
	protected function addToolbar()
	{
		MRToolBar::title(JText::_('CVOTES' ), 'votes.png');
		JToolBarHelper::deleteList('', 'votes.delete','Delete');

		MRToolBar::addSubmenu('votes');
		
		JSubMenuHelper::setAction('index.php?option=com_cobalt&view=votes');

		JSubMenuHelper::addFilter(JText::_('CFILERVOTETYPE'), 'filter_type',
			JHtml::_('select.options', JHtml::_('votes.types'), 'value', 'text', $this->state->get('filter.type')));
		JSubMenuHelper::addFilter(JText::_('CFILTERVOTE'), 'filter_votes',
			JHtml::_('select.options', JHtml::_('votes.values'), 'value', 'text', $this->state->get('filter.votes')));
		JSubMenuHelper::addFilter(JText::_('CFILTERSECTION'), 'filter_section',
			JHtml::_('select.options', JHtml::_('cobalt.sections'), 'value', 'text', $this->state->get('filter.section')));
	
  		//MRToolBar::helpW('http://help.mintjoomla.com/cobalt/index.html?comments.htm', 1000, 500);
	}
	
	protected function getSortFields()
	{
		return array(
			'a.id' => JText::_('ID'),
			'a.ctime' => JText::_('CVOTED'),
			'r.title' => JText::_('CRECORD'),
			'u.username' => JText::_('CUSER'),
			'a.vote' => JText::_('CVOTE'),
			'a.ref_type' => JText::_('CTYPE')
		);

	}

}