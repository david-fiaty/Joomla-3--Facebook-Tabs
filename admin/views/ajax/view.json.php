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
jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Jlfacebooktabsj3ViewAjax extends JViewLegacy
{

	protected $headers;
	protected $response;
   
    public function __construct( $p_array = array() )
    {
    	parent::__construct( $p_array );
    	$this->headers = array(
    	"Cache-Control" => "no-cache, must-revalidate"
    	);
        $this->response = NULL;
    }
   
    // Overwriting JView display method
    function display($tpl = null) 
    {
		
        // Assign data to the view
        $this->response = utf8_encode(json_encode($this->get('AjaxQuery')));
         
        // Check for errors.
        if (count($errors = $this->get('Errors'))) 
        {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }
        
        
        // Display the view
        parent::display($tpl);
    }
    
    public function __destruct()
    {
      unset(
         $this->headers,
         $this->response
      );
    }
        
}