<?php
/**
 * 
 * Autorisations spécifiques à Tradlang
 * 
 */

 
/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @return
 */
function tradlang_autoriser(){}

/**
 * Renvoie la liste des auteurs ou des statuts autorises pour une action donnee
 * 
 * @param string $action L'action que l'on souhaite faire
 * @param boolean $utiliser_defaut [optional]
 * @return 
 */ 
function definir_autorisations_tradlang($action,$utiliser_defaut=true){
	$aut = null;

	switch(strtolower($action)){
		case 'configurer':
			$define = (defined('_TRADLANG_AUTORISATION_CONFIGURER')) ? _TRADLANG_AUTORISATION_CONFIGURER : false;
			break;
		case 'modifier':
			$define = (defined('_TRADLANG_AUTORISATION_MODIFIER')) ? _TRADLANG_AUTORISATION_MODIFIER : '0minirezo';
			break;
		case 'voir':
			$define = (defined('_TRADLANG_AUTORISATION_VOIR')) ? _TRADLANG_AUTORISATION_VOIR : false;
			break;
	}

	if($define){
		$liste = explode(':', $define);
		if (in_array('webmestre', $liste))
			$aut['auteur'] = explode(':', _ID_WEBMESTRES);
		else if (in_array('0minirezo', $liste))
			$aut['statut'] = array('0minirezo');
		else if (in_array('1comite', $liste))
			$aut['statut'] = array('0minirezo', '1comite');
		else
			$aut['auteur'] = $liste;
		return $aut;
	}
	
	return false;
}

/**
 * Autorisation de configurer et d'administrer trad-lang
 * Permet de : 
 * - importer un nouveau module de langue
 * - créer une nouvelle version dans une langue
 * - synchroniser les fichiers de langue avec la base
 * - traduire des locutions
 * 
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return 
 */
function autoriser_tradlang_configurer_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;
	$utiliser_defaut = true;

	/**
	 * Utilisation du CFG si possible
	 */
	if(function_exists('lire_config')){
		$type = lire_config('tradlang/autorisations/configurer_type', 'par_statut');
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = tradlang_verifier_webmestre($qui);
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('tradlang/autorisations/configurer_statuts',array('0minirezo')))){
					return true;
				}
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('tradlang/autorisations/configurer_statuts',array()));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('tradlang/autorisations/configurer_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
	}

	if($type){
		$utiliser_defaut = false;
	}

	/**
	 * Si pas de CFG ou pas autorise dans le cfg => on teste les define
	 */
	$liste = definir_autorisations_tradlang('configurer',$utiliser_defaut);
	if($liste){
		if ($liste['statut'])
			$autorise = in_array($qui['statut'], $liste['statut']);
		else if ($liste['auteur'])
			$autorise = in_array($qui['id_auteur'], $liste['auteur']);
		
		return $autorise;
	}
	/**
	 * Si vraiment on n'a rien, on utilise une fonction par défaut
	 */
	else{
		return autoriser('configurer', 'lang');
	}
}

/**
 * Autorisation de modification des locution dans tradlang
 * 
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return 
 */ 
function autoriser_tradlang_modifier_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;
	$utiliser_defaut = true;

	if(intval($id) > 0){		
		$infos_chaine = sql_fetsel('*','spip_tradlang','id_tradlang='.$id);
		$lang_mere = sql_getfetsel('lang_mere','spip_tradlang_modules','module='.sql_quote($infos_chaine['module']));
		if($infos_chaine['lang'] == $lang_mere){
			return false;
		}
	}
	if(autoriser_tradlang_configurer_dist($faire, $type, $id, $qui, $opt)){
		return autoriser_tradlang_configurer_dist($faire, $type, $id, $qui, $opt);
	}
	
	/**
	 * Utilisation du CFG si possible
	 */
	if(function_exists('lire_config')){
		$type = lire_config('tradlang/autorisations/modifier_type', 'par_statut');
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = tradlang_verifier_webmestre($qui);
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('tradlang/autorisations/modifier_statuts',array()))){
					return true;
				}
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('tradlang/autorisations/modifier_statuts',array('0minirezo')));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('tradlang/autorisations/modifier_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
	}

	// Si pas de configuration CFG, on utilise des valeurs par défaut
	if($type){
		$utiliser_defaut = false;
	}

	// Si $utiliser_defaut = true, on utilisera les valeurs par défaut
	// Sinon on ajoute la possibilité de régler par define
	$liste = definir_autorisations_tradlang('modifier',$utiliser_defaut);
	if ($liste['statut'])
		$autorise = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$autorise = in_array($qui['id_auteur'], $liste['auteur']);
	return $autorise;
}

/**
 * Autorisation de voir l'interface de tradlang
 * 
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return 
 */
function autoriser_tradlang_voir_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;
	$utiliser_defaut = true;

	if(autoriser_tradlang_modifier_dist($faire, $type, $id, $qui, $opt)){
		return autoriser_tradlang_modifier_dist($faire, $type, $id, $qui, $opt);
	}
	
	/**
	 * Utilisation du CFG si possible
	 */
	if(function_exists('lire_config')){
		$type = lire_config('tradlang/autorisations/voir_type', 'par_statut');
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = tickets_verifier_webmestre($qui);
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('tradlang/autorisations/voir_statuts',array()))){
					return true;
				}
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('tradlang/autorisations/voir_statuts',array('0minirezo','1comite')));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('tradlang/autorisations/voir_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
	}

	if($type){
		$utiliser_defaut = false;
	}
	
	/**
	 * Si pas de CFG ou pas autorise dans le cfg => on teste les define
	 */
	$liste = definir_autorisations_tradlang('voir',$utiliser_defaut);
	if($liste){
		if ($liste['statut'])
			$autorise = in_array($qui['statut'], $liste['statut']);
		else if ($liste['auteur'])
			$autorise = in_array($qui['id_auteur'], $liste['auteur']);
	}
	/**
	 * Si vraiment on n'a rien, on utilise une fonction par défaut
	 */
	else{
		return autoriser('voir','lang');
	}
}

function tradlang_verifier_webmestre($qui){
	$webmestre =  false;
	$webmestre = in_array($qui['id_auteur'],explode(':', _ID_WEBMESTRES));
	if(!$webmestre && ($qui['webmestre']=='oui')){
		$webmestre =  true;
	}
	return $webmestres;
}
?>
