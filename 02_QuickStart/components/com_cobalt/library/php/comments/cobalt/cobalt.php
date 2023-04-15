<?php
include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/cobaltcomments.php';

class CobaltCommentsCobalt extends CobaltComments
{

	public function getNum($type, $item)
	{
		static $out = array();

		if(isset($out[$item->id]))
		{
			return $out[$item->id];
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(TRUE);
		$query->select("count(*)");
		$query->from("#__js_res_record");
		$query->where("parent_id = " . $item->id);
		$query->where("parent = 'com_cobalt'");
		$query->where("published = 1");
		$query->where("hidden = 0");
		$db->setQuery($query);

		$out[$item->id] = $db->loadResult();

		return $out[$item->id];
	}

	public function getComments($type, $item)
	{
		$app = JFactory::getApplication();
		if(!$type->params->get('comments.type_id') || !$type->params->get('comments.section_id'))
		{
			JError::raiseNotice(500, 'Not all parameters set to display comments');

			return;
		}

		$user = JFactory::getUser();
		if(!in_array($type->params->get('comments.access', 1), $user->getAuthorisedViewLevels()))
		{
			return;
		}

		$out = $new = '';

		$stype   = ItemsStore::getType($type->params->get('comments.type_id'));
		$section = ItemsStore::getSection($type->params->get('comments.section_id'));

		$app->input->set('parent_id', $item->id);
		$app->input->set('parent', 'com_cobalt');
		$app->input->set('parent_user_id', $item->user_id);
		$app->input->set('parent_see_special', $type->params->get('comments.author_see'));

		include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'api.php';
		$api     = new CobaltApi();
		$records = $api->records($section->id, 'children', $type->params->get('comments.orderby'), array($stype->id), NULL, $type->params->get('comments.catid', 0), $type->params->get('comments.limit', 5), $type->params->get('comments.tmpl_list'));

		$app->input->set('parent', 0);
		$app->input->set('parent_id', 0);

		if((in_array($type->params->get('comments.button_access'), $user->getAuthorisedViewLevels()) || ($type->params->get('comments.button_access') == -1 && $item->user_id && $user->get('id') == $item->user_id)) && $item->params->get('comments.comments_access_post', 1))
		{
			$url = 'index.php?option=com_cobalt&view=form&section_id=' . $section->id;
			$url .= '&type_id=' . $stype->id . ':' . JApplication::stringURLSafe($stype->name);
			if($type->params->get('comments.catid', 0))
			{
				$url .= '&cat_id=' . $type->params->get('comments.catid', 0);
			}
			$url .= '&parent_id=' . $item->id;
			$url .= '&Itemid=' . $section->params->get('general.category_itemid');
			$url .= '&return=' . Url::back();
			$new = '<a class="' . $type->params->get('comments.new_class', 'btn btn-primary btn-large') . '" href="' . JRoute::_($url) . '">' . $type->params->get('comments.button') . '</a>';
		}
		else
		{
			if($records['total'] == 0)
			{
				return;
			}
		}

		if($type->params->get('comments.rating') && $records['total'])
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(TRUE);
			$query->select("AVG(votes_result)");
			$query->from("#__js_res_record");
			$query->where("parent_id = " . $item->id);
			$query->where("parent = 'com_cobalt'");
			if(CStatistics::hasUnPublished($section->id))
			{
				$query->where("published = 1");
			}
			$query->where("hidden = 0");
			$db->setQuery($query);

			$result = $db->loadResult();
			$out .= '<div id="rating-block" class="pull-right">' . JText::_('CTOTALRATING') . ': ' . RatingHelp::loadRating($type->params->get('comments.tmpl_rating', 'default'), $result, 0, 0, 'Cobalt.ItemRatingCallBack', 0, 0) . '</div>';

		}


		$out .= '<div class="page-header"><h2>' . $type->params->get('comments.title') . '</h2></div>';

		if($item->params->get('comments.comments_access_post', 1) === 0 && $records['total'])
		{
			$out = '<div class="alert alert-warning">' . JText::_('CMSG_COMMENTSDISABLED') . '</div>';
		}

		if(in_array($type->params->get('comments.new_position', 2), array(1, 3)))
		{
			$out .= $new;
		}

		$descr = $type->params->get('comments.descr');
		if($descr)
		{
			if(strlen($descr) == strlen(strip_tags($descr)))
			{
				$descr = "<p>{$descr}</p>";
			}
			$out .= $descr;
		}

		$out .= $records['html'];


		if(in_array($type->params->get('comments.new_position', 2), array(2, 3)))
		{
			$out .= $new;
		}


		if($records['total'] > $type->params->get('comments.limit', 5))
		{
			$url = 'index.php?option=com_cobalt&view=records&section_id=' . $section->id;
			$url .= '&parent_id=' . $item->id;
			$url .= '&parent=' . $app->input->get('option');
			$url .= '&view_what=children';
			$url .= '&page_title=' . urlencode(base64_encode(JText::sprintf($type->params->get('comments.title2', 'All discussions of %s'), $item->title)));
			$url .= '&Itemid=' . $section->params->get('general.category_itemid');
			$url .= '&return=' . Url::back();
			$out .= '<a class="btn btn-large" href="' . JRoute::_($url) . '">' . $type->params->get('comments.button2') . '</a>';
		}

		return $out;
	}

	public function getIndex($type, $item)
	{
		$db = JFactory::getDbo();

		$db->setQuery("SELECT fieldsdata FROM #__js_res_record WHERE published = 1 AND hidden = 0 AND parent_id = {$item->id} AND parent = 'com_cobalt'");
		$list = $db->loadColumn();

		return implode(', ', $list);
	}

	public function getLastComment($type, $item)
	{
		if(self::enable())
		{
			$comment = JComments::getLastComment($item->id, 'com_cobalt');

			return 'User "' . $comment->name . '" wrote "' . $comment->comment . '" (' . $comment->date . ')';
		}
	}
}

