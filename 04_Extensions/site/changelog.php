<?php
/**
 * @package JComments
 * @version 3.0.0
 * @author Sergey M. Litvinov (smart@joomlatune.ru)
 * @copyright (C) 2006-2013 by Sergey M. Litvinov (http://www.joomlatune.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;
?>

Changelog
------------

3.0.6
+ Added support of Joomla's CAPTCHA plugins
+ Support onJCommentsFormBeforeDisplay when form is hidden for guests

3.0.5
# Fixed incorrect quotes processing if author name contains apostrophe
# Incorrect content plugin behavior when introtext is empty
+ Added plugin for RS!Events Pro objects support (thanks to Michel Petillion)
^ Updated plugin for RS!Events objects support
# Fixed bug with comments import
+ Added plugin for JomClassifieds objects support
# Fixed notice about undefined property $language
# Fixed meta keywords and description handling for JComments menu item
# Fixed 'Object Group' parameter's type for JComments menu item

3.0.4
# Fixed issue with editor's buttons (thanks to srosa)

3.0.3
# Fixed compatibility bug with Joomla 3.2
# Could not publish/unpublish comments in the JComments backend (Jomla 3.2)
# Warning in content plugin (thanks to barboss)
^ Automatic correction of default usergroup for guests (Joomla 3.2)

3.0.2
# Incorrect update for old parameter 'enable_mambots'
# Saved changes not having any effect (disabling CAPTCHA)
# Fatal error: Cannot redeclare class JCommentsControllerLegacy
^ Improved comments import from RSComments
^ Not showing IP address of authors in admin/backend
^ Categories list does not uses Bootsrap �hosen behavior
- Removed 'All' option from language filter in Settings section
# CustomBBCodes were not loaded
# Refresh cache does not stop if comments list is empty
+ Duplicate feature in CustomBBCodes section
^ Cosmetic improvements in Smilies section
# Button images were not shown in CustomBBCodes section
+ Added plugin for Joomla Estate Agency (JEA) objects support
# Error comments import from JooComments
# Not able to remove the last selected category from list
# Smilies might be not available when the multilanguage feature is enabled
^ Updated Virtuemart plugin
# Incorrect settings saving after disabling multilanguage feature
^ Updated custom bbcodes for YouTube, Facebook
+ Added support links for Instagram photos (tag [instagram]
# BBCode tag [img] does not work with https:// urls
# Error comments import from JAComment
+ Added plugin for DJ-Catalog2 objects support
+ Added support links for Vimeo video (tag [vimeo]
+ Added image for Custom BBCode Instagram and Vimeo
# The unsubscribe link does not work
^ After unsubscribe user will be redirected to page with comments instead site's home 
^ JComments settings will be opened for default site languge if multilanguage feature is active and no language was selected
# Incrorrect objects's cache refresh if multilanguage feature is active
+ Added support the Komento tags {KomentoEnable},  {KomentoDisable} and {KomentoLock}
+ Remove subscriptions for comments for certain article if article has been removed
# The <br /> tags instead new lines in comment edit form in backend

3.0.1
# Updated jDownloads plugin (incorrect links generation)
# Disabling smilies does not disable them
# Content plugin does not support alternate readmore text
# Updated JEvent plugin (thanks Tony from JEvents)
# Incorrect processing of forbidden words list

3.0.0
- Removed support of Joomla 1.0 and Joomla 1.5
- Removed support of comments import from extensions which are not work on Joomla 2.5+
- Removed JComments plugins for extensions which are not work on Joomla 2.5+
+ Added custom SEF extension for sh404sef
+ Added quickicon plugin for Joomla CPanel (allows quick access to JComments extension)
+ Added Mail queue manager in JComments' backend
+ Added plugins for support MijoEvents, MijoPolls, MijoShop, MijoVoice (thanks to Denis Dulici, Mijosoft LLC)
+ Added plugin for support Akeeba Release System
+ Added plugin for support TZ Portfolio
+ Added plugin for support ImproveMyCity
+ Added plugin for support iCagenda
^ The JComments backend now uses native Joomla's code with MVC
^ The JComments installer uses Joomla API to install additional plugins
^ Huge code improvements and optimizations

* -> Security Fix
# -> Bug Fix
$ -> Language fix or change
+ -> Addition
^ -> Change
- -> Removed
! -> Note