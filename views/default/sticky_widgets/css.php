<?php

	/**
	 * Elgg sticky_widgets CSS extender
	 *
	 */

?>
/*-------------------------------
STICKY_WIDGETS PLUGIN
-------------------------------*/
#widget_picker_gallery {
	border-top:1px solid #cccccc;
	background:white;
	width:210px;
	height:auto;
	padding:10px;
	overflow:scroll;
	overflow-x:hidden;
}

/*********************** JQuery UI Tabs **************************/

/* Caution! Ensure accessibility in print and other media types... */
@media projection, screen { /* Use class for showing/hiding tab content, so that visibility can be better controlled in different media types... */
    .ui-tabs-group-hide {
        display: none !important;
    }
}

/* Hide useless elements in print layouts... */
@media print {
    .ui-tabs-group-nav {
        display: none;
    }
}

/* Skin */
.ui-tabs-group-nav, .ui-tabs-group-panel {
    font-family: "Trebuchet MS", Trebuchet, Verdana, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.ui-tabs-group-nav {
    list-style: none;
    margin: 0;
    padding: 0 0 0 3px;
}
.ui-tabs-group-nav:after { /* clearing without presentational markup, IE gets extra treatment */
    display: block;
    clear: both;
    content: " ";
}
.ui-tabs-group-nav li {
    float: left;
    margin: 0 0 0 2px;
    font-weight: bold;
}
.ui-tabs-group-nav a, .ui-tabs-group-nav a span {
    float: left; /* fixes dir=ltr problem and other quirks IE */
    padding: 0 12px 5px;
    background: url(<?php echo $vars['url']; ?>pg/statistics_etl/tabsimage) no-repeat;
    background-color: #c0c0c0;
}
.ui-tabs-group-nav a {
    margin: 5px 0 0; /* position: relative makes opacity fail for disabled tab in IE */
    background-position: 100% 0;
    text-decoration: none;
    white-space: nowrap; /* @ IE 6 */
    outline: 0; /* @ Firefox, prevent dotted border after click */
}
.ui-tabs-group-nav a:link, .ui-tabs-group-nav a:visited {
    color: #0054A7;
}
.ui-tabs-group-nav .ui-tabs-group-selected a {
    position: relative;
    top: 1px;
    z-index: 2;
    /*margin-top: 0;*/
    background-position: 100% -23px;
    color: #fff;
}
.ui-tabs-group-nav a span {
    padding-top: 1px;
    padding-right: 0;
    height: 20px;
    background-position: 0 0;
    line-height: 20px;
}
.ui-tabs-group-nav .ui-tabs-group-selected a span {
    padding-top: 0;
    height: 27px;
    background-position: 0 -23px;
    line-height: 27px;
}
.ui-tabs-group-nav .ui-tabs-group-selected a:link, .ui-tabs-group-nav .ui-tabs-group-selected a:visited,
.ui-tabs-group-nav .ui-tabs-group-disabled a:link, .ui-tabs-group-nav .ui-tabs-group-disabled a:visited { /* @ Opera, use pseudo classes otherwise it confuses cursor... */
    cursor: text;
}
.ui-tabs-group-nav a:hover, .ui-tabs-group-nav a:focus, .ui-tabs-group-nav a:active,
.ui-tabs-group-nav .ui-tabs-group-unselect a:hover, .ui-tabs-group-nav .ui-tabs-group-unselect a:focus, .ui-tabs-group-nav .ui-tabs-group-unselect a:active { /* @ Opera, we need to be explicit again here now... */
    cursor: pointer;
}
.ui-tabs-group-disabled {
    opacity: .4;
    filter: alpha(opacity=40);
}
.ui-tabs-group-nav .ui-tabs-group-disabled a:link, .ui-tabs-group-nav .ui-tabs-group-disabled a:visited {
    color: #000;
}
.ui-tabs-group-panel {
    border-top: 1px solid #c0c0c0;
    padding: 10px;
    background: #fff; /* declare background color for container to avoid distorted fonts in IE while fading */
}
/*.ui-tabs-group-loading em {
    padding: 0 0 0 20px;
    background: url(loading.gif) no-repeat 0 50%;
}*/

/* Additional IE specific bug fixes... */
* html .ui-tabs-group-nav { /* auto clear @ IE 6 & IE 7 Quirks Mode */
    display: inline-block;
}
*:first-child+html .ui-tabs-group-nav  { /* auto clear @ IE 7 Standards Mode - do not group selectors, otherwise IE 6 will ignore complete rule (because of the unknown + combinator)... */
    display: inline-block;
}

