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
/**
 * View information about cobalt.
 *
 * @package		Cobalt
 * @subpackage	com_cobalt
 * @since		6.0
 */
class CobaltViewTools extends JViewLegacy
{
	public function display($tpl = null)
	{
		$db = JFactory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE `type` = 'plugin' AND `element` = 'toolset' AND `folder` = 'mint'");
		$res = $db->loadResult();
		if(!$res)
		{
			$db->setQuery("UPDATE #__extensions SET enabled = 1 WHERE `type` = 'plugin' AND `element` = 'toolset' AND `folder` = 'mint'");
			$db->query();
			$app = JFactory::getApplication();
			$app->enqueueMessage('Toolset plugin is not installed', 'warning');
			$app->redirect('index.php?option=com_cobalt');
		}

		if( JRequest::getVar( 'layout' ) == 'form'){
			$this->_tool();
		} else {
			$this->_list();
		}


		MRToolBar::addSubmenu('tools');

		parent::display( $tpl );
	}

	function _tool()
	{
		$uri = JFactory::getURI();

		$this->form = $this->get('ToolForm');
		$this->tool = $this->get('Tool');
		$this->action = $uri->toString();
	}

	function _list()
	{
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal');


		JToolBarHelper::title( JText::_('XML_TOOLBAR_TITLE_TOOLS'), 'plugin.png');

		$this->tools = $this->get('Tools');
	}
}