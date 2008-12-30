<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// pouvoir utiliser la class ChampExtra
include_spip('inc/cextras');


// Calcule des elements pour le contexte de compilation
// des squelettes de champs extras
// en fonction des parametres donnes dans la classe ChampExtra
function cextras_creer_contexte($c, $contexte_flux, $quete_valeur = false) {
	$contexte = array();
	$contexte['champ_extra'] = $c->champ;
	$contexte['label_extra'] = _T($c->label);
	$contexte['precisions_extra'] = _T($c->precisions);
	$contexte['valeur_extra'] = $contexte_flux[$c->champ];
	
	// ajouter 'erreur_extra' dans le contexte s'il y a une erreur sur le champ
	if (isset($contexte_flux['erreurs']) 
	and is_array($contexte_flux['erreurs'])
	and array_key_exists($c->champ, $contexte_flux['erreurs'])) {
		$contexte['erreur_extra'] = $contexte_flux['erreurs'][$c->champ];
	}
	
	return array_merge($contexte_flux, $contexte);
}


// recuperer en bdd les valeurs des champs extras
// en une seule requete...
function cextra_quete_valeurs_extras($extras, $contexte){
	
	// nom de la table et de la cle primaire
	$table = table_objet_sql($extras[0]->table);
	$_id = id_table_objet($extras[0]->table);

	// recuperer l'id de la cle primaire
	// attention, l'ordre est important car les pipelines afficher et editer
	// ne transmettent pas les memes arguments
	if (isset($contexte[$_id])) {
		$id = $contexte[$_id];		
	} elseif (isset($contexte['id_objet'])) {
		$id = $contexte['id_objet'];
	} elseif (isset($contexte['id']) and intval($contexte['id'])) { // peut valoir 'new'
		$id = $contexte['id'];
	}
	// liste des champs a recuperer
	$champs = array();
	foreach ($extras as $e) {
		$champs[] = $e->champ;
	}
	if (is_array($res = sql_fetsel($champs, $table, $_id . '=' . sql_quote($id)))) {
		return $res;
	}
	return array();
}

// recuperer tous les extras qui verifient le critere demande :
// l'objet sur lequel s'applique l'extra est comparee a $nom
function cextras_get_extras_match($nom, $func='objet_type', $prefixe='') {
	$extras = array();
	if ($champs = pipeline('declarer_champs_extras', array())) {
		foreach ($champs as $c) {
			if ($nom == ($prefixe . $func($c->table))
			and $c->champ and $c->sql) {
				$extras[] = $c;
			}
		}
	}
	return $extras;
}




// ---------- pipelines -----------
	

// ajouter les champs sur les formulaires CVT editer_xx
function cextras_editer_contenu_objet($flux){
	// recuperer les champs crees par les plugins
	if ($extras = cextras_get_extras_match($flux['args']['type'])) {
		foreach ($extras as $c) {

			// le contexte possede deja l'entree SQL, 
			// calcule par le pipeline formulaire_charger.
			$contexte = cextras_creer_contexte($c, $flux['args']['contexte']);
			$extras[$c->champ] = $contexte[$c->champ];
			
			// calculer le bon squelette et l'ajouter
			if (!find_in_path(
			($f = 'extra-saisies/'.$c->type).'.html')) {
				// si on ne sait pas, on se base sur le contenu
				// pour choisir ligne ou bloc
				$f = strstr($contexte[$c->champ], "\n")
					? 'extra-saisies/bloc'
					: 'extra-saisies/ligne';
			}
			$extra = recuperer_fond($f, $contexte);
			$flux['data'] = preg_replace('%(<!--extra-->)%is', $extra."\n".'$1', $flux['data']);			
		}
	}

	return $flux;
}


// ajouter les champs extras soumis par les formulaire CVT editer_xx
function cextras_pre_edition($flux){
	
	// recuperer les champs crees par les plugins
	if ($extras = cextras_get_extras_match($flux['args']['table'], 'table_objet_sql')) {
		foreach ($extras as $c) {
			if (($extra = _request($c->champ)) !== null) {
				$flux['data'][$c->champ] = corriger_caracteres($extra);
			}			
		}
	}

	return $flux;
}


// ajouter le champ extra sur la visualisation de l'objet
function cextras_afficher_contenu_objet($flux){

	// recuperer les champs crees par les plugins
	if ($extras = cextras_get_extras_match($flux['args']['type'])) {

		$contexte = cextra_quete_valeurs_extras($extras, $flux['args']['contexte']);
		$contexte = array_merge($flux['args']['contexte'], $contexte);

		foreach($extras as $c) {
				$contexte = cextras_creer_contexte($c, $contexte);
				
				// calculer le bon squelette et l'ajouter
				if (!find_in_path(
				($f = 'extra-vues/'.$c->type).'.html')) {
					// si on ne sait pas, on se base sur le contenu
					// pour choisir ligne ou bloc
					$f = strstr($contexte[$c->champ], "\n")
						? 'extra-vues/bloc'
						: 'extra-vues/ligne';
				}
				$extra = recuperer_fond($f, $contexte);
				$flux['data'] .= "\n".$extra;			
		}
	}
	return $flux;
}

?>
