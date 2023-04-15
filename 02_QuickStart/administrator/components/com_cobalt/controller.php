<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

/**
 * Main Controller
 *
 * @package		Cobalt
 * @subpackage	com_cobalt
 * @since		6.0
 */
class CobaltController extends JControllerLegacy
{

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	6.0
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$this->input->set('view', $this->input->get('view', 'records'));


		if(!$this->input->get('view'))
		{
			$this->setRedirect('index.php?option=com_cobalt&view=records');
		}

		if(!JComponentHelper::getParams('com_cobalt')->get('general_upload'))
		{
			JError::raiseWarning(400, JText::_('CUPLOADREQ'));
			$this->setRedirect('index.php?option=com_config&view=component&component=com_cobalt');
		}

		if(!JFolder::exists(JPATH_ROOT.'/media/mint/js'))
		{
			JError::raiseWarning(400, JText::_('CINSTALLMEDIAPACK'));
			$this->setRedirect('index.php?option=com_installer&view=install');
		}

		parent::display();

		return $this;
	}

	public function getModel($name = '', $prefix = 'CobaltBModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}
}