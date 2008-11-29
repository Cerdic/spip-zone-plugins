<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_spip_thelia_catalogue_dist()
{
	debut_page(_T("spip_thelia:catalogue_thelia"), "Catalogue T&eacute;lia", "Catalogue T&eacute;lia");

	$thelia_url = '../'._THELIA_ADMIN.'/';
	if (_request('thelia_url')) $thelia_url .= _request('thelia_url');
	
	echo "<iframe src='$thelia_url' style='width:100%;height:600px;'></iframe>";
	echo fin_page();

}
?>