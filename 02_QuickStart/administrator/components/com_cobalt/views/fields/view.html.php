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
class CobaltViewFields extends JViewLegacy
{

	public function display($tpl = null)
	{
		JHtml::_('behavior.tooltip');

		$app = JFactory::getApplication();
		$uri = JFactory::getURI();
		$this->action = $uri->toString();

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		foreach($this->items as &$item)
		{
			$this->ordering[$item->group_id][] = $item->id;
		}
		$model = JModelLegacy::getInstance('Type', 'CobaltBModel', array(
			'ignore_request' => true
		));
		$this->type = $model->getItem($this->state->get('fields.type'));

		if(! $this->type->id)
		{
			JError::raiseNotice(100, 'Type not selected');
			$app->redirect('index.php?option=com_cobalt&view=types');
		}

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
		MRToolBar::title(JText::_('CFIELDS') . ': ' . $this->type->name, 'fields');
		JToolBarHelper::addNew('field.add');
		JToolBarHelper::editList('field.edit');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('fields.publish');
		JToolBarHelper::unpublishList('fields.unpublish');
		JToolBarHelper::custom('fields.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'fields.delete', 'Delete');
		JToolBarHelper::custom('types.close', 'cancel.png', 'cancel.png', 'JTOOLBAR_CLOSE', false);
		//MRToolBar::helpW('http://help.mintjoomla.com/cobalt/index.html?fields.htm', 1000, 500);

		MRToolBar::addSubmenu('types');

		JSubMenuHelper::setAction('index.php?option=com_cobalt&view=fields');

		JSubMenuHelper::addFilter(JText::_('CFILTERTYPE'), 'filter_type', JHtml::_('select.options', JHtml::_('cobalt.contenttypes'), 'value', 'text', $this->state->get('fields.type'), true));

		JSubMenuHelper::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array(
			'trash' => 0,
			'archived' => 0,
			'all' => 0
		)), 'value', 'text', $this->state->get('fields.state'), true));

		JSubMenuHelper::addFilter(JText::_('CFILTERFILTERTYPE'), 'filter_field', JHtml::_('select.options', JHtml::_('cobalt.fieldtypes'), 'value', 'text', $this->state->get('fields.ftype'), true));
		JSubMenuHelper::addFilter(JText::_('JOPTION_SELECT_ACCESS'), 'filter_access', JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('fields.access'), true));

	}

	protected function getSortFields()
	{
		return array(
			'f.field_type' => JText::_('CTYPE'),
			'f.label' => JText::_('CFIELDLABEL'),
			'g.title' => JText::_('CGROUPNAME'),
			'f.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'f.published' => JText::_('CSTATE'),
			'f.access' => JText::_('CACCESS'),
			't.id' => JText::_('ID'),
		);
	}
}
