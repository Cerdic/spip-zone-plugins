<?php

/**
 * Gestion CVT du formulaire de configuration de RANG
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire de configuration des rangs
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_configurer_rang_charger_dist() {
	$valeurs_meta = lire_config('rang_objets');
	$valeurs['rang_objets']=explode(',',$valeurs_meta);
	return $valeurs;
}

/**
 * Traitement du formulaire de configuration des rangs
 *
 * @return array
 *     Retours du traitement
 **/
function formulaires_configurer_rang_traiter_dist() {
	$res = array('editable' => true);
	$valeurs = _request('rang_objets');
	$err = null;

	// création / mise à jour de la méta
	if (!is_null($valeurs))
		ecrire_meta('rang_objets', is_array($valeurs)?implode(',',$valeurs):'');

	// création du champ 'rang' dans les tables sélectionnées
	// + insertion de valeur dans ce champ
	foreach ($valeurs as $key => $table) {

		if (!empty($table)) {
			// si le champ 'rang' n'existe pas, le créer et le remplir
			$champs_table = sql_showtable($table);
			if (!isset($champs_table['field']['rang'])) {

				// créer le champ 'rang'
				sql_alter('TABLE '.$table.' ADD rang SMALLINT NOT NULL');

				// remplir #1 : si aucun numero_titre n'est trouvé, on met la valeur de l'id_prefixe dans rang
				if (!rang_tester_presence_numero($table)) {
					$id = id_table_objet($table);
					$desc = lister_tables_objets_sql($table);
					if (isset($desc['field']['id_rubrique'])) {
						$quelles_rubriques = sql_allfetsel('id_rubrique', $table, '', 'id_rubrique');

						foreach ($quelles_rubriques as $key => $value) {
							$id_rub =  $value['id_rubrique'];
							$quelles_items = sql_allfetsel($id, $table, 'id_rubrique='.$id_rub);

							$i = 1;
							foreach ($quelles_items as $key => $value) {
								$id_prefixe = $value[$id];
								sql_update($table, array( 'rang' => $i ), "$id = $id_prefixe");
								$i++;
							}
						}
					}
				}

				// remplir #2 sinon , recuperer le numero_titre et l'insérer dans rang
				// à faire !!
			}
		}
	}

	$res['message_ok'] = _T('config_info_enregistree');

	return $res;
}

function rang_tester_presence_numero($table) {
	return false;
}
