<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
class JHTMLMrelements
{
	public static function sort($title, $order, $direction = 'asc', $selected = 0, $task = null, $new_direction = 'asc', $tip = '')
	{
		$direction = strtolower($direction);
		$icon = array('arrow-down', 'arrow-up');
		$index = (int) ($direction == 'desc');

		if ($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		$html = '<a href="javascript:void(0);" onclick="Joomla.tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\');">';
		$html .= JText::_($title);

		if ($order == $selected)
		{
			$html .= ' <i class="icon-'.$icon[$index].'"></i>';
		}

		$html .= '</a>';

		return $html;
	}
	/**
	 * AJAX Category selector
	 *
	 * @param string $name form element name
	 * @param mixed $section 0 start from al sections, array - start from sections in array
	 * @param array $default deafult
	 * @param int $limit limit selector.
	 * @param array $ignore IDs of categories to ignore.
	 */
	public static function catselector($name, $section, $default, $limit = 0, $ignore = array())
	{

		$lang = JFactory::getLanguage();
		$lang->load('com_cobalt', JPATH_ROOT);

		$db = JFactory::getDbo();

		if(! $section)
		{
			$db->setQuery("SELECT id, name, categories FROM #__js_res_sections WHERE published = 1 AND categories > 0");
			$sections = $db->loadObjectList();
		}
		else
		{
			settype($section, 'array');

			$db->setQuery("SELECT c.id, c.title, c.path, CONCAT(s.name, '/', c.path), c.params, c.section_id, s.name as section_name,
				(SELECT count(id) FROM #__js_res_categories WHERE parent_id = c.id)  as children
    			FROM #__js_res_categories AS c
				LEFT JOIN #__js_res_sections AS s ON s.id = c.section_id
				WHERE c.published = 1 AND c.section_id IN (".implode(',', $section).") AND c.parent_id = 1  ORDER BY c.lft ASC");
			$categories = $db->loadObjectList();
			foreach($categories as &$category)
			{
				$category->params = new JRegistry($category->params);
				$category->title = htmlentities($category->title, ENT_QUOTES, 'UTF-8');
				$category->path = htmlentities($category->path, ENT_QUOTES, 'UTF-8');
			}
		}

		ArrayHelper::clean_r($default);
		JArrayHelper::toInteger($default);

		if($default)
		{
			$db->setQuery("SELECT c.id, c.title, CONCAT(s.name, '/', c.path) as path
			FROM #__js_res_categories AS c
			LEFT JOIN #__js_res_sections AS s ON s.id = c.section_id WHERE c.id IN (" . implode(',', $default) . ")");
			$defaults = $db->loadObjectList();
		}

		settype($ignore, 'array');

		ob_start();
		include_once 'mrelements/catselector.php';
		$out = ob_get_contents();
		ob_end_clean();

		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(TRUE) . '/components/com_cobalt/library/php/html/mrelements/catselector.css');

		return $out;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $name
	 * @param unknown_type $files
	 * @param array $options width, height, max_size, file_formats, max_count, ,
	 */
	public static function mooupload($name = 'filecontrol', $files = NULL, $options = array(), $field_id = 0)
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root(TRUE) . '/media/mint/js/mooupload/MooUpload.js');
		$doc->addStyleSheet(JURI::root(TRUE) . '/media/mint/js/mooupload/style.css');

		$params = new JRegistry();
		$params->loadArray($options);
		$tempname = $params->get('tmpname', substr(md5(time() . rand(1, 1000000)), 0, 5));
		$record_id = $app->input->getInt('id', 0);

		$exts = explode(',', str_replace(' ', '', $params->get('file_formats', 'zip, jpg, png, jpeg, gif, txt, md, bmp')));
		$session = JFactory::getSession();
		$session->set('width', $params->get('width', 0), md5($name));
		$session->set('height', $params->get('height', 0), md5($name));
		$session->set('max_size', $params->get('max_size', 2097152), md5($name));
		$session->set('file_formats', $exts, md5($name));
		if(! empty($files) && is_array($files))
		{
			$files = json_encode($files);
		}
		else
		{
			$files = 0;
		}

		$out[] = "
		<script type=\"text/javascript\">
			window.addEvent('domready', function() {
				var myUpload = new MooUpload('{$tempname}', {
					action: '" . JRoute::_("index.php?option=com_cobalt&task=files.upload&tmpl=component&section_id=" . $app->input->getInt('section_id') . "&record_id=" . $app->input->getInt('id') . "&type_id=" . $app->input->getInt('type_id') . "&field_id={$field_id}&key=" . md5($name), FALSE ). "',
					action_remove_file: '" . JRoute::_("index.php?option=com_cobalt&task=files.uploadremove&tmpl=component")."',
					method: '" . $params->get('method', 'auto') . "',
					tempname: '{$tempname}',
					files:" . $files . ",
					formname:'" . $name . "[]',
					autostart:" . $params->get('autostart', 0) . ",
					field_id:" . $field_id . ",
    	    		record_id:" . $record_id . ",
					maxfilesize: " . $params->get('max_size', 2097152) . ",
					exts: ['" . implode("','", $exts) . "'],
					maxfiles: " . $params->get('max_count', 1) . ",
					canDelete: " . $params->get('can_delete', 1) . ",
					allowEditTitle: " . $params->get('allow_edit_title', 1) . ",
					allowAddDescr: " . $params->get('allow_add_descr', 1) . ",
					url_root: '".JURI::root(TRUE)."',
					flash: {
				      movie: '" . JURI::root(TRUE) . "/media/mint/js/mooupload/Moo.Uploader.swf'
				    },
				    texts: {
					    error      : '" . JText::_('CERROR') . "',
					    file       : '" . JText::_('CFILE') . "',
					    filesize   : '" . JText::_('CFILESIZE') . "',
					    filetype   : '" . JText::_('CFILETYPE') . "',
					    nohtml5    : '" . JText::_('CNOHTMLSUPPORT') . "',
					    noflash    : '" . JText::_('CINSTALLFLASH') . "',
					    sel        : '" . JText::_('CACT') . "',
					    selectfile : '" . JText::_('CADDFILE') . "',
					    status     : '" . JText::_('CSTATUS') . "',
					    startupload: '" . JText::_('CCTARTUPLOAD') . "',
					    uploaded   : '" . JText::_('CUPLOADED') . "',
					    sure	   : '" . JText::_('CSURE') . "',
					    edit_descr : '" . JText::_('CEDITDESCR') . "',
					    edit_title : '" . JText::_('CEDITTITLE') . "',
					    deleting   : '" . JText::_('CDELETING') . "'
				    },

				    " . ($params->get('callback') ? "
				    onFileUpload:function(fileindex, response){
				    	" . $params->get('callback') . "(fileindex, response);
				    }," : NULL) . "
				    onFileDelete: function(error, filename){
						if(error == '1016')
						{
							msg = '" . JText::sprintf('CERR_FILEDOSENTDELETED', "' + filename", array('jsSafe' => true)) . ";
						}
						if(error == '1017')
						{
							msg = '" . JText::sprintf('CERR_FILEDOSENTEXIST', "' + filename", array('jsSafe' => true)) . ";
						}
						if(error)
						{
							Cobalt.fieldError(".$field_id.", msg);
				    	}
					},
					onSelectError: function(error, filename, filesize){
						var msg = error;
						if(error == '1012')
						{
							msg = '" . JText::sprintf('CERR_FILEUPLOADLIMITREACHED', $params->get('max_count', 1), array('jsSafe' => true)) . "';
						}
						if(error == '1013')
						{
							msg = '" . JText::sprintf('CERR_EXTENSIONNOTALLOWED', "' + filename + '", array('jsSafe' => true)) . "';
						}
						if(error == '1014')
						{
							msg = '" . JText::sprintf('CERR_UPLOADEDFILESIZESMALLER', "' + filename + '", array('jsSafe' => true)) . "';
						}
						if(error == '1015')
						{
							msg = '" . JText::sprintf('CERR_UPLOADEDFILESIZEBIGGER', "' + filename + '", array('jsSafe' => true)) . "';
						}
						Cobalt.fieldError(".$field_id.", msg);
					}
				});

			});
		</script>";

		$out[] = '<div id="' . $tempname . '" class="upload-element"></div>';

		if($exts)
		{
			$out[] = '<br/><span class="small">' . JText::_('CER_ONLYFORMATS') . ': <b>' . implode("</b>, <b>", $exts) . '</b></span>';
		}
		$out[] = '<br/><span class="small">' . JText::_('CNSG_MAXSIZEPERFILE') . ': <b>' . HTMLFormatHelper::formatSize($params->get('max_size', 2097152)) . '</b></span>';

		return implode("\n", $out);

	}

	public static function autocompleteitem($html, $id = NULL)
	{
		$o = new stdClass();

		$o->id = ($id ? $id : strip_tags($html));
		$o->html = $html;
		$o->plain = strip_tags($html);

		return $o;
	}

	public static function listautocomplete($name, $id, $default = array(), $list = array(), $options = array())
	{
		$params = new JRegistry();
		$params->loadArray($options);

		settype($default, 'array');

		if($params->get('only_values', 0) == 1 && ! $list && ! $params->get('ajax_url'))
		{
			return '<input type="hidden" name="' . $name . '" id="' . $id . '" value="" />';
		}

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root(TRUE) . '/media/mint/js/autocomplete/style.css');
		$doc->addScript(JURI::root(TRUE) . '/media/mint/js/autocomplete/GrowingInput.js');
		$doc->addScript(JURI::root(TRUE) . '/media/mint/js/autocomplete/TextboxList.js');
		$doc->addScript(JURI::root(TRUE) . '/media/mint/js/autocomplete/TextboxList.Autocomplete.js');
		$doc->addScript(JURI::root(TRUE) . '/media/mint/js/autocomplete/TextboxList.Autocomplete.Binary.js');

		$el = $add = $skip = $a = array();
		$script = NULL;
		$patern = '["%s", "%s", "%s"]';

		foreach($default as $key => &$def)
		{
			if(! is_object($def))
			{
				$def = self::autocompleteitem($def, $key);
			}

			if(! $def->id) continue;

			$add[] = sprintf('add("%s", "%s", "%s")', str_replace('"', '\\"', stripslashes($def->plain)), str_replace('"', '\\"', stripslashes($def->id)), str_replace('"', '\\"', stripslashes($def->html)));
			$skip[] = $def->id;
		}

		settype($list, 'array');

		foreach($list as &$item)
		{
			if(! is_object($item))
			{
				$item = self::autocompleteitem($item);
			}

			if(in_array($item->id, $skip)) continue;
			if(! trim($item->id)) continue;

			$el[] = sprintf($patern, str_replace('"', '\\"', stripslashes($item->id)), str_replace('"', '\\"', stripslashes($item->plain)), str_replace('"', '\\"', stripslashes($item->html)));
		}

		$a[] = "\nplaceholder: '" . JText::_('CTYPETOSUGGEST') . "'";
		$a[] = "\nremote:{ emptyResultPlaceholder:'" . JText::_('CNOSUGGEST') . "', loadPlaceholder:'" . JText::_('CPLSWAIT') . "'}";
		$a[] = "\nwidth: '" . $params->get('min_width', 300) . "'";
		$a[] = "\nminLength: " . $params->get('min_length', 1);
		$a[] = "\nmaxResults: " . $params->get('max_result', 10);
		if($params->get('only_values', 0) == 1)
		{
			$a[] = "\nonlyFromValues: 1";
		}
		if($params->get('case_sensitive', 0))
		{
			$a[] = "\ninsensitive: false";
		}
		if($params->get('highlight', 0) == 0)
		{
			$a[] = "\nhighlight: false";
		}

		$additional[] = "\nplugins: {autocomplete: {" . implode(',', $a) . "}}";

		if($params->get('coma_separate', 0)) // && !count($el))
		{
			$additional[] = "\nbitsOptions : { editable : {addKeys:188}}";
		}
		if($params->get('max_items', 0))
		{
			$additional[] = "\nmax : " . $params->get('max_items', 0);
		}
		if($params->get('unique', 0))
		{
			$additional[] = "\nunique: true ";
		}
		if($params->get('separateby', 0))
		{
			$additional[] = "\n".'decode: function(o) {
				return o.split(\''.$params->get('separateby').'\');
			},
			encode: function(o) {
					return o.map(function(v) {
					v = ($chk(v[0]) ? v[0] : v[1]);
					return $chk(v) ? v : null;
				}).clean().join(\''.$params->get('separateby').'\');
			}';
		}

		$additional[] = "\ntexts:{ limit : '".JText::_('C_JSLIMITOPTIONS')."'	}";

		$uniq = substr(md5(time() . '-' . rand(0, 1000)), 0, 5);
		$options = '{' . implode(',', $additional) . '}';

		$html[] = '<input type="text" name="' . $name . '" id="' . $id . '" value="" />';
		$html[] = "<script type=\"text/javascript\">";
		$html[] = "var default{$uniq} = ['" . (count($skip) ? implode("','", $skip) : '') . "'];\n";
		$html[] = "var t{$uniq} = new jQuery.TextboxList('#{$id}', {$options});\n";


		$html[] = "t{$uniq}.addEvent('bitBoxRemove', function(box) {";
		if($params->get('max_items', 0))
		{
			//$html[] = "if($('#hidden-{$uniq}')) $('#hidden-{$uniq}').show();";
		}

		if($params->get('onRemove', 0))
		{
			$html[] = "
				jQuery(box).css('background-image', 'url(\"" . JURI::root(TRUE) . "/media/mint/js/mooupload/imgs/load_bg_blue.gif\")');

				jQuery.ajax({
					url:'" . JRoute::_($params->get('onRemove'), FALSE) . "',
					type:'POST',
					dataType:'json',
					data:{
						rid: " . JFactory::getApplication()->input->getInt('id') . ",
						tid: box.value[0]
					}
				}).done(function(json) {
					jQuery(box).css('background-image', '');
					if(json.success)
					{
						jQuery.each(default{$uniq}, function(key, item){
							if(box.value[0] == item)
								default{$uniq}.splice(key);
						});
						//t{$uniq}.update();
					}
					else
					{
						alert(json.error);
					}
				});";
		}
		$html[] = "});";


		$html[] = "t{$uniq}.addEvent('bitBoxAdd', function(box){
				if(box.value[0])
				{
					if(default{$uniq}.contains(box.value[0].toString()))
					{
						return;
					}
				}
			";
		if($params->get('max_items', 0))
		{
			$html[] = "var parent = $(box).parents('ul.textboxlist-bits');
			if(parent.children('li.textboxlist-bit.textboxlist-bit-box').length >= ".$params->get('max_items', 0).")
			{
				//parent.children('li').last().hide().attr('id', 'hidden-{$uniq}');

			}";
		}

		if($params->get('onAdd', 0))
		{
			$html[] = ($params->get('max_items', 0) ? "if(default{$uniq}.length > " . $params->get('max_items', 0) . "){ alert('" . JText::_('CTAGLIMITREACHED') . "'); return;}" : "") . "

				jQuery(box).css('background-image', 'url(\"" . JURI::root(TRUE) . "/media/mint/js/mooupload/imgs/load_bg_blue.gif\")');

				jQuery.ajax({
					url:'" . JRoute::_($params->get('onAdd'), FALSE) . "',
					type:'POST',
					dataType:'json',
					data:{
						rid: " . $params->get('record_id', 0) . ",
						val: box.value,
						max: " . $params->get('max_items', 0) . "
					}
				}).done(function(json) {
					jQuery(box).css('background-image', '');
					if(!json)
					{
						return;
					}
					if(json.success)
					{
						box.value = json.result;
						default{$uniq}.push(json.result[0]);
						//t{$uniq}.update();
					}
					else
					{
						alert(json.error);
					}
			    });";
		}
		$html[] = "});";


		if($add)
		{
			$html[] = "t{$uniq}." . implode(".", $add) . ";\n";
		}
		if($el)
		{
			$html[] = "var r{$uniq} = [" . implode(",", $el) . "];\n";
			$html[] = "t{$uniq}.plugins['autocomplete'].setValues(r{$uniq});\n";
		}
		if($params->get('ajax_url'))
		{
			//$html[] = "t{$uniq}.container.addClass('textboxlist-loading');\n";
			$html[] = "jQuery.ajax({
				url:'" . JRoute::_($params->get('ajax_url'), FALSE). "',
				type:'POST',
				dataType:'json',
				data:{" . $params->get('ajax_data') . "}
			}).done(function(json) {
				if(!json.success)
				{
					alert(json.error);
					return;
				}
				if(json.result)
				{
					//t{$uniq}.container.removeClass('textboxlist-loading');
					t{$uniq}.plugins['autocomplete'].setValues(json.result);
				}
		    });";
		}

		$html[] = "</script>\n";

		return implode("\n", $html);
	}
}