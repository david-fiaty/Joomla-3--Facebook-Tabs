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

require_once JPATH_COMPONENT_ADMINISTRATOR. '/helpers/jlfacebooktabsj3.php';

/**
 * View to edit
 */
class Jlfacebooktabsj3ViewFacebooktab extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $activeCats;

    /**
     * Display the view
     */
    public function display($tpl = null) {
    
    	//add the header scripts
		JlfacebooktabsJ3Helper::addHeaderScripts();
    
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        $this->item->fbpost = $this->get('FbPost');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar() {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user = JFactory::getUser();
        $isNew = ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
            $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
        $canDo = JlfacebooktabsJ3Helper::getActions();

        JToolBarHelper::title(JText::_('COM_JLFACEBOOKTABSJ3_TITLE_FACEBOOKTAB'), 'facebooktab.png');

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create')))) {

            JToolBarHelper::apply('facebooktab.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('facebooktab.save', 'JTOOLBAR_SAVE');
        }
        if (!$checkedOut && ($canDo->get('core.create'))) {
            JToolBarHelper::custom('facebooktab.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }
        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create')) {
            JToolBarHelper::custom('facebooktab.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }
        if (empty($this->item->id)) {
            JToolBarHelper::cancel('facebooktab.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolBarHelper::cancel('facebooktab.cancel', 'JTOOLBAR_CLOSE');
        }
    }

}
