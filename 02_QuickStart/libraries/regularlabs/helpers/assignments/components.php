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

class RLAssignmentsComponents extends RLAssignment
{
	function passComponents()
	{
		return $this->passSimple(strtolower($this->request->option));
	}
}
