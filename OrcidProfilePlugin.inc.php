<?php

/**
 * @file plugins/generic/orcidProfile/OrcidProfilePlugin.inc.php
 *
 * Copyright (c) 2015 University of Pittsburgh
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class OrcidProfilePlugin
 * @ingroup plugins_generic_orcidProfile
 *
 * @brief ORCID Profile plugin class
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class OrcidProfilePlugin extends GenericPlugin {
	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return true;
		if ($success && $this->getEnabled()) {
			// Insert ORCID Profile for the Registration page
			HookRegistry::register('Templates::User::Register::NewUser', array($this, 'insertOrcidHtml'));
			HookRegistry::register('Templates::User::Register::Form', array($this, 'insertOrcidBounce'));

			// Insert ORCID Profile for the Registration page
			HookRegistry::register('Templates::User::Profile', array($this, 'insertOrcidHtml'));

			// Insert ORCID Profile for the Manager
			HookRegistry::register('Templates::Manager::User::Profile', array($this, 'insertOrcidHtml'));

			// Insert ORCID callback
			HookRegistry::register('LoadHandler', array(&$this, 'setupCallbackHandler'));
		}
		return $success;
	}

	/**
	 * Get page handler path for this plugin.
	 * @return string Path to plugin's page handler
	 */
	function getHandlerPath() {
		return $this->getPluginPath() . DIRECTORY_SEPARATOR . 'pages';
	}
	
	/**
	 * Hook callback: register pages for each sushi-lite method
	 * This URL is of the form: orcidapi/{$orcidrequest}
	 * @see PKPPageRouter::route()
	 */
	function setupCallbackHandler($hookName, $params) {
		$page = $params[0];
		if ($this->getEnabled() && $page == 'orcidapi') {
			$this->import('pages/OrcidHandler');
			define('HANDLER_CLASS', 'OrcidHandler');
			return true;
		}
		return false;
	}	 

	function getDisplayName() {
		return __('plugins.generic.orcidProfile.displayName');
	}

	function getDescription() {
		return __('plugins.generic.orcidProfile.description');
	}

	/**
	 * Extend the {url ...} smarty to support this plugin.
	 */
	function smartyPluginUrl($params, &$smarty) {
		$path = array($this->getCategory(), $this->getName());
		if (is_array($params['path'])) {
			$params['path'] = array_merge($path, $params['path']);
		} elseif (!empty($params['path'])) {
			$params['path'] = array_merge($path, array($params['path']));
		} else {
			$params['path'] = $path;
		}

		if (!empty($params['id'])) {
			$params['path'] = array_merge($params['path'], array($params['id']));
			unset($params['id']);
		}
		return $smarty->smartyUrl($params, $smarty);
	}

	/**
	 * Set the page's breadcrumbs, given the plugin's tree of items
	 * to append.
	 * @param $subclass boolean
	 */
	function setBreadcrumbs($isSubclass = false) {
		$templateMgr =& TemplateManager::getManager();
		$pageCrumbs = array(
			array(
				Request::url(null, 'user'),
				'navigation.user'
			),
			array(
				Request::url(null, 'manager'),
				'user.role.manager'
			)
		);
		if ($isSubclass) {
			$pageCrumbs[] = array(
				Request::url(null, 'manager', 'plugins'),
				'manager.plugins'
			);
			$pageCrumbs[] = array(
				Request::url(null, 'manager', 'plugins', 'generic'),
				'plugins.categories.generic'
			);
		}

		$templateMgr->assign('pageHierarchy', $pageCrumbs);
	}

	/**
	 * Display verbs for the management interface.
	 */
	function getManagementVerbs() {
		$verbs = array();
		if ($this->getEnabled()) {
			$verbs[] = array('settings', __('manager.plugins.settings'));
		}
		return parent::getManagementVerbs($verbs);
	}

	/**
	 * Insert ORCID Profile HTML
	 */
	function insertOrcidHtml($hookName, $params) {
		// Avoid re-presenting the form
		if (Request::getUserVar('hideOrcid')) return false;

		$smarty =& $params[1];
		$output =& $params[2];
		$templateMgr =& TemplateManager::getManager();
		$journal = Request::getJournal();

		$templateMgr->assign(array(
			'orcidProfileAPIPath' => $this->getSetting($journal->getId(), 'orcidProfileAPIPath'),
			'orcidClientId' => $this->getSetting($journal->getId(), 'orcidClientId'),
		));

		$output .= $templateMgr->fetch($this->getTemplatePath() . 'orcidProfile.tpl');
		return false;
	}

	/**
	 * Insert ORCID bounce HTML
	 */
	function insertOrcidBounce($hookName, $params) {
		$smarty =& $params[1];
		$output =& $params[2];
		$output .= '<input type="hidden" name="orcidAuth" value="' . htmlspecialchars(Request::getUserVar('orcidAuth')) . '" />';
		$output .= '<script type="text/javascript">
		        $(document).ready(function(){ 
				$(\'#orcid\').attr(\'readonly\', "true");
			});
		</script>';
		return false;
	}

	/**
	 * Execute a management verb on this plugin
	 * @param $verb string
	 * @param $args array
	 * @param $message string Result status message
	 * @param $messageParams array Parameters for the message key
	 * @return boolean
	 */
	function manage($verb, $args, &$message, &$messageParams) {
		$journal =& Request::getJournal();
		if (!parent::manage($verb, $args, $message, $messageParams)) {
			if ($verb == 'enable' && !$this->getSetting($journal->getId(), 'orcidProfileAPIPath')) {
				// default the 1.2 public API if no setting is present
				$this->updateSetting($journal->getId(), 'orcidProfileAPIPath', 'http://pub.orcid.org/v1.2/', 'string');	
			} else {
				return false;
			}
		}

		switch ($verb) {
			case 'settings':
				$templateMgr =& TemplateManager::getManager();
				$templateMgr->register_function('plugin_url', array(&$this, 'smartyPluginUrl'));

				$this->import('OrcidProfileSettingsForm');
				$form = new OrcidProfileSettingsForm($this, $journal->getId());
				if (Request::getUserVar('save')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute();
						Request::redirect(null, 'manager', 'plugin');
						return false;
					} else {
						$this->setBreadcrumbs(true);
						$form->display();
					}
				} else {
					$this->setBreadcrumbs(true);
					$form->initData();
					$form->display();
				}
				return true;
			default:
				// Unknown management verb
				assert(false);
				return false;
		}
	}
}
?>
