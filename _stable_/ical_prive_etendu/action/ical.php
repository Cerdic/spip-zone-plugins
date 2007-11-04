<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/lang');
utiliser_langue_visiteur();
include_spip('inc/texte');
include_spip('inc/charsets');
include_spip('inc/meta');
include_spip('inc/acces');
include_spip('inc/autoriser');
include_spip('inc/auth');


// avec le nouveau compilateur tout ceci me semble faisable en squelette.

// http://doc.spip.org/@ligne_uid
function ligne_uid ($texte) {
	echo filtrer_ical("UID:$texte @ " . url_de_base())."\n";
}

// http://doc.spip.org/@action_ical_dist
function action_ical()
{
	global $id_auteur, $arg, $action, $titres;

	// compatibilite des URLs spip_cal.php3?id=xxx&cle=yyy (SPIP 1.8)
	if (!$id_auteur AND _request('id')) {
		$id_auteur = _request('id');
		$arg = _request('cle');
	}

	if (verifier_low_sec($id_auteur, $arg, $action)) {
		$result = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=" . intval($id_auteur));

		if ($row = spip_fetch_array($result)) {
			$id_utilisateur=$row['id_auteur'];
			$nom_utilisateur=extraire_multi($row['nom']);
			$statut_utilisateur=$row['statut'];
			$langue_utilisateur=$row['lang'];
		}
	  }
	if (!$id_utilisateur) {
		spip_log("spip_cal: acces interdit a $id_auteur par $arg");
		echo _T('info_acces_interdit');
		exit;
	}
	lang_select($langue_utilisateur);
	$id_secteur = _request('id_secteur');
	$age = (_request('age')=='')?30:intval(_request('age'));
	
	$juste = _request('juste');
	switch($juste) {
		case 'art':
			$aff_art = true;
			$aff_brev = false;
			$aff_mess = false;
			break;
		case 'brev':
			$aff_art = false;
			$aff_brev = true;
			$aff_mess = false;
			break;
		case 'artbrev':
			$aff_art = true;
			$aff_brev = true;
			$aff_mess = false;
			break;
		case 'mess':
			$aff_art = false;
			$aff_brev = false;
			$aff_mess = true;
			break;
		default:
			$aff_art = true;
			$aff_brev = true;
			$aff_mess = true;
			break;
	}
	
	$nom_site = $GLOBALS['meta']["nom_site"];
	$adresse_site = url_de_base();
	$spip = "SPIP " . $GLOBALS['spip_version_affichee'] . ' ' . $GLOBALS['home_server'];

	header("Content-Type: text/calendar; charset=utf-8");
	echo	filtrer_ical ("BEGIN:VCALENDAR"), "\n",
		filtrer_ical ("CALSCALE:GREGORIAN"), "\n",
		filtrer_ical ("PRODID: $spip"), "\n",
		filtrer_ical ("VERSION:2.0"), "\n",
		filtrer_ical ("X-WR-CALNAME;VALUE=TEXT:$nom_site / $nom_utilisateur"), "\n",
		filtrer_ical ("X-WR-RELCALID:cal$id_utilisateur @ $adresse_site"), "\n";
	if ($aff_mess) spip_ical_rendez_vous($id_utilisateur, $nom_site, $age);
	if ($aff_mess) spip_ical_taches($id_utilisateur, $nom_site, $age);

	$titres = Array();
	if ($aff_art) 
		$nb_articles = spip_ical_articles($nom_site, $id_utilisateur, $id_secteur, $age);
	else
		$nb_articles = 0;
	if ($aff_brev)
		$nb_breves = spip_ical_breves($nom_site, $id_utilisateur, $id_secteur, $age);
	else
		$nb_breves = 0;
	if ($nb_articles || $nb_breves) {
		if ($nb_articles > 0) $titre_prop[] = _T('info_articles_proposes').": ".$nb_articles;
		if ($nb_breves > 0) $titre_prop[] = _T('info_breves_valider').": ".$nb_breves;
		$titre = join($titre_prop," / ");
		echo	filtrer_ical ("BEGIN:VTODO"), "\n",
			filtrer_ical ("SUMMARY:[$nom_site] $titre"), "\n";
		ligne_uid ("prop");
		$texte = join($titres," / ");
		echo filtrer_ical ("DESCRIPTION:$texte"), "\n";
	
		$today=getdate(time());
		$jour = $today["mday"];
		$mois=$today["mon"];
		$annee=$today["year"];
		echo	filtrer_ical ("DTSTAMP:".date ("Ymd\THis", mktime (12,0,0,$mois,$jour,$annee))), "\n",
			filtrer_ical ("DTSTART:".date ("Ymd\THis", mktime (12,0,0,$mois,$jour,$annee))), "\n",
			filtrer_ical ("CATEGORIES:"._T('icone_a_suivre')), "\n",
			filtrer_ical ("URL:$adresse_site" . _DIR_RESTREINT_ABS), "\n",
			filtrer_ical ("END:VTODO"), "\n";
	}
	if ($aff_mess) spip_ical_messages($id_utilisateur, $nom_site, $age);
	if ($statut_utilisateur == "0minirezo" AND $aff_mess) {
		spip_ical_forums($id_utilisateur, $nom_site, $age);
	}
	echo filtrer_ical ("END:VCALENDAR"), "\n";
}

// http://doc.spip.org/@spip_ical_rendez_vous
function spip_ical_rendez_vous($id_utilisateur, $nom_site, $age)
{
	// Les messages non affichés dans le calendrier sont également pris en compte 
	if ($age==0) $cond=''; else $cond="AND (TO_DAYS(NOW()) - TO_DAYS(date_heure) <= $age)";
	$result_messages=spip_query("SELECT messages.* FROM spip_messages AS messages, spip_auteurs_messages AS lien WHERE ((lien.id_auteur='$id_utilisateur' AND lien.id_message=messages.id_message) OR messages.type='affich') AND messages.statut='publie' $cond GROUP BY messages.id_message ORDER BY messages.date_heure");
	while($row=spip_fetch_array($result_messages)){
		$id_message=$row['id_message'];
		$date_heure=$row["date_heure"];
		$date_heure_fin=$row["date_fin"];
		//$titre=typo($row["titre"]);
		$titre = $row["titre"];
		$texte = $row["texte"];
		$type=$row["type"];

		if ($type == 'normal') {
			$le_type = _T('info_message_2');
			$result_auteurs=spip_query("SELECT auteurs.* FROM spip_auteurs AS auteurs, spip_auteurs_messages AS lien WHERE (lien.id_message='$id_message' AND lien.id_auteur=auteurs.id_auteur)");
			while($row_auteur=spip_fetch_array($result_auteurs)){
				$id_auteur=$row_auteur['id_auteur'];
				$nom_auteur=$row_auteur['nom'];
				$email = $row_auteur ['email'];

				if ($id_auteur != $id_utilisateur) $titre = $titre." - ".$nom_auteur;
			
				if ($id_auteur == $id_utilisateur) echo filtrer_ical ("ORGANIZER:$nom_auteur <$email>"), "\n";
				else  echo filtrer_ical ("ATTENDEE:$nom_auteur <$email>"), "\n";
			}
		}
		else if ($type == 'pb') {
			$le_type = _T('info_pense_bete');
		}
		else if ($type == 'affich') {
			$le_type = _T('info_annonce');
			$titre = "[$nom_site] $titre";
		}

		echo	filtrer_ical ("BEGIN:VEVENT"), "\n",
			filtrer_ical ("SUMMARY:".$titre), "\n",
			filtrer_ical ("DESCRIPTION:$texte"), "\n";

		ligne_uid ("mess$id_message");

		echo	filtrer_ical ("DTSTAMP:".date_ical($date_heure)), "\n",
			filtrer_ical ("DTSTART:".date_ical($date_heure)), "\n";
		if ($date_heure_fin > $date_heure) echo filtrer_ical ("DTEND:".date_ical($date_heure_fin)), "\n";
		
		echo	filtrer_ical ("CATEGORIES:$le_type"), "\n",
			filtrer_ical ("URL:" . generer_url_ecrire("message","id_message=$id_message")), "\n",
			filtrer_ical ("END:VEVENT"), "\n";
	}
}

// http://doc.spip.org/@spip_ical_taches
function spip_ical_taches($id_utilisateur, $nom_site, $age)
{
	if ($age==0) $cond=''; else $cond="AND (TO_DAYS(NOW()) - TO_DAYS(date_heure) <= $age)";
	$result_messages=spip_query("SELECT messages.* FROM spip_messages AS messages, spip_auteurs_messages AS lien WHERE lien.id_auteur='$id_utilisateur' AND lien.id_message=messages.id_message AND messages.type='pb' AND messages.rv!='oui' AND messages.statut='publie' $cond GROUP BY messages.id_message ORDER BY messages.date_heure");
	while($row=spip_fetch_array($result_messages)){
		$id_message=$row['id_message'];
		$date_heure=$row["date_heure"];
		$titre = $row["titre"];
		$texte = $row["texte"];
		$type=$row["type"];

		if ($type == 'normal') {
			$le_type = _T('info_message_2');
			$result_auteurs=spip_query("SELECT auteurs.* FROM spip_auteurs AS auteurs, spip_auteurs_messages AS lien WHERE (lien.id_message='$id_message' AND lien.id_auteur=auteurs.id_auteur)");
			while($row_auteur=spip_fetch_array($result_auteurs)){
				$id_auteur=$row_auteur['id_auteur'];
				$nom_auteur=$row_auteur['nom'];
				$email = $row_auteur ['email'];

				if ($id_auteur != $id_utilisateur) $titre = $titre." - ".$nom_auteur;
			
				if ($id_auteur == $id_utilisateur) echo filtrer_ical ("ORGANIZER:$nom_auteur <$email>"), "\n";
				else  echo filtrer_ical ("ATTENDEE:$nom_auteur <$email>"), "\n";
			}
		}
		else if ($type == 'pb') {
			$le_type = _T('info_pense_bete');
		}
		else if ($type == 'affich') {
			$le_type = _T('info_annonce');
			$titre = "[$nom_site] $titre";
		}
	
		echo	filtrer_ical ("BEGIN:VTODO"), "\n",
			filtrer_ical ("SUMMARY:".$titre), "\n",
			filtrer_ical ("DESCRIPTION:$texte"), "\n";
		ligne_uid ("mess$id_message");
		echo	filtrer_ical ("DTSTAMP:".date_ical($date_heure)), "\n",
			filtrer_ical ("DTSTART:".date_ical($date_heure)), "\n",
			filtrer_ical ("CATEGORIES:$le_type"), "\n",
			filtrer_ical ("URL:" . generer_url_ecrire("message","id_message=$id_message")), "\n",
			filtrer_ical ("END:VTODO"), "\n";
	}
}

// http://doc.spip.org/@spip_ical_articles
function spip_ical_articles($nom_site, $id_utilisateur, $id_secteur, $age)
{
	global $titres;
	$nb_articles=0;
	$opt_prive = array();
	$opt_prive['publique'] = false;
	$opt_publique = array();
	$opt_publique['publique'] = true;
	// On prend tous les articles
	// si $id_secteur, alors on restreint au secteur
	if ($id_secteur>0) $cond = "id_secteur=$id_secteur"; else $cond ='';
	if ($id_secteur>0 AND $age!=O) $cond .= ' AND ';
	if ($age!=0) $cond.="((TO_DAYS(NOW()) - TO_DAYS(date_modif) <= $age) OR statut='prop')";
	$result_articles = spip_query("SELECT id_article, id_rubrique, titre, date, descriptif, texte, date_modif, statut FROM spip_articles WHERE $cond");
	while($row=spip_fetch_array($result_articles)){
		$id_article=$row['id_article'];
		$id_rubrique=$row['id_rubrique'];
		$statut=$row['statut'];
		// Si publie et acces public ou si acces prive
		if ((autoriser('voir', 'article', $id_article, $id_utilisateur, $opt_publique) AND $statut=='publie')
			OR $acces_prive=autoriser('voir', 'article', $id_article, $id_utilisateur, $opt_prive)) {
			switch($statut) {
				case 'prop':
					$cat = _T('info_article_propose');
					break;
				case 'prepa':
					$cat = _T('info_article_redaction');
					break;
				case 'publie':
					$cat = _T('info_article_publie');
					break;
				case 'refuse':
					$cat = _T('info_article_refuse');
					break;
				case 'poubelle':
					$cat = _T('info_article_supprime');
					break;
				default:
					$cat = '';
					break;
			}
			$titre = supprimer_numero($row['titre']);
			$titres[] = $titre;	
			$date_heure = $row['date_modif'];
			if ($statut=='prop') $nb_articles ++;
			$res_auteurs = spip_query("SELECT aut.nom FROM spip_auteurs AS aut, spip_auteurs_articles AS aa WHERE aut.id_auteur=aa.id_auteur AND aa.id_article=$id_article");
			$auteurs = '';
			while($aut_row=spip_fetch_array($res_auteurs)){
				if (strlen($auteurs)>0) $auteurs .= ', ';
				$auteurs .= $aut_row['nom'];
			}
			$arbre = icpe_arbre($id_rubrique);
			if ($acces_prive) $url=generer_url_ecrire("articles","id_article=$id_article");
			else $url=generer_url_public("article","id_article=$id_article");
			
			$texte = _T('icpe:derniere_modification')." ".affdate($row['date_modif'])."\\n";
			$texte .= _T('info_mise_en_ligne')." ".affdate($row['date'])."\n";
			if ($row['descriptif']) $texte .= "\\n\\n"._T('info_descriptif')." ".textebrut($row['descriptif'])."\n";
			if ($row['texte']) $texte .= "\\n\\n"._T('info_texte')." ".couper(textebrut($row['texte']),300)."\n";
			$texte .=  "\\n\\n"._T('info_lien_hypertexte').' '.$url."\n";
			
			echo filtrer_ical ("BEGIN:VEVENT"), "\n",
			filtrer_ical ("SUMMARY:[$nom_site] $titre - $auteurs (".$cat.")"), "\n";
			ligne_uid ("article$id_article");
			echo filtrer_ical ("DTSTAMP:".date ("Ymd\THis", mktime (heures($date_heure),minutes($date_heure),0,mois($date_heure),jour($date_heure),annee($date_heure)))), "\n",
				filtrer_ical ("DTSTART;VALUE=DATE:".date ("Ymd", mktime (heures($date_heure),minutes($date_heure),0,mois($date_heure),jour($date_heure),annee($date_heure)))), "\n",
				filtrer_ical ("CATEGORIES:".$cat), "\n",
				filtrer_ical ("LOCATION:".$arbre), "\n",
				filtrer_ical ("DESCRIPTION:".$texte), "\n",
				filtrer_ical ("URL:".$url), "\n",
				filtrer_ical ("END:VEVENT"), "\n";
		}
	}
	return $nb_articles;
}


// http://doc.spip.org/@spip_ical_breves
function spip_ical_breves($nom_site, $id_utilisateur, $id_secteur, $age)
{
	global $titres;
	$nb_breves=0;
	$opt_prive = array();
	$opt_prive['publique'] = false;
	$opt_publique = array();
	$opt_publique['publique'] = true;
	if ($id_secteur>0) $cond = "id_secteur=$id_secteur"; else $cond ='';
	if ($id_secteur>0 AND $age!=O) $cond .= ' AND ';
	if ($age!=0) $cond.="((TO_DAYS(NOW()) - TO_DAYS(maj) <= $age) OR statut='prop')";
	$result = spip_query("SELECT id_breve, titre, date_heure, statut, id_rubrique, texte, maj FROM spip_breves WHERE $cond");
	while($row=spip_fetch_array($result)){
		$id_breve=$row['id_breve'];
		$id_rubrique=$row['id_rubrique'];
		$statut=$row['statut'];
		if ((autoriser('voir', 'breve', $id_breve, $id_utilisateur, $opt_publique) AND $statut=='publie')
			OR $acces_prive=autoriser('voir', 'breve', $id_breve, $id_utilisateur, $opt_prive)) {
			switch($statut) {
				case 'prop':
					$cat = _T('titre_breve_proposee');
					break;
				case 'publie':
					$cat = _T('titre_breve_publiee');
					break;
				case 'refuse':
					$cat = _T('titre_breve_refusee');
					break;
				default:
					$cat = '';
					break;
			}
			$titre = supprimer_numero($row['titre']);
			$titres[] = $titre;
			$date_heure = $row['maj'];
			$arbre = icpe_arbre($id_rubrique);
			if ($statut=='prop') $nb_breves++;
			if ($acces_prive) $url=generer_url_ecrire("breves_voir","id_breve=$id_breve");
			else $url=generer_url_public("breve","id_breve=$id_breve");
			
			$texte = _T('icpe:derniere_modification')." ".affdate($row['maj'])."\\n";
			$texte .= _T('info_mise_en_ligne')." ".affdate($row['date_heure'])."\n";
			if ($row['texte']) $texte .= "\\n\\n"._T('info_texte')." ".couper(textebrut($row['texte']),300)."\n";
			$texte .=  "\\n\\n"._T('info_lien_hypertexte').' '.$url."\n";
			echo filtrer_ical ("BEGIN:VEVENT"), "\n",
				filtrer_ical ("SUMMARY:[$nom_site] $titre (".$cat.")"), "\n";
			ligne_uid ("breve$id_breve");
			echo	filtrer_ical ("DTSTAMP:".date ("Ymd\THis", mktime (heures($date_heure),minutes($date_heure),0,mois($date_heure),jour($date_heure),annee($date_heure)))), "\n",
				filtrer_ical ("DTSTART;VALUE=DATE:".date ("Ymd", mktime (heures($date_heure),minutes($date_heure),0,mois($date_heure),jour($date_heure),annee($date_heure)))), "\n",
				filtrer_ical ("CATEGORIES:".$cat), "\n",
				filtrer_ical ("LOCATION:".$arbre), "\n",
				filtrer_ical ("DESCRIPTION:".$texte), "\n",
				filtrer_ical ("URL:" . $url), "\n",
				filtrer_ical ("END:VEVENT"), "\n";
		}
	}
	return $nb_breves;
}


// http://doc.spip.org/@spip_ical_messages
function spip_ical_messages($id_utilisateur, $nom_site, $age)
{
	if ($age==0) $cond=''; else $cond="AND (TO_DAYS(NOW()) - TO_DAYS(date_heure) <= $age)";
	$result_messages = spip_query("SELECT * FROM spip_messages AS messages, spip_auteurs_messages AS lien WHERE lien.id_auteur=$id_utilisateur AND statut='publie' AND type='normal' AND lien.id_message=messages.id_message $cond");
	while($row=spip_fetch_array($result_messages)){
		$id_message=$row['id_message'];
		$date_heure=$row["date_heure"];
		$titre = $row["titre"];
		$texte = $row["texte"];
		$type=$row["type"];

		if ($type == 'normal') {
			$le_type = _T('info_message_2');
			$result_auteurs=spip_query("SELECT auteurs.* FROM spip_auteurs AS auteurs, spip_auteurs_messages AS lien WHERE (lien.id_message='$id_message' AND lien.id_auteur=auteurs.id_auteur)");
			while($row_auteur=spip_fetch_array($result_auteurs)){
				$id_auteur=$row_auteur['id_auteur'];
				$nom_auteur = $row_auteur['nom'];
				$email = $row_auteur ['email'];

				if ($id_auteur != $id_utilisateur) $titre = $nom_auteur." - ".$titre;
			
				if ($id_auteur == $id_utilisateur) echo filtrer_ical ("ORGANIZER:$nom_auteur <$email>"), "\n";
				else  echo filtrer_ical ("ATTENDEE:$nom_auteur <$email>"), "\n";
			}
			$result_forum = spip_query("SELECT * FROM spip_forum WHERE statut='perso' AND id_message='$id_message' ORDER BY date_heure DESC LIMIT 1");

			if ($row_forum = spip_fetch_array($result_forum)) {
				$date_heure = $row_forum["date_heure"];
				$texte = $row_forum["texte"];
				$titre = $row_forum["titre"];
				$id_auteur = $row_forum["id_auteur"];

				$result_auteurs2 = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur = $id_auteur");
				if ($row_auteur2 = spip_fetch_array($result_auteurs2)){
					$nom_auteur = $row_auteur2['nom'];
					$email = $row_auteur2 ['email'];
				
					$titre = $nom_auteur." - ".$titre;
				}
			}
		}
		else if ($type == 'pb') {
			$le_type = _T('info_pense_bete');
		}
		else if ($type == 'affich') {
			$le_type = _T('info_annonce');
			$titre = "[$nom_site] $titre";
		}
	
		echo	filtrer_ical ("BEGIN:VTODO"), "\n",
			filtrer_ical ("SUMMARY:".$titre), "\n",
			filtrer_ical ("DESCRIPTION:$texte"), "\n";
	ligne_uid ("nouv_mess$id_message");
	echo	filtrer_ical ("DTSTAMP:".date ("Ymd\THis", mktime (heures($date_heure),minutes($date_heure),0,mois($date_heure),jour($date_heure),annee($date_heure)))), "\n",
		filtrer_ical ("DTSTART:".date ("Ymd\THis", mktime (heures($date_heure),minutes($date_heure),0,mois($date_heure),jour($date_heure),annee($date_heure)))), "\n",
		filtrer_ical ("CATEGORIES:$le_type"), "\n",
		filtrer_ical ("URL:" . generer_url_ecrire("message","id_message=$id_message")), "\n",
		filtrer_ical ("END:VTODO"), "\n";
	}	
}

// http://doc.spip.org/@spip_ical_forums
function spip_ical_forums($id_utilisateur, $nom_site, $age)
{
	$result_forum = spip_query("SELECT * FROM spip_forum WHERE statut = 'prop'");

	while($row=spip_fetch_array($result_forum)){
		$nb_forum ++;
	
		$id_forum=$row['id_forum'];
		$date_heure = $row['date_heure'];
		$titre = $row['titre'];
		$texte = $row['texte'];
		$auteur = $row['auteur'];
		$email_auteur = $row['email_auteur'];
		if ($email_auteur) $email_auteur = "<$email_auteur>";
		
		echo	filtrer_ical ("BEGIN:VEVENT"), "\n",
			filtrer_ical ("SUMMARY:[$nom_site] $titre "._T('icone_forum_suivi')), "\n",
			filtrer_ical ("DESCRIPTION:$texte\r$auteur $email_auteur"), "\n";
		ligne_uid ("forum$id_forum");
		echo	filtrer_ical ("DTSTAMP:".date ("Ymd\THis", mktime (heures($date_heure),minutes($date_heure),0,mois($date_heure),jour($date_heure),annee($date_heure)))), "\n",
			filtrer_ical ("DTSTART:".date ("Ymd\THis", mktime (heures($date_heure),minutes($date_heure),0,mois($date_heure),jour($date_heure),annee($date_heure)))), "\n",
			filtrer_ical ("DTEND:".date ("Ymd\THis", mktime (heures($date_heure),minutes($date_heure)+60,0,mois($date_heure),jour($date_heure),annee($date_heure)))), "\n",
			filtrer_ical ("CATEGORIES:"._T('icone_forum_suivi')), "\n",
			filtrer_ical ("URL:" . generer_url_ecrire("controle_forum")), "\n",
			filtrer_ical ("END:VEVENT"), "\n";
	}

	if ($nb_forum > 0) {
		echo filtrer_ical ("BEGIN:VTODO"), "\n",
			filtrer_ical ("SUMMARY:[$nom_site] "._T('icone_forum_suivi').": $nb_forum"), "\n";
		ligne_uid ("forum");
		
		$today=getdate(time());
		$jour = $today["mday"];
		$mois=$today["mon"];
		$annee=$today["year"];
		echo	filtrer_ical ("DTSTAMP:".date ("Ymd\THis", mktime (12,0,0,$mois,$jour,$annee))), "\n",
			filtrer_ical ("DTSTART:".date ("Ymd\THis", mktime (12,0,0,$mois,$jour,$annee))), "\n",
			filtrer_ical ("CATEGORIES:"._T('icone_forum_suivi')), "\n",
			filtrer_ical ("URL:" . generer_url_ecrire("controle_forum")), "\n",
			filtrer_ical ("END:VTODO"), "\n";
	}
}

function icpe_arbre($id_rubrique) {
	static $liste_rub = array();
	if ($id_rubrique==0)
		return '';
	elseif (!isset($liste_rub[$id_rubrique])) {
		$q = spip_query("SELECT id_parent, titre FROM spip_rubriques WHERE id_rubrique=$id_rubrique");
		if ($r=spip_fetch_array($q))
			if ($r['id_parent']==0)
				return $liste_rub[$id_rubrique]=$r['titre'];
			else
				return $liste_rub[$id_rubrique]= icpe_arbre($r['id_parent']) .' > '. $r['titre'];
		else
			return '';
	} else 
		return $liste_rub[$id_rubrique];
}
?>
