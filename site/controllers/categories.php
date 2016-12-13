<?php
/**
 * @version     1.3
 * @package     JLinker Facebook Tabs for J3
 * @copyright	Copyright (c) 2015 JLinker - All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @author      David Fiaty <contact@jlinker.com> - http://www.jlinker.com
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class Jlfacebooktabsj3ControllerCategories extends JControllerLegacy
{
	public function getModel($name = 'Categories', $prefix = 'Jlfacebooktabsj3Model', $config = array()) 
	{
			$model = parent::getModel($name, $prefix, array('ignore_request' => true));
			return $model;
	}
}

?>