<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_admin_plugin_charger_dist($voir='actif', $verrouille='non'){
	$valeurs = array();
	
	include_spip('inc/svp_depoter_local');
	stp_actualiser_paquets_locaux();
	
	$valeurs['constante'] = array('_DIR_PLUGINS','_DIR_PLUGINS_SUPPL');
	$valeurs['actif'] = 'oui';
	
	if ($voir == 'inactif')
		$valeurs['actif'] = 'non';
	
	if ($voir == 'tous')
		$valeurs['actif'] = '';
	
	if ($verrouille == 'oui')
		$valeurs['constante'] = array('_DIR_EXTENSIONS');
	
	if ($verrouille == 'tous')
		$valeurs['constante'] = '';
	
	$valeurs['actions'] = array();
	
	return $valeurs;
}

function formulaires_admin_plugin_verifier_dist($voir='actif', $verrouille='non'){
	$actions = _request('actions');

	$erreurs = array();

	return $erreurs;
}

function formulaires_admin_plugin_traiter_dist($voir='actif', $verrouille='non'){
	
	$retour = array();
	
	$actions = _request('actions');
	if ($actions) {
		
#		refuser_traiter_formulaire_ajax();
		foreach ($actions as $a=>$ids_paquets) {
			$ids_paquets = array_keys($ids_paquets);
			$action = '';
			switch ($a) {
				case 'activer':
					$action = 'ajoute';
				case 'desactiver':
					if (!$action) {
						$action = 'enleve';
					}
					// forcer la maj des meta pour les cas de modif de numero de version base via phpmyadmin
					lire_metas();
					include_spip('inc/plugin');
					$paquets = array();
					$new_paquets = sql_allfetsel(
						array('pl.prefixe', 'pa.constante', 'pa.src_archive'),
						array('spip_paquets AS pa', 'spip_plugins AS pl'),
						array('pl.id_plugin=pa.id_plugin', sql_in('id_paquet', $ids_paquets)));
					
					foreach($new_paquets as $c=>$p) {
						$paquets[ $p['prefixe'] ] = /*constant($p['constante']) . */ $p['src_archive'];
					}
					spip_log("Changement des plugins actifs par l'auteur " . $GLOBALS['visiteur_session']['id_auteur'] . ": " . join(',', $paquets));
					ecrire_plugin_actifs($paquets, false, $action);
					break;


				case 'desinstaller':
					$del_paquets = sql_allfetsel(
						array('pl.prefixe', 'pa.constante', 'pa.src_archive'),
						array('spip_paquets AS pa', 'spip_plugins AS pl'),
						array('pl.id_plugin=pa.id_plugin', sql_in('id_paquet', $ids_paquets)));

					$installer_plugins = charger_fonction('installer', 'plugins');
					
					$dels = array();
					foreach ($del_paquets as $del) {
						$infos = $installer_plugins($del['src_archive'], 'uninstall');
						if ($infos AND !$infos['install_test'][0]) {
							$dels[] = $del['src_archive'];
						}
					}
					if ($dels) {
						include_spip('inc/plugin');
						ecrire_plugin_actifs($dels, false, 'enleve');
					}
					break;
			}
		}
		$retour['redirect'] = generer_url_ecrire('admin_plugin');
	}

		
	$retour['editable'] = true;

	return $retour;
}




/**
 * Filtre pour simplifier la creation des actions du formulaire
 * [(#ID_PAQUET|svp_nom_action{desactiver})]
 * actions[desactiver][24]
**/
function filtre_svp_nom_action($id_paquet, $action) {
	return "actions[$action][$id_paquet]";
}

?>
