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

function spiplistes_cherche_auteur ($return = false) {
	if (!$cherche_auteur = _request('cherche_auteur')) return;
	
	$col = strpos($cherche_auteur, '@') !== false ? 'email' : 'nom';
	$like = '';
	if (strpos($cherche_auteur, '%') !== false) {
		$like = " WHERE $col LIKE '" . $cherche_auteur . "'";
		$cherche_auteur = str_replace('%', ' ', $cherche_auteur);
	}
	
	$sql_result = spip_query("SELECT id_auteur, $col FROM spip_auteurs $like");
	
	while ($row = spip_fetch_array($sql_result, SPIP_NUM)) {
		$table_auteurs[] = $row[1];
		$table_ids[] = $row[0];
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
		$sql_result = spip_query("SELECT id_auteur,nom,email,bio FROM spip_auteurs WHERE id_auteur=$nouv_auteur LIMIT 1");
		while ($row = spip_fetch_array($sql_result)) {
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
	elseif (count($resultat) < 16) {
		reset($resultat);
		unset($les_auteurs);
		while (list(, $id_auteur) = each($resultat))
			$les_auteurs[] = $id_auteur;
		if ($les_auteurs) {
			$les_auteurs = join(',', $les_auteurs);
			$result .= ""
				. "<strong>"._T('texte_plusieurs_articles', array('cherche_auteur' => $cherche_auteur))."</strong><br />"
				. "<ul>"
				;
			$sql_result = spip_query("SELECT id_auteur,nom,email,bio FROM spip_auteurs WHERE id_auteur IN ($les_auteurs) ORDER BY nom");
			while ($row = spip_fetch_array($sql_result)) {
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
					. " | <a href=\"".generer_url_ecrire(_SPIPLISTES_EXEC_ABONNE_EDIT,"id_auteur=$id_auteur")."\">"._T('spiplistes:choisir')."</a>"
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

	if($return) return($result);
	else echo($result);
}

function spiplistes_afficher_auteurs($query, $url, $return = false) {
	
	global $couleur, $couleur_claire;

	$tri = _request('tri') ? _request('tri') : 'nom';

	$t = spip_query('SELECT COUNT(*) FROM spip_auteurs');
	$nombre_auteurs = spip_fetch_array($t, SPIP_NUM);
	$nombre_auteurs = intval($nombre_auteurs[0]);
	
	// reglage du debut
	$max_par_page = 30;
	$debut = intval(_request('debut'));
	if ($debut > $nombre_auteurs - $max_par_page) {
		$debut = max(0,$nombre_auteurs - $max_par_page);
	}
	
	$t = spip_query($query . ' LIMIT ' . $debut . ',' . $max_par_page);
	
	$auteurs=array();
	$les_auteurs = array();
	while ($auteur = spip_fetch_array($t)) {
		if ($auteur['statut'] == '0minirezo') {
			$auteur['restreint'] = spip_num_rows(spip_query(
				"SELECT * FROM spip_auteurs_rubriques WHERE id_auteur="._q($auteur['id_auteur'])
				));
		}
		$auteurs[] = $auteur;
		$les_auteurs[] = $auteur['id_auteur'];
	}
		
	$lettre = array();
	if (($tri == 'nom') && ($GLOBALS['options'] == 'avancees')) {
		$qlettre = spip_query("SELECT distinct UPPER(LEFT(nom,1)) l, COUNT(*) FROM spip_auteurs GROUP BY l ORDER BY l");
		$count = 0;
		while ($rlettre = spip_fetch_array($qlettre, SPIP_NUM)) {
			$lettre[$rlettre[0]] = $count;
			$count += intval($rlettre[1]);
		}
	}
	
	//////////////////////////////////
	// tableau des resultats
	$result = ""
		. debut_cadre_relief('redacteurs-24.gif', true)
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
			: "<a href='".parametre_url($url,'tri','statut')."' title='"._T('lien_trier_statut')."'>$icon_auteur</a>"
			)
		. "</th>\n"
		// #2: nom
		.	"<th>"
		.	(
		 	($tri == '' || $tri=='nom')
			? _T('info_nom')
			: "<a href='".parametre_url($url,'tri','nom')."' title='"._T('lien_trier_nom')."'>"._T('info_nom')."</a>"
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
			: "<a href='".parametre_url($url,'tri','nombre')."' title='"._T('spiplistes:lien_trier_nombre')."'>"._T('spiplistes:nb_abos')."</a>"
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
		for ($j=0; $j < $nombre_auteurs; $j+=$max_par_page) {
			if ($j > 0) $result .= " | ";
			
			if ($j == $debut)
				$result .= "<strong>$j</strong>";
			elseif ($j > 0)
				$result .= "<a href='".parametre_url($url,'debut',$j)."'>$j</a>";
			else
				$result .= " <a href='".parametre_url($url,'debut',0)."'>0</a>";
			
			if (($debut > $j)  && ($debut < $j+$max_par_page))
				$result .= " | <strong>$debut</strong>";
		}
		$result .= ""
			. "</td></tr>\n"
			;
		// onglets : affichage des lettres
		if (($tri == 'nom') && ($GLOBALS['options'] == 'avancees')) {
			$result .= ""
				. "<tr class='onglets'><td colspan='7'>"
				;
			foreach ($lettre as $key => $val) {
				$result .= 
					($val == $debut)
					? "<strong>$key</strong> "
					: "<a href='".parametre_url($url,'debut',$val)."'>$key</a> "
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
	$trad_map = Array();
	for($index_map=0;$index_map<count($val);$index_map++) {
		$trad_map[$val[$index_map]] = $trad[$index_map];
	}
	$ii=0;
	
	//////////////////////////////////
	// ici commence la vraie boucle

	// les auteurs (la liste)
	foreach ($auteurs as $row) {
		// couleur de ligne
		$couleur = (($ii++) % 2) ? '#eee' : $couleur_claire;


		$result .= ""
			. "<tr style='background-color: $couleur'>"
			//
			// #1: statut auteur (icone)
			. "<td>"
			. bonhomme_statut($row)
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
				: "<img alt='"._T('spiplistes:Pas_adresse_email')."' src='"._DIR_PLUGIN_SPIPLISTES_IMG_PACK."puceoff.gif' />"
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

		$retour = parametre_url($url,'debut',$debut);
		
		$u = generer_action_auteur('spiplistes_changer_statut_abonne', $row['id_auteur']."-format", $retour);
		
		$a_title_abo_html =  " title='"._T('spiplistes:Abonner_format_html')."'";
		$a_title_abo_texte =  " title='"._T('spiplistes:Abonner_format_texte')."'";
		$a_title_desabo =  " title='"._T('spiplistes:Desabonner')."'";

		if($abo == 'html') {
			$option_abo = "<a $a_title_desabo href='".parametre_url($u,'statut','non')."'>"._T('spiplistes:desabo')
			 . "</a> | <a $a_title_abo_texte href='".parametre_url($u,'statut','texte')."'>"._T('spiplistes:texte')."</a>";
		}
		elseif ($abo == 'texte') {
			$option_abo = "<a $a_title_desabo href='".parametre_url($u,'statut','non')."'>"._T('spiplistes:desabo')
			 . "</a> | <a $a_title_abo_html href='".parametre_url($u,'statut','html')."'>"._T('spiplistes:html')."</a>";
		}
		else {
			$option_abo = "<a $a_title_abo_texte href='".parametre_url($u,'statut','texte')."'>"._T('spiplistes:texte')
			 . "</a> | <a $a_title_abo_html href='".parametre_url($u,'statut','html')."'>"._T('spiplistes:html')."</a>";
		}
		
		$result .= ""
			. "&nbsp;".$option_abo
			. "</td></tr>\n"
			;
	}
	
	$result .= ""
		. "</table>\n"
		. "<a name='bas'>"
		. "<table width='100%' border='0'>"
		;
	
	$debut_suivant = $debut + $max_par_page;
	
	if (($debut_suivant < $nombre_auteurs) || ($debut > 0)) {
	
		$result .= ""
			. "<tr height='10'></tr>"
			. "<tr bgcolor='white'><td align='left'>"
			;
		if ($debut > 0) {
			$debut_prec = strval(max($debut - $max_par_page, 0));
			$result .= ""
				. "<form method='post' action='".parametre_url($url,'debut',$debut_prec)."'>"
				. "<div style='text-align:left;'>"
				. "<input type='submit' name='submit' value='&lt;&lt;&lt;' class='fondo' />"
				. "</div>"
				. "</form>"
				;
		}
		$result .= ""
			. "</td><td align='right'>"
			.	(
				($debut_suivant < $nombre_auteurs)
				? "<form method='post' action='".parametre_url($url,'debut',$debut_suivant)."'>\n"
					. "<div style='text-align:right;'>"
					. "<input type='submit' name='submit' value='&gt;&gt;&gt;' class='fondo' />\n"
					. "</div>"
					. "</form>\n"
				: ""
				)
			. "</td></tr>\n"
			;
	}
	
	$result .= ""
		. "</table>\n"
		. fin_cadre_relief(true)
		;

	if($return) return($result);
	else echo($result);
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
