<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline affiche_gauche (SPIP)
 *
 * Insertion du bloc de redirection sur les rubriques
 *
 * @param array $flux
 * @return array
 */
function rubriques_virtuelles_affiche_gauche($flux) {
	if (in_array($flux['args']['exec'], array('rubrique'))
		and $id = $flux['args']['id_rubrique']) {
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/rubriques_virtuelles', array('id_rubrique'=>$id));
	}
	return $flux;
}

/**
 * Insertion dans le pipeline affiche_milieu (SPIP)
 *
 * Insertion du bloc indiquant la redirection eu centre de la rubrique
 *
 * @param array $flux
 * @return array
 */
function rubriques_virtuelles_affiche_milieu($flux) {
	if (in_array($flux['args']['exec'], array('rubrique'))
		and $id = $flux['args']['id_rubrique']) {
		$texte = recuperer_fond(
			'prive/squelettes/inclure/rubriques_virtuelles_centre',
			array('id_rubrique' => $id),
			array('ajax' => true)
		);
		if ($p = strpos($flux['data'], '<div id="wys')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline styliser (SPIP)
 *
 * si le champ virtuel est non vide c'est une redirection.
 * avec un eventuel raccourci Spip
 * si le raccourci a un titre il sera pris comme corps du 302
 *
 * @param string $fond
 * @param array $contexte
 * @param string $connect
 * @return array|bool
 */
function rubriques_virtuelles_styliser($flux) {
	// uniquement si un squelette a ete trouve
	if (($flux['args']['fond'] == 'rubrique') && $id_rubrique = $flux['args']['id_rubrique']) {
		$m = sql_getfetsel('virtuel', 'spip_rubriques', array('id_rubrique='.intval($id_rubrique)));
		if (strlen($m)) {
			include_spip('inc/texte');
			// les navigateurs pataugent si l'URL est vide
			if ($url = virtuel_redirige($m, true)) {
				// passer en url absolue car cette redirection pourra
				// etre utilisee dans un contexte d'url qui change
				// y compris url arbo
				$status = 302;
				if (defined('_STATUS_REDIRECTION_VIRTUEL')) {
					$status=_STATUS_REDIRECTION_VIRTUEL;
				}
				if (!preg_match(',^\w+:,', $url)) {
					include_spip('inc/filtres_mini');
					$url = url_absolue($url);
				}
				$url = str_replace('&amp;', '&', $url);
				include_spip('inc/headers');
				redirige_par_entete(texte_script($url), '', $status);
				return;
			}
		}
	}

	return $flux;
}

/**
 * Insertion dans le pipeline objet_compte_enfants (SPIP)
 *
 * Une rubrique est considérée comme vide lorsqu'elle n'a pas d'objets liés (articles, rubriques, documents).
 *
 * Ici on impose que le champ "virtuel" doit être vide pour que la rubrique soit considérée comme vide.
 *
 * @param unknown $flux
 * @return number
 */
function rubriques_virtuelles_objet_compte_enfants($flux) {
	if ($flux['args']['objet'] == 'rubrique') {
		$id_rubrique = $flux['args']['id_objet'];
		$virtuel = sql_getfetsel('virtuel', 'spip_rubriques', 'id_rubrique='.intval($id_rubrique));
		if (strlen(trim($virtuel)) > 0) {
			$flux['data']['redirection'] = 1;
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline calculer_rubriques (SPIP)
 * (cf calculer_rubriques_publiees() dans inc/rubriques)
 *
 * Évite de dépublier une rubrique avec une redirection
 *
 * @param null $flux
 * @return null
 */
function rubriques_virtuelles_calculer_rubriques($flux) {
	$rubriques_virtuelles_non_publiees = sql_allfetsel(
		'id_rubrique, statut, id_parent',
		'spip_rubriques',
		'statut_tmp != "publie" AND virtuel != ""'
	);
	foreach ($rubriques_virtuelles_non_publiees as $rub) {
		sql_updateq('spip_rubriques', array('statut_tmp'=> 'publie'), 'id_rubrique='.intval($rub['id_rubrique']));
	}
	return $flux;
}

function autoriser_rubriques_virtuelles() {
}

if (!function_exists('autoriser_rubrique_supprimer')) {
	function autoriser_rubrique_supprimer($faire, $type, $id, $qui, $opt) {
		$virtuel = sql_getfetsel('virtuel', 'spip_rubriques', 'id_rubrique='.intval($id));
		if (strlen($virtuel) > 0) {
			return false;
		}
		return autoriser_rubrique_supprimer_dist($faire, $type, $id, $qui, $opt);
	}
}
