<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 2.1 - 06/2011 - SPIP 2.1
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| D. Chiche . pour la maj 2.0
| T. Payet . pour la maj 2.1
| Script certifie KOAK2.0 strict, mais si !

+--------------------------------------------+
| formulaire de configuration
+--------------------------------------------+
*/

function formulaire_configuration_acjr() {

	# elements spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur;
	
	# auteur connecte
	$q=sql_select("nom FROM spip_auteurs WHERE id_auteur="._q($connect_id_auteur));
	$r=sql_fetch($q);
	$nom = typo($r['nom']);	

	$aff='';
	$aff.= "<p class='space_20'></p>";
	
	$aff.= "<form action='".generer_url_action('acjr_config')."' method='post'>"
		. "<input type='hidden' name='redirect' value='".generer_url_ecrire("actijour_conf")."' />\n"
		. "<input type='hidden' name='hash' value='".calculer_action_auteur("acjr_config-rien")."' />\n"
		. "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n"
		. "<input type='hidden' name='arg' value='rien' />\n"
		. "<input type='hidden' name='version_plug' value='".$GLOBALS['actijour_plug_version']."' />\n";
		
		
	$aff.= debut_cadre_trait_couleur('',true,'',_T('actijour:configuration_commune'))
		. "<table width='100%' cellpadding='2' cellspacing='0' border='0' class='table_conf'>"
		. "<tr><td class='col_info'>"._T('actijour:conf_nbl_art')."</td>"
		. "<td class='col_val'>"
		. "<input type='text' name='nbl_art' value='".$GLOBALS['actijour']['nbl_art']."' size='4' class='fondl' />"
		. "</td></tr>"
		. "<tr><td class='col_info'>"._T('actijour:conf_nbl_aut')."</td>"
		. "<td class='col_val'>"
		. "<input type='text' name='nbl_aut' value='".$GLOBALS['actijour']['nbl_aut']."' size='4' class='fondl' />"
		. "</td></tr>"
		. "<tr><td class='col_info'>"._T('actijour:conf_nbl_mensuel')."</td>"
		. "<td class='col_val'>"
		. "<input type='text' name='nbl_mensuel' value='".$GLOBALS['actijour']['nbl_mensuel']."' size='4' class='fondl' />"
		. "</td></tr>"
		. "<tr><td class='col_info'>"._T('actijour:conf_nbl_topsem')."</td>"
		. "<td class='col_val'>"
		. "<input type='text' name='nbl_topsem' value='".$GLOBALS['actijour']['nbl_topsem']."' size='4' class='fondl' />"
		. "</td></tr>"
		. "<tr><td class='col_info'>"._T('actijour:conf_nbl_topmois')."</td>"
		. "<td class='col_val'>"
		. "<input type='text' name='nbl_topmois' value='".$GLOBALS['actijour']['nbl_topmois']."' size='4' class='fondl' />"
		. "</td></tr>"
		. "<tr><td class='col_info'>"._T('actijour:conf_nbl_topgen')."</td>"
		. "<td class='col_val'>"
		. "<input type='text' name='nbl_topgen' value='".$GLOBALS['actijour']['nbl_topgen']."' size='4' class='fondl' />"
		. "</td></tr>"
		. "</table>"
		. fin_cadre_trait_couleur(true);
	
	$_ordon_pg_m = $GLOBALS['actijour']['admin-'.$connect_id_auteur]['ordon_pg_m'];
	$ordon_pg_m = ($_ordon_pg_m)?$_ordon_pg_m:'1,2,3,4';

	$aff.= debut_cadre_trait_couleur('',true,'',_T('actijour:configuration_perso', array('nom'=>$nom)))
		. "<table width='100%' cellpadding='2' cellspacing='0' border='0' class='table_conf'>"
		. "<tr><td class='col_info'>"._T('actijour:conf_ordon_milieu')."</td>"
		. "<td class='col_val'>"
		. "<input type='hidden' name='id_admin' value='$connect_id_auteur' />"
		. "<input type='text' name='ordon_pg_m' value='".$ordon_pg_m."' size='8' class='fondl' />"
		. "</td></tr>"
		. "</table>"
		. fin_cadre_trait_couleur(true);

	$aff.= "<div align='right'><input type='submit' value='"._T('valider')."' class='fondo' /></div>\n"
		. "</form>";
	
	return $aff;
}

?>
