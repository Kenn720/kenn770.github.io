<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controller');
jimport('joomla.application.component.controllerform');

class CobaltControllerTools extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}
	public function save($key = null, $urlVar = null)
	{
		$name = $this->input->get('name');
		$id = $this->input->get('id');
		$uri = JFactory::getURI();
		
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('mint', $name);

		$dispatcher->trigger('onToolExecute', array($name, $id));
		
		$this->setRedirect($uri->toString());
		$this->redirect();
		//parent::display();
	}
}
?>