<?php
/**
 * Pipelines du plugin Coordonnees
 *
 * @plugin     Coordonnees
 * @copyright  2013
 * @author     Marcimat / Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Coordonnees\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) { return;
}

/*
 * function coordonnees_ieconfig_metas
 *
 * export de configuration avec le plugin ieconfig
 *
 * @param $table
 */
function coordonnees_ieconfig_metas($table) {
	$table['coordonnees']['titre'] = _T('paquet-coordonnees:coordonnees_nom');
	$table['coordonnees']['icone'] = 'prive/themes/spip/images/addressbook-16.png';
	$table['coordonnees']['metas_serialize'] = 'contacts_et_organisations';

	return $table;
}
/**
 * Affichage des coordonnées (adresses, mails, numéros)
 * sur la page de visualisation des objets associes
 * Surcharge possible avec 'prive/squelettes/contenu/coordonnees_fiche_nom-objet-associe.html'
**/
function coordonnees_afficher_complement_objet($flux) {
	$texte = '';
	$exec = isset($flux['args']['exec']) ? $flux['args']['exec'] : _request('exec');
	$e = trouver_objet_exec($exec);
	$type = $flux['args']['type'];

	$id_objet = $flux['args']['id'];
	$editable = (in_array(table_objet_sql($type), lire_config('coordonnees/objets', array())) ? 1 : 0);
	$has = false;
	if (!$editable) {
		if (sql_countsel('spip_adresses_liens', 'objet='.sql_quote($type).' AND id_objet='.intval($id_objet))
		  or sql_countsel('spip_numeros_liens', 'objet='.sql_quote($type).' AND id_objet='.intval($id_objet))
		  or sql_countsel('spip_emails_liens', 'objet='.sql_quote($type).' AND id_objet='.intval($id_objet))
		) {
			$has = true;
		}
	}

	$coordonnees_fiche_objet = 'prive/squelettes/contenu/coordonnees_fiche_'.$type;
	if(!find_in_path($coordonnees_fiche_objet.'.html')){
			$coordonnees_fiche_objet = 'prive/squelettes/contenu/coordonnees_fiche_objet';
	}

	if (!$e['edition'] and ($editable or $has)) {
		$texte .= recuperer_fond(
			$coordonnees_fiche_objet,
			array(
			'objet' => $type,
			'id_objet' => intval($flux['args']['id']),
			'editable' => $editable,
			),
			array('ajax'=>'coordonnees')
		);
	}

	if ($texte) {
		if ($p=strpos($flux['data'], '<!--afficher_fiche_objet-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}


/**
 * Liaisons avec les objets
 * sur la page de visualisation des coordonnées
**/
function coordonnees_affiche_gauche($flux) {
	$texte = '';
	$exec = isset($flux['args']['exec']) ? $flux['args']['exec'] : _request('exec');
	$e = trouver_objet_exec($exec);
	if (!$e['edition']
		and $type = $e['type']
		and in_array($type, array('adresse','email','numero'))
		and $id_coordonnee = $flux['args']["id_${type}"]
	) {
		$texte .= recuperer_fond(
			"prive/squelettes/contenu/utilisations_${type}",
			array(
			"id_${type}" => intval($id_coordonnee)
			),
			array('ajax'=>true)
		);
	}

	if ($texte) {
		$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Optimiser la base de donnees en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @param int $n
 * @return int
 */
function coordonnees_optimiser_base_disparus($flux) {
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('adresse'=>'*', 'numero'=>'*', 'email'=>'*'), '*');
	return $flux;
}

/**
 * Permettre aux JS de savoir si on est dans l'espace privé
 */
function coordonnees_header_prive($flux) {
	$flux = '<script type="text/javascript">var spip_ecrire = true;</script>' . $flux;
	
	return $flux;
}

/**
 * Ajouter le JS pour gérer les adresses suivant les pays
 */
function coordonnees_jquery_plugins($scripts) {
	$scripts[] = "javascript/coordonnees_adresses.js";
	
	return $scripts;
}

/**
 * Modifier les saisies d'adresses chargées si on est dans un formulaire posté et que le pays a changé, car ce ne sont plus les mêmes vérification à faire
 * 
 * @param array $flux
 * @return array
 **/
function coordonnees_formulaire_saisies($flux) {
	// Si on est dans un form posté
	if ($flux['args']['je_suis_poste']) {
		// On récupère les pays qui ont changé par l'API
		if (
			$pays_modifies = _request('coordonnees_noms_pays_modifies')
			and is_array($pays_modifies)
		) {
			include_spip('inc/saisies');
			
			// Pour chaque pays modifié
			foreach ($pays_modifies as $nom) {
				// On récupère le nouveau pays changé
				$code_pays = saisies_request($nom);
				
				// On va chercher où se trouve ce champ et se placer à cette endroit du tableau de saisies
				if ($chemin_pays = saisies_chercher($flux['data'], $nom, true)) {
					$cle_pays = array_pop($chemin_pays);
					$bon_endroit = &$flux['data'];
					foreach ($chemin_pays as $cle) {
						$bon_endroit = &$bon_endroit[$cle];
					}
					
					// On va chercher l'identifiant de cette adresse
					$identifiant = $bon_endroit[$cle_pays]['options']['adresse-id'];
					
					// Le champ de pays est-il obligatoire ?
					$obligatoire = $bon_endroit[$cle_pays]['options']['obligatoire'];
					
					// On va supprimer tous les champs qui suivent de la même adresse
					foreach ($bon_endroit as $cle=>$saisie) {
						if ($cle != $cle_pays) {
							// Dès qu'on a trouvé un champ de la même adresse, on le vire
							if (isset($saisie['options']['adresse-id']) and $saisie['options']['adresse-id'] == $identifiant) {
								unset($bon_endroit[$cle]);
							}
							// Dès que ce n'est plus de la même adresse on arrête tout, c'est qu'on a fini
							else {
								break;
							}
						}
					}
					
					// On génère les champs de saisies propre à ce pays (et donc avec les vérifs pour ce pays)
					$saisies_pays = coordonnees_adresses_saisies_par_pays($code_pays, $obligatoire);
					
					// Si le name a au moins un crochet
					if ($modele = $nom and strpos($modele, '[') !== false) {
						// On remplace le champ pays par $0
						$modele = str_replace('pays', '$0', $modele);
						// On transforme toutes les saisies avec ce modèle
						$saisies_pays =  saisies_transformer_noms(
							$saisies_pays,
							'/^\w+$/',
							$modele
						);
					}
					
					// On rajoute de nouveau le hidden, en PHP cette fois, 
					// car si ça s'arrête dans verifier() il faut continuer de dire que ce n'est pas le même pays qu'au chargement
					$saisies_pays[] = array(
						'saisie' => 'hidden',
						'options' => array(
							'nom' => 'coordonnees_noms_pays_modifies[]',
							'defaut' => $nom,
							'valeur_forcee' => $nom,
						),
					);
					
					// On remet l'identifiant
					foreach ($saisies_pays as $cle=>$saisie) {
						$saisies_pays[$cle]['options']['adresse-id'] = $identifiant;
						$saisies_pays[$cle]['options']['attributs'] = 'data-adresse-id="'.$identifiant.'"';
					}
					
					// On insère ces champs juste après le pays
					array_splice($bon_endroit, $cle_pays+1, 0, $saisies_pays);
				}
			}
		}
	}
	
	
	return $flux;
}
