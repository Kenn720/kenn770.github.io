<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class CobaltControllerTags extends JControllerLegacy
{

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}
	function remove()
	{
		$model = $this->getModel('tags', 'CobaltBModel');
		$model->_deleteTag();

		$this->setRedirect('index.php?option=com_cobalt&view=tags', JText::_('C_MSG_TAGDELETEDSUCCESS'));
	}

	function save()
	{
		$model = $this->getModel('tags', 'CobaltBModel');
		if($model->_saveTag())
		{
			$msg = JText::_('C_MSG_TAGSAVEDSUCCESS');
			$this->setRedirect('index.php?option=com_cobalt&view=tags', $msg);
		}
		else
		{
			JError::raiseWarning(500, $model->_error_msg);
			$this->setRedirect('index.php?option=com_cobalt&view=tags');
		}
	}
}
?>