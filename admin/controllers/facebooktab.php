<?php
/**
 * @version     1.3
 * @package     JLinker Facebook Tabs for J3
 * @copyright	Copyright (c) 2015 JLinker - All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @author      David Fiaty <contact@jlinker.com> - http://www.jlinker.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Facebooktab controller class.
 */
class Jlfacebooktabsj3ControllerFacebooktab extends JControllerForm
{

    function __construct() {
        $this->view_list = 'facebooktabs';
        parent::__construct();
    }

	public function postSaveHook($model, $validData)
	{
		$item = $model->getItem();

		$this->updateTabCats($item->get('id'));
		$this->saveFbPost($item->get('id'));
	}

	function saveFbPost($tabid = null) {
	
		$fbpost = JRequest::getVar('fbpost', array());
			
		$filteredToSave = array_map('intval', $fbpost);
		$db = JFactory::getDbo();
		$sql = "UPDATE #__jlfacebooktabsJ3_fbtabs SET fbpost = '" . json_encode($filteredToSave) . "' WHERE id = " . $tabid;
		$db->setQuery($sql);
		$db->execute();
	}   

	public function updateTabCats($tabid = null) {
	
		$app = JFactory::getApplication();
		$post = $app->input->post->getArray(array());
		
		$tabid = ($tabid) ? $tabid : $post['jform']['id'];

		if ( (int) $tabid > 0)
		{
			$db = JFactory::getDbo();
			$sql = "DELETE FROM #__jlfacebooktabsJ3_tab_cat WHERE tabid = " . $tabid;
			$db->setQuery($sql);
			$db->execute(); 	
		
			foreach ($post['jl_items'] as $cid)
			{
				$sql = "INSERT INTO  #__jlfacebooktabsJ3_tab_cat (tabid, catid)
						VALUES (" . $tabid . ", " . $cid . ")";
				$db->setQuery($sql);
				$db->execute(); 							
			}
 		}	
 	}

}