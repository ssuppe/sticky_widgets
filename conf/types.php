<?php

/**
 * types.php
 *
 * Manages the different user 'wheres' (dashboard, profile).
 *
 * One might look upon this class as bad programming practice, and I suppose
 * that might be.  However, I was reluctant to add this as an object in the
 * database because it's simple enough here, and quite frankly I don't
 * want to add YADBC (yet another database call) - it seems quite heavy
 * for such a simple thing.
 */

/**
 * A list of the potential 'wheres' to set up different widgets for.
 * Every 'where' can be edited within the Admin Control Panel.  Leave
 * 'dashboard' and 'profile' where they are, or things will go
 * awry.  (Append only).
 * 
 * These should be the same as contexts.
 *
 * @return array
 */

function getSWContexts() {
	return array(
		"dashboard",
		"profile"
		);
}

/**
 * A list of the different user subtypes available.  Default is for NO subtype.
 * These should match those given in the case statement in function getSWidgetSet
 * below.
 *
 * @return unknown
 */
function getSWTypes() {
	return array(elgg_echo('TYPE_ORG'), elgg_echo('TYPE_USER'));}

/**
 * The most user-unfriendly bit of this code (I think).  If you have employed
 * user subtypes, this is the field name you are storing the subtype in.
 *
 * If you're doing it the Elgg way, then this is just 'subType'.  If you're using
 * a metadata field, put that field name here.
 *
 * If you are NOT using user subtypes (the default), leave this as default.
 *
 * Put another way, these are the KEYS to the VALUES placed in the function
 * getStickyWidgetSet.
 *
 * @return unknown
 */
function getSWSubtype() {
	return 'serviceType';
}

/**
 * Goes hand in hand with getSWSubtype().  This function handles user subtypes.
 * Right now, it's set for NO subtypes.  If you had one, you would add a case
 * for the subtype VALUE, and the name of the Sticky Widget Set to return.  (A SWS
 * is defined as the set of default widgets for this VALUE.
 *
 * It may seem a bit convoluted, but this allows you the option of either having
 * a different set of Sticky widgets per user subtype, or any combination of
 * sharing.
 *
 * For instance, if you wanted subtypes 'wholesale_buyer' and 'wholesale_seller' to have the same
 * set, you would have them return the same value (say, 'wholesale') but if you wanted
 * subtype 'commercial_buyer' could return value 'commercial.'
 *
 * @param string $type
 * @return unknown
 */
function getStickyWidgetSet($type = 'default') {
	switch($type) {
		case elgg_echo('TYPE_ORG'):
			return elgg_echo('TYPE_ORG');
			break;
		case elgg_echo('TYPE_USER'):
			return elgg_echo('TYPE_USER');
			break;
		case 'default':
		default:
			return elgg_echo('TYPE_USER');
	}

}

?>