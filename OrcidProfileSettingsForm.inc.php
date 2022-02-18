<?php

/**
 * @file OrcidProfileSettingsForm.inc.php
 *
 * Copyright (c) 2015-2019 University of Pittsburgh
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class OrcidProfileSettingsForm
 * @ingroup plugins_generic_orcidProfile
 *
 * @brief Form for site admins to modify ORCID Profile plugin settings
 */

use PKP\form\Form;

class OrcidProfileSettingsForm extends Form
{
    public const CONFIG_VARS = [
        'orcidProfileAPIPath' => 'string',
        'orcidClientId' => 'string',
        'orcidClientSecret' => 'string',
        'sendMailToAuthorsOnPublication' => 'bool',
        'logLevel' => 'string',
        'isSandBox' => 'bool'
    ];
    /** @var int $contextId */
    public $contextId;

    /** @var object $plugin */
    public $plugin;

    /**
     * Constructor
     *
     * @param object $plugin
     * @param int $contextId
     */
    public function __construct(&$plugin, $contextId)
    {
        $this->contextId = $contextId;
        $this->plugin = & $plugin;

        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

        if (!$this->plugin->isGloballyConfigured()) {
            $this->addCheck(new \PKP\form\validation\FormValidator(
                $this,
                'orcidProfileAPIPath',
                'required',
                'plugins.generic.orcidProfile.manager.settings.orcidAPIPathRequired'
            ));
        }
        $this->addCheck(new \PKP\form\validation\FormValidatorPost($this));
        $this->addCheck(new \PKP\form\validation\FormValidatorCSRF($this));
        $this->addCheck(new \PKP\form\validation\FormValidatorCustom($this, 'orcidClientId', 'required', 'plugins.generic.orcidProfile.manager.settings.orcidClientId.error', function ($clientId) {
            if (preg_match('/^APP-[\da-zA-Z]{16}|(\d{4}-){3,}\d{3}[\dX]/', $clientId) == 1) {
                $this->plugin->setEnabled(true);
                return true;
            }
            $this->plugin->setEnabled(false);
        }));
        $this->addCheck(new \PKP\form\validation\FormValidatorCustom($this, 'orcidClientSecret', 'required', 'plugins.generic.orcidProfile.manager.settings.orcidClientSecret.error', function ($clientSecret) {
            if (preg_match('/^(\d|-|[a-f]){36,64}/', $clientSecret) == 1) {
                $this->plugin->setEnabled(true);
                return true;
            }
            $this->plugin->setEnabled(false);
        }));
    }

    /**
     * Initialize form data.
     */
    public function initData()
    {
        $contextId = $this->contextId;
        $plugin = & $this->plugin;
        $this->_data = [];
        foreach (self::CONFIG_VARS as $configVar => $type) {
            $this->_data[$configVar] = $plugin->getSetting($contextId, $configVar);
        }
    }

    /**
     * Assign form data to user-submitted data.
     */
    public function readInputData()
    {
        $this->readUserVars(array_keys(self::CONFIG_VARS));
    }

    /**
     * Fetch the form.
     *
     * @copydoc Form::fetch()
     *
     * @param null|mixed $template
     */
    public function fetch($request, $template = null, $display = false)
    {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('globallyConfigured', $this->plugin->isGloballyConfigured());
        $templateMgr->assign('pluginName', $this->plugin->getName());
        return parent::fetch($request, $template, $display);
    }

    /**
     * @copydoc Form::execute()
     */
    public function execute(...$functionArgs)
    {
        $plugin = & $this->plugin;
        $contextId = $this->contextId;
        foreach (self::CONFIG_VARS as $configVar => $type) {
            if ($configVar === 'orcidProfileAPIPath') {
                $plugin->updateSetting($contextId, $configVar, trim($this->getData($configVar), "\"\';"), $type);
            } else {
                $plugin->updateSetting($contextId, $configVar, $this->getData($configVar), $type);
            }
        }
        if (strpos($this->getData('orcidProfileAPIPath'), 'sandbox.orcid.org') == true) {
            $plugin->updateSetting($contextId, 'isSandBox', true, 'bool');
        }

        parent::execute(...$functionArgs);
    }
}
