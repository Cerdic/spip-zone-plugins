<?php
/**
 * Pipeline pour Xiti
 *
 * @plugin     Xiti
 * @copyright  2014-2017
 * @author     France diplomatie - Vincent
 * @licence    GNU/GPL
 * @package    SPIP\Xiti\administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj des tables xiti
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function xiti_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();
	$maj['create'] = array(
		// On ajoute les nouvelles tables
		// spip_xiti_niveaux
		// spip_xiti_niveaux_liens
		array('maj_tables', array('spip_xiti_niveaux', 'spip_xiti_niveaux_liens')),
		array('xiti_maj_ssl')
	);
	$maj['1.5.0'] = array(
		// On ajoute les nouvelles tables
		// spip_xiti_niveaux
		// spip_xiti_niveaux_liens
		array('maj_tables', array('spip_xiti_niveaux', 'spip_xiti_niveaux_liens'))
	);
	$maj['2.0.0'] = array(
		array('xiti_maj_ssl')
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables xiti
 *
 * Supprime la configuration de Xiti et les deux tables
 * gérant les niveaux deux
 *
 * @param string $nom_meta_base_version
 */
function xiti_vider_tables($nom_meta_base_version) {
	effacer_meta('xiti');
	sql_drop_table('spip_xiti_niveaux');
	sql_drop_table('spip_xiti_niveaux_liens');
	sql_delete('spip_versions', sql_in('objet', array('xiti_niveau')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('xiti_niveau')));
	effacer_meta($nom_meta_base_version);
}

/**
 * Fonction de mise à jour qui configure autant que possible automatiquement
 * les serveurs de log SSL Xiti
 */
function xiti_maj_ssl() {
	include_spip('inc/config');
	$config = lire_config('xiti');
	$modif = false;
	if (isset($config['xtsd_xiti']) && trim($config['xtsd_xiti']) != '') {
		$log_ssl = xiti_log_to_logssl($config['xtsd_xiti']);
		if ($log_ssl) {
			$config['logssl'] = $log_ssl;
			$modif = true;
		}
		$secteurs = sql_allfetsel('id_rubrique', 'spip_rubriques', 'id_parent="0"');
		foreach ($secteurs as $rubrique) {
			if (isset($config['xtsd_xiti_'.$rubrique['id_rubrique']])
				and trim($config['xtsd_xiti_'.$rubrique['id_rubrique']]) != '') {
				$log_ssl_rubrique = xiti_log_to_logssl($config['xtsd_xiti_'.$rubrique['id_rubrique']]);
				if ($log_ssl_rubrique) {
					$config['logssl_xiti_'.$rubrique['id_rubrique']] = $log_ssl_rubrique;
					$modif = true;
				}
			}
		}
		$langues = explode(',', lire_config('langues_utilisees'));
		if (count($langues) > 1) {
			foreach ($langues as $langue) {
				if (isset($config['xtsd_xiti_'.$langue])
					and trim($config['xtsd_xiti_'.$langue]) != '') {
					$log_ssl_langue = xiti_log_to_logssl($config['xtsd_xiti_'.$langue]);
					if ($log_ssl_langue) {
						$config['logssl_'.$langue] = $log_ssl_langue;
						$modif = true;
					}
				}
			}
		}
	}
	if ($modif) {
		ecrire_config('xiti', $config);
	}
}

/**
 * Translation de serveurs de log sans SSL connus en log SSL
 *
 * @param string $log
 * @return boolean|string
 */
function xiti_log_to_logssl($log) {
	$log_ssl = false;
	$logs_ssl = array(
		'logc187' => 'logs1187',
		'logc407' => 'logs1407',
		'logc279' => 'logs1279',
		'logp4' => 'logs4',
		'logp5' => 'logs152',
		'logi242' => 'logs1242',
		'logc20' => 'logs2',
		'logi241' => 'logs1241',
		'logi118' => 'logs177',
		'logi125' => 'logs1125',
		'logi162' => 'logs2',
		'logi104' => 'logs177',
		'logi103' => 'logs11'
	);

	$logs_key = array_keys($logs_ssl);
	$log = trim($log);
	if (in_array($log, $logs_key)) {
		$log_ssl = $logs_ssl[$log];
	}
	return $log_ssl;
}
