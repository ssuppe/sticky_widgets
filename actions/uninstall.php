<?php
	/**
	 * 
	 *
	 * @package Elgg
	 * @subpackage StickyWidget
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Steve Suppe <ssuppe.elgg@gmail.com>
	 */
// Run this before deleting from the mod directory.
delete_entities("object", "sticky_widget");
delete_entities("object", "sw_master_timestamp");
delete_entities("object", "sw_timestamp");
?>