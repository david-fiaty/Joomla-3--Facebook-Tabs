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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tabstate');

$appid = JlfacebooktabsJ3Helper::getAppId();
$accesstoken = JlfacebooktabsJ3Helper::getAccessToken();


?>
<script>
var $ = jQuery.noConflict();
// Code that uses other library's $ can follow here.
</script>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {


        
    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'facebooktab.cancel') {
            Joomla.submitform(task, document.getElementById('facebooktab-form'));
        }
        else {
            
            if (task != 'facebooktab.cancel' && document.formvalidator.isValid(document.id('facebooktab-form'))) {
                
                Joomla.submitform(task, document.getElementById('facebooktab-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_("JGLOBAL_VALIDATION_FORM_FAILED")); ?>');
            }
        }
    }
    
    
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jlfacebooktabsj3&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="facebooktab-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_JLFACEBOOKTABSJ3_TITLE_FACEBOOKTAB', true)); ?>
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">

                	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
					<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
					
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('cache_enabled'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('cache_enabled'); ?>
						<button type="button" id="cache" class="btn btn-info btn-small">
						<i class="icon-cogs"></i><?php echo JText::_('COM_JLFACEBOOKTABSJ3_CLEAR_CACHE'); ?></button>
						<img id="icon-loading" style="display:none;" src="<?php echo JURI::base() . 'components/com_jlfacebooktabsj3/assets/images/spinner-sma.gif' ;?>">
						<span id="clear-msg"></span>
						</div>
					</div>		
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('theme'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('theme'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('layout'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('layout'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('intro_text'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('intro_text'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('footer_text'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('footer_text'); ?></div>
					</div>
					
					<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
					<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
					<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

					<?php if(empty($this->item->created_by)){ ?>
						<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />

					<?php } 
					else{ ?>
						<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />

					<?php } ?>

				</fieldset>
			</div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'contents', JText::_('COM_JLFACEBOOKTABSJ3_TITLE_CATEGORIES', true)); ?>
        <div class="row-fluid">
            <div class="form-horizontal">
                <fieldset class="adminform">
                	<div class="jlfbs">        
                		<?php echo $this->loadTemplate("data"); ?> 
                	</div>
                </fieldset>
            </div>
        </div>        
        <?php echo JHtml::_('bootstrap.endTab'); ?>        

        
    	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'display', JText::_('COM_JLFACEBOOKTABSJ3_TITLE_DISPLAY', true)); ?>
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">
                		<?php echo $this->loadTemplate("display"); ?> 
				</fieldset>
			</div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>   

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'descriptions', JText::_('COM_JLFACEBOOKTABSJ3_TITLE_DESCRIPTIONS', true)); ?>
        <div class="row-fluid">
            <div class="form-horizontal">
                <fieldset class="adminform">
                	<div class="jlfbs">        
                		<?php echo $this->loadTemplate("descriptions"); ?> 
                	</div>
                </fieldset>
            </div>
        </div>    
        <?php echo JHtml::_('bootstrap.endTab'); ?>            

        <!-- 
        <?php //echo JHtml::_('bootstrap.addTab', 'myTab', 'fbpost', JText::_('COM_JLFACEBOOKTABSJ3_TITLE_FBPOST', true)); ?>
        <div class="row-fluid">
            <div class="form-horizontal">
                <fieldset class="adminform">
                	<div class="jlfbs">        
                		<?php echo $this->loadTemplate("fbpost"); ?> 
                	</div>
                </fieldset>
            </div> 
        </div>    
        <?php //echo JHtml::_('bootstrap.endTab'); ?>            
		-->
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

		<?php echo JlfacebooktabsJ3Helper::getFooter(); ?>		

    </div>
    
</form>

<?php
$pageid = (int) JlfacebooktabsJ3Helper::getPageId($this->item->id);
if ($pageid > 0)
{
?>
	<script>
	
	var loading = $('#icon-loading');
	
	$('#cache').click(function(event) {
		event.preventDefault();
		
		var pageid = '<?php echo $pageid; ?>';
		var appid = '<?php echo $appid; ?>';
		
		if (parseInt(pageid) > 0 && parseInt(appid) > 0) {
		
			loading.show();
			var requestUrl = 'index.php?option=com_jlfacebooktabsj3&view=ajax&format=json&action=clear.page.cache&pageid=' + pageid + '&appid=' + appid;
   
			$.ajax(requestUrl, {
				success: function(data) {
					loading.hide();
					$('span#clear-msg').text("<?php echo JText::_('COM_JLFACEBOOKTABSJ3_CACHE_CLEARED'); ?>");
				},
				error: function() {
					alert("<?php echo JText::_('JERROR_AN_ERROR_HAS_OCCURRED'); ?>");
				}
			});
		}
		else
		{
			alert('<?php echo JText::_("COM_JLFACEBOOKTABSJ3_ERROR_NO_PAGEID"); ?>');		
		}
	});
	
	</script>
<?php
}
?>