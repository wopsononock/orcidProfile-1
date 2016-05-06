# ORCID Profile Puller

**NOTE: This plugin is a work in progress and not yet ready for production use.**

Plugin for PKP user profiles (tested with OJS 2.x)

Copyright © 2015-2016 University of Pittsburgh
<br />Copyright © 2014-2016 Simon Fraser University Library
<br />Copyright © 2003-2016 John Willinsky

Licensed under GPL 2 or better.

## Features:

 * Hooks into the User Profile, Registration, and Submission (step 3) forms
 * Requests ORCIDs of authors via email

## Install:

 * Copy the source into the PKP product's plugins/generic folder.
 * Run `tools/upgrade.php upgrade` to allow the system to recognize the new plugin.
 * Enable this plugin within the administration interface.
 * Set up [an application with ORCID](https://orcid.org/developer-tools).
  * The URI and description are to reassure users.
  * The redirect URI can be anything within your OJS/OMP installation.
  * ORCID will give you a client ID and secret.
 * Consider the settings within the administation interface.
  * Enter the client and secret from the ORCID application setup.
