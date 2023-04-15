<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controllerform');

class CobaltControllerSection extends JControllerForm
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
		$task = $this->getTask();

		if($task == 'save2copy')
		{
			$new_id = $model->getState('section.id', 0);

			$new = JTable::getInstance('Section', 'CobaltTable');
			$new->load($new_id);
			$params = new JRegistry($new->params);
			$key    = md5(time() . '-' . $new_id);

			$this->_moveTmpl($params, 'markup', $key);
			$this->_moveTmpl($params, 'list', $key);
			$this->_moveTmpl($params, 'category', $key);
			$this->_moveTmpl($params, 'compare', $key);

			$new->params = $params->toString();
			$new->store();
		}
	}

	protected function allowAdd($data = array())
	{
		$user  = JFactory::getUser();
		$allow = $user->authorise('core.create', 'com_cobalt.sections');

		if($allow === NULL)
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
		return JFactory::getUser()->authorise('core.edit', 'com_cobalt.sections');
	}

	private function _moveTmpl(&$params, $name, $key)
	{
		$tmpl_name = $params->get('general.tmpl_'.$name);

		$file = JPATH_ROOT."/components/com_cobalt/configs/default_{$name}_{$tmpl_name}.json";

		if(JFile::exists($file))
		{
			$tmpl = explode('.', $tmpl_name);
			$dest = JPATH_ROOT."/components/com_cobalt/configs/default_{$name}_{$tmpl[0]}.{$key}.json";
			JFile::copy($file, $dest);

			$params->set('general.tmpl_'.$name, $tmpl[0].'.'.$key);
		}
		else
		{
			$params->set('general.tmpl_'.$name, 'default.'.$key);
		}
	}
}