<?php
/**
 * @version     1.3
 * @package     JLinker Facebook Tabs for J3
 * @copyright	Copyright (c) 2015 JLinker - All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @author      David Fiaty <contact@jlinker.com> - http://www.jlinker.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
//load J3 model
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_j3/models'); 
JTable::addIncludePath(JPATH_ADMINISTRATOR .'/components/com_j3/tables');

//load fbtab model
JLoader::import('joomla.application.component.model');
JLoader::import( 'items', JPATH_ADMINISTRATOR . '/components/com_jlfacebooktabsj3/models' );


/**
 * Model
 */
class Jlfacebooktabsj3ModelCategories extends JModelLegacy
{

	var $_model = null;
	var $_tree = array();

	/**
	 * @var string msg
	 */
	protected $categories;

	/**
	 * Get the message
	 * @return string The message to be displayed to the user
	 */
	public function getCategories() 
	{
		if (!isset($this->categories)) 
		{
			//get the vm model
    		//$this->_model = JCategories::getInstance('Content');
			$this->categories = $this->_getTree();
		}

		//return the data
		return $this->categories;
	}
 
    private function _getTree()
	{
		//get the target language
	    $requestedLang = JRequest::getVar('jllang', null);
	    $pageid = JRequest::getVar('pageid', 0);
	    
		if ($requestedLang)
		{
			$lang = str_replace('_','-', $requestedLang);
		}
		else
		{
			$lang = '*';
		}
		
		//get the data
		$db	=  JFactory::getDBO();
		$sql = "SELECT c.id, c.title, c.parent_id 
				FROM #__categories AS c, 
				#__jlfacebooktabsJ3_tab_fbpage AS jltf, #__jlfacebooktabsJ3_tab_cat AS jltc 
				WHERE jltf.pageid = '" . $pageid . "'
				AND c.language = '" . $lang . "'
				AND jltf.tabid = jltc.tabid 
				AND jltc.catid = c.id
				AND c.extension = 'com_content'";
			
		$db->setQuery($sql);					
		$tree = $db->loadObjectList();
			
		return $tree;
	} 
      
}

?>

