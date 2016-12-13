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

if (file_exists(JPATH_ADMINISTRATOR . '/index.php'))
{
	//do nothing
}
else
{
	JFactory::getApplication()->enqueueMessage(JText::_('COM_JLFACEBOOKTABSJ3_NOT_INSTALLED'), 'warning');
}

require(JPATH_ADMINISTRATOR . '/components/com_jlfacebooktabsj3/helpers/j3.php');

//load fbtab model
JLoader::import('joomla.application.component.model');
JLoader::import( 'items', JPATH_ADMINISTRATOR . '/components/com_jlfacebooktabsj3/models' );

/**
 * JlfacebooktabsJ3 helper.
 */
class JlfacebooktabsJ3Helper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        		JHtmlSidebar::addEntry(
			JText::_('COM_JLFACEBOOKTABSJ3_TITLE_FACEBOOKTABS'),
			'index.php?option=com_jlfacebooktabsj3&view=facebooktabs',
			$vName == 'facebooktabs'
		);

    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_jlfacebooktabsj3';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }


	public static function addHeaderScripts() 
    {
		//add jquery for J 2.5
		$doc = JFactory::getDocument();

		//jquery
		$doc->addScript('components/com_jlfacebooktabsj3/assets/js/jquery/jquery-1.11.2.min.js');
		$doc->addScript('components/com_jlfacebooktabsj3/assets/js/jquery/jquery-ui-1.10.1.js');

		//jquery cookie
		$doc->addScript('components/com_jlfacebooktabsj3/assets/js/jquery/jquery.cookie.js');
		
		//jquery tree
		$doc->addScript('components/com_jlfacebooktabsj3/assets/js/tree/jquery.treecheckbox.js');
		$doc->addScript('components/com_jlfacebooktabsj3/assets/js/tree/jquery.treecollapse.js');
		$doc->addScript('components/com_jlfacebooktabsj3/assets/js/tree/jquery.tree.js');
		$doc->addScript('components/com_jlfacebooktabsj3/assets/js/tree/jquery.treeajax.js');
		$doc->addScript('components/com_jlfacebooktabsj3/assets/js/tree/jquery.treednd.js');
		
		//jlg
		$doc->addScript('components/com_jlfacebooktabsj3/assets/js/jlfacebooktabsJ3.js');

		//jquery tree css
		$doc->addStyleSheet('components/com_jlfacebooktabsj3/assets/css/jquery-ui.css');
		$doc->addStyleSheet('components/com_jlfacebooktabsj3/assets/css/jquery.tree.css');
		$doc->addStyleSheet('components/com_jlfacebooktabsj3/assets/css/jlfacebooktabsj3.css');   
	 }   
	 
	public static function getUrlForTab ($tabid)
	{
		$db = JFactory::getDbo();

		$sql = "SELECT jltfb.pageid FROM #__jlfacebooktabsJ3_tab_fbpage AS jltfb, #__jlfacebooktabsJ3_fbtabs AS jlfb
		WHERE 1
		AND jltfb.tabid = " . $tabid . " 
		AND jltfb.tabid = jlfb.id";
		
		$db->setQuery($sql);
												
		return $db->loadResult();
	} 
	 
    public static function getTree() 
    {
    	
    	$j3Data = new JlfacebooktabsJ3J3();
    	
    	return $j3Data->getDataTree();
    	
	}
	
	public static function getAppId()
	{
		$data = null;
		$data = @file_get_contents('https://www.jlinker.com/web/api/app/getid/' . JlfacebooktabsJ3Helper::getAppType());
		$appid = null;
		
		if ($data) {
			$appid = json_decode($data);
		}
		
		return $appid;
	}

	public static function getAppType()
	{
		return 'fbj3j3';
	}

	public static function getAccessToken()
	{
		$data = null;
		$data = @file_get_contents('https://www.jlinker.com/web/api/app/getaccesstoken/' . JlfacebooktabsJ3Helper::getAppType());
		return $data;
	}

    public static function getLayouts() 
    {
 		$layouts = array(
			'table' => 'Table',
			'grid' => 'Grid',
			'slideshow' => 'Slideshow'
		);
		
		return $layouts;
	}
	
    public static function getFooter() 
    {
 		$output = '<div align="center">' . JText::_('COM_JLFACEBOOKTABSJ3_FOOTER') . '</div>';
		return $output;
	}

	public static function getAllContent($tabid)
    {
		if ((int)$tabid > 0)
		{
			//get tab language
			$db = JFactory::getDbo();
			$sql = "SELECT language FROM #__jlfacebooktabsJ3_fbtabs WHERE id = " . (int) $tabid;
			$db->setQuery($sql);
			$lang = $db->loadResult();
			
			//get the data
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select("*");
			$query->from("#__content");
			$query->where("state = 1");
			$query->where("language = '" . $lang . "'");
			$db->setQuery($query);

			$items = $db->loadObjectList();
			
			if ($items)
			{
				return $items;
			}
		}
		
		return array();
	}
	
	public static function getFBPostContent($tabid, $cids = array())
    {		
		if ((int)$tabid > 0 && count($cids) > 0)
		{
			//clean array
			$cids = array_map('intval',  $cids);
			
			//get tab language
			$db = JFactory::getDbo();
			$sql = "SELECT language FROM #__jlfacebooktabsJ3_fbtabs WHERE id = " . (int) $tabid;
			$db->setQuery($sql);
			$lang = $db->loadResult();
		
			//get the content
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select("*");
			$query->from("#__content");
			$query->where("state = 1");
			$query->where("language = '" . $lang . "'");
			$db->setQuery($query);
			$items = $db->loadObjectList();
			if ($items)
			{
				$output = array();
				foreach ($items as $item)
				{
					$output[] = array(
						'title' => strip_tags(html_entity_decode($item->title)),
						'message' => strip_tags(html_entity_decode($item->introtext)),
					);
				}
				
				return $output;
			}
		}
		
		return array();
	}	

	public static function getPageId($tabid)
    {		
		if ((int)$tabid > 0)
		{
			$db = JFactory::getDbo();
			$sql = "SELECT pageid FROM #__jlfacebooktabsJ3_tab_fbpage WHERE tabid = " . (int) $tabid;		
			$db->setQuery($sql);
			$item = $db->loadRow();		
		
			return $item[0];
		}
		
		return 0;
	}	
}
