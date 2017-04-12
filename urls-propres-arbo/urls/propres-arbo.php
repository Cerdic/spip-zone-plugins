<?php
/*****************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                             *
 *                                                                           *
 *  Copyright (c) 2007                                                       *
 *  Boris Lechner - boris.lechner@gmail.com                                  *
 *                                                                           *
 *  Version 1.6 - 2007/05/04                                                 *
 *                                                                           *
 *  Ce fichier étant fortement inspiré de propres.php, je laisse les         *
 *  crédits de ce dernier ci-dessous.                                        *
 *                                                                           *
 *  This program is free software; you can redistribute it and/or modify     *
 *  it under the terms of the GNU General Public License as published by     *
 *  the Free Software Foundation; either version 2 of the License, or        *
 *  (at your option) any later version.                                      *
 *                                                                           *
 *  This program is distributed in the hope that it will be useful,          *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of           *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
 *  GNU General Public License for more details.                             *
 *                                                                           *
 *  You should have received a copy of the GNU General Public License        *
 *  along with this program; if not, write to the Free Software              *
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA *
\*****************************************************************************/

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return; // securiser
if (!function_exists('generer_url_article')) { // si la place n'est pas prise

/*

- Comment utiliser ce jeu d'URLs ?

Recopiez le fichier "htaccess-propres-arbo.txt" du package sous
le sous le nom ".htaccess" (attention a ne pas ecraser d'autres reglages
que vous pourriez avoir mis dans ce fichier)
Les URLs definies seront alors redirigees vers les fichiers de SPIP.

Definissez ensuite dans ecrire/mes_options.php :
	< ?php $type_urls = 'propres-arbo'; ? >
SPIP calculera alors ses liens sous la forme "Mon-titre-deRubrique/Mon-titre-d-article.html".

*/

///////////////////////////////////////////////////////////////////////////////
// VARIABLES DE CONFIGURATION 

define ('_terminaison_urls_propres', '');
define ('_debut_urls_propres', '');

/**
 * la longueur maximale de la totalité de l’url (0 pour ne pas limiter) 
 */
define ('_taille_max_url_propre', '0');

/**
 * la longueur minimale de la totalité de l’url (0 pour ne pas limiter). 
 * Si la taille de l’url calculée est plus petite, celle-ci sera remplacée 
 * par son type suivi de son identifiant (exemples : rubrique1, article42,...) 
 */
define ('_taille_min_url_propre', '0');

/**
 * la longueur maximale d’un niveau de l’url (0 pour ne pas limiter)
 */ 
define ('_taille_max_champ_url_propre', '0');

/**
 * booléen indiquant si les majuscules sont autorisées dans l’url (0 pour interdire, 1 pour autoriser)
 */
define ('_mIxEdCaSe', '0');

// NE PLUS RIEN TOUCHER SOUS CETTE LIGNE
///////////////////////////////////////////////////////////////////////////////

function nomme_feuille($url, $chaine) {
	return @preg_replace(',/([^/]*)$,u', "/$chaine\\1", $url).$chaine; 
}


function obtenir_arborescence($type, $id_objet) {
	if ($id_objet != 0) {
		if ( $type != 'rubrique' ) {
			$table  = "spip_".table_objet($type);
			$col_id = id_table_objet($type);
			
			// Auteurs : on prend le nom
			if ($type == 'auteur')
				$select = 'nom AS titre';
			else if ($type == 'site' OR $type=='syndic')
				$select = 'nom_site AS titre';
			else if ($type == 'mot')
				$select = 'titre';
			else
				$select = 'id_rubrique, titre';
			
			$result = spip_query("SELECT $select FROM $table WHERE $col_id=$id_objet");
			$row    = spip_fetch_array($result);
			$id     = $row['id_rubrique'];
			
		}
		else {
			$result = spip_query("SELECT id_parent, titre FROM spip_rubriques WHERE id_rubrique=$id_objet");
			$row    = spip_fetch_array($result);
			$id     = $row['id_parent'];
		}
		
		$titre = translitteration(corriger_caracteres(supprimer_tags(supprimer_numero(extraire_multi($row['titre'])))));
		if ( _taille_max_champ_url_propre != 0 && strlen($titre) > _taille_max_champ_url_propre ) {
			$titre = substr($titre, 0, _taille_max_champ_url_propre);
		}
		
		if ( $id != 0 )
			$url = obtenir_arborescence('rubrique', $id).'/'.$titre;
		else
			$url = $titre;
	}
	else
		$url="";
	
	return $url;
}

// http://code.spip.net/@_generer_url_propre
function _generer_url_propre($type, $id_objet) {
	$table = "spip_".table_objet($type);
	$col_id = id_table_objet($type);

	// Auteurs : on prend le nom
	if ($type == 'auteur')
		$champ_titre = 'nom AS titre';
	else if ($type == 'site' OR $type=='syndic')
		$champ_titre = 'nom_site AS titre';
	else
		$champ_titre = 'titre';

    // Mots-cles : pas de champ statut
    if ($type == 'mot')
        $statut = "'publie' as statut";
    else
        $statut = 'statut';

    // Articles : récupère l'id_rubrique
    if ($type == 'article')
        $statut = "id_rubrique as id_article_rubrique";
    else
        $statut = "'0' as id_article_rubrique";
	
	// D'abord, essayer de recuperer l'URL existante si possible
	$result = spip_query("SELECT url_propre, $statut, $champ_titre FROM $table WHERE $col_id=$id_objet");
	if (!($row = spip_fetch_array($result))) return ""; # objet inexistant
	
	// Si l'on n'est pas dans spip_redirect.php3 sur un objet non publie
	// ou en preview (astuce pour corriger un url-propre) + admin connecte
	// Ne pas recalculer l'url-propre,
	// sauf si :
	// 1) il n'existe pas, ou
	// 2) l'objet n'est pas 'publie' et on est admin connecte, ou
	// 3) on le demande explicitement (preview) et on est admin connecte
	$modif_url_propre = false;
	
	if (function_exists('action_redirect_dist') AND
	($GLOBALS['preview'] OR ($row['statut'] <> 'publie'))
	AND $GLOBALS['auteur_session']['statut'] == '0minirezo')
		$modif_url_propre = true;
	
	if ($row['url_propre'] AND !$modif_url_propre)
		return $row['url_propre'];
	
	// Sinon, creer l'URL
	include_spip('inc/filtres');
	include_spip('inc/charsets');
	//$url = translitteration(corriger_caracteres(
	//	supprimer_tags(supprimer_numero(extraire_multi($row['titre'])))
	//	));
	$url = obtenir_arborescence($type, $id_objet);
	
	//$url = @preg_replace(',[^/[^[:punct:][:space:]]]+,u', ' ', $url);
	$url = @preg_replace(',[[:space:]&"\'(){}\[\]-_~#|`\^¨!:;.\,?\\\\]+,u', ' ', $url);
	
	// S'il reste trop de caracteres non latins, ou trop peu
	// de caracteres latins, utiliser l'id a la place
	if (preg_match(",([^a-zA-Z0-9 /+*=].*){5},", $url, $r)
	OR strlen($url)<_taille_min_url_propre) {
		$url = $type.$id_objet;
	}
	else {
		$mots = preg_split(',([^a-zA-Z0-9/+*=])+,', $url);
		$url = '';
		foreach ($mots as $mot) {
			if (!$mot) continue;
			$url2 = $url.'-'.$mot;
			if ( _taille_max_url_propre > 0 && strlen($url2) > _taille_max_url_propre ) {
				break;
			}
			$url = $url2;
		}
		$url = substr($url, 1);
// 		echo "$url<br>";
		if (strlen($url) < 2) $url = $type.$id_objet;
	}
	
    // On enleve les tirets autour des slashs
    $url = @preg_replace(',-*/-*,u', '/', $url);
	
	if ( _mIxEdCaSe == 0 )
		$url = strtolower($url);
        
    /**
     * Si on touche à des article de l'accueil
     * Modification spécifique Croix Saint Simon
     * dla 2007/11/30
     */
    if ($row['id_article_rubrique'] == 1) {
    	$url = @preg_replace(array('/accueil\//', '/Accueil\//'), '', $url);
    }
    
	
	// Verifier les eventuels doublons et mettre a jour
	$lock = "url $type $id_objet";
	spip_get_lock($lock, 10);

	$n = spip_num_rows(spip_query("SELECT $col_id FROM $table WHERE url_propre=" . spip_abstract_quote($url) . " AND $col_id != $id_objet LIMIT 1"));
	if ($n > 0) {
		$url = $url.','.$id_objet;
	}

	// Eviter de tamponner les URLs a l'ancienne (cas d'un article
	// intitule "auteur2")
	if ($type == 'article'
	AND preg_match(',^(article|breve|rubrique|mot|auteur)[0-9]+$,', $url))
		$url = $url.','.$id_objet;

	// Mettre a jour dans la base
	spip_query("UPDATE $table SET url_propre=" . spip_abstract_quote($url) . " WHERE $col_id=$id_objet");

	spip_release_lock($lock);

	spip_log("Creation de l'url propre '$url' pour $col_id=$id_objet");

	return $url;
}

// http://code.spip.net/@generer_url_article
function generer_url_article($id_article) {
	$url = _generer_url_propre('article', $id_article);
	if ($url)
		return _debut_urls_propres . $url . _terminaison_urls_propres.".html";
	else
		return get_spip_script('./')."?page=article&id_article=$id_article";
}

// http://code.spip.net/@generer_url_rubrique
function generer_url_rubrique($id_rubrique) {
	$url = _generer_url_propre('rubrique', $id_rubrique);
	if ($url)
		return _debut_urls_propres .$url._terminaison_urls_propres."/";
	else
		return get_spip_script('./')."?page=rubrique&id_rubrique=$id_rubrique";
}

// http://code.spip.net/@generer_url_breve
function generer_url_breve($id_breve) {
	$url = _generer_url_propre('breve', $id_breve);
	if ($url)
		return _debut_urls_propres . nomme_feuille($url, '+') . _terminaison_urls_propres.".html";
	else
		return get_spip_script('./')."?page=breve&id_breve=$id_breve";
}

// http://code.spip.net/@generer_url_forum
function generer_url_forum($id_forum, $show_thread=false) {
	include_spip('inc/forum');
	return generer_url_forum_dist($id_forum, $show_thread);
}

// http://code.spip.net/@generer_url_mot
function generer_url_mot($id_mot) {
	$url = _generer_url_propre('mot', $id_mot);
	if ($url)
		return _debut_urls_propres . '+-'.$url.'-+'._terminaison_urls_propres.".html";
	else
		return get_spip_script('./')."?page=mot&id_mot=$id_mot";
}

// http://code.spip.net/@generer_url_auteur
function generer_url_auteur($id_auteur) {
	$url = _generer_url_propre('auteur', $id_auteur);
	if ($url)
		return _debut_urls_propres . '_'.$url.'_'._terminaison_urls_propres.".html";
	else
		return get_spip_script('./')."?page=auteur&id_auteur=$id_auteur";
}

// http://code.spip.net/@generer_url_site
function generer_url_site($id_syndic) {
	$url = _generer_url_propre('site', $id_syndic);
	if ($url)
		return _debut_urls_propres . '@'.$url.'@'._terminaison_urls_propres.".html";
	else
		return get_spip_script('./')."?page=site&id_syndic=$id_syndic";
}

// http://code.spip.net/@generer_url_document
function generer_url_document($id_document) {
	if (($id_document = intval($id_document)) <= 0)
		return '';
	if (($GLOBALS['meta']["creer_htaccess"]) == 'oui')
	  return generer_url_action('autoriser',"arg=$id_document", true);
	$row = @spip_fetch_array(spip_query("SELECT fichier FROM spip_documents WHERE id_document = $id_document"));
	if ($row) return ($row['fichier']);
	return '';
}

// http://code.spip.net/@recuperer_parametres_url
function recuperer_parametres_url(&$fond, $url) {
	global $contexte;
	$id_objet = 0;

	// Migration depuis anciennes URLs ?
	if ($GLOBALS['_SERVER']['REQUEST_METHOD'] != 'POST' AND
	(preg_match(
	',(^|/)(article|breve|rubrique|mot|auteur|site)(\.php3?|[0-9]+\.html)'
	.'([?&].*)?$,', $url, $regs)
	)) {
		$type = $regs[3];
		$id_objet = intval($GLOBALS[$id_table_objet = id_table_objet($type)]);
	}

	/* Compatibilite urls-page */
	else if (preg_match(
	',[?/&](article|breve|rubrique|mot|auteur|site)[=]?([0-9]+),',
	$url, $regs)) {
		$type = $regs[1];
		$id_objet = $regs[2];
	}

	if ($id_objet) {
		$func = "generer_url_$type";
		$url_propre = $func($id_objet);
		if (strlen($url_propre)
		AND !strstr($url,$url_propre)) {
			include_spip('inc/headers');
			http_status(301);
			// recuperer les arguments supplementaires (&debut_xxx=...)
			$reste = preg_replace('/^&/','?',
				preg_replace("/[?&]$id_table_objet=$id_objet/",'',$regs[5]));
			redirige_par_entete("$url_propre$reste");
		}
	}
	/* Fin compatibilite anciennes urls */

	// Chercher les valeurs d'environnement qui indiquent l'url-propre
	if (isset($GLOBALS['_SERVER']['REDIRECT_url_propre']))
		$url_propre = $GLOBALS['_SERVER']['REDIRECT_url_propre'];
	elseif (isset($GLOBALS['HTTP_ENV_VARS']['url_propre']))
		$url_propre = $GLOBALS['HTTP_ENV_VARS']['url_propre'];
	else {
		$url = substr($url, strpos($url, '/') + 1);
		$url_propre = preg_replace(',[?].*,', '', $url);
	}
	// Mode Query-String ?
	$adapter_le_fond = false;
	if (!$url_propre
	AND preg_match(',([?])([^=/?&]+)(&.*)?$,', $GLOBALS['REQUEST_URI'], $r)) {
		$url_propre = $r[2];
		$adapter_le_fond = true;
	}
	if (!$url_propre) return;

	
	// Compatilibite avec propres2
// 	$url_propre = preg_replace(',\.html$,i', '', $url_propre);

	
	// Detecter les differents types d'objets demandes
	if (preg_match(',^(.*)(\+-)([^/\.]+)-\+.html$,', $url_propre, $regs)) {
		$type = 'mot';
		$url_propre = $regs[1].$regs[3];
	}
	else if (preg_match(',^(.*)(\+)([^/\.]+)\+.html$,', $url_propre, $regs)) {
		$type = 'breve';
		$url_propre = $regs[1].$regs[3];
	}
	else if (preg_match(',^(.*)(_)([^/\.]+)_.html$,', $url_propre, $regs)) {
		$type = 'auteur';
		$url_propre = $regs[1].$regs[3];
	}
	else if (preg_match(',^(.*)(@)([^/\.]+)@.html$,', $url_propre, $regs)) {
		$type = 'syndic';
		$url_propre = $regs[1].$regs[3];
	}
	else if (preg_match(',^(.*[^/\.]+).html$,', $url_propre, $regs)) {
		$type = 'article';
		$url_propre = $regs[1];
	}
	else {
		$type = 'rubrique';
 		preg_match(',^(.*)(/?)$,U', $url_propre, $regs);
		$url_propre = $regs[1];
	}
	
	$table = "spip_".table_objet($type);
	$col_id = id_table_objet($type);
	$result = spip_query("SELECT $col_id FROM $table WHERE url_propre=" . spip_abstract_quote($url_propre));

	if ($row = spip_fetch_array($result)) {
		$contexte[$col_id] = $row[$col_id];
	}

	// En mode Query-String, on fixe ici le $fond utilise
	if ($adapter_le_fond)
		$fond = $type;
	
	return;
}

}


?>
