<?php
/**
 * @package         NoNumber Extension Manager
 * @version         5.2.5
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_nonumbermanager'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

NNFrameworkFunctions::loadLanguage('com_nonumbermanager');
NNFrameworkFunctions::loadLanguage('com_modules', JPATH_ADMINISTRATOR);
NNFrameworkFunctions::loadLanguage('plg_system_nnframework');

$helper = new NoNumberManagerHelper;

if (!$helper->isFrameworkEnabled())
{
	return false;
}

if (version_compare(PHP_VERSION, '5.3', '<'))
{
	$helper->throwError(JText::sprintf('NNEM_NOT_COMPATIBLE_PHP', PHP_VERSION, '5.3'));

	return false;
}

JControllerLegacy::getInstance('NoNumberManager')
	->execute(JFactory::getApplication()->input->get('task'))
	->redirect();

class NoNumberManagerHelper
{
	private $_title = 'COM_NONUMBERMANAGER';
	private $_lang_prefix = 'NNEM';

	/**
	 * Check if the NoNumber Framework is enabled
	 *
	 * @return bool
	 */
	public function isFrameworkEnabled()
	{
		// Return false if NoNumber Framework is not installed
		if (!$this->isFrameworkInstalled())
		{
			return false;
		}

		$nnframework = JPluginHelper::getPlugin('system', 'nnframework');
		if (!isset($nnframework->name))
		{
			$this->throwError(
				JText::_($this->_lang_prefix . '_NONUMBER_FRAMEWORK_NOT_ENABLED')
				. ' ' . JText::sprintf($this->_lang_prefix . '_EXTENSION_CAN_NOT_FUNCTION', JText::_($this->_title))
			);

			return false;
		}

		return true;
	}

	/**
	 * Check if the NoNumber Framework is installed
	 *
	 * @return bool
	 */
	public function isFrameworkInstalled()
	{
		jimport('joomla.filesystem.file');

		if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
		{
			$this->throwError(
				JText::_($this->_lang_prefix . '_NONUMBER_FRAMEWORK_NOT_INSTALLED')
				. ' ' . JText::sprintf($this->_lang_prefix . '_EXTENSION_CAN_NOT_FUNCTION', JText::_($this->_title))
			);

			return false;
		}

		return true;
	}

	/**
	 * Place an error in the message queue
	 */
	public function throwError($text)
	{
		JFactory::getApplication()->enqueueMessage($text, 'error');
	}
}
