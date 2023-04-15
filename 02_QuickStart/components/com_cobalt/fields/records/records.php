<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/fields/cobaltfield.php';
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'api.php';

class JFormFieldCRecords extends CFormField
{
	public function getInput()
	{

	}

	public function onRenderList($record, $type, $section)
	{
		return NULL;
	}

	public function onRenderFull($record, $type, $section)
	{
		static $data = NULL;

		$ids       = array();
		$user_id   = NULL;
		$view_what = $this->params->get('params.list_type', '');
		$user      = JFactory::getUser();
		$app       = JFactory::getApplication();

		switch($view_what)
		{
			case 'author_created':
			case 'author_favorited':
			case 'author_rated':
			case 'author_commented':
			case 'author_visited':
				if(!$record->user_id)
				{
					return NULL;
				}
				$view_what = str_replace('author_', '', $view_what);
				$user_id   = $record->user_id;
				break;

			case 'visitor_created':
			case 'visitor_favorited':
			case 'visitor_rated':
			case 'visitor_commented':
			case 'visitor_visited':
				if(!$user->get('id'))
				{
					return NULL;
				}
				$view_what = str_replace('visitor_', '', $view_what);
				$user_id   = $user->get('id');
				break;

			case 'author_tag_related':
				if(!$record->user_id)
				{
					return NULL;
				}
				$user_id = $record->user_id;
				break;


			case 'user_field_data':
				$user_id = $record->user_id;
			case 'field_data':
				$from = $this->params->get('params.field_from');
				$in   = $this->params->get('params.field_in');
				if(!$from || !$in)
				{
					JError::raiseNotice(120, JText::_('R_NOTSETFROMANDINFIELDS'));

					return NULL;
				}

				JFactory::getApplication()->setUserState('com_cobalt.field.from', $from);
				JFactory::getApplication()->setUserState('com_cobalt.field.in', $in);
				$app->input->set('_rrid', $record->id);

				break;
			case 'distance':
				$field = $this->params->get('params.field_distance');
				if(empty($field))
				{
					return;
				}

				$fields = json_decode($record->fields, TRUE);

				if(empty($fields[$field]))
				{
					return;
				}

				$lat = @$fields[$field]['position']['lat'];
				$lng = @$fields[$field]['position']['lng'];

				if(!$lat || !$lng)
				{
					return;
				}

				$app->input->set('_rrid', $record->id);
				$app->input->set('_rfid', $field);
				$app->input->set('_rdist', $this->params->get('params.distance'));

				break;

		}

		$cat_id = 0;
		if($this->params->get('params.category_affect', 0))
		{
			$cat_id = $this->request->getInt('cat_id', 0);
		}

		JFactory::getApplication()->setUserState('com_cobalt.skip_record', $record->id);

		$api           = new CobaltApi();
		$this->content = $api->records(
			$this->params->get('params.section_id'),
			$view_what,
			$this->params->get('params.orderby', 'r.ctime DESC'),
			$this->params->get('params.type', array()),
			$user_id,
			$cat_id,
			$this->params->get('params.limit', 10),
			$this->params->get('params.tmpl_list', 'default'),
			0, '', FALSE, $ids
		);

		JFactory::getApplication()->setUserState('com_cobalt.skip_record', 0);

		return $this->_display_output('full', $record, $type, $section);

	}

}
