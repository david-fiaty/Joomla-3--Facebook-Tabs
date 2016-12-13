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

class JlfacebooktabsJ3J3
{
	var $_model = null;
	var $_tree = null;
	
    public function getDataTree()
    {
		$output = array();

    	$this->_model = JCategories::getInstance('Content');
		    
		//build the tree
		$this->_tree = $this->_getTree();
		
		$output = null;	
		if ($this->_tree)
		{
			//get the output
			$output = $this->_renderTree($this->_tree);
		}
		
		//return the data
		return $output;
    }
    
    private function _renderTree($tree) 
    {
   		$fbtabModel = JModelLegacy::getInstance( 'facebooktab', 'Jlfacebooktabsj3Model' ); 	
    	$activeCats = $fbtabModel->getActiveCats();

    	//output the html
    	$output = '<ul id="jl-tree">';
	   	foreach ($tree as $item_id => $children)
    	{
    		$item = $this->_getItemData($item_id);	    	
   			if ($item)
    		{			
  				$checked = (in_array($item_id, $activeCats)) ? 'checked="true"' : '';
    					
				$output .= '<li>';
				$output .= '<input type="checkbox" ' . $checked . ' value="' . $item_id . '" name="jl_items[]"><span>' . JHtml::_('string.truncate', $item->title, 50) . '</span>';
				$output .= '<div class="jl_catinfo">';
				$output .= '<span>' . ($item->published > 0 ? '<i class="icon-publish"></i>' : '<i class="icon-unpublish"></i>') . '</span>';
				$output .= '</div>';
			}
			
    		if (count($children) == 0)
    		{
    			$output .= '</li>';
    		}
    		else
    		{
    			$output .= $this->_renderTree($children);
    		}
    	}
    	
    	$output .= '</ul>';

    	return $output;
    } 

    private function _getTree()
	{
		//get the target language
		$mainframe = JFactory::getApplication();
		$data = $mainframe->getUserState( "com_jlmenugenerator.data", array() );
	   	$generate_for = @explode('-', $data['jl_generate_for']);
	 	$lang = isset($generate_for[2]) ? $generate_for[2] :  '*';
	 	$lang = str_replace('_','-', $lang);

		//get the data
		$items = $this->_model->get('root')->getChildren(true);
	
		foreach ($items as $key => $item)
		{	
			$itemLang = $item->language; 
			if ($itemLang == $lang)
			{
				if ((int) $item->parent_id == 0)
				{
					$this->_tree[$item->id] = $this->_getItemsRecursive($item->id);
				}
			}
		}

		return $this->_tree;
	}
	
	private function _getItemsRecursive ($item_id = 0)
	{
		$tree = array();
		$children = $this->_model->get($item_id)->getChildren(true);
	
		if (count($children) > 0)
		{
			foreach ($children as $child)
			{
				if ((int) $child->parent_id == (int) $item_id) 
				{
					$tree[$child->id] = $this->_getItemsRecursive($child->id);
					
				}
			}
		}	
		
		return $tree;					
	}	
    
	private function _getItemData ($item_id)
	{
		$item = $this->_model->get($item_id);
   		return $item;
	}

}