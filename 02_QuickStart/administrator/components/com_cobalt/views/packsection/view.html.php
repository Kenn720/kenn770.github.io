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
class CobaltViewPacksection extends JViewLegacy
{

	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item = $this->get('Item');

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root(TRUE) . '/libraries/mint/forms/style.css');

// 		JRequest::setVar('return', $this->state->get('groups.return'));

		$this->form = $this->get('Form');

		if($this->item->id)
		{
			$this->parameters = JModelLegacy::getInstance('Packsection', 'CobaltBModel')->getSectionForm($this->item->section_id, $this->item->params);
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

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
// 		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		JToolBarHelper::title(($isNew ? JText::_('CNEWPACKSECTION') : JText::_('CEDITPACKSECTION')), 'sections');

// 		if (!$checkedOut)
		{
			JToolBarHelper::apply('packsection.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('packsection.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('packsection.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		JToolBarHelper::cancel('packsection.cancel', 'JTOOLBAR_CANCEL');
		//MRToolBar::helpW('http://help.mintjoomla.com/cobalt/index.html?filters.htm', 1000, 500);
	}
}
