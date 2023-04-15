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
 * View to edit a weblink.
 *
 * @package		Cobalt
 * @subpackage	com_cobalt
 * @since		6.0
 */
class CobaltViewRecords extends JViewLegacy
{

	protected $state;

	protected $item;

	protected $form;

	public function display($tpl = null)
	{
		JHtml::_('behavior.tooltip');

		$uri = JFactory::getURI();
		//var_dump($uri);
		$this->action = $uri->toString();

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		if(is_array($this->items))
		{
			foreach($this->items as &$item)
			{
				$item->categories = empty($item->categories) ? array() : json_decode($item->categories);
				settype($item->categories, 'array');
			}
		}
		$this->sections = $this->get('Sections');
		$this->types = $this->get('Types');

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		MRToolBar::addSubmenu('records');

		JToolBarHelper::title(JText::_('XML_TOOLBAR_TITLE_RECORDS'), 'generic.png');
		MRToolBar::addrrec();
		JToolBarHelper::unarchiveList('records.unpublish');
		JToolBarHelper::archiveList('records.archive');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('records.publish');
		JToolBarHelper::unpublishList('records.unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::custom('records.featured', 'featured', '', 'CFEATURE', true);
		//JToolBarHelper::Custom('records.unfeatured', 'unfeatured', '', 'CUNFEATURE', true);
		JToolBarHelper::divider();
		JToolBarHelper::custom('records.copy', 'copy.png', 'copy.png', 'CCOPY', true);
		MRToolBar::reset();
		//JToolBarHelper::deleteList(JText::_('C_TOOLBAR_CONFIRMDELET'), 'records.delete', 'Delete');

		JSubMenuHelper::setAction('index.php?option=com_cobalt&view=records');

		JSubMenuHelper::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array(
			'trash' => 0,
			'all' => 0
		)), 'value', 'text', $this->state->get('filter.state'), true));
		JSubMenuHelper::addFilter(JText::_('CFILTERSECTION'), 'filter_section', JHtml::_('select.options', $this->sections, 'value', 'text', $this->state->get('filter.section'), true));
		JSubMenuHelper::addFilter(JText::_('CCELECTFIELDTYPE'), 'filter_type', JHtml::_('select.options', $this->types, 'value', 'text', $this->state->get('filter.type'), true));
	}

	protected function getSortFields()
	{
		return array(
			'a.title' => JText::_('CTITLE'),
			'a.ctime' => JText::_('CCREATED'),
			'a.extime' => JText::_('CEXPIRE'),
			'a.mtime' => JText::_('CMODIFIED'),
			'a.id' => JText::_('ID'),
			'a.hits' => JText::_('CHITS'),
			'a.comments' => JText::_('CCOMMENTS'),
			'a.votes' => JText::_('CVOTES'),
			'a.favorite_num' => JText::_('CFAVORITED'),
			'a.published' => JText::_('JSTATUS'),
			'a.access' => JText::_('CACCESS'),
			't.name' => JText::_('CTYPE'),
			's.name' => JText::_('CSECTION'),
			'username' => JText::_('CAUTHOR'),
		);
	}
}
