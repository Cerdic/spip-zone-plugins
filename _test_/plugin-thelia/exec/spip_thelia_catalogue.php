<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_spip_thelia_catalogue_dist()
{
	if (function_exists('debut_page')) {
		// SPIP Version 1.9.x
		debut_page(_T("spip_thelia:catalogue_thelia"), _T("spip_thelia:catalogue_thelia"), _T("spip_thelia:catalogue_thelia"));
	} else {
		// SPIP >= 2.0
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T("spip_thelia:catalogue_thelia"),_T("spip_thelia:catalogue_thelia"),_T("spip_thelia:catalogue_thelia"));
	}

	$thelia_url = '../'._THELIA_ADMIN.'/';
	if (_request('thelia_url')) $thelia_url .= _request('thelia_url');
	
	echo "<iframe src='$thelia_url' style='width:100%;height:600px;'></iframe>";
	echo fin_page();

}
?>
