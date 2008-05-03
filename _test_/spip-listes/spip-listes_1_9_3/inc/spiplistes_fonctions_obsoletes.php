<?php

// inc/spiplistes_fonctions_obsoletes.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

/* les fonctions obsoletes de spiplistes
*/

if(!spiplistes_spip_est_inferieur_193()) { 
	function generer_url_courrier ($script='', $args="", $no_entities=false, $rel=false) {
		$action = get_spip_script();
		$id_courrier = _request('id_courrier');
		$action = parametre_url($action, 'page', 'courrier', '&') . "&id_courrier=$id_courrier";
		if (!$no_entities) {
			$action = quote_amp($action);
		}
		return ($rel ? '' : url_de_base()) . $action;
	}
}

/* inscription directe - import 
 * @param $type_abo = 'html' ou 'texte' ou 'non-change'
 * @param $statut = 'valide' ou 'a_valider'
 */
function spiplistes_ajout_auteur_aux_listes($id_auteur, $ids_liste, $type_abo, $statut) {
  if (!$type_abo) $type_abo = 'html';
  else if ($type_abo == 'txt') $type_abo='texte';
  if ($statut != 'valide')
	$statut = 'a_valider';

  if ($type_abo != 'non-change')
	  spiplistes_MaJ_auteur_elargi($id_auteur, $type_abo);
  
  if(!is_array($ids_liste)){
	$ids_liste = array($ids_liste);
  }
  $query = '';
  while( list(,$val) = each($ids_liste) ) {
	$query .= "DELETE FROM spip_auteurs_listes WHERE id_auteur=".
	  _q($id_auteur)." AND id_liste="._q($val).";\n";
	$query .= "INSERT INTO spip_auteurs_listes ".
	  "(id_auteur,id_liste,format,date_inscription,statut)".
	  " VALUES ("._q($id_auteur).","._q($val).", ".
	  _q($type_abo).", now(), "._q($statut).");\n";
  }
  __exec_multi_queries($query);
  return True;
}

/* Demande d'abonnement à une liste d'un visiteur
 * NE VALIDE PAS L'ABONNEMENT : il faut confirmer
 * Retourne une structure array('inser't ou 'update', id_auteur, cookie_oubli)
 * @param $email : clé unique
 * @param $nom : facultatif
 * @param $prenom : facultatif concaténé avant le nom
 * @param $ids_liste : entier ou array d'entier (id d'une spip_liste)
 * @param $statut_abonnement : 'html' (defaut) ou 'texte'
 */
function spiplistes_ajout_inscription($email, $nom, $prenom, $ids_liste, $statut_abonnement) {
	$email = email_valide($email);
	$id_auteur = spiplistes_idauteur_depuis_email($email);
	$cookie = creer_uniqid();
	if ($id_auteur == 0) {
		$nom = trim($nom);
		$prenom = trim($prenom);
		if (!$nom) {
			$nom = $email;
		} else if ($prenom) {
			$nom = $prenom.' '.$nom;
		}
		$query = "INSERT INTO spip_auteurs (nom, email, statut, cookie_oubli)".
			"VALUES ("._q($nom).", "._q($email).", '6forum', "._q($cookie).");";
		spip_query($query);
		$id_auteur = spiplistes_idauteur_depuis_email($email);
		$ret = array('insert', $id_auteur, $cookie);
		if ( ! $id_auteur ) return False; // erreur
	} else if ($id_auteur) {
		$query = "UPDATE spip_auteurs SET cookie_oubli="._q($cookie).
		  " WHERE id_auteur = ".$id_auteur;
		spip_query($query);
		$ret = array('update', $id_auteur, $cookie);
	} else { return False; } // erreur

	if ($format_abonnement != 'html' 
		or $format_abonnement != 'texte' 
		or $format_abonnement != 'non') {
		$format_abonnement = 'html';
	}
	spiplistes_MaJ_auteur_elargi($id_auteur, $format_abonnement);

	if( ! is_array($ids_liste)) {
		$ids_liste = array ($ids_liste);
	}
	while( list(,$val) = each($ids_liste) ){
		$query = "DELETE FROM spip_auteurs_listes WHERE id_auteur=".
			$id_auteur." AND id_liste="._q($val).";\n";
		$query .= "INSERT INTO spip_auteurs_listes ".
			"(id_auteur, id_liste, date_inscription, statut)".
		"VALUES (".$id_auteur.", "._q($val).", now(), 'a_valider');\n";
	}
	//echo '<!-- '.$query.' -->'."\n";
	__exec_multi_queries($query);
	return $ret;
}

/*
 * Mise a jour de l'auteur elargi (table spip-liste)
 * @param $id_auteur : clé table auteur
 * @param $format : non | texte | html
 */
function spiplistes_MaJ_auteur_elargi($id_auteur, $format) {
	if (!$format) { $format='non'; }
	if (spiplistes_auteur_elargi_existe($id_auteur)) {
		$query = "INSERT INTO spip_auteurs_elargis (id_auteur, spip_listes_format)".
			" VALUES ("._q($id_auteur)." , "._q($format)." ) ";
	} else {
		$query = "UPDATE spip_auteurs_elargis SET spip_listes_format=".q($format).
			" WHERE id_auteur="._q($id_auteur);
	}
	// echo'<!-- '.$query.' -->'."\n";
	spip_query($query);
}




/* Savoir si l'auteur elargi a ete cree */
function spiplistes_auteur_elargi_existe($id_auteur) {
	$query = "SELECT 1 FROM spip_auteurs_elargis WHERE id_auteur="._q($id_auteur);
	$result = spip_query($query);
	if (sql_count($result) == 0) { return False; }
	else { return True; }
}

/* desabonnement de certaines listes uniquement */
function spiplistes_desabonner_des_listes($id_auteur, $ids_liste) {
	if(!is_array($ids_liste)) {
		$ids_liste = array($ids_liste);
	}
	$query = "";
	while ( list(,$val) = each($ids_liste) ) {
		$query .= "DELETE FROM spip_autheur_listes WHERE id_auteur=".
			_q($id_auteur)." AND id_liste="._q($id_liste).";";
	}
	__exec_multi_queries($query);
}



/* validation des inscriptions depuis cookie_oubli */
function spiplistes_valide_listes_depuis_cookie($cookie) {
  // better query, but works only whith mysql>=4 :( 
  $query_mysql4 = "UPDATE spip_auteurs_listes, spip_auteurs".
	" SET spip_auteurs_listes.statut = 'valide',".
	" spip_auteurs.cookie_oubli = ''".
	" WHERE spip_auteurs.cookie_oubli="._q($cookie).
	" AND spip_auteurs_listes.id_auteur = spip_auteurs.id_auteur".
	" AND spip_auteurs.statut<>'5poubelle'";

  // standard queries  (works with mysql3)
  $id_auteur = spiplistes_idauteur_depuis_cookie_oubli($cookie);
  $queries = "UPDATE spip_auteurs_listes SET".
	" statut = 'valide'".
	" WHERE id_auteur = ".$id_auteur.";";
  $queries .= "UPDATE spip_auteurs SET".
	  " cookie_oubli = ''".
	  " WHERE id_auteur = ".$id_auteur.";";

  //echo '<!-- validation = '.$query.' -->'."\n";
  $res = __exec_multi_queries($queries);
  if (! $res) {
	echo "Validation impossible !";
	exit();
  }
}

// inc/formater_auteur_liste.php

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

	if ($url_site_auteur = $row["url_site"]) $vals[] =  "<a href='$url_site_auteur'>". _T('info_site_min')."</a>";
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
	if (sql_count($res)<200){ // probleme de performance au dela, on ne filtre plus
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