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

?>
<div id="sticky-widgets-subtypes">

<ul>

<?php

foreach(getSWTypes() as $type) {

	?>
	<li><a href="#<?= $type ?>"><?php echo elgg_echo("sw:subtype:$type");?></a></li>
	<?php } ?>
</ul>

	<?php

	foreach(getSWTypes() as $type) {

		?>

<div id="<?= $type ?>">
<ul>

<?php

foreach(getSWContexts() as $context) {

	?>
	<li><a href="#<?= $type  ?>-<?= $context ?>"><?php echo elgg_echo("sw:context:$context");?></a></li>
	<?php } ?>
</ul>

	<?php

	foreach(getSWContexts() as $context) {

		?>
<div id="<?= $type  ?>-<?= $context ?>"><?php echo elgg_view("sticky_widgets/defaults_config",array("swType" => $type, "context"=>$context,"widgets"=>$widgets));?>
</div>
		<?php } ?></div>
</div>
		<?php } ?>

<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery("#sticky-widgets-subtypes > ul").tabs();
    <?php foreach(getSWTypes() as $type) { ?>
   		 jQuery("#<?= $type ?> > ul").tabs();
    <?php } ?>
    
    
});

</script>
