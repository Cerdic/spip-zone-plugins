<?php 

function formulaires_gerer_liens_traiter_dist() {
	$champs = _request('chp');
	$valeurs = _request('valeur');
	$groupes = _request('groupe');
	
	
	$liens = array();
	foreach($champs as $k=>$v) {
		$liens[] = array($champs[$k], $valeurs[$k], $groupes[$k]);
	}
	
	$anciens_liens = unserialize(lire_meta('groupes_liens'));
	effacer_meta('groupes_liens');
	ecrire_meta('groupes_liens', serialize($liens));
		
	ecrire_metas();
	
	$liens_a_suppr = array_diff2($anciens_liens, $liens);
	$liens_a_ajout = array_diff2($liens, $anciens_liens);
	
	supprimer_liens($liens_a_suppr);
	ajouter_liens($liens_a_ajout);
}

function array_diff2($array1, $array2) {
	$retour = array();
	foreach($array1 as $k1=>$a) {
		foreach($a as $k2=>$v) {
			if($v != $array[$k1][$v1]) {
				$retour[$k1] = $a;
			}
		}
	}
	return $retour;
}

function formulaires_gerer_liens_verifier_dist() {
	$valeurs = _request('valeur');
	foreach($valeurs as $i=>$v) {
		if($v == '') {
			return array('message_erreur' => 'Le champ valeur ne doit pas etre vide');	
		}
	}
	return array();
}


function supprimer_liens($liens) {
	spip_log('supprimer liens : '.serialize($liens) , 'groupes');
	if((!is_array($liens)) || count($liens)==0)
		return '';
	include_spip('exec/groupe_auteur_supprimer');
	foreach($liens as $lien) {
		$result = sql_select('*', 'spip_auteurs', array($lien[0].'=\''.$lien[1].'\''));
		while($r = sql_fetch($result)) {
			supprimer_groupe_func($lien[2], $r['id_auteur']);
			if(defined('_DIR_PLUGIN_ACCESRESTREINT')) {
				supprimer_groupe_zone_func($lien[2], $r['id_auteur']);
			}
		}
	}
}

function ajouter_liens($liens) {
	spip_log('ajouter liens : '.serialize($liens) , 'groupes');
	if((!is_array($liens)) || count($liens)==0)
		return '';
	include_spip('formulaires/auteur_ajouter');
	foreach($liens as $lien) {
		$result = sql_select('*', 'spip_auteurs', array($lien[0].'=\''.$lien[1].'\''));
		while($r = sql_fetch($result)) {
			ajouter_auteur_groupe_func($lien[2], $r['id_auteur']);
			if(defined('_DIR_PLUGIN_ACCESRESTREINT')) {
				ajouter_auteur_zone_func($lien[2], $r['id_auteur']);
			}
		}
	}
}
?>