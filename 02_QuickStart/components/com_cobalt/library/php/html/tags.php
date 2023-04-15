<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
class JHTMLTags
{
	public static function name($id)
	{
		$db = JFactory::getDBO();
		settype($id, 'int');

		$query = $db->getQuery(true);
		$query->select('tag');
		$query->from('#__js_res_tags');
		$query->where("id = {$id}");

		$db->setQuery($query);
		return $db->loadResult();
	}

	public static function tagcheckboxes($section, $default = array())
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('tag, id');
		$query->from('#__js_res_tags');
		$query->where("id IN(SELECT tag_id FROM #__js_res_tags_history WHERE section_id = {$section->id})");
		$query->order('tag');
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;

		$key = 0;

		foreach ($list AS $tag)
		{
			$chekced = (in_array($tag->id, $default) ? ' checked="checked"' : NULL);
			if($key % 4 == 0) $li[] = '<div class="row-fluid">';
			$li[] = sprintf('<div class="span3"><label class="checkbox"><input type="checkbox" id="ctag-%d" class="inputbox" name="filters[tags][]" value="%d"%s /> <label for="ctag-%d">%s</label></label></div>', $tag->id, $tag->id, $chekced, $tag->id, $tag->tag);
			if($key % 4 == 3) $li[] = '</div>';
			$key++;
		}
		if($key % 4 != 0) $li[] = '</div>';

		return '<div class="container-fluid">'.implode(' ', $li).'</div>';
	}

	public static function tagselect($section, $default = array())
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('tag as text, id as value');
		$query->from('#__js_res_tags');
		$query->where("id IN(SELECT tag_id FROM #__js_res_tags_history WHERE section_id = {$section->id})");
		$query->order('tag');
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;
		array_unshift($list, JHtml::_('select.option', '', '- '.JText::_('CSELECTTAG').' -'));


		return JHtml::_('select.genericlist', $list, 'filters[tags][]', null, 'value', 'text', $default);
	}

	public static function tagform($section, $default = array(), $params = array(), $name = 'filters[tags]')
	{

		if(!is_object($section))
		{
			$section = ItemsStore::getSection($section);
		}
		if (! is_array($default) && ! empty($default))
		{
			$default = explode(',', $default);
		}
		$id = 'tags';
		if (!empty($params))
		{
			$id = isset($params['id']) ? $params['id'] : $id;
		}

		ArrayHelper::clean_r($default);
		JArrayHelper::toInteger($default);
		if($default)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('tag as plain, tag as html, tag as render, id');
			$query->from('#__js_res_tags');
			$query->where("id IN(".implode(',', $default).")");

			$db->setQuery($query);
			$default = $db->loadObjectList();
		}

		$options['coma_separate'] = 0;
		$options['only_values'] = 1;
		$options['min_length'] = 1;
		$options['max_result'] = 10;
		$options['case_sensitive'] = 0;
		$options['unique'] = 1;
		$options['highlight'] = 1;
		$options['max_items'] = 100;

		$options['ajax_url'] = 'index.php?option=com_cobalt&task=ajax.tags_list_filter&tmpl=component&section_id='.$section->id;
		$options['ajax_data'] = '';

		return JHtml::_('mrelements.listautocomplete', $name, $id, $default, array(), $options);
	}

	public static function tagpills($section, $default)
	{
		ArrayHelper::clean_r($default);
		JArrayHelper::toInteger($default);

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('tag, id');
		$query->from('#__js_res_tags');
		$query->where("id IN(SELECT tag_id FROM #__js_res_tags_history WHERE section_id = {$section->id})");
		$query->order('tag');

		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;

		foreach ( $list as $id => &$tag )
		{
			$value = (in_array($tag->id, $default) ? $tag->id : NULL);
			$out[] = '<li id="tag-' . $tag->id . '" '.($value ? 'class="active"' : NULL).'><a href="javascript:void(0);" rel="' . $tag->id . '">' .$tag->tag. '<input type="hidden" name="filters[tags][]" id="fht-'.$tag->id.'" value="'.$value.'"></a></li>';
		}

		$html = '<ul id="tag-list-filters" class="nav nav-pills">'.implode(' ', $out).'</ul>';

		$html .= "<script>
		(function($){
			$.each($('#tag-list-filters').children('li'), function(k, v){
				$(this).bind('click', function(){
					var a = $('a', this)
					var id = a.attr('rel');
					var hf = $('#fht-'+id);
					if(hf.val())
					{
						$(this).removeClass('active');
						hf.val('');
					}
					else
					{
						$(this).addClass('active');
						hf.val(id);
					}
				});
			});
		}(jQuery));
		</script>";


		return $html;
	}

	public static function tagcloud($section, $html_tags, $relevance)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('tag, id');
		$query->from('#__js_res_tags');
		$query->where("id IN(SELECT tag_id FROM #__js_res_tags_history WHERE section_id = {$section->id})");
		$query->order('tag');

		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;

		$out = array();
		$html = explode(",", $html_tags);
		$total_tags = count($html) - 1;
		$step = ceil(count($list) / count($html));
		$html_id = $i = 0;
		$prev_randx = 1;

		foreach ( $list as $id => &$tag )
		{
			$url = FilterHelper::url('task=records.filter&filter_name[0]=filter_tag&filter_val[0]='.$tag->id, $section);
			$tag->tag = JHtml::link(JRoute::_($url), $tag->tag);
			if ($relevance)
			{
				if ($relevance == 3)
				{
					$randx = rand(0, $total_tags);
					if ($randx == $prev_randx || ! $randx)
					{
						if ($prev_randx >= $total_tags)
							$randx = $prev_randx - 2;
						else
							$randx = $prev_randx + 1;
					}
					$t = $html[$randx];
					$prev_randx = $randx;
				}
				else
				{
					if ($i == $step)
					{
						$i = 0;
						$html_id ++;
					}
					$t = $html[$html_id];
				}
				$tag->tag = sprintf('<%s class="tag">%s</%s>', trim($t), $tag->tag, trim($t));
				$i ++;
			}
			$out[] = '<li class="tag_element" id="tag-' . $tag->id . '">' .$tag->tag. '</li>';
		}
		if(!$out) return ;

		return '<ul id="tag-list-filters" class="tag_list">'.implode(' ', $out).'</ul>';

	}
	public static function fetch($list, $record_id, $section_id, $cat_id, $html_tags, $relevance, $show_nums, $max_tags)
	{
		$out = array();
		$nums = null;
		if(!count($list))
		{
			return NULL;
		}

		if ($show_nums || $relevance)
		{
			switch ($relevance)
			{
				case '1' :
					$order = 'hits DESC';
					break;
				case '2' :
					$order = 'r_usage DESC';
					break;
				default :
					$order = null;
			}

			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('t.tag, t.id');
			$query->select('(SELECT COUNT(*) FROM #__js_res_tags_history WHERE tag_id = t.id) as r_usage');
			$query->select('(SELECT SUM(hits) FROM #__js_res_tags_history WHERE tag_id = t.id) as hits');
			$query->from('#__js_res_tags AS t');
			$query->where('t.id IN (' . implode(', ', array_keys($list)) . ')');
			/*
			echo $query; //exit;
			$query->select('t.tag, h.tag_id as id, COUNT(h.record_id) as r_usage, SUM(h.hits) as hits');
			$query->from('#__js_res_tags_history AS H');
			$query->leftJoin('#__js_res_tags as t ON t.id = h.tag_id');
			$query->group('t.id');
			*/
			if ($order)
				$query->order($order);
			$db->setQuery($query);
			$res = $db->loadObjectList();

			if ($order)
				$list = array();

			foreach ( $res as $val )
			{
				if ($order)
					$list[$val->id] = $val->tag;
				switch ($show_nums)
				{
					case '1' :
						$nums[$val->id] = 'rel="tooltip" data-original-title="' . $list[$val->id] . '::' . JText::_('CTAGHITS') . ': ' . $val->hits . '"';
						break;
					case '2' :
						$nums[$val->id] = 'rel="tooltip" data-original-title="' . $list[$val->id] . '::' . JText::_('CTAGUSAGE') . ': ' . $val->r_usage . '"';
						break;
					case '3' :
						$nums[$val->id] = 'rel="tooltip" data-original-title="'.JText::_('CTAGHITS').': '.$val->hits.', '.JText::_('CTAGUSAGE').': '.$val->r_usage.'"';
						break;
				}
			}
			if ($relevance)
			{
				$html = explode(",", $html_tags);
				$total_tags = count($html) - 1;
				$step = ceil(count($list) / count($html));
				$html_id = $i = 0;
				$prev_randx = 1;
				foreach ( $list as $id => $tag )
				{
					if ($relevance == 3)
					{
						$randx = rand(0, $total_tags);
						if ($randx == $prev_randx || ! $randx)
						{
							if ($prev_randx >= $total_tags)
								$randx = $prev_randx - 2;
							else
								$randx = $prev_randx + 1;
						}
						$t = $html[$randx];
						$prev_randx = $randx;
					}
					else
					{
						if ($i == $step)
						{
							$i = 0;
							$html_id ++;
						}
						$t = $html[$html_id];
					}
					$list[$id] = sprintf('<%s class="tag">%s</%s>', trim($t), $tag, trim($t));
					$i ++;
				}
			}


		}

		if (!count($list))
		{
			return NULL;
		}

		$indexes = array();
		$i = 0;
		$ids = array_keys($list);
		while ($i < count($ids))
		{
			$randx = rand(0, (count($list) - 1));
			if(!array_key_exists($randx, $indexes))
			{
				$indexes[$randx] = $ids[$randx];
				$i++;
			}
		}
		$link = 'index.php?option=com_cobalt&task=records.filter&section_id='.$section_id.($cat_id ? '&cat_id='.$cat_id : '')
		.'&filter_name[0]=filter_tag';
		foreach ( $indexes as $i => $id)// => &$tag )
		{
			$tag = $list[$id];
			$tag =  JHtml::link(JRoute::_($link.'&filter_val[0]='.$id), $tag, ($nums ? $nums[$id] : NULL));
			$out[] = '<li class="tag_element" id="tag-' . $id . '">' .$tag. '</li>';
		}
		return implode(' ', $out);
	}
	public static function fetch2($list, $record_id, $section_id, $cat_id, $html_tags, $relevance, $show_nums, $max_tags)
	{
		$out = array();
		$nums = null;
		if(!count($list))
		{
			return NULL;
		}

		if ($show_nums || $relevance)
		{
			switch ($relevance)
			{
				case '1' :
					$order = 'hits DESC';
					break;
				case '2' :
					$order = 'r_usage DESC';
					break;
				default :
					$order = null;
			}

			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('t.tag, t.id');
			$query->select('(SELECT COUNT(*) FROM #__js_res_tags_history WHERE tag_id = t.id) as r_usage');
			$query->select('(SELECT SUM(hits) FROM #__js_res_tags_history WHERE tag_id = t.id) as hits');
			$query->from('#__js_res_tags AS t');
			$query->where('t.id IN (' . implode(', ', array_keys($list)) . ')');
			/*
			echo $query; //exit;
			$query->select('t.tag, h.tag_id as id, COUNT(h.record_id) as r_usage, SUM(h.hits) as hits');
			$query->from('#__js_res_tags_history AS H');
			$query->leftJoin('#__js_res_tags as t ON t.id = h.tag_id');
			$query->group('t.id');
			*/
			if ($order)
				$query->order($order);
			$db->setQuery($query);
			$res = $db->loadObjectList();

			if ($order)
				$list = array();

			foreach ( $res as $val )
			{
				if ($order)
					$list[$val->id] = $val->tag;
				switch ($show_nums)
				{
					case '1' :
						$nums[$val->id] = 'rel="tooltip" data-original-title="' . $list[$val->id] . '::' . JText::_('CTAGHITS') . ': ' . $val->hits . '"';
						break;
					case '2' :
						$nums[$val->id] = 'rel="tooltip" data-original-title="' . $list[$val->id] . '::' . JText::_('CTAGUSAGE') . ': ' . $val->r_usage . '"';
						break;
					case '3' :
						$nums[$val->id] = 'rel="tooltip" data-original-title="'.JText::_('CTAGHITS').': '.$val->hits.', '.JText::_('CTAGUSAGE').': '.$val->r_usage.'"';
						break;
				}
			}
			if ($relevance)
			{
				$html = explode(",", $html_tags);
				$total_tags = count($html) - 1;
				$step = ceil(count($list) / count($html));
				$html_id = $i = 0;
				$prev_randx = 1;
				foreach ( $list as $id => $tag )
				{
					if ($relevance == 3)
					{
						$randx = rand(0, $total_tags);
						if ($randx == $prev_randx || ! $randx)
						{
							if ($prev_randx >= $total_tags)
								$randx = $prev_randx - 2;
							else
								$randx = $prev_randx + 1;
						}
						$t = $html[$randx];
						$prev_randx = $randx;
					}
					else
					{
						if ($i == $step)
						{
							$i = 0;
							$html_id ++;
						}
						$t = $html[$html_id];
					}
					$list[$id] = sprintf('<%s class="tag">%s</%s>', trim($t), $tag, trim($t));
					$i ++;
				}
			}


		}

		if (!count($list))
		{
			return NULL;
		}

		$indexes = array();
		$i = 0;
		$ids = array_keys($list);
		while ($i < count($ids))
		{
			$randx = rand(0, (count($list) - 1));
			if(!array_key_exists($randx, $indexes))
			{
				$indexes[$randx] = $ids[$randx];
				$i++;
			}
		}
		$link = 'index.php?option=com_cobalt&task=records.filter&section_id='.$section_id.($cat_id ? '&cat_id='.$cat_id : '')
			.'&filter_name[0]=filter_tag';
		foreach ( $indexes as $i => $id)// => &$tag )
		{
			//$tag = $list[$id];
			//$tag =  JHtml::link(JRoute::_($link.'&filter_val[0]='.$id), $tag, ($nums ? $nums[$id] : NULL));
			$out[] = array(
				'id' => $id,
				'attr' => ($nums ? $nums[$id] : NULL),
				'tag' => $list[$id],
				'link' => JRoute::_($link.'&filter_val[0]='.$id)
			);
		}
		return $out;
	}
	public static function add_button($record_id, $max_tags, $attach_only)
	{
		$record = JModelLegacy::getInstance('Record', 'CobaltModel');
		$record = $record->getItem($record_id);
		$rtags = json_decode($record->tags, 1);

		$options['coma_separate'] = 0;
		$options['only_values'] = $attach_only;
		$options['min_length'] = 1;
		$options['max_result'] = 10;
		$options['case_sensitive'] = 0;
		$options['highlight'] = 1;
		$options['max_items'] = $max_tags;
		$options['unique'] = 1;
		$options['onAdd'] =  "index.php?option=com_cobalt&task=ajax.add_tags&tmpl=component";
		$options['onRemove'] = "index.php?option=com_cobalt&task=ajax.remove_tag&tmpl=component";
		$options['record_id'] = $record_id;

		$options['ajax_url'] = 'index.php?option=com_cobalt&task=ajax.tags_list&tmpl=component';
		$options['ajax_data'] = '';

		$out = JHtml::_('mrelements.listautocomplete', "tags$record_id", "add-tags-".$record_id, $rtags, null, $options);

		return $out;
	}
}