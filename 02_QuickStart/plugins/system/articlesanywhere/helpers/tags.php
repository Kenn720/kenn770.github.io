<?php
/**
 * @package         Articles Anywhere
 * @version         4.3.1
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemArticlesAnywhereHelperTags
{
	var $helpers = array();
	var $params = null;
	var $config = null;
	var $article = null;
	var $images = null;
	var $data = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemArticlesAnywhereHelpers::getInstance();
		$this->params  = $this->helpers->getParams();

		$this->config = JComponentHelper::getParams('com_content');

		$this->data = (object) array(
			'article' => null,
			'total'   => 1,
			'count'   => 1,
			'even'    => 0,
			'uneven'  => 1,
			'first'   => 1,
			'last'    => 1,
		);
	}

	public function handleIfStatements(&$string, &$article)
	{
		list($tag_start, $tag_end) = $this->helpers->get('process')->getTagCharacters(true);

		preg_match_all(
			'#' . $tag_start . 'if:.*?' . $tag_start . '/if' . $tag_end . '#si',
			$string,
			$matches,
			PREG_SET_ORDER
		);

		if (empty($matches))
		{
			return;
		}

		$this->data->article = $article;

		if (strpos($string, 'text') !== false)
		{
			$article->text = (isset($article->introtext) ? $article->introtext : '')
				. (isset($article->introtext) ? $article->fulltext : '');
		}

		foreach ($matches as $match)
		{
			preg_match_all(
				'#' . $tag_start . '(if|else *?if|else)(?:\:(.+?))?' . $tag_end . '(.*?)(?=' . $tag_start . '(?:else|\/if))#si',
				$match['0'],
				$ifs,
				PREG_SET_ORDER
			);

			if (empty($ifs))
			{
				continue;
			}

			$replace = $this->getIfResult($ifs);

			// replace if block with the IF value
			$string = NNText::strReplaceOnce($match['0'], $replace, $string);
		}

		$article = $this->data->article;
	}

	private function getIfResult(&$matches)
	{
		foreach ($matches as $if)
		{
			if (!$this->passIfStatements($if))
			{
				continue;
			}

			return $if['3'];
		}

		return '';
	}

	private function passIfStatements($if)
	{
		$statement = trim($if['2']);

		if (trim($if['1']) == 'else' && $statement == '')
		{
			return true;
		}

		if ($statement == '')
		{
			return false;
		}

		$statement = html_entity_decode($statement);
		$statement = str_replace(
			array(' AND ', ' OR '),
			array(' && ', ' || '),
			$statement
		);

		$ands = explode(' && ', $statement);

		$pass = false;
		foreach ($ands as $statement)
		{
			$ors = explode(' || ', $statement);
			foreach ($ors as $statement)
			{
				if ($pass = $this->passIfStatement($statement))
				{
					break;
				}
			}

			if (!$pass)
			{
				break;
			}
		}

		return $pass;
	}

	private function passIfStatement($statement)
	{
		$statement = trim($statement);

		/*
		* In array syntax
		* 'bar' IN foo
		* 'bar' !IN foo
		* 'bar' NOT IN foo
		*/
		if (preg_match('#^[\'"]?(?P<val>.*?)[\'"]?\s+(?P<operator>(?:NOT\s+)?\!?IN)\s+(?P<key>[a-zA-Z0-9-_]+)$#s', $statement, $match))
		{
			$reverse = ($match['operator'] == 'NOT IN' || $match['operator'] == '!NOT');

			return $this->passIfStatementArray(
				$this->getValueFromData($match['key']),
				$this->getValueFromData($match['val'], $match['val']),
				$reverse
			);
		}

		/*
		* String comparison syntax:
		* foo = 'bar'
		* foo != 'bar'
		*/
		if (preg_match('#^(?P<key>[a-z0-9-_]+)\s*(?P<operator>\!?=)=*\s*[\'"]?(?P<val>.*?)[\'"]?$#si', $statement, $match))
		{
			$reverse = ($match['2'] == '!=');

			return $this->passIfStatementArray(
				$this->getValueFromData($match['key']),
				$this->getValueFromData($match['val'], $match['val']),
				$reverse
			);
		}

		/*
		* Variable check syntax:
		* foo (= not empty)
		* !foo (= empty)
		*/
		if (preg_match('#^(?P<operator>\!?)(?P<key>[a-z0-9-_]+)$#si', $statement, $match))
		{
			$reverse = ($match['operator'] == '!');

			return $this->passIfStatementSimple(
				$this->getValueFromData($match['key']),
				$reverse
			);
		}

		return $this->passIfStatementPHP($statement);
	}

	private function getValueFromData($key, $default = null)
	{
		if (!is_string($key))
		{
			return $default;
		}

		return isset($this->data->{$key}) ? $this->data->{$key} : (isset($this->data->article->{$key}) ? $this->data->article->{$key} : $default);
	}

	private function passIfStatementSimple($haystack, $reverse = 0)
	{
		if (is_null($haystack))
		{
			return false;
		}

		$pass = !empty($haystack);

		return $reverse ? !$pass : $pass;
	}

	private function passIfStatementString($haystack, $needle, $reverse = 0)
	{
		if (is_null($haystack))
		{
			return false;
		}

		if (is_array($haystack))
		{
			return $this->passIfStatementArray($haystack, $needle, $reverse);
		}

		$pass = $this->passString($haystack, $needle);

		return $reverse ? !$pass : $pass;
	}

	private function passIfStatementArray($haystack, $needle, $reverse = 0)
	{
		if (is_null($haystack))
		{
			return false;
		}

		if (!is_array($haystack))
		{
			$haystack = explode(',', str_replace(', ', ',', $haystack));
		}

		if (!is_array($haystack))
		{
			return false;
		}

		$pass = false;
		foreach ($haystack as $string)
		{
			if ($pass = $this->passString($string, $needle))
			{
				break;
			}
		}

		return $reverse ? !$pass : $pass;
	}

	private function passIfStatementPHP($statement)
	{
		$php = html_entity_decode($statement);
		$php = str_replace('=', '==', $php);

		// replace keys with $article->key
		$php = '$article->' . preg_replace('#\s*(&&|&&|\|\|)\s*#', ' \1 $article->', $php);

		// fix negative keys from $article->!key to !$article->key
		$php = str_replace('$article->!', '!$article->', $php);

		// replace back data variables
		foreach ($this->data as $key => $val)
		{
			if ($key == 'article')
			{
				continue;
			}

			$php = str_replace('$article->' . $key, (int) $val, $php);
		}
		$php = str_replace('$article->empty', (int) ($this->data->count > 0), $php);

		// Place statement in return check
		$php = 'return ( ' . $php . ' ) ? 1 : 0;';

		// Trim the text that needs to be checked and replace weird spaces
		$php = preg_replace(
			'#(\$article->[a-z0-9-_]*)#',
			'trim(str_replace(chr(194) . chr(160), " ", \1))',
			$php
		);

		// Fix extra-1 field syntax: $article->extra-1 to $article->{'extra-1'}
		$php = preg_replace(
			'#->(extra-[a-z0-9]+)#',
			'->{\'\1\'}',
			$php
		);

		$temp_PHP_func = create_function('&$article', $php);

		// evaluate the script
		// but without using the the evil eval
		ob_start();
		$pass = $temp_PHP_func($this->data->article);
		unset($temp_PHP_func);
		ob_end_clean();

		return $pass;
	}

	private function passString($haystack, $needle)
	{
		if (!is_string($haystack) && !is_string($needle)
			&& !is_numeric($haystack)
			&& !is_numeric($needle)
		)
		{
			return false;
		}

		// Simple string comparison
		if (strpos($needle, '*') === false && strpos($needle, '+') === false)
		{
			return strtolower($haystack) == strtolower($needle);
		}

		// Using wildcards
		$needle = preg_quote($needle, '#');
		$needle = str_replace(
			array('\\\\\\*', '\\*', '[:asterisk:]', '\\\\\\+', '\\+', '[:plus:]'),
			array('[:asterisk:]', '.*', '\\*', '[:plus:]', '.+', '\\+'),
			$needle
		);

		return preg_match('#' . $needle . '#si', $haystack);
	}

	public function replaceTags(&$text, &$matches, &$article)
	{
		$this->article = $article;
		foreach ($matches as $match)
		{
			$string = $this->processTag($match['1']);
			if ($string === false)
			{
				continue;
			}

			$text = str_replace($match['0'], $string, $text);
		}
	}

	public function processTag($data)
	{
		$data = explode(':', $data, 2);

		$tag   = trim($data['0']);
		$extra = isset($data['1']) ? $data['1'] : '';

		return $this->processTagByType($tag, $extra);
	}

	public function processTagByType($tag, $extra)
	{
		switch (true)
		{
			// Link closing tag
			case ($tag == '/link'):
				return '</a>';

			// Total count
			case ($tag == 'total' || $tag == 'totalcount'):
				return $this->data->total;

			// Counter
			case ($tag == 'count' || $tag == 'counter'):
				return $this->data->count;

			// Div closing tag
			case ($tag == '/div'):
				return '</div>';

			// Div
			case ($tag == 'div' || strpos($tag, 'div ') === 0):
				return $this->processTagDiv($tag, $extra);

			// URL
			case ($tag == 'url'):
				return $this->getArticleUrl();

			// Link tag
			case ($tag == 'link'):
				return $this->processTagLink();

			// Readmore link
			case (strpos($tag, 'readmore') === 0):
				return $this->processTagReadmore($extra);

			// Title
			case ($tag == 'title' || strpos($tag, 'title:') === 0):
				return $this->processTagTitle($extra);

			// Text
			case (
				(strpos($tag, 'text') === 0)
				|| (strpos($tag, 'intro') === 0)
				|| (strpos($tag, 'full') === 0)
			):
				return $this->processTagText($tag, $extra);

			// Intro image
			case ($tag == 'image-intro'):
				return $this->processTagImageIntro();

			// Fulltext image
			case ($tag == 'image-fulltext'):
				return $this->processTagImageFulltext();


			// Database values
			case (NNText::is_alphanumeric(str_replace(array('-', '_'), '', $tag))):
				return $this->processTagDatabase($tag, $extra);

			default:
				return false;
		}
	}

	public function processTagDiv($tag, $extra)
	{
		if ($tag != 'div')
		{
			$extra = str_replace('div ', '', $tag)
				. ':'
				. $extra;
		}

		if (empty($extra))
		{
			return '<div>';
		}

		$string = '';

		$extra  = explode('|', $extra);
		$extras = new stdClass;
		foreach ($extra as $e)
		{
			if (strpos($e, ':') !== false)
			{
				list($key, $val) = explode(':', $e, 2);
				$extras->{$key} = $val;
			}
		}

		if (isset($extras->class))
		{
			$string .= 'class="' . $extras->class . '"';
		}

		$style = array();

		if (isset($extras->width))
		{
			if (is_numeric($extras->width))
			{
				$extras->width .= 'px';
			}
			$style[] = 'width:' . $extras->width;
		}

		if (isset($extras->height))
		{
			if (is_numeric($extras->height))
			{
				$extras->height .= 'px';
			}
			$style[] = 'height:' . $extras->height;
		}

		if (isset($extras->align))
		{
			$style[] = 'float:' . $extras->align;
		}
		else if (isset($extras->float))
		{
			$style[] = 'float:' . $extras->float;
		}

		if (!empty($style))
		{
			$string .= ' style="' . implode(';', $style) . ';"';
		}

		return trim('<div ' . trim($string)) . '>';
	}

	public function processTagReadmore($extra)
	{
		if (!$link = $this->getArticleUrl())
		{
			return false;
		}

		// load the content language file
		NNFrameworkFunctions::loadLanguage('com_content', JPATH_SITE);

		$extra = explode('|', $extra);

		if (isset($extra['1']))
		{
			return '<a class="' . trim($extra['1']) . '" href="' . $link . '">' . $this->getReadMoreText($extra) . '</a>';
		}

		$params = JComponentHelper::getParams('com_content');
		$params->set('access-view', true);

		if ($text = $this->getCustomReadMoreText($extra))
		{
			$this->article->alternative_readmore = $text;
			$params->set('show_readmore_title', false);
		}

		return JLayoutHelper::render('joomla.content.readmore', array('item' => $this->article, 'params' => $params, 'link' => $link));
	}

	private function getCustomReadMoreText($extra)
	{
		if (!isset($extra['0']) || !trim($extra['0']))
		{
			return '';
		}

		$title = trim($extra['0']);
		$text  = JText::sprintf($title, $this->article->title);

		return $text ?: $title;
	}

	public function getReadMoreText($extra)
	{
		if ($text = $this->getCustomReadMoreText($extra))
		{
			return $text;
		}

		switch (true)
		{
			case (isset($this->article->alternative_readmore) && $this->article->alternative_readmore) :
				$text = $this->article->alternative_readmore;
				break;
			case (!$this->config->get('show_readmore_title', 0)) :
				$text = JText::_('COM_CONTENT_READ_MORE_TITLE');
				break;
			default:
				$text = JText::_('COM_CONTENT_READ_MORE');
				break;
		}

		if (!$this->config->get('show_readmore_title', 0))
		{
			return $text;
		}

		return $text . JHtml::_('string.truncate', ($this->article->title), $this->config->get('readmore_limit'));
	}

	public function processTagLink()
	{
		if (!$link = $this->getArticleUrl())
		{
			return false;
		}

		return '<a href="' . $link . '">';
	}

	public function processTagTitle($extra)
	{
		$title = isset($this->article->title) ? $this->article->title : '';

		if (empty($title) || empty($extra))
		{
			return $title;
		}

		return $this->helpers->get('text')->process($title, $extra);
	}

	public function processTagText($tag, $extra)
	{
		switch (true)
		{
			case (strpos($tag, 'intro') === 0):
				if (!isset($this->article->introtext))
				{
					return false;
				}
				$this->article->text = $this->article->introtext;
				break;

			case (strpos($tag, 'full') === 0):
				if (!isset($this->article->fulltext))
				{
					return false;
				}

				$this->article->text = $this->article->fulltext;
				break;

			case (strpos($tag, 'text') === 0):
				$this->article->text = (isset($this->article->introtext) ? $this->article->introtext : '')
					. (isset($this->article->introtext) ? $this->article->fulltext : '');
				break;
		}

		if ($this->article->text == '')
		{
			return '';
		}

		$string = $this->article->text;

		if (empty($extra))
		{
			return $string;
		}

		return $this->helpers->get('text')->process($string, $extra);
	}

	public function processTagImageIntro()
	{
		if (!isset($this->article->image_intro))
		{
			return '';
		}

		$class = 'img-intro-' . $this->article->float_intro;

		return $this->getImageHtml($this->article->image_intro, $this->article->image_intro_alt, $this->article->image_intro_caption, $class);
	}

	public function processTagImageFulltext()
	{
		if (!isset($this->article->image_fulltext))
		{
			return '';
		}

		$class = 'img-fulltext-' . $this->article->float_fulltext;

		return $this->getImageHtml($this->article->image_fulltext, $this->article->image_fulltext_alt, $this->article->image_fulltext_caption, $class);
	}

	public function getImageHtml($url, $alt = '', $caption = '', $class = '', $in_div = true)
	{
		$img_class = $caption ? 'caption' : '';
		$caption   = $caption ? ' title="' . htmlspecialchars($caption) . '"' : '';

		if ($in_div)
		{
			return '<div class="' . htmlspecialchars($class) . '"><img' . $caption . ' src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '" class="' . $img_class . '"></div>';
		}

		$img_class = trim($img_class . ' ' . htmlspecialchars($class));

		return '<img' . $caption . ' src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '" class="' . $img_class . '">';
	}


	public function processTagDatabase($tag, $extra, $return_empty = false)
	{
		// Get data from data object, even, uneven, first, last
		if (isset($this->data->{$tag}) && is_bool($this->data->{$tag}))
		{
			return $this->data->{$tag} ? 'true' : 'false';
		}

		// Get data from db columns
		if (!isset($this->article->{$tag}) || is_array($this->article->{$tag}) || is_object($this->article->{$tag}))
		{
			return $return_empty ? '' : false;
		}

		$string = $this->article->{$tag};

		// Convert string if it is a date
		$string = $this->convertDateToString($string, $extra);

		return $string;
	}

	public function convertDateToString($string, $extra)
	{
		// Check if string could be a date
		if ((strpos($string, '-') == false)
			|| preg_match('#[a-z]#i', $string)
			|| !strtotime($string)
		)
		{
			return $string;
		}

		if (empty($extra))
		{
			$extra = JText::_('DATE_FORMAT_LC2');
		}

		if (strpos($extra, '%') !== false)
		{
			$extra = NNText::dateToDateFormat($extra);
		}

		return JHtml::_('date', $string, $extra);
	}

	public function canEdit()
	{
		$user = JFactory::getUser();
		if ($user->get('guest'))
		{
			return false;
		}

		$userId = $user->get('id');
		$asset  = 'com_content.article.' . $this->article->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			return true;
		}

		// Now check if edit.own is available.
		if (empty($userId) || $user->authorise('core.edit.own', $asset))
		{
			return false;
		}

		// Check for a valid user and that they are the owner.
		if ($userId != $this->article->created_by)
		{
			return false;
		}

		return true;
	}

	public function getArticleUrl()
	{
		if (isset($this->article->url))
		{
			return $this->article->url;
		}

		if (!isset($this->article->id))
		{
			return false;
		}

		require_once JPATH_SITE . '/components/com_content/helpers/route.php';
		$this->article->url = ContentHelperRoute::getArticleRoute($this->article->id, $this->article->catid, $this->article->language);

		if (empty($this->article->has_access))
		{
			$this->article->url = $this->getRestrictedUrl($this->article->url);
		}

		return $this->article->url;
	}

	public function getRestrictedUrl($url)
	{
		$menu   = JFactory::getApplication()->getMenu();
		$active = $menu->getActive();
		$itemId = $active->id;
		$link   = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));

		$link->setVar('return', base64_encode(JRoute::_($url, false)));

		return (string) $link;
	}

	public function getArticleEditUrl()
	{
		if (isset($this->article->editurl))
		{
			return $this->article->editurl;
		}

		if (!isset($this->article->id))
		{
			return false;
		}

		$this->article->editurl = '';

		if (!$this->canEdit())
		{
			return '';
		}

		$uri                    = JUri::getInstance();
		$this->article->editurl = JRoute::_('index.php?option=com_content&task=article.edit&a_id=' . $this->article->id . '&return=' . base64_encode($uri));

		return $this->article->editurl;
	}

	public function getArticleImages()
	{
		if (!is_array($this->images))
		{
			$article_text = (isset($this->article->introtext) ? $this->article->introtext : '')
				. (isset($this->article->fulltext) ? $this->article->fulltext : '');

			preg_match_all(
				'#<img\s[^>]*src=([\'"])(.*?)\1[^>]*>#si',
				$article_text,
				$this->images,
				PREG_SET_ORDER
			);
		}

		return $this->images;
	}


}
