<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

## WOP:[pi][1][addType]
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_staddressmap_pi1.php', '_pi1', 'list_type', 1);
?>