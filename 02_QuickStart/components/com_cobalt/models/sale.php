<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;

// Base this model on the backend version.
//require_once JPATH_ADMINISTRATOR.'/components/com_content/models/article.php';

jimport('joomla.application.component.modelform');
jimport('joomla.application.component.modeladmin');

class CobaltModelSale extends JModelAdmin
{
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_cobalt.sale', 'sale', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_cobalt.edit.sale.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$pk = JFactory::getApplication()->input->getInt('id');
		JFactory::getApplication()->setUserState('com_cobalt.sale.form.id',  $pk);
		$this->setState('com_cobalt.sale.form.id', $pk);

		$this->setState('layout', JFactory::getApplication()->input->getCmd('layout'));
	}

	public function getTable($name = '', $prefix = 'Table', $options = array()){
		return JTable::getInstance('Sales', 'CobaltTable');
	}

}