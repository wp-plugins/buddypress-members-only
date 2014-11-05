<?php
/*
Plugin Name: BuddyPress Members only
Description: Only registered users can view your site, non members can only see a login/home page with no registration options
Version: 1.0.5
Author: Tomas Zhu
Author URI: http://tomas.zhu.bz/
Plugin URI: http://tomas.zhu.bz/

Copyright 2014  tomas.zhu  (email : expert2wordpress@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
ob_start();
add_action('admin_menu', 'bp_members_only_option_menu');

function bp_members_only_option_menu()
{

   add_menu_page(__('Buddypress Members Only', 'BPMO'), __('Buddypress Members Only', 'BPMO'), 10, 'bpmemberonly', 'buddypress_members_only_setting');
   add_submenu_page('bpmemberonly', __('Buddypress Members Only','BPMO'), __('Buddypress Members Only','BPMO'), 10, 'bpmemberonly', 'buddypress_members_only_setting');
}

function buddypress_members_only_setting()
{
		global $wpdb;
		$m_bpmoregisterpageurl = get_option('bpmoregisterpageurl');

		if (isset($_POST['bpmosubmitnew']))
		{
			if (isset($_POST['bpmoregisterpageurl']))
			{
				$m_bpmoregisterpageurl = $wpdb->escape($_POST['bpmoregisterpageurl']);
			}
				
				update_option('bpmoregisterpageurl',$m_bpmoregisterpageurl);
			
			buddypress_members_only_message("Changes saved.");
		}
		echo "<br />";

		$saved_register_page_url = get_option('bpmoregisterpageurl');
		?>

<div style='margin:10px 5px;'>
<div style='float:left;margin-right:10px;'>
<img src='<?php echo get_option('siteurl');  ?>/wp-content/plugins/buddypress-members-only/images/new.png' style='width:30px;height:30px;'>
</div> 
<div style='padding-top:5px; font-size:22px;'> <i></>Buddypress Members Only Setting:</i></div>
</div>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:90%;">
								<div class="postbox">
									<h3 class='hndle'><span>
										Option Panel:
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<br />
										<form id="bpmoform" name="bpmoform" action="" method="POST">
										<table id="bpmotable" width="100%">
										<tr>
										<td width="30%">
										Register Page URL:
										</td>
										<td width="70%">
										<input type="text" id="bpmoregisterpageurl" name="bpmoregisterpageurl" size="70" value="<?php  echo $saved_register_page_url; ?>">
										</td>
										</tr>
										</table>
										<br />
										<input type="submit" id="bpmosubmitnew" name="bpmosubmitnew" value=" Submit ">
										</form>
										
										<br />
									</div>
								</div>
							</div>
						</div>
					</div>
		    	</div>
			</div>
		</div>
		<div style="clear:both"></div>
		<br />
		
		<?php
		}				

	
function buddypress_members_only_message($p_message)
{

	echo "<div id='message' class='updated fade'>";

	echo $p_message;

	echo "</div>";

}

function buddypress_only_for_members()
{
	if (is_front_page()) return;
	if (function_exists('bp_is_register_page') && function_exists('bp_is_activation_page') )
	{
		if ( bp_is_register_page() || bp_is_activation_page() )
		{
			return;
		}
	}
	$current_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$current_url = str_ireplace('http://','',$current_url);
	$current_url = str_ireplace('www.','',$current_url);
	$saved_register_page_url = get_option('bpmoregisterpageurl');

	$saved_register_page_url = str_ireplace('http://','',$saved_register_page_url);
	$saved_register_page_url = str_ireplace('www.','',$saved_register_page_url);
	
	if (stripos($current_url,$saved_register_page_url) === false)
	{

	}
	else 
	{
		return;
	}
	
	if ( is_user_logged_in() == false )
	{
		if (empty($saved_register_page_url))
		{
			$current_url = $_SERVER['REQUEST_URI'];
			//$redirect_url = wp_login_url( get_option('siteurl').$current_url );
			$redirect_url = wp_login_url( );
			header( 'Location: ' . $redirect_url );
			die();			
		}
		else 
		{
			$saved_register_page_url = 'http://'.$saved_register_page_url;
			header( 'Location: ' . $saved_register_page_url );
			die();
		}
	}
}

if (function_exists('bp_is_register_page') && function_exists('bp_is_activation_page') )
{
	add_action('wp','buddypress_only_for_members');
}
else 
{
	add_action('wp_head','buddypress_only_for_members');
}