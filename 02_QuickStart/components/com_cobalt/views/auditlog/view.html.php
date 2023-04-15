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

class CobaltViewAuditlog extends JViewLegacy
{

	function display($tpl = null)
	{
		$doc = JFactory::getDocument();
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		$model = JModelLegacy::getInstance('Auditlog', 'CobaltModel');
		$this->state = $this->get('State');

		$record = $sections = $types = null;

		$record_id = JFactory::getApplication()->input->getInt('record_id');
		if($record_id)
		{
			$app->setUserState('com_cobalt.auditlog.filter.search', 'rid:'.$record_id);
			$app->redirect(JRoute::_('index.php?option=com_cobalt&view=auditlog', FALSE));
		}


		$items = $model->getItems();

		if(!$user->id)
		{
			JError::raiseWarning(100, JText::_('CERRMSGALACCESS'));
			return;
		}

		$this->sections = $this->types = array();

		$sections = $this->get('Sections');
		unset($sections[0]);
		if (count($sections) > 0)
		{
			foreach($sections as $name)
			{
				$this->sections[] = JHTML::_('select.option', $name->id, $name->name.' <span class="badge">'.$name->total.'</span>');
			}
		}

		$types = $this->get('Types');
		unset($types[0]);
		if (count($types) > 0)
		{
			foreach($types as $name)
			{
				$this->types[] = JHTML::_('select.option', $name->id, $name->name.' <span class="badge">'.$name->total.'</span>');
			}
		}

		$this->type_objects = $types;
		$this->events = $this->get('Events');
		$this->users = $this->get('Users');

		$format = 'd M Y h:i:s';
		if($items)
		{
			foreach ($items as $k => $item)
			{
				$type = $types[$item->type_id];

				$format = $type->params->get('audit.audit_date_format', $type->params->get('audit.audit_date_custom', 'd M Y h:i:s'));
				$items[$k]->date = JHtml::_('date', $item->ctime, $format);
				$items[$k]->categories = (empty($item->categories) ? NULL : json_decode($item->categories));
			}
		}

		$db = JFactory::getDbo();
		$db->setQuery("SELECT MIN(ctime) FROM #__js_res_audit_log");
		$mtime = $db->loadResult();
		if($mtime) $this->mtime = JHtml::_('date', $mtime, $format);

		$this->items = $items;
		$this->pagination = $model->getPagination();

		parent::display($tpl);

	}
}
?>