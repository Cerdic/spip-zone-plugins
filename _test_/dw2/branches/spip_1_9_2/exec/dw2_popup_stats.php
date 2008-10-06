<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Popup - Aff. Statistiques telechargement 
| pour un document.
+--------------------------------------------+
*/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_popup_stats() {

	// elements spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;
	
	
	// function requises ...
	include_spip("inc/dw2_inc_admin");
	include_spip("inc/dw2_inc_func");
	include_spip("inc/dw2_inc_pres");
	
	
	// reconstruire .. var=val des get et post
	// var : 
	// .. Option .. utiliser : $var = _request($var);
	foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
	foreach($_POST as $k => $v) { $$k=$_POST[$k]; }

	$id_document = intval($id_document);
	
#h.09/03 adaptation 1.9.2
##
include_spip('inc/headers');
http_no_cache();
include_spip('inc/commencer_page');
# + echo sur fonction :
	echo init_entete('graph document : '.$id_document,'');
##	
	echo "<body>\n";
	
	echo "<div style='padding:10px; text-align:center;'>\n";

		include(_DIR_PLUGIN_DW2."/inc/dw2_inc_stats.php");
	
	echo "</div>\n</body>\n</html>";

}

?>
