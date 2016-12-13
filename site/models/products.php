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
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');


//load the tab config
require_once(JPATH_COMPONENT_ADMINISTRATOR. '/tables/facebooktab.php');

/**
 * Model
 */
class Jlfacebooktabsj3ModelProducts extends JModelLegacy
{
	/**
	 * @var string msg
	 */
	protected $products;
	protected $currencyModel;
	protected $productModel;
	/**
	 * Get the message
	 * @return string The message to be displayed to the user
	 */
	public function getProducts() 
	{
		if (!isset($this->products)) 
		{
			//get the target language
			$lang = JRequest::getVar('jllang', null);
			if ($lang)
			{
				$lang = str_replace('_','-', $lang);
			}
			else
			{
				$lang = '*';
			}

			//get the data
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select("*");
			$query->from("#__content");
			$query->where("state = 1");
			$query->where("language = '" . $lang . "'");
			$db->setQuery($query);
	
			$items = $db->loadObjectList();
			$this->products = $this->_filterByTab($items);
			
		}
		
		//return the data
		return $this->products;
	}
	
	private function _filterByTab ($dataArray)
	{
		
		$tabid = JRequest::getVar('jltabid', null);      
			
		if ((int)$tabid > 0)
		{
			$db = JFactory::getDbo();

			$tabconf =  new JlfacebooktabsJ3Tablefacebooktab($db);
			$tabconf->load($tabid);

			//start the filtering
			$output = array();
			$targetProds = $this->_getTargetProducts();
					
			if ($targetProds)
			{
				foreach ($dataArray as $key => $item)
				{
					if (in_array($item->id, $targetProds))
					{	
						$item = $this->_addData($item);
						$output[] = $item;					
					}				
				}
			}
		
			return $output;
		}
		
		return false;
	}

	private function _addData($item) {
	
		//update url field
		$config = JFactory::getConfig();
		$item->link =  JRoute::_('index.php?option=com_content&view=article&id=' . $item->id, true, -1);
		if ((int)$config->get('force_ssl') > 0)
		{
			$item->link = str_replace('http', 'https', $item->link);
		}
				
		return $item;
	}
	
	private function _replaceTagUrls($tags) {
	
		$output = array();
		
		if (count($tags) > 0)
		{
			foreach ($tags as $tag)
			{
				$tag->link = str_replace(JURI::base(true) . '/', JURI::base(), $tag->link);
				$output[] = $tag;
			}
		}
		
		return $output;
	}


	private function _getTargetProducts ()
	{
		$tabid = JRequest::getVar('jltabid', null);         	
		$catid = JRequest::getVar('catid', null);   
		$cat_query = '';
				
		if ((int) $tabid > 0)
		{
			if ((int) $catid > 0)
			{
				$cat_query .= ' AND c.catid = ' . $catid;
			}

			$db = JFactory::getDbo();
			$sql = '
			SELECT c.id FROM #__content AS c
			LEFT JOIN #__jlfacebooktabsJ3_tab_cat AS jltc ON c.catid = jltc.catid
			WHERE jltc.tabid = ' . $tabid . $cat_query;
			
			$db->setQuery($sql);
			$result = $db->loadRowList();
			if ($result) return call_user_func_array('array_merge', $result);

		}
		
		return array();
	}
}





?>

