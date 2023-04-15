<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.component.modellist');

require_once JPATH_ROOT . '/components/com_cobalt/library/php/helpers/helper.php';

class CobaltApi
{
	const FIELD_FULL = 'full';
	const FIELD_LIST = 'list';

	public static function getArticleLink($record_id)
	{
		$record = ItemsStore::getRecord($record_id);
		$url    = JRoute::_(Url::record($record));

		return JHtml::link($url, $record->title);
	}

	/**
	 * @param string $condition Somethign like 'r.id = 12' or 'r.id IN (SELECT...)'
	 */
	public static function renderRating($type_id, $section_id, $condition)
	{
		$type    = ItemsStore::getType($type_id);
		$section = ItemsStore::getSection($section_id);

		$db    = JFactory::getDbo();
		$query = $db->getQuery(TRUE);

		$query->select('r.votes, r.votes_result, r.multirating');
		$query->from('#__js_res_record AS r');
		$query->where('r.type_id = ' . $type_id);
		$query->where('r.section_id = ' . $section_id);
		if(CStatistics::hasUnPublished($section_id))
		{
			$query->where('r.published = 1');
		}
		if($condition)
		{
			$query->where($condition);
		}

		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list)
		{
			//return;
		}

		$record               = new stdClass();
		$record->user_id      = NULL;
		$record->id           = rand(1000, time());
		$record->votes        = 0;
		$record->votes_result = 0;
		$record->multirating  = array();

		$ratings = array();
		foreach($list as $article)
		{
			$record->votes += $article->votes;
			$record->votes_result += $article->votes_result;

			if($article->multirating)
			{
				$mr = json_decode($article->multirating, TRUE);
				foreach($mr AS $key => $rating)
				{
					@$ratings[$key]['sum'] += $rating['sum'];
					@$ratings[$key]['num'] += $rating['num'];
					@$ratings[$key]['avg']++;
				}
			}

		}

		if($ratings)
		{
			$total = 0;
			foreach($ratings AS $key => $rating)
			{
				$ratings[$key]['sum'] = round($ratings[$key]['sum'] / $ratings[$key]['avg']);
				$total += $ratings[$key]['sum'];
				unset($ratings[$key]['avg']);
			}

			$record->votes_result = round($total / count($ratings), 0);
			$record->multirating  = $ratings;
		}
		else
		{
			$record->votes_result = $record->votes ? round($record->votes_result / $record->votes, 0) : 0;
		}
		$record->multirating = json_encode($record->multirating);

		$rating = RatingHelp::loadMultiratings($record, $type, $section, TRUE);

		return array(
			'html'  => $rating,
			'total' => $record->votes_result,
			'multi' => json_decode($record->multirating, TRUE),
			'num'   => $record->votes
		);
	}

	public static function getField($field_id, $record, $default = NULL, $bykey = FALSE)
	{
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_cobalt/tables/');
		$field_table = JTable::getInstance('Field', 'CobaltTable');
		if($bykey)
		{
			$field_table->load(array('key' => $field_id));
		}
		else
		{
			$field_table->load($field_id);
		}

		if(!$field_table->id)
		{
			JError::raiseError(500, JText::_('CERRNOFILED'));

			return;
		}

		$field_path = JPATH_ROOT . "/components/com_cobalt/fields/{$field_table->field_type}/{$field_table->field_type}.php";
		if(!JFile::exists($field_path))
		{
			JError::raiseError(500, JText::_('CERRNOFILEHDD'));

			return;
		}
		require_once $field_path;

		if(!is_object($record))
		{
			$record = ItemsStore::getRecord($record);
		}

		if($default === NULL)
		{
			$values  = json_decode($record->fields, TRUE);
			$default = @$values[$field_id];
		}

		$classname = 'JFormFieldC' . ucfirst($field_table->field_type);
		if(!class_exists($classname))
		{
			JError::raiseError(500, JText::_('CCLASSNOTFOUND'));

			return;
		}

		return new $classname($field_table, $default);
	}

	public static function renderField($record, $field_id, $view, $default = NULL, $bykey = FAlSE)
	{
		if(!$record)
		{
			return;
		}

		if(!is_object($record) && $record > 0)
		{
			$record = ItemsStore::getRecord($record);
		}

		if(!$record->id)
		{
			return;
		}

		$fieldclass = self::getField($field_id, $record, $default, $bykey);

		$func = ($view == 'full') ? 'onRenderFull' : 'onRenderList';

		if(!method_exists($fieldclass, $func))
		{
			JError::raiseError(500, JText::_('AJAX_METHODNOTFOUND'));

			return;
		}

		$type    = ItemsStore::getType($record->type_id);
		$section = ItemsStore::getSection($record->section_id);

		$result = $fieldclass->$func($record, $type, $section);

		return $result;
	}

	/**
	 * @param int    $section_id
	 * @param string $view_what
	 * @param string $order
	 * @param array  $type_ids
	 * @param null   $user_id   No user must be NULL, otherwise 0 would be Guest
	 * @param int    $cat_id
	 * @param int    $limit
	 * @param null   $tpl
	 * @param int    $client    name of the extension that use cobalt records
	 * @param string $client_id ID of the parent cobalt record
	 * @param bool   $lang      true or false. Selects only current language records or records on any language.
	 * @param array  $ids       Ids array of the records.
	 *
	 * @return array
	 */
	public function records($section_id, $view_what, $order, $type_ids = array(), $user_id = NULL,
							$cat_id = 0, $limit = 5, $tpl = NULL, $client = 0, $client_id = '', $lang = FALSE, $ids = array())
	{
		require_once JPATH_ROOT . '/components/com_cobalt/models/record.php';
		$content       = array('total' => 0, 'html' => NULL, 'ids' => array());
		$this->section = ItemsStore::getSection($section_id);

		if(!$this->section->id)
		{
			JError::raiseNotice(404, 'Section not found');

			return;
		}

		$app             = JFactory::getApplication();
		$this->appParams = new JRegistry(array());
		if(method_exists($app, 'getParams'))
		{
			$this->appParams = $app->getParams();
		}

		//$this->section->params->set('general.section_home_items', 2);
		$this->section->params->set('general.featured_first', 0);
		$this->section->params->set('general.records_mode', 0);
		if($lang)
		{
			$this->section->params->set('general.lang_mode', 1);
		}


		$order = explode(' ', $order);

		$back_sid   = $app->input->get('section_id');
		$back_vw    = $app->input->get('view_what');
		$back_cat   = $app->input->get('force_cat_id');
		$back_type  = $app->input->get('filter_type');
		$back_user  = $app->input->get('user_id');
		$back_uc    = $app->input->get('ucat_id');
		$back_limit = $app->input->get('limit', NULL);

		$state_limit = $app->getUserState('global.list.limit', 20);
		$state_ord   = $app->getUserState('com_cobalt.records' . $section_id . '.ordercol');
		$state_ordd  = $app->getUserState('com_cobalt.records' . $section_id . '.orderdirn');
		$app->input->set('section_id', $section_id);
		$app->input->set('view_what', $view_what);
		$app->input->set('force_cat_id', $cat_id);
		$app->input->set('user_id', $user_id);
		$app->input->set('ucat_id', 0);
		$app->input->set('limit', $limit);
		$app->input->set('api', 1);
		$app->setUserState('global.list.limit', $limit);
		$sortable = CobaltModelRecord::$sortable;

		$records                = JModelLegacy::getInstance('Records', 'CobaltModel');
		$records->section       = $this->section;
		$records->_filtersWhere = FALSE;
		$records->_navigation   = FALSE;
		$records->getState(NULL);

		$records->setState('records.section_id', $this->section->id);
		$records->setState('records.type', $type_ids);
		$records->_ids = $ids;
		$records->setState('records.ordering', $order[0]);
		$records->setState('records.direction', $order[1]);
		$items         = $records->getItems();

		$ids = array();
		foreach($items as $key => $item)
		{
			$items[$key] = JModelLegacy::getInstance('Record', 'CobaltModel')->_prepareItem($item, ($client ? $client : 'list'));
			$ids[]       = $item->id;
		}

		$this->input = $app->input;

		require_once JPATH_ROOT . '/components/com_cobalt/views/records/view.html.php';
		$view                    = new CobaltViewRecords();
		$this->total_fields_keys = $view->_fieldsSummary($items);
		$this->items             = $items;
		$this->user              = JFactory::getUser();
		$this->input             = $app->input;

		require_once JPATH_ROOT . '/components/com_cobalt/models/category.php';
		$catmodel       = new CobaltModelCategory();
		$this->category = $catmodel->getEmpty();
		if($app->input->getInt('force_cat_id'))
		{
			$this->category = $catmodel->getItem($app->input->getInt('force_cat_id'));
		}

		$this->submission_types      = $records->getAllTypes();
		$this->total_types           = $records->getFilterTypes();
		$this->fields_keys_by_id     = $records->getKeys($this->section);
		CobaltModelRecord::$sortable = $sortable;

		$tpl = $this->_setuptemplate($tpl);

		if($items)
		{
			ob_start();
			include JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'records' . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR . 'default_list_' . $tpl . '.php';
			$content['html'] = ob_get_contents();
			ob_end_clean();
			$content['total'] = count($items);
			$content['list']  = $items;
			$content['ids']   = $ids;
		}

		$app->input->set('section_id', $back_sid);
		$app->input->set('view_what', $back_vw);
		$app->input->set('force_cat_id', $back_cat);
		$app->input->set('user_id', $back_user);
		$app->input->set('ucat_id', $back_uc);
		$app->input->set('limit', $back_limit);
		$app->input->set('api', 0);

		$app->setUserState('global.list.limit', $state_limit);
		$app->setUserState('com_cobalt.records' . $section_id . '.ordercol', $state_ord);
		$app->setUserState('com_cobalt.records' . $section_id . '.orderdirn', $state_ordd);

		return $content;
	}

	private function _setuptemplate($tpl = NULL)
	{
		$dir       = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'records' . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR;
		$templates = (array)$this->section->params->get('general.tmpl_list');

		$cleaned_tmpl = array();
		foreach($templates as $template)
		{
			$tmp = explode('.', $template);
			$cleaned_tmpl[] = $tmp[0];
		}

		if(in_array($cleaned_tmpl, $templates))
		{
			$tpl = $this->section->params->get('general.tmpl_list_default');
		}

		if(!$tpl)
		{
			$tpl = @$templates[0];
		}

		if(!$tpl)
		{
			$tpl = 'default';
		}

		$tmpl = explode('.', $tpl);
		$tmpl = $tmpl[0];

		if(!JFile::exists("{$dir}default_list_{$tmpl}.php"))
		{
			JError::raiseError(100, 'TMPL not found');

			return;
		}

		$this->section->params->set('general.tmpl_list', $tpl);

		$this->list_template       = $tmpl;
		$this->tmpl_params['list'] = CTmpl::prepareTemplate('default_list_', 'general.tmpl_list', $this->section->params);

		$this->section->params->set('general.tmpl_list', $templates);

		return $tmpl;
	}
}