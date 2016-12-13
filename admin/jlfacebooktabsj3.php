<?php
/**
 * @version     1.3
 * @package     JLinker Facebook Tabs for J3
 * @copyright	Copyright (c) 2015 JLinker - All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @author      David Fiaty <contact@jlinker.com> - http://www.jlinker.com
 */

// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jlfacebooktabsj3')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Jlfacebooktabsj3');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
