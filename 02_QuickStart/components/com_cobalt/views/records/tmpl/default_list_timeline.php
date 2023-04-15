<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
$params = $this->tmpl_params ['list'];
$doc = JFactory::getDocument();
$doc->addScript(JUri::root(TRUE).'/components/com_cobalt/views/records/tmpl/default_list_timeline/js/storyjs-embed.js');
function _to_json($item, $that)
{
	$params = $that->tmpl_params['list'];
	$obj = $asets = $as = $ob = array();

	$obj['headline'] = sprintf('<a href=%s%s>%s%s</a>', JRoute::_($item->url), ($item->nofollow ? 'rel=nofollow' : ''), htmlentities($item->title, ENT_QUOTES, 'UTF-8'), CEventsHelper::showNum('record', $item->id));

	$obj['startDate'] = JHtml::_('date', $item->created, 'Y,m,d,H,i,s');

	if($params->get('tmpl_params.field_id_date_start')) {
		$date = @$item->fields_by_key[$that->fields_keys_by_id[$params->get('tmpl_params.field_id_date_start')]]->value;
		if($date)
		{
			if(is_array($date))
			{
				$date = $date[0];
			}
		}
		$obj['startDate'] = JHtml::_('date', $date, 'Y,m,d,H,i,s');
	}

	if(is_object($item->extime))
	{
		$obj['endDate'] = JHtml::_('date', $item->expire, 'Y,m,d,H,i,s');
	}

	if($params->get('tmpl_params.field_id_date_end')) {
		$date = @$item->fields_by_key[$that->fields_keys_by_id[$params->get('tmpl_params.field_id_date_end')]]->value;
		if($date)
		{
			if(is_array($date))
			{
				$date = $date[0];
			}
		}
		$obj['endDate'] = JHtml::_('date', $date, 'Y,m,d,H,i,s');
	}

	$tkey = @$that->fields_keys_by_id[$params->get('tmpl_params.field_id_text')];
	if(!empty($item->fields_by_key[$tkey]))
	{
		$obj['text'] = str_replace(array("\n", "\r"), "", strip_tags($item->fields_by_key[$that->fields_keys_by_id[$params->get('tmpl_params.field_id_text')]]->result));
	}


	foreach ($item->fields_by_key AS $key => $field)
	{
		$asets['caption'] = '';
		$asets['credit'] = '';

		if($key == @$that->fields_keys_by_id[$params->get('tmpl_params.field_id_geo')] && !empty($field->value['position']['lat']) && !empty($field->value['position']['lng']))
		{
			$asets['media'] = sprintf("https://maps.google.com/maps?q=%s,%s", $field->value['position']['lat'], $field->value['position']['lng']);
			break;
		}

		if($key == @$that->fields_keys_by_id[$params->get('tmpl_params.field_id_video')] && !empty($field->value['link'][0]))
		{
			$asets['media'] = $field->value['link'][0];
			break;
		}

		if($key == @$that->fields_keys_by_id[$params->get('tmpl_params.field_id_image')] && ($field->type == 'image') 
		&& !empty($field->value['image']))
		{
			$url = CImgHelper::getThumb(JPATH_ROOT . '/'. $field->value['image'], $params->get('tmpl_params.mwidth', 300), $params->get('tmpl_params.mheight', 200), 'image', $item->user_id,
				array(
					'mode' => $field->params->get('params.thumbs_list_mode', 1),
					'strache' => $field->params->get('params.thumbs_list_stretch', 0),
					'quality' => $field->params->get('params.thumbs_list_quality', 80)));

			$asets['media'] = $url;
			break;
		}

		if($key == @$that->fields_keys_by_id[$params->get('tmpl_params.field_id_image')] && ($field->type == 'gallery') && !empty($field->value[0]['fullpath']))
		{
			$file = JPATH_ROOT . DIRECTORY_SEPARATOR . JComponentHelper::getParams('com_cobalt')->get('general_upload') .
			DIRECTORY_SEPARATOR . $subfolder = $field->params->get('params.subfolder', $field->value[0]['ext']) . DIRECTORY_SEPARATOR . $field->value[0]['fullpath'];

			$url = CImgHelper::getThumb($file, $params->get('tmpl_params.mwidth', 300), $params->get('tmpl_params.mheight', 200), 'gallery', $item->user_id,
				array(
						'mode' => $field->params->get('params.thumbs_list_mode', 1),
						'strache' => $field->params->get('params.thumbs_list_stretch', 0),
						'quality' => $field->params->get('params.thumbs_list_quality', 80)));

			$asets['media'] = $url;
		}

		if($key == @$that->fields_keys_by_id[$params->get('tmpl_params.field_id_link')] && !empty($field->value[0]['url']))
		{
			$asets['media'] = $field->value[0]['url'];
			$asets['caption'] = $field->value[0]['url'];
			$asets['credit'] = $field->value[0]['label'];
			break;
		}

	}

	foreach ($asets AS $k => $v)
		$as[] = '"'.$k.'":"'.htmlentities($v, ENT_COMPAT, 'UTF-8').'"';
	
	foreach ($obj AS $k => $v)
		$ob[] = '"'.$k.'":\''.$v.'\'';

	$out = '{'.implode(",", $ob).',asset:{'.implode(",", $as).'}}';
	return $out;
}
?>
<style>
#my-timeline .container {
	border: 0px solid;
}
</style>
<div id="my-timeline"></div>
<script type="text/javascript">
jQuery(document).ready(function() {
    createStoryJS({
	    type:       'timeline',
	    width:      '<?php echo $params->get('tmpl_params.width', '100%');?>',
	    height:     '<?php echo $params->get('tmpl_params.height', '600');?>',
	    start_at_slide:     '<?php echo $params->get('tmpl_params.start');?>',
	    start_zoom_adjust:  '-2',
	    start_at_end: <?php echo $params->get('tmpl_params.start_at_end', 'false');?>,
	    source: {
	        "timeline":
	        {
	            "headline":"<?php echo $params->get('tmpl_params.headline');?>",
	            "type":"default",
	    		"text":"<?php echo $params->get('tmpl_params.headlinetext');?>",
	    		"date": [
		     	     <?php foreach ($this->items AS $item):?>
		     	     	<?php echo _to_json($item, $this);?>,
		     	     <?php endforeach;?>
	            ]
            }
	    },
	    embed_id:   'my-timeline'
    });
});
</script>