<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// pouvoir utiliser la class ChampExtra
include_spip('inc/cextras');


// recuperer un tableau des indications fournies pour des selections (enum, radio...)
function cextras_data_array($data) {
	$datas = array();
	// 2 possibilites : enum deja un tableau (vient certainement d'un plugin),
	// sinon texte a decouper (vient certainement de interfaces pour champs extra).
	if (is_array($data)) {
		$datas = $data;
	} else {
		foreach ($vals = explode("\n", $data) as $x) {
			list($cle, $desc) = explode(',', trim($x), 2);
			$datas[$cle] = _T($desc);
		}
	}
	return $datas;	
}


// Calcule des elements pour le contexte de compilation
// des squelettes de champs extras
// en fonction des parametres donnes dans la classe ChampExtra
function cextras_creer_contexte($c, $contexte_flux, $prefixe='') {
	$contexte = array();
	$nom_champ = $prefixe . $c->champ;
	$contexte['champ_extra'] = $nom_champ;
	$contexte['label_extra'] = _T($c->label);
	$contexte['precisions_extra'] = _T($c->precisions);
	if (isset($c->saisie_parametres['explication']) and $c->saisie_parametres['explication'])
		$contexte['precisions_extra'] = _T($c->saisie_parametres['explication']);
	$contexte['obligatoire_extra'] = $c->obligatoire ? 'obligatoire' : '';
	$contexte['verifier_extra'] = $c->verifier;
	$contexte['verifier_options_extra'] = $c->verifier_options;
	$contexte['valeur_extra'] = $contexte_flux[$nom_champ];
	$contexte['enum_extra'] = $c->enum;
	$contexte['class_extra'] = $c->saisie_parametres['class']; // class CSS sur les champs (input, textarea, ...)
	// ajouter 'erreur_extra' dans le contexte s'il y a une erreur sur le champ
	if (isset($contexte_flux['erreurs'])
	and is_array($contexte_flux['erreurs'])
	and array_key_exists($nom_champ, $contexte_flux['erreurs'])) {
		$contexte['erreur_extra'] = $contexte_flux['erreurs'][$nom_champ];
	}

	return array_merge($contexte_flux, $contexte);
}



// en utilisant le plugin "saisies"
function ce_calculer_saisie($c, $contexte, $prefixe='') {
	
	// pas besoin de la config de SPIP ?
	unset($contexte['config']);


	$nom_champ = $prefixe . $c->champ;
	$contexte['nom'] = $nom_champ;
	$contexte['type_saisie'] = $c->saisie;
	$contexte['label'] = _T($c->label);
	if (isset($contexte[$nom_champ]) and $contexte[$nom_champ]) {
		$contexte['valeur'] = $contexte[$nom_champ];
	}

	if ($c->saisie_parametres['datas']) {
		$contexte['datas'] = cextras_data_array($c->saisie_parametres['datas']);
	}

	$params = $c->saisie_parametres;

	// traductions a faire
	$contexte['explication'] = _T($params['explication']);
	$contexte['attention'] = _T($params['attention']);

	unset (	$params['explication'],
			$params['attention']);

	// tout inserer le reste des champs
	$contexte = array_merge($contexte, $params);

	
	// lorsqu'on a 'datas', c'est qu'on est dans une liste de choix.
	// Champs Extra les stocke separes par des virgule.
	if ($contexte['datas']) {
		// n'appliquer que si la saisie en a besoin !
		$desc_saisies = saisies_lister_par_nom( saisies_charger_infos($c->saisie) );
		if ($desc_saisies['datas']) {
			$contexte['valeur'] = explode(',', $contexte['valeur']);
		}
	}

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
// l'objet sur lequel s'applique l'extra est comparee a $type
function cextras_get_extras_match($type) {
	static $champs = false;
	if ($champs === false) {
		$champs = pipeline('declarer_champs_extras', array());
	}
	
	$extras = array();
	if ($champs) {
		$type = objet_type(table_objet($type));
		foreach ($champs as $c) {
			// attention aux cas compliques site->syndic !
			if ($type == $c->_type and $c->champ and $c->sql) {
				$extras[] = $c;
			}
		}
	}
	
	return $extras;
}


/**
 * Retourne la description (classe ChampExtra) d'un champ extra d'un objet donné.
 *
 * @param $type : type d'objet (article)
 * @param $champ : nom du champ (puissance)
 * 
 * @return ChampExtra|false
**/
function cextras_get_extra($type, $champ) {
	$extras = cextras_get_extras_match($type);
	foreach ($extras as $c) {
		if ($c->champ == $champ) {
			return $c;
		}
	}
	return false;
}


// ---------- pipelines -----------


// ajouter les champs sur les formulaires CVT editer_xx
function cextras_editer_contenu_objet($flux){
	// recuperer les champs crees par les plugins
	if ($extras = cextras_get_extras_match($flux['args']['type'])) {
		// les saisies a ajouter seront mises dedans.
		$inserer_saisie = '';

		// Il peut arriver qu'un prefixe soit appliqué sur les noms de champs de formulaire
		// (mais pas en base) ceci pour permettre d'inserer les champs de formulaire d'un objet dans
		// le formulaire d'un autre objet, en prefixant tous ses champs, par exemple
		// pour spip_auteurs_elargis et spip_auteurs. Dans ce cas il ne pourra pas y avoir
		// conflits si spip_auteurs a un champ extra 'nom' et spip_auteurs_elargis aussi.
		// La contrainte est que le formulaire inseré doit appeler le pipeline 'editer_contenu_objet'
		// en lui indiquant quel est le prefixe utilisé d'une part, et d'autre part
		// il faut qu'il s'occupe lui même d'ajouter les données via
		// le pipeline formulaire_charger de spip_auteurs (pour cet exemple) avec les bons prefixe.
		if (isset($flux['args']['prefixe_champs_extras']) and $prefixe = $flux['args']['prefixe_champs_extras']) {
			$inserer_saisie .= "<input type='hidden' name='prefixe_champs_extras_" . $flux['args']['type'] . "' value='$prefixe' />\n";
		} else {
			$prefixe = '';
		}

		
		foreach ($extras as $c) {

			// on affiche seulement les champs dont la saisie est autorisee
			$type = $c->_type . '_' . $c->champ;
			include_spip('inc/autoriser');
			if (autoriser('modifierextra', $type, $flux['args']['id'], '', array(
				'type' => $flux['args']['type'],
				'id_objet' => $flux['args']['id'],
				'contexte' => $flux['args']['contexte'])))
			{

				list($f, $contexte) = ce_calculer_saisie($c, $flux['args']['contexte'], $prefixe);

				// Si un prefixe de champ est demande par le pipeline
				// par exemple pour afficher et completer un objet différent dans
				// le formulaire d'un premier objet (ex: spip_auteurs_etendus et spip_auteurs)
				// l'indiquer !
				$saisie = recuperer_fond($f, $contexte);

				// Signaler a cextras_pre_edition que le champ est edite
				// (cas des checkbox multiples quand on renvoie vide
				//  qui n'envoient rien de rien, meme pas un array vide)
				$saisie .= '<input type="hidden" name="cextra_' . $prefixe . $c->champ.'" value="1" />';

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
		// recherchons un eventuel prefixe utilise pour poster les champs
		$type = objet_type(table_objet($flux['args']['table']));
		$prefixe = _request('prefixe_champs_extras_' . $type);
		if (!$prefixe) {
			$prefixe = '';
		}
		foreach ($extras as $c) {
			if (_request('cextra_' . $prefixe . $c->champ)) {
				$extra = _request($prefixe . $c->champ);
				if (is_array($extra)) {
					$extra = join(',',$extra);
				}
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
		foreach ($extras as $c) {

			// on affiche seulement les champs dont la vue est autorisee
			$type = $c->_type . '_' . $c->champ;
			include_spip('inc/autoriser');
			if (autoriser('voirextra', $type, $flux['args']['id_objet'], '', array(
				'type' => $flux['args']['type'],
				'id_objet' => $flux['args']['id_objet'],
				'contexte' => $contexte)))
			{

				$contexte = cextras_creer_contexte($c, $contexte);

				// calculer le bon squelette et l'ajouter
				if (!find_in_path(($f = 'saisies-vues/' . $c->saisie) . '.html')) {
					$flux['data'] .= recuperer_fond('prive/squelettes/saisie_vue_absente', array(
						'saisie' => $c->saisie,
						'valeur' => $contexte[$c->champ],
					));
					extras_log("Vue de saisie non trouvee pour $c->saisie", true);
				} else {
					$contexte['valeur'] = $contexte[$c->champ];
					// ajouter les listes d'éléments possibles
					if (isset($c->saisie_parametres['datas']) and $c->saisie_parametres['datas']) {
						$contexte['datas'] = cextras_data_array($c->saisie_parametres['datas']);
					} 

					// lorsqu'on a 'datas', c'est qu'on est dans une liste de choix.
					// Champs Extra les stocke separes par des virgule.
					if ($contexte['datas']) {
						// n'appliquer que si la saisie en a besoin !
						$desc_saisies = saisies_lister_par_nom( saisies_charger_infos($c->saisie) );
						if ($desc_saisies['datas']) {
							$contexte['valeur'] = explode(',', $contexte['valeur']);
						}	
					}
					
					$extra = recuperer_fond($f, $contexte);
					$extra = '<div class="champ ' . $c->champ.'">
						<div class="label">' . _T($c->label) . '</div>' . $extra . '</div>';
					
					$flux['data'] .= "\n".$extra;
				} 
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
	if (strncmp($form, 'editer_', 7) === 0) {
		$type = substr($form, 7);
		
		// des champs extras correspondent ?
		if ($extras = cextras_get_extras_match($type)) {

			// Il peut arriver qu'un prefixe soit appliqué sur les noms de champs de formulaire
			// La contrainte est que le formulaire inseré doit appeler le pipeline 'formulaire_verifier'
			// avec le bon type d'objet (en indiquant le prefixe) et concaténer ainsi les résultats
			if (isset($flux['args']['prefixe_champs_extras'])
			and $prefixe = $flux['args']['prefixe_champs_extras']) {
			} else {
				$prefixe = '';
			}
					
			include_spip('inc/autoriser');

			// si le plugin "verifier" est actif, on tentera dans
			// la verification de lancer la fonction de verification
			// demandee par le champ, si definie dans sa description
			// 'verifier' (et 'verifier_options')
			$verifier = charger_fonction('verifier', 'inc', true);
			
			foreach ($extras as $c) {
				// si on est autorise a modifier le champ
				// et que le champ est obligatoire
				// alors on renvoie une erreur.
				// Mais : ne pas renvoyer d'erreur si le champ est
				// obligatoire, mais qu'il n'est pas visible dans le formulaire
				// (si affiche uniquement pour la rubrique XX par exemple).
				// On teste seulement les champs dont la modification est autorisee
				$type = $c->_type . '_' . $c->champ;
				$id_objet = $flux['args']['args'][0]; // ? vraiment toujours ?

				// l'autorisation n'a pas de contexte a transmettre
				// comme dans l'autre appel (cextras_afficher_contenu_objet())
				// du coup, on risque de se retrouver parfois avec des
				// resultats differents... Il faudra surveiller.
				if (autoriser('modifierextra', $type, $id_objet, '', array(
					'type' => $c->_type,
					'id_objet' => $id_objet)))
				{	
					if ($c->obligatoire AND !_request($prefixe . $c->champ)) {
						$flux['data'][$prefixe . $c->champ] = _T('info_obligatoire');
					} elseif ($c->verifier AND $verifier) {
						if ($erreur = $verifier(_request($prefixe . $c->champ), $c->verifier, $c->verifier_options)) {
							$flux['data'][$prefixe . $c->champ] = $erreur;
						}
					}
				}
			}
		}
	}
	return $flux;
}



?>
