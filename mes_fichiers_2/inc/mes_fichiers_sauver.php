<?php
/**
 * La fonction de sauvegarde des fichiers
 * @param unknown_type $liste
 */
function inc_mes_fichiers_sauver_dist($liste=null,$options=array()){
	/**
	 * Si $liste == null c'est que l'on veut tout sauvegarder de possible
	 * il peut être un array vide dans le cas d'un problème de config
	 */
	if(is_null($liste)){
		include_spip('inc/mes_fichiers_utils');
		$liste = mes_fichiers_a_sauver();
	}

	if(count($liste)>0){
		include_spip('inc/pclzip');

		// Archivage du contenu
		if (!@is_dir(_DIR_MES_FICHIERS))
			$dir = sous_repertoire(_DIR_TMP,"mes_fichiers");
		$prefixe = lire_config('mes_fichiers/prefixe','mf2');
		$mes_fichiers = new PclZip(_DIR_MES_FICHIERS . $prefixe.'_'.date("Ymd_His").'.zip');
		$auteur = $options['auteur'] ? $options['auteur'] : $GLOBALS['visiteur_session']['id_auteur'];
		$comment = array('auteur' => $auteur, 'contenu' => $liste);
		$erreur = $mes_fichiers->create($liste, PCLZIP_OPT_COMMENT, serialize($comment));
		if($erreur == 0){
			$erreur_texte = $mes_fichiers->errorInfo(true);
		}
	}else{
		$erreur_texte = _T('mes_fichiers:erreur_aucun_fichier_sauver');
	}

	// Pipeline
	pipeline('post_sauvegarde',
		array(
			'args' => array(
				'err' => $erreur_texte,
				'auteur' => $auteur,
				'type' => 'mes_fichiers'
			),
			'data' => ''
		)
	);

    /**
     * notifications si necessaire
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