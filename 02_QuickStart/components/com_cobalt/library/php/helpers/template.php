<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined ( '_JEXEC' ) or die ();
class CTmpl
{
	static public function prepareTemplate($type, $name, &$params)
	{
		$template = $params->get($name);

		if(!$template)
		{
			JError::raiseWarning(404, JText::_('CTEMPLATENOTFOUND').': '.$name);
		}

		$template = explode('.', $template);

		$params->set($name, $template[0]);

		$dir = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'views' .DIRECTORY_SEPARATOR;

		switch ($type)
		{
			case 'default_cindex_':
			case 'default_list_':
			case 'default_filters_':
			case 'default_menu_':
			case 'default_markup_':
				$dir .= 'records' . DIRECTORY_SEPARATOR . 'tmpl' .DIRECTORY_SEPARATOR;
				break;
			case 'default_record_':
			case 'default_comments_':
				$dir .= 'record' . DIRECTORY_SEPARATOR . 'tmpl' .DIRECTORY_SEPARATOR;
				break;
			case 'default_form_':
			case 'default_category_':
				$dir .= 'form' . DIRECTORY_SEPARATOR . 'tmpl' .DIRECTORY_SEPARATOR;
				break;
		}

		$url = str_replace(array(JPATH_ROOT, DIRECTORY_SEPARATOR), array(JURI::root(TRUE), '/'), $dir);
		$doc = JFactory::getDocument();
		$css = $dir.$type.$template[0].'.css';
		if(JFile::exists($css))
		{
			$doc->addStyleSheet($url.$type. $template[0] . '.css');
		}

		$js = $dir . $type . $template[0] . '.js';
		if(JFile::exists($js))
		{
			$doc->addScript($url.$type. $template[0] . '.js');
		}

		$config = JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_cobalt'. DIRECTORY_SEPARATOR .'configs'.DIRECTORY_SEPARATOR;

		$json1 = $config . $type . $template[0] . '.' .@$template[1] .  '.json';
		$json3 = $config . $type . $template[0] . '.json';

		/*
		echo '<br>';
		echo $json1.'<br>';
		echo $json2.'<br>';
		echo $json3.'<br>';
		echo $json4.'<br>';
		*/
		if(JFile::exists($json1))
		{
			$file = JFile::read($json1);
		}
		elseif(JFile::exists($json3))
		{
			$file = JFile::read($json3);
		}
		else
		{
			JError::raiseWarning(100, 'Config not found: '.$json1);
			$file = array();
		}
		return new JRegistry($file);
	}
}


?>