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
class CobaltViewGroups extends JViewLegacy
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

		$model = JModelLegacy::getInstance('Type', 'CobaltBModel', array('ignore_request' => true));
		$this->type = $model->getItem($this->state->get('groups.type'));

		if(!$this->type->id)
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
		MRToolBar::title(JText::_('CGROUPS' ), 'groups');
		JToolBarHelper::addNew('group.add');
		JToolBarHelper::editList('group.edit');
		JToolBarHelper::divider();
		JToolBarHelper::custom('groups.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		JToolBarHelper::deleteList('', 'groups.delete','Delete');
		JToolBarHelper::cancel('groups.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::divider();
		//MRToolBar::helpW('http://help.mintjoomla.com/cobalt/index.html?fields.htm', 1000, 500);

		MRToolBar::addSubmenu('types');
	}
}
