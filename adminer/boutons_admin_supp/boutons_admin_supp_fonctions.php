<?php
require find_in_path("boutons_supp_options.php");

function boutons_admin_supp_insert_head_css($flux) {
	static $done = false;
	if (!$done) {
		$done = true;
		switch($GLOBALS['style_barre']){
			case "a_droite":
				$flux .= "\n
<style type=\"text/css\">
	.spip-admin-boutons {
	display:list-item;
	list-style-type:none;
	}
</style>\n";
			break;
			case "translucide":
				$flux .= "\n
<style type=\"text/css\">
	.spip-admin-float, #bouton_montrer {
	opacity:".$GLOBALS['translucidite']." !important;
	}
</style>\n";
			break;
		}
	}
	return $flux;
}
function boutons_admin_supp_insert_head($flux) {
	$flux = boutons_admin_supp_insert_head_css($flux); // au cas ou il n'est pas implemente
	return $flux;
}
?>
