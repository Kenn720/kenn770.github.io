<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.view' );

class CobaltViewSection extends JViewLegacy {

	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		JHtmlBehavior::framework ();

		$this->state = $this->get ( 'State' );
		$this->item = $this->get ( 'Item' );
		$this->form = $this->get ( 'Form' );
		$this->canDo = MRHelper::getActions ( 'section', $this->state->get ( 'section.id' ) );


		$params = new JForm('params', array('control' => 'params'));
		$params->loadFile(JPATH_COMPONENT_ADMINISTRATOR. DIRECTORY_SEPARATOR .'xml'. DIRECTORY_SEPARATOR .'section.xml');
		$this->params_form = $params;
		$this->params_groups = array (
		'general' => JText::_('FS_GENERAL'),
		'events' => JText::_('FS_EVENTPARAMS'),
		'personalize' => JText::_('FS_PERSPARAMS'),
		'more' => JText::_('FS_OTHERPARAMS'),
		);


		// Check for errors.
		if (count ( $errors = $this->get ( 'Errors' ) )) {
			JError::raiseError ( 500, implode ( "\n", $errors ) );
			return false;
		}

		/*	if(!$this->item || ($this->item && !$this->item->id))
		{
			JError::raiseNotice(100, 'Please, save a section');
		}
		*/

		$this->addToolbar ();
		parent::display ( $tpl );
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user = JFactory::getUser ();
		$isNew = (! $this->item || $this->item->id == 0);
		//$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $user->get ( 'id' ));

		JToolBarHelper::title ( ($isNew ? JText::_ ('CNEWSECTION' ) : JText::_ ('CEDITSECTION' ).': '.$this->item->name), ($isNew ? 'sections' : 'sections') );

		JToolBarHelper::apply ( 'section.apply', 'JTOOLBAR_APPLY' );
		JToolBarHelper::save ( 'section.save', 'JTOOLBAR_SAVE' );
		JToolBarHelper::custom ( 'section.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
		if (! $isNew)
			JToolBarHelper::custom ( 'section.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false );

		JToolBarHelper::cancel ( 'section.cancel', 'JTOOLBAR_CANCEL' );
		MRToolBar::helpW ( 'http://help.mintjoomla.com/cobalt/index.html?category.htm', 1000, 500 );
		JToolBarHelper::divider ();

		// Get the results for each action.
	//	$canDo = MRHelper::getActions ( 'com_cobalt', $this->item->id );


	}
}
