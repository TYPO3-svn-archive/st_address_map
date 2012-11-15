<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

include_once(t3lib_extMgm::extPath('st_address_map') . 'lib/user_st_addressmapOnCurrentPage.php'); // Conditions for JS including
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_staddressmap_pi1.php', '_pi1', 'list_type', 1);
?>