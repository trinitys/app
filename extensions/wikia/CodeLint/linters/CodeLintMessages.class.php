<?php

/**
 * CodeLint i18n
 * Class used for linting i18n files / message usage
 * @author Sebastian Marzjan
 *
 * Usage example:
 * SERVER_ID=1 php /usr/wikia/source/wiki/maintenance/wikia/codelint.php --conf=/usr/wikia/docroot/wiki.factory/LocalSettings.php --mode=messages --dir=/usr/wikia/source/wiki/extensions/wikia/CodeLint/
 *
 * Todo:
 * - enable omitting lines from message file from checks (unused/undocumented messages)
 * - enable omitting lines from php / js files from checks (non-existent messages)
 * - record full usage stats to blame for nonexistent messages
 *
 */

class CodeLintMessages extends CodeLint {
	const LANG_KEY_DOCUMENTATION = 'qqq';

	const ERROR_NONEXISTENT_DOCUMENTATION = 1;
	const ERROR_UNDOCUMENTED_MESSAGE = 2;
	const ERROR_WHITESPACES_AT_BEGINNING = 4;
	const ERROR_WHITESPACES_AT_END = 8;
	const ERROR_UNUSED_MESSAGE = 16;
	const ERROR_UNDEFINED_MESSAGE = 32;

	const MESSAGE_USAGE_CACHE_PREFIX = '/tmp/message-usage-cache-';

	// file name pattern - used when linting directories
	protected $filePattern = '*.i18n.php';

	protected $fileRegexPattern = '#^(.*\.i18n\.php)$#';

	// typical message calls in php files
	protected $phpMesageUsagePatterns = array(
		'#((\$this->wf->[m|M]sg)\([ ]*[\'"]([a-zA-Z0-9-]*)[\'"][ ]*(,.*)?\))#',
		'#((\$app->wf->[m|M]sg)\([ ]*[\'"]([a-zA-Z0-9-]*)[\'"][ ]*(,.*)?\))#',
		'#((F::app\(\)->wf->[m|M]sg)\([ ]*[\'"]([a-zA-Z0-9-]*)[\'"][ ]*(,.*)?\))#',
		'#((\$this->wf->[m|M]sgForContent)\([ ]*[\'"]([a-zA-Z0-9-]*)[\'"][ ]*(,.*)?\))#',
		'#((\$app->wf->[m|M]sgForContent)\([ ]*[\'"]([a-zA-Z0-9-]*)[\'"][ ]*(,.*)?\))#',
		'#((F::app\(\)->wf->[m|M]sgForContent)\([ ]*[\'"]([a-zA-Z0-9-]*)[\'"][ ]*(,.*)?\))#',
		'#((\$this->wf->[m|M]sgExt)\([ ]*[\'"]([a-zA-Z0-9-]*)[\'"][ ]*(,.*)?\))#',
		'#((\$app->wf->[m|M]sgExt)\([ ]*[\'"]([a-zA-Z0-9-]*)[\'"][ ]*(,.*)?\))#',
		'#((F::app\(\)->wf->[m|M]sgExt)\([ ]*[\'"]([a-zA-Z0-9-]*)[\'"][ ]*(,.*)?\))#',
		'#((wfMsg|wfMsgForContent|wfMsgExt)\([ ]*[\'"]([a-zA-Z0-9-]*)[\'"][ ]*(,.*)?\))#',
	);

	// typical message calls in js files
	protected $jsMessageUsagePatterns = array(
		'#(\$.msg\([ ]?[\'"]([a-zA-Z0-9-]*)[\'"][ ]?(,.*)?\))#',
	);

	// typical root dirs of extensions
	protected $rootDirPattern = "";

	protected $currentFile = null;
	protected $currentDir = null;

	protected $usageList = array();

	protected $definedMessages = array();


	public function __construct() {
		global $IP;
		$this->rootDirPattern = "#($IP/extensions/(wikia/)?([a-zA-Z0-9]+)/)#";
	}

	protected function getExtensionDirectory($fileName) {
		$match = preg_match($this->rootDirPattern, $fileName, $matches);
		if ($match && !empty($matches[1])) {
			return $matches[1];
		}
		// failover
		return dirname($fileName);
	}

	public function initializeUsages($fileName) {
		$extDir = $this->getExtensionDirectory($fileName);

		if (empty($this->usageList[$extDir])) {
			$this->loadListFromCache($extDir);
		}

		if (empty($this->usageList[$extDir])) {
			$this->buildUsageList($extDir);
			if (!empty($this->usageList[$extDir])) {
				//$this->saveListToCache($extDir, $this->usageList[$extDir]);
			}
		}
	}

	protected function loadListFromCache($directory) {
		$cacheFile = self::MESSAGE_USAGE_CACHE_PREFIX . sha1($directory);
		if (file_exists($cacheFile) && filemtime($cacheFile) > time() - 1 * 60 * 60) {
			//$this->usageList[$directory] = unserialize(file_get_contents($cacheFile));
		}
	}

	protected function saveListToCache($directory, $data) {
		$cacheFile = self::MESSAGE_USAGE_CACHE_PREFIX . sha1($directory);
		file_put_contents($cacheFile, serialize($data));
	}

	protected function filterOutFilesNotToScan(&$files) {
		foreach ($files as $key => $file) {
			if (preg_match($this->fileRegexPattern, $file, $matches)) {
				unset($files[$key]);
			}
		}
	}

	protected function buildUsageList($directory) {
		$usages = array();

		$this->usageList[$directory] = array();
		$jsFiles = $this->findFiles($directory, '*.js');
		$phpFiles = $this->findFiles($directory, '*.php');

		$this->filterOutFilesNotToScan($phpFiles);

		foreach ($jsFiles as $file) {
			$fileContents = file($file);

			foreach ($this->jsMessageUsagePatterns as $pattern) {
				$matches = preg_grep($pattern, $fileContents);

				foreach ($matches as $lineNum => &$match) {
					preg_match($pattern, $match, $additionalMatches);
					$completeMatches[$additionalMatches[2]][$file] [] = $lineNum;
				}
			}
		}

		foreach ($phpFiles as $file) {
			$fileContents = file($file);

			foreach ($this->phpMesageUsagePatterns as $pattern) {
				$matches = preg_grep($pattern, $fileContents);

				foreach ($matches as $lineNum => &$match) {
					preg_match($pattern, $match, $additionalMatches);
					$completeMatches[$additionalMatches[3]][$file] [] = $lineNum;
				}
			}
		}

		$this->usageList[$directory] = $completeMatches;
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
		$this->currentDir = $this->getExtensionDirectory($fileName);
		$this->initializeUsages($fileName);

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
				$this->addToDefinedMessages($messageKey);
				if (empty($errorcount[$messageKey])) {
					$errorcount[$messageKey] = array(
						self::ERROR_NONEXISTENT_DOCUMENTATION => 0,
						self::ERROR_UNDOCUMENTED_MESSAGE => 0,
						self::ERROR_WHITESPACES_AT_BEGINNING => 0,
						self::ERROR_WHITESPACES_AT_END => 0,
						self::ERROR_UNUSED_MESSAGE => 0,
					);
				}

				$this->checkUndocumentedMessage($messageKey, $documented_keys, $errorcount, $errors);
				$this->checkWhitespacesAtBeginning($messageKey, $errorcount, $errors);
				$this->checkWhitespacesAtEnd($messageKey, $errorcount, $errors);
				$this->checkUnusedMessage($messageKey, $errorcount, $errors);
			}
		}

		$this->currentFile = null;
		$this->currentDir = null;

		return array(
			'errors' => $errors,
			'time' => round(microtime(true) - $startTime, 4)
		);
	}

	public function afterCheckDirectory($directoryName, $blacklist = array()) {
		$definedMessages = array_keys($this->definedMessages);
		foreach ($this->usageList as $directory => $messages) {
			foreach ($messages as $messageKey => $usageSpecifics) {
				$errors = array();
				$startTime = microtime(true);
				$results = array();
				$errorcount[$messageKey][self::ERROR_UNDEFINED_MESSAGE] = 0;
				if (!in_array($messageKey, $definedMessages)) {

					foreach($usageSpecifics as $file => $lines) {
						$errors [] = array(
							'type' => self::ERROR_UNDEFINED_MESSAGE,
							'error' => 'Message ' . $messageKey . ' is not defined',
							'raw' => "Message '{a}' is not defined",
							'isImportant' => true,
							'lines' => $lines ,
							'blame' => $this->getBlameInfo($file, $lines[0]),
							'a' => $messageKey
						);

						$results[$file] = array(
							'errors' => $errors,
							'errorsCount' => count($errors),
							'importantErrorsCount' => count($errors),
							'time' => round(microtime(true) - $startTime, 4),
							'lines' => $lines,
							'fileChecked' => realpath($file)
						);
					}
				}

			}
		}



		return $results;
	}

	protected function checkUnusedMessage($messageKey, &$errorcount, &$errors) {
		$usages = array_keys($this->usageList[$this->currentDir]);
		if (!in_array($messageKey, $usages)) {
			$errors [] = array(
				'type' => self::ERROR_UNUSED_MESSAGE,
				'raw' => "Message '{a}' seems to be unused",
				'reason' => 'Message ' . $messageKey . ' seems to be unused',
				'line' => $this->findLineWith($messageKey, $errorcount[$messageKey][self::ERROR_UNUSED_MESSAGE]),
				'a' => $messageKey
			);
		}
	}

	protected function checkWhitespacesAtEnd($messageKey, &$errorcount, &$errors) {
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

	protected function checkWhitespacesAtBeginning($messageKey, &$errorcount, &$errors) {
		if (ltrim($messageKey) != $messageKey) {
			$errors [] = array(
				'type' => self::ERROR_WHITESPACES_AT_BEGINNING,
				'raw' => "Message '{a}' contains white characters at the beginning of key",
				'reason' => $messageKey . ' is not documented',
				'line' => $this->findLineWith($messageKey, $errorcount[$messageKey][self::ERROR_WHITESPACES_AT_BEGINNING]),
				'a' => $messageKey
			);
		}
	}

	protected function checkUndocumentedMessage($messageKey, $documented_keys, &$errorcount, &$errors) {
		if (!in_array($messageKey, $documented_keys)) {
			$errors [] = array(
				'type' => self::ERROR_UNDOCUMENTED_MESSAGE,
				'raw' => "Message '{a}' is not documented",
				'reason' => 'Message ' . $messageKey . ' is not documented',
				'line' => $this->findLineWith($messageKey, $errorcount[$messageKey][self::ERROR_UNDOCUMENTED_MESSAGE]),
				'a' => $messageKey
			);
		}
	}

	protected function findLineWith($messageKey, &$errorcount) {
		$lineCount = 0;

		if ($this->currentFile) {
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

		if (is_file($entry)) {
			$fileContents = file_get_contents($entry);
			if (mb_strpos($fileContents, '/* i18nLint skip */') !== false) {
				wfProfileOut(__METHOD__);
				return true;
			}
		}

		if (parent::isBlacklisted($entry, $blacklist)) {
			return true;
		}

		wfProfileOut(__METHOD__);
		return false;
	}

	protected function addToDefinedMessages($messageKey) {
		if (!isset($this->definedMessages[$messageKey])) {
			$this->definedMessages[$messageKey] = true;
		}
	}
}
