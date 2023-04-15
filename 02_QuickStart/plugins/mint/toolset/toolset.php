<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php';

class plgMintToolset extends JPlugin
{
	function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);
	}

	function onToolsGetIcons($context)
	{
		$db = JFactory::getDBO();

		$i = 0;

		$i++;
		$icon[$i]              = new stdClass();
		$icon[$i]->label       = JText::_('DB Tool');
		$icon[$i]->icon        = 'plugins/mint/toolset/icons/db.png';
		$icon[$i]->description = JText::_('Some important operations with tables');
		$icon[$i]->name        = 'toolset';
		$icon[$i]->id          = 1;
		$icon[$i]->height      = 500;

		$i++;
		$icon[$i]              = new stdClass();
		$icon[$i]->label       = JText::_('User ID replacer');
		$icon[$i]->icon        = 'plugins/mint/toolset/icons/move_user.png';
		$icon[$i]->description = JText::_('If you acidently deleted some user or you want to move rocords, files, activity, ... t other user run this tool');
		$icon[$i]->name        = 'toolset';
		$icon[$i]->id          = 2;
		$icon[$i]->height      = 300;

		$i++;
		$icon[$i]              = new stdClass();
		$icon[$i]->label       = JText::_('Clean thumbnails');
		$icon[$i]->icon        = 'plugins/mint/toolset/icons/clean_thumbs.png';
		$icon[$i]->description = JText::_('Delete all thumbnails. Will be regenerated on next view. Useful if change tumbnailing mode.');
		$icon[$i]->name        = 'toolset';
		$icon[$i]->id          = 3;
		$icon[$i]->height      = 300;

		$i++;
		$icon[$i]              = new stdClass();
		$icon[$i]->label       = JText::_('Clean Files');
		$icon[$i]->icon        = 'plugins/mint/toolset/icons/clean_files.png';
		$icon[$i]->description = JText::_('<b>Attention!!! Always make backup of uploads folder before using this tool.</b>
			Sometimes users upload files but not finalize upload. If you want to delete all unused files to free some space.');
		$icon[$i]->name        = 'toolset';
		$icon[$i]->id          = 4;
		$icon[$i]->height      = 300;

		$i++;
		$icon[$i]              = new stdClass();
		$icon[$i]->label       = JText::_('Clean deleted files');
		$icon[$i]->icon        = 'plugins/mint/toolset/icons/clean_files.png';
		$icon[$i]->description = JText::_('<b>Attention!!! Always make backup of uploads folder and Cobalt BD before using this tool. You will not be able to restore files through Audit log after this tool is applied.</b> When users delete files they are not removed from disk giving me opportunity to restore');
		$icon[$i]->name        = 'toolset';
		$icon[$i]->id          = 6;
		$icon[$i]->height      = 300;

		$i++;
		$icon[$i]              = new stdClass();
		$icon[$i]->label       = JText::_('Reindex');
		$icon[$i]->icon        = 'plugins/mint/toolset/icons/zoom.png';
		$icon[$i]->description = JText::_('Reindex articles to make them searchable');
		$icon[$i]->name        = 'toolset';
		$icon[$i]->id          = 5;
		$icon[$i]->height      = 300;

		$i++;
		$icon[$i]              = new stdClass();
		$icon[$i]->label       = JText::_('Geo migrate');
		$icon[$i]->icon        = 'plugins/mint/toolset/icons/geo.png';
		$icon[$i]->description = JText::_('Migrate from geo to geo2');
		$icon[$i]->name        = 'toolset';
		$icon[$i]->id          = 7;
		$icon[$i]->height      = 300;

		return $icon;
	}

	protected function _getTool($id)
	{
		$tools = $this->onToolsGetIcons('1');
		foreach($tools as $tool)
		{
			if($id == $tool->id)
				return $tool;
		}
	}

	public function onToolGetForm($context, $form, $name, $id)
	{
		if($name != 'toolset')
			return;

		$tool = $this->_getTool($id);

		if(!JFile::exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'saved' . DIRECTORY_SEPARATOR . $id . '.conf'))
		{
			$a = '';
			JFile::write(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'saved' . DIRECTORY_SEPARATOR . $id . '.conf', $a);
		}

		$params = new JRegistry();
		$params->loadFile(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'saved' . DIRECTORY_SEPARATOR . $id . '.conf');

		$form_object = JForm::getInstance('plg_toolset.form', JPATH_PLUGINS . DIRECTORY_SEPARATOR . 'mint' . DIRECTORY_SEPARATOR . 'toolset' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'forms.xml', array('control' => 'jform'));
		$form        = MEFormHelper::renderFieldset($form_object, 'toolset' . $id, $params, NULL, FORM_STYLE_TABLE);

		return $form;
	}

	function onToolExecute($name, $id)
	{
		if($name != 'toolset')
			return;

		$app = JFactory::getApplication();

		$db     = JFactory::getDBO();
		$params = new JRegistry('');
		if(@$_POST['jform'])
			$params->loadArray(@$_POST['jform']);

		$content = $params->toString();
		JFile::write(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'saved' . DIRECTORY_SEPARATOR . $id . '.conf', $content);

		switch($id)
		{
			case 1:
				$this->dbtools($params);
				break;
			case 2:
				$this->moveUser($params);
				break;
			case 3:
				$this->cleanThumbs($params);
				break;
			case 4:
				$this->cleanFiles($params);
				break;
			case 6:
				$this->cleanFiles($params, 2);
				break;
			case 5:
				$this->reindex($params);
				break;
			case 7:
				$this->geo($params);
				break;
		}
	}

	function geo($params)
	{
		$app = JFactory::getApplication();

		if(!$params->get('field_id_geo'))
		{
			$app->enqueueMessage('Field is not selected!', 'error');

			return FALSE;
		}

		$db = JFactory::getDbo();
		$db->setQuery("SELECT * FROM `#__js_res_fields` WHERE id = " . $params->get('field_id_geo'));
		$field = $db->loadObject();

		if(empty($field->id))
		{
			$app->enqueueMessage('Field not found!', 'error');

			return FALSE;
		}

		$db->setQuery(sprintf("UPDATE `#__js_res_fields` SET field_type = 'geo2', `key` = 'k%s' WHERE id = %d", md5($field->label . '-geo2'), $field->id));
		$db->execute();

		$db->setQuery("UPDATE `#__js_res_record_values`
			SET	value_index = REPLACE(value_index,'1','')
			WHERE field_id = " . $field->id);
		$db->execute();

		$db->setQuery(sprintf("UPDATE `#__js_res_record_values` SET
			value_index = CONCAT(value_index, '1'),
			field_type = 'geo2', `field_key` = 'k%s' WHERE field_id = %d", // AND field_type = 'geo'",
			md5($field->label . '-geo2'), $field->id));
		$db->execute();

		$db->setQuery("SELECT `id`, `fields` FROM `#__js_res_record` WHERE id IN(SELECT r.record_id FROM `#__js_res_record_values` AS r WHERE r.field_id = {$field->id})");
		$list = $db->loadAssocList('id', 'fields');

		$table = JTable::getInstance('Record', 'CobaltTable');

		foreach($list as $id => $fields)
		{
			$value = json_decode($fields, TRUE);
			if(empty($value[$field->id]))
			{
				continue;
			}

			if(!empty($value[$field->id][1]))
			{
				continue;
			}

			$value[$field->id] = array("1" => $value[$field->id]);

			$table->load($id);
			$table->fields = json_encode($value);
			$table->store();
			$table->reset();
			$table->id = NULL;
		}
		$app->enqueueMessage('Successfully updated all articles!');
	}

	function reindex($params)
	{
		$types = $params->get('types', FALSE);
		set_time_limit(0);
		ini_set('max_execution_time', 0);

		if(!$types)
		{
			return;
		}
		if(!is_array($types))
		{
			settype($types, 'array');
		}
		$db = JFactory::getDbo();
		$db->setQuery('SELECT id, title, type_id, section_id, user_id, fields FROM `#__js_res_record` WHERE type_id IN (' . implode(',', $types) . ')');
		$ids   = $db->loadObjectList();
		$count = 0;
		if(!empty($ids))
		{
			require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'fields.php';
			$fields_model = new CobaltModelFields();

			foreach($ids as $item)
			{
				$out_fieldsdata = array();
				$fields_list    = $fields_model->getFormFields($item->type_id, $item->id, FALSE);
				$type           = ItemsStore::getType($item->type_id);
				$section        = ItemsStore::getSection($item->section_id);
				if(!is_object($section->params))
				{
					$section->params = new JRegistry($section->params);
				}

				foreach($fields_list as $field)
				{
					if($field->params->get('core.searchable'))
					{
						$data = $field->onPrepareFullTextSearch($field->value, $item, $type, $section);
						if(is_array($data))
						{
							$data = implode(', ', $data);
						}
						$out_fieldsdata[$field->id] = $data;
					}

				}

				$user = JFactory::getUser($item->user_id);

				if($section->params->get('more.search_title'))
				{
					$out_fieldsdata[] = $item->title;
				}
				if($section->params->get('more.search_name'))
				{
					$out_fieldsdata[] = $user->get('name');
					$out_fieldsdata[] = $user->get('username');
				}
				if($section->params->get('more.search_email'))
				{
					$out_fieldsdata[] = $user->get('email');
				}
				if($section->params->get('more.search_category') && $item->categories != '[]')
				{
					$cats             = json_decode($item->categories, TRUE);
					$out_fieldsdata[] = implode(', ', array_values($cats));
				}

				if($section->params->get('more.search_comments'))
				{
					$out_fieldsdata[] = CommentHelper::fullText($type, $item);
				}

				$db2 = JFactory::getDbo();
				$db2->setQuery("UPDATE `#__js_res_record` SET fieldsdata = '" . $db2->escape(strip_tags(implode(', ', $out_fieldsdata))) . "' WHERE id = $item->id");
				$db2->query();

				unset($db2, $out_fieldsdata, $user, $type, $section);

				if($count == 10000)
				{
					//exit;
				}

				$count++;
			}
		}

		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::sprintf('%d record(s) have been reindexed.', $count));
	}

	function cleanFiles($params, $what = 0)
	{
		$db = JFactory::getDBO();

		if($what == 0)
		{
			$sql = "SELECT DISTINCT record_id FROM #__js_res_record_values WHERE field_type IN('uploads','video','audio','gallery','paytodownload','image')";
			$db->setQuery($sql);
			$records   = $db->loadColumn();
			$records[] = 0;

			$sql = "SELECT * FROM #__js_res_files WHERE record_id NOT IN (" . implode(',', $records) . ") OR saved = 0";
		}
		else
		{
			$sql = "SELECT * FROM `#__js_res_files` WHERE `saved` = 2";
		}

		$db->setQuery($sql);
		$files = $db->loadObjectList();

		$size          = $lost_files = 0;
		$cobalt_params = JComponentHelper::getParams('com_cobalt');

		$files_ids[] = 0;
		foreach($files AS $file)
		{
			$subfolder = $this->_getSubfolder($file->field_id);
			$size += $file->size;
			$part      = explode("_", $file->filename);
			$filetodel = JPATH_ROOT . DIRECTORY_SEPARATOR . $cobalt_params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . date($cobalt_params->get('folder_format'), $part[0]) . DIRECTORY_SEPARATOR . $file->filename;
			if(JFile::exists($filetodel))
			{
				if(JFile::delete($filetodel))
				{
					$files_ids[] = $file->id;
				}
			}
			else
			{
				$files_ids[] = $file->id;
			}
		}

		$sql = "DELETE FROM #__js_res_files WHERE id IN (" . implode(',', $files_ids) . ")";
		$db->setQuery($sql);
		$db->query();


		$files_in_folder = JFolder::files(JPATH_ROOT . DIRECTORY_SEPARATOR . $cobalt_params->get('general_upload'), '[0-9]{10}_[a-zA-Z0-9]{32}\..', TRUE, TRUE);
		settype($files_in_folder, 'array');


		$sql = "SELECT filename FROM #__js_res_files";
		$db->setQuery($sql);
		$files_in_db = $db->loadColumn();

		foreach($files_in_folder as $file)
		{
			if(!in_array(JFile::getName($file), $files_in_db))
			{
				$temp_size = filesize($file);
				if(JFile::delete($file))
				{
					$size += $temp_size;
					$lost_files++;
				}
			}
		}

		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::sprintf('%d file(s) have been deleted. Total size %s.', count($files) + $lost_files, HTMLFormatHelper::formatSize($size)));
	}

	private function _getSubfolder($id)
	{
		static $params = array();
		static $defaults = array();

		if(!isset($params[$id]))
		{
			$db  = JFactory::getDbo();
			$sql = "SELECT params, field_type FROM #__js_res_fields WHERE id = " . $id;
			$db->setQuery($sql);
			$result        = $db->loadObject();
			$params[$id]   = new JRegistry($result->params);
			$defaults[$id] = $result->field_type;
		}

		return $params[$id]->get('params.subfolder', $defaults[$id]);
	}

	function cleanThumbs($params)
	{
		$app    = JFactory::getApplication();
		$folder = JPATH_ROOT . DIRECTORY_SEPARATOR . JComponentHelper::getParams('com_cobalt')->get('general_upload') . DIRECTORY_SEPARATOR . 'thumbs_cache';
		if(JFolder::exists($folder))
		{
			JFolder::delete($folder);
			$app->enqueueMessage(JText::_('Uploads folder thumbnail cache deleted'));
		}
		else
		{
			$app->enqueueMessage(JText::_('No thumbnail in uploads folder'));
		}

		$folder = JPATH_ROOT . '/cache/cobalt_thumbs';
		if(JFolder::exists($folder))
		{
			JFolder::delete($folder);
			$app->enqueueMessage(JText::_('Cache folder thumbnail cache deleted'));
		}
		else
		{
			$app->enqueueMessage(JText::_('No thumbnail in cache folder'));
		}
	}

	function dbtools($params)
	{
		$app = JFactory::getApplication();

		$tables = $params->get('db_tables');
		settype($tables[0], 'array');
		$tables     = $tables[0];
		$table_line = implode(', ', $tables);

		if(!$tables)
		{
			JError::raiseWarning(400, JText::_('No table selected'));

			return;
		}
		$db = JFactory::getDBO();

		switch($params->get('db_action'))
		{
			case 1:
				$sql = "OPTIMIZE TABLE " . $table_line;
				$db->setQuery($sql);
				$db->query();
				$app->enqueueMessage(JText::_('Tables Optimized') . ': ' . $table_line);
				break;
			case 2:
				$sql = "REPAIR TABLE " . $table_line;
				$db->setQuery($sql);
				$db->query();
				$app->enqueueMessage(JText::_('Tables Repaired') . ': ' . $table_line);
				break;
			case 3:
				$sql = "ANALYZE TABLE " . $table_line;
				$db->setQuery($sql);
				$app->enqueueMessage(JText::_('Tables Analized'));
				$res = $db->loadObjectList();
				if($res)
				{
					echo '<table class="adminlist"><thead><TR><th>Table</th><th>Op</th><th>Message Type</th><th>Message</th></tr></thead>';
					foreach($res AS $r)
					{
						echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $r->Table, $r->Op, $r->Msg_type, $r->Msg_text);
					}
					echo '</table><br />';
				}
				break;
			case 5:
				$sql = "CHECK TABLE " . $table_line;
				$db->setQuery($sql);
				$app->enqueueMessage(JText::_('Tables Checked'));
				$res = $db->loadObjectList();
				if($res)
				{
					echo '<table class="adminlist"><thead><TR><th>Table</th><th>Op</th><th>Message Type</th><th>Message</th></tr></thead>';
					foreach($res AS $r)
					{
						echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $r->Table, $r->Op, $r->Msg_type, $r->Msg_text);
					}
					echo '</table><br />';
				}
				break;

			case 4:
				foreach($tables AS $table)
				{
					$sql = "TRUNCATE TABLE " . $table;
					$db->setQuery($sql);
					$db->query();
					$app->enqueueMessage(JText::_('TRUNCATE Table') . ': ' . $table);
				}
				break;
		}
	}

	function moveUser($params)
	{
		require_once JPATH_PLUGINS . DIRECTORY_SEPARATOR . 'mint' . DIRECTORY_SEPARATOR . 'toolset' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'toolset_users.php';
		METoolSetUserHelper::execute($params);
	}

}