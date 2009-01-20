<?php

/**
 * Affichage de la page 1
 *
 */
function genere_etape_1(){
	global $couleur_claire;
	// Cadre d'information sur la partie gauche
	echo debut_gauche("",true); // Partie gauche de la page
	echo debut_boite_info(true);// Cadre d'information concernant le plugin
	echo propre(_T('peuplementldap:info_etape_1'));
	echo fin_boite_info(true);
        
	// Formulaire sur la partie centrale
	echo debut_droite('',true);
	$icone = "../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$GLOBALS['peuplement_ldap_icon'];
	echo debut_cadre_relief($icone,true,'',_T('peuplementldap:titre_form_etape_1'));
  	echo "<form action=\"".generer_url_ecrire("peuplement_ldap")."\" method=\"post\" />";
	echo "<input type='text' class='formo'  name='peuplement_ldap_filtre' value='" .$GLOBALS['peuplement_ldap_filtre_defaut']. "' />";
	echo "<input type='hidden' name='peuplement_ldap_etape' value='2' />";
	echo "<br />";
	echo "<div style='text-align:right'>";
	echo "<input type='submit' value='"._T('peuplementldap:titre_btn_valider')."' class='fondo' name='peuplement_ldap_btnvalider' />";
	echo "</div>";
	echo "</form>";
	echo fin_cadre_relief(true);
	echo "<br />";
	echo debut_boite_alerte();
	echo "<center>";
	echo _T('peuplementldap:attention_au_filtre');
	echo "</center>";
	echo fin_boite_alerte();
}


function genere_etape_2($entreesLdap){
	global $couleur_claire;
	// Cadre d'information sur la partie gauche
	echo debut_gauche("",true);// Partie gauche de la page
	echo debut_boite_info(true);// Cadre d'information concernant le plugin
	echo propre(_T('peuplementldap:info_etape_2'));
	echo fin_boite_info(true);	
	// Formulaire sur la partie centrale
	echo debut_droite("",true);
	$icone = "../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$GLOBALS['peuplement_ldap_icon'];
	echo debut_cadre_relief($icone,true,'',_T('peuplementldap:titre_form_etape_2'));
	echo "<form action=\"".generer_url_ecrire('peuplement_ldap')."\" method=\"post\" >";
	echo "<input type='hidden' name='peuplement_ldap_etape' value='3' />";
	echo "<input type='hidden' name='peuplement_ldap_filtre' value='"._request('peuplement_ldap_filtre')."' />";	
	// Affichage de la liste des entrees issues de l'annuaire LDAP
	echo "<div style='text-align:right'><b>".$entreesLdap['count']."</b>"._T('peuplementldap:nombre_entrees')."</div>";
	echo "<table  class='arial2'  cellpadding='2' cellspacing='0' style='width: 100%; border: 0px;'>";
	for ($i=0;$i<count($entreesLdap)-1;$i++){
		echo "<tr class=\"tr_liste\">";
		echo "<td><input type='checkbox' name='ajouter_entree_".$i."' value='".$entreesLdap[$i]["dn"]."#".$entreesLdap[$i]["mail"][0]."#".$entreesLdap[$i]["cn"][0]."'/></td>";
        echo "<td>".$entreesLdap[$i]["cn"][0]."</td>";
    	echo "<td>".$entreesLdap[$i]["mail"][0]."</td>";
		echo "</tr>";
	}
	echo "</table>";
	
	echo "<br />";
	echo "<div style='text-align:right'>";
	echo "<input type='submit' value='"._T('peuplementldap:titre_btn_valid_filtre')."' class='fondo' name='peuplement_ldap_btnvaliderFiltre' />";
	echo "&nbsp;&nbsp;";
	echo "<input type='submit' value='"._T('peuplementldap:titre_btn_valid_selection')."' class='fondo' name='peuplement_ldap_btnvaliderSelection' />";
	
	echo "</div>";
	echo "</form>";
	echo fin_cadre_relief(true);
}

function genere_etape_3($compte_rendu){
	global $couleur_claire;
	// Cadre d'information sur la partie gauche
	debut_gauche();// Partie gauche de la page
	debut_boite_info();// Cadre d'information concernant le plugin
	echo propre(_T('peuplementldap:info_etape_3'));
	fin_boite_info();
	echo "<br /><br />";
	debut_boite_info();
	echo affiche_legende();
	fin_boite_info();
	// Compte rendu des insertions d'auteur
	debut_droite();
	$icone = "../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$GLOBALS['peuplement_ldap_icon'];
	debut_cadre_relief();
	echo "<br />";
	bandeau_titre_boite2(_T('peuplementldap:titre_form_etape_3'), $icone, $couleur_claire, "black");
	echo "<br />";
	echo afficher_liste_debut_tableau();
	$largeurs = array('','','','');
	$styles = array('arial11', 'arial1', 'arial1');
	echo afficher_liste($largeurs, $compte_rendu, $styles);
	echo afficher_liste_fin_tableau();
}

function recherche_ldap($filtre){
	$resultat="";
	$ldap = spip_connect_ldap();
    $ldap_link = $ldap['link'];
    $ldap_base = $ldap['base'];
	if (!$ldap_link){ // Echec de la connexion à l'annuaire LDAP
		echo _T('avis_connexion_ldap_echec_1');
		echo "<div style='text-align:$spip_lang_right'>";
		echo "<input type='submit' value='Valider' class='fondo' name='peuplement_ldap_btnvalider' />";
		echo "</div>";
		return false;
	}
	else{
		$search=@ldap_search($ldap_link, $GLOBALS["ldap_base"], $filtre, array("dn","cn","mail"));
		return @ldap_get_entries($ldap_link,$search);
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

/*
 * En fonction de $id_result (resultat de la fonction insere_auteur),
 * renvoi le nom de l'image a utiliser pour l'affichage du compte-rendu.
 */
function getImage($id_result){
	switch($id_result){
		case 0: return $GLOBALS['peuplement_ldap_insert_ko'];
		case 1: return $GLOBALS['peuplement_ldap_insert_doublon'];
		case 2: return $GLOBALS['peuplement_ldap_insert_ok'];
	}
}


function affiche_legende(){
	$legende = "<strong>"._T('peuplementldap:legende')."</strong><br />";
	$legende.= "<img src=\"../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$GLOBALS['peuplement_ldap_insert_ok']."\" />&nbsp;"._T('peuplementldap:legende_ok')."<br />";
	$legende.= "<img src=\"../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$GLOBALS['peuplement_ldap_insert_doublon']."\" />&nbsp;"._T('peuplementldap:legende_doublon')."<br />";
	$legende.= "<img src=\"../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$GLOBALS['peuplement_ldap_insert_ko']."\" />&nbsp;"._T('peuplementldap:legende_ko')."<br />";
	
	return $legende;
}


?>