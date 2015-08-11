<?php

/**
 * @defgroup plugins_generic_orcidProfile
 */
 
/**
 * @file plugins/generic/orcidProfile/index.php
 *
 * Copyright (c) 2015 University of Pittsburgh
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_orcidProfile
 * @brief Wrapper for ORCID Profile plugin.
 *
 */

require_once('OrcidProfilePlugin.inc.php');

return new OrcidProfilePlugin();

?>
