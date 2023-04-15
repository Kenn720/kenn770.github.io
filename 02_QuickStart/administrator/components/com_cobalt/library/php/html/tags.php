<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class JHTMLTags
{
	public static function form($record, $section_id)
	{
		$model = JModelLegacy::getInstance('Article', 'ResModel');
		$tags = $model->getTags($record->id);
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$tabs = JPane::getInstance('tabs');

		$html = '<input type="hidden" class="inputbox" name="tags" value=", '.$tags.'" id="alltags" />';

		$tags = explode(", ", $tags);
		$escape = array();

		foreach ($tags AS $tag)
		{
			$tag = trim($tag);
			if(!$tag) continue;
			$t[] = JHTML::_('tags.tag', $tag);
			$escape[] = $db->quote(trim($tag));
		}

		$html .= '<p id="tag_list">';
		if(@$t) $html .= implode("", $t);
		$html .= '</p>';

		if($escape) $where = " AND t.tag NOT IN (".implode(",", $escape).") ";

		$all = array();
		$alltags = array();
		$html2 = ''; $i = 0;

		if($user->get('id'))
		{
			$sql = "SELECT t.tag FROM #__js_res_tags AS t
			LEFT JOIN #__js_res_tags_history AS th On th.tag_id = t.id
			WHERE th.section_id = {$section_id} ". @$where .
			"AND th.user_id = ".$user->get('id').
			" GROUP BY t.id
			ORDER BY t.tag ASC";

			$db->setQuery($sql);
			$alltags = $db->loadObjectList();

			if($alltags)
			{
				$link = sprintf('<span id="tag_show_link"><a href="javascript:void(0);" onclick="tag_show_my()">%s</a></span>', JText::_('CCHOSETAG'));
				$html2 .= '<div id="tags_my" style="display:none; clear:both">';
				if(count($alltags) < 20)
				{
					foreach ($alltags as $item) {
						$a[] = sprintf('<span id="tagl%d" class="tag_item" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s</span>', ++$i, $item->tag, $i, $item->tag);
					}

					$html2 .= implode(" ", @$a).'<div style="clear:both"></div>';
				}
				else
				{
					$sql = "SELECT t.tag, th.hits FROM #__js_res_tags AS t
					LEFT JOIN #__js_res_tags_history AS th On th.tag_id = t.id
					WHERE th.section_id = {$section_id} ". @$where .
					"AND th.user_id = ".$user->get('id').
					" GROUP BY t.id
					ORDER BY th.hits DESC
					LIMIT 20";

					$db->setQuery($sql);
					$hits = $db->loadObjectList();

					if($hits) $all['h'] = $hits;

					$sql = "SELECT t.tag, th.ctime FROM #__js_res_tags AS t
					LEFT JOIN #__js_res_tags_history AS th On th.tag_id = t.id
					WHERE th.section_id = {$section_id} ". @$where .
					"AND th.user_id = ".$user->get('id').
					" GROUP BY t.id
					ORDER BY th.ctime DESC
					LIMIT 20";

					$db->setQuery($sql);
					$last = $db->loadObjectList();

					$sql = "SELECT t.tag, count(th.record_id) as total FROM #__js_res_tags AS t
					LEFT JOIN #__js_res_tags_history AS th On th.tag_id = t.id
					WHERE th.section_id = {$section_id} ". @$where .
					"AND th.user_id = ".$user->get('id').
					" GROUP BY t.id
					ORDER BY total DESC
					LIMIT 20";

					$db->setQuery($sql);
					$used = $db->loadObjectList();

					if($last) $all['l'] = $last;

					$html2 .=  $tabs->startPane('tags-pane');

					$html2 .=  $tabs->startPanel(JText::_('CLATESTTAGS'), 'tlt1');
					foreach ($last as $item) {
						$date = JFactory::getDate($item->ctime);
						$now = JFactory::getDate();
						if($now->format('%d') == $date->format('%d'))
						{
							$lbl = JText::_('CTODAY');
						}
						elseif(($now->format('%d') - 1) == $date->format('%d'))
						{
							$lbl = JText::_('CYESTERDAY');
						}
						else
						{
							$diff = $now->toUnix() - $date->toUnix();
							$n = round($diff / 86400);
							$lbl = $n.' '.JText::_('CDAYAGO');
						}

						$l[] = sprintf('<span id="tagl%d" class="tag_item" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s <span class="small">(%s)</span></span>', ++$i, $item->tag, $i, $item->tag, $lbl);
					}
					$html2 .= implode(' ', $l).'<div style="clear:both"></div>';
					$html2 .=  $tabs->endPanel();

					$html2 .=  $tabs->startPanel(JText::_('CMOSTUSE'), 'tmu1');
					foreach ($used as $item) {
						$lbl = $item->total.' '.JText::_('CRECORDS');

						$u[] = sprintf('<span id="tagl%d" class="tag_item" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s <span class="small">(%s)</span></span>', ++$i, $item->tag, $i, $item->tag, $lbl);
					}
					$html2 .= implode(' ', $u).'<div style="clear:both"></div>';
					$html2 .=  $tabs->endPanel();

					$html2 .=  $tabs->startPanel(JText::_('CMOSTPOP'), 'tmp1');
					foreach ($hits as $item) {
						$lbl = $item->hits.' '.JText::_('CHITS');

						$h[] = sprintf('<span id="tagl%d" class="tag_item" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s <span class="small">(%s)</span></span>', ++$i, $item->tag, $i, $item->tag, $lbl);
					}
					$html2 .= implode(' ', $h).'<div style="clear:both"></div>';
					$html2 .=  $tabs->endPanel();

					$html2 .=  $tabs->endPane();
				}
				$html2 .= '</div>';
			}

		}
		$html .= sprintf('<p style="clear:both"><img id="tag_image" src="%s/components/com_resource/images/tag-icon.png" align="absmiddle"><input onkeyup="getTagSuggestions(this.value);" type="text" name="tag" id="tag_input" class="inputbox" /> <input type="button"  onclick="tag_insert(document.getElementById(\'tag_input\').value);" class="button" value="%s" /> %s <br><span class="small">%s</span></p><p style="clear:both" id="search_tags_result"> </p>', JURI::root(TRUE), JText::_('CADD'), ($alltags ? $link : NULL), JText::_('CENTERSEPARATE'));

		$html .= @$html2;

		return $html;
	}

	public static function tag($tag)
	{
		static $i; $i++;
		$out = sprintf(' <span id="etag%d" class="tag_item"><img align="absmiddle" src="%s/components/com_resource/images/tag_delete.png" class="hasTooltip" title="::%s %s" style="cursor:pointer" onclick="deleteTag(\'%s\', \'etag%d\')" /> %s</span>', $i, JURI::root(TRUE), JText::_('CDELETETAG'), $tag, $tag, $i, $tag);
		return $out;
	}
	public static function tag2($tag, $i)
	{
		//static $i; $i++;
		$out = sprintf('<span id="tagl%d" class="tag_item tag" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s</span>', $i, $tag, $i, $tag);
		return $out;
	}
	public static function tag3()
	{
		
	}
	public static function list_tags($item, $section_id, $iparams, $params)
	{
		static $numbers = array(); $out = '';

		if(!$iparams->get('item_tag')) return FALSE;
		
		$id = $item->id;
		$db = JFactory::getDBO();
		$url = JFactory::getURI();
		$user = JFactory::getUser();
		$section_id ? NULL : $section_id = JRequest::getInt('category_id');

		$sql = "SELECT t.id, t.tag  FROM #__js_res_tags_history AS h
		LEFT JOIN #__js_res_tags AS t ON t.id = h.tag_id
		WHERE h.record_id = {$id} GROUP BY t.id";
		$db->setQuery($sql);
		$tags = $db->loadObjectList();
		$cat_id = ResHelper::getCategorySection($section_id);
		$params->merge($iparams);
		
		$script = "function addTagToRecord(rid)
{
	var tf = document.getElementById('atfid'+rid);
	tf.style.display = 'none';
	
	var tv = document.getElementById('new_tag_input'+rid);
	
	string = tv.value;
	tv.value = '';
	
	var tni = document.getElementById('load_image'+rid);
	tni.style.display = 'inline';
	
	xajax_jsAddTag(rid, string);
}";
			
		//$document = JFactory::getDocument();
		//$document->addScriptDeclaration($script);
		
		$where = getFilterWhere($params);
		foreach ($tags AS $tag)
		{
			$num = false; $options = array();
			if($params->get('category_mode') == 2 && $item->user_id)
			{
				$options['user_id'] = $item->user_id;
				$options['view_what'] = 'created';
			}
			$link = MEUrl::link_list(($params->get('filters_mode') == 2 ? $cat_id : $section_id), $options);
			$link .= '&filter_tag='.$tag->id;
			$link = MERoute::_($link);

			if ($params->get('item_tag_num'))
			{
				if(@$numbers[$tag->id][$section_id])
				{
					$tag->tag .= " (".$numbers[$tag->id][$section_id].") ";
				}
				else
				{
					switch ($params->get('filters_mode', 1))
					{
						case 1:
							$sql = "SELECT h.id FROM #__js_res_tags_history AS h
							LEFT JOIN #__js_res_record AS r ON r.id = h.record_id
							LEFT JOIN #__js_res_record_category AS rc ON rc.record_id = r.id
							WHERE rc.catid = {$section_id} AND h.tag_id = {$tag->id} {$where} GROUP BY h.id";
							break;
						case 3:
							//$category = JRequest::getInt('category_id', $section_id);
							$ids = ResHelper::getCategoryChildrenIds($section_id);
							$sql = "SELECT h.id FROM #__js_res_tags_history AS h
							LEFT JOIN #__js_res_record AS r ON r.id = h.record_id
							LEFT JOIN #__js_res_record_category AS rc ON rc.record_id = h.record_id
							WHERE rc.catid IN ({$ids}) AND h.tag_id = {$tag->id} {$where} GROUP BY h.id";
							break;
						case 2:
							$sql = "SELECT h.id FROM #__js_res_tags_history AS h
							LEFT JOIN #__js_res_record AS r ON r.id = h.record_id
							WHERE h.section_id = {$cat_id} AND h.tag_id = {$tag->id} {$where}";
							break;
					}
					$db->setQuery($sql);
					$db->query();
					$num = $db->getNumRows();
					$tag->tag .= " ({$num}) ";
					$numbers[$tag->id][$section_id] = $num;
				}
			}

			$t[] = JHTML::link( $link, $tag->tag, array());
		}
		if(@$t) $out = implode(", ", $t);
		
		if(MEAccess::isAdmin() || MEAccess::isAuthor($item->user_id) || ($user->get('aid') >= $params->get('item_tag_access') && !($params->get('item_tag_access') == 'none')))
		{
			
			$out .= ' <span id="new_tags'.$item->id.'"></span> '.
			JHTML::image(JURI::root(TRUE).'/components/com_resource/images/load.gif', '', array('id'=>'load_image'.$item->id, 'style' => 'display:none'))
			.' <span style="display:none" id="atfid'.$item->id.'"><input type="text" class="inputbox" id="new_tag_input'.$item->id.'" /> <input type="button" class="button" value="'. JText::_('CADD') .'" onclick="addTagToRecord('.$item->id.')" /></span>'.JHTML::image(JURI::root(TRUE).'/components/com_resource/images/tag-icon-plus.png', JText::_('CADDTAGS'), array('id'=>'tag_img_id'.$item->id, 'align'=>'absmiddle', 'onclick'=>'document.getElementById(\'atfid'.$item->id.'\').style.display = \'block\';', 'style'=>'cursor:pointer'));
		}
		
		return $out;
	}
}
?>