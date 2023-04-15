<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
require_once JPATH_ROOT . '/components/com_cobalt/library/php/helpers/helper.php';

class plgContentGlossary extends JPlugin
{

	function onContentPrepare($context, &$article, &$params, $page = 0)
	//function onPrepareContent (&$article, &$params, $limitstart)
	{
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		if($app->isAdmin())
		{
			return;
		}

		JHTML::_('behavior.tooltip');

		$field = $this->params->get('field');
		if(! $field)
		{
			JError::raiseWarning(230, 'Glossary plugin - parameters not set. Edit plugin or unpublish it.');
			return;
		}

		$where = array();
		$sections = $this->params->get('categories');
		ArrayHelper::clean_r($sections);
		JArrayHelper::toInteger($sections);
		$sections[] = 0;

		$query = $db->getQuery(true);

		$query->select('r.*');
		$query->select("(SELECT field_value FROM #__js_res_record_values WHERE field_key = '{$field}' AND record_id = r.id) as field_value");
		$query->from('#__js_res_record AS r');
		$query->where('r.published = 1');
		$query->where('r.section_id IN(' . implode(',', $sections) . ')');
		if($this->params->get('types'))
		{
			$query->where('r.type_id IN(' . implode(',', (array)$this->params->get('types')) . ')');
		}

		static $result = array();
		$key = md5((string)$query);

		if(! isset($result[$key]))
		{
			$db->setQuery($query);
			$result[$key] = $db->loadObjectList();

			$css = '.glossary{' . $this->params->get('css', "cursor:pointer; background-color:yellow; text-decoration:underline;") . '}';
			$document->addStyleDeclaration($css);

			settype($result[$key], 'array');
		}

		static $num = array();
		foreach($result[$key] as $vall)
		{
			if($this->params->get('link'))
			{
				$link = Url::record($vall);
				$title = htmlspecialchars($vall->title, ENT_COMPAT, 'UTF-8');
				$nums = '';
				if($this->params->get('link') == 2)
				{
					if(!isset($num[$vall->title]))
					{
						$query = $db->getQuery(true);
						$query->select('count(id)');
						$query->from('#__js_res_hits');
						$query->where("record_id = '{$vall->id}'");
						$db->setQuery($query);
						$num[$vall->title] = $db->loadResult();
					}
					$lang = JFactory::getLanguage();
					$lang->load('com_cobalt', JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_cobalt');
					$nums = " (".JText::_('CHITS').': '.$num[$vall->title].")";
				}
				$string = sprintf('\\1<a href="%s"><span data-placement="top" rel="popover" data-original-title="%s" data-content="%s" class="glossary">%s</span></a>\\3', $link, $title.$nums, htmlspecialchars($vall->field_value, ENT_COMPAT, 'UTF-8'), $title);
			}
			else
			{
				$string = sprintf('\\1<span data-placement="top" rel="popover" data-original-title="%s" data-content="%s" class="glossary">\\2</span>\\3', htmlspecialchars($vall->title, ENT_COMPAT, 'UTF-8'), htmlspecialchars($vall->field_value, ENT_COMPAT, 'UTF-8'));
			}

			$article->text = preg_replace("/([^\w]?)(" . preg_quote($vall->title) . ")([^\w]?)/isU", $string, $article->text);
		}
	}
}
?>