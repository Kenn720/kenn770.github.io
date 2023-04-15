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

class CobaltControllerField extends JControllerForm
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
		return parent::getModel('Field', $prefix, $config);
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{

		$app = JFactory::getApplication();

		$append = '';
		if($t = $app->input->getInt('type_id'))
		$append = '&type_id='.$t;
		$append .= parent::getRedirectToItemAppend($recordId, $urlVar);
		return $append;

	}
	protected function getRedirectToListAppend()
	{
		$app = JFactory::getApplication();
		$append = '';
		if($t = $app->input->getInt('type_id'))
		$append = '&type_id='.$t;

		return $append.parent::getRedirectToListAppend();
	}

	public function postSaveHook(JModelLegacy $model, $data = array())
	{
		$db = JFactory::getDbo();
		$key = 'k'.md5($data['label'].'-'.$data['field_type']);

		$db->setQuery("UPDATE #__js_res_record_values SET field_key = '{$key}', field_type = '{$data['field_type']}', field_label = '". $db->escape($data['label']) ."' WHERE field_id = ".$model->getState('field.id'));
		$db->query();
	}

	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		$allow = $user->authorise('core.create', 'com_cobalt.fields');

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
		return JFactory::getUser()->authorise('core.edit', 'com_cobalt.fields');
	}
}