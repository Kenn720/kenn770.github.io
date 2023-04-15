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
class CobaltViewAbout extends JViewLegacy
{

	public function display($tpl = null)
	{
		$this->addToolbar();

		$data2 = array('version' => 'Not Installed');

		$file = JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'cobalt.xml';
		$data = JApplicationHelper::parseXMLInstallFile($file);

		$fields_path = JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_cobalt'. DIRECTORY_SEPARATOR .'fields';

		$db = JFactory::getDbo();
		$db->setQuery("SELECT element, manifest_cache FROM #__extensions WHERE name LIKE 'Cobalt - Field - %'");
		$fields = $db->loadObjectList();

		foreach ($fields as $key => $field) {
			$fields[$key]->name = ucfirst($field->element);
			$mnf = new JRegistry($field->manifest_cache);
			$fields[$key]->version = $mnf->get('version');
			
		}

		$this->data = $data;
		$this->fields = $fields;

		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('XML_TOOLBAR_TITLE_ABOUT'), 'systeminfo.png');
		MRToolBar::addSubmenu('about');
	}
}
