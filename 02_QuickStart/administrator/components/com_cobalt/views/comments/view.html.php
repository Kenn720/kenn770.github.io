<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.filesystem.folder');

JHTML::_('behavior.modal', 'a.modal');

class CobaltViewComments extends JViewLegacy
{

	public function display($tpl = null)
	{

		JHtml::_('behavior.tooltip');

		$uri = JFactory::getURI();
		$this->action = $uri->toString();

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		/*
		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}*/

		$this->addToolbar();

		parent::display($tpl);

	}

	function prepareItems(&$items)
	{
		foreach($items as $key => $item)
		{
			if(JFolder::exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_juser'))
			{
				$item->user_href = JURI::root(TRUE) . '/administrator/index.php?option=com_juser&view=user&task=edit&cid[]=' . $item->userid;
			}
			else
			{
				$item->user_href = JURI::root(TRUE) . '/administrator/index.php?option=com_user&view=user&task=edit&cid[]=' . $item->userid;
			}

			$items[$key] = $item;
		}

	}

	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('CCOMMENTS'), 'comments.png');
		JToolBarHelper::editList('comment.edit');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('comments.publish');
		JToolBarHelper::unpublishList('comments.unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'comments.delete', 'Delete');

		MRToolBar::addSubmenu('comments');

		JSubMenuHelper::setAction('index.php?option=com_cobalt&view=comments');

		JSubMenuHelper::addFilter(JText::_('CFILTERRECORDTYPE'), 'filter_typeid',
			JHtml::_('select.options', JHtml::_('cobalt.recordtypes'), 'value', 'text', $this->state->get('filter.type')));
		JSubMenuHelper::addFilter(JText::_('CFILTERSECTION'), 'filter_section',
			JHtml::_('select.options', JHtml::_('cobalt.sections'), 'value', 'text', $this->state->get('filter.section')));
		JSubMenuHelper::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0,'trash' => 0,'all' => 0,)), 'value', 'text', $this->state->get('filter.state'), true));

	}

	protected function getSortFields()
	{
		return array(
			'id' => JText::_('ID'),
			'a.ctime' => JText::_('CCREATED'),
			'r.title' => JText::_('CRECORD'),
			'u.username' => JText::_('CUSER'),
			'a.published' => JText::_('JSTATUS'),
			'a.comment' => JText::_('CSUBJECT')
		);

	}

}