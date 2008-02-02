<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_spip_thelia_catalogue_dist()
{
	debut_page(_T("spip_thelia:catalogue_thelia"), "Catalogue T&eacute;lia", "Catalogue T&eacute;lia");

	echo "<iframe src='../admin' style='width:100%;height:600px;'></iframe>";
	echo fin_page();

}
?>