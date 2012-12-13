<?php

/**
 * CodeLint i18n
 * Class used for linting i18n files / message usage
 * @author Sebastian Marzjan
 */

class CodeLintMessages extends CodeLint {
	const LANG_KEY_DOCUMENTATION = 'qqq';
	const ERROR_NONEXISTENT_DOCUMENTATION = 1;
	const ERROR_UNDOCUMENTED_MESSAGE = 2;
	const ERROR_WHITESPACES_AT_BEGINNING = 4;
	const ERROR_WHITESPACES_AT_END = 8;

	// file name pattern - used when linting directories
	protected $filePattern = '*.i18n.php';

	protected $currentFile = null;

	public function __construct() {
		// initialize cache
	}

	/**
	 * Filter out errors we don't really want in the report
	 *
	 * @param array $error error entry reported by i18n lint
	 * @return boolean returns true if the entry should be kept
	 */
	public function filterErrorsOut($error) {
		return true;
	}

	/**
	 * Simplify error report to match the generic format
	 *
	 * @param array $entry single entry from error report
	 * @return array modified entry
	 */
	public function internalFormatReportEntry($entry) {
		return array(
			'error' => $entry['reason'],
			'line' => $entry['line'],
		);
	}

	/**
	 * Perform lint on a file and return list of errors
	 *
	 * @param string $fileName file to be checked
	 * @return array list of reported warnings
	 */
	public function internalCheckFile($fileName) {
		$startTime = microtime(true);
		$documented_keys = array();
		$errors = array();

		$this->currentFile = file($fileName);
		// $messages should now be defined
		require($fileName);

		if (!isset($messages['qqq'])) {
			$errors [] = array(
				'type' => self::ERROR_NONEXISTENT_DOCUMENTATION,
				'raw' => "'Message documentation in '{a}' does not exist",
				'reason' => 'Message documentation does not exist at all',
				'evidence' => $fileName,
				'line' => 1,
				'a' => basename($fileName)
			);
		} else {
			$documented_keys = array_keys($messages['qqq']);
		}

		$errorcount = array();
		foreach ($messages as $lang => $messageList) {
			foreach ($messageList as $messageKey => $messageBody) {
				if (empty($errorcount[$messageKey])) {
					$errorcount[$messageKey] = array(
						self::ERROR_NONEXISTENT_DOCUMENTATION => 0,
						self::ERROR_UNDOCUMENTED_MESSAGE => 0,
						self::ERROR_WHITESPACES_AT_BEGINNING => 0,
						self::ERROR_WHITESPACES_AT_END => 0,
					);
				}

				if (!in_array($messageKey, $documented_keys)) {
					$errors [] = array(
						'type' => self::ERROR_UNDOCUMENTED_MESSAGE,
						'raw' => "Message '{a}' is not documented",
						'reason' => 'Message ' . $messageKey . ' is not documented',
						'line' => $this->findLineWith($messageKey, $errorcount[$messageKey][self::ERROR_UNDOCUMENTED_MESSAGE]),
						'a' => $messageKey
					);
				}
				if (ltrim($messageKey) != $messageKey) {
					$errors [] = array(
						'type' => self::ERROR_WHITESPACES_AT_BEGINNING,
						'raw' => "Message '{a}' contains white characters at the beginning of key",
						'reason' => $messageKey . ' is not documented',
						'line' => $this->findLineWith($messageKey, $errorcount[$messageKey][self::ERROR_WHITESPACES_AT_BEGINNING]),
						'a' => $messageKey
					);
				}
				if (rtrim($messageKey) != $messageKey) {
					$errors [] = array(
						'type' => self::ERROR_WHITESPACES_AT_END,
						'raw' => "Message '{a}' contains white characters at the end of key",
						'reason' => 'Message ' . $messageKey . ' contains white characters at the end of key',
						'line' => $this->findLineWith($messageKey, $errorcount[$messageKey][self::ERROR_WHITESPACES_AT_END]),
						'a' => $messageKey
					);
				}
			}
		}

		$this->currentFile = null;

		return array(
			'errors' => $errors,
			'time' => round(microtime(true) - $startTime, 4)
		);
	}

	protected function findLineWith($messageKey, &$errorcount) {
		$lineCount = 0;
		// todo: cache not to repeat from start on each loop
		foreach ($this->currentFile as $lineNum => $lineContent) {
			if (mb_strpos($lineContent, $messageKey) !== false) {
				if ($lineCount < $errorcount) {
					$lineCount++;
				} else {
					$errorcount++;
					return $lineNum;
				}
			}
		}
		return null;
	}

	/**
	 * Decide whether given error is important and should be eventaully marked in the report
	 *
	 * @param string $errorMsg error message
	 * @return boolean is it an important error
	 */
	protected function isImportantError($errorMsg) {
		switch ($errorMsg) {
			case 'Message documentation does not exist at all':
			 	$ret = true;
				break;
			default:
				$ret = false;
		}

		return $ret;
	}

	/**
	 * Check whether given directory / name matches any blacklist entry
	 *
	 * @param string $entry name to check against blacklist entries
	 * @param array $blacklist blacklist entries
	 * @return boolean is entry blacklisted
	 */
	protected function isBlacklisted($entry, $blacklist) {
		wfProfileIn(__METHOD__);

		$fileContents = file_get_contents($entry);
		if (mb_strpos($fileContents, '/* i18nLint skip */') !== false) {
			wfProfileOut(__METHOD__);
			return true;
		}

		if(parent::isBlacklisted($entry, $blacklist)) {
			return true;
		}

		wfProfileOut(__METHOD__);
		return false;
	}
}
