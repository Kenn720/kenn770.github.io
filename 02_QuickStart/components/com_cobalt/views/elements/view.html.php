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
require_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components/com_cobalt/library/php/commerce/mintpay.php';
require_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components/com_cobalt/library/php/commerce/mintpayabstract.php';

class CobaltViewElements extends JViewLegacy
{

	function display($tpl = null)
	{
		$this->{'_' . $this->getLayout()}($tpl);
	}

	private function _homepages($tpl = null)
	{
		$app = JFactory::getApplication();
		$model = JModelLegacy::getInstance('Homepages', 'CobaltModel');

		$this->author = $model->_getauthor();
		$this->user = JFactory::getUser();
		$this->isme = $this->user->get('id') == $this->author;

		$db = JFactory::getDbo();
		$db->setQuery("SELECT * FROM `#__js_res_user_options` WHERE user_id = {$this->author}");
		$this->params = new JRegistry(@$db->loadObject()->params);

		$this->state = $model->getState();
		$this->items = $model->getItems();
		$this->all = $model->getAll();

		parent::display($tpl);
	}


	private function _addsale($tpl)
	{
		$user = JFactory::getUser();

		if(!$user->get('id'))
		{
			JError::raiseWarning(403, JText::_('CERRNOSALER'));
			CCommunityHelper::goToLogin();
		}

		$model = JModelLegacy::getInstance('Sale', 'CobaltModel');
		$this->state = $model->getState();
		$this->item = $model->getItem();

		$this->form = $model->getForm();
		$this->form->setFieldAttribute('gateway_id', 'default', strtoupper(substr(md5(time()), 0, 5)));

		JError::raiseNotice(100, JText::_('CNEWORDERNOTICE'));

		parent::display($tpl);
	}

	private function _saler($tpl)
	{
		$user = JFactory::getUser();

		if(!$user->get('id'))
		{
			JError::raiseWarning(403, JText::_('CERRNOSALER'));
			CCommunityHelper::goToLogin();
		}

		JFactory::getApplication()->input->set('filter_buyer', 0);
		JFactory::getApplication()->input->set('filter_saler', $user->get('id'));

		$this->_orders($user);

		parent::display($tpl);
	}
	private function _buyer($tpl)
	{
		$user = JFactory::getUser();

		if(!$user->get('id'))
		{
			JError::raiseWarning(403, JText::_('CERRNOBUYER'));
			CCommunityHelper::goToLogin();
		}

		JFactory::getApplication()->input->set('filter_saler', 0);
		JFactory::getApplication()->input->set('filter_buyer', $user->get('id'));
		$this->_orders($user);

		parent::display($tpl);
	}
	private function _orders($user)
	{
		$user = JFactory::getUser();

		if(!$user->get('id'))
		{
			JError::raiseWarning(403, JText::_('CERRNOBUYER'));
			CCommunityHelper::goToLogin();
		}

		$model = JModelLegacy::getInstance('Orders', 'CobaltModel');
		$this->orders = $model->getItems();
		$this->state = $model->getState();
		$this->pagination = $model->getPagination();

		$this->pay = new MintPayAbstract();
		$this->all_sales = $model->isSuperUser();
		$this->cur_section = $model->getSection();
		$this->filter_sections = $model->getSections();

		$this->stat = $this->pay->get_statuses();
		foreach ($this->stat AS $key => $stat)
		{
			$this->statuses[] = JHtml::_('select.option', $key, $stat);
		}

		if(!$this->orders)
		{
			$this->orders = array();
		}

		foreach ($this->orders AS &$order)
		{
			JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . 'tables' . DIRECTORY_SEPARATOR . 'field.php');
			$field_table = JTable::getInstance('Field', 'CobaltTable');
			$field_table->load($order->field_id);

			$order->record = ItemsStore::getRecord($order->record_id);

			$values = json_decode($order->record->fields, TRUE);
			$default = @$values[$order->field_id];

			$field_path =  JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . $field_table->field_type . DIRECTORY_SEPARATOR . $field_table->field_type . '.php';
			require_once  $field_path;

			$classname = 'JFormFieldC' . ucfirst($field_table->field_type);
			if(!class_exists($classname))
			{
				continue;
			}

			$fieldclass = new $classname($field_table, $default);

			if(!method_exists($fieldclass, 'onOrderList'))
			{
				continue;
			}

			$order->field = $fieldclass->onOrderList($order, $order->record);
			$order->name = ($order->name ? $order->name : $order->rtitle);
		}

	}
	private function _field($tpl)
	{
		$app = JFactory::getApplication();
		$section_id = $app->input->get('section_id');
		$func = $app->input->get('func');
		$id = $app->input->getInt('id');
		$record_id = $app->input->getInt('record');
		$params = $app->input->post;

		if(! $id)
		{
			JError::raiseError(500, JText::_('AJAX_NOFIELDID'));
			return;
		}

		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . 'tables' . DIRECTORY_SEPARATOR . 'field.php');
		$field_table = JTable::getInstance('Field', 'CobaltTable');
		$field_table->load($id);
		$field_path =  JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . $field_table->field_type . DIRECTORY_SEPARATOR . $field_table->field_type . '.php';
		if(! JFile::exists($field_path))
		{
			JError::raiseError(500, JText::_('AJAX_FIELDNOTFOUND'));
			return;
		}

		if(! $func)
		{
			JError::raiseError(500, JText::_('AJAX_NOFUNCNAME'));
			return;
		}

		require_once $field_path;

		$default = array();
		$record = null;
		if($record_id)
		{
			$record_model = JModelLegacy::getInstance('Record', 'CobaltModel');
			$record = $record_model->getItem($record_id);
			$values = json_decode($record->fields, TRUE);
			$default = @$values[$id];
		}
		$section = null;
		if($section_id)
		{
			$section = ItemsStore::getSection($section_id);
		}

		$classname = 'JFormFieldC' . ucfirst($field_table->field_type);
		if(! class_exists($classname))
		{
			JError::raiseError(500, JText::_('CCLASSNOTFOUND'));
			return;
		}

		$fieldclass = new $classname($field_table, $default);

		if(! method_exists($fieldclass, $func))
		{
			JError::raiseError(500, JText::_('AJAX_METHODNOTFOUND').$func);
			return;
		}
		$this->context = $fieldclass->$func($record, $section);

		parent::display($tpl);
	}

	private function _options($tpl)
	{
		JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_cobalt&view=options&return='.JFactory::getApplication()->input->getBase64('return'), FALSE));
		return;
	}

	private function _records($tpl)
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$user = JFactory::getUser();
		$model = JModelLegacy::getInstance('Records', 'CobaltModel');
		$state_limit = JFactory::getApplication()->getUserState('global.list.limit');
		$app->input->set('limit', 10);
		$app->input->set('view_what', 'all');

		$tmpl_params = array();
		$category = NULL;

		require_once JPATH_ROOT . '/components/com_cobalt/models/category.php';
		$model_category       = new CobaltModelCategory();
		if(! JFactory::getApplication()->input->getInt('section_id'))
		{
			JError::raiseWarning(404, JText::_('CNOSECTION'));
			return;
		}
		$section = ItemsStore::getSection(JFactory::getApplication()->input->getInt('section_id'));

		if($section->published == 0)
		{
			JError::raise(E_WARNING, 403, JText::_('CERR_SECTIONUNPUB'));
			return;
		}

		if(! in_array($section->access, $user->getAuthorisedViewLevels()) && !MECAccess::allowRestricted($user, $section))
		{
			JError::raise(E_WARNING, 403, $section->params->get('general.access_msg'));
			return;
		}
		$this->section = $section;
		$model->section = $section;

		// --- GET CATEGORY ----
		$category = $model_category->getEmpty();
		if(JFactory::getApplication()->input->getInt('cat_id'))
		{
			$category = $model_category->getItem(JFactory::getApplication()->input->getInt('cat_id'));
			if(! isset($category->id))
			{
				JError::raiseNotice(404, JText::_('CCATNOTFOUND'));
				$category = $model_category->getEmpty();
			}
			if($category->id && ($category->section_id != $section->id))
			{
				JError::raiseNotice(403, JText::_('CCATWRONGSECTION'));
				$category = $model_category->getEmpty();
			}
			JFactory::getApplication()->input->set('cat_id', $category->id);
		}
		$this->category = $category;

		// Get field
		if(JFactory::getApplication()->input->getInt('field_id'))
		{
			JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . 'tables' . DIRECTORY_SEPARATOR . 'field.php');
			$field = JTable::getInstance('Field', 'CobaltTable');
			$field->load(JFactory::getApplication()->input->getInt('field_id'));
			$params = new JRegistry($field->params);

			if(!in_array($params->get('params.strict_to_user'), $user->getAuthorisedViewLevels()))
			{
				if($params->get('params.strict_to_user_mode') > 1)
				{
					$record = JTable::getInstance('Record', 'CobaltTable');
					$record->load(JFactory::getApplication()->input->getInt('record_id'));
					$user_id = $record->user_id;
					if(!$user_id && $params->get('params.strict_to_user_mode') == 3)
					{
						$user_id = $user->get('id');
					}
				}
				else
				{
					$user_id = $user->get('id');
				}

				if(!$user_id)
				{
					$user_id = 999999999;
				}
				JFactory::getApplication()->input->set('user_id', $user_id);
			}
		}

		$this->state = $model->getState();
		// parent / child filter
		$field_type = $app->input->getCmd('type', false);
		if ($field_type && ($field_type == 'parent' || $field_type == 'child'))
		{
			$model->setState('records.type', $app->input->getInt('type_id', null));
		}
		$this->items = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->user = $user;

		JFactory::getApplication()->setUserState('global.list.limit', $state_limit);

		parent::display($tpl);
	}

	private function _products($tpl)
	{
		$user = JFactory::getUser();

		if(!$user->get('id'))
		{
			JError::raiseWarning(403, JText::_('CERRNOBUYER'));
			CCommunityHelper::goToLogin();
		}

		$model = JModelLegacy::getInstance('Products', 'CobaltModel');
		$orders_model = JModelLegacy::getInstance('Orders', 'CobaltModel');
		$state_limit = JFactory::getApplication()->getUserState('global.list.limit');

		$tmpl_params = array();
		$category = NULL;
		$this->all_products = $orders_model->isSuperUser();
		$this->items = $model->getItems();
		$this->worns = $model->getWorns();
		$this->types = $model->types;
		$this->state = $model->getState();
		$this->pagination = $model->getPagination();

		parent::display($tpl);
	}
}