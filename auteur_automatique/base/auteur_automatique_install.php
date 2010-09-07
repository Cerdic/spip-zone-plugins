<?php
/*
* Configuration de SPIP pour auteur_automatique
* Realisation : RealET : real3t@gmail.com
* Attention, fichier en UTF-8 sans BOM
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Fonction d'installation, mise a jour de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function auteur_automatique_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;

	if (version_compare($current_version,'1.0','<')) {
		if (defined("_AA_OPENID")) {
			include_spip('action/editer_auteur');
			$id_auteur = insert_auteur();
			include_spip('inc/acces');
			$pass = creer_pass_aleatoire(16, $id_auteur);
			$c = array('statut'=>_AA_STATUT,
					'webmestre'=>_AA_webmestre,
					'login'=>_AA_LOGIN,
					'pass'=>$pass, // ne sert à rien pour le login, mais est present en cas de desactivation d'OpenId
					'openid'=>_AA_OPENID,
					'bio'=>_AA_BIO,
					'nom_site'=>_AA_NOM_SITE,
					'url_site'=>_AA_URL_SITE,
					'nom'=>_AA_NOM,
					'email'=>_AA_EMAIL
					);
			$err = instituer_auteur($id_auteur, $c);
			$err .= auteurs_set($id_auteur, $c);
			if ($err) spip_log("auteur_automatique: $err");
		}
		include_spip('inc/meta');
		ecrire_meta($nom_meta_base_version,$current_version='1.0','non');
	}
}

/*
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function auteur_automatique_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	effacer_meta($nom_meta_base_version);
}
?>
