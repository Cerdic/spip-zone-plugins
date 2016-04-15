<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS['itineraires_locomotions'] = array(
	'canoe'   => _T('itineraire:locomotion_canoe'),
	'cheval'  => _T('itineraire:locomotion_cheval'),
	'pied'    => _T('itineraire:locomotion_pied'),
	'velo'    => _T('itineraire:locomotion_velo'),
	'vtt'     => _T('itineraire:locomotion_vtt'),
	'voiture' => _T('itineraire:locomotion_voiture'),
);

function itineraires_locomotions($type=null){
	$locomotions = $GLOBALS['itineraires_locomotions'];
	
	if ($type and is_string($type)){
		if (isset($locomotions[$type])){
			return $locomotions[$type];
		}
		else{
			return '';
		}
	}
	
	return $locomotions;
}

function itineraires_locomotions_durees($id_itineraire) {
	$id_itineraire = intval($id_itineraire);
	
	// On ajoute locomotions_durees
	$locomotions_durees = array();
	// Si c'est une modif on cherche l'existant
	if (
		$id_itineraire > 0
		and $locomotions = sql_allfetsel('*', 'spip_itineraires_locomotions', 'id_itineraire = '.$id_itineraire)
		and is_array($locomotions)
	) {
		$locomotions_durees = array('actives'=>array(), 'durees'=>array());
		foreach ($locomotions as $locomotion){
			$locomotions_durees['actives'][] = $locomotion['type_locomotion'];
			// Seulement s'il y a une durée
			if ($duree = $locomotion['duree']) {
				$h = floor($duree/3600);
				$m = floor(($duree-$h*3600)/60);
				if ($h) {
					$locomotions_durees['durees'][$locomotion['type_locomotion']]['heures'] = $h;
				}
				if ($m) {
					$locomotions_durees['durees'][$locomotion['type_locomotion']]['minutes'] = $m;
				}
			}
		}
	}
	
	return $locomotions_durees;
}
