<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');
include_once JPATH_ROOT . '/components/com_cobalt/library/php/helpers/template.php';

class CobaltBModelPack extends JModelAdmin
{
	private $_xml_paths = array();

	private $_tpl = array();
	private $_tpl_config = array();
	private $_subtmpl = array();

	public function __construct($config)
	{
		$app = JFactory::getApplication();
		$app->registerEvent('onContentBeforeDelete', 'ZipRemover');
		$config['event_before_delete'] = 'deleteZip';
		$this->option = 'com_cobalt';

		return parent::__construct($config);
	}

	public function getTable($type = 'Packs', $prefix = 'CobaltTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


	public function getForm($data = array(), $loadData = TRUE)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_cobalt.pack', 'pack', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_cobalt.edit.' . $this->getName() . '.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data)
	{
		return parent::save($data);
	}

	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_cobalt.pack.' . (int)$record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_cobalt.pack.' . (int)$record->id);
	}

	public function delete(&$pks)
	{
		$result = parent::delete($pks);

		if($result)
		{
			$db = JFactory::getDbo();
			$db->setQuery("DELETE FROM #__js_res_packs_sections WHERE pack_id IN (" . implode(',', $pks) . ")");
			$db->query();
		}
	}

	public function build($pack_id)
	{
		jimport('joomla.archive');
		JModelLegacy::addIncludePath(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/models');
		$db = JFactory::getDBO();

		$packsections_model = JModelLegacy::getInstance('Packsections', 'CobaltBModel');

		$this->pack    = $this->getItem($pack_id);
		$pack_sections = $packsections_model->getPackSectoins($pack_id);
		if(!$pack_sections)
		{
			JError::raiseWarning(404, JText::sprintf('CPACKNOSECTIONS', $this->pack->name));

			return FALSE;
		}

		$add_files = explode("\n", $this->pack->add_files);
		settype($add_files, 'array');

		$section_ids = array_keys($pack_sections);

		$type_ids = $type_ids_content = $type_ids_f_r_tmpls = $field_ids = array(0);
		$tpl      = $this->rating = $types_to_save = $icons = $types_tmpl = array();

		define('VIEWS_ROOT', JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/views');
		define('PACK_KEY', $this->pack->key);
		define('PACK_ROOT', JPATH_CACHE . DIRECTORY_SEPARATOR . $this->pack->key);

		$name_part = $this->pack->addkey ? '_' . PACK_KEY : '';
		define('NAME_PART', $name_part);

		if(JFolder::exists(PACK_ROOT))
		{
			JFolder::delete(PACK_ROOT);
		}

		JFolder::create(PACK_ROOT);

		foreach($pack_sections as $k => $ps)
		{
			$section = ItemsStore::getSection($ps->section_id);
			if(is_array($section->params))
			{
				$section->params = new JRegistry($section->params);
			}

			$this->_prepare_templates($ps->params, $section->params, 'general.');
			$cats = $this->_getItems('categories', array('section_id = ' . $ps->section_id), 'level ASC');
			foreach($cats as $c)
			{
				$cat_params = new JRegistry($c->params);
				$this->_prepare_templates($ps->params, $cat_params);
				$c->params          = $cat_params->toString();
				$categories[$c->id] = $c;

				if($c->image)
				{
					$add_files[] = $c->image;
				}
			}

			$types = $ps->params->get('types', array());

			foreach($types as $type_id => $type_settings)
			{
				$type_ids[] = $type_id;

				foreach($type_settings as $key => $value)
				{
					// переназначение/мердженье параметров типов в настройках паксекшенна
					@$types_tmpl[$type_id]->$key = !empty($types_tmpl[$type_id]->$key) ? $types_tmpl[$type_id]->$key : $type_settings->$key;
				}
			}

			$section->params        = $section->params->toString();
			$sections[$section->id] = $section;

		}

		foreach($types_tmpl as $type_id => $type_settings)
		{
			$type       = ItemsStore::getType($type_id);
			if(!is_object($type->params))
			{
				$type->params = new JRegistry($type->params);
			}
			$type_settings = new JRegistry($type_settings);
			if($type_settings->get('copy_content'))
			{
				$type_ids_content[] = $type_id;
			}
			if($type_settings->get('copy_field_record_templates'))
			{
				$type_ids_f_r_tmpls[] = $type_id;
			}

			$this->_tpl_config[] = 'default_record_' . $type->params->get('properties.tmpl_article');
			if($type_settings->get('article'))
			{
				$this->_tpl[] = 'record/tmpl/default_record_' . $type->params->get('properties.tmpl_article');
				$type->params->set('properties.tmpl_article', $this->_getTmplName($type->params->get('properties.tmpl_article')));
			}
			$tmpl_articleform_params = CTmpl::prepareTemplate('default_form_', 'properties.tmpl_articleform', clone $type->params);

			$this->_tpl_config[]     = 'default_form_' . $type->params->get('properties.tmpl_articleform');
			if($type_settings->get('articleform'))
			{
				$this->_tpl[] = 'form/tmpl/default_form_' . $type->params->get('properties.tmpl_articleform');
				$type->params->set('properties.tmpl_articleform', $this->_getTmplName($type->params->get('properties.tmpl_articleform')));
			}
			if($type_settings->get('rating'))
			{
				$this->rating[] = $type->params->get('properties.tmpl_rating');
			}

			$this->_tpl_config[] = 'default_comments_' . $type->params->get('properties.tmpl_comment');
			if($type_settings->get('comment'))
			{
				$this->_tpl[] = 'record/tmpl/default_comments_' . $type->params->get('properties.tmpl_comment');
				$type->params->set('properties.tmpl_comment', $this->_getTmplName($type->params->get('properties.tmpl_comment')));
			}

			$this->_tpl_config[] = 'default_category_' . $tmpl_articleform_params->get('tmpl_params.tmpl_category');
			if($type_settings->get('categoryselect'))
			{
				$this->_tpl[] = 'form/tmpl/default_category_' . $tmpl_articleform_params->get('tmpl_params.tmpl_category');
				$tmpl_articleform_params->set('tmpl_params.tmpl_category', $this->_getTmplName($tmpl_articleform_params->get('tmpl_params.tmpl_category')));

				$this->_subtmpl['default_form_' . $type->params->get('properties.tmpl_articleform')] = $tmpl_articleform_params;
			}
			$type->params             = $type->params->toString();
			$types_to_save[$type->id] = $type;
		}

		$fields = $this->_getItems('fields', array('type_id IN (' . implode(',', $type_ids) . ')'));
		$this->_packFile('fields', $fields);

		foreach($fields as $field)
		{
			if(in_array($field->type_id, $type_ids_f_r_tmpls))
			{
				$f_params = new JRegistry($field->params);
				foreach($f_params->toArray() as $key => $param)
				{
					if(is_array($param))
					{
						foreach($param as $k => $value)
						{
							if(empty($value))
							{
								continue;
							}

							$find = strstr($k, 'tmpl_');
							if($find)
							{
								$tmpl_type = str_replace('tmpl_', '', $k);

								$parts = explode('.', $value);
								switch($tmpl_type)
								{
									case 'rating':
										$rating[] = $parts[0];
										break;
									case 'list':
										$this->_tpl[]        = 'records/tmpl/default_list_' . $parts[0];
										$this->_tpl_config[] = 'default_list_' . $value;
										break;
									case 'full':
										$this->_tpl[]        = 'record/tmpl/default_record_' . $parts[0];
										$this->_tpl_config[] = 'default_record_' . $value;
										break;
								}
							}
						}
					}
				}
			}



			$field_ids[] = $field->id;
		}

		$mls = $this->_getItems('field_multilevelselect', array('field_id IN (' . implode(',', $field_ids) . ')'), 'lft');
		$this->_packFile('field_multilevelselect', $mls);

		$stepaccess = $this->_getItems('field_stepaccess', array('field_id IN (' . implode(',', $field_ids) . ')'));
		$this->_packFile('field_stepaccess', $stepaccess);

		$this->_copy_tmpls();

		$this->_packFile('categories', $categories);
		$this->_packFile('sections', $sections);

		$user_categories = $this->_getItems('category_user', array('section_id IN ( ' . implode(',', $section_ids) . ')'));
		settype($user_categories, 'array');
		foreach($user_categories as $uc)
		{
			if($uc->icon)
			{
				$dest   = PACK_ROOT . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . PACK_KEY . DIRECTORY_SEPARATOR . 'usercategories' . DIRECTORY_SEPARATOR . $uc->user_id . DIRECTORY_SEPARATOR . $uc->icon;
				$folder = dirname($dest);
				if(!JFolder::exists($folder))
				{
					JFolder::create($folder);
				}
				JFile::copy(JPATH_ROOT . DIRECTORY_SEPARATOR . 'images/usercategories' . DIRECTORY_SEPARATOR . $uc->user_id . DIRECTORY_SEPARATOR . $uc->icon, $dest);


				//$add_files[] = "images/usercategories/{$uc->user_id}/{$uc->icon}";
			}
		}
		$this->_packFile('category_user', $user_categories);
		$this->_packFile('types', $types_to_save);


		$this->_packFile('fields_group', $this->_getItems('fields_group', array('type_id IN (' . implode(',', $type_ids) . ')')));

		$rcond = array('type_id IN (' . implode(',', $type_ids_content) . ') AND section_id IN ( ' . implode(',', $section_ids) . ')');

		$records = $this->_getItems('record', $rcond);
		$this->_packFile('records', $records);

		$r_ids = array_keys($records);
		if(!empty($r_ids))
		{
			$comments = $this->_getItems('comments', array('record_id IN (' . implode(',', $r_ids) . ')'), 'level ASC');
		}

		$comments[0] = 0;
		$comment_ids = array_keys($comments);

		$this->_packFile('comments', $comments);

		$records[0] = 0;
		$record_ids = array_keys($records);

		$users = $this->_getUsers($record_ids);
		$this->_packFile('users', $users);
		$users[0] = 1;
		$user_ids = array_keys($users);

		$votes = $this->_getItems('vote', array('(ref_id IN (' . implode(',', $record_ids) . ') AND ref_type = "record") OR (ref_id IN (' . implode(',', $comment_ids) . ') AND ref_type = "comment")'));
		$this->_packFile('vote', $votes);

		$tags_history = $this->_getItems('tags_history', array('record_id IN (' . implode(',', $record_ids) . ')'));
		$this->_packFile('tags_history', $tags_history);

		$tags = $this->_getItems('tags', array('id IN ( SELECT tag_id FROM #__js_res_tags_history WHERE record_id IN (' . implode(',', $record_ids) . '))'));
		$this->_packFile('tags', $tags);

		$this->_packFile('favorite', $this->_getItems('favorite', array('record_id IN (' . implode(',', $record_ids) . ')')));
		$this->_packFile('record_values', $this->_getItems('record_values', array('record_id IN (' . implode(',', $record_ids) . ')')));
		$this->_packFile('record_category', $this->_getItems('record_category', array('record_id IN (' . implode(',', $record_ids) . ')')));
		$this->_packFile('sales', $this->_getItems('sales', array('record_id IN (' . implode(',', $record_ids) . ')')));

		$this->_packFile('notifications', $this->_getItems('notifications', array('ref_2 IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')')));
		$this->_packFile('subscribe', $this->_getItems('subscribe', array('section_id IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')')));
		$this->_packFile('subscribe_user', $this->_getItems('subscribe_user', array('section_id IN ( ' . implode(',', $section_ids) . ') AND (user_id IN(' . implode(',', $user_ids) . ')  OR u_id IN(' . implode(',', $user_ids) . '))')));
		$this->_packFile('subscribe_cat', $this->_getItems('subscribe_cat', array('section_id IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')')));

		$this->_packFile('moderators', $this->_getItems('moderators', array('section_id IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')')));
		$this->_packFile('user_post_map', $this->_getItems('user_post_map', array('section_id IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')')));
		$this->_packFile('user_options', $this->_getItems('user_options', array('user_id IN(' . implode(',', $user_ids) . ')')));
		$this->_packFile('user_options_autofollow', $this->_getItems('user_options_autofollow	', array('section_id IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')')));

		$files = $this->_getItems('files', array('record_id IN (' . implode(',', $record_ids) . ') AND saved = 1'));
		$this->_packFile('files', $files);
		settype($files, 'array');
		$cobalt_params = JComponentHelper::getParams('com_cobalt');
		foreach($files as $file)
		{
			$params    = new JRegistry($fields[$file->field_id]->params);
			$subfolder = $params->get('params.subfolder', JFile::getExt($file->filename));
			$file_fullpath = str_replace($cobalt_params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR, '', $file->fullpath);
			$dest      = PACK_ROOT . '/uploads/' . $subfolder . DIRECTORY_SEPARATOR . $file_fullpath;
			$folder    = dirname($dest);
			if(!JFolder::exists($folder))
			{
				JFolder::create($folder);
			}
			JFile::copy(JPATH_ROOT . DIRECTORY_SEPARATOR . $cobalt_params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . $file_fullpath, $dest);
		}

		if(!empty($add_files))
		{
			foreach($add_files AS $file)
			{
				$file = trim($file);
				if(!$file)
				{
					continue;
				}
				$path = JPATH_ROOT . DIRECTORY_SEPARATOR . $file;
				$path = JPath::clean($path);

				if(JFile::exists($path))
				{
					$folder = JPath::clean(PACK_ROOT . DIRECTORY_SEPARATOR . 'add' . DIRECTORY_SEPARATOR . $file);
					JFolder::create(dirname($folder));
					JFile::copy($path, $folder);
				}
				if(JFolder::exists($path))
				{
					JFolder::copy($path, JPath::clean(PACK_ROOT . DIRECTORY_SEPARATOR . 'add' . DIRECTORY_SEPARATOR . $file), NULL, TRUE);
				}
			}
		}

		$this->_add_folder_path_additionals();

		foreach($pack_sections as $ps)
		{
			$ps->params = $ps->params->toString();
		}


		$this->pack->sections = $pack_sections;
		$this->_packFile('pack', $this->pack);
		$install_file = file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'library/php/pack/install.php.txt');
		$class_name = JFilterInput::getInstance()->clean('Cobalt 8 Pack - ' . $this->pack->get('name', 'Pack name'), 'cmd') . 'InstallerScript';
		$class_name = str_replace('-', '', $class_name);
		$install_file = preg_replace('/{CLASSNAME}/', $class_name, $install_file);

		JFile::write(PACK_ROOT . DIRECTORY_SEPARATOR . 'install.pack.php', $install_file);

		$this->_add_folder_path('configs');
		$this->_generateXml();

		//Archiving
		$zip_filename = JPATH_CACHE . DIRECTORY_SEPARATOR . 'pack_cobalt.' . JFilterOutput::stringURLSafe($this->pack->get('name', 'Pack name')) . '('.str_replace('pack', '', PACK_KEY).').j3.v.8.' . ($this->pack->version + 1) . '.zip';

		$zipper = new Zipper();
		$zipper->open($zip_filename, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
		$zipper->addDir(PACK_ROOT);
		$zipper->close();

		JFolder::delete(PACK_ROOT);

		$table = $this->getTable();
		$table->load($this->pack->id);
		$table->btime = JFactory::getDate()->toSql();
		$table->version += 1;
		$table->store();

		return TRUE;
	}

	private function _getTmplName($name)
	{
		if($this->pack->addkey)
		{
			$name    = explode('.', $name);
			$name[0] = $name[0] . NAME_PART;
			if(count($name) < 2)
			{
				return $name[0];
			}

			return $name[0] . '.' . $name[1];
		}
		else
		{
			return $name;
		}
	}

	private function _copy_tmpls()
	{
		$this->_tpl = array_unique($this->_tpl);

		foreach($this->_tpl AS $tplname)
		{
			$this->_copy_tmpl($tplname);
		}
		$this->_copy_tmpl_config();

		$rating = array_unique($this->rating);
		foreach($rating AS $tplname)
		{
			$this->_copy_rating_tmpl($tplname);
		}

	}
	private function _copy_tmpl($name)
	{
		$name = explode('.', $name);
		if(count($name) < 2)
		{
			return;
		}
		$name = $name[0];
		$name = str_replace(NAME_PART, '', $name);
		$src  = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/views' . DIRECTORY_SEPARATOR . $name;
		$dest = PACK_ROOT . DIRECTORY_SEPARATOR . 'site/views' . DIRECTORY_SEPARATOR . $name . NAME_PART;

		$folder = dirname($dest);

		if(!JFolder::exists($folder))
		{
			JFolder::create($folder);
		}

		JFile::copy($src . '.php', $dest . '.php');
		$this->_add_file_path('views' . DIRECTORY_SEPARATOR . $name . NAME_PART . '.php');

		if(JFile::exists($src . '.css'))
		{
			JFile::copy($src . '.css', $dest . '.css');
			$this->_add_file_path('views' . DIRECTORY_SEPARATOR . $name . NAME_PART . '.css');
		}
		if(JFile::exists($src . '.png'))
		{
			JFile::copy($src . '.png', $dest . '.png');
			$this->_add_file_path('views' . DIRECTORY_SEPARATOR . $name . NAME_PART . '.png');
		}
		if(JFile::exists($src . '.xml'))
		{
			JFile::copy($src . '.xml', $dest . '.xml');
			$this->_add_file_path('views' . DIRECTORY_SEPARATOR . $name . NAME_PART . '.xml');
		}
		if(JFile::exists($src . '.js'))
		{
			JFile::copy($src . '.js', $dest . '.js');
			$this->_add_file_path('views' . DIRECTORY_SEPARATOR . $name . NAME_PART . '.js');
		}
		if(JFolder::exists($src))
		{
			JFolder::copy($src, $dest, '', TRUE);
			$this->_add_folder_path('views' . DIRECTORY_SEPARATOR . $name . NAME_PART);
		}
	}

	private function _copy_tmpl_config()
	{
		$configs = array_unique($this->_tpl_config);
		$src     = JPATH_ROOT . '/components/com_cobalt/configs/';
		$dest    = PACK_ROOT . '/site/configs/';

		$this->_packFile('configs', $configs);

		foreach($configs as $config)
		{
			if(JFile::exists($src . $config . '.json'))
			{
				$cnf = json_decode(JFile::read($src . $config . '.json'), TRUE);
				foreach($cnf AS $key => $val)
				{
					foreach($val AS $k => $v)
					{
						$keys = explode('_', $k);
						if($keys[0] != 'tmpl')
						{
							continue;
						}

						unset($keys[0]);
						$name                = implode('_', $keys);
						$this->_tpl_config[] = 'default_' . $name . '_' . $v;
					}
				}
			}
		}
		$configs = array_unique($this->_tpl_config);

		if(!JFolder::exists($dest))
		{
			JFolder::create($dest);
		}
		foreach($configs as $config)
		{
			if(JFile::exists($src . $config . '.json'))
			{
				JFile::copy($src . $config . '.json', $dest . $this->_getTmplName($config) . '.json');
			}
			else
			{
				$parts = explode('.', $config);
				if(JFile::exists($src . $parts[0] . '.json'))
				{
					JFile::copy($src . $parts[0] . '.json', $dest . $this->_getTmplName($config) . '.json');
				}
			}
		}

		if(!empty($this->_subtmpl))
		{
			foreach($this->_subtmpl as $tmpl => $params)
			{
				$str = $params->toString();
				JFile::write($dest . $tmpl . '.json', $str);
			}
		}
	}

	private function _copy_rating_tmpl($name)
	{
		$name = explode('.', $name);
		$name = $name[0];
		$src  = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/views/rating_tmpls' . DIRECTORY_SEPARATOR;
		$dest = PACK_ROOT . DIRECTORY_SEPARATOR . 'site/views/rating_tmpls' . DIRECTORY_SEPARATOR;

		$folder = dirname($dest);

		if(!JFolder::exists($folder))
		{
			JFolder::create($folder);
		}

		if(JFolder::exists($src . $name . '_img'))
		{
			JFolder::copy($src . $name . '_img', $dest . $name . '_img', '', TRUE);
			$this->_add_folder_path('views/rating_tmpls' . DIRECTORY_SEPARATOR . $name . '_img');
		}

		JFile::copy($src . 'rating_' . $name . '.php', $dest . 'rating_' . $name . '.php');
		$this->_add_file_path('views/rating_tmpls/rating_' . $name . '.php');

		JFile::copy($src . 'rating_' . $name . '.xml', $dest . 'rating_' . $name . '.xml');
		$this->_add_file_path('views/rating_tmpls/rating_' . $name . '.xml');
	}

	private function _prepare_templates($params, &$tmpls, $prefix = NULL)
	{
		$list = $tmpls->get($prefix . 'tmpl_list', array());
		settype($list, 'array');
		$_tpl_names = array();
		foreach($list AS $tplname)
		{
			if(!$tplname)
			{
				continue;
			}
			$this->_tpl_config[] = 'default_list_' . $tplname;
			if($params->get('list') && $tmpls->get($prefix . 'tmpl_list', array()))
			{
				$this->_tpl[] = 'records/tmpl/default_list_' . $tplname;
				$_tpl_names[] = $this->_getTmplName($tplname);
			}
		}
		if($_tpl_names)
		{
			$tmpls->set($prefix . 'tmpl_list', $_tpl_names);
		}
		$this->_tpl_config[] = 'default_cindex_' . $tmpls->get($prefix . 'tmpl_category');
		if($params->get('cat_index') && $tmpls->get($prefix . 'tmpl_category'))
		{
			$this->_tpl[] = 'records/tmpl/default_cindex_' . $tmpls->get($prefix . 'tmpl_category');
			$tmpls->set($prefix . 'tmpl_category', $this->_getTmplName($tmpls->get($prefix . 'tmpl_category')));
		}
		$this->_tpl_config[] = 'default_list_' . $tmpls->get($prefix . 'tmpl_compare');
		if($params->get('compare') && $tmpls->get($prefix . 'tmpl_compare'))
		{
			$this->_tpl[] = 'records/tmpl/default_list_' . $tmpls->get($prefix . 'tmpl_compare');
			$tmpls->set($prefix . 'tmpl_compare', $this->_getTmplName($tmpls->get($prefix . 'tmpl_compare')));
		}
		$this->_tpl_config[] = 'default_markup_' . $tmpls->get($prefix . 'tmpl_markup');
		if($params->get('markup') && $tmpls->get($prefix . 'tmpl_markup'))
		{
			$this->_tpl[] = 'records/tmpl/default_markup_' . $tmpls->get($prefix . 'tmpl_markup');
			$tmpls->set($prefix . 'tmpl_markup', $this->_getTmplName($tmpls->get($prefix . 'tmpl_markup')));
		}
	}

	private function _packFile($filename, $object)
	{
		if(!$object)
		{
			$object = array();
		}
		$json = json_encode($object);
		JFile::write(PACK_ROOT . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . PACK_KEY . DIRECTORY_SEPARATOR . $filename . '.json', $json);
	}

	private function _getItems($table_name, $where = array(), $order = 'id')
	{
		$db = JFactory::getDbo();
		$db->setQuery("SELECT * FROM #__js_res_{$table_name} WHERE " . implode(' AND ', $where) . " ORDER BY {$order}");
		$items = $db->loadObjectList('id');

		return $items;
	}

	private function _getUsers($record_ids)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(TRUE);
		$query->select('*');
		$query->from('#__users');
		$query->where('id IN (SELECT user_id FROM #__js_res_record WHERE id IN (' . implode(',', $record_ids) . ') )
		OR id IN(SELECT user_id FROM #__js_res_comments WHERE record_id IN (' . implode(',', $record_ids) . ') )');
		$db->setQuery($query);
		$users = $db->loadObjectList('id');

		return $users;
	}

	private function _add_file_path($name, $index = 'file')
	{
		$this->_xml_paths[$index][] = $name;
	}

	private function _add_folder_path($path)
	{
		$dest = PACK_ROOT . DIRECTORY_SEPARATOR . 'site' . DIRECTORY_SEPARATOR;

		$files = JFolder::files($dest . $path);

		foreach($files AS $file)
		{
			$this->_xml_paths['file'][] = $path . DIRECTORY_SEPARATOR . $file;
		}

		if($folders = JFolder::folders($dest . DIRECTORY_SEPARATOR . $path))
		{
			foreach($folders as $folder)
			{
				$this->_add_folder_path($path . DIRECTORY_SEPARATOR . $folder);
			}
		}

		$this->_xml_paths['folder'][] = $path;
	}

	private function _add_folder_path_additionals()
	{
		$dest = PACK_ROOT . '/add';
		if(JFolder::exists($dest))
		{
			$files = JFolder::files($dest);

			foreach($files AS $file)
			{
				$this->_xml_paths['add'][] = $file;
			}

			if($folders = JFolder::folders($dest))
			{
				foreach($folders as $folder)
				{
					$this->_xml_paths['addfolder'][] = $folder;
				}
			}
		}

		$dest = PACK_ROOT . '/uploads';
		if(JFolder::exists($dest))
		{
			$files = JFolder::files($dest);

			foreach($files AS $file)
			{
				$this->_xml_paths['files'][] = $file;
			}

			if($folders = JFolder::folders($dest))
			{
				foreach($folders as $folder)
				{
					$this->_xml_paths['filesfolders'][] = $folder;
				}
			}
		}

	}

	private function _generateXml()
	{
		$install = JFile::read(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'library/php/pack/install.xml.txt');

		$install = str_replace('[NAME]', $this->pack->get('name', 'Pack name'), $install);
		$install = str_replace('[AUTHOR_NAME]', $this->pack->get('author_name', 'Author name not set'), $install);
		$install = str_replace('[AUTHOR_EMAIL]', $this->pack->get('author_email', 'Author email  not set'), $install);
		$install = str_replace('[AUTHOR_URL]', $this->pack->get('author_url', 'Author url  not set'), $install);
		$install = str_replace('[CTIME]', $this->pack->get('ctime'), $install);
		$install = str_replace('[COPYRIGHT]', $this->pack->get('copyright', ''), $install);
		$install = str_replace('[VERSION]', (int)$this->pack->get('version', '') + 1, $install);
		$install = str_replace('[DESCR]', $this->pack->get('description', ''), $install);
		$install = str_replace('[KEY]', $this->pack->get('key'), $install);

		$replace = '';
		if(!empty($this->_xml_paths['file']))
		{
			$this->_xml_paths['file'] = array_unique($this->_xml_paths['file']);
			foreach($this->_xml_paths['file'] AS $file)
			{
				$file = str_replace('\\', '/', $file);
				$replace .= "\t\t\t<filename>{$file}</filename>\r\n";
			}
		}

		if(!empty($this->_xml_paths['folder']))
		{
			$this->_xml_paths['folder'] = array_unique($this->_xml_paths['folder']);
			foreach($this->_xml_paths['folder'] AS $folder)
			{
				$folder = str_replace('\\', '/', $folder);
				$replace .= "\t\t\t<folder>{$folder}</folder>\r\n";
			}
		}

		$install = str_replace('[FRONT]', $replace, $install);

		$replace = ''; $add = array();
		if(isset($this->_xml_paths['add']))
		{
			foreach($this->_xml_paths['add'] AS $file)
			{
				$file = str_replace('\\', '/', $file);
				$replace .= "\t\t\t<filename>{$file}</filename>\r\n";
			}
		}
		if(isset($this->_xml_paths['addfolder']))
		{
			foreach($this->_xml_paths['addfolder'] AS $folder)
			{
				$folder = str_replace('\\', '/', $folder);
				$replace .= "\t\t\t<folder>{$folder}</folder>\r\n";
			}
		}
		if($replace)
		{
			$add[] = "\t\t<files folder=\"add\">\n" . $replace . "\t\t</files>";
		}

		$replace = '';
		if(isset($this->_xml_paths['files']))
		{
			foreach($this->_xml_paths['files'] AS $file)
			{
				$file = str_replace('\\', '/', $file);
				$replace .= "\t\t\t<filename>{$file}</filename>\r\n";
			}
		}
		if(isset($this->_xml_paths['filesfolders']))
		{
			foreach($this->_xml_paths['filesfolders'] AS $folder)
			{
				$folder = str_replace('\\', '/', $folder);
				$replace .= "\t\t\t<folder>{$folder}</folder>\r\n";
			}
		}
		if($replace)
		{
			$add[] = "\t\t<files folder=\"uploads\" target=\"".trim(JComponentHelper::getParams('com_cobalt')->get('general_upload'), '/')."\">\n" . $replace . "\t\t</files>";
		}
		$install = str_replace('[ADD]', implode("\n", $add), $install);

		JFile::write(PACK_ROOT . DIRECTORY_SEPARATOR . 'pack.xml', $install);
	}
}

class ZipRemover extends JEvent
{
	public function deleteZip($context, $table)
	{

		$filename = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'cache/pack_' . $table->key . '.zip';
		if(JFile::exists($filename))
		{
			JFile::delete($filename);
		}
	}
}

class Zipper extends ZipArchive
{
	public function addDir($path)
	{
		$nodes = glob($path . '/*');
		foreach($nodes as $node)
		{
			if(is_dir($node))
			{
				$this->addDir($node);
			}
			else if(is_file($node))
			{
				$this->addFile($node, str_replace(PACK_ROOT, PACK_KEY, $node));
			}
		}
	}

}