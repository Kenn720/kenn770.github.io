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

jimport('joomla.application.component.view');

/**
 * View class for default list view
 */
class NoNumberManagerViewDefault extends JViewLegacy
{
	protected $items = null;
	protected $config = null;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');

		switch (JFactory::getApplication()->input->get('task'))
		{
			case 'update':
				$tpl = 'update';
				break;

			case 'storekey':
				$this->getModel()->storeKey();
				JFactory::getApplication()->redirect('index.php?option=com_nonumbermanager', '', 'message', true);

				return;

			default:
				$this->addToolbar();
		}
		// Include the component HTML helpers.
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
		parent::display($tpl);
	}

	/**
	 * Function that gets the config settings
	 */
	protected function getConfig()
	{
		if (!is_null($this->config))
		{
			return $this->config;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters   = NNParameters::getInstance();
		$this->config = $parameters->getComponentParams('nonumbermanager');

		return $this->config;
	}

	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbar()
	{
		$canDo = $this->getActions();

		JFactory::getDocument()->setTitle(JText::_('NONUMBER_EXTENSION_MANAGER'));

		JToolbarHelper::title(JText::_('NONUMBER_EXTENSION_MANAGER'), 'nonumbermanager icon-nonumber');

		$config = $this->getConfig();
		NoNumberManagerToolbarHelper::addButtons($config);

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_nonumbermanager', '400');
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 */
	public static function getActions()
	{
		$user      = JFactory::getUser();
		$result    = new JObject;
		$assetName = 'com_nonumbermanager';

		$actions = array(
			'core.admin', 'core.manage',
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

class NoNumberManagerToolbarHelper extends JToolbarHelper
{
	public static function addButtons($config)
	{
		$bar = JToolbar::getInstance('toolbar');

		$html = '
			</div>
			<div class="btn-wrapper">
				<span class="refresh btn btn-small" onclick="nnem_function(\'refresh\');" rel="tooltip" title="' . JText::_('NNEM_REFRESH_DESC') . '">
					<span class="icon-refresh"></span>
				</span>
			</div>

			<div class="btn-wrapper hidden-phone installselected_disabled" id="toolbar-installselected_disabled">
				<span class="btn btn-small disabled">
					<span class="icon-box-add"></span> ' . JText::_('NNEM_INSTALL_SELECTED') . '
				</span>
			</div>
			<div class="btn-wrapper hidden-phone installselected" id="toolbar-installselected">
				<span class="btn btn-small btn-info hidden-phone" onclick="nnem_function(\'installselected\');" rel="tooltip" title="' . JText::_('NNEM_INSTALL_SELECTED_DESC') . '">
					<span class="icon-box-add"></span> ' . JText::_('NNEM_INSTALL_SELECTED') . '
				</span>
			</div>

			<div class="btn-wrapper updateall_disabled" id="toolbar-updateall_disabled">
				<span class="btn btn-small disabled">
					<span class="icon-upload"></span> ' . JText::_('NNEM_UPDATE_ALL') . '
				</span>
			</div>
			<div class="btn-wrapper updateall" id="toolbar-updateall">
				<span class="btn btn-small btn-warning" onclick="nnem_function(\'updateall\');" rel="tooltip" title="' . JText::_('NNEM_UPDATE_ALL_DESC') . '">
					<span class="icon-upload"></span> ' . JText::_('NNEM_UPDATE_ALL') . '
				</span>
		';

		if ($config->show_reinstall_all)
		{
			$html .= '
			</div>

			<div class="btn-wrapper reinstallall_disabled" id="toolbar-reinstallall_disabled">
				<span class="btn btn-small disabled">
					' . JText::_('NNEM_REINSTALL_ALL') . '
				</span>
			</div>
			<div class="btn-wrapper reinstallall" id="toolbar-reinstallall">
				<span class="btn btn-small btn-default" onclick="nnem_function(\'reinstallall\');" rel="tooltip" title="' . JText::_('NNEM_reinstall_ALL_DESC') . '">
					' . JText::_('NNEM_REINSTALL_ALL') . '
				</span>
			';
		}

		$bar->appendButton('Custom', $html);
	}
}
