<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

JHTML::_('behavior.modal', 'a.cmodal');

jimport('joomla.application.component.view');
class CobaltViewField extends JViewLegacy
{

	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$type_id = $this->item->type_id ? $this->item->type_id  : $this->state->get('fields.type');
		//JRequest::setVar('type_id', $this->state->get('fields.type'));

		$this->form = $this->get('Form');
		$this->user = JFactory::getUser();
		$params = new JForm('params', array('control' => 'params'));
		$params->loadFile(JPATH_COMPONENT_ADMINISTRATOR. DIRECTORY_SEPARATOR .'xml'. DIRECTORY_SEPARATOR .'field.xml');
		$this->params_form = $params;

		$this->params_groups = array('core' => JText::_('FS_GENERAL'),
		'emerald' => JText::_('FS_EMERALDINTEGRATE')
		);

		if($this->item->id)
		{
			$this->parameters = JModelLegacy::getInstance('Field', 'CobaltBModel')->getFieldForm($this->item->field_type, $this->item->params);
		}

		$app->input->set('type_id', $type_id);

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

		JToolBarHelper::title(($isNew ? JText::_('CNEWFIELD') : JText::_('SEDITFIELD')), ($isNew ? 'field_new.png' : 'field_edit.png'));

		if (!$checkedOut){
			JToolBarHelper::apply('field.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('field.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('field.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			if(!$isNew) JToolBarHelper::custom('field.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		JToolBarHelper::cancel('field.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::divider();
	}
}
