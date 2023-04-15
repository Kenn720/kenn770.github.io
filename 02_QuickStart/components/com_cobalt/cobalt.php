<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_COMPONENT . '/library/php/helpers/helper.php';

$params              = JComponentHelper::getParams('com_cobalt');
$meta                = array();
$meta['description'] = $params->get('metadesc');
$meta['keywords']    = $params->get('metakey');
$meta['author']      = $params->get('author');
$meta['robots']      = $params->get('robots');
$meta['copyright']   = $params->get('rights');

MetaHelper::setMeta($meta);

JFactory::getApplication()->setUserState('skipers.all', array());

$controller = JControllerLegacy::getInstance('Cobalt');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

