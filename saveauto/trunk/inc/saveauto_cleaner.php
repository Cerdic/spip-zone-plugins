<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * La fonction de suppression des sauvegardes obsolètes
 * Retourne un array() $liste des fichiers supprimés
 *
 * @param array $options
 * @return array $liste
 */
function inc_saveauto_cleaner_dist($options=array()){
	$temps = time();

	include_spip('inc/config');
	$jours_obso = intval(lire_config('saveauto/jours_obso', 15));
	$auteur = $options['auteur'] ? $options['auteur'] : $GLOBALS['visiteur_session']['id_auteur'];

	if($jours_obso > 0){
		$prefixe = lire_config('saveauto/prefixe_save','sav').'_';
	    $sauvegardes = preg_files(_DIR_DUMP, "${prefixe}.+\.(zip|sql)$");
	    $liste = array();
	    foreach($sauvegardes as $sauvegarde) {
			$date_fichier = filemtime($sauvegarde);
			if ($temps > ($date_fichier + $jours_obso*3600*24)) {
				$liste[] = $sauvegarde;
				supprimer_fichier($sauvegarde);
			}
		}
    }

	// Pipeline
	pipeline('post_sauvegarde',
		array(
			'args' => array(
				'liste' => $liste,
				'auteur' => $auteur,
				'type' => 'saveauto_clean'
			),
			'data' => ''
		)
	);

    /**
     * notifications si necessaire
     */
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('saveauto_cleaner', '',
			array('liste' => $liste, 'auteur' => $auteur)
		);
	}

	return $liste;
}
?>