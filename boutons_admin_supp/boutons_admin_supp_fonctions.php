<?php
require find_in_path("boutons_supp_options.php");

function boutons_admin_supp_insert_head($flux) {
switch($GLOBALS['style_barre']){
	case "a_droite":
		$css = "\n
<style type=\"text/css\">
	.spip-admin-boutons {
	display:list-item;
	list-style-type:none;
	}
</style>\n";
		break;

	case "translucide":
		$css = "\n
<style type=\"text/css\">
	.spip-admin-float, #bouton_montrer {
	opacity:".$GLOBALS['translucidite']." !important;
	}
</style>\n";
		break;
}
	return $css.$flux;
}

?>
