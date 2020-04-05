<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * La fonction de sauvegarde des fichiers
 *
 * @param array/null $liste
 */
function inc_mes_fichiers_sauver_dist($liste=null, $options=array()) {

	include_spip('inc/mes_fichiers_utils');
	include_spip('inc/config');
	$erreur_texte = '';

	/**
	 * Si $liste == null c'est que l'on veut tout sauvegarder de possible
	 * il peut être un array vide dans le cas d'un problème de config
	 */
	if (is_null($liste)) {
		$liste = mes_fichiers_a_sauver();
	}

	if (count($liste)>0) {
		include_spip('inc/pclzip');

		if (defined('_DIR_SITE')) {
			$remove_path = _DIR_SITE;
		}
		else {
			$remove_path = _DIR_RACINE;
		}

		/**
		 * On vérifie que les répertoires ne sont pas trop gros
		 * On nomme les fichiers et répertoires pour les commentaires sans
		 * _DIR_RACINE ni _DIR_MUTU
		 */
		$taille_max = intval(lire_config('mes_fichiers/taille_max_rep', '75'))*1000*1000;
		foreach ($liste as $key => $item) {
			if(is_dir($item) AND (mes_fichiers_dirsize($item) > $taille_max)) {
				unset($liste[$key]);
			}
			else {
				$liste_finale[] = mes_fichiers_joli_repertoire($item);
			}
		}

		/**
		 * On lance l'archivage du contenu
		 */
		if (!@is_dir(_DIR_MES_FICHIERS))
			$dir = sous_repertoire(_DIR_TMP,"mes_fichiers");

		$prefixe = lire_config('mes_fichiers/prefixe','mf2');
		$mes_fichiers = new PclZip(_DIR_MES_FICHIERS . $prefixe.'_'.date("Ymd_His").'.zip');

		$auteur = $options['auteur'] ? $options['auteur'] : $GLOBALS['visiteur_session']['id_auteur'];
		if ($auteur == 'cron')
			$auteur = 'SPIP';
		$comment = array('auteur' => $auteur, 'contenu' => $liste_finale);

		$erreur = $mes_fichiers->create(
			$liste,
			PCLZIP_OPT_COMMENT,serialize($comment),
			PCLZIP_OPT_REMOVE_PATH, $remove_path,
			PCLZIP_OPT_ADD_TEMP_FILE_ON);

		if($erreur == 0){
			$erreur_texte = $mes_fichiers->errorInfo(true);
		}
	}
	else {
		$erreur_texte = _T('mes_fichiers:erreur_aucun_fichier_sauver');
	}

	/**
	 * Un pipeline post_sauvegarde pour que d'autres plugins puissent
	 * agir à ce moment là
	 */
	pipeline('post_sauvegarde',
		array(
			'args' => array(
				'err' => $erreur_texte,
				'auteur' => $auteur,
				'type' => 'mes_fichiers_sauver'
			),
			'data' => ''
		)
	);

    /**
     * Notifications si nécessaire
     */
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('mes_fichiers_sauver', '',
			array(
				'auteur' => $auteur,
				'err' => $erreur_texte)
		);
	}

	return $erreur_texte;
}

?>