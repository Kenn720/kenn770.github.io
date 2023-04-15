<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
class CobaltControllerRecords extends JControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
		$this->registerTask('unfeatured',	'featured');

		$this->registerTask('reset_hits',	'reset');
		$this->registerTask('reset_com',	'reset');
		$this->registerTask('reset_vote',	'reset');
		$this->registerTask('reset_fav',	'reset');
		$this->registerTask('reset_ctime',	'reset');
		$this->registerTask('reset_mtime',	'reset');
		$this->registerTask('reset_extime',	'reset');
	}

	public function getModel($name = 'Record', $prefix = 'CobaltBModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function featured()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= $this->input->get('cid', array(), '', 'array');
		$values	= array('featured' => 1, 'unfeatured' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->featured($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_cobalt&view=records');
	}

	public function reset()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$ids	= $this->input->get('cid', array(), '', 'array');
		$task	= $this->getTask();
		$errors = false;

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
			$errors = true;
		}
		else {
			$model = $this->getModel();

			if (!$model->reset($ids, $task)) {
				JError::raiseWarning(500, $model->getError());
				$errors = true;
			}
		}

		$this->setRedirect('index.php?option=com_cobalt&view=records', (!$errors ? JText::_('CRESETSUCCESS') : null));
	}

	public function copy()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$ids	= $this->input->get('cid', array(), '', 'array');
		$errors = false;

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
			$errors = true;
		}
		else {
			$model = $this->getModel();

			if (!$model->copy($ids)) {
				JError::raiseWarning(500, $model->getError());
				$errors = true;
			}
		}

		$this->setRedirect('index.php?option=com_cobalt&view=records', (!$errors ? JText::_('CCOPY_SUCCESS') : null));
	}
}