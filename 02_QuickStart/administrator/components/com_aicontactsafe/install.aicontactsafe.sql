
CREATE TABLE IF NOT EXISTS `#__aicontactsafe_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `config_key` varchar(50) NOT NULL DEFAULT '',
  `config_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Дамп даних таблиці `#__aicontactsafe_config`
--

INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES
(1, 'use_css_backend', '1'),
(2, 'use_SqueezeBox', '0'),
(3, 'highlight_errors', '1'),
(4, 'keep_session_alive', '0'),
(5, 'activate_help', '1'),
(6, 'date_format', 'l, d F Y H:i'),
(7, 'default_status_filter', '0'),
(8, 'editbox_cols', '40'),
(9, 'editbox_rows', '10'),
(10, 'default_name', ''),
(11, 'default_email', ''),
(12, 'default_subject', ''),
(13, 'activate_spam_control', '0'),
(14, 'block_words', 'url='),
(15, 'record_blocked_messages', '1'),
(16, 'activate_ip_ban', '0'),
(17, 'ban_ips', ''),
(18, 'redirect_ips', ''),
(19, 'ban_ips_blocked_words', '0'),
(20, 'maximum_messages_ban_ip', '0'),
(21, 'maximum_minutes_ban_ip', '0'),
(22, 'email_ban_ip', ''),
(23, 'set_sender_joomla', '0'),
(24, 'upload_attachments', 'media/aicontactsafe/attachments'),
(25, 'maximum_size', '5000000'),
(26, 'attachments_types', 'rar,zip,doc,xls,txt,gif,jpg,png,bmp'),
(27, 'attach_to_email', '1'),
(28, 'delete_after_sent', '0'),
(29, 'gid_messages', '8'),
(30, 'users_all_messages', '0');

-- --------------------------------------------------------

--
-- Структура таблиці `#__aicontactsafe_contactinformations`
--

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_contactinformations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) unsigned NOT NULL,
  `info_key` varchar(50) NOT NULL DEFAULT '',
  `info_label` varchar(250) NOT NULL DEFAULT '',
  `info_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Дамп даних таблиці `#__aicontactsafe_contactinformations`
--

INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES
(1, 1, 'contact_info', 'contact_info (Заказать обратный звонок)', ''),
(2, 2, 'contact_info', 'contact_info (Контакты)', ''),
(13, 3, 'meta_description', 'meta_description (Онлайн консультация)', ''),
(14, 3, 'meta_keywords', 'meta_keywords (Онлайн консультация)', ''),
(15, 3, 'meta_robots', 'meta_robots (Онлайн консультация)', ''),
(16, 3, 'thank_you_message', 'thank_you_message (Онлайн консультация)', 'Спасибо! Ваше сообщение отправлено. На Вашу почту придёт письмо с датой и временем консультации, а также инструкция по оплате.'),
(3, 1, 'meta_description', 'meta_description (Заказать обратный звонок)', ''),
(4, 2, 'meta_description', 'meta_description (Контакты)', ''),
(5, 1, 'meta_keywords', 'meta_keywords (Заказать обратный звонок)', ''),
(6, 2, 'meta_keywords', 'meta_keywords (Контакты)', ''),
(7, 1, 'meta_robots', 'meta_robots (Заказать обратный звонок)', ''),
(8, 2, 'meta_robots', 'meta_robots (Контакты)', ''),
(9, 1, 'thank_you_message', 'thank_you_message (Заказать обратный звонок)', 'Спасибо! Ваше сообщение отправлено. Я свяжусь с Вами в ближайшее время.'),
(10, 2, 'thank_you_message', 'thank_you_message (Контакты)', 'Спасибо! Ваше сообщение отправлено. Я свяжусь с Вами в ближайшее время.'),
(11, 1, 'required_field_notification', 'required_field_notification (Заказать обратный звонок)', 'Обязательные поля помечены %mark%'),
(12, 2, 'required_field_notification', 'required_field_notification (Контакты)', 'Обязательные поля помечены %mark%'),
(17, 3, 'required_field_notification', 'required_field_notification (Онлайн консультация)', 'Обязательные поля помечены %mark%'),
(18, 3, 'contact_info', 'contact_info (Copy of Контакты)', '');

-- --------------------------------------------------------

--
-- Структура таблиці `#__aicontactsafe_fields`
--

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `field_label` text NOT NULL,
  `label_parameters` text NOT NULL,
  `field_label_message` text NOT NULL,
  `label_message_parameters` text NOT NULL,
  `label_after_field` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `field_type` varchar(2) NOT NULL DEFAULT 'TX',
  `field_parameters` text NOT NULL,
  `field_values` text NOT NULL,
  `field_limit` int(11) NOT NULL DEFAULT '0',
  `default_value` varchar(150) NOT NULL DEFAULT '',
  `auto_fill` varchar(10) NOT NULL DEFAULT '',
  `field_sufix` text NOT NULL,
  `field_prefix` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `field_required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `field_in_message` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `send_message` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `checked_out` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп даних таблиці `#__aicontactsafe_fields`
--

INSERT INTO `#__aicontactsafe_fields` (`id`, `name`, `field_label`, `label_parameters`, `field_label_message`, `label_message_parameters`, `label_after_field`, `field_type`, `field_parameters`, `field_values`, `field_limit`, `default_value`, `auto_fill`, `field_sufix`, `field_prefix`, `ordering`, `field_required`, `field_in_message`, `send_message`, `date_added`, `last_update`, `published`, `checked_out`, `checked_out_time`) VALUES
(1, 'aics_name', 'Имя', '', 'Имя', '', 0, 'TX', 'class=&#039;textbox&#039;', '', 0, '', '', '', '', 1, 1, 1, 0, '2013-11-27 16:00:52', '2014-01-18 21:17:29', 1, 0, '0000-00-00'),
(2, 'aics_email', 'E-mail', '', 'E-mail', '', 0, 'EM', 'class=&#039;email&#039;', '', 0, '', '', '', '', 2, 1, 1, 0, '2013-11-27 16:00:52', '2014-01-18 21:17:37', 1, 0, '0000-00-00'),
(3, 'aics_phone', 'Телефон', '', 'Телефон', '', 0, 'TX', 'class=&#039;textbox&#039;', '', 25, '', '', '', '', 3, 0, 1, 0, '2013-11-27 16:00:52', '2014-01-18 21:04:33', 1, 0, '0000-00-00'),
(5, 'aics_message', 'Сообщение', '', 'Сообщение', '', 0, 'ED', 'class=&#039;editbox&#039;', '', 0, '', '', '', '', 5, 1, 1, 0, '2013-11-27 16:00:52', '2013-12-22 23:01:47', 1, 0, '0000-00-00'),
(7, 'aics_datatime', 'Дата и время', '', 'Дата и время', '', 0, 'ED', 'class=&#039;editbox&#039;', '', 0, '', '', '', '', 5, 1, 1, 0, '2013-12-22 21:45:55', '2013-12-22 23:02:15', 1, 0, '0000-00-00'),
(8, 'aics_skype', 'Skype логин', '', 'Skype логин', '', 0, 'TX', 'class=&#039;textbox&#039;', '', 0, '', 'UN', '', '', 1, 1, 1, 0, '2013-12-22 21:46:13', '2013-12-22 21:47:00', 1, 0, '0000-00-00'),
(9, 'aics_phone_required', 'Телефон', '', 'Телефон', '', 0, 'TX', 'class=&#039;textbox&#039;', '', 25, '', '', '', '', 3, 1, 1, 0, '2014-01-18 21:03:45', '2014-01-18 21:04:24', 1, 0, '0000-00-00');

-- --------------------------------------------------------

--
-- Структура таблиці `#__aicontactsafe_fieldvalues`
--

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_fieldvalues` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) unsigned NOT NULL,
  `message_id` int(11) unsigned NOT NULL,
  `field_value` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `checked_out` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблиці `#__aicontactsafe_messagefiles`
--

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_messagefiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` int(11) unsigned NOT NULL,
  `name` text NOT NULL,
  `r_id` int(21) unsigned NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `checked_out` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблиці `#__aicontactsafe_messages`
--

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `subject` varchar(200) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `send_to_sender` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sender_ip` varchar(20) NOT NULL DEFAULT '',
  `profile_id` int(11) unsigned NOT NULL,
  `status_id` int(11) unsigned NOT NULL,
  `manual_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `email_destination` text NOT NULL,
  `email_reply` varchar(100) NOT NULL DEFAULT '',
  `subject_reply` text NOT NULL,
  `message_reply` text NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `checked_out` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп даних таблиці `#__aicontactsafe_messages`
--

INSERT INTO `#__aicontactsafe_messages` (`id`, `name`, `email`, `subject`, `message`, `send_to_sender`, `sender_ip`, `profile_id`, `status_id`, `manual_status`, `email_destination`, `email_reply`, `subject_reply`, `message_reply`, `user_id`, `date_added`, `last_update`, `published`, `checked_out`, `checked_out_time`) VALUES
(1, 'Название сайта', 'admin@cyberintels.com', 'Заказ обратного звонка ', '<table border="0" cellpadding="0" cellspacing="2"><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td><span   >Имя</span></td><td>&nbsp;</td><td>Александр Хмельницкий</td></tr><tr><td><span   >Телефон</span></td><td>&nbsp;</td><td></td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table>', 0, '217.77.215.59', 1, 1, 0, 'admin@cyberintels.com', '', '', '', 390, '2014-01-18 21:03:28', '2014-01-18 21:03:28', 1, 0, '0000-00-00'),
(2, 'Название сайта', 'admin@cyberintels.com', 'Заказ обратного звонка ', '<table border="0" cellpadding="0" cellspacing="2"><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td><span   >Имя</span></td><td>&nbsp;</td><td>Александр Хмельницкий</td></tr><tr><td><span   >Телефон</span></td><td>&nbsp;</td><td>dsfffff</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table>', 0, '217.77.215.59', 1, 1, 0, 'admin@cyberintels.com', '', '', '', 390, '2014-01-18 21:09:16', '2014-01-18 21:09:16', 1, 0, '0000-00-00'),
(3, 'Психотерапевт — психолог Мосеев Евгений Павлович', 'admin@cyberintels.com', 'Заказ обратного звонка ', '<table border="0" cellpadding="0" cellspacing="2"><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td><span   >Имя</span></td><td>&nbsp;</td><td>Александр</td></tr><tr><td><span   >Телефон</span></td><td>&nbsp;</td><td>+380971002825</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table>', 0, '217.77.215.59', 1, 1, 0, 'admin@cyberintels.com', '', '', '', 390, '2014-01-18 21:27:38', '2014-01-18 21:27:38', 1, 0, '0000-00-00'),
(4, 'Психотерапевт — психолог Мосеев Евгений Павлович', 'admin@cyberintels.com', 'Психотерапевт — психолог Мосеев Евгений Павлович ', '<table border="0" cellpadding="0" cellspacing="2"><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td><span   >Имя</span></td><td>&nbsp;</td><td>Александр</td></tr><tr><td><span   >E-mail</span></td><td>&nbsp;</td><td>admin@cyberintels.com</td></tr><tr><td><span   >Телефон</span></td><td>&nbsp;</td><td></td></tr><tr><td><span   >Сообщение</span></td><td>&nbsp;</td><td>edrf wer werf werf werfw erfwe  rwref wherjfw ebrkjfhwrkgjwer we we rjwer werg wekrgw erw erwer gwkeurhwerh twertwert werrtw ertwer wer wer twer twertwerwerr</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table>', 0, '217.77.215.59', 2, 1, 0, 'admin@cyberintels.com', '', '', '', 390, '2014-01-18 21:28:43', '2014-01-18 21:28:43', 1, 0, '0000-00-00');

-- --------------------------------------------------------

--
-- Структура таблиці `#__aicontactsafe_profiles`
--

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_profiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `use_ajax` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `use_message_css` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `contact_form_width` int(11) NOT NULL DEFAULT '0',
  `bottom_row_space` int(11) NOT NULL DEFAULT '0',
  `align_buttons` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `contact_info_width` int(11) NOT NULL DEFAULT '0',
  `use_captcha` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `captcha_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `align_captcha` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `email_address` varchar(100) NOT NULL DEFAULT '',
  `always_send_to_email_address` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `subject_prefix` varchar(100) NOT NULL DEFAULT '',
  `email_mode` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `record_message` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `record_fields` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `custom_date_format` varchar(30) NOT NULL DEFAULT '%d %B %Y',
  `custom_date_years_back` int(11) NOT NULL DEFAULT '70',
  `custom_date_years_forward` int(11) NOT NULL DEFAULT '0',
  `required_field_mark` text NOT NULL,
  `display_format` int(11) NOT NULL DEFAULT '2',
  `plg_contact_info` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `use_random_letters` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `min_word_length` tinyint(2) unsigned NOT NULL DEFAULT '5',
  `max_word_length` tinyint(2) unsigned NOT NULL DEFAULT '8',
  `set_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `active_fields` text NOT NULL,
  `captcha_width` smallint(4) NOT NULL DEFAULT '400',
  `captcha_height` smallint(4) NOT NULL DEFAULT '55',
  `captcha_bgcolor` varchar(10) NOT NULL DEFAULT '#FFFFFF',
  `captcha_backgroundTransparent` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `captcha_colors` text NOT NULL,
  `name_field_id` int(11) unsigned NOT NULL,
  `email_field_id` int(11) unsigned NOT NULL,
  `subject_field_id` int(11) unsigned NOT NULL,
  `send_to_sender_field_id` int(11) NOT NULL,
  `redirect_on_success` text NOT NULL,
  `fields_order` text NOT NULL,
  `use_mail_template` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `default_status_id` int(11) unsigned NOT NULL,
  `read_status_id` int(11) unsigned NOT NULL,
  `reply_status_id` int(11) unsigned NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `checked_out` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп даних таблиці `#__aicontactsafe_profiles`
--

INSERT INTO `#__aicontactsafe_profiles` (`id`, `name`, `use_ajax`, `use_message_css`, `contact_form_width`, `bottom_row_space`, `align_buttons`, `contact_info_width`, `use_captcha`, `captcha_type`, `align_captcha`, `email_address`, `always_send_to_email_address`, `subject_prefix`, `email_mode`, `record_message`, `record_fields`, `custom_date_format`, `custom_date_years_back`, `custom_date_years_forward`, `required_field_mark`, `display_format`, `plg_contact_info`, `use_random_letters`, `min_word_length`, `max_word_length`, `set_default`, `active_fields`, `captcha_width`, `captcha_height`, `captcha_bgcolor`, `captcha_backgroundTransparent`, `captcha_colors`, `name_field_id`, `email_field_id`, `subject_field_id`, `send_to_sender_field_id`, `redirect_on_success`, `fields_order`, `use_mail_template`, `default_status_id`, `read_status_id`, `reply_status_id`, `date_added`, `last_update`, `published`, `checked_out`, `checked_out_time`) VALUES
(1, 'Заказать обратный звонок', 1, 1, 0, 0, 2, 0, 0, 0, 1, 'admin@cyberintels.com', 1, 'Заказ обратного звонка', 1, 1, 0, 'dmy', 60, 0, '*', 0, 0, 0, 5, 8, 1, '1,9', 300, 55, '#FFFFFF', 1, '#FF0000;#00FF00;#0000FF', 0, 0, 0, 0, '', '1,8,2,3,5,7,9', 0, 1, 2, 3, '2009-01-01 00:00:00', '2014-01-18 21:04:50', 1, 0, '0000-00-00'),
(2, 'Контакты', 1, 1, 0, 0, 2, 0, 0, 0, 1, 'admin@cyberintels.com', 1, 'Сообщение с сайта', 1, 1, 0, 'dmy', 60, 0, '*', 0, 0, 0, 5, 8, 0, '1,2,3,5', 180, 55, '#FFFFFF', 1, '#FF0000;#00FF00;#0000FF', 0, 0, 0, 0, '', '1,2,3,5,8,9,7', 0, 1, 2, 3, '2009-01-01 00:00:00', '2014-01-18 21:30:31', 1, 0, '0000-00-00'),
(3, 'Онлайн консультация', 1, 1, 0, 0, 2, 0, 0, 0, 1, 'info@cyberintels.com', 1, 'Запись на онлайн-консультацию с сайта afraidout.com', 1, 1, 0, 'dmy', 60, 0, '*', 0, 0, 0, 5, 8, 0, '1,2,8,7', 180, 55, '#FFFFFF', 1, '#FF0000;#00FF00;#0000FF', 0, 0, 0, 0, '', '1,2,3,5,8,7', 0, 1, 2, 3, '2009-01-01 00:00:00', '2013-12-22 22:50:15', 1, 0, '0000-00-00');

-- --------------------------------------------------------

--
-- Структура таблиці `#__aicontactsafe_statuses`
--

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_statuses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `color` varchar(10) NOT NULL DEFAULT '#FFFFFF',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `checked_out` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп даних таблиці `#__aicontactsafe_statuses`
--

INSERT INTO `#__aicontactsafe_statuses` (`id`, `name`, `color`, `ordering`, `date_added`, `last_update`, `published`, `checked_out`, `checked_out_time`) VALUES
(1, 'New', '#FF0000', 1, '2013-11-27 16:00:52', '2013-11-27 16:00:52', 1, 0, '0000-00-00'),
(2, 'Read', '#000000', 2, '2013-11-27 16:00:52', '2013-11-27 16:00:52', 1, 0, '0000-00-00'),
(3, 'Replied', '#009900', 3, '2013-11-27 16:00:52', '2013-11-27 16:00:52', 1, 0, '0000-00-00'),
(4, 'Archived', '#CCCCCC', 4, '2013-11-27 16:00:52', '2013-11-27 16:00:52', 1, 0, '0000-00-00');