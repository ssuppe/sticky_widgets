<?php

/**
 * This view borrows heavily from the widget editor for the profile in the
 * 'stock' elgg distribution.  It's encapsulated within this module because
 * it's been edited pretty extensively for AJAX purposed.  This page
 * is responsible for rendering the edit phase of sticky_widgets
 */

// Load Elgg engine
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");

admin_gatekeeper();

set_context('admin');
// Set admin user for user block
set_page_owner($_SESSION['guid']);

// Get the current page's owner
		$page_owner = page_owner_entity();
		if ($page_owner === false || is_null($page_owner)) {
			$page_owner = $_SESSION['user'];
			set_page_owner($_SESSION['guid']);
		}

$area2 = elgg_view_title(elgg_echo('sw:title'));
$widgets = get_sticky_widgets(2,"default","profile");
if(empty($widgets)){
  $area2.= sprintf(elgg_echo("sw:widgets:noconfig"),$CONFIG->url."pg/sticky_widgets/admin/defaults.php");
}
else{
  $area2 .= elgg_view('sticky_widgets/edit');
}

echo page_draw(elgg_echo('sw:title'), elgg_view_layout("two_column_left_sidebar", '', $area2));

?>