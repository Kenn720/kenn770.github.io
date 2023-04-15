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
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class CobaltControllerConfig extends JControllerForm
{

	public function apply()
	{
		$this->save();
	}

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
		$task = $this->input->getCmd('task');
		$data = $this->input->get('jform', array(), 'array');

		if(isset($data['rules']))
		{
			foreach($data['rules'] AS $k => $rule)
			{
				foreach ($rule AS $k2 => $v)
				{
					if($v === '') unset($data['rules'][$k][$k2]);
				}
			}
			jimport('joomla.access.rules');
			$rules = new JRules($data['rules']);
			$asset = JTable::getInstance('asset');

			if(! $asset->loadByName('com_cobalt'))
			{
				$root = JTable::getInstance('asset');
				$root->loadByName('root.1');
				$asset->name = 'com_cobalt';
				$asset->setLocation($root->id, 'last-child');
				//$asset->parent_id = $root->id;
			}
			$asset->title = JText::_('CACLACCETNAMEMAIN');
			$asset->rules = (string)$rules;

			if(! $asset->check() || ! $asset->store())
			{
				$this->setError($asset->getError());
				return false;
			}

			unset($data['rules']);
		}


		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where("`type` = 'component'");
		$query->where("`element` = 'com_cobalt'");
		$db->setQuery($query);

		$id = $db->loadResult();

		$table = JTable::getInstance('extension');

		if(! $table->load($id))
		{
			JError::raiseWarning(100, $table->getError());
		}


		$registry = new JRegistry();
		$registry->loadArray($data);

		$table->params = (string)$registry;

		if(! $table->check())
		{
			JError::raiseWarning(100, $table->getError());
		}

		if(! $table->store())
		{
			JError::raiseWarning(100, $table->getError());
		}

		$cache = JFactory::getCache('_system');
		$cache->clean();

		$msg = JText::_('XML_MSG_CONFIGSAVEDSUCESSFULY');
		$app = JFactory::getApplication();
		switch($task)
		{
			case 'apply':
				$app->redirect('index.php?option=com_cobalt&view=config', $msg);
			break;
			case 'save':
			default:
				$app->redirect('index.php?option=com_cobalt', $msg);
			break;
		}
	}

	public function cancel($key = null)
	{
		$app = JFactory::getApplication();
		$app->redirect('index.php?option=com_cobalt');
	}

	static public function setVal($key, $value)
	{
		$config = CobaltControllerConfig::initialyze();

		$config->set($key, $value);

		$ini = $config->toString('ini');

		JFile::write(JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'config.ini');

		return true;
	}

	static public function initialyze()
	{	/*Static $configuration = null;

		if($configuration) return $configuration;

		$uri = JFactory::getURI();
		//$ini = JFile::read( JPATH_ROOT. DIRECTORY_SEPARATOR .'administrator'. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_cobalt'. DIRECTORY_SEPARATOR .'config.ini');

		$configuration = new JParameter($ini);

		$params_array = $configuration->toArray();

		foreach($params_array AS $key => $val){
			$name = strtoupper('res_'.$key);
			if(!defined(strtoupper($name))){
				define(strtoupper($name), $val, true);
			}
		}
		return $configuration;
*/	}
}
?>