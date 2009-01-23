<?php

/**
 * Get Widgets is the AJAXable action for getting the content that belongs in the
 * sticky_widgets customization page.  Returns left, middle, right columns, as well 
 * as the gallery of widgets not yet used in a nice, easy to digest JSON format.
 */

admin_gatekeeper();
$type = get_input('swType');  // sticky Widget type (see types.php)
$context = get_input('context');
//$where = get_input('swWhere');// sticky Widget where (see types.php)
$widgettypes = sw_get_widget_types($context);

// These 'getters' have hardcoded contexts because I maintain that for uniqueness
$area1widgets = get_sticky_widgets(2,$type, $context,1);
$area2widgets = get_sticky_widgets(2,$type, $context,2);
$area3widgets = get_sticky_widgets(2,$type, $context,3);

if (empty($area1widgets) && empty($area2widgets) && empty($area3widgets)) {

	if (isset($vars['area3'])) $vars['area1'] = $vars['area3'];
	if (isset($vars['area4'])) $vars['area2'] = $vars['area4'];

}
$seenWidgets = array();  // Keep track of the widgets we've seen, so that we don't show them in the gallery.
$left = drawSetDraggables($area1widgets, $widgettypes, $seenWidgets);
$middle = drawSetDraggables($area2widgets, $widgettypes, $seenWidgets);
$right = drawSetDraggables($area3widgets, $widgettypes, $seenWidgets);
$gallery = drawGallery($widgettypes, $seenWidgets);

// Output
echo json_encode(array('left' => $left, 'right' => $right, 'middle' => $middle, 'gallery' => $gallery));

/**
 * For a given column in the set gui, the draggables need to be deawn
 *
 * @param string $areaWidgets Which widgets to draw
 * @param string $widgettypes Master list of widgets
 * @param string $seen Array of Widgets we've seen thus far
 * @return string HTML to put in a particular DIV tag (leftcolumn, right column, etc)
 */
function drawSetDraggables($areaWidgets, $widgettypes, &$seen) {
	global $CONFIG;
	$left = "";
	$leftcolumn_widgets = "";
	if (is_array($areaWidgets) && sizeof($areaWidgets) > 0) {
		foreach($areaWidgets as $widget) {
			if (!empty($leftcolumn_widgets)) {
				$leftcolumn_widgets .= "::";
			}
			$leftcolumn_widgets .= "{$widget->handler}::{$widget->getGUID()}";

			$seen[] = $widgettypes[$widget->handler]->name;
			$left .= '<table class="draggable_widget" cellspacing="0">';
			$left .= '<tr>';
			$left .= '<td width="149px">';
			$left .= '<h3>' . $widgettypes[$widget->handler]->name;
			$left .= '<input type="hidden" name="handler"	value="' . $widget->handler . '" />';
			$left .= '<input type="hidden" name="multiple" value="' . $widgettypes[$widget->handler]->multiple . '" />';
			$left .= '<input type="hidden" name="side" value="' . in_array('side',$widgettypes[$widget->handler]->positions) . '"/>';
			$left .= '<input type="hidden" name="main" value="' . in_array('main',$widgettypes[$widget->handler]->positions) . '" />';
			$left .= '<input type="hidden" name="description" value="' . htmlentities($widgettypes[$widget->handler]->description) . '" />';
			$left .= '<input type="hidden" name="guid" value="' . $widget->getGUID() . '" /></h3>';
			$left .= '</td>';
			$left .= '<td width="17px" align="right"></td>';
			$left .= '<td width="17px" align="right"><a href="#"><img';
			$left .= ' src="' . $CONFIG->url . '/_graphics/spacer.gif" width="14px"';
			$left .= 'height="14px" class="more_info" /></a></td>';
			$left .= '<td width="17px" align="right"><a href="#"><img';
			$left .= ' src="' . $CONFIG->url . '/_graphics/spacer.gif" width="15px"';
			$left .= 'height="15px" class="drag_handle" /></a></td>';
			$left .= '</tr>';
			$left .= '</table>';
		}
	}
	return $left;
}

/**
 * The gallery is the 'list' of widgets we have not yet added to the profile/
 * dashboard.  Pass $seen along if you want to filter for those widgets that
 * are already being used
 *
 * @param array $widgettypes
 * @param array $seen
 * @return string html for the gallery
 */
function drawGallery($widgettypes, $seen) {
	global $CONFIG;
	$gallery = "";
	foreach($widgettypes as $handler => $widget) {
		if(!in_array($widget->name, $seen)) {
			$gallery .= '<table class="draggable_widget" cellspacing="0"><tr><td>';
			$gallery .= '<h3>';
			$gallery .= $widget->name;
			$gallery .= '<input type="hidden" name="multiple" value="';
				
			if ((isset($widget->handler)) && (isset($widgettypes[$widget->handler]->multiple))){
				$gallery .= $widgettypes[$widget->handler]->multiple;
			}
			$gallery .= '"/>';
			$gallery .= '<input type="hidden" name="side" value="';
			if ((isset($widget->handler)) && (isset($widgettypes[$widget->handler]))
			&& (is_array($widgettypes[$widget->handler]->positions))) {
				$gallery .= in_array('side',$widgettypes[$widget->handler]->positions);
			}
			$gallery .= '" />';
			$gallery .= '<input type="hidden" name="main" value="';
			if ((isset($widget->handler)) && (isset($widgettypes[$widget->handler]))
			&& (is_array($widgettypes[$widget->handler]->positions))) {
				$gallery .= in_array('main',$widgettypes[$widget->handler]->positions);
			}
			$gallery .= '" />';
			$gallery .= '<input type="hidden" name="handler" value="' . htmlentities($handler) . '" />';
			$gallery .= '<input type="hidden" name="description" value="' . htmlentities($widget->description, null, 'UTF-8') . '" />';
			$gallery .= '<input type="hidden" name="guid" value="0" />';
			$gallery .= '</h3>';
			$gallery .= '</td>';
			$gallery .= '<td width="17px" align="right"></td>';
			$gallery .= '<td width="17px" align="right"><a href="#"><img src="' . $CONFIG->url . '/_graphics/spacer.gif" width="14px" height="14px" class="more_info" /></a></td>';
			$gallery .= '<td width="17px" align="right"><a href="#"><img src="' . $CONFIG->url . '/_graphics/spacer.gif" width="15px" height="15px" class="drag_handle" />';
			$gallery .= '</a>';
			$gallery .= '</td>';
			$gallery .= '</tr>';
			$gallery .= '</table>';

		}
	}


//	$gallery .= '<br />';
	$gallery .= '<!-- bit of space at the bottom of the widget gallery -->';

//	$gallery .= '</div>';
//	$gallery .= '<!-- /#customise_editpanel_rhs -->';
	return $gallery;
} ?>
