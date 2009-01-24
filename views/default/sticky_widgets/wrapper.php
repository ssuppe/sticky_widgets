<?php

	/**
	 * Elgg sticky widget wrapper
	 *
	 * @package Elgg
	 * @subpackage StickyWidget
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Diego Andrés Ramírez Aragón <diego@somosmas.org>
	 */

	static $widgettypes;

	$callback = get_input('callback');

	if (!isset($widgettypes)) $widgettypes = sw_get_widget_types();

	if ($vars['entity'] instanceof ElggObject && $vars['entity']->getSubtype() == 'sticky_widget') {
		$handler = $vars['entity']->handler;
		$title = $widgettypes[$vars['entity']->handler]->name;
	} else {
		$handler = "error";
		$title = elgg_echo("error");
	}

	if ($callback != "true") {

?>

	<div id="widget<?php echo $vars['entity']->getGUID(); ?>">
	<div class="collapsable_box">
	<div class="collapsable_box_header">
	<?php if ($vars['entity']->canEdit()) { ?><a href="javascript:void(0);" class="toggle_box_edit_panel"><?php echo elgg_echo('edit'); ?></a><?php } ?>
	<h1><?php echo $title; ?></h1>
	</div>
	<?php

		if ($vars['entity']->canEdit()) {

	?>
	<div class="collapsable_box_editpanel"><?php

		echo elgg_view('sticky_widgets/editwrapper',
						array(
								'body' => elgg_view("widgets/{$handler}/edit",$vars),
								'entity' => $vars['entity']
							  )
					   );

	?></div><!-- /collapsable_box_editpanel -->
	<?php

		}

	?>
	<div class="collapsable_box_content">
		<?php

		echo "<div id=\"widgetcontent{$vars['entity']->getGUID()}\">";


	} else { // end if callback != "true"

//		echo elgg_view("widgets/{$handler}/view",$vars);

?>

<script language="javascript">
 $(document).ready(function(){
   	setup_avatar_menu();
 });

</script>


<?php

	}

	if ($callback != "true") {
		//echo elgg_view('ajax/loader');
		echo $handler->description;
		echo "</div>";

		?>
	</div><!-- /.collapsable_box_content -->
	</div><!-- /.collapsable_box -->
	</div>
<?php

	}

?>