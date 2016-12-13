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
<div class="control-label"><?php echo $this->form->getLabel('language'); ?></div>
<div class="controls"><?php echo $this->form->getInput('language'); ?></div>
</div>
<table cellpadding="3" cellspacing="3" border="0" width="100%">
	<tr>
		<td>
			<div id="jl-headers">
				<h1><a id="jl-check-all" href="javascript:void(0)"><?php echo JText::_('COM_JLFACEBOOKTABSJ3_LBL_CHECK_ALL'); ?></a> | 
				<a id="jl-uncheck-all" href="javascript:void(0)"><?php echo JText::_('COM_JLFACEBOOKTABSJ3_LBL_UNCHECK_ALL'); ?></a></h1>				
				<h1><?php echo JText::_('COM_JLFACEBOOKTABSJ3_LBL_PUBLISHED'); ?></h1>
			</div>
		</td>
	</tr>
</table>
		
<table cellpadding="3" cellspacing="3" border="0" width="100%">
	<tr>
		<td nowrap="nowrap">
			<div id="jl-container">
				<div>
					<?php 
					
						$tree = JlfacebooktabsJ3Helper::getTree();
						if ($tree) 
						{
							echo $tree;
						}
						else
						{
						?>
							<p class="jl-no-data">
								<?php echo JText::_( 'COM_JLFACEBOOKTABSJ3_NO_DATA' ); ?>
							</p>
						<?php
						}
					?>					
				</div>
			</div>
		</td>
	</tr>
</table>			
        
 <script>
	jQuery(document).ready(function() {	
		var targetTree = '#jl-container div';

		//init the tree data
		$(targetTree).tree({
			dnd: false,
			onCheck: {
				node: 'expand'
			},
		});

		//style the tree rows
		$('#jl-tree li:even').addClass('jl-even');
		$('#jl-tree li:odd').addClass('jl-odd');

		//bind the check all behavior
		$('#jl-check-all').click(function(){
			$("input[name='jl_items[]']").each( function() { 
				$(this).prop('checked', true);
			});	
		});

		//bind the uncheck all behavior
		$('#jl-uncheck-all').click(function(){
			$("input[name='jl_items[]']").each( function() { 
				$(this).prop('checked', false);
			});	
		});
	});
</script>       