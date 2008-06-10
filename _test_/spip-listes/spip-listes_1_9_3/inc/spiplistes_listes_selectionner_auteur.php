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
function spiplistes_listes_auteurs_elligibles ($id_liste, $statut_liste = '', $faire = '') {
	$nb_auteurs = 0;
	$auteurs_array = array();
	if($lister_moderateurs = ($faire == 'moderer')) {
		// récupère la liste des modérateurs
		$ids_already = spiplistes_mod_listes_get_id_auteur($id_liste);
		$ids_already = (isset($ids_already[$id_liste]) ? $ids_already[$id_liste] : array());
		$sql_where[] = "statut=".sql_quote('0minirezo');
	}
	else {
		// récupère la liste des abonnés
		$ids_already = spiplistes_listes_liste_abo_ids($id_liste);
		// prépare la liste des non-abonnés elligibles
		$sql_where = array("email <> ''"); // email obligatoire !
		// si liste privée, ne prend que l'equipe de redacs
		if($statut_liste == _SPIPLISTES_PRIVATE_LIST) {
			$sql_where[] = "(statut=".sql_quote('0minirezo')." OR statut=".sql_quote('1comite').")";
		}
	}
	$sql_from = array("spip_auteurs");
	// demande la liste des elligibles
	$sql_result = sql_select("nom,id_auteur,statut", $sql_from, $sql_where, '', array('statut','nom'));
	if(sql_count($sql_result)) {
		while($row = sql_fetch($sql_result)) {
			// ne pas prendre ceux déjà abonnés
			if(in_array($row['id_auteur'], $ids_already)) {
				continue;
			}
			if(!isset($auteurs_array[$row['statut']])) {
				$auteurs_array[$row['statut']] = array();
			}
			$auteurs_array[$row['statut']][$row['id_auteur']] = $row['nom'];
			$nb_auteurs++;
		}
	}
	return(array($auteurs_array, $nb_auteurs));
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
function spiplistes_listes_selectionner_elligibles (
	$non_abonnes
	, $nb_non_abonnes
	, $id_liste
	, $tri
	, $script_action
	, $script_exec
	, $id_grosse_boite
	, $args_action = ''
) {
	static $bouton_id;
	$bouton_id++;
	$js = "findObj_forcer('valider_ajouter_abo_$bouton_id').style.visibility='visible';";
	if($nb_non_abonnes <= _SPIPLISTES_SELECT_MIN_AUTEURS){
		$t_statut = array(
			  '0minirezo' => _T('info_administrateurs')
			, '1comite' => _T('info_redacteurs')
			, '6forum' => _T('info_visiteurs')
			, '6visiteur' => _T('info_visiteurs')
		);
		// si un seul, activer plutot la sélection par la souris 
		// onchange n'est pas transmis si un seul 'option'
		$onevent = ($nb_non_abonnes == 1) ? "onmousedown" : "onchange";
		$select_abo = ""
			. "<span class='verdana1'>"._T('spiplistes:abon_ajouter')."</span>\n"
			. "<select name='select_abo' size='1' class='fondl' style='width:150px;' $onevent=\"$js\">\n";
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
	
	if(empty($args_action)) {
		$args_action = "id_liste=$id_liste";
	}
	$action = generer_action_auteur($script_action, $args_action);
	$retour = generer_url_ecrire($script_exec, "id_liste=$id_liste");

	return(
		  "<!-- form listes select elligibles -->\n"
		. "<form style='margin:0px; border:0px' action='$action' method='post'>\n"
		. "<div id='boite_selection_elligibles_$bouton_id' style='padding:0;margin:0.5em 0 0'>\n"
		. $select_abo
		. "<span><input type='submit' value='Ajouter' "
			. " onclick=\"return AjaxSqueeze(this.form, '$id_grosse_boite', '$retour', event)\" "
			. " class='fondo visible_au_chargement' id='valider_ajouter_abo_$bouton_id'/></span>\n"
		. "</div>\n"
		. "<input name='arg' type='hidden' value='$id_liste' />"
		. "<input name='action' type='hidden' value='".$script_action."' />"
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
		. "<div id='grosse_boite_abonnements' class='verdana1'>\n"
		. "<div id='auteurs'>\n"
		. spiplistes_listes_boite_abonnes($id_liste, $tri, $debut, $script_retour)
		. "</div>\n"
		;
	// demande la liste des elligibles
	list($non_abonnes, $nb_non_abonnes) = spiplistes_listes_auteurs_elligibles($id_liste, $statut_liste);
	if($nb_non_abonnes > 0) {
		$boite_abonnements .= spiplistes_listes_selectionner_elligibles(
			$non_abonnes
			, $nb_non_abonnes
			, $id_liste
			, $tri
			, _SPIPLISTES_ACTION_ABONNER_AUTEUR
			, _SPIPLISTES_EXEC_LISTE_GERER
			, 'grosse_boite_abonnements'
		);
	}
	$boite_abonnements .= ""
		. "</div>\n"
		;
	return($boite_abonnements);
}

//CP-20080610
// boite des moderateurs
function spiplistes_listes_boite_moderateurs ($id_liste, $script_retour, $id_conteneur) {
	$boite_moderateurs = ""
		. "<div id='grosse_boite_moderateurs' class='verdana1' style='width:100%;height:auto'>\n"
		. "<ul class='liste-moderateurs'>\n"
		;
	$sql_result = sql_select(
		"a.id_auteur,a.statut,a.nom"
		, array(
			"spip_auteurs AS a"
			, "spip_auteurs_mod_listes AS m"
		)
		, array(
			"a.id_auteur=m.id_auteur"
			, "m.id_liste=".sql_quote($id_liste)
		)
	);
	$ii = 1;
	$faire = 'supprimer';
	while($row = sql_fetch($sql_result)) {
		$id_auteur = $row['id_auteur'];
		$exec_url = generer_url_ecrire($script_retour, "id_liste=$id_liste&id_auteur=$id_auteur&faire=$faire");
		$action_url = generer_action_auteur(_SPIPLISTES_ACTION_MOD_GERER, $id_liste." ".$id_auteur." ".$faire);
		$couleur_ligne = (($ii++) % 2) ? '#eee' : '#fff';
		$boite_moderateurs .= ""
			. "<li style='background-color: $couleur_ligne'>\n"
			. "<span class='statut-nom'>".spiplistes_bonhomme_statut($row)
				. "<span class='nom'>&nbsp;".$row['nom'] . "</span>\n"
			. "</span>\n"
			. "<a class='supprim' href='"
						. parametre_url($exec_url, 'redirect', $exec_url)
						. "' onclick=\"return AjaxSqueeze('$action_url','$id_conteneur','$exec_url',event)\">"
						. _T('spiplistes:sup_mod')
						. "</a>\n"
			. "</li>\n"
			;
	}
	$boite_moderateurs .= ""
		. "</ul>\n"
		;
	// demande la liste des elligibles
	list($elligibles, $nb_elligibles) = spiplistes_listes_auteurs_elligibles($id_liste, $statut_liste, "moderer");
	if($nb_elligibles > 0) {
		$boite_moderateurs .= spiplistes_listes_selectionner_elligibles(
			$elligibles
			, $nb_elligibles
			, $id_liste
			, 'nom'
			, _SPIPLISTES_ACTION_MOD_GERER
			, $script_retour
			, 'grosse_boite_moderateurs'
			, "$id_liste $id_auteur ajouter"
			);
	}
	$boite_moderateurs .= ""
		. "</div>\n"
		;
	return($boite_moderateurs);
} //

?>