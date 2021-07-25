<?php


class OrcidValidator {

	/**
	 * OrcidValidator constructor.
	 * @param $plugin
	 */
	function __construct(&$plugin) {
		$this->plugin =& $plugin;
	}

		/**
	 * @param $str
	 * @return bool
	 */
	public function validateClientId($str): bool {
		if (preg_match('/^APP-[\da-zA-Z]{16}|(\d{4}-){3,}\d{3}[\dX]/', $str) == 1) {
			$this->plugin->setEnabled(true);
			return true;
		} else {
			$this->plugin->setEnabled(false);
			return false;
		}
	}

	/**
	 * @param $str
	 * @return bool
	 */
	public function validateClientSecret($str): bool {
		if (preg_match('/^(\d|-|[a-f]){36,64}/', $str) == 1) {
			$this->plugin->setEnabled(true);
			return true;
		} else {
			$this->plugin->setEnabled(false);
			return false;
		}
	}

}
