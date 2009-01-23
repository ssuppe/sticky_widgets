<?php
/**
 * Sticky widgets defaults configuration page.
 *
 * Initializes the sticky widgets objects and give you an UI for specify default users values
 *
 * @package StickyWidgets
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Diego Andrés Ramírez Aragón <diego@somosmas.org>
 */

global $CONFIG;

$context = $vars["context"];
$widgets = $vars["widgets"];

$sticky_widgets = get_sticky_widgets(2,"default",$context);
if(!is_array($sticky_widgets)){
  $sticky_widgets = array();
}
$handlers = array_keys($widgets);
$existing = array();
foreach($sticky_widgets as $sticky){
  $handler = $sticky->get("handler");
  if(in_array($handler,$handlers)){
    $existing[]=$handler;
  }
}
$new_handlers = array_diff($handlers,$existing);

if(!empty($new_handlers)){
  foreach($new_handlers as $handler){
    add_sticky_widget(2,"default", $handler,$context);
  }
  $sticky_widgets4 = get_sticky_widgets(2,"default",$context,0);
  if(is_array($sticky_widgets4)){
    $sticky_widgets = array_merge($sticky_widgets,$sticky_widgets4);
  }
}

foreach($sticky_widgets as $widget){
  echo elgg_view_entity($widget);
}

?>
