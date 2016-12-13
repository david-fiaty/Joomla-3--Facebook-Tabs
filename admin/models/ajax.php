<?php
/**
 * @version     1.3
 * @package     JLinker Facebook Tabs for J3
 * @copyright	Copyright (c) 2015 JLinker - All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @author      David Fiaty <contact@jlinker.com> - http://www.jlinker.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * JlfacebooktabsJ3 model.
 */
class Jlfacebooktabsj3ModelAjax extends JModelLegacy
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_JLFACEBOOKTABSJ3';

	public function getAjaxQuery()
	{
		$action = JRequest::getVar('action'); 
		$db = JFactory::getDbo();

		$errors = array('status' => 0, 'msg' => null);
		
		//update the tab/fbpage map
		if ($action == 'update.tab.page') {
		
			$pageid = JRequest::getVar('pageid'); 
			$tabid  = JRequest::getVar('tabid'); 

			//check if page/tab exists
			$sql = "SELECT jltfb.pageid FROM #__jlfacebooktabsJ3_tab_fbpage AS jltfb, #__jlfacebooktabsJ3_fbtabs AS jlfb
			WHERE 1
			AND jltfb.tabid = jlfb.id 
			AND jltfb.pageid = " . $pageid . " 
			AND jlfb.state = 1";
			
			$db->setQuery($sql);

			if ($db->loadResult() != $pageid)
			{		
				$sql = "INSERT INTO  #__jlfacebooktabsJ3_tab_fbpage (tabid, pageid)
						VALUES (" . (int) $tabid . ", '" . (int) $pageid . "')";
				$db->setQuery($sql);
				if (!$db->execute())
				{
					$errors['status'] = 1;
					$errors['msg'] = JText::_('JERROR_AN_ERROR_HAS_OCCURRED');
				}		
			}	
			else if ($db->loadResult() == $pageid)
			{
				$errors['status'] = 1;
				$errors['msg'] = JText::_('COM_JLFACEBOOKTABSJ3_ERROR_ITEM_EXISTS');
			}				
		}
		
		else if  ($action == 'fbpost.tab.page') {
		
			$pageid = JRequest::getVar('pageid'); 
			$tabid  = JRequest::getVar('tabid'); 
			$jlitems  = JRequest::getVar('jlitems', array()); 
			
			if (count($jlitems) > 0)
			{
				$data = JlfacebooktabsJ3Helper::getFBPostContent($tabid, $jlitems);
			}

			//loop to send to facebook		
			/*
			var requestUrl = 'https://graph.facebook.com/' + '<?php echo $pageid; ?>' + '/feed?access_token=' + '<?php echo $accesstoken; ?>'
			+ '&message=test msg';  */

		}				

		else if  ($action == 'clear.page.cache') {
		
			$pageid = JRequest::getVar('pageid'); 
			$appid = JRequest::getVar('appid'); 
			$data = @file_get_contents('https://www.jlinker.com/web/api/app/clearcache/' . $pageid . '/' . $appid);
			
			if (!$data)
			{
				$errors['status'] = 1;
				$errors['msg'] = JText::_('JERROR_AN_ERROR_HAS_OCCURRED');
			}		
		
			return $data;

		}
		return json_encode($errors);		
	}
}
