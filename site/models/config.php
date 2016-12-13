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
/**
 * Model
 */
class Jlfacebooktabsj3ModelConfig extends JModelLegacy
{
        /**
         * @var string msg
         */
        protected $config;
 
        /**
         * Get the message
         * @return string The message to be displayed to the user
         */
        public function getConfig() 
        {
 			if (!isset($this->config)) 
        	{
        		//get from request: facebook page id
        		$pageid = JRequest::getVar('pageid', null);
        		
        		//load the config
        		if ((int)$pageid > 0)
				{
					$db = JFactory::getDbo();
					$sql = "
						SELECT * FROM #__jlfacebooktabsJ3_fbtabs AS jlfbt
						LEFT JOIN #__jlfacebooktabsJ3_tab_fbpage AS jlfbp ON jlfbt.id = jlfbp.tabid
						WHERE jlfbp.pageid = '" . $pageid . "' AND jlfbt.state = 1";
					$db->setQuery($sql);
			
					$this->config = $db->loadObject();
					
					//add site url
					if ($this->config)
					{
						//add site url
						$this->config->siteurl = rtrim(JURI::base(), '/');	

						//add global component config
						$params = JComponentHelper::getParams('com_jlfacebooktabsj3' );
						$this->config->apikey = $params->get('apikey', '');
						$this->config->default_image = $params->get('default_image', '');
					}
				}							
            }

            //return the data
            return $this->config;
        }
        

}

?>


