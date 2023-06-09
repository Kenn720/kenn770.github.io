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

require_once __DIR__ . '/script.install.helper.php';

class RegularLabsInstallerScript extends RegularLabsInstallerScriptHelper
{
	public $name           = 'Regular Labs Library';
	public $alias          = 'regularlabs';
	public $extension_type = 'library';

	public function onBeforeInstall()
	{
		if (!$this->isNewer())
		{
			return false;
		}
	}
}
