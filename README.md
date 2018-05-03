# ORCID Profile Plugin

**NOTE: This plugin is a work in progress and should be tested with your OJS setup in the ORCID Sandbox before using in production.**

**NOTE: Please ensure you're using the correct branch. Use the [master branch](https://github.com/pkp/orcidProfile/tree/master) for OJS 3.0, and the [ojs-dev-2_4 branch](https://github.com/pkp/orcidProfile/tree/ojs-dev-2_4) for OJS 2.4.x.**

Plugin for adding and verifying ORCID iD in PKP user profiles and author metadata (for OJS 3.1.1).

Copyright © 2017-2018 University Library Heidelberg

Copyright © 2015-2018 University of Pittsburgh

Copyright © 2014-2018 Simon Fraser University

Copyright © 2003-2018 John Willinsky

Licensed under GPL 2 or better.

## Features:
### New in version 1.1.0
* Enable site-wide configuration of ORCID API settings using config.inc.php 
* Support ORCID API Version 2.1  (store only https ORCID Ids)
* Added checkbox in Author meta data form to send e-mail for requesting ORCID authorization from article authors.  
  The e-mail will be sent on saving the form data.
* Added plugin setting to automatically send the e-mail on promoting a submission to production.
* Added text to author meta data form to show if the ORCID access has been granted and the expiration date.
* Updated locale strings, e-mail template texts and added HTML line breaks to existing templates.
* Added template for showing detailed success or failure message for ORCID authorization redirects.
* Added logging of ORCID API communication to separate ORCID log file in `OJS_FILES_DIR/orcid.log`.  
  The log level can be adjusted in the plugin settings.
  **NOTE: Make sure that the files folder is not publicly accessible**
* **NOTE: ORCID Member organizations only**  
  Added new e-mail template `ORCID_REQUEST_AUTHOR_AUTHORIZATION` to distinguish between collecting ORCID id and requesting ORCID record access.  
  If the ORCID member API is selected this template will be used to generate the e-mails to authors.  
  The included authorization link will have the access scope `/activities/update`. An author can authorize access to his/her ORCID record to allow the adding of the submission to the record.  
  See https://members.orcid.org/api/oauth/orcid-scopes for more information.
* **NOTE: ORCID Member organizations only**  
  Added uploading of submission meta data to authorized and connected ORCID records of authors when:
  * assigning a submission to an already published issue
  * publishing a new issue
  * author grants permission after the publication of the issue  

  Submission meta data will be updated in the ORCID record if the process is triggered again.

## Install:

 * (To be written)
