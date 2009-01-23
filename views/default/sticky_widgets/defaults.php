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
$widgets = sw_get_widget_types();

//@todo Add support for extra subtypes
?>

<div id="sticky-widgets-defaults">
<ul>
	<li><a href="#profile"><?php echo elgg_echo('profile');?></a></li>
	<li><a href="#dashboard"><?php echo elgg_echo('dashboard');?></a></li>
</ul>
<div id="profile"><?php echo elgg_view("sticky_widgets/defaults_config",array("context"=>"profile","widgets"=>$widgets));?>
</div>

<div id="dashboard"><?php echo elgg_view("sticky_widgets/defaults_config",array("context"=>"dashboard","widgets"=>$widgets));?>
</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery("#sticky-widgets-defaults > ul").tabs();
});

</script>