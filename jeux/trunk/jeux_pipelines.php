<?php
#ini_set('display_errors','1'); error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
if (!defined("_ECRIRE_INC_VERSION")) return;
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#

include_spip('jeux_utils');
// tableau de parametres exploitables par les plugins
global $jeux_config;
// debut d'indexage des jeux presents sur la page
global $debut_index_jeux;

// fonction pre-traitement, pipeline pre_propre
// insertion de quelques balises de reconnaissance
// $texte_brut est vrai si le texte est issu d'un jeu en base : le traitement en est simplifie.
function jeux_pre_propre($texte, $texte_brut=false) {
	
	// commencer par isoler le jeu
	if(!strlen($texte)) return '';
	if($texte_brut)
		// texte brut d'un jeu en base
		$texteAvant = $texteApres = '';
	else {
		// contenu d'un article normal
		if(strpos($texte, _JEUX_DEBUT)===false || strpos($texte, _JEUX_FIN)===false) return $texte;
		// isoler le jeu...
		list($texteAvant, $suite) = explode(_JEUX_DEBUT, $texte, 2); 
		list($texte, $texteApres) = explode(_JEUX_FIN, $suite, 2); 
	}

	// s'il n'est pas present dans un formulaire envoye,
	// l'identifiant du jeu est choisi au hasard...
	// ca peut servir en cas d'affichage de plusieurs articles par page.
	// en passant tous les jeux en ajax, ce ne sera plus la peine.
	// cet identifiant pourrait etre remplace par l'id_jeu d'un jeu present en base 
	global $debut_index_jeux;
	if(!isset($debut_index_jeux))
		$debut_index_jeux = _request('debut_index_jeux')?_request('debut_index_jeux'):rand(10000, 99999);

	// creer un index d'affichage, servant a differencier plusieurs jeux sur une page en l'absence d'AJAX
	static $indexJeux = null;
	if(isset($indexJeux)) ++$indexJeux;
		else $indexJeux = $debut_index_jeux;
	// en cas de formulaire CVT ajax, le jeu est toujours unique et son $indexJeux est deja connu 
	$indexJeuxReel = (_request('var_ajax')=='form' && _request('jeu_cvt')=='oui' && _request('index_jeux'))
		?_request('index_jeux'):$indexJeux;

	// ...decoder le texte obtenu en fonction des signatures et inclure le jeu
	$liste = jeux_liste_les_jeux($texte);
	jeux_decode_les_jeux($texte, $indexJeuxReel);
	// calcul des fichiers necessaires pour le header
	if(count($liste)) {
		// on oblige qd meme jeux.css et jeux.js si un jeu est detecte
		$header = jeux_stylesheet_html('jeux') ."\n". jeux_javascript('jeux') . "\n";
		// css et js des jeux detectes
		foreach($liste as $jeu) $header .= jeux_stylesheet($jeu) . "\n";
		foreach($liste as $jeu) $header .= jeux_javascript($jeu) . "\n";
		$header = htmlentities(preg_replace(",\n+,", "||", trim($header)));
		$header = jeux_rem('JEUX-HEAD', count($liste), base64_encode($header));
	} else $header = '';

	return $texteAvant . $header
		. jeux_rem('PLUGIN-DEBUT', $indexJeuxReel, join('/', $liste))
		. "<div id='JEU$indexJeuxReel' class='jeux_global'>$texte</div>"
#		. "<div id='JEU$indexJeuxReel' class='jeux_global ajax'>$texte</div>"
		. jeux_rem('PLUGIN-FIN', $indexJeuxReel)
		. jeux_pre_propre($texteApres);
}

// fonction post-traitement, pipeline post_propre
// les jeux sont reellement decryptes
function jeux_post_propre($texte) { 
	$texte = echappe_retour($texte, 'JEUX');

	$sep1 = '['._JEUX_POST.'|'; $sep2 = '@@]';
	if (strpos($texte, $sep1)===false || strpos($texte, $sep2)===false) return $texte;
	
	// isoler les parametres...
	list($texteAvant, $suite) = explode($sep1, $texte, 2);
	list($texte, $texteApres) = explode($sep2, $suite, 2);
	list($fonc, $texteJeu, $indexJeux) = explode('|', $texte, 3);
	if (function_exists($fonc)) $texte = $fonc($texteJeu, $indexJeux);

	return $texteAvant.$texte.jeux_post_propre($texteApres);
}


// pipeline header_prive
function jeux_header_prive($flux){
	include_spip('public/assembler');
	include_spip('jeux_utils');
	global $jeux_header_prive, $jeux_javascript_prive;
	$flux .= _JEUX_HEAD1
		. "<link rel='stylesheet' href='../spip.php?page=jeux.css' type='text/css' media='projection, screen' />"
		. "<link rel='stylesheet' href='".find_in_path('prive/jeux_prive.css')."' type='text/css' media='projection, screen' />";
	foreach($jeux_header_prive as $s) $flux .= jeux_stylesheet($s);
	foreach($jeux_javascript_prive as $s) $flux .= jeux_javascript($s);
	return $flux;
}

// pipeline insert_head
function jeux_insert_head($flux){
	include_spip('jeux_utils');
	return $flux . _JEUX_HEAD2;
}

// Le pipeline affichage_final, execute a chaque hit sur toute la page
// Recherche tous les "title=JEUX-HEAD(...)" --> et incorporation a la place de _JEUX_HEAD2
// dans <head> des fichiers js et css necessaires.
function jeux_affichage_final($flux) {
	preg_match_all(",'JEUX-HEAD-#[0-9]+ `([^>]*)`',", $flux, $matches, PREG_SET_ORDER);
	if(!count($matches)) return $flux;
	$liste = array(_JEUX_HEAD2);
	foreach ($matches as $val) $liste = array_merge($liste, explode('||', base64_decode($val[1])));
	$liste = array_unique($liste);
 	$header = html_entity_decode(join("\n",$liste));
	return str_replace(_JEUX_HEAD2, $header."\n\n", $flux);
}







/**
 * Optimiser la base de donnees en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @param int $n
 * @return int
 */
function jeux_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('jeux'=>'*'),'*');
	// Supprimer les résultats sans auteurs
	$res           = sql_select   ("spip_jeux_resultats.id_resultat AS id","spip_jeux_resultats LEFT JOIN spip_auteurs ON spip_jeux_resultats.id_auteur = spip_auteurs.id_auteur","spip_auteurs.id_auteur IS NULL");     
	$flux['data'] += optimiser_sansref('spip_jeux_resultats','id_resultat',$res);
	// Supprimer les résultats sans jeux
	$res           = sql_select   ("spip_jeux_resultats.id_resultat AS id","spip_jeux_resultats LEFT JOIN spip_jeux ON spip_jeux_resultats.id_jeu = spip_jeux.id_jeu","spip_jeux.id_jeu IS NULL");     
	$flux['data'] += optimiser_sansref('spip_jeux_resultats','id_resultat',$res);
	return $flux;
}



// Afficher liens vers les résultats de l'auteur
function jeux_affiche_gauche($flux){
     if ($flux['args']['exec'] == 'auteur') {
        $flux['data'].= boite_ouvrir('','info');
        $flux['data'].= '<a href="'.generer_url_ecrire('jeux_resultats','id_auteur='.$flux['args']['id_auteur']).'">'._T('jeux:voir_resultats').'</a>';
        $flux['data'].= boite_fermer();  
        
     }
      if ($flux['args']['exec'] == 'infos_perso') {
        $flux['data'].= boite_ouvrir('','info');
        $flux['data'].= '<a href="'.generer_url_ecrire('jeux_resultats','id_auteur='.$GLOBALS['auteur_session']['id_auteur']).'">'._T('jeux:voir_resultats').'</a>';
        $flux['data'].= boite_fermer();  
        
     }
    return $flux;    
}

?>
