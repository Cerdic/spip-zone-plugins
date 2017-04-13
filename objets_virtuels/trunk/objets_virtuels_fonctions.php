<?php
/**
 * Fonctions utiles au plugin Objets virtuels
 *
 * @plugin     Objets virtuels
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Objets_virtuels\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Retourne la liste des tables où le champ virtuel est activé
 *
 * On prend en compte la configuration de ce plugin et la configuration
 * du champ 'virtuel' sur les articles (historique)
 */
function objets_virtuels_tables_actives() {
	$tables = lire_config('objets_virtuels', '');
	$tables = array_filter(explode(',', $tables));
	if (lire_config('articles_redirection') == 'oui') {
		$tables = array_unique(array_merge($tables, ['spip_articles']));
	}
	return $tables;
}

/**
 * Afficher le lien de redirection d'un objet virtuel si il y a lieu
 *
 * @param string $virtuel
 * @return string
 */
function lien_objet_virtuel($virtuel) {
	include_spip('inc/lien');
	if (!$virtuel = virtuel_redirige($virtuel)) {
		return '';
	}

	return propre("[->" . $virtuel . "]");
}


/**
 * Retourne l'URL de redirection d'un objet virtuel, seulement si il est publié
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $connect
 * @param bool $statut Test du statut ?
 * @return array|bool|null
 */
function quete_objet_virtuel($objet, $id_objet, $connect = '', $statut = false) {
	$table = table_objet_sql($objet);
	$infos = lister_tables_objets_sql($table);
	$id_table_objet = id_table_objet($objet);

	$where = [$id_table_objet . '=' . intval($id_objet)];

	// gros bazar pour prendre en compte du mieux que l'on peut le champ statut
	// s'il y en a un de déclaré.
	if ($statut and !empty($infos['statut'])) {
		$principal = array_shift($infos['statut']);
		if (
			!empty($principal['champ'])
			and !empty($principal['publie'])
			and !empty($infos['field'][$principal['champ']])
		) {
			if ($principal['publie'][0] == '!') {
				$where[] = $principal['champ'] . "!=" . sql_quote(substr($principal['publie'], 1));
			} else {
				$where[] = $principal['champ'] . "=" . sql_quote($principal['publie']);
			}
		}
	}

	return sql_getfetsel(
		'virtuel',
		$table,
		$where,
		'',
		'',
		'',
		'',
		$connect
	);
}




/**
 * Si le champ virtuel est non vide c'est une redirection.
 * avec un éventuel raccourci Spip
 *
 * Si le raccourci a un titre il sera pris comme corps du 302
 *
 * @param string $fond
 * @param array $contexte
 * @param string $connect
 * @return array|bool
 */
function public_tester_redirection($fond, $contexte, $connect) {
	include_spip('objets_virtuels_fonctions');

	$table = table_objet_sql($fond);

	if (in_array($table, objets_virtuels_tables_actives())) {
		$id_table_objet = id_table_objet($table);
		if (
			isset($contexte[$id_table_objet])
			and $id_objet = intval($contexte[$id_table_objet])
		) {
			$objet = objet_type($table);
			$m = quete_objet_virtuel($objet, $id_objet, $connect, true);
			if (strlen($m)) {
				include_spip('inc/texte');
				// les navigateurs pataugent si l'URL est vide
				if ($url = virtuel_redirige($m, true)) {
					// passer en url absolue car cette redirection pourra
					// etre utilisee dans un contexte d'url qui change
					// y compris url arbo
					$status = 302;
					if (defined('_STATUS_REDIRECTION_VIRTUEL')) {
						$status = _STATUS_REDIRECTION_VIRTUEL;
					}
					if (!preg_match(',^\w+:,', $url)) {
						include_spip('inc/filtres_mini');
						$url = url_absolue($url);
					}
					$url = str_replace('&amp;', '&', $url);

					return array(
						'texte' => "<"
							. "?php include_spip('inc/headers');redirige_par_entete('"
							. texte_script($url)
							. "','',$status);"
							. "?" . ">",
						'process_ins' => 'php',
						'status' => $status
					);
				}
			}
		}
	}

	return false;
}
