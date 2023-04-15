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
        if(!JComponentHelper::getParams('com_cobalt')->get('general_upload'))
		{
			JError::raiseWarning(400, JText::_('CUPLOADREQ'));
			return;
		}

        if(!$this->input->get('view'))
        {
            //$this->input->set('view', 'records');
        }

		$display =  parent::display();

		if(JFactory::getApplication()->input->get('tmpl') != 'component' && JComponentHelper::getParams('com_cobalt')->get('general_copyright'))
		{
			$html = '<div class="clearfix"></div><center><small style="font-size: 10px;">%s</small></center>';
			echo sprintf($html, JText::sprintf('CPOWEREDBY', '<a target="_blank" href="http://www.mintjoomla.com/joomla-components/cobalt.html">Cobalt</a>'));
		}

		if($this->input->get('no_html'))
		{
			JFactory::getApplication()->close();
		}

		return $display;
	}

}