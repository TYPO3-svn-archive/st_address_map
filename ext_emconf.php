<?php

########################################################################
# Extension Manager/Repository config file for ext "st_address_map".
#
# Auto generated 07-08-2012 23:32
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Address visualization with Google Maps and tt_address',
	'description' => '',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.1.5',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Thomas Scheibitz',
	'author_email' => 'mail@kreativschmiede-eichsfeld.de',
	'author_company' => 'Kreativschmiede Eichsfeld',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'tt_address' => '2.2.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:21:{s:9:"ChangeLog";s:4:"a51b";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"8cae";s:17:"ext_localconf.php";s:4:"6a69";s:14:"ext_tables.php";s:4:"268b";s:14:"ext_tables.sql";s:4:"dda4";s:12:"flexform.xml";s:4:"ce16";s:16:"locallang_db.xml";s:4:"06b1";s:17:"locallang_tca.php";s:4:"6136";s:19:"doc/wizard_form.dat";s:4:"e118";s:20:"doc/wizard_form.html";s:4:"437e";s:33:"pi1/class.tx_staddressmap_pi1.php";s:4:"de76";s:17:"pi1/locallang.xml";s:4:"bcc2";s:16:"static/style.css";s:4:"8b80";s:20:"static/template.html";s:4:"9042";s:23:"static/tx_addressmap.js";s:4:"180f";s:46:"static/selectbox/jquery.selectbox-0.1.3.min.js";s:4:"c76e";s:37:"static/selectbox/jquery.selectbox.css";s:4:"8cb8";s:33:"static/selectbox/select-icons.png";s:4:"4490";s:35:"static/st_address_map/constants.txt";s:4:"d41d";s:31:"static/st_address_map/setup.txt";s:4:"f18b";}',
);

?>