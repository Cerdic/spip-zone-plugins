<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// pouvoir utiliser la class ChampExtra
include_spip('inc/champsextras');

// Calcule des elements pour le contexte de compilation
// des squelettes de champs extras
// en fonction des parametres donnes dans la classe ChampExtra
function champsextras_creer_contexte($c, $contexte_flux) {
	$contexte = array();
	$contexte['champextra'] = $c->champ;
	$contexte['label_' . $c->champ] = $c->label;
	
	// retrouver la valeur du champ demande
	$table = table_objet_sql($c->table);
	$_id = id_table_objet($c->table);

	// attention, l'ordre est important car les pipelines afficher et editer
	// ne transmettent pas les memes arguments
	if (isset($contexte_flux[$_id])) {
		$id = $contexte_flux[$_id];		
	} elseif (isset($contexte_flux['id_objet'])) {
		$id = $contexte_flux['id_objet'];
	} elseif (isset($contexte_flux['id']) and intval($contexte_flux['id'])) { // peut valoir 'new'
		$id = $contexte_flux['id'];
	}

	$contexte[$c->champ] = sql_getfetsel($c->champ, $table, $_id . '=' . sql_quote($id));
	return array_merge($contexte_flux, $contexte);
}
	
// ajouter les champs sur les formulaires CVT editer_xx
function champsextras_editer_contenu_objet($flux){
	
	// recuperer les champs crees par les plugins
	if ($champs = pipeline('declarer_champs_extras', array())) {
		foreach ($champs as $c) {
			// si le champ est du meme type que le flux
			if ($flux['args']['type']==objet_type($c->table) and $c->champ and $c->sql) {

				$contexte = champsextras_creer_contexte($c, $flux['args']['contexte']);

				// calculer le bon squelette et l'ajouter
				$extra = recuperer_fond('formulaires/inc-champ-formulaire-'.$c->type, $contexte);	
				$flux['data'] = preg_replace('%(<!--extra-->)%is', $extra."\n".'$1', $flux['data']);
			}
		}
	}
	
	return $flux;
}


// ajouter les champs extras soumis par les formulaire CVT editer_xx
function champsextras_pre_edition($flux){
	
	// recuperer les champs crees par les plugins
	if ($champs = pipeline('declarer_champs_extras', array())) {
		foreach ($champs as $c) {
			// si le champ est du meme type que le flux
			if ($flux['args']['table']==table_objet_sql($c->table) and $c->champ and $c->sql) {
				if ($extra = _request($c->champ)) {
					$flux['data'][$c->champ] = corriger_caracteres($extra);
				}				
			}
		}
	}
	
	return $flux;
}


// ajouter le champ extra sur la visualisation de l'objet
function champsextras_afficher_contenu_objet($flux){
	// recuperer les champs crees par les plugins
	if ($champs = pipeline('declarer_champs_extras', array())) {
		foreach ($champs as $c) {
			// si le champ est du meme type que le flux
			if ($flux['args']['type']==objet_type($c->table) and $c->champ and $c->sql) {
	
				$contexte = champsextras_creer_contexte($c, $flux['args']['contexte']);

				// calculer le bon squelette et l'ajouter
				$extra = recuperer_fond('prive/contenu/inc-champ-extra', $contexte);	
				$flux['data'] .= "\n".$extra;
			}
		}
	}
	return $flux;
}

?>
