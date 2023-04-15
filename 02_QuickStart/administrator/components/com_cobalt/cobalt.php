<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');
jimport('joomla.application.component.modeladmin');
jimport('joomla.html.pane');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.form.form');
jimport('joomla.utilities.date');
jimport('mint.forms.formhelper');
jimport('mint.forms.fields.cobal.cobaltfield');

JHTML::_('behavior.tooltip');

JForm::addFieldPath(JPATH_ROOT . '/libraries/mint/forms/fields');
JForm::addRulePath(JPATH_ROOT . '/libraries/mint/forms/rules');
JForm::addFormPath(JPATH_ROOT . '/libraries/mint/forms/forms');
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/library/php/html');
JHtml::addIncludePath(JPATH_ROOT . '/components/com_cobalt/library/php/html');

require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php';
require_once JPATH_COMPONENT_SITE . '/library/php/helpers/helper.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/controllers/config.php';

$document = JFactory::getDocument();
$document->addScript(JURI::root(TRUE) . '/components/com_cobalt/library/js/felixrating.js');
$document->addScript(JURI::root(TRUE) . '/administrator/components/com_cobalt/library/js/main.js');
$document->addStyleSheet(JURI::root(TRUE) . '/administrator/components/com_cobalt/library/css/fixes.css');
$document->addStyleSheet(JURI::root(TRUE) . '/components/com_cobalt/library/css/style.css');

// Access check.
if(!JFactory::getUser()->authorise('core.manage', 'com_cobalt'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = JControllerLegacy::getInstance('Cobalt');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
<div style="clear: both;"></div>
<br/>
<br/>
<center>
	<small>Copyright &copy; 2012 <a target="_blank" href="http://www.mintjoomla.com">MintJoomla</a>. All rights reserved
	</small>
</center>