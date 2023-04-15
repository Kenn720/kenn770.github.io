<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controllerform');

class CobaltControllerGroup extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}

	public function getModel($name = '', $prefix = 'CobaltBModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function postSaveHook(JModelLegacy $model, $validData = array())
	{

		$table = JTable::getInstance('Group', 'CobaltTable');
		$table->reorder('type_id ='. $validData['type_id']);

		if($this->input->getBase64('return'))
		{
			$return = base64_decode(urldecode($this->input->getBase64('return')));
			$this->setRedirect($return.'&group='.$model->getState('group.id'));
			return;
		}
	}


	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		$allow = $user->authorise('core.create', 'com_cobalt.groups');

		if($allow === null)
		{
			return parent::allowAdd($data);
		}
		else
		{
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		return JFactory::getUser()->authorise('core.edit', 'com_cobalt.groups');
	}
}