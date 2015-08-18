# ORCID Profile Puller

Plugin for PKP user profiles (tested with OJS 2.x)

Copyright 2015 (c) University of Pittsburgh

Licensed under GPL 2 or better.

## Features:

 * Hooks into the User Profile, Registration, and User Management forms
 * Adds a query by-email or by-ORCID-iD to pre-populate profile fields based on ORCID data.

## Install:

 * Copy the source into the PKP product's plugins/generic folder.
 * Run `tools/upgrade.php upgrade` to allow the system to recognize the new plugin.
 * Enable this plugin within the administration interface.
 * Consider the settings within the administation interface.

## Bugs/TODOs:


 * The email / ORCID iD should be validated more strictly.  For example: `lib.pkp.classes.validation.ValidatorEmail` and `lib.pkp.classes.validation.ValidatorORCID`.
 * The settings should allow you to turn this on/off for each Template hook.
 * The resulting data should not blindly overwrite the current form data (if present)
 * Populating content into TinyMCE-based fields doesn't work.

