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
class CobaltViewType extends JViewLegacy
{

	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		JHtmlBehavior::framework();

		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');

		$params = new JForm('params', array(
			'control' => 'params'
		));
		$params->loadFile(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . 'type.xml');
		$this->params_form = $params;

		$this->params_groups = array(
			'properties' => JText::_('FS_GENERAL'),
			'submission' => JText::_('FS_SUBMISPARAMS'),
			'comments' => JText::_('FS_COMMPARAMS'),
			'emerald' => JText::_('FS_EMERALDINTEGRATE')
		);

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
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user = JFactory::getUser();
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		JToolBarHelper::title(($isNew ? JText::_('CNEWTYPE') : JText::_('CEDITTYPE').': '.$this->item->name), ($isNew ? 'type_new.png' : 'type_edit.png'));

		if(! $checkedOut)
		{
			JToolBarHelper::apply('type.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('type.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::save2new('type.save2new');
			if(! $isNew) JToolBarHelper::save2copy('type.save2copy');
		}
		JToolBarHelper::cancel('type.cancel', 'JTOOLBAR_CANCEL');
		//MRToolBar::helpW('http://help.mintjoomla.com/cobalt/index.html?filters.htm', 1000, 500);
		JToolBarHelper::divider();
	}
}
