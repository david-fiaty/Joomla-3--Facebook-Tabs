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

jimport('joomla.application.component.modeladmin');

/**
 * JlfacebooktabsJ3 model.
 */
class Jlfacebooktabsj3ModelFacebooktab extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_JLFACEBOOKTABSJ3';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Facebooktab', $prefix = 'Jlfacebooktabsj3Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_jlfacebooktabsj3.facebooktab', 'facebooktab', array('control' => 'jform', 'load_data' => $loadData));
        
        
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jlfacebooktabsj3.edit.facebooktab.data', array());

		if (empty($data)) {
			$data = $this->getItem();
            
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {

			//Do any procesing on fields here if needed
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {
			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__jlfacebooktabsJ3_fbtabs');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
	}

	function save($array){
				
		//set active language
		$jform = JRequest::getVar('jform');
		$lang = $jform['language'];
		
		$mainframe = JFactory::getApplication();
		$mainframe->setUserState( "com_jlfacebooktabsj3.jl_generate_for", $lang );		
				
		return parent::save($array);
	}   
	
	public function getFbPost()
	{
		$id = JRequest::getVar('id', null);
		
		if ((int) $id > 0)
		{
			$db = JFactory::getDbo();
			$sql = "SELECT fbpost FROM #__jlfacebooktabsJ3_fbtabs WHERE id = " . $id;
			$db->setQuery($sql);
			
			$res = $db->loadObject();
			if ($res)
			{
				return json_decode($res->fbpost);
			}
		}
		
		return array();
	}	
	
	public function getActiveCats()
	{
		$id = JRequest::getVar('id', null);
		
		if ((int) $id > 0)
		{
			$db = JFactory::getDbo();
			$sql = "SELECT catid FROM #__jlfacebooktabsJ3_tab_cat WHERE tabid = " . $id;
			$db->setQuery($sql);
			
			$res = $db->loadRowList();
			if ($res)
			{
				return call_user_func_array('array_merge', $res);
			}
		}
		
		return array();
	}
		

}