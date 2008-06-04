<?php 

// inc/spiplistes_listes_selectionner_auteur.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

//CP-20080603
// utilisé par exec/spiplistes_liste_gerer.php

// sélectionne les auteurs elligibles à un abonnement
// - adresse email obligatoire

define("_SPIPLISTES_LIGNES_PAR_PAGE", 10);
define("_SPIPLISTES_SELECT_MIN_AUTEURS", 50); // nb auteurs dans le <select>

//CP-20080603
// renvoie un tableau d'auteurs, du style
// $result['1comite'][$id_auteur] = nom
// + le nombre d'elligibles
// si $id_liste == 0, liste complète
// sinon, ceux non abonnés à la liste
// Si liste privée, uiquement redacs
// Si liste publique, tout le monde (sauf si déjà abonné)
// $nb_non_abonnes = nombre d'elligibles trouvés
// Pour récupérer le résultat :
//   list($non_abonnes, $nb_non_abonnes) = spiplistes_listes_auteurs_elligibles($id_liste, $statut);
function spiplistes_listes_auteurs_elligibles ($id_liste = 0, $statut_liste = '') {
	$nb_non_abonnes = 0;
	$non_abonnes = array();
	$ids_abos =
		($id_liste > 0)
		// récupère la liste des abonnés
		? spiplistes_listes_liste_abo_ids($id_liste)
		: array()
		;
	$sql_from = array("spip_auteurs");
	// prépare la liste des non-abonnés elligibles
	$sql_where = array("email <> ''"); // email obligatoire !
	// si liste privée, ne prend que l'equipe de redacs
	if($statut_liste == _SPIPLISTES_PRIVATE_LIST) {
		$sql_where[] = "(statut=".sql_quote('0minirezo')." OR statut=".sql_quote('1comite').")";
	}
	// demande la liste des elligibles
	$sql_result = sql_select("nom,id_auteur,statut", $sql_from, $sql_where, '', array('statut','nom'));
	if(sql_count($sql_result)) {
		while($row = sql_fetch($sql_result)) {
			// ne pas prendre ceux déjà abonnés
			if(in_array($row['id_auteur'], $ids_abos)) {
				continue;
			}
			if(!isset($non_abonnes[$row['statut']])) {
				$non_abonnes[$row['statut']] = array();
			}
			$non_abonnes[$row['statut']][$row['id_auteur']] = $row['nom'];
			$nb_non_abonnes++;
		}
	}
	return(array($non_abonnes, $nb_non_abonnes));
}

//CP-20080603
// renvoie la boite en liste des abonnés à une liste
// si $id_liste == 0, liste tous les abonnements
function spiplistes_listes_boite_abonnes ($id_liste, $tri, $debut, $scrip_retour) {
	$id_liste = intval($id_liste);
	$sql_where = array("aut.statut <> ".sql_quote('5poubelle'));
	switch ($tri) {
		case 'statut':
			$sql_order = array('statut','login','unom');
			break;
		case 'email':
			$sql_order = array('LOWER(email)');
			break;
		case 'nombre':
			$sql_order = array('compteur DESC','unom');
			break;
		case 'nom':
		default:
			$sql_order = array('unom');
	}
	if($id_liste > 0) {
		$sql_where[] = "lien.id_liste=".sql_quote($id_liste);
	}
	$sql_select = "
		aut.id_auteur AS id_auteur,
		aut.statut AS statut,
		aut.login AS login,
		aut.nom AS nom,
		aut.email AS email,
		aut.url_site AS url_site,
		aut.messagerie AS messagerie,
		fmt.`spip_listes_format` AS format,
		UPPER(aut.nom) AS unom,
		COUNT(lien.id_liste) as compteur";
	$sql_from = "spip_auteurs AS aut
		LEFT JOIN spip_auteurs_listes AS lien ON aut.id_auteur=lien.id_auteur
		LEFT JOIN spip_listes AS liste ON (lien.id_liste = liste.id_liste)
		LEFT JOIN spip_auteurs_elargis AS fmt ON aut.id_auteur=fmt.id_auteur";
	$sql_group = 'aut.id_auteur';
	$boite_abonnes = ""
		. spiplistes_afficher_auteurs(
			  $sql_select, $sql_from, $sql_where, $sql_group, $sql_order
			, $scrip_retour
			, _SPIPLISTES_LIGNES_PAR_PAGE
			, $tri
			, $id_liste
			, $debut
			)
		;
	return($boite_abonnes);
}

//CP-20080603
// met en forme le résultat de spiplistes_listes_auteurs_elligibles()
// retourne liste des elligibles sous forme de select, selecteur_spip ou recherche 
// si trop nombreux.
function spiplistes_listes_selectionner_elligibles ($non_abonnes, $nb_non_abonnes, $id_liste, $tri) {

	$js = "findObj_forcer('valider_ajouter_abo').style.visibility='visible';";
	if($nb_non_abonnes <= _SPIPLISTES_SELECT_MIN_AUTEURS){
		$t_statut = array(
			  '0minirezo' => _T('info_administrateurs')
			, '1comite' => _T('info_redacteurs')
			, '6forum' => _T('info_visiteurs')
			, '6visiteur' => _T('info_visiteurs')
		);
		$select_abo = ""
			. "<span class='verdana1'>"._T('spiplistes:abon_ajouter')."</span>\n"
			. "<select name='select_abo' size='1' class='fondl' style='width:150px;' onchange=\"$js\">\n";
		foreach($non_abonnes as $key => $values) {
			$select_abo .= "<optgroup label=\"".$t_statut[$key]."\" style='background-color: $couleur_claire;'>\n";
			foreach($values as $id => $nom) {
				$select_abo .= "<option value='$id'>$nom</option>\n";
			}
			$select_abo .= "</optgroup>\n";
		}
		$select_abo .= ""
			. "</select>\n"
			;
		$clic = _T('bouton_ajouter');
	// Il existe sous SPIP une autre boite selecteur lorsque le nombre d'auteurs > 10
	//  A voir si besoin d'adapter pour SPIP-Listes ? (trier les sans emails, les déja abonnés, etc.)
	// En attendant, au dela de _SPIPLISTES_SELECT_MIN_AUTEURS, affiche la boite de recherche.
	//
	//} else if((_SPIP_AJAX < 1) || ($nb_non_abonnes >= _SPIP_SELECT_MAX_AUTEURS)) {
	} else {
		  $select_abo = "$text <input type='text' name='cherche_auteur' onclick=\"$js\" class='fondl' value='' size='20' />";
		  $clic = _T('bouton_chercher');
	} 
	/*else {
		 $select_abo = selecteur_auteur_ajax($type, $id, $js, _T('spiplistes:abon_ajouter'));
		 $clic = _T('bouton_ajouter');
	}*/
	
	$action = generer_action_auteur(_SPIPLISTES_ACTION_ABONNER_AUTEUR, "id_liste=$id_liste");
	$retour = generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "id_liste=$id_liste");

	return(
		  "<!-- form listes select elligibles -->\n"
		. "<form style='margin:0px; border:0px' action='$action' method='post'>\n"
		. "<div id='boite_selection_elligibles'>\n"
		. $select_abo
		. "<span><input type='submit' value='Ajouter' "
			. " onclick=\"return AjaxSqueeze(this.form, 'grosse_boite_abonnements', '$retour', event)\" "
			. " class='fondo visible_au_chargement' id='valider_ajouter_abo'/></span>\n"
		. "</div>\n"
		. "<input name='arg' type='hidden' value='$id_liste' />"
		. "<input name='action' type='hidden' value='"._SPIPLISTES_ACTION_ABONNER_AUTEUR."' />"
		. "<input name='redirect' type='hidden' value='".$retour."' />"
		. "<input name='id_liste' type='hidden' value='".$id_liste."' />"
		. "<input name='tri' type='hidden' value='".$tri."' />"
		. "</form>\n"
	);
} //

//CP20080603
// la boite complète (abonnés et elligibles)
// fonction appelé aussi par action pour resultat en ajax
function spiplistes_listes_boite_abonnements ($id_liste, $statut_liste, $tri, $debut, $script_retour) {
	$boite_abonnements = ""
		. "<div id='auteurs'>\n"
		. spiplistes_listes_boite_abonnes($id_liste, $tri, $debut, $script_retour)
		. "</div>\n"
		;
	// demande la liste des elligibles
	list($non_abonnes, $nb_non_abonnes) = spiplistes_listes_auteurs_elligibles($id_liste, $statut_liste);
	if($nb_non_abonnes > 0) {
		$boite_abonnements .= spiplistes_listes_selectionner_elligibles($non_abonnes, $nb_non_abonnes, $id_liste, $tri);
	}
	return($boite_abonnements);
}

?>