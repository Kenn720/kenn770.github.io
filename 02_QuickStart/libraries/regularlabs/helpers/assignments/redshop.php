<?php
/**
 * @package         Regular Labs Library
 * @version         16.5.3297
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/assignment.php';

class RLAssignmentsRedShop extends RLAssignment
{
	function init()
	{
		$this->request->item_id     = JFactory::getApplication()->input->getInt('pid', 0);
		$this->request->category_id = JFactory::getApplication()->input->getInt('cid', 0);
		$this->request->id          = ($this->request->item_id) ? $this->request->item_id : $this->request->category_id;
	}

	function passPageTypes()
	{
		return $this->passByPageTypes('com_redshop', $this->selection, $this->assignment, true);
	}

	function passCategories()
	{
		if ($this->request->option != 'com_redshop')
		{
			return $this->pass(false);
		}

		$pass = (
			($this->params->inc_categories
				&& ($this->request->view == 'category')
			)
			|| ($this->params->inc_items && $this->request->view == 'product')
		);

		if (!$pass)
		{
			return $this->pass(false);
		}

		$cats = array();
		if ($this->request->category_id)
		{
			$cats = $this->request->category_id;
		}
		else if ($this->request->item_id)
		{
			$query = $this->db->getQuery(true)
				->select('x.category_id')
				->from('#__redshop_product_category_xref AS x')
				->where('x.product_id = ' . (int) $this->request->item_id);
			$this->db->setQuery($query);
			$cats = $this->db->loadColumn();
		}

		$cats = $this->makeArray($cats);

		$pass = $this->passSimple($cats, 'include');

		if ($pass && $this->params->inc_children == 2)
		{
			return $this->pass(false);
		}
		else if (!$pass && $this->params->inc_children)
		{
			foreach ($cats as $cat)
			{
				$cats = array_merge($cats, $this->getCatParentIds($cat));
			}
		}

		return $this->passSimple($cats);
	}

	function passProducts()
	{
		if (!$this->request->id || $this->request->option != 'com_redshop' || $this->request->view != 'product')
		{
			return $this->pass(false);
		}

		return $this->passSimple($this->request->id);
	}

	function getCatParentIds($id = 0)
	{
		return $this->getParentIds($id, 'redshop_category_xref', 'category_parent_id', 'category_child_id');
	}
}
