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

$doc = JFactory::getDocument();

$frozen = true;
$allContent = null;
$pageid = 0;

if ($this->item->id > 0)
{
	$frozen = false;
	$allContent = JlfacebooktabsJ3Helper::getAllContent($this->item->id);
	$pageid = JlfacebooktabsJ3Helper::getPageId($this->item->id);

	$script = 'var allContent = ' . json_encode($allContent);
	$doc->addScriptDeclaration( $script );
}

?>

<div class="control-group">
	<?php
		if (!$frozen)
		{
		?>
			<div class="control-label"><?php echo JText::_("COM_JLFACEBOOKTABSJ3_LBL_CONTENT"); ?></div>
			<div class="controls controls-1">
				<select id="fbpost" name="fbpost[]" multiple>
					<?php
						foreach ($allContent as $content)
						{
							$selected = (in_array($content->j3_product_id, $this->item->fbpost)) ? "selected" : '';
					
							?>
								<option value="<?php echo $content->j3_product_id;?>" <?php echo $selected; ?>><?php echo $content->product_name;?></option>
							<?php
						}
					?>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;
			</div>
			
			<div class="controls-2">
					<div id="sendButton"></div>
					<div id="fbbuttons">
						<fb:login-button autologoutlink="true" data-size="large" onlogin="checkLoginState();"></fb:login-button>
					</div>
					<div id="status"></div>  
					<div id="msg"></div>  
			</div>
		<?php
		}
		else
		{
	?>	
			<div class="control-label"><?php echo JText::_("COM_JLFACEBOOKTABSJ3_LBL_PLEASE_SAVE"); ?></div>	
	<?php
		}
	?>	
</div>

<script>
	window.fbAsyncInit = function() {
		FB.init({
			appId      : '<?php echo JlfacebooktabsJ3Helper::getAppId(); ?>',
			cookie     : true,   
			xfbml      : true, 
			//channelUrl : 'https://www.jlinker.com/channel.html',
			version    : 'v2.5'
		});

		FB.getLoginStatus(function(response) {
			statusChangeCallback(response);
		});
	};

	(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));

	function testAPI() {
		FB.api('/me', function(response) {
		  document.getElementById('status').innerHTML = '<?php echo JText::_("COM_JLFACEBOOKTABSJ3_FB_CONNECTED"); ?>' + response.name;
		});
	}  

	function statusChangeCallback(response) 
	{
		if (response.status === 'connected') 
		{
			jQuery('#fbbuttons').append('<a href="#" id="sendToFacebook" class="btn btn-primary" onclick="sendData()">' + '<?php echo JText::_("COM_JLFACEBOOKTABSJ3_LBL_SEND"); ?>' + '</a>');
			testAPI();
		} 
		else if (response.status === 'not_authorized') 
		{
			document.getElementById('status').innerHTML = '<?php echo JText::_("COM_JLFACEBOOKTABSJ3_FB_APP_LOGIN"); ?>';
		} 
		else 
		{
			jQuery('#sendToFacebook').remove();
			document.getElementById('status').innerHTML = '<?php echo JText::_("COM_JLFACEBOOKTABSJ3_FB_ACCOUNT_LOGIN"); ?>';
		}
	}

	function checkLoginState() 
	{
		FB.getLoginStatus(function(response) {
		  statusChangeCallback(response);
		});
	}
	
	function getSelectedItems() {
	
		//get button obj
		var btn = jQuery(elt);
	
		//get selected vals
		var selectedItems = [];
		jQuery('#fbpost :selected').each(function(i, selected){ 
			selectedItems[i] = jQuery(selected).val(); 
		});
			
		if( selectedItems.length > 0 ) { 
		
			var pageid = '<?php echo (int) $pageid; ?>';
			if (pageid > 0)
			{
				var requestUrl = 'index.php?option=com_jlfacebooktabsj3&view=ajax&format=json&action=fbpost.tab.page&pageid=<?php echo $pageid; ?>&tabid=<?php echo $this->item->id; ?>'; 
				jQuery.ajax({
					url: requestUrl,  
					type: 'POST',
					data: {jlitems:selectedItems},
					success:function(data) {

					},
					error: function(data) {

					}	
				});
			}	
			else
			{
				alert('<?php echo JText::_("COM_JLFACEBOOKTABSJ3_ERROR_NO_PAGEID"); ?>');		
			}	
		}
		else
		{
			alert('<?php echo JText::_("COM_JLFACEBOOKTABSJ3_ERROR_NO_VALUES"); ?>');
		}
	}

	function checkPermissions(permArray)
	{
		var manage_pages = findPerm('manage_pages', permArray.data);
		var publish_pages = findPerm('publish_pages', permArray.data);

		if (manage_pages[0].status == 'granted' && publish_pages[0].status == 'granted')
		{
			return true;
		}

		return false;
	}

	function findPerm(permName, permObjects){
		return $.grep(permObjects, function(n, i){
			return n.permission == permName;
		});
	};
	
	function sendData() {
		FB.login(
			function(response) {
				if (response.status === 'connected') 
				{
					FB.api('/me/permissions', function (response) 
					{
						var allowed = checkPermissions(response);
						if (allowed)
						{
							var selectedContent = jQuery('select#fbpost').val();
					
							for (var i = 0; i < allContent.length; i++)
							{
								if (jQuery.inArray(allContent[i].j3_product_id, selectedContent) !== -1)
								{
									//prepare the data
									var params = {};
									var link = 'index.php?option=com_j3&view=productdetails&j3_product_id=' + allContent[i].j3_product_id;
									params['message'] = 'nice post message from cmsbox.fr';
									params['name'] = allContent[i].product_name;
									params['description'] = allContent[i].product_s_desc;
									params['link'] = '<?php echo JURI::root() . JRoute::_("' + link + '"); ?>';
									params['picture'] = 'http://3.bp.blogspot.com/--jbgq-gytJQ/URaJHK_93LI/AAAAAAAAAF0/SkyoK7H3r7U/s1600/Simple+Apple.png';
									params['caption'] = 'nice caption from cmsbox.fr';
	
									//post the data
									FB.api('/<?php echo $pageid; ?>/feed', 'post', params, function(response) {
										if (!response || response.error) 
										{
											//permissions are not sufficient
											//should never be executed 
											//as permissions have already been checked previously
										} 
										else 
										{
											document.getElementById('msg').innerHTML = '<?php echo JText::_("COM_JLFACEBOOKTABSJ3_FB_POST_SUCCESS"); ?>';
										}
									});
								}
							}
						}
						else
						{
							document.getElementById('msg').innerHTML = '<?php echo JText::_("COM_JLFACEBOOKTABSJ3_FB_NEEDS_PERMISSIONS"); ?>';
						}	
					});
				} 
			},
			{
				scope: 'public_profile,email,manage_pages,publish_pages',
				return_scopes: true,
				auth_type: 'rerequest'
			}
		);
	}
</script>

<style>
.controls-1, .controls-2 {
	float: left;
}

#status {
	margin-top: 10px;
}

a#sendToFacebook.btn.btn-primary {
	margin-top: -8px;
	margin-left: 10px;
}

#msg, #status {
	padding: 1px 10px 1px 10px;
}

#msg {
	background-color: #C6E2FF;
	color: #483D8B;
}
</style>

