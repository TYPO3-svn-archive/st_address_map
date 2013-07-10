<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Thomas Scheibitz <mail@kreativschmiede-eichsfeld.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Gmap with tt_address' for the 'st_address_map' extension.
 *
 * @author	Thomas Scheibitz <<mail@kreativschmiede-eichsfeld.de>
 * @package	TYPO3
 * @subpackage	tx_staddressmap
 */
class tx_staddressmap_pi1 extends tslib_pibase {
	var $prefixId		= 'tx_staddressmap_pi1';		// Same as class name
	var $scriptRelPath	= 'pi1/class.tx_staddressmap_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey			= 'st_address_map';	// The extension key.
	var $pi_checkCHash	= true;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();
		$errormessage = '';

		if($this->conf['fancyselect'] == 1) {
			$GLOBALS['TSFE']->additionalFooterData[$extKey.'_85'] = '<script type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'static/selectbox/jquery.selectbox-0.1.3.min.js"></script>
			<script type="text/javascript">
				$(function () {
    				$(".tx_staddressmap_select").selectbox(
						"change",0
					);
				});
			</script>';
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey.'_86']		= '<link href="'.t3lib_extMgm::siteRelPath($this->extKey).'static/selectbox/jquery.selectbox.css" rel="stylesheet" type="text/css" />';
		}

		$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_staddressmap_pi1.'];
		$templatefile = ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_staddressmap_pi1.']['templateFile']) ? ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_staddressmap_pi1.']['templateFile']) : ('EXT:st_address_map/static/template.html');
		$this->templateHtml = $this->cObj->fileResource($templatefile);
		$subpart = $this->cObj->getSubpart($this->templateHtml, '###TEMPLATE###');

		// errorhandling
		foreach ($this->cObj->data['pi_flexform']['data']['sDEF']['lDEF'] as $key => $value) {
			$$key = reset($value);
			if(reset($value) == '') {
				$errormessage .= $this->checkEmptyFields($key);
				if($errormessage != '') $errormessage .= '<br />';
			}
		}
		if($errormessage != '') return '<div class="error">'.$errormessage.'</div>';

		// set addresslist
		$addresslist = explode(',',$addresslist);
		$addresslist = implode(' or pid = ',$addresslist);

		$content_id 		= $this->cObj->data['uid'];
		$tablefields = ($this->conf['tablefields'] == '') ? '' : $this->conf['tablefields'].',' ;

		/* ----- Ajax ----- */
		if(t3lib_div::_GET('type') == $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_staddressmap_pi1.']['ajaxtypenumb']) {
			return $this->gimmeData(t3lib_div::_GET('v'), t3lib_div::_GET('cid'), t3lib_div::_GET('t'), t3lib_div::_GET('rad'));
		}

		/* ----- selectfields ----- */
		foreach (preg_split('/\s?,\s?/',$this->conf['dropdownfields']) as $value) {
			$option = '';
			// Frontend RadiusSelectField
			if($value == 'radiusselect') {
				$selectrad = explode(',', $this->conf['searchradiusfe']);
				$option = '<select class="tx_staddressmap_select" id="tx_staddressmap_select_'.$value.'"><option value="-1">'.$this->pi_getLL('please_select').'</option>';
				foreach($selectrad as $row) {
					$option .= '<option value="' . $row . '">' . $row . ' km</option>';
				}
				$option .= '</select>';
				$markerArray['###' . strtoupper($value) . '###'] = $option;
			} else {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('hidden,deleted,' . $value,'tt_address','(pid = ' . $addresslist . ') AND (hidden=0 AND deleted=0)',$groupBy = $value, $orderBy = $value, $limit = '');
				if($res && $GLOBALS['TYPO3_DB']->sql_affected_rows($res) != 0) {
					$option = '<select class="tx_staddressmap_select" id="tx_staddressmap_select_' . $value . '"><option value="-1">' . $this->pi_getLL('please_select') . '</option>';
					foreach($res as $row) {
						$option .= '<option value="' . $row[$value] . '">' . $row[$value] . '</option>';
					}
					$option .= '</select>';
				}  else {
					return $this->pi_getLL('nodata');
				}
				$markerArray['###' . strtoupper($value) . '###'] = $option;
			}
		}

		/* ----- inputfields ----- */
		foreach (preg_split('/\s?,\s?/',$this->conf['inputfields']) as $value) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($value,'tt_address','hidden=0 AND deleted=0',$groupBy = $value,$orderBy = $value,$limit = '');
			if($res && $GLOBALS['TYPO3_DB']->sql_affected_rows($res) != 0) {
				foreach($res as $row) {
					$option = '<input class="tx_staddressmap_input" id="tx_staddressmap_input_'.$value.'" value="" />';
				}
			}  else {
				return $this->pi_getLL('nodata');
			}
			$markerArray['###'.strtoupper($value).'###'] = $option;
		}

		# ----- Map -----
		if($this->conf['seeatstart'] == 1) {
			$maps = '<input id="tx_staddressmap_seeatstart" type="hidden" value="1" /><input id="tx_staddressmap_order" type="hidden" value="'.$this->conf['orderall'].'" />';
		}

		$maps .= '<input id="tx_staddressmap_cid" type="hidden" value="'.$content_id.'" /><div id="tx_staddressmap_gmap_'.$content_id.'" class="tx_staddressmap_gmap" style="width: '.$mapwidth.'px; height: '.$mapheight.'px"></div>';

		# ----- Mapsjavascript -----

		$bubblemarker = ($this->conf['bubblemarker']) ? 'var icon = "'.$this->conf['bubblemarker'].'";' : 'var icon = "";' ;
		$GLOBALS['TSFE']->additionalFooterData[$this->extKey.'_665_'.$content_id] = '
			<script type="text/javascript">
			
			var map;
			var circle = null;
			var circledata = null;
			var marker = new Array();
			var centerpoints = new Array();
			var detailzoom = new Array();
			
			var city_marker = new Array();
			var city_centerpoints = new Array();
			var city_detailzoom = new Array();
			
			var region_marker = new Array();
			var region_centerpoints = new Array();
			var region_detailzoom = new Array();
			' . $bubblemarker . '
			
				function initialize(){
				var latlng = new google.maps.LatLng(' . $center_coordinates . ');
				var myMap_' . $content_id . ' = {
					zoom: ' . $start_zoom . ',
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				map = new google.maps.Map(document.getElementById("tx_staddressmap_gmap_' . $content_id . '"), myMap_' . $content_id . ');
			}
			
	        </script>';

		$markerArray['###MAPS###'] = $maps;
		$markerArray['###ADDRESSLIST###'] = '<div id="tx_staddressmap_addresslist_' . $content_id . '" class="tx_staddressmap_addresslist"></div>';

		if($this->conf['searchbutton'] == 1) {
			$markerArray['###SEARCHBUTTON###'] = '<input class="tx_staddressmap_submit" type="submit" value="' . $this->pi_getLL('search') . '" />';
		} else {
			$markerArray['###SEARCHBUTTON###'] = '';
		}


		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray).'<div style="display:none" id="tx_staddressmap_addresslist_pageid">' . $GLOBALS["TSFE"]->id.'</div><div style="display:none" id="tx_staddressmap_addresslist_ajaxtypenumb">' . $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_staddressmap_pi1.']['ajaxtypenumb'] . '</div>';
		return $this->pi_wrapInBaseClass($content);
	}

	private function checkEmptyFields($key) {
		return $this->pi_getLL('error_empty_'.$key);
	}


	/**
	 * Get geo coordinates from address
	 *
	 * @param string $data
	 * @return array lat, lng
	 */

	protected function getMapsCoordinates($data){
		$json = t3lib_div::getUrl('https://maps.googleapis.com/maps/api/geocode/json?sensor=false&region=de&address=' . urlencode($data));
		$jsonDecoded = json_decode($json, true);
		if (!empty($jsonDecoded['results'])) {
			$lat = $jsonDecoded['results']['0']['geometry']['location']['lat'];
			$lng = $jsonDecoded['results']['0']['geometry']['location']['lng'];
		} else {
			$lat = 0;
			$lng = 0;
		}
		return array($lat, $lng);
	}

	protected function gimmeData($var, $cid, $what, $rad=20000) {

		$tablefields = $this->conf['tablefields'];
		$searchradius = $this->conf['searchradius'];

		$subpart = $this->cObj->getSubpart($this->templateHtml, '###ADDRESSLISTS###');
		$singlerow=$this->cObj->getSubpart($subpart, '###ROW###');

		// check flexform
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pi_flexform','tt_content','(hidden=0 and deleted=0) and uid=' . $cid, $groupBy = '', $orderBy = '', $limit = '');
		if($res && $GLOBALS['TYPO3_DB']->sql_affected_rows($res) != 0) {
			while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$flexform	= t3lib_div::xml2array($row['pi_flexform']);
			}
		} else {
			return $this->pi_getLL('nodata');
		}

		foreach ($flexform['data']['sDEF']['lDEF'] as $key => $value) {
			$$key = reset($value);
		}

		if(!$rad || $rad == -1) {
			$rad = ($searchradius or $searchradius != 0) ? $searchradius : '20000' ;
		}
		// set addresslist
		$addresslist = explode(',', $addresslist);
		$addresslist = implode(' or pid = ',$addresslist);

		// Radius search
		$js_circle = 'circledata = null;';
		if(in_array($what, preg_split('/\s?,\s?/',$this->conf['radiusfields']))) {
			// radius
			$rc = ($this->conf['radiuscountry']) ? ',' . $this->conf['radiuscountry'] : '' ;
			$koord = $this->getMapsCoordinates(t3lib_div::_GET('v') . $rc);

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'uid,  '.$tablefields.', tx_staddressmap_lat, tx_staddressmap_lng,
				6378.388 * acos(sin(RADIANS(tx_staddressmap_lat)) * sin(RADIANS('.$koord['0'].')) + cos(RADIANS(tx_staddressmap_lat)) * cos(RADIANS('.$koord['0'].')) * cos(RADIANS('.$koord['1'].') -  RADIANS(tx_staddressmap_lng))) AS EAdvanced',
				'tt_address',
				'(hidden=0 AND deleted=0) AND (pid = '.$addresslist.') AND 6378.388 * acos(sin(RADIANS(tx_staddressmap_lat)) * sin(RADIANS('.$koord['0'].')) + cos(RADIANS(tx_staddressmap_lat)) * cos(RADIANS('.$koord['0'].')) * cos(RADIANS('.$koord['1'].') -  RADIANS(tx_staddressmap_lng))) <= '.$rad,
				$groupBy = '',
				$orderBy = 'EAdvanced',
				$limit = ''
			);

			// see radius
			if($this->conf['circle'] == 1) {
				$js_circle .= '
					circledata = {
						strokeColor: "'.$this->conf['circleStrokeColor'].'",
						strokeOpacity: '.$this->conf['circleStrokeOpacity'].',
						strokeWeight: '.$this->conf['circleStrokeWeight'].',
						fillColor: "'.$this->conf['circlefillColor'].'",
						fillOpacity: '.$this->conf['circlefillOpacity'].',
						map: map,
						center: new google.maps.LatLng('.$koord['0'].', '.$koord['1'].'),
						radius: '.($rad*1000).'
					};
				';
			}
		} else {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'uid,' . $tablefields . ' tx_staddressmap_lat, tx_staddressmap_lng',
				'tt_address',
				'(hidden=0 and deleted=0) and (pid = ' . $addresslist . ') and ' . $what . ' like "' . $var . '%"',
				$groupBy = '',
				$orderBy = '',
				$limit = ''
			);
		}

		// see all
		if(t3lib_div::_GET('all') == 1) {
			$rad = ($searchradius or $searchradius != 0) ? $searchradius : '20000' ;
			$res = $GLOBALS['TYPO3_DB']->exec_selectgetRows(
				'uid, '.$tablefields.', tx_staddressmap_lat, tx_staddressmap_lng',
				'tt_address',
				'(hidden=0 and deleted=0) and (pid = '.$addresslist.')',
				$groupBy = '',
				$orderBy = t3lib_div::_GET('order'),
				$limit = ''
			);
			$js_circle = '';
		}

		if($res && $GLOBALS['TYPO3_DB']->sql_affected_rows($res) != 0) {
			$ji = 0;
			$common_lat = array();
			$common_lng = array();
			$js_output = '<script type="text/javascript">'."\n";
			$js_output .= 'var a = new Array();'."\n";

			foreach($res as $key => $row) {
				if($row['tx_staddressmap_lat'] == 0 || $row['tx_staddressmap_lng'] == 0) {
					$newkoord = $this->getMapsCoordinates($row['zip'] . ' ' . $row['city'] . ',' . $row['address'] . ',' . $row['country']);
					$koor_update = array();
					$koor_update['tx_staddressmap_lat'] = $newkoord[0];
					$koor_update['tx_staddressmap_lng'] = $newkoord[1];
					$insert_q = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
						'tt_address',
						'uid = ' . $row['uid'],
						$koor_update
					);
				}

				// begin js
				$js_output .= 'a[' . $ji . '] = new Object();'."\n";
				// begin bubbletext
				$bubbletext = '';
				foreach (preg_split('/\s?,\s?/', $this->conf['bubblefields'], -1) as $tvalue) {
					if($row[$tvalue]) {
						$bubblewrap = $this->conf['bubblelayout.'][$tvalue] ? $this->conf['bubblelayout.'][$tvalue] : '|';
						if($tvalue == 'email') {
							$bubbletext .= t3lib_TStemplate::wrap(str_replace(array('<a',"'",'"'), array("tx_addressmap_replace","|-|","-|-"), $this->cObj->mailto_makelinks('mailto:' . $row[$tvalue])), $bubblewrap);
						} elseif ($tvalue == 'www') {
							$conf = array();
							$conf['parameter'] = $row[$tvalue];
							$linktext = explode(' ', $row[$tvalue]);
							$bubbletext .= t3lib_TStemplate::wrap($this->cObj->typolink($linktext[0], $conf), $bubblewrap);
						} else {
							$bubbletext .= t3lib_TStemplate::wrap(str_replace("\r\n", '<br />', htmlentities($row[$tvalue], ENT_COMPAT, 'UTF-8', 0)), $bubblewrap);
						}
					}
				}

				// list
				foreach (preg_split('/\s?,\s?/', $tablefields) as $tvalue) {
					if($row[$tvalue]) {
						$valconf = array(
							'outputfield' => nl2br($row[$tvalue])
						);
						$this->cObj->start($valconf);
						$markerArray['###' . strtoupper($tvalue) . '###'] = $this->cObj->cObjGetSingle($this->conf['listlayout.'][$tvalue], $this->conf['listlayout.'][$tvalue . '.']);
					} else {
						$markerArray['###' . strtoupper($tvalue) . '###'] = '';
					}
				}

				$bubbletext = t3lib_TStemplate::wrap($bubbletext, $this->conf['bubblelayout.']['wrap']);
				$js_output .= 'a[' . $ji . '].name = \'' . $bubbletext . '\'' . "\n";
				$js_output .= 'a[' . $ji . '].lat = ' . $row['tx_staddressmap_lat'] . ';' . "\n";
				$js_output .= 'a[' . $ji . '].lng = ' . $row['tx_staddressmap_lng'] . ';' . "\n";

				# ----- Calculate average coordinates
				$common_lat[] = $row['tx_staddressmap_lat'];
				$common_lng[] = $row['tx_staddressmap_lng'];
				$ji++;

				$markerArray['###DISTANCE###'] = ($this->conf['radiusfields'] != '' && round($row['EAdvanced'], 1) > 0) ? t3lib_TStemplate::wrap(round($row['EAdvanced'], 1) . ' km',$this->conf['listlayout.']['distance']) : '' ;

				$adresslistrow .= $this->cObj->substituteMarkerArrayCached($singlerow,$markerArray);
			}
			$js_output .= 'marker[0] = a;'."\n";
			$js_output .= 'centerpoints[0] = new Object();'."\n";

			if(in_array($what, preg_split('/\s?,\s?/',$this->conf['radiusfields']))) {
				$js_output .= 'centerpoints[0].lat = ' . $koord['0'] . ';' . "\n";
				$js_output .= 'centerpoints[0].lng = ' . $koord['1'] . ';' . "\n";
			} else {
				$js_output .= 'centerpoints[0].lat = ' . ((max($common_lat)+min($common_lat))/2) . ';' . "\n";
				$js_output .= 'centerpoints[0].lng = ' . ((max($common_lng)+min($common_lng))/2) . ';' . "\n";
			}

			$js_output .= 'detailzoom[0] = new Object();'."\n";
			$js_output .= 'detailzoom[0] = ' . $detail_zoom . ';' . "\n";
			$js_output .= $js_circle;
			$js_output .= '</script>';

		}  else {
			return $this->pi_getLL('nodata') . '<script type="text/javascript">marker = new Array();</script>';
		}

		$subpartArray['###ROW###'] = $adresslistrow;
		$markerArray['###JSOUTPUT###'] = $js_output;

		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, $subpartArray,array());
		return $this->pi_wrapInBaseClass($content);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/st_address_map/pi1/class.tx_staddressmap_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/st_address_map/pi1/class.tx_staddressmap_pi1.php']);
}

?>