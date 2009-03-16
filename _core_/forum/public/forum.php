<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


//
// <BOUCLE(FORUMS)>
//
// http://doc.spip.org/@boucle_FORUMS_dist
function boucle_FORUMS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mstatut = $id_table .'.statut';
	
	// Par defaut, selectionner uniquement les forums sans mere
	// Les criteres {tout} et {plat} inversent ce choix
	// de meme qu'un critere sur {id_forum} ou {id_parent}
	if (!isset($boucle->modificateur['tout']) 
	  AND !isset($boucle->modificateur['plat'])
	  AND !isset($boucle->modificateur['criteres']['id_forum'])
	  AND !isset($boucle->modificateur['criteres']['id_parent'])
	  ) {
		array_unshift($boucle->where,array("'='", "'$id_table." ."id_parent'", 0));
	}
	// Restreindre aux elements publies
	if (!isset($boucle->modificateur['criteres']['statut'])) {
		if ($GLOBALS['var_preview'])
			array_unshift($boucle->where,array("'IN'", "'$mstatut'", "'(\\'publie\\',\\'prive\\')'"));		
		else
			array_unshift($boucle->where,array("'='", "'$mstatut'", "'\\'publie\\''"));
	}

	return calculer_boucle($id_boucle, $boucles); 
}

// {meme_parent}
// http://www.spip.net/@meme_parent
// http://doc.spip.org/@critere_meme_parent_dist
function critere_FORUMS_meme_parent_dist($idb, &$boucles, $crit) {
	global $exceptions_des_tables;
	$boucle = &$boucles[$idb];
	$arg = kwote(calculer_argument_precedent($idb, 'id_parent', $boucles));
	$id_parent = isset($exceptions_des_tables[$boucle->id_table]['id_parent']) ?
		$exceptions_des_tables[$boucle->id_table]['id_parent'] :
		'id_parent';
	$mparent = $boucle->id_table . '.' . $id_parent;

	$boucle->where[]= array("'='", "'$mparent'", $arg);
	$boucle->where[]= array("'>'", "'$mparent'", 0);
	$boucle->modificateur['plat'] = true;
}

// Faute de copie du champ id_secteur dans la table des forums,
// faut le retrouver par jointure
// Pour chaque Row il faudrait tester si le forum est 
// d'article, de breve, de rubrique, ou de syndication.
// Pour le moment on ne traite que les articles,
// les 3 autres cas ne marcheront donc pas: ca ferait 4 jointures
// qu'il faut traiter optimalement ou alors pas du tout.
function public_critere_secteur_forums_dist($idb, &$boucles, $val, $crit)
{
	static $trouver_table;
	if (!$trouver_table)
		$trouver_table = charger_fonction('trouver_table', 'base');

	$desc = $trouver_table('articles', $boucles[$idb]->sql_serveur);
	return calculer_critere_externe_init($boucles[$idb], array($desc['table']), 'id_secteur', $desc, $crit->cond, true);
}


//
// Parametres de reponse a un forum
//

// http://doc.spip.org/@balise_PARAMETRES_FORUM_dist
function balise_PARAMETRES_FORUM_dist($p) {
	$_id_article = champ_sql('id_article', $p);
	$p->code = '
		// refus des forums ?
		(quete_accepter_forum('.$_id_article.')=="non" OR
		($GLOBALS["meta"]["forums_publics"] == "non"
		AND quete_accepter_forum('.$_id_article.') == ""))
		? "" : // sinon:
		';
	// pas de calculs superflus si le site est monolingue
	$lang = strpos($GLOBALS['meta']['langues_utilisees'], ',');

	switch ($p->type_requete) {
		case 'articles':
			$c = '"id_article=".' . champ_sql('id_article', $p);
			if ($lang) $lang = champ_sql('lang', $p);
			break;
		case 'breves':
			$c = '"id_breve=".' . champ_sql('id_breve', $p);
			if ($lang) $lang = champ_sql('lang', $p);
			break;
		case 'rubriques':
			$c = '"id_rubrique=".' . champ_sql('id_rubrique', $p);
			if ($lang) $lang = champ_sql('lang', $p);
			break;
		case 'syndication':
		case 'syndic':
			// passer par la rubrique pour avoir un champ Lang
			// la table syndic n'en ayant pas
			$c =  '"id_syndic=".' . champ_sql('id_syndic', $p);
			if ($lang) $lang = 'sql_getfetsel("lang", "spip_rubriques", ("id_rubrique=" . intval("' . champ_sql('id_rubrique', $p) . '")))';
			break;
		case 'forums':
		default:
		// ATTENTION mettre 'id_rubrique' avant 'id_syndic':
		// a l'execution  lang_parametres_forum
		// y cherchera l'identifiant  donnant la langue
		// et pour id_syndic c'est id_rubrique car sa table n'en a pas
		  
			$liste_table = array ("article","breve","rubrique","syndic","forum");
			$c = '';
			$tables = array();
			foreach ($liste_table as $t) {
				$champ = 'id_' . $t;
				$x = champ_sql($champ, $p);
				$c .= (($c) ? ".\n" : "") . "((!$x) ? '' : ('&$champ='.$x))";
				if ($lang AND $t!='forum') $tables[]= 
				  "'$champ' => '" . table_objet_sql($t) . "'";
			}
			$c = "substr($c,1)";

			if ($lang)
				$lang = "array(" . join(",",$tables) .")";
			break;
	}

	if ($lang) $c = "lang_parametres_forum($c,$lang)";

	// Syntaxe [(#PARAMETRES_FORUM{#SELF})] pour fixer le retour du forum
	# note : ce bloc qui sert a recuperer des arguments calcules pourrait
	# porter un nom et faire partie de l'API.
	$retour = interprete_argument_balise(1,$p);
	if ($retour===NULL)
		$retour = "''";

	// Attention un eventuel &retour=xxx dans l'URL est prioritaire
	$c .= '.
	(($lien = (_request("retour") ? _request("retour") : str_replace("&amp;", "&", '.$retour.'))) ? "&retour=".rawurlencode($lien) : "")';

	$c = '('.$c.')';
	// Ajouter le code d'invalideur specifique a cette balise
	include_spip('inc/invalideur');
	if ($i = charger_fonction('code_invalideur_forums','',true))
		$p->code .= $i($p, $c);
	else
		$p->code .= $c;

	$p->interdire_scripts = false;
	return $p;
}



# retourne le champ 'accepter_forum' d'un article
# semble ne plus servir nulle part
if(!function_exists('quete_accepter_forum')) {
function quete_accepter_forum($id_article) {
	// si la fonction est appelee en dehors d'une boucle
	// article (forum de breves), $id_article est nul
	// mais il faut neanmoins accepter l'affichage du forum
	// d'ou le 0=>'' (et pas 0=>'non').
	static $cache = array(0 => '');
	
	$id_article = intval($id_article);

	if (isset($cache[$id_article]))	return $cache[$id_article];

	return $cache[$id_article] = sql_getfetsel('accepter_forum','spip_articles',"id_article=$id_article");
}
}

// Ajouter "&lang=..." si la langue du forum n'est pas celle du site.
// Si le 2e parametre n'est pas une chaine, c'est qu'on n'a pas pu
// determiner la table a la compil, on le fait maintenant.
// Il faudrait encore completer: on ne connait pas la langue
// pour une boucle forum sans id_article ou id_rubrique donné par le contexte
// et c'est signale par un message d'erreur abscons: "table inconnue forum".
// 
// http://doc.spip.org/@lang_parametres_forum
if(!function_exists('lang_parametres_forum')) {
function lang_parametres_forum($qs, $lang) {
	if (is_array($lang) AND preg_match(',id_(\w+)=([0-9]+),', $qs, $r)) {
		$id = 'id_' . $r[1];
		if ($t = $lang[$id])
			$lang = sql_getfetsel('lang', $t, "$id=" . $r[2]);
	}
  // Si ce n'est pas la meme que celle du site, l'ajouter aux parametres

	if ($lang AND $lang <> $GLOBALS['meta']['langue_site'])
		return $qs . "&lang=" . $lang;

	return $qs;
}
}

// Pour que le compilo ajoute un invalideur a la balise #PARAMETRES_FORUM
// Noter l'invalideur de la page contenant ces parametres,
// en cas de premier post sur le forum
// http://doc.spip.org/@code_invalideur_forums
function code_invalideur_forums_dist($p, $code) {
	return $code;
}
?>