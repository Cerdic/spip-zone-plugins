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

	if (version_compare($current_version,'1.0.1','<')) {
		include_spip('inc/liste_auteurs_automatiques');
		$liste_auteurs = liste_auteurs_automatiques();
		if ($liste_auteurs[0]['login']) {
			include_spip('action/editer_auteur');
			include_spip('inc/acces');
			foreach($liste_auteurs as $auteur) {
				$id_auteur = sql_fetsel("id_auteur", "spip_auteurs", "login='".$auteur['login']."'");
				if (!$id_auteur) {
					$id_auteur = insert_auteur();
					autoriser_exception('modifier', 'auteur', $id_auteur); // se donner temporairement le droit
					$err = instituer_auteur($id_auteur, $auteur);
					$err .= auteurs_set($id_auteur, $auteur);
					autoriser_exception('modifier', 'auteur', $id_auteur,false); // revenir a la normale
					if ($err) {
						spip_log("auteur_automatique: $err");
					} else {
						echo('<div>'.$auteur['login'].'</div>');
					}
				}
			}
		}
		include_spip('inc/meta');
		ecrire_meta($nom_meta_base_version,$current_version='1.0.1','non');
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
