<?php

/**
 * Utilisation de pipelines
 *
 * @package SPIP\Formidable\Pipelines
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/formidable_fichiers');
define(
	'_RACCOURCI_MODELE_FORMIDABLE',
	'(<(formulaire\|formidable|formidable|form)' # <modele
	.'([0-9]*)\s*' # id
	.'([|](?:<[^<>]*>|[^>])*)?' # |arguments (y compris des tags <...>)
	.'>)' # fin du modele >
	.'\s*(<\/a>)?' # eventuel </a>
);

/**
 * Ajouter la protection NoSpam de base a formidable (jeton)
 *
 * @param $formulaires
 * @return array
 */
function formidable_nospam_lister_formulaires($formulaires) {
	$formulaires[] = 'formidable';
	return $formulaires;
}

/**
 * Trouver les liens <form
 * @param $texte
 * @return array
 */
function formidable_trouve_liens($texte) {
	$formulaires = array();
	if (preg_match_all(','._RACCOURCI_MODELE_FORMIDABLE.',ims', $texte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $r) {
			$id_formulaire = 0;
			if ($r[2] == 'formidable') {
				$id_formulaire = $r[3];
			} elseif ($r[2] == 'form') {
				$id_formulaire = sql_getfetsel(
					'id_formulaire',
					'spip_formulaires',
					'identifiant='.sql_quote('form'.$r[3])
				);
			} elseif ($r[2] == 'formulaire|formidable') {
				$args = ltrim($r[4], '|');
				$args = explode('=', $args);
				$args = $args[1];
				$args = explode('|', $args);
				$args = trim(reset($args));
				if (is_numeric($args)) {
					$id_formulaire = intval($args);
				} else {
					$id_formulaire = sql_getfetsel(
						'id_formulaire',
						'spip_formulaires',
						'identifiant='.sql_quote($args)
					);
				}
			}
			if ($id_formulaire = intval($id_formulaire)) {
				$formulaires[$id_formulaire] = $id_formulaire;
			}
		}
	}
	return $formulaires;
}

/**
 * Associer/dissocier les formulaires a un objet qui les utilise (ou ne les utilise plus)
 * @param $flux
 * @return mixed
 */
function formidable_post_edition($flux) {
	if (isset($flux['args']['table'])
		and $table = $flux['args']['table']
		and $id_objet = intval($flux['args']['id_objet'])
		and $primary = id_table_objet($table)
		and $row = sql_fetsel('*', $table, "$primary=".intval($id_objet))
	) {
		$objet = objet_type($table);
		$contenu = implode(' ', $row);
		$formulaires = formidable_trouve_liens($contenu);
		include_spip('action/editer_liens');
		$deja = objet_trouver_liens(array('formulaire' => '*'), array($objet => $id_objet));
		$del = array();
		if (count($deja)) {
			foreach ($deja as $l) {
				if (isset($formulaires[$l['id_formulaire']])) {
					unset($formulaires[$l['id_formulaire']]);
				} else {
					$del[] = $l['id_formulaire'];
				}
			}
		}
		if (count($formulaires)) {
			objet_associer(array('formulaire' => $formulaires), array($objet => $id_objet));
		}
		if (count($del)) {
			objet_dissocier(array('formulaire' => $del), array($objet=>$id_objet));
		}
	}
	return $flux;
}

/**
 * Afficher les formulaires utilises par un objet
 * @param $flux
 * @return mixed
 */
function formidable_affiche_droite($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
		and isset($e['type'])
		and $objet = $e['type']
		and isset($flux['args'][$e['id_table_objet']])
		and $id = $flux['args'][$e['id_table_objet']]
		and sql_countsel('spip_formulaires_liens', 'objet='.sql_quote($objet).' AND id_objet='.intval($id))) {
		$flux['data'] .= recuperer_fond(
			'prive/squelettes/inclure/formulaires_lies',
			array('objet' => $objet, 'id_objet' => $id)
		);
	}
	return $flux;
}

/**
 * Afficher l'édition des liens sur les objets configurés
 **/
function formidable_affiche_milieu($flux) {
	include_spip('inc/config');
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);

	if (!$e['edition'] and isset($flux['args'][$e['id_table_objet']]) and in_array($e['table_objet_sql'], lire_config('formidable/objets', array()))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'formulaires',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p=strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}

/**
 * Optimiser la base de donnée en enlevant les liens de formulaires supprimés
 *
 * @pipeline optimiser_base_disparus
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline
 */
function formidable_optimiser_base_disparus($flux) {
	// Les formulaires qui sont à la poubelle
	$res = sql_select(
		'id_formulaire AS id',
		'spip_formulaires',
		'statut='.sql_quote('poubelle')
	);
	$res2 = sql_select(
		'id_formulaire AS id',
		'spip_formulaires',
		'statut='.sql_quote('poubelle')
	);//Copie pour supprimer les fichiers

	// On génère la suppression
	$flux['data'] += optimiser_sansref('spip_formulaires', 'id_formulaire', $res);

	while ($reponse = sql_fetch($res2)) {
		$flux['data'] += formidable_effacer_fichiers_formulaire($reponse['id']);
	}

	// les reponses qui sont associees a un formulaire inexistant
	$res = sql_select(
		'R.id_formulaire AS id',
		'spip_formulaires_reponses AS R LEFT JOIN spip_formulaires AS F ON R.id_formulaire=F.id_formulaire',
		'R.id_formulaire > 0 AND F.id_formulaire IS NULL'
	);

	$flux['data'] += optimiser_sansref('spip_formulaires_reponses', 'id_formulaire', $res);

	// Les réponses qui sont à la poubelle
	$res = sql_select(
		'id_formulaires_reponse AS id, id_formulaire AS form',
		'spip_formulaires_reponses',
		'statut='.sql_quote('poubelle')
	);
	$res2 = sql_select(
		'id_formulaires_reponse AS id, id_formulaire AS form',
		'spip_formulaires_reponses',
		sql_in('statut', array('refuse', 'poubelle'))
	);	//Copie pour la suppression des fichiers des réponses, c'est idiot de pas pouvoir faire une seule requete
	// On génère la suppression
	$flux['data'] += optimiser_sansref('spip_formulaires_reponses', 'id_formulaires_reponse', $res);
	while ($reponse = sql_fetch($res2)) {
		$flux['data'] += formidable_effacer_fichiers_reponse($reponse['form'], $reponse['id']);
	}


	// les champs des reponses associes a une reponse inexistante
	$res = sql_select(
		'C.id_formulaires_reponse AS id',
		'spip_formulaires_reponses_champs AS C
			LEFT JOIN spip_formulaires_reponses AS R ON C.id_formulaires_reponse=R.id_formulaires_reponse',
		'C.id_formulaires_reponse > 0 AND R.id_formulaires_reponse IS NULL'
	);

	$flux['data'] += optimiser_sansref('spip_formulaires_reponses_champs', 'id_formulaires_reponse', $res);

	return $flux;
}

/**
 * S'assurer que le traitement email ait lieu après le traitement enregistrement
 *
 * @pipeline formidable_traitements
 * @param array $flux
 * @return array $flux
 **/
function formidable_formidable_traitements($flux) {
	if (isset($flux['data']['email']) and isset($flux['data']['enregistrement'])) {
		$keys = array_keys($flux['data']);
		$position_email = array_search('email', $keys);
		$position_enregistrement = array_search('enregistrement', $keys);

		if ($position_enregistrement > $position_email) { // si enregistrement après email
			$nouveau_tab = array();
			foreach ($keys as $key) { //on reconstruit le tableau, en inversant simplement email et enregistrement
				if ($key == 'email') {
					$nouveau_tab['enregistrement'] = $flux['data']['enregistrement'];
				} elseif ($key == 'enregistrement') {
					$nouveau_tab['email'] = $flux['data']['email'];
				} else {
					$nouveau_tab[$key] = $flux['data'][$key];
				}
			}
			$flux['data'] = $nouveau_tab;
		}
	}

	return $flux;
}

/** Hasher les ip régulièrement
 *  @param array $flux
 *  @return array $flux
**/
function formidable_taches_generales_cron($flux) {
	$flux['formidable_hasher_ip'] = 24*3600;
	$flux['formidable_effacer_fichiers_email'] = 24*3600;
	$flux['formidable_effacer_enregistrements'] = 24*3600;
	return $flux;
}

/** Déclarer les formulaires et les réponses
 * au plugin corbeille
 * @param array $flux;
 * @return array $flux;
**/
function formidable_corbeille_table_infos($flux) {
	$flux['formulaires']= array(
		'statut'=>'poubelle',
		'table'=>'formulaires',
		'tableliee'=>array('spip_formulaires_liens')
	);
	$flux['formulaires_reponses']= array(
		'statut'=>'poubelle',
		'table'=>'formulaires_reponses'
	);
	return $flux;
}
