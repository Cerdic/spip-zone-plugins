<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * La fonction de suppression des fichiers obsolètes
 * Retourne un array() $liste des fichiers supprimés
 *
 * @param array $options
 * @return array $liste
 */
function inc_mes_fichiers_cleaner_dist($options=array()){
	$temps = time();

	include_spip('inc/config');
	$jours_obso = intval(lire_config('mes_fichiers/duree_sauvegarde', 15));
	$auteur = $options['auteur'] ? $options['auteur'] : $GLOBALS['visiteur_session']['id_auteur'];
	if ($auteur == 'cron')
		$auteur = 'SPIP';

	if($jours_obso > 0){
		$prefixe = lire_config('mes_fichiers/prefixe','mf2').'_';
	    $sauvegardes = preg_files(_DIR_MES_FICHIERS,"$prefixe.+[.](zip)$");
	    $liste = array();
	    foreach($sauvegardes as $sauvegarde){
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
				'type' => 'mes_fichiers_cleaner'
			),
			'data' => ''
		)
	);

    /**
     * notifications si necessaire
     */
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('mes_fichiers_cleaner', '',
			array('liste' => $liste, 'auteur' => $auteur)
		);
	}

	return $liste;
}
?>