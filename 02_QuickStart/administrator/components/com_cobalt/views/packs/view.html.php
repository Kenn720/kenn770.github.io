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
class CobaltViewPacks extends JViewLegacy
{

	public function display($tpl = null)
	{
		$uri = JFactory::getURI();
		$this->action = $uri->toString();
		
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
 		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		MRToolBar::title(JText::_('CREDISPACK' ), 'packs');
		JToolBarHelper::addNew('pack.add');
		JToolBarHelper::editList('pack.edit');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'packs.delete','Delete');
		
		MRToolBar::addSubmenu('packer');
		//MRToolBar::helpW('http://help.mintjoomla.com/cobalt/index.html?fields.htm', 1000, 500); 
	}
}
