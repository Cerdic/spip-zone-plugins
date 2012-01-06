<?php
/*
 * Plugin En Travaux
 * (c) 2006-2012 Arnaud Ventre, Cedric Morin
 * Distribue sous licence GPL
 *
 */


/**
 * Installation/maj base
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function entravaux_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();

	include_spip('inc/autoriser');
	// upgrade inconditionnel
	if (isset($GLOBALS['meta']['entravaux_id_auteur'])){
		entravaux_poser_verrou("accesferme");
		effacer_meta('entravaux_id_auteur');
	}

	// seul un webmestre peut activer les travaux sur le site
	// si c'est un autre admin qui active le plugin, il ne fait rien en base
	if (autoriser('travaux')) {
		$maj['create'] = array(
			array('entravaux_poser_verrou','accesferme'),
		);
		include_spip('base/upgrade');
		maj_plugin($nom_meta_base_version, $version_cible, $maj);
	}
	// sinon on ne fait *rien* (activation par un admin, ou upgrade silencieux apres import de base)
}

/**
 * Installation/maj base
 *
 * @param string $nom_meta_base_version
 */
function entravaux_vider_tables($nom_meta_base_version) {
	effacer_meta("entravaux_id_auteur");
	effacer_meta("entravaux_message");
	effacer_meta($nom_meta_base_version);
}


/**
 * Poser un verrou sous forme de fichier dans local/
 * pour ne pas qu'il saute si on importe une base
 * On loge dans le verrou l'id_auteur qui l'a pose a toute fin utile
 * On force la mise a jour de la meta
 * @param string $nom
 */
function entravaux_poser_verrou($nom){
	ecrire_fichier(_DIR_VAR.'entravaux_'.$nom.'.lock',"auteur:".$GLOBALS['visiteur_session']['id_auteur']);
	entravaux_check_verrou($nom, true);
}

/**
 * Lever un verrou sous forme de fichier dans local/
 * pour ne pas qu'il saute si on importe une base
 * On force la mise a jour de la meta
 * @param string $nom
 */
function entravaux_lever_verrou($nom){
	spip_unlink(_DIR_VAR.'entravaux_'.$nom.'.lock');
	entravaux_check_verrou($nom, true);
}

?>