<?php

// * Acces restreint, plugin pour SPIP * //
if (!defined("_ECRIRE_INC_VERSION")) return;

// declarer le pipeline pour le core
$GLOBALS['spip_pipeline']['AccesRestreint_liste_zones_autorisees']='';

// Si on n'est pas connecte, aucune autorisation n'est disponible
// pas la peine de sortir la grosse artillerie
if (!isset($GLOBALS['auteur_session']['id_auteur'])){
	$GLOBALS['AccesRestreint_zones_autorisees'] = '';
}
else {
	// Pipeline : calculer les zones autorisees, sous la forme '1,2,3'
	// TODO : avec un petit cache pour eviter de solliciter la base de donnees
	$GLOBALS['AccesRestreint_zones_autorisees'] =
		pipeline('AccesRestreint_liste_zones_autorisees', '');
}

// Ajouter un marqueur de cache pour le differencier selon les autorisations
if (!isset($GLOBALS['marqueur'])) $GLOBALS['marqueur'] = '';
$GLOBALS['marqueur'] .= ":AccesRestreint_zones_autorisees="
	.$GLOBALS['AccesRestreint_zones_autorisees'];

//
// Autorisations
//

if(!function_exists('autoriser_rubrique_voir')) {
function autoriser_rubrique_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint');
	static $rub_exclues;
	if (isset($opt['publique']))
		$publique = $opt['publique'];
	else
		$publique = _DIR_RESTREINT!="";
	if (!isset($rub_exclues[$publique]) || !is_array($rub_exclues[$publique])) {
		// Si autoriser est appelee pour un autre auteur que l'auteur connecte  ou si pas d'auteur connecte , on passe $id_auteur en parametre
		if(isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']) && $qui['id_auteur']!=$GLOBALS['auteur_session']['id_auteur'])
			$rub_exclues[$publique] = AccesRestreint_liste_rubriques_exclues($publique,$qui['id_auteur']);
		elseif (!isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']))
			$rub_exclues[$publique] = AccesRestreint_liste_rubriques_exclues($publique,$qui['id_auteur']);
		else
			$rub_exclues[$publique] = AccesRestreint_liste_rubriques_exclues($publique);
		$rub_exclues[$publique] = array_flip($rub_exclues[$publique]);
	}
	return !isset($rub_exclues[$publique][$id]);
}
}
if(!function_exists('autoriser_article_voir')) {
function autoriser_article_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint');
	static $art_exclus;
	if (isset($opt['publique']))
		$publique = $opt['publique'];
	else
		$publique = _DIR_RESTREINT!="";
	if (!isset($art_exclus[$publique]) || !is_array($art_exclus[$publique])) {
		// Si autoriser est appelee pour un autre auteur que l'auteur connecte  ou si pas d'auteur connecte , on passe $id_auteur en parametre
		if(isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']) && $qui['id_auteur']!=$GLOBALS['auteur_session']['id_auteur'])
			$art_exclus[$publique] = AccesRestreint_liste_articles_exclus($publique,$qui['id_auteur']);
		elseif (!isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']))
			$art_exclus[$publique] = AccesRestreint_liste_articles_exclus($publique,$qui['id_auteur']);
		else
			$art_exclus[$publique] = AccesRestreint_liste_articles_exclus($publique);
		$art_exclus[$publique] = array_flip($art_exclus[$publique]);
	}
	return !isset($art_exclus[$publique][$id]);
}
}
if(!function_exists('autoriser_breve_voir')) {
function autoriser_breve_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint');
	static $breves_exclues;
	if (isset($opt['publique']))
		$publique = $opt['publique'];
	else
		$publique = _DIR_RESTREINT!="";
	if (!isset($breves_exclues[$publique]) || !is_array($breves_exclues[$publique])) {
		// Si autoriser est appelee pour un autre auteur que l'auteur connecte  ou si pas d'auteur connecte , on passe $id_auteur en parametre
		if(isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']) && $qui['id_auteur']!=$GLOBALS['auteur_session']['id_auteur'])
			$breves_exclues[$publique] = AccesRestreint_liste_breves_exclues($publique,$qui['id_auteur']);
		elseif (!isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']))
			$breves_exclues[$publique] = AccesRestreint_liste_breves_exclues($publique,$qui['id_auteur']);
		else
			$breves_exclues[$publique] = AccesRestreint_liste_breves_exclues($publique);
		$breves_exclues[$publique] = array_flip($breves_exclues[$publique]);
	}
	return !isset($breves_exclues[$publique][$id]);
}
}
if(!function_exists('autoriser_site_voir')) {
function autoriser_site_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint');
	static $sites_exclus;
	if (isset($opt['publique']))
		$publique = $opt['publique'];
	else
		$publique = _DIR_RESTREINT!="";
	if (!isset($sites_exclus[$publique]) || !is_array($sites_exclus[$publique])) {
		// Si autoriser est appelee pour un autre auteur que l'auteur connecte  ou si pas d'auteur connecte , on passe $id_auteur en parametre
		if(isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']) && $qui['id_auteur']!=$GLOBALS['auteur_session']['id_auteur'])
			$sites_exclus[$publique] = AccesRestreint_liste_syndic_exclus($publique,$qui['id_auteur']);
		elseif (!isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']))
			$sites_exclus[$publique] = AccesRestreint_liste_syndic_exclus($publique,$qui['id_auteur']);
		else
			$sites_exclus[$publique] = AccesRestreint_liste_syndic_exclus($publique);
		$sites_exclus[$publique] = array_flip($sites_exclus[$publique]);
	}
	return !isset($sites_exclus[$publique][$id]);
}
}
if(!function_exists('autoriser_evenement_voir')) {
function autoriser_evenement_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint');
	static $evenements_exclus;
	if (isset($opt['publique']))
		$publique = $opt['publique'];
	else
		$publique = _DIR_RESTREINT!="";
	if (!isset($evenements_exclus[$publique]) || !is_array($evenements_exclus[$publique])) {
		// Si autoriser est appelee pour un autre auteur que l'auteur connecte  ou si pas d'auteur connecte , on passe $id_auteur en parametre
		if(isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']) && $qui['id_auteur']!=$GLOBALS['auteur_session']['id_auteur'])
			$evenements_exclus[$publique] = AccesRestreint_liste_evenements_exclus($publique,$qui['id_auteur']);
		elseif (!isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']))
			$evenements_exclus[$publique] = AccesRestreint_liste_evenements_exclus($publique,$qui['id_auteur']);
		else
			$evenements_exclus[$publique] = AccesRestreint_liste_evenements_exclus($publique);
		$evenements_exclus[$publique] = array_flip($evenements_exclus[$publique]);
	}
	return !isset($evenements_exclus[$publique][$id]);
}
}

?>