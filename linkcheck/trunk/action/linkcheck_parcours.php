<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_linkcheck_parcours_dist() {
	set_time_limit(0);
	include_spip('inc/autoriser');
	include_spip('inc/linkcheck_fcts');

	include_spip('inc/linkcheck_vars');
	include_spip('inc/linkcheck_fcts');
	include_spip('inc/autoriser');
	include_spip('inc/queue');
	include_spip('inc/config');

	global $db_ok;
	$branche=_request('branche', 0);

	if (autoriser('webmestre')) {
		//on regarde si la fonction a déjà été effectuée partiellement en récupérant les ids de reprise
		$dio = lire_config('linkcheck_dernier_id_objet');
		$do = lire_config('linkcheck_dernier_objet');
		$etat = lire_config('linkcheck_etat_parcours');

		//si le parcours a déja été réalisé, on reinitialise les méta
		if ($etat) {
			ecrire_config('linkcheck_dernier_id_objet', 0);
			ecrire_config('linkcheck_dernier_objet', 0);
		}

		//pour chaque tables
		$tables_a_traiter = linkcheck_tables_a_traiter();
		foreach ($tables_a_traiter as $key_table => $table) {
			//si on en est bien a cette table
			if (($do && $do<$key_table) || !$do) {
				foreach ($table as $table_sql => $info_table) {
					if (in_array($table_sql, array('spip_plugins'))) {
						continue;
					}
					$nom_champ_id=id_table_objet($table_sql);
					// Récuperer la liste des champs suivant le type d'objet
					$tab_champs_a_traiter = linkcheck_champs_a_traiter($info_table);
					if (empty($tab_champs_a_traiter)) {
						continue;
					}
					$champs_a_traiter = is_array($tab_champs_a_traiter) ?
					implode(',', array_keys($tab_champs_a_traiter)) : '*';

					// Recommencer à l'endroit ou l'on s'est arrêté
					$where  = $nom_champ_id.'>'.intval($dio);

					// Ne sélectionner que les objets dans la base qui contiennent des URLs
					// @todo Tester pour oracle et sqlite
					if ($db_ok['type'] === 'mysql') {
						$tab_expreg_mysql = array(
							"(((http|https|ftp|ftps)://)?www\.)|((http|https|ftp|ftps)://(.*\.)?)(.*\.)+[a-zA-Z0-9]{2,9}(/.*)?(\'|\"| |\.|\]|,|;|\s|\->)/?",
							'(\->)([a-zA-Z]{3,10}[0-9]{1,})\]'
						);

						$where_reg = array();
						foreach ($tab_champs_a_traiter as $nom_champs => $type_champs) {
							if ($type_champs) {
								foreach ($tab_expreg_mysql as $expreg) {
									$where_reg[] = $nom_champs . ' regexp(\'' . $expreg . '\')';
								}
							} else {
								$where_reg[] = $nom_champs . ' <> \'\'';
							}
						}
						$where .= (!empty($where_reg)) ? ' AND (' . implode(' or ', $where_reg) . ')' : '';
					}
					// On réduit la recherche à une branche du site

					$where .= ($branche > 0) ?
					' AND (id_rubrique IN(' . implode(',', linkcheck_marmots($branche)) . '))' : '';

					if (isset($info_table['statut'][0]['previsu'])) {
						$statuts = explode(',', str_replace('/auteur', '', $info_table['statut'][0]['previsu']));
						foreach ($statuts as $key => $val) {
							if ($val == '!') {
								unset($statuts[$key]);
							}
						}
						if (count($statuts) > 0) {
							$where .= ' AND '.sql_in('statut', $statuts);
						}
					} elseif (isset($info_table['field']['statut'])) {
						// On exclus de la selection, les objets dont le statut est refuse ou poubelle
						$where .= ' AND '.sql_in('statut', array('refuse', 'poubelle'), true);
					}

					$sql = sql_allfetsel(
						$nom_champ_id.','.$champs_a_traiter,
						$table_sql,
						$where,
						'',
						$nom_champ_id.' ASC'
					);

					//pour chaque objet
					$objet = objet_type($table_sql);
					foreach ($sql as $res) {
						//on créé les variables à envoyer
						$id_objet = $res[$nom_champ_id];
						unset($res[$nom_champ_id]);

						//on liste les liens
						$tab_liens = linkcheck_lister_liens($res);

						// On regarde si l'objet parent est publie
						$objet_publie = objet_test_si_publie($objet, $id_objet);

						if ($objet_publie) {
							$publie = 'oui';
						} else {
							$publie = 'non';
						}

						//on les insere dans la base
						linkcheck_ajouter_liens($tab_liens, $objet, $id_objet, $publie);

						//on renseigne les ids de reprise
						ecrire_config('linkcheck_dernier_id_objet', $id_objet);
					}
					//on renseigne l'indice de la table
					ecrire_config('linkcheck_dernier_objet', $key_table);
				}
			}
		}

		//quand la fonction a été executée en entier on renseigne la base
		ecrire_config('linkcheck_etat_parcours', true);
	}

	if ($redirect = _request('redirect')) {
		include_spip('inc/headers');
		redirige_par_entete($redirect.'&message=parcours_ok');
	}
}
