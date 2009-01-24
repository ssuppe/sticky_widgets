<?php
	/**
	 * 	 * Elgg sticky widgets widget save action
	 * 
	 *
	 * @package Elgg
	 * @subpackage StickyWidget
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Steve Suppe <ssuppe.elgg@gmail.com>
	 */

	/**
	 *
	 * @package Elgg
	 * @subpackage StickyWidgets
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Diego Andrés Ramírez Aragón <diego@somosmas.org>
	 */

		action_gatekeeper();

		$guid = get_input('guid');
		$params = $_REQUEST['params'];
		$pageurl = get_input('pageurl');
		$noforward = get_input('noforward',false);
		$context = get_input("context","profile");

		$result = false;

		if (!empty($guid)) {

			$result = save_sticky_widget_info($guid,$params,$context);

		}

		if ($result) {
			system_message(elgg_echo('widgets:save:success'));
		} else {
			register_error(elgg_echo('widgets:save:failure'));
		}

		if (!$noforward)
			forward($_SERVER['HTTP_REFERER']);

?>