<?php
	include_spip('inc/meta');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	
	/**
	 * Mises a jour des tables de gestion des transactions lors des montees de version du code
	 *
	 * @param texte $nom_meta_base_version
	 * @param texte $version_cible
	 */
	function transaction_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			if ($current_version==0.0){
				if (include_spip('base/transaction')){
					creer_base();
					echo "Transaction Install<br/>";
					ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
				}
				else return;
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}
		}
	}
	
	/**
	 * Suppression des tables transactions lors de la desinstallation
	 *
	 * @param texte $nom_meta_base_version
	 */
	function transaction_vider_tables($nom_meta_base_version) {
		sql_drop_table('spip_formulaires_transactions');
		effacer_meta($nom_meta_base_version);
	}

?>
