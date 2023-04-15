<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
JFactory::getDocument()->addScript(JURI::root().'media/mint/js/notimoo/notimoo-v1.2.1.js');
JFactory::getDocument()->addStyleSheet(JURI::root().'media/mint/js/notimoo/notimoo-v1.2.1.css');
$options = array();
if($params->get('ntmoo_parent') != '')
	$options[] = "parent: '{$params->get('ntmoo_parent')}'";
if($params->get('ntmoo_width', 300) != '')
	$options[] = "width: {$params->get('ntmoo_width', 300)}";
if($params->get('ntmoo_height', 50) != '')
	$options[] = "height: {$params->get('ntmoo_height', 50)}";
if($params->get('ntmoo_time', 5000) != '')
	$options[] = "visibleTime: {$params->get('ntmoo_time', 5000)}";
$options[] = "locationVType: '{$params->get('ntmoo_locationv', 'top')}'";
$options[] = "locationHType: '{$params->get('ntmoo_locationh', 'right')}'";
if($params->get('ntmoo_positionv', 10) != '')
	$options[] = "locationVBase: {$params->get('ntmoo_positionv', 10)}";
if($params->get('ntmoo_positionh', 10) != '')
	$options[] = "locationHBase: {$params->get('ntmoo_positionh', 10)}";
if($params->get('ntmoo_margin', 5) !='')
	$options[] = "notificationsMargin: {$params->get('ntmoo_margin', 5)}";
if($params->get('ntmoo_opacity_time', 750) != '')
	$options[] = "opacityTransitionTime: {$params->get('ntmoo_opacity_time', 750)}";
if($params->get('ntmoo_close_time', 750) != '')
	$options[] = "closeRelocationTransitionTime: {$params->get('ntmoo_close_time', 750)}";
if($params->get('ntmoo_scroll_time', 750) != '')
	$options[] = "scrollRelocationTransitionTime:{$params->get('ntmoo_scroll_time', 750)}";
if($params->get('ntmoo_opacity', '0.95') != '')
	$options[] = "notificationOpacity: {$params->get('ntmoo_opacity', '0.95')}";
$options[] = "onClose: function(el) {
			var attr = el.get('id').split('-');
			modMarkRead(attr[1]);
}";
$js = "
window.addEvent('domready', function(event){
       var notimooManager = new Notimoo({
	   		".(count($options) ? implode(",\n", $options) : '')."
	   });
		var mod_ntfcs = [];
		setTimeout(modGetNewNotifications, 3000);
		function modGetNewNotifications()
		{
		 var req = new Request.JSON({
					url: '".JRoute::_('index.php?option=com_cobalt&task=ajax.get_notifications')."',
					method:'post',
					autoCancel:true,
					data:{exist: mod_ntfcs, section_id: [".$sections."] },
					onComplete: function(json) {
						if(!json.success)
						{
							return;
						}
						var n = mod_ntfcs.length;
				   		Array.each(json.result, function (item, k) {
							notimooManager.show({
								message: item.html,
								".($params->get('ntmoo_class', '') != '' ? "customClass: '{$params->get('ntmoo_class')}', " : '' )."
								".($params->get('ntmoo_time', 5000) != '' ? "visibleTime: k * ".$params->get('ntmoo_time', 5000).", " : '')."
								sticky: ".$params->get('ntmoo_sticky').",
								id: 'mod_ntfc-'+item.id
							});
							mod_ntfcs[n] = item.id;
							n++;
						});
				   		if($$('.notimoo').length)
							document.title = '('+$$('.notimoo').length+') ' + notimoo_doc_title;
						else
							document.title = notimoo_doc_title;

					}
				}).send();
			setTimeout(modGetNewNotifications, (".$params->get('ntmoo_updtime', 60)." * 1000));
		}";
$js .= "
});
var notimoo_doc_title = document.title;
function modMarkRead(id)
{
	var req = new Request.JSON({
		url: '".JRoute::_('index.php?option=com_cobalt&task=ajax.mark_notification')."',
		method: 'post',
		autoCancel: true,
		data:{id: id},
		onComplete: function(json) {
			if(!json.success)
			{
				alert(json.error);
				return;
			}
			if($('mod_ntfc-'+id))
				$('mod_ntfc-'+id).destroy();
			if($$('.notimoo').length)
			document.title = '('+$$('.notimoo').length+') ' + notimoo_doc_title;
			else
			document.title = notimoo_doc_title;

		}
	}).send();
}
";
JFactory::getDocument()->addScriptDeclaration($js);
?>
