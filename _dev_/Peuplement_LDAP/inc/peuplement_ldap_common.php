<?php

/**
 * Affichage de la page 1
 *
 */
function genere_etape_1(){
	global $couleur_claire;
	// Cadre d'information sur la partie gauche
	debut_gauche();// Partie gauche de la page
	debut_boite_info();// Cadre d'information concernant le plugin
	echo propre(_T('peuplementldap:info_etape_1'));
	fin_boite_info();
	fin_gauche();
        
	// Formulaire sur la partie centrale
	debut_droite();
	$icone = "../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/personal.png";
	debut_cadre_relief();
	echo "<br />";
	echo generer_url_post_ecrire("peuplement_ldap");
	bandeau_titre_boite2(_T('peuplementldap:titre_form_etape_1'), $icone, $couleur_claire, "black");	
	echo "<input type='text' class='formo'  name='peuplement_ldap_filtre' value='" .$GLOBALS['peuplement_ldap_filtre_defaut']. "' />";
	echo "<input type='hidden' name='peuplement_ldap_etape' value='2' />";
	echo "<br />";
	echo "<div style='text-align:right'>";
	echo "<input type='submit' value='Valider' class='fondo' name='peuplement_ldap_btnvalider' />";
	echo "</div>";
	echo "</form>";
	fin_cadre_relief();
}


function genere_etape_2($entreesLdap){
	global $couleur_claire;
	// Cadre d'information sur la partie gauche
	debut_gauche();// Partie gauche de la page
	debut_boite_info();// Cadre d'information concernant le plugin
	echo propre(_T('peuplementldap:info_etape_2'));
	fin_boite_info();	
	// Formulaire sur la partie centrale
	debut_droite();
	$icone = "../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/personal.png";
	debut_cadre_relief();
	echo "<br />";
	echo generer_url_post_ecrire("peuplement_ldap");
	echo "<input type='hidden' name='peuplement_ldap_etape' value='3' />";
	echo "<input type='hidden' name='peuplement_ldap_filtre' value='"._request('peuplement_ldap_filtre')."' />";
	bandeau_titre_boite2(_T('peuplementldap:titre_form_etape_2'), $icone, $couleur_claire, "black");
	
	// Affichage de la liste des entrees issues de l'annuaire LDAP
	$table='';
	echo "<div style='text-align:right'><b>".$entreesLdap['count']."</b>"._T('peuplementldap:nombre_entrees')."</div>";
	for ($i=0;$i<count($entreesLdap)-1;$i++){
		$table[$i][0]="<input type='checkbox' name='ajouter_entree_".$i."' value='".$entreesLdap[$i]["dn"]."#".$entreesLdap[$i]["mail"][0]."#".$entreesLdap[$i]["mail"][1]."'/>";
        $table[$i][1]=$entreesLdap[$i]["cn"][0];
    	$table[$i][2]=$entreesLdap[$i]["mail"][0];
	}
	echo "<table width='100%' cellpadding='5' cellspacing='0' border='0'>";
	$largeurs = array('','','');
	$styles = array('arial11', 'arial1', 'arial1','arial1');
	echo afficher_liste($largeurs, $table, $styles);
	echo "</table>";
	
	echo "<br />";
	echo "<div style='text-align:right'>";
	echo "<input type='submit' value='"._T('peuplementldap:titre_btn_valid_filtre')."' class='fondo' name='peuplement_ldap_btnvaliderFiltre' />";
	echo "&nbsp;&nbsp;";
	echo "<input type='submit' value='"._T('peuplementldap:titre_btn_valid_selection')."' class='fondo' name='peuplement_ldap_btnvaliderSelection' />";
	
	echo "</div>";
	echo "</form>";
	fin_cadre_relief();	
}

function genere_etape_3(){
	global $couleur_claire;
	// Cadre d'information sur la partie gauche
	debut_gauche();// Partie gauche de la page
	debut_boite_info();// Cadre d'information concernant le plugin
	echo propre(_T('peuplementldap:info_etape_3'));
	fin_boite_info();
	
	
	
	// Formulaire sur la partie centrale
	debut_droite();
	$icone = "../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/personal.png";
	debut_cadre_relief();
	echo "<br />";
	echo generer_url_post_ecrire("peuplement_ldap");
	bandeau_titre_boite2(_T('peuplementldap:titre_form_etape_3'), $icone, $couleur_claire, "black");
}

function recherche_ldap($filtre){
	$resultat="";
	$peuplementldap_cnx = spip_connect_ldap();
	if (!$peuplementldap_cnx){ // Echec de la connexion à l'annuaire LDAP
		echo _T('avis_connexion_ldap_echec_1');
		echo "<div style='text-align:$spip_lang_right'>";
		echo "<input type='submit' value='Valider' class='fondo' name='peuplement_ldap_btnvalider' />";
		echo "</div>";
		return false;
	}
	else{
		$search=@ldap_search($peuplementldap_cnx, $GLOBALS["ldap_base"], $filtre, array("dn","cn","mail"));
		return @ldap_get_entries($peuplementldap_cnx,$search);
	}	
}

/**
* Insère les entrées a l'aide de la méthode auth_ldap_inserer de Spip
* Retourne le résultat de l'insertion :
* 0 Echec
* 1 Identifiant existant déjà
* 2 Ok
*/
function insere_auteur($dn,$mail){
        // Controle qu'un identifiant de connexion identique ne soit pas déjà présent
        $cpt = spip_num_rows(spip_query("SELECT * FROM spip_auteurs WHERE email=\"".strtolower($mail)."\""));
        if ($cpt > 0){
                return 1;
        }
        if (auth_ldap_inserer($dn, $GLOBALS['meta']["ldap_statut_import"]))
                return 2;
        else
                return 0;
}

?>