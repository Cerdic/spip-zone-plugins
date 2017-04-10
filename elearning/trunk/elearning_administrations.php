<?php
#---------------------------------------------------#
#  Plugin  : E-Learning                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : https://contrib.spip.net/Plugin-E-learning  #
#-----------------------------------------------------------------#

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// La fonction de base appelée par le gestionnaire de plugins
function elearning_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	
	// Mise à jour de la config toute dans spip_meta pour SPIP 3
	$maj['1.0.0'] = array(
		array('elearning_maj_1_0_0'),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

// Supprimer les tables du plugin
function elearning_vider_tables($nom_meta_version_base) {
	/* Blabla effacer les tables */
	effacer_meta($nom_meta_version_base);
}

// Passage de la config dans spip_meta
function elearning_maj_1_0_0() {
	include_spip('inc/config');
	
	// On récupère la rubrique e-learning
	if ($id_rubrique = intval(lire_config('elearning/rubrique_elearning'))) {
		// On récupère tous les modules
		$rubriques_modules = sql_allfetsel(
			'id_rubrique, extra',
			'spip_rubriques',
			array(
				'id_parent = '.$id_rubrique
			)
		);
		
		foreach ($rubriques_modules as $rubrique_module) {
			// On cherche si la rubrique a bien une config de elearning
			if (
				$id_module = intval($rubrique_module['id_rubrique'])
				and $rubrique_module['extra']
				and $extra = unserialize($rubrique_module['extra'])
				and is_array($extra)
				and isset($extra['elearning'])
			) {
				$config = $extra['elearning'];
				
				// On écrit la config dans spip_meta
				ecrire_config('elearning/modules/'.$id_module, $config);
				
				// On supprime de spip_rubriques
				unset($extra['elearning']);
				if (empty($extra)) {
					$extra = '';
				}
				else {
					$extra = serialize($extra);
				}
				sql_updateq(
					'spip_rubriques',
					array('extra' => $extra),
					'id_rubrique = '.$id_module
				);
			}
		}
	}
}
