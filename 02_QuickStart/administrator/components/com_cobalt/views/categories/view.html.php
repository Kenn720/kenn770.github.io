<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Categories view class for the Category package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class CobaltViewCategories extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $section;
	protected $_defaultModel = 'CobaltBModelCategories';

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		global $app;

		$this->section 		= JRequest::getInt('section_id');
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');



		if (!$this->section) {
			JError::raiseWarning(100, JText::_('C_MSG_SELECTSECTIO'));
			$app->redirect('index.php?option=com_cobalt&view=sections');

		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[$item->parent_id][] = $item->id;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		// Initialise variables.
		$section	= $this->state->get('filter.section');
		$canDo		= null;

		// Get the results for each action.
		$canDo = MRHelper::getActions('section', $this->state->get('filter.section'));

		MRToolBar::title(JText::_('CCATEGORIES' ).': '.ItemsStore::getSection($this->section)->name, 'categories');


		JToolBarHelper::addNew('category.add');
		JToolBarHelper::editList('category.edit');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('categories.publish');
		JToolBarHelper::unpublishList('categories.unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'categories.delete');
		JToolBarHelper::custom('categories.close','cancel', '', 'Close', false);
		JToolBarHelper::divider();
		//MRToolBar::helpW('http://help.mintjoomla.com/cobalt/index.html?categories.htm', 1000, 500);

		MRToolBar::addSubmenu('sections');

		$sections = array();
		$section_list = $this->get('Sections');
		foreach ($section_list as $val)
		{
			$sections[] = JHtml::_('select.option', $val->value, $val->text);
		}

		JSubMenuHelper::addFilter(JText::_('CFILTERSECTION'), 'filter_section', JHtml::_('select.options', $sections, 'value', 'text', $this->state->get('filter.section'), true));

		// Levels filter.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('J1'));
		$options[]	= JHtml::_('select.option', '2', JText::_('J2'));
		$options[]	= JHtml::_('select.option', '3', JText::_('J3'));
		$options[]	= JHtml::_('select.option', '4', JText::_('J4'));
		$options[]	= JHtml::_('select.option', '5', JText::_('J5'));
		$options[]	= JHtml::_('select.option', '6', JText::_('J6'));
		$options[]	= JHtml::_('select.option', '7', JText::_('J7'));
		$options[]	= JHtml::_('select.option', '8', JText::_('J8'));
		$options[]	= JHtml::_('select.option', '9', JText::_('J9'));
		$options[]	= JHtml::_('select.option', '10', JText::_('J10'));

		JSubMenuHelper::addFilter(JText::_('XML_SELECT_LEVEL'), 'filter_level', JHtml::_('select.options', $options, 'value', 'text', $this->state->get('filter.level'), true));

		JSubMenuHelper::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array(
		'trash' => 0,
		'archived' => 0,
		'all' => 0
		)), 'value', 'text', $this->state->get('filter.published'), true));

		JSubMenuHelper::addFilter(JText::_('JOPTION_SELECT_ACCESS'), 'filter_access', JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'), true));

	}

	protected function getSortFields()
	{
		return array(
		'a.lft' => JText::_('JGRID_HEADING_ORDERING'),
		'a.published' => JText::_('JSTATUS'),
		'a.title' => JText::_('CTITLE'),
		'a.access' => JText::_('JGRID_HEADING_ACCESS'),
		'language' => JText::_('JGRID_HEADING_LANGUAGE'),
		'a.id' => JText::_('ID'),
		);
	}
}
