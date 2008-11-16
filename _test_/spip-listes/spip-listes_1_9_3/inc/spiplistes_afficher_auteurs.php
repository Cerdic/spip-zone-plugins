<?php
// inc/spiplistes_afficher_auteurs.php
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

include_spip('inc/spiplistes_api');



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
?>