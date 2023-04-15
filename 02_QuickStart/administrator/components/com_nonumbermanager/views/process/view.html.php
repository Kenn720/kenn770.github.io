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
 * View for the install processes
 */
class NoNumberManagerViewProcess extends JViewLegacy
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$action = JFactory::getApplication()->input->get('action');
		if ($action)
		{
			$model = $this->getModel();

			switch ($action)
			{
				case 'uninstall':
					$model->uninstall(JFactory::getApplication()->input->get('id'));
					break;

				case 'install':
				default:
					$model->install(JFactory::getApplication()->input->get('id'), JFactory::getApplication()->input->getString('url'));
					break;
			}

			parent::display('empty');

			return;
		}

		$this->items = $this->get('Items');
		$this->getConfig();

		parent::display($tpl);
	}

	/**
	 * Function that gets the config settings
	 */
	protected function getConfig()
	{
		if (!isset($this->config))
		{
			require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
			$parameters   = NNParameters::getInstance();
			$this->config = $parameters->getComponentParams('nonumbermanager');
		}

		return $this->config;
	}
}
