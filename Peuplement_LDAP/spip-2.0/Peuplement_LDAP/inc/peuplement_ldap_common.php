<?php

/**
 * IHM de l'etape 1 de l'importation des auteurs.
 * 
 * 
 * Affichage du formulaire de saisie du filtre Ldap
 */
function genere_etape_1(){
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

/**
 * IHM de l'etape 2 de l'importation
 *
 * Affichage des entrées Ldap correspondant au filtre
 * 
 * @param Array $entreesLdap
 */
function genere_etape_2($entreesLdap){
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
	// Cadre d'information sur la partie gauche
	echo debut_gauche("",true);// Partie gauche de la page
	echo debut_boite_info(true);// Cadre d'information concernant le plugin
	echo propre(_T('peuplementldap:info_etape_3'));
	echo fin_boite_info(true);
	echo "<br /><br />";
	echo debut_boite_info(true);
	echo affiche_legende();
	echo fin_boite_info(true);
	echo debut_droite("",true);
	// Compte rendu des insertions d'auteur
	$icone = "../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$GLOBALS['peuplement_ldap_icon'];
	echo debut_cadre_relief($icone,true,'',_T('peuplementldap:titre_form_etape_3'));
	echo "<table  class='arial2'  cellpadding='2' cellspacing='0' style='width: 100%; border: 0px;'>";
	foreach ($compte_rendu as $unAuteur){
		echo "<tr class=\"tr_liste\">";
        echo "<td>".$unAuteur[0]."</td>";
    	echo "<td>".$unAuteur[1]."</td>";
    	echo "<td>".$unAuteur[2]."</td>";
		echo "</tr>";
	}
	echo "</table>";
	
	echo fin_cadre_relief(true);
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
		ldap_sort($ldap_link,$search,"cn"); // Tri des données sur le cn
		return @ldap_get_entries($ldap_link,$search);
	}	
}

/**
* Insère les entrées 
* Retourne le résultat de l'insertion :
* 0 Echec
* 1 Identifiant existant déjà
* 2 Ok
*/
function insere_auteur($dn,$mail){
		// Controle qu'un identifiant de connexion identique ne soit pas déjà présent

	if (sql_countsel("spip_auteurs","email=".sql_quote(strtolower($mail))))
                return 1;

	if ($GLOBALS['meta']["ldap_statut_import"]
	AND $desc = auth_ldap_retrouver($dn, array('login' => 'uid', 'nom' => 'cn', 'email' => 'mail'))) {
	  // rajouter le statut indique  a l'install
		$desc['statut'] = $GLOBALS['meta']["ldap_statut_import"];
		$desc['source'] = 'ldap';
		$desc['pass'] = '';

		if (sql_insertq('spip_auteurs', $desc)) return 2;
	}
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
	$legende.= "<img src=\""._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$GLOBALS['peuplement_ldap_insert_ok']."\" />&nbsp;"._T('peuplementldap:legende_ok')."<br />";
	$legende.= "<img src=\""._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$GLOBALS['peuplement_ldap_insert_doublon']."\" />&nbsp;"._T('peuplementldap:legende_doublon')."<br />";
	$legende.= "<img src=\""._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$GLOBALS['peuplement_ldap_insert_ko']."\" />&nbsp;"._T('peuplementldap:legende_ko')."<br />";
	
	return $legende;
}


?>