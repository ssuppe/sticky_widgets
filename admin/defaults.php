<?php
/**
 * Sticky widgets defaults configuration values config page.
 *
 *
 * @package StickyWidgets
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Diego Andrés Ramírez Aragón <diego@somosmas.org>
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

$area2 = elgg_view_title(elgg_echo('sw:title:defaults'));
$area2 .= elgg_view('sticky_widgets/defaults');

echo page_draw(elgg_echo('sw:title'), elgg_view_layout("two_column_left_sidebar", '', $area2));

?>