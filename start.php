<?php
/**
 * @package Elgg
 * @subpackage StickyWidget
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Steve Suppe <ssuppe.elgg@gmail.com>
 */
require_once('conf/types.php');
require_once('conf/functions.php');


function stickywidgets_init() {
	global $CONFIG;
	add_subtype("object", "sticky_widget", "StickyElggWidget");
	// Register a page handler, so we can have nice URLs
	register_page_handler('sticky_widgets','sw_page_handler');
	// Register page handler for the dashboard
	register_page_handler('dashboard','sw_dashboard');
	// Register page handler for profile
	register_page_handler('profile','sw_profile_page_handler');

	register_action('sticky_widgets/getWidgets',false,$CONFIG->pluginspath . "sticky_widgets/actions/getWidgets.php", true);
	register_action('sticky_widgets/reorder',false,$CONFIG->pluginspath . "sticky_widgets/actions/reorder.php", true);
	register_action('sticky_widgets/uninstall',false,$CONFIG->pluginspath . "sticky_widgets/actions/uninstall.php", true);
	register_action('sticky_widgets/save',false,$CONFIG->pluginspath . "sticky_widgets/actions/save.php", true);

	// Extend system CSS with our own styles, which are defined in the messageboard/css view
	extend_view('css','sticky_widgets/css');
	extend_view("metatags","sticky_widgets/js");
	register_plugin_hook('container_permissions_check','object','sticky_widgets_container_permission_check');
	register_plugin_hook('permissions_check','object','sticky_widgets_container_permission_check');

}

function sw_dashboard($page) {
	global $CONFIG;
	if(!@include_once($CONFIG->pluginspath . "sticky_widgets/dashboard/index.php")) {
		return false;
	}
	return true;
}


function sw_admin_pagesetup()
{
	if (get_context() == 'admin' && isadminloggedin()) {
		global $CONFIG;
		add_submenu_item(elgg_echo('sw:title'), $CONFIG->wwwroot . 'pg/sticky_widgets/admin/edit.php',"s");
		add_submenu_item(elgg_echo('sw:title:defaults'), $CONFIG->wwwroot . 'pg/sticky_widgets/admin/defaults.php',"s");
	}
}

/**
 * Profile page handler
 *
 * @param array $page Array of page elements, forwarded by the page handling mechanism
 */
function sw_profile_page_handler($page) {

	global $CONFIG;

	// The username should be the file we're getting
	if (isset($page[0])) {
		set_input('username',$page[0]);
	}
	// Include the standard profile index
	include($CONFIG->pluginspath . "sticky_widgets/profile/index.php");

}

function sw_page_handler($page)
{
	global $CONFIG;

	if(isset($page[0])) {
		switch($page[0]) {
			case 'admin':
				if(isset($page[1])) {
					switch($page[1]) {
						default:
							$p = implode("/", $page);
							include($CONFIG->pluginspath . "sticky_widgets/" . $p);
							break;
					}
				}
				break;
						case 'user' :
							break;
						default: include($CONFIG->pluginspath . 'sticky_widgets' . implode('/',$page));
		}
	}

}

register_elgg_event_handler('init','system','stickywidgets_init');
register_elgg_event_handler('pagesetup','system','sw_admin_pagesetup');

?>