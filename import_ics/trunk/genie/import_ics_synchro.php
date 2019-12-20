<?php

/**
 * Gestion du génie import_ics_synchro
 *
 * @plugin import_ics pour SPIP
 * @license GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/import_ics');
/**
 * Actualise tous les almanachs
 *
 * @genie import_ics_synchro
 *
 * @param int $last
 *     Timestamp de la dernière exécution de cette tâche
 * @return int
 *     Positif : la tâche a été effectuée
 */
function genie_import_ics_synchro_dist($t){

	//on recupère toutes les infos sur les almanachs
	if(
		$resultats = sql_allfetsel('*', 'spip_almanachs')
		and is_array($resultats)
	)
		{
			//pour chacun des almanachs, on va importer les evenements
		foreach ($resultats as $r) {
				$id_almanach = $r['id_almanach'];
				spip_log ("Import via génie de l'almanach $id_almanach",'import_ics'._LOG_INFO);
				importer_almanach(
					$id_almanach,
					$r['url'],
					$r['id_article'],
					array(
						'ete' => $r['decalage_ete'],
						'hiver' => $r['decalage_hiver']
					),
					$r['dtend_inclus']
				);
				spip_log ("Fin de l'import via génie de l'almanach $id_almanach",'import_ics'._LOG_INFO);
			}
			return 1;
		}
}

