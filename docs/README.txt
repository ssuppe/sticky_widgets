OVERVIEW

This plugin creates 'sticky widgets' for Elgg.  This means that the user doesn't have the
option to configure plugin layout, but you (any admin) do.  You can set widgets for both
the dashboard and the profile, as well as user subtypes (for advanced users).  This is all done
via a GUI from the Administration Page.

The benefits are that you get to control layout, and can change/upgrade widgets at any
time and everyone sees the change right away.

INSTALLATION
Note:  This is mostly drag and drop, but has advanced settings developers/webmasters
need to know about (although not many).  See <ADVANCED SETTINGS> for these advanced things.

Basically, move the folder sticky_widgets into the $elgg_root/mod directory and call it a day.

UNINSTALLATION
On the admin menu, go to Sticky Widgets, and at the bottom is an uninstall button.  Click this
to remove all the Sticky Widget object from the database.  Users will be left with the last 
configuration you had applied.  Only then delete the mod.

DEVELOPER NOTES
In order to make this work, I 'bent' the framework ever so slightly.  I didn't change
any core functionality, but the trick was I needed 'global widgets,' so I assigned the
entities/widgets I create an owner ID of '2', hardcoded, which I assume is ALWAYS the guid
of the first admin.

Why?  Although the other idea presented on the Elgg Development Group was a good one
(create a user just for doing widget layout and always read THEIR configuration), I
really didn't want to add a user just for doing this.  Also, by doing it this way, I
believe I've gone a little further in ensuring that only admins have access to these
objects.

There seem to be no negative side-effects, other than that slightly-queasy feeling I
get every time I save user-2-owned objects as a user who might not have GUID 2 :)

ADVANCED SETTINGS
In conf/types.php, there are two 'contexts' specified - profile and dashboard.  These control
the toggles (radio buttons) on the edit page as to which area of the site you are editing.
Feel free to add more here - they're show up in the GUI immediately upon refresh.

Also, by editing 3 different methods in conf/types.php, you can enable user subtypes.  Use
carefully, the documentation is in the file. 

Disclaimer:  If it seems a bit convoluted, I apologize.  But this fills a 'business' need for me
(in that I needed subtyping without using Elgg's user->subType, but wanted to support it just
in case).  If you have a better idea, please let me know.  What this means is that the GUI and
this file may not accurately reflect each other, so please be careful.

Finally, there is a method called sw_shouldIgnoreContext that overrides widgets default behavior to
only work in the context the widget developer specified.  I find this useful, I hope you do too.  If
not, change it to return false and all will be well.

PEANUT GALLERY
Feel free to let me know about bugs, suggestions, critiques, complaints/kudos.  They will 
most likely be followed up in that order :)

CHANGELOG

0.5.1 : 14 Jan 2009 : 
	* Fixed a php tag and added an 'echo' statement in admin/edit.php.  (Thanks Fabrice).
	  Updated CSS. No object functionality changed.  
	  
0.5.2 : 14 Jan 2009 : 
	* Fixed some HTML generation that was making IE puke on the draggable widgets.
		
0.5.3 : 16 Jan 2009 :
	* Added some re-initializing of JQuery draggable/droppables in edit.php after I make the AJAX
	  update.  Hopefully this fixes everyones drag/drop issues!
	  
0.6.0 : 20 Jan 2009 :
	* 'Newer' codebase, removes feature bug from original implementation.  Also incorporates timestamps
	   so we only have to replace user widgets (against the master) when things have actually changed.

0.6.1 : 20 Jan 2009 :
	* Fixed some permissions problems with updating widgets for other users (such as
	  when you are visiting their profile and they need to update according to Sticky 
	  widgets).
	  
0.6.3 : 25 Jan 2009 :
	* Accepts many of Diego Andrés Ramírez Aragón's re-org modifications (thanks!) and also kept his
	  (currently non-functioning) GUI in for SW settings defaults.  This is non-functioning until we
	  come up with a way to copy the SW's defaults over to the user's widgets as they are created."
	  
0.6.4 : 2 Feb 2009 :
	* Fixed a problem where titles of widgets weren't showing up due to an honoring of the widget's
	  context restrictions.
	* Fixed some misplaced HTML tags that will hopefully make the edit panel work correctly for people.