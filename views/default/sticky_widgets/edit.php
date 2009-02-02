<?php
/**
 * AJAXable edit panel taken from the profile edit panel for widgets.  The difference
 * is that this one is built for AJAX, and capable of handling multiple wheres (dashboard,
 * profile, or whatever else you want).
 *
 * Radio buttons are provided to change contexts (not elgg contexts, just contexts)
 * between the different wheres.  Care has been taken to ensure that when you switch,
 * no state is held from the last one, so you don't end up saving a 'hidden' field or anything.
 * (WYSIWYG).
 *
 */
global $CONFIG;
$widgettypes = get_widget_types();

$swTypes = getSWTypes();
$swWheres = getSWContexts();
$owner = page_owner_entity();

?>
<!--  <script type="text/javascript"
	src="<?php echo $vars['url']; ?>vendors/jquery/service/dragsndrops/jquery-ui-personalized-1.6rc5.min.js"></script>-->

<script type="text/javascript">
<!--
	var swType = '<?= $swTypes[0] ?>';
	var swWhere = '<?= $swWheres[0] ?>';
//-->
</script>
<div
	id="customise_editpanel" style="display: block">
<div class="customise_editpanel_instructions">
<h2><?php echo elgg_echo('widgets:add'); ?></h2>
<?php echo autop(elgg_echo('widgets:add:description')); ?></div>

<div id="customise_editpanel_rhs">
<h2><?php echo elgg_echo("widgets:gallery"); ?></h2>
<div id="widget_picker_gallery"><br />
<!-- bit of space at the bottom of the widget gallery --></div>
<!-- /#customise_editpanel_rhs --></div>
<!-- /#widget_picker_gallery -->

<div id="customise_page_view">
<h1><?= elgg_echo('sw:title:swtype')?></h1>
<form>
<span id="sticky_widgets_choices"> <?php

foreach($swTypes as $t) {
	?> <input type="radio" onClick="switchWidgets('<?= $t ?>', swWhere)"
	name="widget_type" value="<?= $t ?>"
	<?= $t == $swTypes[0] ? "checked" : "" ?> /><?= $t ?> <?php
}
?>
<h1><?= elgg_echo('sw:title:swwhere')?></h1>
<?php
foreach($swWheres as $w) {
	?> <input type="radio" onClick="switchWidgets(swType,'<?= $w ?>' )"
	name="widget_where" value="<?= $w ?>"
	<?= $w == $swWheres[0] ? "checked" : "" ?> /><?= $w ?> <?php
}
?></span>
</form>
<table cellspacing="0">
	<tr>
		<td colspan="2" align="left" valign="top"><?php
		if(get_context() == "profile"){
			?>
		<h2 class="profile_box"><?php echo elgg_echo("widgets:profilebox"); ?></h2>
		<div id="profile_box_widgets">
		<p><small><?php echo elgg_echo('widgets:position:fixed'); ?></small></p>
		</div>
		<?php
		}
		?></td>


		<td rowspan="2" align="left" valign="top">
		<h2><?php echo elgg_echo("widgets:rightcolumn"); ?></h2>
		<div id="rightcolumn_widgets"
		<?php if(get_context() == "profile")echo "class=\"long\""; ?>></div>
		</td>
		<!-- /rightcolumn td -->

	</tr>

	<tr>

		<td>
		<h2><?php echo elgg_echo("widgets:leftcolumn"); ?></h2>
		<div id="leftcolumn_widgets"></div>
		</td>

		<td>

		<h2><?php echo elgg_echo("widgets:middlecolumn"); ?></h2>
		<div id="middlecolumn_widgets"></div>
		</td>
	</tr>
</table>

</div>
<!-- /#customise_page_view -->

<form action="<?php echo $vars['url']; ?>action/sticky_widgets/reorder"
	method="post"><textarea type="textarea" value="Left widgets"
	style="display: none" name="debugField1" id="debugField1" /><?php echo $leftcolumn_widgets; ?></textarea>
<textarea type="textarea" value="Middle widgets" style="display: none"
	name="debugField2" id="debugField2" /><?php echo $middlecolumn_widgets; ?></textarea>
<textarea type="textarea" value="Right widgets" style="display: none"
	name="debugField3" id="debugField3" /><?php echo $rightcolumn_widgets; ?></textarea>

<input type="hidden" name="context"
	value="<?php echo 'sticky_widgets'; ?>" /> <input type="hidden"
	name="owner" value="<?php echo page_owner(); ?>" /> <span
	id="reorder_types"></span> <input type="submit"
	value="<?php echo elgg_echo('save'); ?>" class="submit_button"
	onclick="$('a.toggle_customise_edit_panel').click();" /> <input
	type="button" value="<?php echo elgg_echo('cancel'); ?>"
	class="cancel_button"
	onclick="$('a.toggle_customise_edit_panel').click();" /></form>
</div>

<script type="text/javascript">
<!--
// Document is ready, populate the edit panel with defaults
$(document).ready(function(){
	populateAll();
});

function populateWidgets(data) {
	$("#rightcolumn_widgets").html(data.right);
	$("#leftcolumn_widgets").html(data.left);
	$("#middlecolumn_widgets").html(data.middle);
	$("#widget_picker_gallery").html(data.gallery);

	//
	$(this).sortable( "refresh" );

 var widgetNamesLeft = outputWidgetList('#leftcolumn_widgets');
 var widgetNamesMiddle = outputWidgetList('#middlecolumn_widgets');
 var widgetNamesRight = outputWidgetList('#rightcolumn_widgets');

 document.getElementById('debugField1').value = widgetNamesLeft;
 document.getElementById('debugField2').value = widgetNamesMiddle;
 document.getElementById('debugField3').value = widgetNamesRight;

	// Stolen from initialise_elgg.php to re-init the draggable/droppable behaviors.
	// WIDGET GALLERY
	// sortable widgets
	var els = ['#leftcolumn_widgets', '#middlecolumn_widgets', '#rightcolumn_widgets', '#widget_picker_gallery' ];
	var $els = $(els.toString());

	$els.sortable({
		items: '.draggable_widget',
		handle: '.drag_handle',
		cursor: 'move',
		revert: true,
		opacity: 1.0,
		appendTo: 'body',
		placeholder: 'placeholder',
		connectWith: els,
		start:function(e,ui) {
			// prevent droppable drop function from running when re-sorting main lists
			//$('#middlecolumn_widgets').droppable("disable");
			//$('#leftcolumn_widgets').droppable("disable");
		},
		stop: function(e,ui) {
			// refresh list before updating hidden fields with new widget order
			$(this).sortable( "refresh" );

			var widgetNamesLeft = outputWidgetList('#leftcolumn_widgets');
			var widgetNamesMiddle = outputWidgetList('#middlecolumn_widgets');
			var widgetNamesRight = outputWidgetList('#rightcolumn_widgets');

			document.getElementById('debugField1').value = widgetNamesLeft;
			document.getElementById('debugField2').value = widgetNamesMiddle;
			document.getElementById('debugField3').value = widgetNamesRight;

		}
	});

	// setup hover class for dragged widgets
	$("#rightcolumn_widgets").droppable({
		accept: ".draggable_widget",
		hoverClass: 'droppable-hover'
	});
	$("#middlecolumn_widgets").droppable({
		accept: ".draggable_widget",
		hoverClass: 'droppable-hover'
	});
	$("#leftcolumn_widgets").droppable({
		accept: ".draggable_widget",
		hoverClass: 'droppable-hover'
	});

}

// Get the JSON for the columns/gallery based on the current where
function populateAll() {
$.getJSON("<?= $vars['url'] ?>action/sticky_widgets/getWidgets",
			{ 'swType' : swType, 'context' : swWhere}, populateWidgets);
			$('#reorder_types').html('<input type="hidden" name="context" value="' + swWhere + '"/>' +
							 '<input type="hidden" name="swType" value="' + swType + '"/>');
}

// Switch the widgets, reAJAX everything
function switchWidgets(type, where) {
	swType = type;
	swWhere = where;
	populateAll();
}
  //-->
</script>
<div id="uninstalldiv">
<?php
$submit = elgg_view('input/submit', array('value' => 'Uninstall Sticky Widgets'));
$form = elgg_view('input/form', array('action' => "{$vars['url']}actions/sticky_widgets/uninstall", "body" => $submit));
echo $form;
?>
</div>
<!-- /customise_editpanel -->
