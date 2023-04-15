<?php
/**
 * by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * HTML Article View class for the Content component
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since		1.5
 */
class CobaltViewForm extends JViewLegacy
{
	/**
	 * @var JForm
	 */
	protected $form;

	protected $item;
	protected $return_page;
	protected $state;


	public function display($tpl = NULL)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$user = JFactory::getUser();

		MetaHelper::setMeta(array('robots' => 'NOINDEX, NOFOLLOW'));

		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$isnew = !empty($this->item->id);

		if (!$app->input->getInt('section_id', @$this->item->section_id))
		{
			JError::raiseWarning(403, JText::_('CNOSECTION'));
			return FALSE;
		}

		if (!$app->input->getInt('type_id'))
		{
			$app->redirect('index.php?option=com_cobalt&view=types&section_id=' . $app->input->getInt('section_id'));
			return FALSE;
		}

		$model_section = JModelLegacy::getInstance('Section', 'CobaltModel');
		$section = $model_section->getItem($app->input->getInt('section_id'));

		$this->type = $this->get('RecordType');
		$this->form = $this->get('Form');

		$this->params = $this->type->params;

		if($this->params->get('properties.item_title_limit', 0))
		{
			$this->form->setFieldAttribute('title', 'maxlength', $this->params->get('properties.item_title_limit', 0));
		}

		if(!in_array($this->params->get('submission.submission'), $user->getAuthorisedViewLevels()) && !MECAccess::allowNew($this->type, $section))
		{
			if(!$user->get('id'))
			{
				JError::raiseWarning(403, JText::_('CPLEASELOGIN'));
			}
			else
			{
				JError::raiseWarning(403, JText::_('CNOPERMISION'));
			}
			$modal = '';
			if($app->input->getInt('modal', FALSE))
			{
				$modal = '&tmpl=component&modal=1';
			}
			$app->redirect(JRoute::_('index.php?option=com_users&return='.Url::back().$modal));
			return FALSE;
		}

		if (empty($section->id))
		{
			JError::raiseWarning(403, JText::_('CNOSECTION'));
			return FALSE;
		}

		if (!in_array($this->type->id, $section->params->get('general.type')))
		{
			JError::raiseWarning(403, JText::_('CERRTYPENOTALLOWED'));
			return FALSE;
		}

        $parent_id = $app->input->get('parent_id', @$this->item->parent_id);
        $parent = $app->input->get('parent', (!empty($this->item->parent) ? $this->item->parent : 'com_cobalt'));
        $this->parent = ((!empty($parent_id) && $parent == 'com_cobalt') ? ItemsStore::getRecord($parent_id) : NULL);

		if($parent_id && $this->parent->published == 0)
		{
			JError::raiseWarning(403, JText::_('CNOPERMISION'));
			return Falsex;
		}

        if (empty($this->item->id))
		{
			if($section->params->get('general.record_submit_limit') > 0 &&
				($model_section->countUserRecords($section->id) >= $section->params->get('general.record_submit_limit')))
			{
				JError::raiseWarning(403, JText::sprintf('CMAXSUBMITREACHED', $section->params->get('general.record_submit_limit')));
				return FALSE;
			}
			if($this->type->params->get('submission.limits_total') > 0 &&
				($model_section->countUserRecords($section->id, $this->type->id) >= $this->type->params->get('submission.limits_total')))
			{
				JError::raiseWarning(403, JText::sprintf('CMAXSUBMITREACHED', $this->type->params->get('submission.limits_total')));
				return FALSE;
			}

			if($this->type->get('emerald.type_ulimit_subscription') &&
				EmeraldApi::hasSubscription($this->type->get('emerald.type_ulimit_subscription'),
					'', 0, $this->type->get('emerald.type_ulimit_subscription_count'), FALSE))
			{
				$this->type->params->set('submission.limits_day', $this->type->get('emerald.type_ulimit_count'));
			}


			if($this->type->params->get('submission.limits_day') > 0 &&
				($model_section->countUserRecords($section->id, $this->type->id, TRUE) >= $this->type->params->get('submission.limits_day')))
			{
				JError::raiseWarning(403, JText::sprintf('CMAXSUBMITREACHEDDAY', $this->type->params->get('submission.limits_day')));
				return FALSE;
			}

            if($this->parent)
            {
                $parent_type = ItemsStore::getType($this->parent->type_id);
				$this->parent->params = new JRegistry($this->parent->params);

				if($this->parent->params->get('comments.comments_access_post', 1) === 0)
				{
					$app->enqueueMessage(JText::_($parent_type->params->get('comments.comdisabled')), 'warning');
					$app->redirect(Url::record($this->parent, $parent_type));
					return FALSE;
				}

				if(($parent_type->params->get('comments.author_add', 1) == 0) && ($user->get('id') == $this->parent->user_id) && $this->parent->user_id)
				{
					$app->enqueueMessage(JText::_($parent_type->params->get('comments.author_add_msg')), 'warning');
					$app->redirect(Url::record($this->parent, $parent_type));
					return FALSE;
				}

				if($parent_type->params->get('comments.button_access') == -1 && $this->parent->user_id && $user->get('id') != $this->parent->user_id) {
					$app->enqueueMessage(JText::_('CNOPERMISION'));
					$app->redirect(Url::record($this->parent, $parent_type));
					return FALSE;
				}

                if($parent_type->params->get('comments.user_limit') > 0)
                {
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);

                    $query->select("COUNT(id)");
                    $query->from("#__js_res_record");
                    $query->where("section_id = '{$section->id}'");
                    $query->where("type_id = '{$this->type->id}'");
                    $query->where("user_id = '{$user->id}'");
                    $query->where("parent_id = '{$parent_id}'");
                    $query->where("parent = 'com_cobalt'");

                    $db->setQuery($query);
                    $num = $db->loadResult();

                    if($num >= $parent_type->params->get('comments.user_limit'))
                    {
                        $app->enqueueMessage(JText::_($parent_type->params->get('comments.limit_msg')), 'warning');
                        if($parent_type->params->get('comments.user_limit') == 1 && $parent_type->params->get('comments.limit_redirect') == 1)
                        {
                            $query = $db->getQuery(true);
                            $query->select("id");
                            $query->from("#__js_res_record");
                            $query->where("section_id = '{$section->id}'");
                            $query->where("type_id = '{$this->type->id}'");
                            $query->where("user_id = '{$user->id}'");
                            $query->where("parent_id = '{$parent_id}'");
                            $query->where("parent = 'com_cobalt'");

                            $db->setQuery($query);
                            $id = $db->loadResult();

                            $app->redirect(Url::edit($id, $app->input->getBase64('return')));
                        }
                        else
                        {
                            return FALSE;
                        }
                    }
                }

            }
		}

		if($default_category = $app->input->getInt('defcat_id'))
		{
			$this->params->set('submission.allow_category', 0);
			$app->input->set('cat_id', $default_category);
		}

		if($this->item->id)
		{
			CEmeraldHelper::allowType('edit', $this->type, $user->get('id', $this->item->user_id), $section, TRUE,
				'index.php?option=com_cobalt&view=form&id=' . $this->item->id, $this->item->user_id, FALSE);
		}
		else
		{
			CEmeraldHelper::allowType('submit', $this->type, $user->get('id', $this->item->user_id), $section, TRUE,
				'index.php?option=com_cobalt&view=form&section_id=' . $section->id.'&type_id=' . $this->type->id, $this->item->user_id, FALSE);
		}

		$model_fields = JModelLegacy::getInstance('Fields', 'CobaltModel');
		$fields = $model_fields->getFormFields($this->type->id, @$this->item->id);
		$this->_prepareFields($fields, $section, $this->item);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(403, implode("\n", $errors));
			return FALSE;
		}

		if (empty($this->item->id))
		{
			$authorised = $user->authorise('tmpl_core.create', 'com_cobalt') || (count($user->getAuthorisedCategories('com_cobalt', 'tmpl_core.create')));
		}
		else
		{
			$authorised = $this->item->params->get('access-edit');
		}

		if (!empty($this->item))
		{
			$this->form->bind($this->item);
		}
		$app->input->set('cat_user_id', $this->item->user_id);

		$tmpl_params = CTmpl::prepareTemplate('default_form_', 'properties.tmpl_articleform', $this->params);
		$this->tmpl_params = $tmpl_params;

		$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR . 'default_form_' . $this->params->get('properties.tmpl_articleform', 'default') . '.css';
		if (JFile::exists($file))
		{
			$doc->addStyleSheet(JURI::root(TRUE) . '/components/com_cobalt/views/form/tmpl/default_form_' . $this->params->get('properties.tmpl_articleform', 'default') . '.css');
		}
		
		$this->multirating = FALSE;
		if ($this->type->params->get('properties.rate_access', 0) == -1)
		{
			$this->multirating = RatingHelp::loadFormMultiratings($this->item, $this->type, $section, 2);
			$multirating_prop = explode("\n", str_replace("\r", "", $this->type->params->get('properties.rate_multirating_options')));
			ArrayHelper::clean_r($multirating_prop);
			$this->rate_prop = count($multirating_prop);
		}

		$core_fields_all = array("ctime", "extime", "ftime", "langs", "hidden", "votes", "votes_result", "hits", "ip", "featured", "user_id", "archive", "access", "published");

		if($this->multirating)
		{
			$core_fields_all = array_diff($core_fields_all, array("votes", "votes_result"));
		}
		
		$core_fields = $core_admin_fields = array();

		foreach($core_fields_all as $field)
		{
			$this->form->setFieldAttribute($field, 'label', $tmpl_params->get('tmpl_core.form_label_' . $field));
			if($field == 'langs' && empty($this->item->id))
			{
				$this->form->setValue($field, NULL, JFactory::getLanguage()->getTag());
			}
			if ($tmpl_params->get('tmpl_core.form_show_' . $field) == 1 || $tmpl_params->get('submission.submission') == $tmpl_params->get('tmpl_core.form_show_' . $field))
			{
				$core_fields[] = $field;
			}
			else
			{
				if (in_array($tmpl_params->get('tmpl_core.form_show_' . $field), $user->getAuthorisedViewLevels()))
				{
					$core_admin_fields[] = $field;
				}
			}
		}

		$this->core_fields = $core_fields;
		$this->core_admin_fields = $core_admin_fields;

		$this->anywhere = FALSE;
		if($section->params->get('personalize.personalize', 0)
			&& $section->params->get('personalize.post_anywhere', 0)
			&& (!$isnew && $this->item->user_id || $isnew && $user->get('id')))
		{
			$this->anywhere = TRUE;
		}
		$this->ucategory = FALSE;
		if($section->params->get('personalize.personalize', 0)
			&& in_array($section->params->get('personalize.pcat_submit', 0), $user->getAuthorisedViewLevels())
			&& (!$isnew && $this->item->user_id || $isnew && $user->get('id')))
		{
			$this->ucategory = TRUE;
		}

		$this->meta = array();

		if (in_array($tmpl_params->get('tmpl_core.form_show_meta_descr'), $user->getAuthorisedViewLevels()))
		{
			$this->meta[JText::_('CMETADESCR')] = 'meta_descr';
		}
		if (in_array($tmpl_params->get('tmpl_core.form_show_meta_key'), $user->getAuthorisedViewLevels()))
		{
			$this->meta[JText::_('CMETAKEY')] = 'meta_key';
		}
		if (in_array($tmpl_params->get('tmpl_core.form_show_meta_robots'), $user->getAuthorisedViewLevels()))
		{
			$this->meta[JText::_('CROBOTS')] = 'meta_index';
		}
		if (in_array($tmpl_params->get('tmpl_core.form_show_alias'), $user->getAuthorisedViewLevels()))
		{
			$this->meta[JText::_('CALIASES')] = 'alias';
		}

		$this->user = $user;

		$this->section = $section;

		require_once JPATH_ROOT . '/components/com_cobalt/models/category.php';
		$model_category       = new CobaltModelCategory();
		$this->category = $model_category->getEmpty();
		$id = @array_shift(@array_flip($this->item->categories));
		if($app->input->getInt('cat_id', $id))
		{
			$this->category = ItemsStore::getCategory($app->input->getInt('cat_id', $id));
		}

		if (in_array($this->params->get('submission.allow_category'), $user->getAuthorisedViewLevels()))
		{
			if (is_array($section->params))
			{
				$params = new JRegistry();
				$params->loadArray($section->params);
				$section->params = $params;
			}
			$this->section = $section;
			$catsel_params = new JRegistry();

			// echo  $tmpl_params->get('tmpl_params.tmpl_categoryselect');
			$this->catsel_params = CTmpl::prepareTemplate('default_category_', 'tmpl_params.tmpl_category', $tmpl_params);

			$data = JFactory::getApplication()->getUserState('com_cobalt.edit.form.data', array());

			if (empty($data['category']) && !empty($this->item->categories))
			{
				$data['category'] = array_keys($this->item->categories);
			}
			elseif(empty($data['category']) && !$this->item->id && !empty($this->category->id) && $this->category->params->get('submission'))
			{
				$category_enable = true;

				if($this->type->params->get('category_limit.category'))
				{
					if($this->type->params->get('category_limit.allow') && !in_array($this->category->id, $this->type->params->get('category_limit.category')))
					{
						$category_enable = false;
					}
					if(!$this->type->params->get('category_limit.allow') && in_array($this->category->id, $this->type->params->get('category_limit.category')))
					{
						$category_enable = false;
					}
				}

				if($category_enable)
				{
					$data['category'] = $this->category->id;
				}
			}

			$this->default_categories = @$data['category'];
		}

		$nacats = array();
		if($this->type->params->get('category_limit.category'))
		{
			$nacats = $this->type->params->get('category_limit.category');

			if($this->type->params->get('category_limit.category_limit_mode') == 1)
			{
				$nacats = MECAccess::_getsubcats($nacats, $this->section);
			}


			if($this->type->params->get('category_limit.allow') == 1 && $nacats)
			{
				$nacats = MECAccess::_invertcats($nacats, $this->section);
			}
		}

		if($mrcats = MECAccess::getModeratorRestrictedCategories($this->user->get('id'), $this->section))
		{
			$nacats = $mrcats;
		}

		$this->not_allow_cats = $nacats;

		$this->_prepareDocument();

		$this->allow_multi = $this->type->params->get('submission.multi_category', 0);
		if($this->type->params->get('emerald.type_multicat_subscription'))
		{
			if(!EmeraldApi::hasSubscription(
				$this->type->params->get('emerald.type_multicat_subscription'),
				$this->type->params->get('emerald.type_multicat_subscription_msg'), 0,
				$this->type->params->get('emerald.type_multicat_subscription_count'), FALSE))
			{
				$this->allow_multi = 0;
				if(trim($this->type->params->get('emerald.type_multicat_subscription_msg')))
				{
					$this->allow_multi_msg = 1;
				}
			}
		}

		parent::display($tpl);
	}

	private function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$doc = JFactory::getDocument();
		$menus	= $app->getMenu();
		$pathway = $app->getPathway();
		$pathway = $app->getPathway();
		$title = FALSE;
		if($this->item->id)
		{
			$title = JText::sprintf('CTEDIT', $this->escape($this->type->name), $this->item->title);
			$pathway->addItem($title);
		}
		else
		{
			$title = JText::sprintf('CTSUBMIT', $this->escape($this->type->name));
			$pathway->addItem($title);
		}

		$this->appParams = $app->getParams();
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$title .= ' - '.$menu->params->get('page_title', $menu->title);
			$this->appParams->def('page_heading', $title);
		}
		else
		{
			$title .= ' - '.$this->section->name;
		}
		// Check for empty title and add site name if param is set
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		if (empty($title)) {
			$title = $this->item->title;
		}
		$doc->setTitle($title);
	}

	public function isCheckedOut()
	{
		if(empty($this->item->id))
		{
			return FALSE;
		}
		if(!method_exists($this->item, 'isCheckedOut'))
		{
			return FALSE;
		}

		return $this->item->isCheckedOut($this->user->get('id'));
	}

	private function _prepareFields($fields, $section, $item)
	{
		$sorted = $fg = array();
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		foreach($fields as $key => $field)
		{
			$result = $msg = NULL;
			if (!$result = $field->getInput($this->item))
			{
				continue;
			}

			if (!$this->item->id)
			{
				if (!in_array($field->params->get('core.field_submit_access'), $user->getAuthorisedViewLevels()))
				{
					if (!trim($field->params->get('core.field_submit_message')))
					{
						continue;
					}
					else
					{
						$msg = JText::_($field->params->get('core.field_submit_message'));
					}
				}
			}
			else
			{
				if (!in_array($field->params->get('core.field_edit_access'), $user->getAuthorisedViewLevels()))
				{
					if (!trim($field->params->get('core.field_edit_message')))
					{
						continue;
					}
					else
					{
						$msg = JText::_($field->params->get('core.field_edit_message'));
					}
				}
			}

			$method = $this->item->id ? 'edit' : 'submit';
			if (!CEmeraldHelper::allowField($method, $field, $item->user_id, $section, $item, TRUE, FALSE))
			{
				if ($field->params->get('emerald.field_'.$method.'_subscription') && trim($field->params->get('emerald.field_'.$method.'_subscription_msg')))
				{
					//$plans = CEmeraldHelper::getSubscrList($field->params->get('emerald.field_'.$method.'_subscription'), $field->params->get('emerald.subscr_idd', $app->input->getInt('Itemid')));
					$msg = JText::_($field->params->get('emerald.field_'.$method.'_subscription_msg'));
					$msg .= sprintf('<br><small><a href="%s">%s</a></small>',
						EmeraldApi::getLink('list', true, $field->params->get('emerald.field_'.$method.'_subscription')),
						JText::_('CSUBSCRIBENOW')
					);
				}
				else continue;
			}

			if (!$msg)
			{
				$field->result = $result;
				$field->js = $field->onJSValidate();
			}
			else
			{
				$field->result = '<div class="alert">'.$msg.'</div>';
				$field->js = '';
			}

			$sorted[(int)$field->group_id][$key] = $field;
			$fg[$field->group_id]['name'] = $field->group_title;
			$fg[$field->group_id]['descr'] = $field->group_descr;
			$fg[$field->group_id]['icon'] = $field->group_icon;
		}

		$this->fields = $fields;
		$this->sorted_fields = $sorted;
		$this->field_groups = $fg;
	}
}
