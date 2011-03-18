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

/*CP-2200081116
 * pour l'ajout abonne', il y a trois ajax boites imbriquées
 * A - la grosse pour la liste des abonnés et le champ des elligibles
 * B - la plus petite dans la grosse pour juste la liste des abonnés
 * C - sous la B, le champ des elligibles
 * Lorsqu'on ajoute un abonné, A est mis à jour
 * Lorsqu'on change un format, B seul est mis à jour
 * Lorsqu'on cherche un elligible, seul C est mis à jour
 */
define("_SPIPLISTES_ID_GROSSE_BOITE", "grosse_boite_abonnements");
define("_SPIPLISTES_ID_PETITE_BOITE", "petite_boite_abonnements");
define("_SPIPLISTES_ID_FROM_ELLIGIBL", "form-recherche-abo-elligibles");

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
		if($statut_liste == _SPIPLISTES_LIST_PRIVATE) {
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

/*
 * CP-20080603
 * @return la boite en liste des abonnes a une liste
 * 	si $id_liste == 0, liste tous les abonnements
 * @param $id_liste entier
 * @param $statut_liste staut ou false
 * @param $tri string: 'statut', 'nom', ou 'nombre' (qte)
 * @param $debut id_auteur du premier affiche
 * @param $script_retour string
 */
function spiplistes_listes_boite_abonnes ($id_liste, $statut_liste, $tri, $debut, $script_retour) {

	global $spip_lang_left, $spip_lang_right;
	
	$id_liste = intval($id_liste);
	$legende_tableau = '';

	// construction de la req SQL
	$sql_select = "
		aut.id_auteur AS id_auteur,
		aut.statut AS statut,
		aut.login AS login,
		aut.nom AS nom,
		aut.email AS email,
		aut.url_site AS url_site,
		fmt.`spip_listes_format` AS format,
		UPPER(aut.nom) AS unom,
		COUNT(lien.id_liste) as compteur";
	$sql_from = "
		spip_auteurs AS aut
		LEFT JOIN spip_auteurs_listes AS lien ON aut.id_auteur=lien.id_auteur
		LEFT JOIN spip_listes AS liste ON (lien.id_liste = liste.id_liste)
		LEFT JOIN spip_auteurs_elargis AS fmt ON aut.id_auteur=fmt.id_auteur";
	$sql_where = array("aut.statut <> ".sql_quote('5poubelle'));
	if($id_liste > 0) {
		$sql_where[] = "lien.id_liste=".sql_quote($id_liste);
	}
	$sql_group = 'aut.id_auteur';
	switch ($tri) {
		case 'statut':
			$sql_order = array('statut','login','unom');
			break;
		case 'nombre':
			$sql_order = array('compteur DESC','unom');
			break;
		default:
			$tri = 'nom';
			$sql_order = array('unom');
	}

	$nb_auteurs = sql_countsel('spip_auteurs');
	
	if($sql_result = sql_select(array('id_auteur', 'format'), 'spip_auteurs_listes'))
	{
		$abonnes = array();
		while($row = sql_fetch($sql_result))
		{
			if(!isset($abonnes[$row['id_auteur']])) { 
				$abonnes[$row['id_auteur']] = array();
				$nb_abonnes++; 
			}
			$abonnes[$row['id_auteur']][$row['format']]++;
			$abonnes[$row['format']]++;
		}
	}
	
	if(!$id_liste)
	{
		$legende_tableau = trim(spiplistes_str_auteurs($nb_auteurs)) 
		. ', '
		. _T('spiplistes:_dont_')
		. spiplistes_str_abonnes ($nb_abonnes) 
		;
		if(isset($abonnes['non']) && $abonnes['non'])
		{
			$legende_tableau .= _T('spiplistes:_dont_n_sans_format_reception', array('n' => $abonnes['non']));
		}
		else if($nb_abonnes)
		{
			$legende_tableau .= _T('spiplistes:_avec_');
		}
		$legende_tableau .= 
			($ii = intval($abonnes['html']))
			? spiplistes_str_abonnements(intval($abonnes['html'])) . _T('spiplistes:_au_format_s', array('s' => _T('spiplistes:html')))
			: ''
			;
		$legende_tableau .= 
			($jj = intval($abonnes['texte']))
			? ($ii?', ':''). spiplistes_str_abonnements(intval($abonnes['texte'])) . _T('spiplistes:_au_format_s', array('s' => _T('spiplistes:texte')))
			: ''
			;
		$legende_tableau .= ''
			. '.'
			;
	}
	
	$nombre_abonnes = 
		($id_liste > 0)
		? spiplistes_abonnements_compter($id_liste ? "id_liste=".sql_quote($id_liste) : "")
		// demande inventaire complet des auteurs (liste abonnes_tous)
		: sql_countsel("spip_auteurs")
		;

	// reglage du debut
	if(!$debut) {
		// si js pas active, recupere dans l'url
		$debut = intval(_request('debut'));
	}
	if ($debut > ($ii = $nombre_abonnes - _SPIPLISTES_LIGNES_PAR_PAGE)) {
		$debut = max(0, $ii);
	}
	
	$sql_result = sql_select($sql_select, $sql_from, $sql_where, $sql_group, $sql_order, $debut . ',' . _SPIPLISTES_LIGNES_PAR_PAGE);
	if ($sql_result === false) {
		spiplistes_sqlerror_log("listes_boite_abonnes");
	}

	$auteurs = array();
	$les_auteurs = array();
	while ($row = sql_fetch($sql_result)) {
		if ($row['statut'] == '0minirezo') {
			$row['restreint'] = sql_count(sql_select(
				"*"
				, "spip_auteurs_rubriques"
				, "id_auteur=".sql_quote($row['id_auteur'])
				));
		}
		$auteurs[] = $row;
		$les_auteurs[] = $row['id_auteur'];
	}
		
	$lettres_onglet = array();
	
	if($nombre_abonnes > 10) { 
		// SELECT DISTINCT UPPER(LEFT(nom,1)) AS l, COUNT(*) AS n FROM spip_auteurs GROUP BY l ORDER BY l
		$sql_result = sql_select(
			array("DISTINCT UPPER(LEFT(aut.nom,1)) AS l"
				, "COUNT(*) AS n")
			, (($id_liste > 0) ? $sql_from : 'spip_auteurs AS aut')
			, $sql_where 
			, "l"
			, array("l")
			);
		if($result === false) {
				spiplistes_sqlerror_log("listes_boite_abonnes");
		} 
		else {
			$count = 0;
			while ($row = sql_fetch($sql_result)) {
				$lettres_onglet[$row['l']] = $count;
				$count += intval($row['n']);
			}
		}
	}
	
	$legende_tableau =
		($id_liste)
		? spiplistes_nb_abonnes_liste_str_get($id_liste)
		: $legende_tableau
		;
	$result = ""
		. "<div id='"._SPIPLISTES_ID_PETITE_BOITE."'>\n"
		. "<div class='verdana2' id='legend-abos1-propre'>"
		. '<small>' . $legende_tableau . '</small>'
		. "</div>\n"
		;
		
	function spiplistes_lien_ajaxsqueeze ($r_script, $r_param, $a_script, $a_param, $id_dest, $title, $html) {
		$exec_url = generer_url_ecrire($r_script, $r_param);
		$action_url = generer_action_auteur($a_script, $a_param);
		$result = "<a href='" . $exec_url . "'"
				. " onclick=\"javascript:return AjaxSqueeze('$action_url', '$id_dest', '$exec_url', event)\""
				. " title='".$title."'>".$html."</a>";
		return($result);
	}

	
	//////////////////////////////////
	// tableau des resultats
	$result .= ""
		. "<table border='0' cellpadding='3' cellspacing='0' width='100%' class='verdana1 spiplistes-abos'>\n"
		;
	$colspan = 0;
	
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
			: spiplistes_lien_ajaxsqueeze ($script_retour, "id_liste=$id_liste&tri=statut&debut=0"
					, _SPIPLISTES_ACTION_LISTE_ABONNES
					, $id_liste." 0 statut", _SPIPLISTES_ID_PETITE_BOITE
					, _T('lien_trier_statut'), $icon_auteur)
			)
		. "</th>\n"
		;
		$colspan++;
		
		// #2: nom
	$result .= ""
		. "<th>"
		.	(
			($tri == '' || $tri=='nom')
			? _T('info_nom')
			: spiplistes_lien_ajaxsqueeze ($script_retour, "id_liste=$id_liste&tri=nom&debut=0"
					, _SPIPLISTES_ACTION_LISTE_ABONNES
					, $id_liste." 0 nom", _SPIPLISTES_ID_PETITE_BOITE
					, _T('lien_trier_nom'), _T('info_nom'))
			)
		. "</th>\n";
		$colspan++;
		
		// #3: contact mail
	$result .= ""
		. "<th>" . _T('email')
		. "</th>\n"
		;
		$colspan++;
		
	$result .= ""
		// #4: site
		.	"<th>"._T('info_site')
		. "</th>\n";
		$colspan++;
		
	$result .= ""
		// #5: Format si abonne	
		.	"<th>"._T('spiplistes:format')
		. "</th>\n"
		;
		$colspan++;
		
	// si dans la page abonnes_tous, inventaire nb abonnements
	if($id_liste == 0) {	
			// #6: Nombre d'abonnements	
			// si js inactif, $exec_url prend le relais
		$result .= ""
			.	"<th>"
			.	(
				($tri=='nombre')
				? _T('spiplistes:nb_abos')
				: spiplistes_lien_ajaxsqueeze ($script_retour, "id_liste=$id_liste&tri=nombre&debut=0"
						, _SPIPLISTES_ACTION_LISTE_ABONNES
						, $id_liste." 0 nombre", _SPIPLISTES_ID_PETITE_BOITE
						, _T('spiplistes:lien_trier_nombre'), _T('spiplistes:nb_abos'))
				)
			. "</th>\n"
			;
		$colspan++;
	}
		
		// #7: Modifier l'abonnement
	$result .= ""
		.	"<th>"
		. _T('spiplistes:modifier')
		. "</th>\n"
		;
		$colspan++;
		
		// #8: supprimer l'abonne'
	$result .= ""
		.	"<th title='"._T('spiplistes:supprimer_un_abo')."'>"
		. _T('spiplistes:sup_')
		. "</th>\n"
		;
		$colspan++;
		
		// fin de la ligne de titre
	$result .= "</tr>\n";
	
	// onglets de pagination (si pagination)
	if ($nombre_abonnes > _SPIPLISTES_LIGNES_PAR_PAGE) {
		$result .= ""
			. "<tr class='onglets'><td colspan='$colspan'>"
			;
		// onglets : affiche les chiffres 
		$result .= "<!-- onglets chiffres -->\n";
		for ($j=0; $j < $nombre_abonnes; $j+=_SPIPLISTES_LIGNES_PAR_PAGE) {
			if ($j > 0) $result .= " | ";
			
			if ($j == $debut) {
				$result .= "<strong>$j</strong>";
			} else {
				// si js inactif, $exec_url prend le relais
				$exec_url = generer_url_ecrire($script_retour, "id_liste=$id_liste&tri=$tri&debut=$j");
				// sinon, ajax animera la boite des abos
				$action_url = generer_action_auteur(_SPIPLISTES_ACTION_LISTE_ABONNES
					, $id_liste." ".$j." ".$tri);
//				$action_url = parametre_url($action_url, "redirect=$script_retour");

				$result .= 
					"<a href='"
						. parametre_url($exec_url, 'redirect', $script_retour)
						. "' onclick=\"return AjaxSqueeze('$action_url', '"._SPIPLISTES_ID_PETITE_BOITE."', '$exec_url', event)\">"
						. $j
						. "</a>\n"
						;
			}
			
			if (($debut > $j)  && ($debut < $j+_SPIPLISTES_LIGNES_PAR_PAGE)) {
				$result .= " | <strong>$debut</strong>";
			}
		}
		$result .= ""
			. "</td></tr>\n"
			;
			
		// onglets : affichage des lettres
		$result .= ""
			. "<tr class='onglets'><td colspan='$colspan'>\n"
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
					. "' onclick=\"return AjaxSqueeze('$action_url', '"._SPIPLISTES_ID_PETITE_BOITE."', '$exec_url',event)\">"
					. $key
					. "</a>\n"
				;
		}
		$result .= ""
			. "</td></tr>\n"
			;
		$result .= ""
			//. "<tr height='5'></tr>"
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
			. "</td>\n";

		// si dans la page abonnes_tous, inventaire nb abonnements
		if($id_liste == 0) {
			$result .= ""
				// #6: nombre d'abonnement
				. "<td>"
				.	(
					($row['compteur'])
					? "<span class='spiplistes-legend-stitre'>".$row['compteur']."</span>"
					: ""
					)
				. "</td>\n";
		}
		$result .= ""	
			//
			// #7: Modifier l'abonnement
			. "<td>"
			. "<a name='abo".$row['id_auteur']."'></a>"
			;

		$exec_url = generer_url_ecrire($script_retour,"id_liste=$id_liste&debut=$debut&tri=$tri");
		$action_url = generer_action_auteur('spiplistes_changer_statut_abonne', $row['id_auteur']."-format", $exec_url);
		$action_url_ajax = generer_action_auteur(_SPIPLISTES_ACTION_CHANGER_STATUT_ABONNE, $row['id_auteur']."-format");
		$action_url_ajax = parametre_url($action_url_ajax, 'id_liste', $id_liste);
		$action_url_ajax = parametre_url($action_url_ajax, 'debut', $debut);
		$action_url_ajax = parametre_url($action_url_ajax, 'tri', $tri);
		
		$a_format = array('html' => "", 'texte' => "", 'non' => "");
		foreach(array_keys($a_format) as $key) {
			$legend = ($key == 'non') ? 'format_aucun' : $key;
			$a_format[$key] = ""
				. "<a ".$a_title_abo[$key]." href='"
					. parametre_url($action_url, 'statut', $key)
					. "' onclick=\"return AjaxSqueeze('"
					. parametre_url($action_url_ajax, 'statut', $key)
						."', '"._SPIPLISTES_ID_GROSSE_BOITE."', '',event)\">"
					. _T('spiplistes:'.$legend)
					. "</a>\n"
				;		
		}
		$result .= ""
			. "&nbsp;"
				. $a_format[(in_array($abo, array('html','texte')) ? 'non' : 'texte')]
				. " | "
				. $a_format[(($abo == 'html') ? 'texte' : 'html')]
			. "</td>\n"
			;
		
		// #8: supprimer l'abonne' de la liste (ne supprime pas dans la table auteurs)
		$exec_url = generer_url_ecrire($script_retour,"id_liste=$id_liste&debut=$debut&tri=$tri");
		$action_url = generer_action_auteur('spiplistes_changer_statut_abonne', $row['id_auteur']."-supprimer", $exec_url);
		$action_url_ajax = generer_action_auteur(_SPIPLISTES_ACTION_CHANGER_STATUT_ABONNE, $row['id_auteur']."-supprimer");
		$action_url_ajax = parametre_url($action_url_ajax, 'id_liste', $id_liste);
		$action_url_ajax = parametre_url($action_url_ajax, 'debut', $debut);
		$action_url_ajax = parametre_url($action_url_ajax, 'tri', $tri);
		$action_url_ajax = parametre_url($action_url_ajax, 'script_retour', $script_retour);
		$result .= ""
			. "<td class='supprimer_cet_abo'>"
			. "<a ".$a_title_abo[$key]." href='$action_url'"
			. " onclick=\"return AjaxSqueeze('"
				. $action_url_ajax
					."', '"._SPIPLISTES_ID_GROSSE_BOITE."', '',event)\""
					. " title='"._T('spiplistes:supprimer_cet_abo')."'"
					. ">"
				. "&nbsp;<span>"._T('spiplistes:supprimer_cet_abo')."</span>"
				. "</a>\n"
			. "</td>\n"
			;
		// fin de la ligne du tableau
		$result .= "</tr>\n";
	} //
	
	$result .= ""
		. "</table>\n"
		;
		
	// fleche de pagination si besoin
	$debut_suivant = $debut + _SPIPLISTES_LIGNES_PAR_PAGE;
	
	if (($debut_suivant < $nombre_abonnes) || ($debut > 0)) {
		
		$exec_url = generer_url_ecrire($script_retour, "id_liste=$id_liste&tri=$tri&debut=$debut");
		
		$result .= ""
			. "<table id='bas' width='100%' border='0'>"
			. "<tr bgcolor='white'><td style='text-align: $spip_lang_left'>"
			;
		if ($debut > 0) {
			$debut_prec = strval(max($debut - _SPIPLISTES_LIGNES_PAR_PAGE, 0));
			$action_url = generer_action_auteur(_SPIPLISTES_ACTION_LISTE_ABONNES
				, $id_liste." ".$debut_prec." ".$tri);
			$result .= ""
				. "<a href='"
				. parametre_url($action_url, 'redirect', $exec_url)
				. "' onclick=\"return AjaxSqueeze('$action_url', '"._SPIPLISTES_ID_PETITE_BOITE."', '$exec_url',event)\">"
						. "&lt;&lt;&lt;"
						. "</a>\n"
				;
		}
		if($debut_suivant < $nombre_abonnes) {
			$action_url = generer_action_auteur(_SPIPLISTES_ACTION_LISTE_ABONNES
				, $id_liste." ".$debut_suivant." ".$tri);
			$result .= ""
				. "</td><td style='text-align: $spip_lang_right'>\n"
				. "<!-- fleche suivante -->\n"
				. "<a href='"
				. parametre_url($action_url, 'redirect', $exec_url)
				. "' onclick=\"return AjaxSqueeze('$action_url', '"._SPIPLISTES_ID_PETITE_BOITE."', '$exec_url',event)\">"
						. "&gt;&gt;&gt;"
						. "</a>\n"
				;
		}
		$result .= ""
			. "</td></tr>\n"
			. "</table>\n"
			;
	}
	
	$result .= ""
		. "</div>\n" // end _SPIPLISTES_ID_PETITE_BOITE
		;
		
	return($result);
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
	, $retour_ajax = false
) {
	static $bouton_id;
	$bouton_id++;
	$type_ajout = ($script_action == _SPIPLISTES_ACTION_MOD_GERER) ? "mod" : "abo";
	
	// si retour de formulaire, les vars sont dans le $_POST
	$cherche_auteur = trim(urldecode(_request('cherche_auteur')));
	$icone_loupe = trim(urldecode(_request('icone_loupe')));
	if(empty($icone_loupe)) {
		// En ajax, find_in_path() ne trouve pas le chemin correct.
		// Oblige' de le noter au premier appel
		// et le transmettre dans le form.
		$icone_loupe = find_in_path('images/loupe.png');
	}
	foreach(array('id_grosse_boite', 'script_exec', 'script_action') as $key) {
		if(empty($$key)) {
			$$key = urldecode(_request($key));
		}
	}

	$select_abo = "";
	$btn_ajouter = ""
		. "<span><input type='submit' value='"._T('bouton_ajouter')."' "
		. " onclick=\"return AjaxSqueeze(this.form, '$id_grosse_boite')\" "
		. " class='fondo' id='btn_ajouter_id_".$type_ajout."'/></span>\n"
		;
	if(empty($args_action)) {
		$args_action = "id_liste=$id_liste";
	}
	$action = generer_action_auteur($script_action, $args_action);
	$exec = generer_url_ecrire($script_exec, $args_action);
	
	// retour de formulaire ?
	if(!empty($cherche_auteur) && $id_liste) {

		$statut_liste = spiplistes_listes_liste_statut($id_liste);
		
		$nb_elligibles = 0;
		$sql_from = "spip_auteurs AS a";
		$sql_where = array(
			"a.nom LIKE '%$cherche_auteur%'"
			, "LENGTH(a.email)"
			, "(statut=".sql_quote('0minirezo')." OR statut=".sql_quote('1comite')
				// si pas une liste privée, complète le where
				. (($statut_liste != _SPIPLISTES_LIST_PRIVATE) ? " OR statut=".sql_quote('6forum') : "")
				. ")"
			, "NOT EXISTS (SELECT NULL FROM spip_auteurs_listes AS l WHERE l.id_auteur = a.id_auteur AND l.id_liste = ".sql_quote($id_liste).")"
			);
		/*
		 * la requete ci-dessus en + clair
		 *//*
		$sql_query = "SELECT id_auteur,nom,statut FROM spip_auteurs AS a
			WHERE nom LIKE '%$cherche_auteur%'
				AND LENGTH(a.email)
				AND (statut='0minirezo' OR statut='1comite' OR statut='6forum')
				AND NOT EXISTS (SELECT NULL FROM spip_auteurs_listes AS l WHERE l.id_auteur = a.id_auteur AND l.id_liste = $id_liste)";
		*/
		
		// demande la liste des elligibles recherches
		$sql_result = sql_select("id_auteur,nom,statut", $sql_from, $sql_where, '', array('statut','nom'));

		if($sql_result) {
			$elligibles = array();
			while($row = spip_fetch_array($sql_result)) {
				if(!isset($elligibles[$row['statut']])) {
					$elligibles[$row['statut']] = array();
				}
				$elligibles[$row['statut']][$row['id_auteur']] = $row['nom'];
				$nb_elligibles++;
			}
			$select_abo = spiplistes_elligibles_select ($elligibles, $nb_elligibles, $type_ajout) . $btn_ajouter;
		}
		else {
			spiplistes_sqlerror_log("listes_selectionner_elligibles");
		}
		
	}
	
	if(empty($select_abo) && ($nb_non_abonnes <= _SPIPLISTES_SELECT_MIN_AUTEURS)) {
		$select_abo = spiplistes_elligibles_select ($non_abonnes, $nb_non_abonnes, $type_ajout) . $btn_ajouter;
	}
	// au dela de _SPIPLISTES_SELECT_MIN_AUTEURS, afficher la boite de recherche. 
	else {
		  $select_abo = ""
		  	. "<div>"
		  	. "<label id='spiplistes_l_recherche'>"
			// en ajax, http_img_pack() ne retrouve pas son petit
		 	//. http_img_pack("loupe.png", _T('info_rechercher'), "width='26' height='20'")
			. "<img src='$icone_loupe' alt='"._T('info_rechercher')."' width='26' height='20' />"
			. "<input type='text' name='cherche_auteur' id='in_cherche_auteur' class='fondl' value='' size='20' style='margin:0 4px' />\n"
			. "</label>\n"
			. "<span><input type='submit' value='"._T('bouton_chercher')."' "
				. " id='btn_chercher_id' name='spiplistes_bouton_chercher'"
				// recherche ne rafraichit que ce formulaire
				. " onclick=\"return AjaxSqueeze(this.form, '"._SPIPLISTES_ID_FROM_ELLIGIBL."$bouton_id')\" "
				. " class='fondo' /></span>\n"
			. "</div>"
			. $select_abo
		  	;
	} 
	
	$result = ""
		// si retour ajax, ne pas ajouter le conteneur dans le conteneur ;-)
		. (($retour_ajax) ? "" : "<div id='"._SPIPLISTES_ID_FROM_ELLIGIBL."$bouton_id'>\n")
		. "<form style='margin:0px; border:0px' action='$action' method='post'>\n"
		. "<div id='boite_selection_elligibles_$bouton_id' style='padding:0;margin:0.5em 0 0'>\n"
		. $select_abo
		. "</div>\n"
		. "<input name='arg' type='hidden' value='$id_liste' />\n"
		. "<input name='action' type='hidden' value='".$script_action."' />\n"
		. "<input name='redirect' type='hidden' value='".$exec."' />\n"
		. "<input name='id_liste' type='hidden' value='$id_liste' />\n"
		. "<input name='tri' type='hidden' value='$tri' />\n"
		. "<input name='script_action' type='hidden' value='$script_action' />\n"
		. "<input name='script_exec' type='hidden' value='$script_exec' />\n"
		. "<input name='id_grosse_boite' type='hidden' value='$id_grosse_boite' />\n"
		. "<input name='icone_loupe' type='hidden' value='$icone_loupe' />\n"
		. "</form>\n"
		. (($retour_ajax) ? "" : "</div>\n")
		;
	
	return($result);
}

function spiplistes_elligibles_select ($elligibles, $nb_elligibles, $type_ajout = 'abo') {
	$t_statut = array(
		  '0minirezo' => _T('info_administrateurs')
		, '1comite' => _T('info_redacteurs')
		, '6forum' => _T('info_visiteurs')
		, '6visiteur' => _T('info_visiteurs')
	);
	$legend = ($type_ajout == 'abo') ? 'abon_ajouter' : 'ajouter_un_moderateur';
	// si un seul, activer plutot la selection par la souris 
	// onchange n'est pas transmis si un seul 'option'
	$onevent = ($nb_elligibles == 1) ? "onmousedown" : "onchange";
	$select_abo = ""
		. "<span class='verdana1 ajout_legend'>".ucfirst(_T('spiplistes:'.$legend))." : </span>\n"
		. "<select id='sel_ajouter_id_".$type_ajout."' name='ajouter_id_".$type_ajout."' size='1' class='fondl' style='width:150px;' $onevent=\"$js\">\n";
	foreach($elligibles as $key => $values) {
		$select_abo .= "<optgroup label=\"".$t_statut[$key]."\" style='background-color: $couleur_claire;'>\n";
		foreach($values as $id => $nom) {
			$select_abo .= "<option value='$id'>$nom</option>\n";
		}
		$select_abo .= "</optgroup>\n";
	}
	$select_abo .= ""
		. "</select>\n"
		;
	return($select_abo);
}

//CP20080603
// la boite complete (abonnes et elligibles) enveloppee pour ajax
function spiplistes_listes_boite_abonnements ($id_liste, $statut_liste, $tri, $debut, $script_retour) {

	$boite_abonnements = ""
		. "<div id='" . _SPIPLISTES_ID_GROSSE_BOITE . "' class='verdana1'>\n"
		. spiplistes_listes_boite_abonnes($id_liste, $statut_liste, $tri, $debut, $script_retour)
		. spiplistes_listes_boite_elligibles ($id_liste, $statut_liste, $tri, $debut)
		. "</div>\n"
		;
	return($boite_abonnements);
}

//CP-20081117
// boite construction des elligibles. Appelee aussi via action/ajax
function spiplistes_listes_boite_elligibles ($id_liste, $statut_liste, $tri, $debut) {
	
	$result = '';
	
	// proposer les elligibles si id_liste (liste_gerer)
	if($id_liste > 0) {
		list($elligibles, $nb_elligibles) = spiplistes_listes_auteurs_elligibles($id_liste, $statut_liste);
		if($nb_elligibles > 0) {
			$result = spiplistes_listes_selectionner_elligibles(
				$elligibles
				, $nb_elligibles
				, $id_liste
				, $tri
				, _SPIPLISTES_ACTION_ABONNER_AUTEUR
				, _SPIPLISTES_EXEC_LISTE_GERER
				, _SPIPLISTES_ID_GROSSE_BOITE
			);
		}
	}
	return($result);
}

//CP-20080610
// boite des moderateurs
function spiplistes_listes_boite_moderateurs ($id_liste, $script_retour, $id_conteneur) 
{
	$boite_moderateurs = "";
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
		$action_url = generer_action_auteur(_SPIPLISTES_ACTION_MOD_GERER, "$id_liste $id_auteur $faire");
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
	if(strlen($boite_moderateurs))
	{
		$boite_moderateurs = ""
			. "<ul class='liste-moderateurs'>\n"
			. $boite_moderateurs
			. "</ul>\n"
			;
	
	}
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
			, "$id_liste 0 ajouter"
			);
	}
	$boite_moderateurs = ""
		. "<div id='grosse_boite_moderateurs' class='verdana1' style='width:100%;height:auto'>\n"
		. $boite_moderateurs
		. "</div>\n"
		;
	return($boite_moderateurs);
} //


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

