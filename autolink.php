<?php

/*
Plugin Name: AutoLink
Description: This plugin automatically adds HTML anchor tags to plain text links and email addresses embedded in the content of posts and pages. You can choose to open the links in a new window or in the current window.
Version: 1.0
Author: Andreu Llos
Author URI: http://andreullos.com
Copyright: 2011, Andreu Llos

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
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
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

add_action('admin_menu', 'autolink_register_menu_item');


function autolink_register_menu_item() {	
	register_setting( 'autolink-settings-group', 'autolink_link_type'); 
	add_option('autolink_link_type', 1); //default is to make the links to open in a new window
	add_options_page('AutoLink Options', 'AutoLink', 10, 'autolink-options', 'autolink_options_panel');
}


function autolink_options_panel(){
	if(!function_exists('current_user_can') || !current_user_can('manage_options')){
			die(__('Cheatin&#8217; uh?'));
	} 
	?> 
	<div class="wrap">
	<h2>AutoLink Options</h2>
	<form method="post" action="options.php">

		<?php settings_fields('autolink-settings-group'); ?>
		<p><? _e("Choose the target of the links you want:", "autolink"); ?><br /><select name="autolink_link_type">
    	<option value="1"<? if(get_option('autolink_link_type')=='1'){ ?>selected="selected"<? } ?>><? _e("Open all the links in a new window", "autolink"); ?></option>
        <option value="2"<? if(get_option('autolink_link_type')=='2'){ ?>selected="selected"<? } ?>><? _e("Open all the links in the current window", "autolink"); ?></option>	    
        </select>	
		<p class="submit">
		  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>

	</div>
<? }



add_filter('the_content', 'allos_auto_link');
function allos_auto_link($text){
    $ret = ' ' . $text;
    if(get_option('autolink_link_type')=='1'){
	    $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a target=\"_blank\" href=\"\\2\" >\\2</a>'", $ret);
	    $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "'\\1<a target=\"_blank\" href=\"http://\\2\" >\\2</a>'", $ret);
    } elseif(get_option('autolink_link_type')=='2'){
	    $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a href=\"\\2\" >\\2</a>'", $ret);
	    $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://\\2\" >\\2</a>'", $ret);
    } 
    $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
    $ret = substr($ret, 1);
    return($ret);
}


?>