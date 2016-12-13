<?php
/**
 * @version     1.3
 * @package     JLinker Facebook Tabs for J3
 * @copyright	Copyright (c) 2015 JLinker - All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @author      David Fiaty <contact@jlinker.com> - http://www.jlinker.com
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

// fetch the view
$view = JRequest::getVar( 'view' , 'products' );

// use the view to fetch the right controller
require_once( JPATH_COMPONENT. '/controllers/'. $view.'.php' );

// initiate the contoller class and execute the controller
$controllerClass = 'Jlfacebooktabsj3Controller'.ucfirst($view);
$controller = new $controllerClass;
// call the display function in the controller by default - add a task param to the url to call another function in the controller
$controller->execute( JRequest::getVar( 'task', 'display' ) ); 
$controller->redirect();


?>