<?php
// inc/spiplistes_afficher_auteurs.php
/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

include_spip('inc/spiplistes_api');

function spiplistes_cherche_auteur () {
	if (!$cherche_auteur = _request('cherche_auteur')) return;
	
	$col = strpos($cherche_auteur, '@') !== false ? 'email' : 'nom';
	$like = '';
	if (strpos($cherche_auteur, '%') !== false) {
		$like = " WHERE $col LIKE '" . $cherche_auteur . "'";
		$cherche_auteur = str_replace('%', ' ', $cherche_auteur);
	}
	
	$sql_result = sql_select("id_auteur,$col", "spip_auteurs", $like);
	
	while($row = sql_fetch($sql_result)) {
		$table_auteurs[] = $row[$col];
		$table_ids[] = $row['id_auteur'];
	}
	
	$resultat = mots_ressemblants($cherche_auteur, $table_auteurs, $table_ids);

	$result = ""
		. "<div id='boite-result-chercher-auteur'>"
		. debut_boite_info(true)
		;
	if (!$resultat) {
		$result .= ""
			. "<strong>"._T('texte_aucun_resultat_auteur', array('cherche_auteur' => $cherche_auteur)).".</strong><br />\n"
			;
	}
	else if (count($resultat) == 1) {
		list(, $nouv_auteur) = each($resultat);
		$result .= ""
			. "<strong>"._T('spiplistes:une_inscription')."</strong>:<br />\n"
			. "<ul>"
			;
		$sql_result = sql_select("id_auteur,nom,email,bio", "spip_auteurs", "id_auteur=".sql_quote($nouv_auteur), '', '', 1);
		while ($row = sql_fetch($sql_result)) {
			$id_auteur = $row['id_auteur'];
			$nom_auteur = $row['nom'];
			$email_auteur = $row['email'];
			$bio_auteur = $row['bio'];

			$result .= ""
				. "<li class='auteur'>"
					. "<a class='nom_auteur' href=\"".generer_url_ecrire(_SPIPLISTES_EXEC_ABONNE_EDIT, "id_auteur=$id_auteur")."\">".typo($nom_auteur)."</a>"
				. " | $email_auteur"
				. "</li>\n"
				;
		}
		$result .= ""
			. "</ul>\n"
			;
	}
	else if (count($resultat) < 16) {
		reset($resultat);
		unset($les_auteurs);
		while (list(, $id_auteur) = each($resultat)) {
			$les_auteurs[] = $id_auteur;
		}
		if($les_auteurs) {
			$les_auteurs = join(',', $les_auteurs);
			$result .= ""
				. "<strong>"._T('texte_plusieurs_articles', array('cherche_auteur' => $cherche_auteur))."</strong><br />"
				. "<ul>"
				;
			$sql_select = array('id_auteur','nom','email','bio');
			$sql_result = sql_select($sql_select, "spip_auteurs", "id_auteur IN ($les_auteurs)", '', array('nom'));
			while ($row = sql_fetch($sql_result)) {
				$id_auteur = $row['id_auteur'];
				$nom_auteur = $row['nom'];
				$email_auteur = $row['email'];
				$bio_auteur = $row['bio'];
				
				$result .= ""
					. "<li class='auteur'><span class='nom_auteur'>".typo($nom_auteur)."</span>"
					;
				if ($email_auteur) {
					$result .= ""
						. " ($email_auteur)"
						;
				}
				$result .= ""
					. " | <a href=\"".generer_url_ecrire(_SPIPLISTES_EXEC_ABONNE_EDIT,"id_auteur=$id_auteur")."\">"
					. _T('spiplistes:choisir')."</a>"
					;
				if (trim($bio_auteur)) {
					$result .= ""
						. "<br /><font size=1>".couper(propre($bio_auteur), 100)."</font>\n"
						;
				}
				$result .= ""
					. "</li>\n"
					;
			}
			$result .= ""
				. "</ul>\n"
				;
		}
	}
	else {
		$result .= ""
			. "<strong>"._T('texte_trop_resultats_auteurs', array('cherche_auteur' => $cherche_auteur))."</strong><br />"
			;
	}
	
	$result .= ""
		. fin_boite_info(true)
		. "</div>"
		;

	return($result);
}

function spiplistes_afficher_auteurs (
	$sql_select, $sql_from, $sql_where, $sql_group, $sql_order
	, $script_retour
	, $max_par_page = 10
	, $tri = 'nom'
	, $id_liste = 0
	, $debut = 0
) {
	
	global 
		  $spip_lang_left
		, $spip_lang_right
		;

	$nombre_auteurs = sql_count(sql_select("COUNT(aut.id_auteur)", $sql_from, $sql_where, $sql_group));

	// reglage du debut
	if(!$debut) {
		// si js pas activé, récupère dans l'url
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
			$auteur['restreint'] = sql_count(spip_query(
				"SELECT * FROM spip_auteurs_rubriques WHERE id_auteur=".sql_quote($auteur['id_auteur'])
				));
		}
		$auteurs[] = $auteur;
		$les_auteurs[] = $auteur['id_auteur'];
	}
		
	$lettres_onglet = array();
	if($tri == 'nom') {
		$sql_result = sql_select(
			array("DISTINCT UPPER(LEFT(nom,1)) AS l"
				, "COUNT(*) AS n")
			, "spip_auteurs", '', "l", array("l"));
		$count = 0;
		while ($row = sql_fetch($sql_result)) {
			$lettres_onglet[$row['l']] = $count;
			$count += intval($row['n']);
		}
	}
	
	//////////////////////////////////
	// tableau des resultats
	$result = ""
		. "<table border='0' cellpadding='3' cellspacing='0' width='100%' class='arial2, spiplistes-abos'>\n"
		;
	
	// titres du tableau (a-la-SPIP, en haut)
	$icon_auteur = "<img src='"._DIR_IMG_PACK."/admin-12.gif' alt='' border='0'>";
	$result .= ""
		. "<tr bgcolor='#DBE1C5'>"
		//
		// #1: statut auteur (icone)
		. "<th width='20'>"
		.	(
			($tri=='statut')
			? $icon_auteur
			: "<a href='".parametre_url($script_retour,'tri','statut')."' title='"._T('lien_trier_statut')."'>$icon_auteur</a>"
			)
		. "</th>\n"
		// #2: nom
		.	"<th>"
		.	(
		 	($tri == '' || $tri=='nom')
			? _T('info_nom')
			: "<a href='".parametre_url($script_retour,'tri','nom')."' title='"._T('lien_trier_nom')."'>"._T('info_nom')."</a>"
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
		// #5: Format si abonné	
		.	"<th>"._T('spiplistes:format')
		. "</th>\n"
		//
		// #6: Nombre d'abonnements	
		.	"<th>"
		.	(
			($tri=='nombre')
			? _T('spiplistes:nb_abos')
			: "<a href='".parametre_url($script_retour,'tri','nombre')."' title='"._T('spiplistes:lien_trier_nombre')."'>"._T('spiplistes:nb_abos')."</a>"
			)
		. "</th>\n"
		//
		// #7: Modifier l'abonnement
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
						. "' onclick=\"return AjaxSqueeze('$action_url','auteurs','$exec_url',event)\">"
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
		if (($tri == 'nom') && ($GLOBALS['options'] == 'avancees')) {
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
						. "' onclick=\"return AjaxSqueeze('$action_url','auteurs','$exec_url',event)\">"
						. $key
						. "</a>\n"
					;
			}
			$result .= ""
				. "</td></tr>\n"
				;
		}
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

	$a_title_abo_html =  " title='"._T('spiplistes:Abonner_format_html')."'";
	$a_title_abo_texte =  " title='"._T('spiplistes:Abonner_format_texte')."'";
	$a_title_desabo =  " title='"._T('spiplistes:Desabonner')."'";

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
				? "<a href='mailto:".$row['email']."'><img alt='' src='"._DIR_IMG_PACK."m_envoi_rtl.gif' /></a>"
				: "<img alt='"._T('spiplistes:Pas_adresse_email')."'"
					// img ne prend pas de title (incorrect) mais mozilla n'affiche en bullet que pour title
					. " title='"._T('spiplistes:Pas_adresse_email')."'"
					. " src='"._DIR_PLUGIN_SPIPLISTES_IMG_PACK."puceoff.gif' />"
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
			// #5: Format si abonné	
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
				? "<span class='spiplistes-legend-stitre'>".$row['compteur']."<span>"
				: ""
				)
			. "</td>\n"
			//
			// #7: Modifier l'abonnement
			. "<td>"
			. "<a name='abo".$row['id_auteur']."'></a>"
			;

		// SPIP 192 ne prend pas les array dans parametre_url()
		// obligé de l'appeler 2 x
		$retour = parametre_url($script_retour,'debut',$debut);
		$retour = parametre_url($retour,'tri',$tri);
		
		$u = generer_action_auteur('spiplistes_changer_statut_abonne', $row['id_auteur']."-format", $retour);
		
		$a_format_html = "<a $a_title_abo_html href='".parametre_url($u,'statut','html')."'>"._T('spiplistes:html')."</a>";
		$a_format_texte = "<a $a_title_abo_texte href='".parametre_url($u,'statut','texte')."'>"._T('spiplistes:texte')."</a>";
		$a_format_non = "<a $a_title_desabo href='".parametre_url($u,'statut','non')."'>"._T('spiplistes:desabo')."</a>";
		
		if($abo == 'html') {
			$option_abo = $a_format_non." | ".$a_format_texte;
		}
		elseif ($abo == 'texte') {
			$option_abo = $a_format_non." | ".$a_format_html;
		}
		else {
			$option_abo = $a_format_texte." | ".$a_format_html;
		}
		
		$result .= ""
			. "&nbsp;".$option_abo
			. "</td></tr>\n"
			;
	}
	
	$result .= ""
		. "</table>\n"
		;
		
	// flèche de pagination si besoin
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
				. "' onclick=\"return AjaxSqueeze('$action_url','auteurs','$exec_url',event)\">"
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
				. "' onclick=\"return AjaxSqueeze('$action_url','auteurs','$exec_url',event)\">"
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

// Lorsqu'appelé par ?action, perd la position
// corrige le lien relatif
function spiplistes_bonhomme_statut ($row) {
	$result = bonhomme_statut($row);
	if(preg_match(",^<img src='dist/images,", $result)) {
		$result = preg_replace(",^<img src='dist/images,", "<img src='../dist/images", $result);
	}
	return($result);
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
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
?>