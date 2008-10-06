<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifi� KOAK2.0 strict, mais si !
+--------------------------------------------+
| package mes_options.
+--------------------------------------------+
*/

//
// fonction appelee a chaque hit backoffice :
//

# securite manuelle
# Pour bloquer tout risque d'inclusion
# de Doc (type jpg, png) part une mauvaise manip sur page config :
# commenter la ligne (39) : $arg=$params['criteres_auto_doc'];

// rev. h.02/02/07
function inclus_auto_doc() {
	if (!function_exists('sql_showtable')) {include_spip('base/abstract_sql');}
	$a = sql_showtable("spip_dw2_config",true);

	if($a) {
		$q=sql_query("SELECT nom, valeur FROM spip_dw2_config");
		$params=array();
		while($r=sql_fetch($q)) {
			$params[$r['nom']] = $r['valeur'];
		}
		if($params['mode_enregistre_doc']=="auto") {
			include_spip('inc/dw2_inc_ajouts');
			$arg='';
			$typecat=$params['type_categorie'];
			if(isset($params['criteres_auto_doc'])) {
				// a commenter
				$arg=$params['criteres_auto_doc'];
			}
			calc_inclus_auto_doc($arg,$typecat);
		}
		// h.03/03/07 -- on en profite pour declarer, (mes_fonctions)(public) le param :
		// - forcer les url_document vers url DW2 (type url_doc_out)
		$GLOBALS['forcer_url_dw2']=$params['forcer_url_dw2'];
	}
}

// Inclure nouveaux documents dans DW2
inclus_auto_doc();

?>