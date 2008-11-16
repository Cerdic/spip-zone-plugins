<?php 

// inc/spiplistes_listes_selectionner_auteur.php

/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

//CP-20081116
// utilise par 
// - exec/spiplistes_liste_gerer.php
// - exec/spiplistes_abonnes_tous.php
// - action/spiplistes_listes_abonner_auteur.php
// - action/spiplistes_liste_des_abonnes.php
// - action/spiplistes_changer_statut_abonne.php

// selectionne les auteurs elligibles a un abonnement
// - adresse email obligatoire

define("_SPIPLISTES_LIGNES_PAR_PAGE", 10);
define("_SPIPLISTES_SELECT_MIN_AUTEURS", 50); // nb auteurs dans le <select>

//CP-20080603
// renvoie un tableau d'auteurs, du style
// $result['1comite'][$id_auteur] = nom
// + le nombre d'elligibles
// si $id_liste == 0, liste complete
// sinon, ceux non abonnes a la liste
// Si liste privee, uiquement redacs
// Si liste publique, tout le monde (sauf si deja abonne)
// $nb_non_abonnes = nombre d'elligibles trouves
// Pour recuperer le resultat :
//   list($non_abonnes, $nb_non_abonnes) = spiplistes_listes_auteurs_elligibles($id_liste, $statut);
function spiplistes_listes_auteurs_elligibles ($id_liste, $statut_liste = '', $faire = '') {
	$nb_auteurs = 0;
	$auteurs_array = array();
	if($lister_moderateurs = ($faire == 'moderer')) {
		// recupere la liste des moderateurs
		$ids_already = spiplistes_mod_listes_get_id_auteur($id_liste);
		$ids_already = (isset($ids_already[$id_liste]) ? $ids_already[$id_liste] : array());
		$sql_where[] = "statut=".sql_quote('0minirezo');
	}
	else {
		// recupere la liste des abonnes
		$ids_already = spiplistes_listes_liste_abo_ids($id_liste);
		// prepare la liste des non-abonnes elligibles
		$sql_where = array("email <> ''"); // email obligatoire !
		// si liste privee, ne prend que l'equipe de redacs
		if($statut_liste == _SPIPLISTES_PRIVATE_LIST) {
			$sql_where[] = "(statut=".sql_quote('0minirezo')." OR statut=".sql_quote('1comite').")";
		}
	}
	$sql_from = array("spip_auteurs");
	// demande la liste des elligibles
	$sql_result = sql_select("nom,id_auteur,statut", $sql_from, $sql_where, '', array('statut','nom'));
	if(sql_count($sql_result)) {
		while($row = sql_fetch($sql_result)) {
			// ne pas prendre ceux deja abonnes
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
// renvoie la boite en liste des abonnes a une liste
// si $id_liste == 0, liste tous les abonnements
function spiplistes_listes_boite_abonnes (
	$id_liste, $tri, $debut, $scrip_retour
	, $id_boite_dest_ajax = ''
	) {
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
			, $id_boite_dest_ajax
			)
		;
	return($boite_abonnes);
}

//CP-20080603
// met en forme le resultat de spiplistes_listes_auteurs_elligibles()
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
		// si un seul, activer plutot la selection par la souris 
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
	//  A voir si besoin d'adapter pour SPIP-Listes ? (trier les sans emails, les deja abonnes, etc.)
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
		. "<span><input type='submit' value='$clic' "
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
// la boite complete (abonnes et elligibles)
// fonction appele aussi par action pour resultat en ajax
/*
 * @param $elligibles transmis par action/ajax lors de la recherche, tableau des elligibles
 */
function spiplistes_listes_boite_abonnements ($id_liste, $statut_liste, $tri, $debut, $script_retour, $elligibles = null, $nb_elligibles = 0) {
	$nb = spiplistes_listes_nb_abonnes_compter($id_liste);
	$legend = _T('spiplistes:nbre_abonnes').$nb;
	$legend = "<small>".spiplistes_nb_abonnes_liste_str_get($id_liste)."</small>";
	$id_boite_dest_ajax = "grosse_boite_abonnements";
	$boite_abonnements = ""
		. "<div id='$id_boite_dest_ajax' class='verdana1'>\n"
		. "<div class='verdana2' id='legend-abos1-propre'>$legend</div>\n"
		. "<div id='auteurs'>\n"
		. spiplistes_listes_boite_abonnes($id_liste, $tri, $debut, $script_retour, $id_boite_dest_ajax)
		. "</div>\n"
		;
	if($elligibles === null) {
		// si pas transmis par ajax
		// demande la liste des elligibles
		list($elligibles, $nb_elligibles) = spiplistes_listes_auteurs_elligibles($id_liste, $statut_liste);
	}
	if(($nb_elligibles > 0)
		&& ($id_liste > 0) // ?exec=spiplistes_abonnes_tous a sa propre boite de recherche
	) {
		$boite_abonnements .= spiplistes_listes_selectionner_elligibles(
			$elligibles
			, $nb_elligibles
			, $id_liste
			, $tri
			, _SPIPLISTES_ACTION_ABONNER_AUTEUR
			, _SPIPLISTES_EXEC_LISTE_GERER
			, $id_boite_dest_ajax
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


function spiplistes_afficher_auteurs (
	$sql_select, $sql_from, $sql_where, $sql_group, $sql_order
	, $script_retour
	, $max_par_page = 10
	, $tri = 'nom'
	, $id_liste = 0
	, $debut = 0
	, $id_boite_dest_ajax = 'auteurs'
) {

	global 
		  $spip_lang_left
		, $spip_lang_right
		;

	$nombre_auteurs = sql_count(sql_select("COUNT(aut.id_auteur)", $sql_from, $sql_where, $sql_group));

	// reglage du debut
	if(!$debut) {
		// si js pas active, recupere dans l'url
		$debut = intval(_request('debut'));
	}
	if ($debut > $nombre_auteurs - $max_par_page) {
		$debut = max(0, $nombre_auteurs - $max_par_page);
	}
	
	$sql_result = sql_select($sql_select, $sql_from, $sql_where, $sql_group, $sql_order, $debut . ',' . $max_par_page);
	
	$auteurs = array();
	$les_auteurs = array();
	while ($auteur = sql_fetch($sql_result)) {
		if ($auteur['statut'] == '0minirezo') {
			$auteur['restreint'] = sql_count(sql_select(
				"*"
				, "spip_auteurs_rubriques"
				, "id_auteur=".sql_quote($auteur['id_auteur'])
				));
		}
		$auteurs[] = $auteur;
		$les_auteurs[] = $auteur['id_auteur'];
	}
		
	$lettres_onglet = array();
	
	if($nombre_auteurs > 10) { 
		// SELECT DISTINCT UPPER(LEFT(nom,1)) AS l, COUNT(*) AS n FROM spip_auteurs GROUP BY l ORDER BY l
		$sql_result = sql_select(
			array("DISTINCT UPPER(LEFT(nom,1)) AS l"
				, "COUNT(*) AS n")
			, "spip_auteurs" // FROM
			, $sql_where 
			, "l", array("l"));
		if($result === false) {
				spiplistes_log("DATABASE ERROR: [" . sql_errno() . "] " . sql_error());
		} 
		else {
			$count = 0;
			while ($row = sql_fetch($sql_result)) {
				$lettres_onglet[$row['l']] = $count;
				$count += intval($row['n']);
			}
		}
	}
	
	//////////////////////////////////
	// tableau des resultats
	$result = ""
		. "<table border='0' cellpadding='3' cellspacing='0' width='100%' class='arial2, spiplistes-abos'>\n"
		;
	
	// titres du tableau (a-la-SPIP, en haut)
	$icon_auteur = spiplistes_corrige_img_pack("<img src='"._DIR_IMG_PACK."/admin-12.gif' alt='' border='0' />");
	$id_liste_url = ($id_liste ? "&id_liste=$id_liste" : "");
	$result .= ""
		. "<tr bgcolor='#DBE1C5'>"
		//
		// #1: statut auteur (icone)
		. "<th width='20'>"
		.	(
			($tri=='statut')
			? $icon_auteur
			: "<a href='"
				. generer_url_ecrire($script_retour, "tri=statut".$id_liste_url)
				. "' title='"._T('lien_trier_statut')."'>$icon_auteur</a>"
			)
		. "</th>\n"
		// #2: nom
		.	"<th>"
		.	(
		 	($tri == '' || $tri=='nom')
			? _T('info_nom')
			: "<a href='"
				. generer_url_ecrire($script_retour,"tri=nom".$id_liste_url)
				. "' title='"._T('lien_trier_nom')."'>"._T('info_nom')."</a>"
			)
		. "</th>\n"
		//
		// #3: contact mail
		. "<th>"._T('email')
		. "</th>\n"
		//
		// #4: site
		.	"<th>"._T('info_site')
		. "</th>\n"
		//
		// #5: Format si abonne	
		.	"<th>"._T('spiplistes:format')
		. "</th>\n"
		;
		// #6: Nombre d'abonnements	
		$j = 0;
		// si js inactif, $exec_url prend le relais
		$exec_url = generer_url_ecrire($script_retour, "id_liste=$id_liste&tri=nombre&debut=$js");
		// sinon, ajax animera la boite des abos
		$action_url = generer_action_auteur(_SPIPLISTES_ACTION_LISTE_ABONNES
			, $id_liste." ".$j." nombre");
	$result .= ""
		.	"<th>"
		.	(
			($tri=='nombre')
			? _T('spiplistes:nb_abos')
			: "<a href='"
				. parametre_url($exec_url, 'redirect', $exec_url)."'"
				. " onclick=\"return AjaxSqueeze('$action_url', '$id_boite_dest_ajax', '$exec_url', event)\""
				. " title='"._T('spiplistes:lien_trier_nombre')."'>"._T('spiplistes:nb_abos')."</a>"
			)
		. "</th>\n"
		;
		// #7: Modifier l'abonnement
	$result .= ""
		.	"<th>"
		. _T('spiplistes:modifier')
		. "</th></tr>\n"
		;
	
	// onglets de pagination (si pagination)
	if ($nombre_auteurs > $max_par_page) {
		$result .= ""
			. "<tr class='onglets'><td colspan='7'>"
			;
		// onglets : affiche les chiffres 
		$result .= "<!-- onglets chiffres -->\n";
		for ($j=0; $j < $nombre_auteurs; $j+=$max_par_page) {
			if ($j > 0) $result .= " | ";
			
			if ($j == $debut) {
				$result .= "<strong>$j</strong>";
			} else {
				// si js inactif, $exec_url prend le relais
				$exec_url = generer_url_ecrire($script_retour, "id_liste=$id_liste&tri=$tri&debut=$j");
				// sinon, ajax animera la boite des abos
				$action_url = generer_action_auteur(_SPIPLISTES_ACTION_LISTE_ABONNES
					, $id_liste." ".$j." ".$tri);
				$result .= 
					"<a href='"
						. parametre_url($exec_url, 'redirect', $exec_url)
						. "' onclick=\"return AjaxSqueeze('$action_url', '$id_boite_dest_ajax', '$exec_url',event)\">"
						. $j
						. "</a>\n"
						;
			}
			
			if (($debut > $j)  && ($debut < $j+$max_par_page)) {
				$result .= " | <strong>$debut</strong>";
			}
		}
		$result .= ""
			. "</td></tr>\n"
			;
		// onglets : affichage des lettres
		//if ($tri == 'nom') {
			$result .= ""
				. "<tr class='onglets'><td colspan='7'>\n"
				. "<!-- onglets des lettres -->\n"
				;
			foreach ($lettres_onglet as $key => $val) {
				// si js inactif, $exec_url prend le relais
				$exec_url = generer_url_ecrire($script_retour, "id_liste=$id_liste&tri=$tri&debut=$val");
				// sinon, ajax animera la boite des abos
				$action_url = generer_action_auteur(_SPIPLISTES_ACTION_LISTE_ABONNES
					, $id_liste." ".$val." ".$tri);
				$result .= 
					($val == $debut)
					? "<strong>$key</strong> "
					: "<a href='"
						. parametre_url($exec_url, 'redirect', $exec_url)
						. "' onclick=\"return AjaxSqueeze('$action_url', '$id_boite_dest_ajax', '$exec_url',event)\">"
						. $key
						. "</a>\n"
					;
			}
			$result .= ""
				. "</td></tr>\n"
				;
		//}
		$result .= ""
			. "<tr height='5'></tr>"
			;
	}
	
	//translate extra field data
	list(,,,$trad,$val) = explode("|",_T("spiplistes:options")); 
	$trad = explode(",",$trad);
	$val = explode(",",$val);
	$trad_map = array();
	for($index_map=0;$index_map<count($val);$index_map++) {
		$trad_map[$val[$index_map]] = $trad[$index_map];
	}

	$a_title_abo = array(
		'html' =>  " title=\""._T('spiplistes:Abonner_format_html')."\""
		, 'texte' =>  " title=\""._T('spiplistes:Abonner_format_texte')."\""
		, 'desabo' =>  " title=\""._T('spiplistes:Desabonner')."\""
	);

	$ii = 1;
	
	//////////////////////////////////
	// ici commence la vraie boucle

	// les auteurs (la liste)
	foreach ($auteurs as $row) {
		// couleur de ligne
		$couleur_ligne = (($ii++) % 2) ? '#eee' : '#fff';

		$result .= ""
			. "<tr style='background-color: $couleur_ligne'>"
			//
			// #1: statut auteur (icone)
			. "<td>"
			. spiplistes_bonhomme_statut($row)
			. "</td>\n"
			//
			// #2: nom
			. "<td>"
			. "<a href='".generer_url_ecrire(_SPIPLISTES_EXEC_ABONNE_EDIT, "id_auteur=".$row['id_auteur'])."'>".typo($row['nom']).'</a>'
			.	(
				($connect_statut == '0minirezo' && $row['restreint'])
				? " &nbsp;<small>"._T('statut_admin_restreint')."</small>"
				: ""
				)
			. "</td>\n"
			//
			// #3: contact mail
			. "<td>"
			.	(
				(strlen($row['email'])>3)
				? "<a href='mailto:".$row['email']."'>"
					. spiplistes_corrige_img_pack("<img src='"._DIR_IMG_PACK."m_envoi_rtl.gif' alt='' /></a>")
				: "<span title='"._T('spiplistes:Pas_adresse_email')."'>&bull;</span>"
				)
			. "</td>\n"
			//
			// #4: site
			. "<td>"
			.	(
					(strlen($row['url_site'])>3)
					? "<a href='".$row['url_site']."' class='spip_out'>"._T('lien_site')."</a>"
					: "&nbsp;"
				)
			. "</td>\n"
			//
			// #5: Format si abonne	
			. "<td>"
			.	(
				(($abo = $row['format']) && (!empty($abo)) && ($abo != 'non'))
				? $trad_map[$abo]
				: "<span title='"._T('spiplistes:Sans_abonnement')."'> - </span>"
				)
			. "</td>\n"
			//
			// #6: nombre d'abonnement
			. "<td>"
			.	(
				($row['compteur'])
				? "<span class='spiplistes-legend-stitre'>".$row['compteur']."</span>"
				: ""
				)
			. "</td>\n"
			//
			// #7: Modifier l'abonnement
			. "<td>"
			. "<a name='abo".$row['id_auteur']."'></a>"
			;

		$exec_url = generer_url_ecrire($script_retour,"id_liste=$id_liste&debut=$debut&tri=$tri");
		$action_url = generer_action_auteur('spiplistes_changer_statut_abonne', $row['id_auteur']."-format", $exec_url);
		$action_url_ajax = generer_action_auteur('spiplistes_changer_statut_abonne', $row['id_auteur']."-format");
		$action_url_ajax = parametre_url($action_url_ajax, 'id_liste', $id_liste);
		$action_url_ajax = parametre_url($action_url_ajax, 'debut', $debut);
		$action_url_ajax = parametre_url($action_url_ajax, 'tri', $tri);
		
		$a_format = array('html' => "", 'texte' => "", 'non' => "");
		foreach(array_keys($a_format) as $key) {
			$legend = ($key == 'non') ? 'Desabonner' : $key;
			$a_format[$key] = ""
				. "<a ".$a_title_abo[$key]." href='"
					. parametre_url($action_url, 'statut', $key)
					. "' onclick=\"return AjaxSqueeze('"
					. parametre_url($action_url_ajax, 'statut', $key)
						."', '$id_boite_dest_ajax', '',event)\">"
					. _T('spiplistes:'.$legend)
					. "</a>\n"
				;		
		}
		$result .= ""
			. "&nbsp;"
				. $a_format[(in_array($abo, array('html','texte')) ? 'non' : 'texte')]
				. " | "
				. $a_format[(($abo == 'html') ? 'texte' : 'html')]
			. "</td></tr>\n"
			;
	} //
	
	$result .= ""
		. "</table>\n"
		;
		
	// fleche de pagination si besoin
	$debut_suivant = $debut + $max_par_page;
	
	if (($debut_suivant < $nombre_auteurs) || ($debut > 0)) {
		
		$exec_url = generer_url_ecrire($script_retour, "id_liste=$id_liste&tri=$tri&debut=$debut");
		
		$result .= ""
			. "<table id='bas' width='100%' border='0'>"
			. "<tr bgcolor='white'><td style='text-align: $spip_lang_left'>"
			;
		if ($debut > 0) {
			$debut_prec = strval(max($debut - $max_par_page, 0));
			$action_url = generer_action_auteur(_SPIPLISTES_ACTION_LISTE_ABONNES
				, $id_liste." ".$debut_prec." ".$tri);
			$result .= ""
				. "<a href='"
				. parametre_url($action_url, 'redirect', $exec_url)
				. "' onclick=\"return AjaxSqueeze('$action_url', '$id_boite_dest_ajax', '$exec_url',event)\">"
						. "&lt;&lt;&lt;"
						. "</a>\n"
				;
		}
		if($debut_suivant < $nombre_auteurs) {
			$action_url = generer_action_auteur(_SPIPLISTES_ACTION_LISTE_ABONNES
				, $id_liste." ".$debut_suivant." ".$tri);
			$result .= ""
				. "</td><td style='text-align: $spip_lang_right'\n"
				. "<!-- fleche suivante -->\n"
				. "<a href='"
				. parametre_url($action_url, 'redirect', $exec_url)
				. "' onclick=\"return AjaxSqueeze('$action_url', '$id_boite_dest_ajax', '$exec_url',event)\">"
						. "&gt;&gt;&gt;"
						. "</a>\n"
				;
		}
		$result .= ""
			. "</td></tr>\n"
			. "</table>\n"
			;
	} //
	
	return($result);
}

function spiplistes_bonhomme_statut ($row) {
	return(spiplistes_corrige_img_pack(bonhomme_statut($row)));
}

// Lorsqu'appele par ?action (ajax), perd la position
// corrige le lien relatif
function spiplistes_corrige_img_pack ($img) {
	if(preg_match(",^<img src='dist/images,", $img)) {
		$img = preg_replace(",^<img src='dist/images,", "<img src='../dist/images", $img);
	}
	return($img);
}

?>