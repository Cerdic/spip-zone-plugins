<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| fonction outils :
| 
+--------------------------------------------+
*/

function netcat() {
// elements spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;
	
	include_spip('inc/date');

	// config
	$nbr_lignes_tableau = $GLOBALS['dw2_param']['nbr_lignes_tableau'];
	
	
	// reconstruire .. var=val des get et post
	// var :   
	// .. Option .. utiliser : $var = _request($var);
	foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
	foreach($_POST as $k => $v) { $$k=$_POST[$k]; }


//
// prepa
//
// total des types fichiers du catalogue
$q=spip_query("SELECT SUBSTRING_INDEX(url, '.', -1) AS typefich, COUNT(*) AS nbtype ".
			"FROM spip_dw2_doc GROUP BY typefich");
while($r=spip_fetch_array($q)) {
	$tbl_typefichier[]=$r['typefich'];
}


if($date) {echo $date;}

//
// affichage
//
debut_cadre_trait_couleur("warning-24.gif", false, "", _T('dw:supprimer_doc_du_catalogue'));

	echo _T('dw:supprimer_doc_du_catalogue_info')."<br />";

	echo "<form action ='".generer_url_action("dw2actions", "arg=netcat-rien")."' method='post'>";
	
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_outils", "outil=netcat")."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-netcat-rien")."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
	
	echo debut_boite_filet('a');
	echo "<input type='checkbox' name='choixselect' value='date' />&nbsp;".
		_T('dw:supprimer_doc_a_date')."<br />";
	
	echo "<div style='margin: 5px; margin-$spip_lang_left: 20px;'>" .
		 afficher_jour($jour, "name='jour' size='1' class='fondl' ", true) .
		 afficher_mois($mois, "name='mois' size='1' class='fondl' ", true) .
		 afficher_annee($annee, "name='annee' size='1' class='fondl' ").
		 ' - ' .
		 afficher_heure($heure, "name='heure' size='1' class='fondl' ") .
		 afficher_minute($minute, "name='minute' size='1' class='fondl' ");
	echo "</div>";
	echo fin_bloc();
	/*
	echo debut_boite_filet('a');
	echo "<div style='margin: 5px; margin-$spip_lang_left: 20px;'>";
	echo "<input type='checkbox' name='choixselect[]' value='tout' checked='checked' />&nbsp;".
		_L('De type : ')."";
	echo "&nbsp;<input type='checkbox' name='it_type[]' value='1' />&nbsp;"._T('dw:cfg_criteres_auto_doc_val_1');
	echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='it_type[]' value='2' />&nbsp;"._T('dw:cfg_criteres_auto_doc_val_2');
	echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='it_type[]' value='3' />&nbsp;"._T('dw:cfg_criteres_auto_doc_val_3');
	echo "</div>";
	echo fin_bloc();
	*/
	echo "<div align='right'>
		<input type='submit' class='fondo' value='". _T('dw:valider')."' />
		</div>";
	echo fin_bloc();
	
	echo "</form>";

fin_cadre_trait_couleur();
}

?>
