<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');


class CobaltBModelTools extends JModelAdmin
{
	var $_tools       = array();
	var $_form        = array();

	function __construct()
	{
	  	$this->_id = JFactory::getApplication()->input->getInt('id');
	  	$this->option = 'com_cobalt';
		parent::__construct();
	}

	public function getTools()
	{
		if (empty($this->_tools)) {

			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('mint');
			$results = $dispatcher->trigger('onToolsGetIcons', array('com_cobalt.tools'));
			$total = array();
			foreach ($results As $result)
				$total = array_merge($total, $result);

			$this->_tools = $total;
		}
		settype($this->_tools, 'array');
		return $this->_tools;
	}

	public function getTool()
	{
		if (empty($this->_tools)) {

			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('mint');
			$results = $dispatcher->trigger( 'onToolsGetIcons', array('com_cobalt.tools'));

			$total = array();
			foreach ($results As $result)
				$total = array_merge($total, $result);

			$this->_tools = $total;
		}

		foreach ($this->_tools AS $tool)
		{
			if(JRequest::getVar('id') == $tool->id)
			{
				$this->_tool = $tool;
				break;
			}

		}

		return $this->_tool;
	}

	public function getToolForm()
	{
		$tool = $this->getTool();
		$name = $tool->name;
		$id   = $tool->id;
		$form = '';

		if(!$name || !$id) return JText::_('C_MSG_TOOLFORMFAIL');

		$dispatcher =JDispatcher::getInstance();
		JPluginHelper::importPlugin( 'mint', $name);
		$form = $dispatcher->trigger( 'onToolGetForm', array('com_cobalt.tools', $form, $name, $id));

		return implode('', array_values($form));
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_cobalt.config', 'config', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
}