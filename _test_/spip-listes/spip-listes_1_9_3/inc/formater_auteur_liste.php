<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;


// Ligne info auteur dans boite de sélection auteur
function inc_formater_auteur_liste_dist($id_auteur, $script_edition = _SPIPLISTES_EXEC_ABONNE_EDIT) {

  global $connect_id_auteur, $spip_lang_rtl, $connect_statut;

	$id_auteur = intval($id_auteur);

	$row = spip_fetch_array(spip_query("SELECT *, (en_ligne<DATE_SUB(NOW(),INTERVAL 15 DAY)) AS parti FROM spip_auteurs where id_auteur=$id_auteur"));

	$vals = array();

	$href = generer_url_ecrire("auteurs","statut=" . $row['statut']);
	$vals[] = "<a href='$href'>" . bonhomme_statut($row) . '</a>';

	if (($id_auteur == $connect_id_auteur) OR $row['parti'])
		$vals[]= '&nbsp;';
	else	$vals[]= formater_auteur_listes_mail($row['email'], $id_auteur);

	if ($bio_auteur = attribut_html(propre(couper($row["bio"], 100))))
		$bio_auteur = " title=\"$bio_auteur\"";

	if (!$nom = typo($row['nom']))
		$nom = "<span style='color: red'>" . _T('texte_vide') . '</span>';

	$vals[] = "<a href='"
	. generer_url_ecrire($script_edition, "id_auteur=$id_auteur&initial=-1")
	. "' $bio_auteur>$nom</a>";

	if ($url_site_auteur = $row["url_site"]) $vals[] =  "<a href='$url_site_auteur'>"._T('info_site_min')."</a>";
	else $vals[] =  "&nbsp;";

	$sql_query = "SELECT COUNT(listes.id_liste) AS n 
		FROM spip_auteurs_listes AS lien, spip_listes AS listes 
		WHERE lien.id_auteur=$id_auteur AND listes.id_liste=lien.id_liste 
			AND (listes.statut='"._SPIPLISTES_PUBLIC_LIST."' 
				OR listes.statut='"._SPIPLISTES_PRIVATE_LIST."'
				OR listes.statut='"._SPIPLISTES_MONTHLY_LIST."') 
		GROUP BY lien.id_auteur";
	$cpt = spip_fetch_array(spip_query($sql_query));

	$nombre_listes = intval($cpt['n']);

	if ($nombre_listes > 1) $vals[] =  $nombre_listes.' '._T('spiplistes:info_liste_2');
	elseif ($nombre_listes == 1) $vals[] =  _T('spiplistes:info_1_liste');
	else $vals[] =  "&nbsp;";

	return ($vals);
}

// http://doc.spip.org/@formater_auteur_mail
function formater_auteur_listes_mail($email, $id_auteur)
{
	global $spip_lang_rtl;

//	if ($email) $href='mailto:' . $email;
//	else $href = generer_action_auteur("editer_message","normal/$id_auteur");
	$href = generer_action_auteur("editer_message","normal/$id_auteur");

	return "<a href='$href' title=\""
		  . _T('email')
		  . '">'
		. http_img_pack("m_envoi$spip_lang_rtl.gif", "m&gt;", " width='14' height='7'", _T('info_envoyer_message_prive'))
		  . '</a>';
}

function inc_determiner_non_auteurs_liste($type, $id, $cond_les_auteurs, $order)
{
	$cond = '';
	$res = determiner_auteurs_objet($type, $id, $cond_les_auteurs);
	if (spip_num_rows($res)<200){ // probleme de performance au dela, on ne filtre plus
		while ($row = spip_fetch_array($res))
			$cond .= ",".$row['id_auteur'];
	}
	if (strlen($cond))
		$cond = "id_auteur NOT IN (" . substr($cond,1) . ') AND ';

	$sql_query = "SELECT * FROM spip_auteurs WHERE $cond" . "statut!='5poubelle' AND statut!='nouveau' ORDER BY $order";
//spiplistes_log($sql_query);
	return spip_query($sql_query);
}

?>
