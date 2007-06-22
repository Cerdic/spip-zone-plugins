<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *  Modifs pour plugin PMB Arnault Pachot - OpenStudio                     *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// Effectuer la syndication d'un unique site, retourne 0 si aucun a faire.
//

// http://doc.spip.org/@executer_une_syndication
function executer_une_syndication() {
	$id_syndic = 0;

	## valeurs modifiables dans mes_options
	## attention il est tres mal vu de prendre une periode < 20 minutes
	define('_PERIODE_SYNDICATION', 2*60);
	define('_PERIODE_SYNDICATION_SUSPENDUE', 24*60);

	// On va tenter un site 'sus' ou 'off' de plus de 24h, et le passer en 'off'
	// s'il echoue
	$where = "syndication IN ('sus','off')
	AND statut='publie'
	AND date_syndic < DATE_SUB(NOW(), INTERVAL
	"._PERIODE_SYNDICATION_SUSPENDUE." MINUTE)";
	$row = spip_fetch_array(spip_query("SELECT id_syndic FROM spip_syndic WHERE $where	ORDER BY date_syndic LIMIT 1"));
	if ($row) {
		$id_syndic = $row["id_syndic"];
		syndic_a_jour($id_syndic, 'off');
	}

	// Et un site 'oui' de plus de 2 heures, qui passe en 'sus' s'il echoue
	$where = "syndication='oui'
	AND statut='publie'
	AND date_syndic < DATE_SUB(NOW(), INTERVAL "._PERIODE_SYNDICATION." MINUTE)";
	$row = spip_fetch_array(spip_query("SELECT id_syndic FROM spip_syndic WHERE $where	ORDER BY date_syndic LIMIT 1"));

	if ($row) {
		$id_syndic = $row["id_syndic"];
		syndic_a_jour($id_syndic, 'sus');
	}
	return $id_syndic;
}


// A partir d'un <dc:subject> ou autre essayer de recuperer
// le mot et son url ; on cree <a href="url" rel="tag">mot</a>
// http://doc.spip.org/@creer_tag
function creer_tag($mot,$type,$url) {
	if (!strlen($mot = trim($mot))) return '';
	$mot = "<a rel=\"tag\">$mot</a>";
	if ($url)
		$mot = inserer_attribut($mot, 'href', $url);
	if ($type)
		$mot = inserer_attribut($mot, 'rel', $type);
	return $mot;
}

// http://doc.spip.org/@ajouter_tags
function ajouter_tags($matches, $item) {
	include_spip('inc/filtres');
	$tags = array();
	foreach ($matches as $match) {
		$type = ($match[3] == 'category' OR $match[3] == 'directory')
			? 'directory':'tag';
		$mot = supprimer_tags($match[0]);
		if (!strlen($mot)) break;
		// rechercher un url
		if ($url = extraire_attribut($match[0], 'domain')
		OR $url = extraire_attribut($match[0], 'resource')
		OR $url = extraire_attribut($match[0], 'url'))
			{}

		## cas particuliers
		else if (extraire_attribut($match[0], 'scheme') == 'urn:flickr:tags') {
			foreach(explode(' ', $mot) as $petit)
				if ($t = creer_tag($petit, $type,
				'http://www.flickr.com/photos/tags/'.rawurlencode($petit).'/'))
					$tags[] = $t;
			$mot = '';
		} else {
			# type del.icio.us
			foreach(explode(' ', $mot) as $petit)
				if (preg_match(',<rdf[^>]* resource=["\']([^>]*/'
				.preg_quote(rawurlencode($petit),',').')["\'],i',
				$item, $m)) {
					$mot = '';
					if ($t = creer_tag($petit, $type, $m[1]))
						$tags[] = $t;
				}
		}

		if ($t = creer_tag($mot, $type, $url))
			$tags[] = $t;
	}
	return $tags;
}


// Retablit le contenu des blocs [[CDATA]] dans un tableau
// http://doc.spip.org/@cdata_echappe_retour
function cdata_echappe_retour(&$table, &$echappe_cdata) {
	foreach ($table as $var => $val) {
		$table[$var] = filtrer_entites($table[$var]);
		foreach ($echappe_cdata as $n => $e)
			$table[$var] = str_replace("@@@SPIP_CDATA$n@@@",
				$e, $table[$var]);
	}
}


// prend un fichier backend et retourne un tableau des items lus,
// et une chaine en cas d'erreur
// http://doc.spip.org/@analyser_backend
function analyser_backend($rss, $url_syndic='') {
	include_spip('inc/texte'); # pour couper()

	
	$rss = pipeline('pre_syndication', $rss);

	
	// Echapper les CDATA
	$echappe_cdata = array();
	if (preg_match_all(',<!\[CDATA\[(.*)]]>,Uims', $rss,
	$regs, PREG_SET_ORDER)) {
		foreach ($regs as $n => $reg) {
			$echappe_cdata[$n] = $reg[1];
			$rss = str_replace($reg[0], "@@@SPIP_CDATA$n@@@", $rss);
		}
	}

	// supprimer les commentaires
	$rss = preg_replace(',<!--\s+.*\s-->,Ums', '', $rss);

	// simplifier le backend, en supprimant les espaces de nommage type "dc:"
	$rss = preg_replace(',<(/?)(dc):,i', '<\1', $rss);

	// chercher auteur/lang dans le fil au cas ou les items n'en auraient pas
	list($header) = preg_split(',<(item|entry)[:[:space:]>],', $rss, 2);
	if (preg_match_all(
	',<(author|creator)>(.*)</\1>,Uims',
	$header, $regs, PREG_SET_ORDER)) {
		$les_auteurs_du_site = array();
		foreach ($regs as $reg) {
			$nom = $reg[2];
			if (preg_match(',<name>(.*)</name>,Uims', $nom, $reg))
				$nom = $reg[1];
			$les_auteurs_du_site[] = trim(textebrut(filtrer_entites($nom)));
		}
		$les_auteurs_du_site = join(', ', array_unique($les_auteurs_du_site));
	} else
		$les_auteurs_du_site = '';

	if (preg_match(',<([^>]*xml:)?lang(uage)?'.'>([^<>]+)<,i',
	$header, $match))
		$langue_du_site = $match[3];

	$items = array();
	if (preg_match_all(',<(item|entry)([:[:space:]][^>]*)?'.
	'>(.*)</\1>,Uims',$rss,$r, PREG_PATTERN_ORDER))
		$items = $r[0];


	
	//
	// Analyser chaque <item>...</item> du backend et le transformer en tableau
	//

	if (!count($items)) return _T('avis_echec_syndication_01');

	foreach ($items as $item) {
		$data = array();

		// URL (semi-obligatoire, sert de cle)

		// guid n'est un URL que si marque de <guid ispermalink="true"> ;
		// attention la valeur par defaut est 'true' ce qui oblige a quelque
		// gymnastique
		if (preg_match(',<guid.*>[[:space:]]*(https?:[^<]*)</guid>,Uims',
		$item, $regs) AND preg_match(',^(true|1)?$,i',
		extraire_attribut($regs[0], 'ispermalink')))
			$data['url'] = $regs[1];

		// <link>, plus classique
		else if (preg_match(
		',<link[^>]*[[:space:]]rel=["\']?alternate[^>]*>(.*)</link>,Uims',
		$item, $regs))
			$data['url'] = $regs[1];
		else if (preg_match(',<link[^>]*[[:space:]]rel=.alternate[^>]*>,Uims',
		$item, $regs))
			$data['url'] = extraire_attribut($regs[0], 'href');
		else if (preg_match(',<link[^>]*>(.*)</link>,Uims', $item, $regs))
			$data['url'] = $regs[1];
		else if (preg_match(',<link[^>]*>,Uims', $item, $regs))
			$data['url'] = extraire_attribut($regs[0], 'href');

		// Aucun link ni guid, mais une enclosure
		else if (preg_match(',<enclosure[^>]*>,ims', $item, $regs)
		AND $url = extraire_attribut($regs[0], 'url'))
			$data['url'] = $url;

		// pas d'url, c'est genre un compteur...
		else
			$data['url'] = '';

		// Titre (semi-obligatoire)
		if (preg_match(",<title[^>]*>(.*?)</title>,ims",$item,$match))
			$data['titre'] = $match[1];
		else if (preg_match(',<link[[:space:]][^>]*>,Uims',$item,$mat)
		AND $title = extraire_attribut($mat[0], 'title'))
			$data['titre'] = $title; 
		if (!strlen($data['titre'] = trim($data['titre'])))
			$data['titre'] = _T('ecrire:info_sans_titre');

		// Date
		$la_date = '';
		if (preg_match(',<(published|modified|issued)>([^<]*)<,Uims',
		$item,$match))
			$la_date = my_strtotime($match[2]);
		if (!$la_date AND
		preg_match(',<(pubdate)>([^<]*)<,Uims',$item, $match))
			$la_date = my_strtotime($match[2]);
		if (!$la_date AND
		preg_match(',<([a-z]+:date)>([^<]*)<,Uims',$item,$match))
			$la_date = my_strtotime($match[2]);
		if (!$la_date AND
		preg_match(',<date>([^<]*)<,Uims',$item,$match))
			$la_date = my_strtotime($match[1]);

		// controle de validite de la date
		// pour eviter qu'un backend errone passe toujours devant
		// (note: ca pourrait etre defini site par site, mais ca risque d'etre
		// plus lourd que vraiment utile)
		if ($GLOBALS['controler_dates_rss']) {
			if ($la_date < time() - 365 * 24 * 3600
			OR $la_date > time() + 48 * 3600)
				$la_date = time();
		}

		$data['date'] = $la_date;

		// Honorer le <lastbuilddate> en forcant la date
		if (preg_match(',<(lastbuilddate|updated|modified)>([^<>]+)</\1>,i',
		$item, $regs)
		AND $lastbuilddate = my_strtotime(trim($regs[2]))
		// pas dans le futur
		AND $lastbuilddate < time())
			$data['lastbuilddate'] = $lastbuilddate;

		// Auteur(s)
		if (preg_match_all(
		',<(author|creator)>(.*)</\1>,Uims',
		$item, $regs, PREG_SET_ORDER)) {
			$auteurs = array();
			foreach ($regs as $reg) {
				$nom = $reg[2];
				if (preg_match(',<name>(.*)</name>,Uims', $nom, $reg))
					$nom = $reg[1];
				$auteurs[] = trim(textebrut(filtrer_entites($nom)));
			}
			$data['lesauteurs'] = join(', ', array_unique($auteurs));
		}
		else
			$data['lesauteurs'] = $les_auteurs_du_site;

		// Description
		if (preg_match(',<((description|summary)([:[:space:]][^>]*)?)'
		.'>(.*)</\2[:>[:space:]],Uims',$item,$match)) {
			$data['descriptif'] = trim($match[4]);
		}
		if (preg_match(',<((content)([:[:space:]][^>]*)?)'
		.'>(.*)</\2[:>[:space:]],Uims',$item,$match)) {
			$data['content'] = trim($match[4]);
		}

		// lang
		if (preg_match(',<([^>]*xml:)?lang(uage)?'.'>([^<>]+)<,i',
			$item, $match))
			$data['lang'] = trim($match[3]);
		else
			$data['lang'] = trim($langue_du_site);

		// source et url_source  (pas trouve d'exemple en ligne !!)
		# <source url="http://www.truc.net/music/uatsap.mp3" length="19917" />
		# <source url="http://www.truc.net/rss">Site source</source>
		if (preg_match(',(<source[^>]*>)(([^<>]+)</source>)?,i',
		$item, $match)) {
			$data['source'] = trim($match[3]);
			$data['url_source'] = str_replace('&amp;', '&',
				trim(extraire_attribut($match[1], 'url')));
		}

		// tags
		# a partir de "<dc:subject>", (del.icio.us)
		# ou <media:category> (flickr)
		# ou <itunes:category> (apple)
		# on cree nos tags microformat <a rel="directory" href="url">titre</a>
		# http://microformats.org/wiki/rel-directory
		$tags = array();
		if (preg_match_all(
		',<(([a-z]+:)?(subject|category|directory|keywords?|tags?|type))[^>]*>'
		.'(.*?)</\1>,ims',
		$item, $matches, PREG_SET_ORDER))
			$tags = ajouter_tags($matches, $item); # array()
		// Pieces jointes : s'il n'y a pas de microformat relEnclosure,
		// chercher <enclosure> au format RSS et les passer en microformat
		if (!afficher_enclosures(join(', ', $tags)))
			if (preg_match_all(',<enclosure[[:space:]][^<>]+>,i',
			$item, $matches, PREG_PATTERN_ORDER))
				$data['enclosures'] = join(', ',
					array_map('enclosure2microformat', $matches[0]));
		$data['item'] = $item;

		// Nettoyer les donnees et remettre les CDATA en place
		cdata_echappe_retour($data, $echappe_cdata);
		cdata_echappe_retour($tags, $echappe_cdata);

		// passer l'url en absolue
		$data['url'] = url_absolue(filtrer_entites($data['url']), $url_syndic);
		
		// Trouver les microformats (ecrase les <category> et <dc:subject>)
		if (preg_match_all(
		',<a[[:space:]]([^>]+[[:space:]])?rel=[^>]+>.*</a>,Uims',
		$data['item'], $regs, PREG_PATTERN_ORDER)) {
			$tags = $regs[0];
		}
		// Cas particulier : tags Connotea sous la forme <a class="postedtag">
		if (preg_match_all(
		',<a[[:space:]][^>]+ class="postedtag"[^>]*>.*</a>,Uims',
		$data['item'], $regs, PREG_PATTERN_ORDER))
			$tags = preg_replace(', class="postedtag",i',
			' rel="tag"', $regs[0]);

		$data['tags'] = $tags;

		$articles[] = $data;
	}

	return $articles;
}

// prend un fichier backend issu de PMB et retourne un tableau des items lus,
// et une chaine en cas d'erreur
// analyser_backend_pmb
function analyser_backend_pmb($rss, $url_syndic='') {
	include_spip('inc/texte'); # pour couper()

	
	$rss = pipeline('pre_syndication', $rss);

	
	// Echapper les CDATA
	$echappe_cdata = array();
	if (preg_match_all(',<!\[CDATA\[(.*)]]>,Uims', $rss,
	$regs, PREG_SET_ORDER)) {
		foreach ($regs as $n => $reg) {
			$echappe_cdata[$n] = $reg[1];
			$rss = str_replace($reg[0], "@@@SPIP_CDATA$n@@@", $rss);
		}
	}

	// supprimer les commentaires
	$rss = preg_replace(',<!--\s+.*\s-->,Ums', '', $rss);

	// simplifier le backend, en supprimant les espaces de nommage type "dc:"
	$rss = preg_replace(',<(/?)(dc):,i', '<\1', $rss);

	// chercher auteur/lang dans le fil au cas ou les items n'en auraient pas
	list($header) = preg_split(',<(item|entry)[:[:space:]>],', $rss, 2);
	if (preg_match_all(
	',<(author|creator)>(.*)</\1>,Uims',
	$header, $regs, PREG_SET_ORDER)) {
		$les_auteurs_du_site = array();
		foreach ($regs as $reg) {
			$nom = $reg[2];
			if (preg_match(',<name>(.*)</name>,Uims', $nom, $reg))
				$nom = $reg[1];
			$les_auteurs_du_site[] = trim(textebrut(filtrer_entites($nom)));
		}
		$les_auteurs_du_site = join(', ', array_unique($les_auteurs_du_site));
	} else
		$les_auteurs_du_site = '';

	if (preg_match(',<([^>]*xml:)?lang(uage)?'.'>([^<>]+)<,i',
	$header, $match))
		$langue_du_site = $match[3];

	$items = array();
	if (preg_match_all(',<(item|entry)([:[:space:]][^>]*)?'.
	'>(.*)</\1>,Uims',$rss,$r, PREG_PATTERN_ORDER))
		$items = $r[0];


	
	//
	// Analyser chaque <item>...</item> du backend et le transformer en tableau
	//

	if (!count($items)) return _T('avis_echec_syndication_01');

	foreach ($items as $item) {
		$data = array();

		//découpage des données
		$description = html_entity_decode($item);
		$description = substr($description, strpos($description, "<description>")+ strlen("<description>"), strpos($description, "</description>") - (strpos($description, "<description>")+ strlen("<description>")));
		

		//0. photo <img...>. si une photo  est présente elle est toujours en premier
		if(substr($description, 0, 4) == "<img")
		{
			$result[0] = substr($description, strpos($description, 'http'));
		
			if (strpos($result[0], "'") != FALSE)
				$data['pmb_photo_src'] = substr($result[0], 0, strpos($result[0], "'") );
			else
				$data['pmb_photo_src'] = substr($result[0], 0, strpos($result[0], "&") );
			
			//serie
			$debut_titre = substr(
				$description, 
				strpos($description, ">") + 1, 
				strpos($description, "[") - (strpos($description, ">") + 1)
				);		

		}
		else
		{
			$data['pmb_photo_src'] = "";
			//serie
			$debut_titre = substr($description, 0, strpos($description, "[") -1);		

		}
		
		
		//1. mention de titre
		//format : {serie}. {tit1} [{typdoc}] ={tit3} :{tit4} ;{tit2}
		
		
		$chaine_tmp = substr($description, strpos($description, '[')+1, strpos($description, ']')-(strpos($description, '[')+1) );
		$data['pmb_type'] = ucfirst($chaine_tmp);
		//echo("<br/>pmb_type = ".$data['pmb_type']);
		//echo("<br/>pmb_photo_src = ".$data['pmb_photo_src']);
		//echo("<br/>debut titre = ".$debut_titre);
			
		//serie
		if (strpos($debut_titre, "&nbsp") != FALSE)
		{
			$data['pmb_serie'] = substr($debut_titre, 0, strpos($debut_titre, "&nbsp") - 1);
		}
		else
		{
			$data['pmb_serie'] = "";
		}
		//echo("<br/>pmb_serie = ".$data['pmb_serie']);
		//echo("<br/>debut titre = ".$debut_titre);

		$titres_suite = substr(
				$description, 
				strpos($description, "]") + 1);

		$titres_suite = substr(	$titres_suite, 0, strpos($titres_suite,".&") );
		//echo("<br/>titre_suite = ".$titres_suite);
		
		if (preg_match('`\/(.)+$`', $titres_suite, $result))
		{
			$data['pmb_auteurs'] = trim( substr($result[0], 1) );	
		}
		else
		{
			$data['pmb_auteurs'] = "";
		}

		$data['pmb_titre2'] = ""; $data['pmb_titre3']=""; $data['pmb_titre4']="";

		if (strpos($titres_suite, "=") != FALSE)
		{
			if (strpos($titres_suite, ":") != FALSE)
			{
				$data['pmb_titre3'] = substr($titres_suite, strpos($titres_suite, "=")+ 1, (strpos($titres_suite, ":")) - (strpos($titres_suite, "=")+ 1));
			}
			else if (strpos($titres_suite, ";") != FALSE)
			{
				$data['pmb_titre3'] = substr($titres_suite, strpos($titres_suite, "=")+ 1, (strpos($titres_suite, ";")) - (strpos($titres_suite, "=")+ 1));
			}
			else if (strpos($titres_suite, "/") != FALSE)
			{
				$data['pmb_titre3'] = substr($titres_suite, strpos($titres_suite, "=")+ 1, (strpos($titres_suite, "/")) - (strpos($titres_suite, "=")+ 1));
			}
			else
			{
				$data['pmb_titre3'] = $titres_suite;
			}
		}
		else if (strpos($titres_suite, ":") != FALSE)
		{
			if (strpos($titres_suite, ";") != FALSE)
			{
				$data['pmb_titre4'] = substr($titres_suite, strpos($titres_suite, ":")+ 1, (strpos($titres_suite, ";")) - (strpos($titres_suite, ":")+ 1));
			}
			else if (strpos($titres_suite, " / ") != FALSE)
			{
				$data['pmb_titre4'] = substr($titres_suite, strpos($titres_suite, ":")+ 1, (strpos($titres_suite, " / ")) - (strpos($titres_suite, ":")+ 1));
			}
			else
			{
				$data['pmb_titre4'] = $titres_suite;
			}
		}
		else if (strpos($titres_suite, ";") != FALSE)
		{
			if (strpos($titres_suite, "/") != FALSE)
			{
				$data['pmb_titre2'] = substr($titres_suite, strpos($titres_suite, ";")+ 1, (strpos($titres_suite, "/")) - (strpos($titres_suite, ";")+ 1));
			}
			else
			{
				$data['pmb_titre2'] = $titres_suite;
			}
		}
		

		//echo("<br/>pmb_auteurs = ".$data['pmb_auteurs']);
		/*echo("<br/>pmb_titre2 = ".$data['pmb_titre2']);
		echo("<br/>pmb_titre3 = ".$data['pmb_titre3']);
		echo("<br/>pmb_titre4 = ".$data['pmb_titre4']);*/
		

		
		//2. mention d'édition - optionnel
		//format : 

		//3. zone de collection et d'éditeur
		$zone_edition = @substr($description, @strpos($description, $titres_suite) + @strlen($titres_suite));
		$zone_edition = @substr($zone_edition, 0, @strpos($zone_edition, "<table>"));
		$zone_edition_ligne1 = @substr(	$zone_edition, 0, @strpos($zone_edition, "<br") - 1);
		$zone_edition_ligne2 = @substr(	$zone_edition, strpos($zone_edition, $zone_edition_ligne1) + @strlen($zone_edition_ligne1) +6);
		$zone_edition_ligne2 = @substr( $zone_edition_ligne2, 0, @strpos($zone_edition_ligne2, "<br") );
		$zone_edition_ligne3 = @substr(	$zone_edition, strpos($zone_edition, $zone_edition_ligne2) + @strlen($zone_edition_ligne2) +5);
		$zone_edition_ligne3 = @substr( $zone_edition_ligne3, 0, @strpos($zone_edition_ligne3, "<br") );
		$zone_edition_ligne4 = @substr(	$zone_edition, strpos($zone_edition, $zone_edition_ligne3) + @strlen($zone_edition_ligne3) +5);
		$zone_edition_ligne4 = @substr( $zone_edition_ligne4, 0, @strpos($zone_edition_ligne4, "<br") );
		
		$zone_edition_ligne1 = trim(substr($zone_edition_ligne1, 8));
		if (strpos($zone_edition_ligne1, "&nbsp;.&nbsp;-&nbsp;") != FALSE)
		{
			$zone_edition_ligne1_partie1 = @substr($zone_edition_ligne1, 0, @strpos($zone_edition_ligne1, "&nbsp;.&nbsp;-&nbsp;") );
			$zone_edition_ligne1_partie2 = @substr($zone_edition_ligne1, @strpos($zone_edition_ligne1, "&nbsp;.&nbsp;-&nbsp;") + 14);
 		}
		else
		{
			$zone_edition_ligne1_partie1 = $zone_edition_ligne1;
			$zone_edition_ligne1_partie2 = "";
		}
		
		if (strpos($zone_edition_ligne1_partie1, ", ") != FALSE)
		{
			$data['pmb_annee_de_publication'] = trim(substr($zone_edition_ligne1_partie1, @strrpos($zone_edition_ligne1_partie1, ", ") + 1));
			if (strpos($zone_edition_ligne1_partie1, ": ") != FALSE)
			{
				$data['pmb_editeur_lieu'] = substr($zone_edition_ligne1_partie1, 0, strpos($zone_edition_ligne1_partie1, ": ") - 1);
				$data['pmb_editeur_lieu'] = str_replace("[", "", $data['pmb_editeur_lieu']);
				$data['pmb_editeur_lieu'] = str_replace("]", "", $data['pmb_editeur_lieu']);
				$data['pmb_editeur'] = substr($zone_edition_ligne1_partie1, strpos($zone_edition_ligne1_partie1, ": ") + 1, strpos($zone_edition_ligne1_partie1, ", ") - strpos($zone_edition_ligne1_partie1, ": ") - 1);
			}
			else
			{
				$data['pmb_editeur_lieu'] = "";
				$data['pmb_editeur'] = substr($zone_edition_ligne1_partie1, 0, strpos($zone_edition_ligne1_partie1, ", ") );
			}
		}
		else
		{
			$data['pmb_annee_de_publication'] = "";

			if (strpos($zone_edition_ligne1_partie1, ": ") != FALSE)
			{
				$data['pmb_editeur_lieu'] = substr($zone_edition_ligne1_partie1, 0, strpos($zone_edition_ligne1_partie1, ": ") - 1);
				$data['pmb_editeur_lieu'] = str_replace("[", "", $data['pmb_editeur_lieu']);
				$data['pmb_editeur_lieu'] = str_replace("]", "", $data['pmb_editeur_lieu']);
				$data['pmb_editeur'] = substr($zone_edition_ligne1_partie1, strpos($zone_edition_ligne1_partie1, ": ") + 1);
			}
			else
			{
				$data['pmb_editeur_lieu'] = "";
				$data['pmb_editeur'] = trim($zone_edition_ligne1_partie1);
			}
		}	

		
		if (strpos($zone_edition_ligne1_partie2, "; ") != FALSE)
		{
			$data['pmb_format'] = trim(substr($zone_edition_ligne1_partie2, strrpos($zone_edition_ligne1_partie2, "; ") + 1));
			if (strpos($zone_edition_ligne1_partie2, ": ") != FALSE)
			{
				$data['pmb_importance'] = trim(substr($zone_edition_ligne1_partie2, 0, strpos($zone_edition_ligne1_partie2, ": ") - 1));
				$data['pmb_presentation'] = trim(substr($zone_edition_ligne1_partie2, strpos($zone_edition_ligne1_partie2, ": ") + 1, strpos($zone_edition_ligne1_partie2, "; ") - strpos($zone_edition_ligne1_partie2, ": ")-1));
			}
			else
			{
				$data['pmb_presentation'] = "";
				$data['pmb_importance'] = trim(substr($zone_edition_ligne1_partie2, 0, strpos($zone_edition_ligne1_partie2, "; ") - 1));
			}

		}
		else
		{
			$data['pmb_format'] = "";

			if (strpos($zone_edition_ligne1_partie2, ": ") != FALSE)
			{
				$data['pmb_importance'] = trim(substr($zone_edition_ligne1_partie2, 0, strpos($zone_edition_ligne1_partie2, ": ") - 1));
				$data['pmb_presentation'] = trim(substr($zone_edition_ligne1_partie2, strpos($zone_edition_ligne1_partie2, ": ") + 1));
			}
			else
			{
				$data['pmb_presentation'] = "";
				$data['pmb_importance'] = trim($zone_edition_ligne1_partie2, 1);
			}
		}	

		//parfois il y a des crochets autour du nombre de pages. on supprime
		$data['pmb_importance'] = str_replace("[", "", $data['pmb_importance']);
		$data['pmb_importance'] = str_replace("]", "", $data['pmb_importance']);
				

		//echo("<br/>pmb_serie = ".$data['pmb_serie']);
		
		/*echo("<br/>pmb_presentation = ".$data['pmb_presentation']);
		echo("<br/>pmb_editeur = ".$data['pmb_editeur']);
		echo("<br/>pmb_importance = ".$data['pmb_importance']);
		echo("<br/>zone edition 1 partie 1 = ".$zone_edition_ligne1_partie1);
		echo("<br/>zone edition 1 partie 2 = ".$zone_edition_ligne1_partie2);
		
		echo("<br/>zone edition 1 = ".$zone_edition_ligne1);
		echo("<br/>zone edition 2 = ".$zone_edition_ligne2);
		echo("<br/>zone edition 3 = ".$zone_edition_ligne3);
		echo("<br/>zone edition 4 = ".$zone_edition_ligne4);*/
		//4. ISBN ou NO commercial
		
		//5. note générale
		
		//6. langues

		//descriptif on recherche le premier <table>
		if (strpos($description, "<tr>") != FALSE)
		{
			$data['descriptif'] = strip_tags(substr($description, strpos($description, "<tr>") + 3), "<br><br/>");
			$data['descriptif'] = substr($data['descriptif'], strpos($data['descriptif'], "</tr>") + 1);
		}
		else
		{
			$data['descriptif'] = "Pas de description.";
		}
		//echo("<br/>pmb_descriptif = ".$data['descriptif']);
		


		if (strpos($description, "<tr>") != FALSE)
		{
			$data['pmb_genre'] = substr($description, strrpos($description, "<tr>") + 20);
			
		}
		else
		{
			$data['pmb_genre'] = "";
		}
		//echo("<br/>pmb_genre = ".$data['pmb_genre']);




		//7. Notices liées
		$data['pmb_notices_liees'] = "";
		
		/*echo("<br/>pmb_notices_liees = ".$data['pmb_notices_liees']);
		echo("<br/>description: $description-fin");*/

		//fin découpage

		
		//recherche du type. il est toujours entre deux crochets.
		//prendre les premiers
		
		/*
		
		
		//recherche des données UNIMARC
		$tbrut = textebrut(html_entity_decode($item));
		$zone_recherche =  substr($tbrut, strpos($tbrut, "]") + 1);
		$zone_recherche =  substr($zone_recherche, 0, strrpos($zone_recherche, "ISBN") - 1);
		$pmb_auteurs = substr($zone_recherche, 0, strpos($zone_recherche, " . -"));
		
		$pmb_editeur = substr($zone_recherche, strlen($pmb_auteurs));
		$pmb_editeur = trim(substr($pmb_editeur, strpos($editeur, " - ")));
		
		$suite = $pmb_editeur;
		
		$pmb_editeur = substr($pmb_editeur, 3, strpos($pmb_editeur, " . - ") - 3);
		$suite = substr($suite, 4);
		$suite = trim(substr($suite, strpos($suite, " . - ")+5));
		
		$pmb_importance = substr($suite, 0, strpos($suite, "p.")+2);
		$pmb_annee_de_publication = substr($pmb_editeur, -4);
		$pmb_presentation = "";
		$pmb_format = substr($suite, strpos($suite, ";") + 1);
		if (preg_match('`:(.)+;`', $suite, $result))
		{
			$pmb_presentation = trim(substr(trim($result[0]), 1));
			$pmb_presentation = trim(substr($pmb_presentation, 0, strlen($pmb_presentation) -1));
		}
		else
		{
			$pmb_presentation = "";
		}

		$pmb_editeur = trim(substr($pmb_editeur, 0, strpos($pmb_editeur, ",")));
		$pmb_editeur = str_replace("[", "", $pmb_editeur);
		$pmb_editeur = str_replace("]", "", $pmb_editeur);
	
		if (strpos($pmb_editeur, ":") == FALSE)
		{
			$pmb_editeur_lieu = "";
		}
		else
		{
			$pmb_editeur_lieu = trim(substr($pmb_editeur, 0, strpos($pmb_editeur, ":")));
			$pmb_editeur = trim(substr($pmb_editeur, strpos($pmb_editeur, ":")+1));
		}
		
		$pmb_auteurs = trim(substr($pmb_auteurs, strpos($pmb_auteurs, "/") + 1));

		$data['pmb_auteurs'] = $pmb_auteurs;
		$data['pmb_editeur'] = $pmb_editeur;
		$data['pmb_editeur_lieu'] = $pmb_editeur_lieu;
		$data['pmb_format'] = $pmb_format;
		$data['pmb_annee_de_publication'] = $pmb_annee_de_publication;
		$data['pmb_importance'] = $pmb_importance;
		$data['pmb_presentation'] = $pmb_presentation;
		*/

		//recherche du résumé
		/*$texte = textebrut(html_entity_decode($item));
		if (strpos($texte, '(fre)')) $result = substr($texte, strpos($texte, '(fre)')+5);
		if (strpos($texte, '(eng)')) $result = substr($result, strpos($result, '(eng)')+5);
		if (strpos($texte, '(ame)')) $result = substr($result, strpos($result, '(ame)')+5);
		if (strpos($texte, '(fro)')) $result = substr($result, strpos($result, '(fro)')+5);
		$data['descriptif'] = $result;*/
		
		//recherche de l'indexation décimale
		/*if (preg_match('`[0-9]+\s(\S)*(\s)*$`', $result, $match))
		{
			$pmb_index_decimale = $match[0];
			echo("<br/>index decimale : $pmb_index_decimale");
		}
		else
		{
			echo("<br/>index decimale : pas trouvee");
		}

		echo("<br/>descriptif : $result");*/
		//recherche du numéro ISBN
		if (preg_match('`[iI][sS][bB][nN](\s)*[0-9]+-[0-9]+-[0-9]+-.`',html_entity_decode($item), $result))
		{
			$data['pmb_isbn'] = trim(substr($result[0], 4));
		}
		else
		{
			$data['pmb_isbn'] = "";
		}
		// URL (semi-obligatoire, sert de cle)

		// guid n'est un URL que si marque de <guid ispermalink="true"> ;
		// attention la valeur par defaut est 'true' ce qui oblige a quelque
		// gymnastique
		if (preg_match(',<guid.*>[[:space:]]*(https?:[^<]*)</guid>,Uims',
		$item, $regs) AND preg_match(',^(true|1)?$,i',
		extraire_attribut($regs[0], 'ispermalink')))
			$data['url'] = $regs[1];

		// <link>, plus classique
		else if (preg_match(
		',<link[^>]*[[:space:]]rel=["\']?alternate[^>]*>(.*)</link>,Uims',
		$item, $regs))
			$data['url'] = $regs[1];
		else if (preg_match(',<link[^>]*[[:space:]]rel=.alternate[^>]*>,Uims',
		$item, $regs))
			$data['url'] = extraire_attribut($regs[0], 'href');
		else if (preg_match(',<link[^>]*>(.*)</link>,Uims', $item, $regs))
			$data['url'] = $regs[1];
		else if (preg_match(',<link[^>]*>,Uims', $item, $regs))
			$data['url'] = extraire_attribut($regs[0], 'href');

		// Aucun link ni guid, mais une enclosure
		else if (preg_match(',<enclosure[^>]*>,ims', $item, $regs)
		AND $url = extraire_attribut($regs[0], 'url'))
			$data['url'] = $url;

		// pas d'url, c'est genre un compteur...
		else
			$data['url'] = '';


		//APA on extrait le id_notice de PMB
		preg_match('`id=[0-9]+$`',$data['url'], $result);		
		$data['pmb_id_notice'] = substr($result[0], 3);

		// Titre (semi-obligatoire)
		if (preg_match(",<title[^>]*>(.*?)</title>,ims",$item,$match))
			$data['titre'] = $match[1];
		else if (preg_match(',<link[[:space:]][^>]*>,Uims',$item,$mat)
		AND $title = extraire_attribut($mat[0], 'title'))
			$data['titre'] = $title; 
		if (!strlen($data['titre'] = trim($data['titre'])))
			$data['titre'] = _T('ecrire:info_sans_titre');

		//cas ou l'auteur se trouve dans le titre
		if (strpos($data['titre'], '/'))
		{
			$data['auteur'] = trim(substr($data['titre'], strrpos($data['titre'], '/')+1));
			$data['titre'] = trim(substr($data['titre'], 0, strrpos($data['titre'], '/')));
		}		
		else
		{
			$data['auteur'] = $data['pmb_auteurs'];
		}
		// Date
		$la_date = '';
		if (preg_match(',<(published|modified|issued)>([^<]*)<,Uims',
		$item,$match))
			$la_date = my_strtotime($match[2]);
		if (!$la_date AND
		preg_match(',<(pubdate)>([^<]*)<,Uims',$item, $match))
			$la_date = my_strtotime($match[2]);
		if (!$la_date AND
		preg_match(',<([a-z]+:date)>([^<]*)<,Uims',$item,$match))
			$la_date = my_strtotime($match[2]);
		if (!$la_date AND
		preg_match(',<date>([^<]*)<,Uims',$item,$match))
			$la_date = my_strtotime($match[1]);

		// controle de validite de la date
		// pour eviter qu'un backend errone passe toujours devant
		// (note: ca pourrait etre defini site par site, mais ca risque d'etre
		// plus lourd que vraiment utile)
		if ($GLOBALS['controler_dates_rss']) {
			if ($la_date < time() - 365 * 24 * 3600
			OR $la_date > time() + 48 * 3600)
				$la_date = time();
		}

		$data['date'] = $la_date;

		// Honorer le <lastbuilddate> en forcant la date
		if (preg_match(',<(lastbuilddate|updated|modified)>([^<>]+)</\1>,i',
		$item, $regs)
		AND $lastbuilddate = my_strtotime(trim($regs[2]))
		// pas dans le futur
		AND $lastbuilddate < time())
			$data['lastbuilddate'] = $lastbuilddate;

		// Auteur(s)
		if (preg_match_all(
		',<(author|creator)>(.*)</\1>,Uims',
		$item, $regs, PREG_SET_ORDER)) {
			$auteurs = array();
			foreach ($regs as $reg) {
				$nom = $reg[2];
				if (preg_match(',<name>(.*)</name>,Uims', $nom, $reg))
					$nom = $reg[1];
				$auteurs[] = trim(textebrut(filtrer_entites($nom)));
			}
			$data['lesauteurs'] = join(', ', array_unique($auteurs));
		}
		else
			$data['lesauteurs'] = $les_auteurs_du_site;

		// Description
		if (preg_match(',<((description|summary)([:[:space:]][^>]*)?)'
		.'>(.*)</\2[:>[:space:]],Uims',$item,$match)) {
			//APA$data['descriptif'] = trim($match[4]);
		}
		if (preg_match(',<((content)([:[:space:]][^>]*)?)'
		.'>(.*)</\2[:>[:space:]],Uims',$item,$match)) {
			$data['content'] = trim($match[4]);
		}

		// lang
		if (preg_match(',<([^>]*xml:)?lang(uage)?'.'>([^<>]+)<,i',
			$item, $match))
			$data['lang'] = trim($match[3]);
		else
			$data['lang'] = trim($langue_du_site);

		// source et url_source  (pas trouve d'exemple en ligne !!)
		# <source url="http://www.truc.net/music/uatsap.mp3" length="19917" />
		# <source url="http://www.truc.net/rss">Site source</source>
		if (preg_match(',(<source[^>]*>)(([^<>]+)</source>)?,i',
		$item, $match)) {
			$data['source'] = trim($match[3]);
			$data['url_source'] = str_replace('&amp;', '&',
				trim(extraire_attribut($match[1], 'url')));
		}

		// tags
		# a partir de "<dc:subject>", (del.icio.us)
		# ou <media:category> (flickr)
		# ou <itunes:category> (apple)
		# on cree nos tags microformat <a rel="directory" href="url">titre</a>
		# http://microformats.org/wiki/rel-directory
		$tags = array();
		if (preg_match_all(
		',<(([a-z]+:)?(subject|category|directory|keywords?|tags?|type))[^>]*>'
		.'(.*?)</\1>,ims',
		$item, $matches, PREG_SET_ORDER))
			$tags = ajouter_tags($matches, $item); # array()
		// Pieces jointes : s'il n'y a pas de microformat relEnclosure,
		// chercher <enclosure> au format RSS et les passer en microformat
		if (!afficher_enclosures(join(', ', $tags)))
			if (preg_match_all(',<enclosure[[:space:]][^<>]+>,i',
			$item, $matches, PREG_PATTERN_ORDER))
				$data['enclosures'] = join(', ',
					array_map('enclosure2microformat', $matches[0]));
		$data['item'] = $item;

		// Nettoyer les donnees et remettre les CDATA en place
		cdata_echappe_retour($data, $echappe_cdata);
		cdata_echappe_retour($tags, $echappe_cdata);

		// passer l'url en absolue
		$data['url'] = url_absolue(filtrer_entites($data['url']), $url_syndic);
		
		// Trouver les microformats (ecrase les <category> et <dc:subject>)
		if (preg_match_all(
		',<a[[:space:]]([^>]+[[:space:]])?rel=[^>]+>.*</a>,Uims',
		$data['item'], $regs, PREG_PATTERN_ORDER)) {
			$tags = $regs[0];
		}
		// Cas particulier : tags Connotea sous la forme <a class="postedtag">
		if (preg_match_all(
		',<a[[:space:]][^>]+ class="postedtag"[^>]*>.*</a>,Uims',
		$data['item'], $regs, PREG_PATTERN_ORDER))
			$tags = preg_replace(', class="postedtag",i',
			' rel="tag"', $regs[0]);

		$data['tags'] = $tags;
		if ($data['pmb_photo_src'] == "")
		{
			//on regarde si une image est attachée à l'item
			if (substr(extraire_attribut(extraire_balise($data['enclosures'], 'a'), 'type'), "image") != FALSE)
			{	
				 $data['pmb_photo_src'] = extraire_attribut(extraire_balise($data['enclosures'], "a"), "href");
			
			}
		}

		$articles[] = $data;
	}

	return $articles;
}




//
// Insere un article syndique (renvoie true si l'article est nouveau)
//
// http://doc.spip.org/@inserer_article_syndique
function inserer_article_syndique ($data, $now_id_syndic, $statut, $url_site, $url_syndic, $resume, $documents) {


	// Creer le lien s'il est nouveau - cle=(id_syndic,url)
	$le_lien = substr($data['url'], 0,255);
	$n = spip_num_rows(spip_query("SELECT * FROM spip_syndic_articles WHERE url=" . spip_abstract_quote($le_lien) . " AND id_syndic=$now_id_syndic"));
	
	if ($n == 0 and !spip_sql_error()) {
		spip_query("INSERT INTO spip_syndic_articles (id_syndic, url, date, statut) VALUES ('$now_id_syndic', " . spip_abstract_quote($le_lien) . ", FROM_UNIXTIME(".$data['date']."), '$statut')");
		$ajout = true;
	}

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

	// Mettre a jour la date si lastbuilddate
	$update_date = $data['lastbuilddate'] ?
		"date = FROM_UNIXTIME(".$data['lastbuilddate'].")," : '';

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
	spip_query("UPDATE spip_syndic_articles SET				titre=" . spip_abstract_quote($data['titre']) .			 ",	".$update_date."								lesauteurs=" . spip_abstract_quote($data['lesauteurs']) . ",			descriptif=" . spip_abstract_quote($desc) . ",					lang=".spip_abstract_quote(substr($data['lang'],0,10)).",			source=".spip_abstract_quote(substr($data['source'],0,255)).",			url_source=".spip_abstract_quote(substr($data['url_source'],0,255)).",		tags=" . spip_abstract_quote($tags) .					 "	WHERE id_syndic='$now_id_syndic' AND url=" . spip_abstract_quote($le_lien));
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
// Insere un article syndique issu de PMB (renvoie true si l'article est nouveau)
//
// inserer_article_syndique_pmb
function inserer_article_syndique_pmb ($data, $now_id_syndic, $statut, $url_site, $url_syndic, $resume, $documents) {


	// Creer le lien s'il est nouveau - cle=(id_syndic,url)
	$le_lien = substr($data['url'], 0,255);
	$n = spip_num_rows(spip_query("SELECT * FROM spip_syndic_articles_pmb WHERE url=" . spip_abstract_quote($le_lien) . " AND id_syndic=$now_id_syndic"));
	
	if ($n == 0 and !spip_sql_error()) {
		spip_query("INSERT INTO spip_syndic_articles_pmb (id_syndic, url, date, statut) VALUES ('$now_id_syndic', " . spip_abstract_quote($le_lien) . ", FROM_UNIXTIME(".$data['date']."), '$statut')");
		$ajout = true;
	}

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

	// Mettre a jour la date si lastbuilddate
	$update_date = $data['lastbuilddate'] ?
		"date = FROM_UNIXTIME(".$data['lastbuilddate'].")," : '';

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




	spip_query("UPDATE spip_syndic_articles_pmb SET				titre=" . spip_abstract_quote($data['titre']) .			 ",	".$update_date."								lesauteurs=" . spip_abstract_quote($data['auteur']) . ",			descriptif=" . spip_abstract_quote($desc) . ",					lang=".spip_abstract_quote(substr($data['lang'],0,10)).",			source=".spip_abstract_quote(substr($data['source'],0,255)).",			url_source=".spip_abstract_quote(substr($data['url_source'],0,255)).",		tags=" . spip_abstract_quote($tags) .",	pmb_type=".spip_abstract_quote($data['pmb_type']).",	pmb_photo_src=".spip_abstract_quote($data['pmb_photo_src']).",	pmb_id_notice=".spip_abstract_quote($data['pmb_id_notice']).", pmb_url_base=".spip_abstract_quote(substr(html_entity_decode($data['url']), 0, strrpos(html_entity_decode($data['url']), "/")+1)).", pmb_isbn=".spip_abstract_quote($data['pmb_isbn']).", pmb_auteurs=".spip_abstract_quote($data['pmb_auteurs']).", pmb_editeur=".spip_abstract_quote($data['pmb_editeur']).", pmb_editeur_lieu=".spip_abstract_quote($data['pmb_editeur_lieu']).", pmb_format=".spip_abstract_quote($data['pmb_format']).", pmb_annee_de_publication=".spip_abstract_quote($data['pmb_annee_de_publication']).", pmb_importance=".spip_abstract_quote($data['pmb_importance']).", pmb_presentation=".spip_abstract_quote($data['pmb_presentation']).", pmb_serie=".spip_abstract_quote($data['pmb_serie']).", pmb_titre2=".spip_abstract_quote($data['pmb_titre2']).", pmb_titre3=".spip_abstract_quote($data['pmb_titre3']).", pmb_titre4=".spip_abstract_quote($data['pmb_titre4'])."		WHERE id_syndic='$now_id_syndic' AND url=" . spip_abstract_quote($le_lien));
	
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
// Mettre a jour le site
//
// http://doc.spip.org/@syndic_a_jour
function syndic_a_jour($now_id_syndic, $statut = 'off') {
	include_spip('inc/texte');

	$result = spip_query("SELECT * FROM spip_syndic WHERE id_syndic='$now_id_syndic'");

	if (!$row = spip_fetch_array($result))
		return;

	$url_syndic = $row['url_syndic'];
	$url_site = $row['url_site'];

	if ($row['moderation'] == 'oui')
		$moderation = 'dispo';	// a valider
	else
		$moderation = 'publie';	// en ligne sans validation

	// Section critique : n'autoriser qu'une seule syndication
	// simultanee pour un site donne
	if (!spip_get_lock("syndication $url_syndic"))
		return;

	spip_query("UPDATE spip_syndic SET syndication='$statut', date_syndic=NOW() WHERE id_syndic='$now_id_syndic'");

	// Aller chercher les donnees du RSS et les analyser
	include_spip('inc/distant');
	$rss = recuperer_page($url_syndic, true);
	if (!$rss)
		$articles = _T('avis_echec_syndication_02');
	else
	{
		if (preg_match( '`<generator>(.)*[Pp][Mm][Bb](.)*</generator>`', $rss))
		{	
			//echo("flux PMB");
			$articles = analyser_backend_pmb($rss, $url_syndic);

			// Les enregistrer dans la base
			if (is_array($articles)) {
				$urls = array();
				foreach ($articles as $data) {
					inserer_article_syndique_pmb ($data, $now_id_syndic, $moderation, $url_site, $url_syndic, $row['resume'], $row['documents']);
					$urls[] = $data['url'];
				}

				// moderation automatique des liens qui sont sortis du feed
				if (count($urls) > 0
				AND $row['miroir'] == 'oui') {
					spip_query("UPDATE spip_syndic_articles_pmb	SET statut='off', maj=maj WHERE id_syndic=$now_id_syndic AND NOT (url IN ("	. join(",", array_map('spip_abstract_quote',$urls))	. "))");
				}
	
				// suppression apres 2 mois des liens qui sont sortis du feed
				if (count($urls) > 0
					AND $row['oubli'] == 'oui') {
					$time = date('U') - 61*24*3600; # deux mois
					spip_query("DELETE FROM spip_syndic_articles_pmb WHERE id_syndic=$now_id_syndic AND UNIX_TIMESTAMP(maj) < $time AND UNIX_TIMESTAMP(date) < $time AND NOT (url IN (" . join(",", array_map('spip_abstract_quote',$urls)) . "))");
				}
				
				//suppression des articles syndiqués avant activation plugin PMB
				spip_query("DELETE FROM spip_syndic_articles WHERE id_syndic=$now_id_syndic");
				
				
				// Noter que la syndication est OK
				spip_query("UPDATE spip_syndic SET syndication='oui' WHERE id_syndic='$now_id_syndic'");
			}
		}
		else
		{	
			//echo("flux non PMB");
			$articles = analyser_backend($rss, $url_syndic);

			// Les enregistrer dans la base
			if (is_array($articles)) {
				$urls = array();
				foreach ($articles as $data) {
					inserer_article_syndique ($data, $now_id_syndic, $moderation, $url_site, $url_syndic, $row['resume'], $row['documents']);
					$urls[] = $data['url'];
				}
	
				// moderation automatique des liens qui sont sortis du feed
				if (count($urls) > 0
					AND $row['miroir'] == 'oui') {
					spip_query("UPDATE spip_syndic_articles	SET statut='off', maj=maj WHERE id_syndic=$now_id_syndic AND NOT (url IN ("	. join(",", array_map('spip_abstract_quote',$urls))	. "))");
				}
		
				// suppression apres 2 mois des liens qui sont sortis du feed
				if (count($urls) > 0
				AND $row['oubli'] == 'oui') {
					$time = date('U') - 61*24*3600; # deux mois
					spip_query("DELETE FROM spip_syndic_articles WHERE id_syndic=$now_id_syndic AND UNIX_TIMESTAMP(maj) < $time AND UNIX_TIMESTAMP(date) < $time AND NOT (url IN (" . join(",", array_map('spip_abstract_quote',$urls)) . "))");
				}


				// Noter que la syndication est OK
				spip_query("UPDATE spip_syndic SET syndication='oui' WHERE id_syndic='$now_id_syndic'");
		
			}
		}

		
	}
	

	// Ne pas oublier de liberer le verrou
	spip_release_lock($url_syndic);


	// Renvoyer l'erreur le cas echeant
	if (!is_array($articles))
		return $articles;
	else
		return false; # c'est bon
}


// helas strtotime ne reconnait pas le format W3C
// http://www.w3.org/TR/NOTE-datetime
// http://doc.spip.org/@my_strtotime
function my_strtotime($la_date) {

	// format complet
	if (preg_match(
	',^([0-9]+-[0-9]+-[0-9]+[T ][0-9]+:[0-9]+(:[0-9]+)?)(\.[0-9]+)?'
	.'(Z|([-+][0-9][0-9]):[0-9]+)?$,',
	$la_date, $match)) {
		$la_date = str_replace("T", " ", $match[1])." GMT";
		return strtotime($la_date) - intval($match[5]) * 3600;
	}

	// YYYY
	if (preg_match(',^([0-9][0-9][0-9][0-9])$,', $la_date, $match))
		return strtotime($match[1]."-01-01");

	// YYYY-MM
	if (preg_match(',^([0-9][0-9][0-9][0-9]-[0-9][0-9])$,', $la_date, $match))
		return strtotime($match[1]."-01");

	// utiliser strtotime en dernier ressort
	$s = strtotime($la_date);
	if ($s > 0)
		return $s;

	// erreur
	spip_log("Impossible de lire le format de date '$la_date'");
	return false;
}


// http://doc.spip.org/@cron_syndic
function cron_syndic($t) {
	$r = executer_une_syndication();
	if (($GLOBALS['meta']['activer_moteur'] == 'oui') &&
	    ($GLOBALS['meta']["visiter_sites"] == 'oui')) {
		include_spip("inc/indexation");
		$r2 = executer_une_indexation_syndic();
		$r = $r && $r2;
	}
	return $r;
}

?>
