<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// pouvoir utiliser la class ChampExtra
include_spip('inc/cextras');


// Creer les item d'un select a partir des enum
function cextras_enum($enum, $val='', $type='valeur', $name='') {
	$enums = array();
	// 2 possibilites : enum deja un tableau (vient certainement d'un plugin),
	// sinon texte a decouper (vient certainement de interfaces pour champs extra).
	if (is_array($enum)) {
		$enums = $enum;
	} else {
		foreach ($vals = explode("\n", $enum) as $x) {
			list($cle, $desc) = explode(',', trim($x), 2);
			$enums[$cle] = _T($desc);
		}
	}

	$val_t = explode(',', $val);

	foreach($enums as $cle => $desc) {
		switch($type) {
			case 'valeur':
				$enums[$cle] =
					($cle == $val
					OR in_array($cle, $val_t))
						? sinon(sinon($desc,$cle),_T('cextras:cextra_par_defaut'))
						: '';
				break;
			case 'option':
				$enums[$cle] = '<option value="'.entites_html($cle).'"'
					. ($cle == $val
						? " selected='selected'"
						: ''
					) .'> '.sinon(sinon($desc,$cle),_T('cextras:cextra_par_defaut'))
					.'</option>'
					."\n";
				break;
			case 'radio':
				$enums[$cle] = "<div class='choix'><input type='radio' name='$name' id='${name}_$cle' value=\"".entites_html($cle).'"'
					. ($cle == $val
						? " checked='checked'"
						: ''
					) ."><label for='${name}_$cle'>"
					. sinon(sinon($desc,$cle),_T('cextras:cextra_par_defaut'))
					.'</label></div>'
					."\n";
				break;
			case 'cases':
				$enums[$cle] = "<div class='choix'><input type='checkbox' name='${name}[]' id='${name}_$cle' value=\"".entites_html($cle).'"'
					. (in_array($cle, $val_t)
						? " checked='checked'"
						: ''
					) ." /><label for='${name}_$cle'>"
					. sinon(sinon($desc,$cle),_T('cextras:cextra_par_defaut'))
					.'</label></div>'
					."\n";
				break;
		}
	}

	return trim(join("\n", $enums));
}


// Calcule des elements pour le contexte de compilation
// des squelettes de champs extras
// en fonction des parametres donnes dans la classe ChampExtra
function cextras_creer_contexte($c, $contexte_flux) {
	$contexte = array();
	$contexte['champ_extra'] = $c->champ;
	$contexte['label_extra'] = _T($c->label);
	$contexte['precisions_extra'] = _T($c->precisions);
	if (isset($c->saisie_parametres['explication']) and $c->saisie_parametres['explication'])
		$contexte['precisions_extra'] = _T($c->saisie_parametres['explication']);
	$contexte['obligatoire_extra'] = $c->obligatoire ? 'obligatoire' : '';
	$contexte['verifier_extra'] = $c->verifier;
	$contexte['verifier_options_extra'] = $c->verifier_options;
	$contexte['valeur_extra'] = $contexte_flux[$c->champ];
	$contexte['enum_extra'] = $c->enum;
	// ajouter 'erreur_extra' dans le contexte s'il y a une erreur sur le champ
	if (isset($contexte_flux['erreurs'])
	and is_array($contexte_flux['erreurs'])
	and array_key_exists($c->champ, $contexte_flux['erreurs'])) {
		$contexte['erreur_extra'] = $contexte_flux['erreurs'][$c->champ];
	}

	return array_merge($contexte_flux, $contexte);
}


// recuperation d'une saisie interne
function ce_calculer_saisie_interne($c, $contexte) {
	// le contexte possede deja l'entree SQL,
	// calcule par le pipeline formulaire_charger.
	$contexte = cextras_creer_contexte($c, $contexte);

	// calculer le bon squelette et l'ajouter
	if (!find_in_path(
	($f = 'extra-saisies/'.$c->type).'.html')) {
		// si on ne sait pas, on se base sur le contenu
		// pour choisir ligne ou bloc
		$f = strstr($contexte[$c->champ], "\n")
			? 'extra-saisies/bloc'
			: 'extra-saisies/ligne';
	}
	return array($f, $contexte);
}


// en utilisant le plugin "saisies"
function ce_calculer_saisie_externe($c, $contexte) {

	$contexte['nom'] = $c->champ;
	$contexte['type_saisie'] = $c->type;
	$contexte['label'] = _T($c->label);
	if (isset($contexte[$c->champ]) and $contexte[$c->champ]) {
		$contexte['valeur'] = $contexte[$c->champ];
	}

	$params = $c->saisie_parametres;

	// remapper les precisions
	if ($c->precisions) {
		$params['explication'] = $c->precisions;
	}

	// traductions a faire
	$contexte['explication'] = _T($params['explication']);
	$contexte['attention'] = _T($params['attention']);

	unset (	$params['explication'],
			$params['attention']);

	// tout inserer le reste des champs
	$contexte = array_merge($contexte, $params);

	return array('saisies/_base', $contexte);
}




// recuperer en bdd les valeurs des champs extras
// en une seule requete...

function cextra_quete_valeurs_extras($extras, $type, $id){

	// nom de la table et de la cle primaire
	$table = table_objet_sql($type);
	$_id = id_table_objet($type);

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
function cextras_get_extras_match($nom) {
	$extras = array();
	if ($champs = pipeline('declarer_champs_extras', array())) {
		$nom = objet_type(table_objet($nom));
		foreach ($champs as $c) {
			// attention aux cas compliques site->syndic !
			if ($nom == objet_type(table_objet($c->table)) and $c->champ and $c->sql) {
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
		// les saisies a ajouter seront mises dedans.
		$inserer_saisie = '';

		foreach ($extras as $c) {

			// on affiche seulement les champs dont la saisie est autorisee
			$type = objet_type($c->table).'_'.$c->champ;
			include_spip('inc/autoriser');
			if (autoriser('modifierextra', $type, $flux['args']['id'], '', array(
				'type' => $flux['args']['type'],
				'id_objet' => $flux['args']['id'],
				'contexte' => $flux['args']['contexte'])))
			{

				if ($c->saisie_externe) {
					list($f, $contexte) = ce_calculer_saisie_externe($c, $flux['args']['contexte']);
				} else {
					list($f, $contexte) = ce_calculer_saisie_interne($c, $flux['args']['contexte']);
				}
				$saisie = recuperer_fond($f, $contexte);

				// Signaler a cextras_pre_edition que le champ est edite
				// (cas des checkbox multiples quand on renvoie vide
				//  qui n'envoient rien de rien, meme pas un array vide)
				$saisie .= '<input type="hidden" name="cextra_'.$c->champ.'" value="1" />';

				// ajouter la saisie.
				$inserer_saisie .= $saisie;
			}
		}

		// inserer les differentes saisies entre <ul>
		if ($inserer_saisie) {
			$flux['data'] = preg_replace('%(<!--extra-->)%is', '<ul>'.$inserer_saisie.'</ul>'."\n".'$1', $flux['data']);
		}
	}

	return $flux;
}


// ajouter les champs extras soumis par les formulaire CVT editer_xx
function cextras_pre_edition($flux){

	// recuperer les champs crees par les plugins
	if ($extras = cextras_get_extras_match($flux['args']['table'])) {
		foreach ($extras as $c) {
			if (_request('cextra_'.$c->champ)) {
				$extra = _request($c->champ);
				if (is_array($extra))
					$extra = join(',',$extra);
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

		$contexte = cextra_quete_valeurs_extras($extras, $flux['args']['type'], $flux['args']['id_objet']);
		$contexte = array_merge($flux['args']['contexte'], $contexte);

		foreach($extras as $c) {

			// on affiche seulement les champs dont la vue est autorisee
			$type = objet_type($c->table).'_'.$c->champ;
			include_spip('inc/autoriser');
			if (autoriser('voirextra', $type, $flux['args']['id_objet'], '', array(
				'type' => $flux['args']['type'],
				'id_objet' => $flux['args']['id_objet'],
				'contexte' => $contexte)))
			{

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
	}
	return $flux;
}

// verification de la validite des champs extras
function cextras_formulaire_verifier($flux){
	// recuperer les champs crees par les plugins
	$form = $flux['args']['form'];
	// formulaire d'edition ?
	if (strpos($form, 'editer_') !== false) {
		$type = str_replace('editer_','',$form);
		// des champs extras correspondent ?
		if ($extras = cextras_get_extras_match($type)) {

			include_spip('inc/autoriser');
			$verifier = charger_fonction('verifier','inc',true);

			foreach ($extras as $c) {
				// si on est autorise a modifier le champ
				// et que le champ est obligatoire
				// alors on renvoie une erreur.
				// Mais : ne pas renvoyer d'erreur si le champ est
				// obligatoire, mais qu'il n'est pas visible dans le formulaire
				// (si affiche uniquement pour la rubrique XX par exemple).
				// On teste seulement les champs dont la modification est autorisee
				$type = objet_type($c->table).'_'.$c->champ;
				$id_objet = $flux['args']['args'][0]; // ? vraiment toujours ?

				// l'autorisation n'a pas de contexte a transmettre
				// comme dans l'autre appel (cextras_afficher_contenu_objet())
				// du coup, on risque de se retrouver parfois avec des
				// resultats differents... Il faudra surveiller.

				if (autoriser('modifierextra', $type, $id_objet, '', array(
					'type' => $c->table,
					'id_objet' => $id_objet)))
				{
					if ($c->obligatoire AND !_request($c->champ)) {
						$flux['data'][$c->champ] = _T('info_obligatoire');
					}
					else if($c->verifier && $verifier){
						$erreur[$c->champ] = $verifier(_request($c->champ),$c->verifier,$c->verifier_options);
						if($erreur[$c->champ]){
							$flux['data'][$c->champ] = $erreur[$c->champ];
						}
					}
				}
			}
		}
	}
	return $flux;
}


// prendre en compte les champs extras 2 dans les recherches
// pour les champs qui le demandent
function cextras_rechercher_liste_des_champs($tables){
	if ($champs = pipeline('declarer_champs_extras', array())) {
		$t = array();
		// trouver les tables/champs a rechercher
		foreach ($champs as $c) {
			if ($c->rechercher) {
				// priorite 2 par defaut, sinon sa valeur.
				// Plus le chiffre est grand, plus les points de recherche
				// attribues pour ce champ seront eleves
				if ($c->rechercher === true
				OR  $c->rechercher === 'oui'
				OR  $c->rechercher === 'on') {
					$priorite = 2;
				} else {
					$priorite = intval($c->rechercher);
				}
				if ($priorite) {
					$t[objet_type($c->table)][$c->champ] = $priorite;
				}
			}
		}
		// les ajouter
		if ($t) {
			$tables = array_merge_recursive($tables, $t);
		}
	}
	return $tables;
}


?>
