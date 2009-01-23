<?php
		/**
		 * The save script for the sticky widgets.  This page will take the submitted
		 * sticky widgets and save them according to their new layout.
		 */
		
		$owner = 2;  		// Hard-coded to '2' (which I believe is always the first admin user)
		$context = get_input('context');
		$swType = get_input('swType');	// sticky Widget type (see types.php)
		$maincontent = get_input('debugField1');
		$sidebar = get_input('debugField2');
		$rightbar = get_input('debugField3');
		
		$result = reorder_sticky_widgets_from_panel($maincontent, $sidebar, $rightbar, $context, $owner, $swType);
		
		if ($result) {
			system_message(elgg_echo('widgets:panel:save:success'));
		} else {
			system_message(elgg_echo('widgets:panel:save:failure'));
		}
		
		forward($_SERVER['HTTP_REFERER']);

?>