# ORCID Profile Puller

# NOTE: This plugin is a work in progress and not yet ready for production use.

Plugin for PKP user profiles (tested with OJS 2.x)

Copyright (c) 2015-2016 University of Pittsburgh
Copyright (c) 2014-2016 Simon Fraser University Library
Copyright (c) 2003-2016 John Willinsky

Licensed under GPL 2 or better.

## Features:

 * Hooks into the User Profile, Registration, and User Management forms
 * Adds a query by-email or by-ORCID-iD to pre-populate profile fields based on ORCID data.

## Install:

 * Copy the source into the PKP product's plugins/generic folder.
 * Run `tools/upgrade.php upgrade` to allow the system to recognize the new plugin.
 * Enable this plugin within the administration interface.
 * Consider the settings within the administation interface.
 * Apply https://github.com/pkp/ojs/commit/ee9de84713b2ce880a92a78ca751c104a9765d35 to your OJS installation

## Bugs/TODOs:


 * The email / ORCID iD should be validated more strictly.  For example: `lib.pkp.classes.validation.ValidatorEmail` and `lib.pkp.classes.validation.ValidatorORCID`.
 * The settings should allow you to turn this on/off for each Template hook.
 * The resulting data should not blindly overwrite the current form data (if present)
 * Populating content into TinyMCE-based fields doesn't work.

