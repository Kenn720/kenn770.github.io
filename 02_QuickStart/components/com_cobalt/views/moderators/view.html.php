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

class CobaltViewModerators extends JViewLegacy
{
	function display($tpl = null)
	{
		$user_id = JFactory::getUser()->get('id');
		$this->state = $this->get('State');

		if(!MECAccess::isModerator($user_id, JFactory::getApplication()->input->getInt('filter_section', $this->state->get('filter.section', 0))))
		{
			JError::raise(E_WARNING, 403, JText::_('CERR_NOPAGEACCESS'));
			return;
		}

		$uri = JFactory::getURI();
// 		$this->action = $uri->toString();

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filter_sections = $this->get('Sections');
		$this->section_model = JModelLegacy::getInstance('Section', 'CobaltModel');

		$this->_prepareDocument();

		parent::display($tpl);
	}

	private function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$menus = $app->getMenu();
		$menu = $menus->getActive();
		$pathway = $app->getPathway();
		$this->appParams = $app->getParams();

		$title = null;
		$path = array();

		if($menu)
		{
			$title = $menu->params->get('page_title', $menu->title);
			$this->appParams->def('page_heading', $title);
		}

		$title = JText::_('CMODERLIST');

		$pathway->addItem($title);

		$path = array(array('title' => $title, 'link' => ''));

		if($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$doc->setTitle($title);
	}
}
?>