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

?>
<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('lightbox_active'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('lightbox_active'); ?></div>
</div>		
<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('featured_only'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('featured_only'); ?></div>
</div>					
<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('show_category'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('show_category'); ?></div>
</div>
<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('show_hits'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('show_hits'); ?></div>
</div>
<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('show_author'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('show_author'); ?></div>
</div>
<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('show_creation_date'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('show_creation_date'); ?></div>
</div>