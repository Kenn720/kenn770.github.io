<?php
/**
 * @package         NoNumber Extension Manager
 * @version         5.2.5
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class Com_NoNumberManagerInstallerScript extends Com_NoNumberManagerInstallerScriptHelper
{
	public $name = 'NONUMBER_EXTENSION_MANAGER';
	public $alias = 'nonumberextensionmanager';
	public $extname = 'nonumbermanager';
	public $extension_type = 'component';

	public function onAfterInstall()
	{
		$this->removeOldeLicensesTable();
		$this->deleteOldFiles();
	}

	public function removeOldeLicensesTable()
	{
		$query = "DROP TABLE IF EXISTS `#__nonumber_licenses`;";
		$this->db->setQuery($query);
		$this->db->execute();
	}

	private function deleteOldFiles()
	{
		$this->deleteFolders(
			array(
				JPATH_SITE . '/components/com_nonumbermanager',
			)
		);
	}
}
