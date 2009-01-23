<?php

/**
 * All of the functions here were 'borrowed' from the Elgg site and customized
 * to work for sticky_widgets.  Subsequently, all function calls to the originals
 * in the actions/views have been replaced with these.
 */

/**
 * Reorder sticky widgets (save widgets) according to the column they are in and
 * who they are intended for (swType).
 *
 * @param unknown_type $panelstring1
 * @param unknown_type $panelstring2
 * @param unknown_type $panelstring3
 * @param unknown_type $context
 * @param unknown_type $owner
 * @param unknown_type $swType
 * @param unknown_type $swWhere
 * @return unknown
 */
function reorder_sticky_widgets_from_panel($panelstring1, $panelstring2, $panelstring3, $context, $owner = 2, $swType) {

	$return = true;

	$mainwidgets = explode('::',$panelstring1);
	$sidewidgets = explode('::',$panelstring2);
	$rightwidgets = explode('::',$panelstring3);

	$handlers = array();
	$guids = array();

	// First, lookup our 'timestamp' for sticky_widgets and check
	// to see if the user's timestamp is equal to or later.

	if (is_array($mainwidgets) && sizeof($mainwidgets) > 0) {
		foreach($mainwidgets as $widget) {

			$guid = (int) $widget;

			if ("{$guid}" == "{$widget}") {
				$guids[1][] = $widget;
			} else {
				$handlers[1][] = $widget;
			}

		}
	}
	if (is_array($sidewidgets) && sizeof($sidewidgets) > 0) {
		foreach($sidewidgets as $widget) {

			$guid = (int) $widget;

			if ("{$guid}" == "{$widget}") {
				$guids[2][] = $widget;
			} else {
				$handlers[2][] = $widget;
			}

		}
	}
	if (is_array($rightwidgets) && sizeof($rightwidgets) > 0) {
		foreach($rightwidgets as $widget) {

			$guid = (int) $widget;

			if ("{$guid}" == "{$widget}") {
				$guids[3][] = $widget;
			} else {
				$handlers[3][] = $widget;
			}

		}
	}

	// Reorder existing widgets or delete ones that have vanished
	foreach (array(1,2,3) as $column) {
		if ($dbwidgets = get_sticky_widgets($owner, $swType, $context,$column)) {

			foreach($dbwidgets as $dbwidget) {
				if (in_array($dbwidget->getGUID(),$guids[1]) || in_array($dbwidget->getGUID(),$guids[2]) || in_array($dbwidget->getGUID(),$guids[3])) {
					if (in_array($dbwidget->getGUID(),$guids[1])) {
						$pos = array_search($dbwidget->getGUID(),$guids[1]);
						$col = 1;
					} else if (in_array($dbwidget->getGUID(),$guids[2])) {
						$pos = array_search($dbwidget->getGUID(),$guids[2]);
						$col = 2;
					} else {
						$pos = array_search($dbwidget->getGUID(),$guids[3]);
						$col = 3;
					}
					$pos = ($pos + 1) * 10;
					$dbwidget->column = $col;
					$dbwidget->order = $pos;
				} else {
					$dbguid = $dbwidget->getGUID();
					if (!$dbwidget->delete()) {
						$return = false;
					} else {
						// Remove state cookie
						setcookie('widget' + $dbguid, null);
					}
				}
			}

		}
		// Add new ones
		if (sizeof($guids[$column]) > 0) {
			foreach($guids[$column] as $key => $guid) {
				if ($guid == 0) {
					$pos = ($key + 1) * 10;
					$handler = $handlers[$column][$key];
					if (!add_sticky_widget($owner,$swType, $handler,$context,$pos,$column))
					$return = false;
				}
			}
		}
	}
	setSWMasterTimestamp($context);
	return $return;

}

/**
 * Create the user widgets based on the sticky widgets in the DB.  Only happens
 * a) if StickyWidgets has been used for this context and b) if the SWs were updated
 * since the user has refreshed this context in their browser.
 *
 * TODO: Shouldn't just be context checking, should also be user subtype.
 *
 * @param unknown_type $user_guid
 * @param unknown_type $swType
 * @param unknown_type $context
 * @return unknown
 */
function get_user_widgets_from_sticky($user_guid, $swType, $context) {
	// First, let's do a compare of the last updated 'sticky_widgets'
	// to this user's SW Timestamp...
	// The ordering here is on purpose, because for uninitialized states,
	// you would want the master first
	$uTime = getSWTimestampForUser($user_guid, $context);

	$mTime = getSWMasterTimestamp($context);
	//	if($mTime < 0) {
	//		setSWMasterTimestamp($context);
	//		$mTime = getSWMasterTimestamp($context);
	//	}
	// If there was no mTime, then there has been no save, so continue to
	// use the default widgets;
	if($mTime > 0 && $uTime <= $mTime) {

		$widgetspanel = array();
		foreach (array(1,2,3) as $column) {
			// First, get the STICKY widgets for this column/context
			$swidgets = get_sticky_widgets(2,$swType,$context,$column);
			// Create the necessary string to pass to the reorder panel
			$w = "";
			$i = 0;
			foreach($swidgets as $sw) {
				//			$sw = $swidgets[$i];
				$w = $w . $sw->handler . "::" . 0;
				if($i != (sizeof($swidgets) -1)) {
					$w .= "::";
					$i++;
				}
			}
			$widgetspanel[] = $w;
		}
		$ctx = get_context();
		set_context('add_sticky_widgets');
		if(!reorder_widgets_from_panel_via_sticky($widgetspanel[0], $widgetspanel[1], $widgetspanel[2], $context, $user_guid)) {
			system_message(elgg_echo('sw:usercheck:fail'));
		}
		set_context($ctx);
		setSWTimestampForUser($user_guid, $context);
	}
	$widgets[0] = get_widgets($user_guid, $context,1);
	$widgets[1] = get_widgets($user_guid, $context,2);
	$widgets[2] = get_widgets($user_guid, $context,3);
	return $widgets;
}

/**
 * Get widgets for a particular context and column, in order of display
 *
 * @param int $user_guid The owner user GUID
 * @param string $context The context (profile, dashboard etc)
 * @param int $column The column (1 or 2)
 * @return array|false An array of widget ElggObjects, or false
 */
function get_sticky_widgets($user_guid, $swType,$context, $column) {

	if ($widgets = get_entities_from_private_setting_multi(array(
	 'column' => $column,
	 'context' => $context,
	'swType' => $swType), "object", "sticky_widget", $user_guid, "", 10000)) 
	/* if ($widgets = get_user_objects_by_metadata($user_guid, "widget", array(
	 'column' => $column,
	 'context' => 'admin',
	 'swType' => $swType
	 ), 10000))*/

	{

		$widgetorder = array();
		foreach($widgets as $widget) {
			$order = $widget->order;
			while(isset($widgetorder[$order])) {
				$order++;
			}
			$widgetorder[$order] = $widget;
		}

		ksort($widgetorder);

		return $widgetorder;

	}

	return false;

}


/**
 * Add a new widget
 *
 * @param int $user_guid User GUID to associate this widget with
 * @param string $handler The handler for this widget
 * @param string $context The page context for this widget
 * @param int $order The order to display this widget in
 * @param int $column The column to display this widget in (1, 2 or 3)
 * @return true|false Depending on success
 */
function add_sticky_widget($user_guid, $swType, $handler, $context, $order = 0, $column = 1) {

	if (empty($user_guid) || empty($context) || empty($handler) || !widget_type_exists($handler))
	return false;

	if ($user = get_user($user_guid)) {

		$widget = new StickyElggWidget;
		$widget->owner_guid = $user_guid;
		$widget->container_guid = $user_guid;
		$widget->access_id = 1;
		$ctx = get_context();
		set_context('add_sticky_widgets');
		if (!$widget->save()){
			set_context($ctx);
			return false;
		}
		set_context($ctx);

		$widget->handler = $handler;
		$widget->context = $context;
		$widget->column = $column;
		$widget->order = $order;
		$widget->swType = $swType;
		//		$widget->swWhere = $swWhere;
		// save_widget_location($widget, $order, $column);
		return true;

	}

	return false;

}

/**
 * Displays an internal layout for the use of a plugin canvas.
 * Takes a variable number of parameters, which are made available
 * in the views as $vars['area1'] .. $vars['areaN'].
 *
 * @param string $layout The name of the views in canvas/layouts/.
 * @return string The layout
 */
function sw_elgg_view_layout($layout) {

	$arg = 1;
	$param_array = array();
	while ($arg < func_num_args()-2) {
		$param_array['area' . $arg] = func_get_arg($arg);
		$arg++;
	}
	$param_array['swType'] = func_get_arg($arg++);
	//	$param_array['swWhere'] = func_get_arg($arg);
	if (elgg_view_exists("canvas/layouts/{$layout}")) {
		return elgg_view("canvas/layouts/{$layout}",$param_array);
	} else {
		return elgg_view("canvas/default",$param_array);
	}

}

/* Returns an array of stdClass objects representing the defined widget types
 *
 * @param $widget_context 'dashboard' or 'profile' really.  Some widgets are
 * for one, some are for another, or all.  Specify it
 * explicitly here.  If not specified, then all widgets will work
 * for all contexts.
 * @return array A list of types defined (if any)
 */
function sw_get_widget_types($widget_context = 'ignore') {

	global $CONFIG;
	if (!empty($CONFIG->widgets)
	&& !empty($CONFIG->widgets->handlers)
	&& is_array($CONFIG->widgets->handlers)) {
			
		//					$context = get_context();
			
		foreach($CONFIG->widgets->handlers as $key => $handler) {
			if (!in_array('all',$handler->context) &&
			!in_array($widget_context,$handler->context) &&
			sw_shouldIgnore() != true) {
				unset($CONFIG->widgets->handlers[$key]);
			}
		}
			
		return $CONFIG->widgets->handlers;
			
	}

	return array();

}

function sw_shouldIgnore() {
	return true;
}

/**
 * Add a new widget that is being cloned via StickyWidgets.
 *
 * @param int $user_guid User GUID to associate this widget with
 * @param string $handler The handler for this widget
 * @param string $context The page context for this widget
 * @param int $order The order to display this widget in
 * @param int $column The column to display this widget in (1, 2 or 3)
 * @return true|false Depending on success
 */
function add_widget_via_sticky($user_guid, $handler, $context, $order = 0, $column = 1) {

	if (empty($user_guid) || empty($context) || empty($handler) || !widget_type_exists($handler))
	return false;

	if ($user = get_user($user_guid)) {

		$widget = new ElggWidget;
		$widget->owner_guid = $user_guid;
		$widget->access_id = 1;
		$widget->container_guid = $user_guid;
		$ctx = get_context();
		set_context('add_sticky_widgets');
		if (!$widget->save()){
			set_context($ctx);
			return false;
		}
		set_context($ctx);

		$widget->handler = $handler;
		$widget->context = $context;
		$widget->column = $column;
		$widget->order = $order;

		// save_widget_location($widget, $order, $column);
		return true;

	}

	return false;

}

/**
 * Reorder 'normal' (user) widgets BASED on sticky widgets
 *
 * @param unknown_type $panelstring1
 * @param unknown_type $panelstring2
 * @param unknown_type $panelstring3
 * @param unknown_type $context
 * @param unknown_type $owner
 * @return unknown
 */
function reorder_widgets_from_panel_via_sticky($panelstring1, $panelstring2, $panelstring3, $context, $owner) {

	$return = true;

	$mainwidgets = explode('::',$panelstring1);
	$sidewidgets = explode('::',$panelstring2);
	$rightwidgets = explode('::',$panelstring3);

	$handlers = array();
	$guids = array();

	if (is_array($mainwidgets) && sizeof($mainwidgets) > 0) {
		foreach($mainwidgets as $widget) {

			$guid = (int) $widget;

			if ("{$guid}" == "{$widget}") {
				$guids[1][] = $widget;
			} else {
				$handlers[1][] = $widget;
			}

		}
	}
	if (is_array($sidewidgets) && sizeof($sidewidgets) > 0) {
		foreach($sidewidgets as $widget) {

			$guid = (int) $widget;

			if ("{$guid}" == "{$widget}") {
				$guids[2][] = $widget;
			} else {
				$handlers[2][] = $widget;
			}

		}
	}
	if (is_array($rightwidgets) && sizeof($rightwidgets) > 0) {
		foreach($rightwidgets as $widget) {

			$guid = (int) $widget;

			if ("{$guid}" == "{$widget}") {
				$guids[3][] = $widget;
			} else {
				$handlers[3][] = $widget;
			}

		}
	}

	// Reorder existing widgets or delete ones that have vanished
	foreach (array(1,2,3) as $column) {
		if ($dbwidgets = get_widgets($owner,$context,$column)) {

			foreach($dbwidgets as $dbwidget) {
				if (in_array($dbwidget->getGUID(),$guids[1]) || in_array($dbwidget->getGUID(),$guids[2]) || in_array($dbwidget->getGUID(),$guids[3])) {
					if (in_array($dbwidget->getGUID(),$guids[1])) {
						$pos = array_search($dbwidget->getGUID(),$guids[1]);
						$col = 1;
					} else if (in_array($dbwidget->getGUID(),$guids[2])) {
						$pos = array_search($dbwidget->getGUID(),$guids[2]);
						$col = 2;
					} else {
						$pos = array_search($dbwidget->getGUID(),$guids[3]);
						$col = 3;
					}
					$pos = ($pos + 1) * 10;
					$dbwidget->column = $col;
					$dbwidget->order = $pos;
				} else {
					$dbguid = $dbwidget->getGUID();
					if (!$dbwidget->delete()) {
						//						$return = false;
					} else {
						// Remove state cookie
						setcookie('widget' + $dbquid, null);
					}
				}
			}

		}
		// Add new ones
		if (sizeof($guids[$column]) > 0) {
			foreach($guids[$column] as $key => $guid) {
				if ($guid == 0) {
					$pos = ($key + 1) * 10;
					$handler = $handlers[$column][$key];
					if (!add_widget_via_sticky($owner,$handler,$context,$pos,$column))
					$return = false;
				}
			}
		}
	}

	return $return;

}

/**
 * My subclass o widget for Sticky Widgets.  Makes grabbing
 * objects from the DB more clear. (Plus gives me all of the
 * functionality of regular widgets without being them - through
 * inheritance).
 *
 */
class StickyElggWidget extends ElggWidget  {
	protected function initialise_attributes() {
		parent::initialise_attributes();
		$this->attributes['subtype'] = 'sticky_widget';
	}
}

/**
 * Looks up (or creates if it doesn't exist), the SWTimestamp object for the
 * last save of sticky widgets.
 *
 * @return int of the current time
 */
function getSWMasterTimestamp($context) {
	$timestamps = get_entities("object", "sw_master_timestamp", 2);
	$timestamp = "";
	foreach($timestamps as $t) {
		if($t->description == $context) {
			$timestamp = $t;
			break;
		}
	}
	if(empty($timestamp)) {
		return -1;
	} else {
		return $timestamp->time_updated;
	}
}

/**
 * Set the Master Timestamp for this context to now.
 *
 * @param unknown_type $context
 */
function setSWMasterTimestamp($context) {
	$timestamps = get_entities("object", "sw_master_timestamp", 2);
	$timestamp = "";
	foreach($timestamps as $t) {
		if($t->description == $context) {
			$timestamp = $t;
			break;
		}
	}

	if(empty($timestamp)) {
		$timestamp = new ElggObject();
		$timestamp->owner_guid = 2;
		$timestamp->container_guid = 2;
		$timestamp->subtype = 'sw_master_timestamp';
		$timestamp->access_id = 1;
		$timestamp->title = "SW Master Timestamp";
		$timestamp->description = $context;
		//		$timestamp->save();
	}
	$ctx = get_context();
	set_context('add_sticky_widgets');
	$timestamp->save();
	set_context($ctx);

	//	return $timestamp->time_updated;
}

/**
 * Get the last-checked value for sticky widgets in this context.
 *
 * @param unknown_type $guid
 * @param unknown_type $context
 * @return unknown
 */
function getSWTimestampForUser($guid, $context) {
	$timestamps = get_entities("object", "sw_timestamp", $guid);
	$timestamp = "";
	foreach($timestamps as $t) {
		if($t->description == $context) {
			$timestamp = $t;
			break;
		}
	}

	if(empty($timestamp)) {
		// Guaranteed to alwys be less than the current time
		return -1;
	}  else {
		return $timestamp->time_updated;
	}
}

/**
 * Set the last-checked value for this context to npw.
 *
 * @param unknown_type $guid
 * @param unknown_type $context
 * @return unknown
 */
function setSWTimestampForUser($guid, $context) {
	$timestamps = get_entities("object", "sw_timestamp", $guid);
	$timestamp = "";
	foreach($timestamps as $t) {
		if($t->description == $context) {
			$timestamp = $t;
			break;
		}
	}

	if(empty($timestamp)) {
		$timestamp = new ElggObject();
		$timestamp->owner_guid = $guid;
		$timestamp->container_guid = $guid;
		$timestamp->subtype = 'sw_timestamp';
		$timestamp->access_id = 1;
		$timestamp->title = "SW Timestamp";
		$timestamp->description = $context;

	}
	$ctx = get_context();
	set_context('add_sticky_widgets');
	$timestamp->save();
	set_context($ctx);

	//	$timestamp->description = time();
	return $timestamp->time_updated;
}

/**
 * Extend container permissions checking to extend can_write_to_container for write users.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 */
function sticky_widgets_container_permission_check($hook, $entity_type, $returnvalue, $params) {
	if(get_context() == 'add_sticky_widgets') {
		return true;
	}
	return false;
}

?>