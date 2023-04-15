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
JHtml::addIncludePath(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'php');

class CobaltViewDiff extends JViewLegacy
{

	function display($tpl = null)
	{
		$doc = JFactory::getDocument();
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$this->input = $app->input;

		$model = JModelLegacy::getInstance('Auditversion', 'CobaltModel');
		$this->state = $this->get('State');
		
		$version = $app->input->getInt('version', false);
		if(!$version)
		{	
			JError::raiseWarning(404, JText::_('NOVERSIONID'));
			return;
		}
		
		$record_id = $app->input->getInt('record_id', false);
		if(!$record_id)
		{
			JError::raiseWarning(404, JText::_('NORECORDID'));
			return;
		}
		
		$item = $model->getItem($version, $record_id);
		
		if(!$item)
		{
			return false;
		}
		$this->item = $item;

		$this->record = ItemsStore::getRecord($record_id);
		$this->section = ItemsStore::getSection($this->record->section_id);
		$this->current_fields = json_decode($this->record->fields, true);
		$this->version_fields = json_decode($item->record->fields, true);
		$vers = JModelLegacy::getInstance('Auditversions', 'CobaltModel');
		$vers->record = $this->record;
		$this->versions = $vers->getItems();

		foreach ($this->versions as $key => $vals) {
			if($vals->version == $this->record->version || $vals->version == $version)
			{
				unset($this->versions[$key]);
			}
		}

		$current_field_keys = array_keys($this->current_fields);
		$version_field_keys = array_keys($this->version_fields);
		
		$this->all_field_keys = array_unique(array_merge($current_field_keys, $version_field_keys));
		
		$field_model = JModelLegacy::getInstance('Fields', 'CobaltModel');
		$this->fields = $field_model->getFormFields($this->record->type_id, $this->record->id);
		
		$this->_prepareDocument();
		
		parent::display($tpl);
		
	}
	
	private function _prepareDocument()
	{
		JHtml::_('bootstrap.tooltip');
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$menus = $app->getMenu();
		$menu = $menus->getActive();
		$this->appParams = $app->getParams();
		$pathway = $app->getPathway();
	
		$title = null;
		$path = array();
		
		if($menu)
		{
			$title = $menu->params->get('page_title', $menu->title);
			$this->appParams->def('page_heading', $title);
		}
		
		if($this->record->title)
		{
			$title = JText::_('CAUDITVERSIONSCOMPARE').' : '.$this->record->title.' v. '.$this->record->version.' '.JText::_('and').' v.'.$this->item->version;
		}
		$path = array(array('title' => $title, 'link' => ''));
		$path[] = array('title' => $this->section->name, 'link' => JRoute::_(Url::records($this->section)));
	
		if(empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
	
		$doc->setTitle($title);
	
		$path = array_reverse($path);
	
		foreach($path as $item)
		{
			$pathway->addItem($item['title'], $item['link']);
		}
	}
}
?>