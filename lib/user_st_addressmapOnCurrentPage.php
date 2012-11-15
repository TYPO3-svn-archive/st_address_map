<?php

/**
* Function user_st_addressmapOnCurrentPage() checks if a st_addressmap plugin is inserted on current page
*
* @param	string		$mode: mode could be empty or "ssd"
* @return	boolean		0/1
*/

function user_st_addressmapOnCurrentPage($mode = '') {

	$result = FALSE;
	if ($mode == '1') { return true; }

	if (TYPO3_MODE == 'FE') {
		$ttContentWhere = 'AND deleted = 0 AND hidden = 0';
		if (is_array($GLOBALS['TCA']['tt_content']) && method_exists($GLOBALS['TSFE']->sys_page, 'enableFields')) {
			$ttContentWhere = $GLOBALS['TSFE']->sys_page->enableFields('tt_content');
		}

		$pid = getCorrectPageIdForStaddressmapOnCurrentPageUserfunc();
		$where = 'pid = ' . intval($pid) . ' AND (list_type = "st_address_map_pi1"  OR CType = "shortcut")' . $ttContentWhere;
		$orderBy = 'CType';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('uid, CType, records', 'tt_content', $where, '', $orderBy, '');

		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			switch($row['CType']) {
				// Normal content element
				case 'list':
				$result = isStaddressmapOnCurrentPage($mode, $row['uid']);
				break;

				// Content element "Insert plugin"
				case 'shortcut':
				$recordUids = array();
				$records = t3lib_div::trimExplode(',', $row['records'], TRUE);
				foreach ($records as $record) {
					$recordInfo = t3lib_BEfunc::splitTable_Uid($record);
					if ($recordInfo[0] === 'tt_content') {
						$recordUids[] = $recordInfo[1];
					}
				}
				$recordUids = $GLOBALS['TYPO3_DB']->cleanIntList(implode(',', $recordUids));

				if(!$recordUids) {
					break;
				}

				$where = 'uid IN ( ' . $recordUids . ' ) AND list_type = "st_address_map_pi1"' . $ttContentWhere;
				$shortcutRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tt_content', $where, '', '', 1);
				$shortcutRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($shortcutRes);
				$result = isStaddressmapOnCurrentPage($mode, $shortcutRow['uid']);
				break;
			}

			if ($result === TRUE) {
				break;
			}
		}
	}
	return $result;
}


/**
 * Returns the correct Page-ID for the "isStaddressmapOnCurrentPage"-Check
 * This method has a check for "content_from_pid"-field of pages table
 *
 * @return	integer		Correct PageID
 */
function getCorrectPageIdForStaddressmapOnCurrentPageUserfunc() {
	$pid = $GLOBALS['TSFE']->id;

	// This part is copied and modified from TSpagegen::pagegenInit();
	// because we can`t call it directly. It is to early.
	if ($GLOBALS['TSFE']->page['content_from_pid'] > 0) {
		// make REAL copy of TSFE object - not reference!
		$temp_copy_TSFE = clone($GLOBALS['TSFE']);
		// Set ->id to the content_from_pid value - we are going to evaluate this pid as was it a given id for a page-display!
		$temp_copy_TSFE->id = $GLOBALS['TSFE']->page['content_from_pid'];
		$temp_copy_TSFE->getPageAndRootlineWithDomain($GLOBALS['TSFE']->tmpl->setup['config.']['content_from_pid_allowOutsideDomain'] ? 0 : $GLOBALS['TSFE']->domainStartPage);
		$pid = intval($temp_copy_TSFE->id);
		unset($temp_copy_TSFE);
	}

	return $pid;
}

function isStaddressmapOnCurrentPage($mode, $uid) {

	if ((($mode != 'ssd' || $GLOBALS['TSFE']->tmpl->setup['config.']['simulateStaticDocuments'] == 1) && $uid > 0)) {
		return TRUE;
	}

	return FALSE;
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/user_powermailOnCurrentPage.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/user_powermailOnCurrentPage.php']);
}
?>

?>