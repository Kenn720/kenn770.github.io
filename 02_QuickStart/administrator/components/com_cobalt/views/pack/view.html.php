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
class CobaltViewPack extends JViewLegacy
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
// 		JRequest::setVar('return', $this->state->get('groups.return'));

		$this->form = $this->get('Form');

		$params = new JForm('params', array('control' => 'params'));
		$params->loadFile(JPATH_COMPONENT_ADMINISTRATOR. DIRECTORY_SEPARATOR .'xml'. DIRECTORY_SEPARATOR .'pack.xml');
		$this->params_form = $params;
		$this->params_groups = array (
				'general' => 'General'
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
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		JToolBarHelper::title(($isNew ? JText::_('CNEWPACK') : JText::_('CEDITPACK')), 'packs');

		if (!$checkedOut){
			JToolBarHelper::apply('pack.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('pack.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('pack.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			if(!$isNew) JToolBarHelper::custom('pack.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		JToolBarHelper::cancel('pack.cancel', 'JTOOLBAR_CANCEL');
		//MRToolBar::helpW('http://help.mintjoomla.com/cobalt/index.html?filters.htm', 1000, 500);
	}
}
