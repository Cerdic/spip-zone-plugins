<?php

/**
 * Chargement des fonctions pour les squelettes
 *
 * @package SPIP\Formidable\Fonctions
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/formidable');
include_spip('public/formidable_criteres');

/**
 * #VOIR_REPONSE{checkbox_2} dans une boucle (FORMULAIRES_REPONSES)
 *
 * @param Pile $p
 * @return Pile
 */
function balise_VOIR_REPONSE_dist($p) {
	$nom = interprete_argument_balise(1, $p);
	if (!$type_retour = interprete_argument_balise(2, $p)) {
		$type_retour = 'null';
	}
	if (!$sans_reponse = interprete_argument_balise(3, $p)) {
		$sans_reponse = 'null';
	}
	$id_formulaires_reponse = champ_sql('id_formulaires_reponse', $p);
	$id_formulaire = champ_sql('id_formulaire', $p);
	$boucle = $p->boucles;
	$boucle = current($boucle);
	$sql_serveur = $boucle->sql_serveur;
	$sql_serveur = "'$sql_serveur'";
	$p->code = "calculer_voir_reponse($id_formulaires_reponse, $id_formulaire, $nom, $sql_serveur, $type_retour, $sans_reponse)";
	return $p;
}

/**
 * @param int $id_formulaires_reponse
 * @param int $id_formulaire
 * @param string $nom
 * @param string $sql_serveur
 * @param string $type_retour
 *   'brut' : valeur brute
 *   'valeur_uniquement' : la valeur seulement
 *   'label' : le label associé à la saisie
 *   'edit' : pour les crayons
 *   defaut : tout le HTML de la saisie
 * @param null|string $sans_reponse
 *   texte affiche si aucune valeur en base pour ce champ
 * @return array|string
 */
function calculer_voir_reponse($id_formulaires_reponse, $id_formulaire, $nom, $sql_serveur = '', $type_retour = null, $sans_reponse = null) {
	static $formulaires_saisies = array();
	static $reponses_valeurs = array();
	$tenter_unserialize = charger_fonction('tenter_unserialize', 'filtre/');

	// Si pas déjà présent, on cherche les saisies de ce formulaire
	if (!isset($formulaires_saisies[$id_formulaire])) {
		$formulaires_saisies[$id_formulaire] = unserialize(
			sql_getfetsel('saisies',//select
			'spip_formulaires',//from
			'id_formulaire = '.intval($id_formulaire),//where
			'',//groupby
			'',//orderby
			'',//limit
			'',//having
			$sql_serveur
		)
		);
	}
	// Si pas déjà présent, on cherche les valeurs de cette réponse
	if (!isset($reponses_valeurs[$id_formulaires_reponse])) {
		if ($champs = sql_allfetsel(
			'nom,valeur,id_formulaires_reponses_champ',//select
			'spip_formulaires_reponses_champs',//from
			'id_formulaires_reponse = '.intval($id_formulaires_reponse),//where
			'',//groupby
			'',//orderby
			'',//limit
			'',//having
			$sql_serveur//
		)) {
			foreach ($champs as $champ) {
				$reponses_valeurs[$id_formulaires_reponse][$champ['nom']] = array(
					'valeur' =>  $tenter_unserialize($champ['valeur']),
					'id' => $champ['id_formulaires_reponses_champ']
				);
			}
		}
	}

	// Si on demande la valeur brute, on ne génère rien, on renvoie telle quelle
	if ($type_retour == 'brut') {
		return $reponses_valeurs[$id_formulaires_reponse][$nom]['valeur'];
	}

	// Si on demande edit > mode crayon > on génère le crayon correspond
	if ($type_retour == 'edit') {
		return 'crayon '.'formulaires_reponses_champ-valeur-'. $reponses_valeurs[$id_formulaires_reponse][$nom]['id'];
	}
	// Si on trouve bien la saisie demandée
	if ($saisie = saisies_chercher($formulaires_saisies[$id_formulaire], $nom)) {
		// Si on demande le label, on ne génère rien, on renvoie juste le label
		if ($type_retour == 'label') {
			return $saisie['options']['label'];
		}
		// On génère la vue de cette saisie avec la valeur trouvée précédemment
		return recuperer_fond(
			'saisies-vues/_base',
			array_merge(
				array(
					'type_saisie' => $saisie['saisie'],
					'valeur' => $reponses_valeurs[$id_formulaires_reponse][$nom]['valeur'],
					'valeur_uniquement' => ($type_retour == 'valeur_uniquement' ? 'oui' : 'non'),
					'sans_reponse' => $sans_reponse,
				),
				$saisie['options']
			)
		);
	}
}

/**
 * Afficher le resume d'une reponse selon un modele qui contient des noms de champ "@input_1@ ..."
 *
 * @param int $id_formulaires_reponse
 * @param int $id_formulaire
 * @param string $resume_reponse
 * @return string
 */
function affiche_resume_reponse($id_formulaires_reponse, $id_formulaire = null, $modele_resume = null) {
	static $modeles_resume = array();
	static $modeles_vars = array();

	if (is_null($id_formulaire)) {
		$id_formulaire = sql_getfetsel(
			'id_formulaire',
			'spip_formulaires_reponses',
			'id_formulaires_reponse='.intval($id_formulaires_reponse)
		);
	}
	if (is_null($modele_resume) and !isset($modeles_resume[$id_formulaire])) {
		$traitements_formulaire = unserialize(sql_getfetsel(
			'traitements',
			'spip_formulaires',
			'id_formulaire='.intval($id_formulaire)
		));
		if (isset($traitements_formulaire['enregistrement']['resume_reponse'])) {
			$modeles_resume[$id_formulaire] = $traitements_formulaire['enregistrement']['resume_reponse'];
		} else {
			$modeles_resume[$id_formulaire] = '';
		}
	}
	if (is_null($modele_resume)) {
		$modele_resume = $modeles_resume[$id_formulaire];
	}

	if (!$modele_resume) {
		return '';
	}

	if (!isset($modeles_vars[$modele_resume])) {
		preg_match_all(',@(.*)@,Uims', $modele_resume, $matches);
		$modeles_vars[$modele_resume] = $matches[1];
	}

	$valeurs = array();
	foreach ($modeles_vars[$modele_resume] as $var) {
		$valeur = calculer_voir_reponse($id_formulaires_reponse, $id_formulaire, $var, 'valeur_uniquement', '');
		$valeur = formidable_nettoyer_saisie_vue($valeur);
		$valeurs["@$var@"] = $valeur;
	}
	return pipeline(
		'formidable_affiche_resume_reponse',
		array(
			'args' => array(
				'id_formulaire' => $id_formulaire,
				'id_formulaires_reponse' => $id_formulaires_reponse,
				'modele_resume' => $modele_resume,
				'valeurs' => $valeurs,
			),
			'data' => str_replace(array_keys($valeurs), array_values($valeurs), $modele_resume),
		)
	);
}

/**
 * Supprimer les balise d'une vue de saisies
 * sans pour autant faire un trim
 * @param str $valeur
 * @return str
**/
function formidable_nettoyer_saisie_vue($valeur) {
	// on ne veut pas du \n de PtoBR, mais on ne veut pas non plus faire un trim
	$valeur = str_ireplace('</p>', '', $valeur);
	$valeur = PtoBR($valeur);
	if (strpos($valeur, '</li>')) {
		$valeur = explode('</li>', $valeur);
		array_pop($valeur);
		$valeur = implode(', ', $valeur);
	}
	$valeur = supprimer_tags($valeur);
	$valeur = str_replace("\n"," ",$valeur);
	$valeur = str_replace("\r"," ",$valeur);
	$valeur = str_replace("\t"," ",$valeur);
	return $valeur;
}

/**
 * Si une saisie est de type 'fichiers'
 * insère dans la description du résultat de cette saisie
 * l'url de l'action pour récuperer la saisie
 * Ajoute également une vignette correspondent à l'extention
 * @param array $saisie_a_modifier
 * @param string $nom_saisie
 * @param array $saisies_du_formulaire
 * @param int|string $id_formulaire
 * @param int|string $id_formulaires_reponse
 * return array $saisie_a_modifier
 **/
function formidable_ajouter_action_recuperer_fichier($saisie_a_modifier, $nom_saisie, $saisies_du_formulaire, $id_formulaire, $id_formulaires_reponse) {
	// précaution
	include_spip('inc/saisies_lister');
	include_spip('inc/formidable_fichiers');
	$id_formulaire = strval($id_formulaire);
	$id_formulaires_reponse = strval($id_formulaires_reponse);
	$vignette_par_defaut = charger_fonction('vignette', 'inc/');
	if (array_key_exists($nom_saisie, saisies_lister_avec_type($saisies_du_formulaire, 'fichiers'))) { //saisies SPIP
		if (isset($saisie_a_modifier) and is_array($saisie_a_modifier)) {
			foreach ($saisie_a_modifier as $i => $valeur) {
				$url = formidable_generer_url_action_recuperer_fichier(
					$id_formulaire,
					$id_formulaires_reponse,
					$nom_saisie,
					$valeur['nom']
				);
				$saisie_a_modifier[$i]['url'] = $url;
				if (in_array($valeur['extension'],array('png','jpg','gif'))) {
					$saisie_a_modifier[$i]['vignette'] = _DIR_FICHIERS_FORMIDABLE."formulaire_$id_formulaire/reponse_$id_formulaires_reponse/$nom_saisie/".$valeur['nom'];
				}	else {
					$saisie_a_modifier[$i]['vignette'] = $vignette_par_defaut($valeur['extension'], false);
				}
			}
		}
	}
	return $saisie_a_modifier;
}
