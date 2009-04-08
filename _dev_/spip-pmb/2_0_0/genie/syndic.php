<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/syndic');

// http://doc.spip.org/@genie_syndic_dist
function genie_syndic_dist($t) {
	define('_GENIE_SYNDIC', 1); // pour message de compatibilite ci-dessous
	return executer_une_syndication();
}

//
// Effectuer la syndication d'un unique site,
// retourne 0 si aucun a faire ou echec lors de la tentative
//

// http://doc.spip.org/@executer_une_syndication
function executer_une_syndication() {

	spip_connect();

	## valeurs modifiables dans mes_options
	## attention il est tres mal vu de prendre une periode < 20 minutes
	define('_PERIODE_SYNDICATION', 2*60);
	define('_PERIODE_SYNDICATION_SUSPENDUE', 24*60);

	// On va tenter un site 'sus' ou 'off' de plus de 24h, et le passer en 'off'
	// s'il echoue
	$where = sql_in("syndication", array('sus','off')) . "
	AND statut='publie'
	AND date_syndic < DATE_SUB(NOW(), INTERVAL
	"._PERIODE_SYNDICATION_SUSPENDUE." MINUTE)";
	$id_syndic = sql_getfetsel("id_syndic", "spip_syndic", $where, '', "date_syndic", "1");
	if ($id_syndic) {
		$res1 = syndic_a_jour($id_syndic, 'off');
	} else $res1 = true;

	// Et un site 'oui' de plus de 2 heures, qui passe en 'sus' s'il echoue
	$where = "syndication='oui'
	AND statut='publie'
	AND date_syndic < DATE_SUB(NOW(), INTERVAL "._PERIODE_SYNDICATION." MINUTE)";
	$id_syndic = sql_getfetsel("id_syndic", "spip_syndic", $where, '', "date_syndic", "1");

	if ($id_syndic) {
		$res2 = syndic_a_jour($id_syndic, 'sus');
	} else $res2 = true;

	return ($res1 OR $res2) ? 0 : $id_syndic;
}


//
// Mettre a jour le site
//
// Attention, cette fonction ne doit pas etre appellee simultanement
// sur un meme site: un verrouillage a du etre pose en amont.
//

// http://doc.spip.org/@syndic_a_jour
function syndic_a_jour($now_id_syndic, $statut = 'off') {
	include_spip('inc/texte');
	if (!defined('_GENIE_SYNDIC'))
		spip_log("syndic_a_jour doit etre appelee par Cron. Cf. " .
			 "http://trac.rezo.net/trac/spip/changeset/10294",
			 'vieilles_defs');
	$row = sql_fetsel("*", "spip_syndic", "id_syndic=$now_id_syndic");

	if (!$row) return;

	$url_syndic = $row['url_syndic'];
	$url_site = $row['url_site'];

	if ($row['moderation'] == 'oui')
		$moderation = 'dispo';	// a valider
	else
		$moderation = 'publie';	// en ligne sans validation

	sql_update('spip_syndic', array('syndication'=>sql_quote($statut), 'date_syndic'=>'NOW()'), "id_syndic=$now_id_syndic");

	// Aller chercher les donnees du RSS et les analyser
	include_spip('inc/distant');
	$rss = recuperer_page($url_syndic, true);
	if (!$rss)
		$articles = _T('avis_echec_syndication_02');
	else {
	//APA	
		if (preg_match( '`<generator>(.)*[Pp][Mm][Bb](.)*</generator>`', $rss)) {	
			//cas d'un flux PMB
			$articles = analyser_backend_pmb($rss, $url_syndic);

			// Renvoyer l'erreur le cas echeant
			if (!is_array($articles)) return $articles;

			// Les enregistrer dans la base

			$faits = array();
			foreach ($articles as $data) {
				inserer_article_syndique_pmb ($data, $now_id_syndic, $moderation, $url_site, $url_syndic, $row['resume'], $row['documents'], $faits);
			}

			// moderation automatique des liens qui sont sortis du feed
			if (count($faits) > 0) {
				$faits = sql_in("id_syndic_article", $faits, 'NOT');
				if ($row['miroir'] == 'oui') {
					sql_update('spip_syndic_articles_pmb', array('statut'=>"'off'", 'maj'=>'maj'), "id_syndic=$now_id_syndic AND $faits");
				}
				// suppression apres 2 mois des liens qui sont sortis du feed
				if ($row['oubli'] == 'oui') {
					sql_delete('spip_syndic_articles_pmb', "id_syndic=$now_id_syndic AND maj < DATE_SUB(NOW(), INTERVAL 2 MONTH) AND date < DATE_SUB(NOW(), INTERVAL 2 MONTH) AND $faits");
				}
			}
			//suppression des articles pmb mal class�s avant activation du plugin
			sql_delete('spip_syndic_articles', "id_syndic=$now_id_syndic");
				
		} else {
			//cas d'un flux normal
			
			$articles = analyser_backend($rss, $url_syndic);

			// Renvoyer l'erreur le cas echeant
			if (!is_array($articles)) return $articles;

			// Les enregistrer dans la base

			$faits = array();
			foreach ($articles as $data) {
				inserer_article_syndique ($data, $now_id_syndic, $moderation, $url_site, $url_syndic, $row['resume'], $row['documents'], $faits);
			}

			// moderation automatique des liens qui sont sortis du feed
			if (count($faits) > 0) {
				$faits = sql_in("id_syndic_article", $faits, 'NOT');
				if ($row['miroir'] == 'oui') {
					sql_update('spip_syndic_articles', array('statut'=>"'off'", 'maj'=>'maj'), "id_syndic=$now_id_syndic AND $faits");
				}
				// suppression apres 2 mois des liens qui sont sortis du feed
				if ($row['oubli'] == 'oui') {
					sql_delete('spip_syndic_articles', "id_syndic=$now_id_syndic AND maj < DATE_SUB(NOW(), INTERVAL 2 MONTH) AND date < DATE_SUB(NOW(), INTERVAL 2 MONTH) AND $faits");
				}
			}
		}
	}
	//finAPA
	// Noter que la syndication est OK
	sql_updateq("spip_syndic", array("syndication" => 'oui'), "id_syndic=$now_id_syndic");

	return false; # c'est bon
}


//
// Insere un article syndique (renvoie true si l'article est nouveau)
// en  verifiant qu'on ne vient pas de l'ecrire avec
// un autre item du meme feed qui aurait le meme link
//
// http://doc.spip.org/@inserer_article_syndique
function inserer_article_syndique ($data, $now_id_syndic, $statut, $url_site, $url_syndic, $resume, $documents, &$faits) {
	// Creer le lien s'il est nouveau - cle=(id_syndic,url)
	// On coupe a 255 caracteres pour eviter tout doublon
	// sur une URL de plus de 255 qui exloserait la base de donnees
	$le_lien = substr($data['url'], 0,255);

// Chercher les liens de meme cle
// S'il y a plusieurs liens qui repondent, il faut choisir le plus proche
// (ie meme titre et pas deja fait), le mettre a jour et ignorer les autres

	$n = 0;
	$s = sql_select("id_syndic_article,titre", "spip_syndic_articles", "url=" . sql_quote($le_lien) . " AND id_syndic=$now_id_syndic AND " . sql_in('id_syndic_article', $faits, 'NOT'), "", "maj DESC");
	while ($a = sql_fetch($s)) {
		$id =  $a['id_syndic_article'];
		if ($a['titre'] == $data['titre']) {
			$id_syndic_article = $id;
			break;
		}
		$n++;
	}
	// S'il y en avait qu'un, le prendre quel que soit le titre
	if ($n == 1)
		$id_syndic_article = $id;
	// Si l'article n'existe pas, on le cree
	elseif (!isset($id_syndic_article)) {
		$ajout = $id_syndic_article = sql_insertq('spip_syndic_articles',
				array('id_syndic' => $now_id_syndic,
				'url' => $le_lien,
				'date' => date("Y-m-d H:i:s", $data['date']),
				'statut'  => $statut));
		if (!$ajout) return;
	}
	$faits[] = $id_syndic_article;

	// Descriptif, en mode resume ou mode 'full text'
	// on prend en priorite data['descriptif'] si on est en mode resume,
	// et data['content'] si on est en mode "full syndication"
	if ($resume != 'non') {
		// mode "resume"
		$desc = strlen($data['descriptif']) ?
			$data['descriptif'] : $data['content'];
		$desc = couper(trim(textebrut($desc)), 300);
	} else {
		// mode "full syndication"
		// choisir le contenu pertinent
		// & refaire les liens relatifs
		$desc = strlen($data['content']) ?
			$data['content'] : $data['descriptif'];
		$desc = liens_absolus($desc, $url_syndic);
	}

	// tags & enclosures (preparer spip_syndic_articles.tags)
	$tags = $data['enclosures'];
	# eviter les doublons (cle = url+titre) et passer d'un tableau a une chaine
	if ($data['tags']) {
		$vus = array();
		foreach ($data['tags'] as $tag) {
			$cle = supprimer_tags($tag).extraire_attribut($tag,'href');
			$vus[$cle] = $tag;
		}
		$tags .= ($tags ? ', ' : '') . join(', ', $vus);
	}

	// Mise a jour du contenu (titre,auteurs,description,date?,source...)
	$vals = array(
			'titre' => $data['titre'],
			'lesauteurs' => $data['lesauteurs'],
			'descriptif' => $desc,
			'lang'=> substr($data['lang'],0,10),
			'source' => substr($data['source'],0,255),
			'url_source' => substr($data['url_source'],0,255),
			'tags' => $tags);

	// Mettre a jour la date si lastbuilddate
	if ($data['lastbuilddate'])
		$vals['date']= date("Y-m-d H:i:s", $data['lastbuilddate']);
				    
	sql_updateq('spip_syndic_articles', $vals, "id_syndic_article=$id_syndic_article");

	// Point d'entree post_syndication
	pipeline('post_syndication',
		array(
			$le_lien,
			$now_id_syndic,
			$data
		)
	);

	return $ajout;
}
//
// Insere un article syndique (renvoie true si l'article est nouveau)
// fonction modifi�e pour pmb
function inserer_article_syndique_pmb ($data, $now_id_syndic, $statut, $url_site, $url_syndic, $resume, $documents, &$faits) {
	// Creer le lien s'il est nouveau - cle=(id_syndic,url)
	// On coupe a 255 caracteres pour eviter tout doublon
	// sur une URL de plus de 255 qui exloserait la base de donnees
	$le_lien = substr($data['url'], 0,255);

// Chercher les liens de meme cle
// S'il y a plusieurs liens qui repondent, il faut choisir le plus proche
// (ie meme titre et pas deja fait), le mettre a jour et ignorer les autres

	$n = 0;
	$s = sql_select("id_syndic_article,titre", "spip_syndic_articles_pmb", "url=" . sql_quote($le_lien) . " AND id_syndic=$now_id_syndic AND " . sql_in('id_syndic_article', $faits, 'NOT'), "", "maj DESC");
	while ($a = sql_fetch($s)) {
		$id =  $a['id_syndic_article'];
		if ($a['titre'] == $data['titre']) {
			$id_syndic_article = $id;
			break;
		}
		$n++;
	}
	// S'il y en avait qu'un, le prendre quel que soit le titre
	if ($n == 1)
		$id_syndic_article = $id;
	// Si l'article n'existe pas, on le cree
	elseif (!isset($id_syndic_article)) {
		$ajout = $id_syndic_article = sql_insertq('spip_syndic_articles_pmb',
				array('id_syndic' => $now_id_syndic,
				'url' => $le_lien,
				'date' => date("Y-m-d H:i:s", $data['date']),
				'statut'  => $statut));
		if (!$ajout) return;
	}
	$faits[] = $id_syndic_article;

	// Descriptif, en mode resume ou mode 'full text'
	// on prend en priorite data['descriptif'] si on est en mode resume,
	// et data['content'] si on est en mode "full syndication"
	if ($resume != 'non') {
		// mode "resume"
		$desc = strlen($data['descriptif']) ?
			$data['descriptif'] : $data['content'];
		$desc = couper(trim(textebrut($desc)), 300);
	} else {
		// mode "full syndication"
		// choisir le contenu pertinent
		// & refaire les liens relatifs
		$desc = strlen($data['content']) ?
			$data['content'] : $data['descriptif'];
		$desc = liens_absolus($desc, $url_syndic);
	}

	// tags & enclosures (preparer spip_syndic_articles_pmb.tags)
	$tags = $data['enclosures'];
	# eviter les doublons (cle = url+titre) et passer d'un tableau a une chaine
	if ($data['tags']) {
		$vus = array();
		foreach ($data['tags'] as $tag) {
			$cle = supprimer_tags($tag).extraire_attribut($tag,'href');
			$vus[$cle] = $tag;
		}
		$tags .= ($tags ? ', ' : '') . join(', ', $vus);
	}

	// Mise a jour du contenu (titre,auteurs,description,date?,source...)
	$vals = array(
			'titre' => $data['titre'],
			'lesauteurs' => $data['lesauteurs'],
			'descriptif' => $desc,
			'lang'=> substr($data['lang'],0,10),
			'source' => substr($data['source'],0,255),
			'url_source' => substr($data['url_source'],0,255),
			'pmb_type' => $data['pmb_type'],	
			'pmb_photo_src' => $data['pmb_photo_src'],	
			'pmb_id_notice' => $data['pmb_id_notice'], 
			'pmb_url_base' => substr(html_entity_decode($data['url']), 0, strrpos(html_entity_decode($data['url']), "/")+1), 
			'pmb_isbn'=> $data['pmb_isbn'], 
			'pmb_auteurs'=> $data['pmb_auteurs'], 
			'pmb_editeur'=> $data['pmb_editeur'], 
			'pmb_editeur_lieu' => $data['pmb_editeur_lieu'], 
			'pmb_format' => $data['pmb_format'], 
			'pmb_annee_de_publication' => $data['pmb_annee_de_publication'], 
			'pmb_importance' => $data['pmb_importance'], 
			'pmb_presentation' => $data['pmb_presentation'], 
			'pmb_serie' => $data['pmb_serie'], 
			'pmb_titre2' => $data['pmb_titre2'], 
			'pmb_titre3' => $data['pmb_titre3'], 
			'pmb_titre4' => $data['pmb_titre4'],
			'tags' => $tags);

		
	// Mettre a jour la date si lastbuilddate
	if ($data['lastbuilddate'])
		$vals['date']= date("Y-m-d H:i:s", $data['lastbuilddate']);
				    
	sql_updateq('spip_syndic_articles_pmb', $vals, "id_syndic_article=$id_syndic_article");

	// Point d'entree post_syndication
	pipeline('post_syndication',
		array(
			$le_lien,
			$now_id_syndic,
			$data
		)
	);

	return $ajout;
}
?>
