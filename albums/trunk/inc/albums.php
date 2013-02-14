<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Associer/dissocier un album a un objet editorial
 * Utilise par les boutons d actions
 *
 * @param string $action
 * 		associer ou dissocier
 * @param int $id_album
 * @param string $objet
 * @param int $id_objet
 */
function editer_liens_album($action,$id_album,$objet,$id_objet){

	if (!$action OR (!in_array($action,array('associer','dissocier'))) OR !$id_album OR !$objet OR !$id_objet)
		return;

	include_spip('inc/autoriser');
	if (intval($id_objet) AND autoriser('associeralbum', $objet, $id_objet)){
		include_spip('action/editer_liens');
		switch ($action) {
			case 'associer' :
				objet_associer(array('album'=>$id_album), array($objet=>$id_objet));
				break;
			case 'dissocier' :
				objet_dissocier(array('album'=>$id_album), array($objet=>$id_objet));
				break;
		}
	}
}


/**
 * Lister les modeles albums
 * modele principal et $modele
 *
 * @param bool $variante
 *		si true, renvoie une liste des variantes uniquement
 * @staticvar array $liste_modeles
 * @return array
 */
function lister_modeles_albums($original=false){
	static $liste_modeles = false;

	if ($liste_modeles === false) {
		$liste_modeles = array();
		$match = ($original) ? "album[.]yaml$" : "[album]*[.]yaml$";
		$liste = find_all_in_path(_DIR_YAML_MODELES_ALBUM, $match);

		if (count($liste)){
			include_spip('inc/yaml');
			foreach($liste as $modele => $chemin)
				$liste_modeles[$modele] = yaml_charger_inclusions(yaml_decode_file($chemin));
		}
	}

	return $liste_modeles;
}

/**
 * Charger les informations d un modele
 *
 * @staticvar array $infos_$modele
 * @return array
 */
function charger_infos_modele_album($modele){
	static $infos_modele = array();

	if (!isset($infos_modele[$modele])) {
		if (substr($modele,-5)!='.yaml')
			$modele .= '.yaml';
		if ($chemin = find_in_path($modele, _DIR_YAML_MODELES_ALBUM)) {
			include_spip('inc/yaml');
			$infos_modele[$modele] = yaml_charger_inclusions(yaml_decode_file($chemin));
		}
	}

	return $infos_modele[$modele];
}

?>
