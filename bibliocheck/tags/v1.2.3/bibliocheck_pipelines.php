<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function bibliocheck_affiche_milieu($flux) {
	$exec = $flux['args']['exec'];
	if ($exec=='configurer_zotspip' && autoriser('webmestre'))
		$flux['data'] .= recuperer_fond('prive/inclure/configurer_bibliocheck');
	// si on est sur la page ticket
	if ($exec=='ticket'){
		$texte = recuperer_fond(
				'prive/inclure/ticket_complement_bibliocheck',
				array(
					'id_ticket'=>$flux['args']['id_ticket']
				)
		);
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}
	return $flux;
}

function bibliocheck_affiche_droite($flux) {
	$exec = $flux['args']['exec'];
	if ($exec=='ticket')
		$flux['data'] .= recuperer_fond('prive/inclure/maj_zotspip');
	return $flux;
}

/* pour que le pipeline ne rale pas ! */
function bibliocheck_autoriser(){}

/**
 * Autorisation de vérifier les références biblio
  * 
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */
function autoriser_biblio_verifier_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;

	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	$type = lire_config('bibliocheck/autorisation_type');
	if($type){
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = ($qui['webmestre'] == 'oui');
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('bibliocheck/autorisation_statuts',array('0minirezo')))){
					return true;
				}
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('bibliocheck/autorisation_statuts',array()));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('bibliocheck/autorisation_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
		$utiliser_defaut = false;
	}

	// Si pas configuré ou pas autorisé dans la conf => webmaster
	$autorise = ($qui['webmestre'] == 'oui');

	return $autorise;
}

