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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_jlfacebooktabsj3/assets/css/jlfacebooktabsj3.css');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_jlfacebooktabsj3');
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_jlfacebooktabsj3&task=facebooktabs.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'facebooktabList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
$appid = JlfacebooktabsJ3Helper::getAppId();
$accesstoken = JlfacebooktabsJ3Helper::getAccessToken();

//layouts
$layouts = JlfacebooktabsJ3Helper::getLayouts();
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}

	jQuery(document).ready(function () {
		
		//form submit
		jQuery('#clear-search-button').on('click', function () {
			jQuery('#filter_search').val('');
			jQuery('#adminForm').submit();
		});
			

		//after fb send  actions
		jQuery('body').on('click', '.fb-send', function() {
			var targetZone = jQuery(this).parents('.jlfbs-btn');
			var pageid = jQuery(this).attr('id').split('-');
			targetZone.empty();
			
			var resetUrl = 'index.php?option=com_jlfacebooktabsj3&view=facebooktabs&reset='+ pageid[2];			
			var resetLink = '<label class="label label-default"><a href="' + resetUrl + '" style="color: white"><?php echo JText::_("Reset"); ?></a></label>';
			
			targetZone.html('<div class="jlfbs-btn"><code><a target="_blank" href="https://www.facebook.com/'+ pageid[2] +'">' + pageid[2] + '</a></code>' + resetLink + '</div>');
		});

		//popover on click outside
		jQuery('body').on('click', function (e) {
			jQuery('.jlfbs-btn a').each(function () {
				// hide any open popovers when the anywhere else in the body is clicked
				if (!jQuery(this).is(e.target) && jQuery(this).has(e.target).length === 0 && jQuery('.popover').has(e.target).length === 0) {
					jQuery(this).popover('hide');
				}
			});
		});		
		
	});
	
	//facebook functions
	function sendToFB(elt) {
		var fb_page_url = elt.prev().val();	
						
		//start process							
		if (fb_page_url && isValidUrl(fb_page_url))
		{
			openFB(fb_page_url, elt);
		}
		else
		{
			alert('<?php echo JText::_("COM_JLFACEBOOKTABSJ3_ERROR_INVALID_URL"); ?>');
		}
	}

	function isValidUrl(myurl) {
		return /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(myurl)
	}	

	function openFB(fbUrl, elt){

		//show spinner
		jQuery('.fb-send-progress').css('visibility','visible');
		
		jQuery.get("https://graph.facebook.com/?id=" + fbUrl + "&access_token=<?php echo $accesstoken; ?>" , function( pageInfo ) {
		
			//if fb page exists
			if (parseInt(pageInfo.id) > 0)
			{ 
				var next = fbUrl + '?sk=app_<?php echo $appid; ?>';
				var targetUrl = 'https://www.facebook.com/dialog/pagetab?app_id=<?php echo $appid; ?>&redirect_uri=' + next ;
		
				//check fb data
				checkFBData(pageInfo, elt, targetUrl);
			}
			else
			{
				alert("<?php echo JText::_('COM_JLFACEBOOKTABSJ3_ERROR_FBPAGE_NOT_FOUND'); ?>");			
			}
			
			//hide spinner
			jQuery('.fb-send-progress').css('visibility','hidden');
			
		});
	}
	
	function createAPIClient(pageid)
	{
		var dataUrl = "https://www.jlinker.com/web/api/client/api/addurl/?page_id=" + pageid + "&site_url=<?php echo JURI::root(); ?>&app_id=<?php echo $appid; ?>&app_type=<?php echo JlfacebooktabsJ3Helper::getAppType(); ?>";
		$.ajax({
		  dataType: "jsonp",
		  url: dataUrl,
		}).done(function ( data ) { });
	}
	
	function findFBPageUrl(pageid, targetElt)
	{
		jQuery.get("https://graph.facebook.com/?id=" + pageid + "&access_token=<?php echo $accesstoken; ?>", function( pageInfo ) {
			if (parseInt(pageInfo.id) > 0)
			{ 
				targetElt.html(pageInfo.link);
			}
		});
	}
	
	function checkFBData(obj, elt, url)	
	{			
		//save pageid and tabid
		var requestUrl = 'index.php?option=com_jlfacebooktabsj3&view=ajax&format=json&action=update.tab.page&pageid=' + obj.id + '&tabid=' + elt.attr('id');
		jQuery.ajax({
			url: requestUrl,  
			success:function(data) {
			
				var op = JSON.parse(data);
				if (op.status == 0)
				{
					createAPIClient(obj.id);
					jQuery('<a href="' + url + '" class="fb-send btn btn-primary" id="fb-send-' + obj.id + '" target="_blank"><?php echo JText::_("COM_JLFACEBOOKTABSJ3_LBL_SEND");?></a>').insertAfter( ".fb-check" );	
					jQuery( ".fb-check" ).remove();
				}
				else if (op.status == 1)
				{
					alert(op.msg);
				}
				else
				{
					alert("<?php echo JText::_('JERROR_AN_ERROR_HAS_OCCURRED'); ?>");				
				}
			},
			error: function(data) {
				alert("<?php echo JText::_('JERROR_AN_ERROR_HAS_OCCURRED'); ?>");
			}	
			
		});		
		
	}
		
	/* override bootstrap popover to include callback */
	var showPopover = jQuery.fn.popover.Constructor.prototype.show;
	jQuery.fn.popover.Constructor.prototype.show = function() {
		showPopover.call(this);
		if (this.options.showCallback) {
			this.options.showCallback.call(this);
		}
	}
 
	var hidePopover = jQuery.fn.popover.Constructor.prototype.hide;
	jQuery.fn.popover.Constructor.prototype.hide = function() {
		if (this.options.hideCallback) {
			this.options.hideCallback.call(this);
		}
		hidePopover.call(this);
	}

/* usage */
 
/* 
$('#example').popover({
	showCallback : function() {
		console.log('popover is shown');
	},
	hideCallback : function() {
		console.log('popover is hidden');	
	}
});
 
*/
		
</script>

<?php

//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_jlfacebooktabsj3&view=facebooktabs'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
    
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn hasTooltip" id="clear-search-button" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>        
		<div class="clearfix"> </div>
		<table class="table table-striped" id="facebooktabList">
			<thead>
				<tr>
                <?php if (isset($this->items[0]->ordering)): ?>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
                <?php endif; ?>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
                <?php if (isset($this->items[0]->state)): ?>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
                <?php endif; ?>
                    
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_JLFACEBOOKTABSJ3_FACEBOOKTABS_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_JLFACEBOOKTABSJ3_FACEBOOKTABS_THEME', 'a.theme', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_JLFACEBOOKTABSJ3_FACEBOOKTABS_LAYOUT', 'a.layout', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_JLFACEBOOKTABSJ3_LBL_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
				</th>
 				<th class='left'>
				<?php echo JText::_('COM_JLFACEBOOKTABSJ3_FACEBOOKTABS_ADD'); ?>
				</th>                   
                    
                <?php if (isset($this->items[0]->id)): ?>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
                <?php endif; ?>
				</tr>
			</thead>
			<tfoot>
                <?php 
                if(isset($this->items[0])){
                    $colspan = count(get_object_vars($this->items[0]));
                }
                else{
                    $colspan = 10;
                }
            ?>
			<tr>
				<td colspan="<?php echo $colspan ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				$ordering   = ($listOrder == 'a.ordering');
                $canCreate	= $user->authorise('core.create',		'com_jlfacebooktabsj3');
                $canEdit	= $user->authorise('core.edit',			'com_jlfacebooktabsj3');
                $canCheckin	= $user->authorise('core.manage',		'com_jlfacebooktabsj3');
                $canChange	= $user->authorise('core.edit.state',	'com_jlfacebooktabsj3');
				?>
				<tr class="row<?php echo $i % 2; ?>">
                    
                <?php if (isset($this->items[0]->ordering)): ?>
					<td class="order nowrap center hidden-phone">
					<?php if ($canChange) :
						$disableClassName = '';
						$disabledLabel	  = '';
						if (!$saveOrder) :
							$disabledLabel    = JText::_('JORDERINGDISABLED');
							$disableClassName = 'inactive tip-top';
						endif; ?>
						<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
							<i class="icon-menu"></i>
						</span>
						<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
					<?php else : ?>
						<span class="sortable-handler inactive" >
							<i class="icon-menu"></i>
						</span>
					<?php endif; ?>
					</td>
                <?php endif; ?>
					<td class="hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
                <?php if (isset($this->items[0]->state)): ?>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $item->state, $i, 'facebooktabs.', $canChange, 'cb'); ?>
					</td>
                <?php endif; ?>
                    
				<td>
				<?php if (isset($item->checked_out) && $item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'facebooktabs.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_jlfacebooktabsj3&task=facebooktab.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->name); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->name); ?>
				<?php endif; ?>
				</td>
				<td>

					<?php echo ucfirst($item->theme); ?>
				</td>
				<td>

					<?php echo $layouts[$item->layout]; ?>
				</td>
				<td>

					<?php echo $item->language; ?>
				</td>
				<td>
				<?php
				$fbpageid = JlfacebooktabsJ3Helper::getUrlForTab($item->id);
				if ($fbpageid) {
				?>
				<code class="code-<?php echo $item->id; ?>"><a target="_blank" href="https://www.facebook.com/<?php echo $fbpageid; ?>"><?php echo $fbpageid; ?></a></code>
				<script>findFBPageUrl("<?php echo $fbpageid; ?>", jQuery(".code-<?php echo $item->id; ?>"));</script>
				<label class="label label-default"><a href="<?php echo JRoute::_('index.php?option=com_jlfacebooktabsj3&view=facebooktabs&reset='.(int) $item->id); ?>" style="color: white"><?php echo JText::_("Reset"); ?></a></label>
				<?php
				}
				else
				{
				?>
				
					<div class="jlfbs-btn"><a class="fb-show" href="#" id="pop-<?php echo $item->id; ?>"></a></div>
					<script>
					
	
											
							jQuery('.jlfbs-btn a#pop-' + '<?php echo $item->id; ?>').popover({
								title: '<strong><?php echo JText::_("COM_JLFACEBOOKTABSJ3_LBL_FBTAB"); ?></strong>', 
								content: '<code class="code-<?php echo $item->id; ?>"><?php echo JText::_("COM_JLFACEBOOKTABSJ3_LBL_FBPAGE_URL"); ?></code><input type="text" name="fb_page_url" value=""/><a href="#" id="' + '<?php echo $item->id; ?>' + '" class="fb-check btn btn-default" onclick="sendToFB(jQuery(this))"><?php echo JText::_("COM_JLFACEBOOKTABSJ3_LBL_FBPAGE_CHECK"); ?></a><div class="fb-send-progress"></div>', 
								html: true, 
								placement: "left",
								showCallback: function () {
			
									//hide popover spinner
									jQuery('.fb-send-progress').css('visibility','hidden');
			
								}
							}); 

	
					</script>
				<?php
				}
				?>
				</td>


                <?php if (isset($this->items[0]->id)): ?>
					<td class="center hidden-phone">
						<?php echo (int) $item->id; ?>
					</td>
                <?php endif; ?>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
		
		<?php echo JlfacebooktabsJ3Helper::getFooter(); ?>		

	</div>

</form>        
		
		
<style>

#facebooktabList .label-default {
	display: inline;
	margin-left: 4px;
}
</style>

