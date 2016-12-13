<?php
/**
 * @version     1.3
 * @package     JLinker Facebook Tabs for J3
 * @copyright	Copyright (c) 2015 JLinker - All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @author      David Fiaty <contact@jlinker.com> - http://www.jlinker.com
 */

defined('_JEXEC') or die;
 
/**
 * Script file 
 */
 
class com_jlfacebooktabsj3InstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		// $parent is the class calling this method
		$parent->getParent()->setRedirectURL('index.php?option=com_jlfacebooktabsj3');
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		// $parent is the class calling this method
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		//new fields
		$newfields = array('cache_enabled', 'items_limit', 'items_sort');

		//get old fields
		$db = JFactory::getDbo();
		$sql = "SHOW COLUMNS FROM #__jlfacebooktabsJ3_fbtabs";
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		
		if ($result)
		{
			$oldfields = array_map(function($o) { return $o->Field; }, $result);	
		
			//process
			foreach ($newfields as $f)
			{
				if (!in_array($f, $oldfields))
				{
					$sql = "ALTER TABLE `#__jlfacebooktabsJ3_fbtabs`";
					$sql .= " ADD COLUMN `" . $f . "` tinyint(3) NOT NULL";
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{

	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{

	}
}