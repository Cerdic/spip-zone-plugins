<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Tuto-commerce
 *
 * @plugin     Tuto-commerce
 * @copyright  2015
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Tuto-commerce\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Tuto-commerce.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function tutocommerce_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_produits_demos')),
		array('peupler_base_produits_demos')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Tuto-commerce.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function tutocommerce_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_produits_demos");

	effacer_meta($nom_meta_base_version);
}


/**
 * Fonction pour peupler la table spip_produits_demos avec 3 produits factices
 *
 * @return void
 */
function peupler_base_produits_demos() {

	// données des 3 produits à insérer
	$set = array(
		array('titre'=>'Cube',    'prix'=>'12.5'),
		array('titre'=>'Sphere',  'prix'=>'8'),
		array('titre'=>'Cylindre','prix'=>'23.99')
	);

	// insertion des 3 produits
	sql_insertq_multi('spip_produits_demos', $set);

	// ajout des logos d'après les fichiers présents dans demo/images (produitdemo_sphere.png etc.)
	foreach ($set as $k=>$produit) {

		$titre = $produit['titre'];
		$id_produitdemo = sql_getfetsel('id_produitdemo',table_objet_sql('produitdemo'),"titre=".sql_quote($titre));

		// copie temporaire de l'image dans /tmp
		$fichier = find_in_path('demo/images/produitdemo_'.strtolower($titre).'.png');
		$fichier_tmp = _DIR_TMP.pathinfo($image, PATHINFO_FILENAME).'_tmp.png';
		if (!copy($fichier,$fichier_tmp))
			return;
		$name = pathinfo($fichier_tmp, PATHINFO_BASENAME);
		$type = 'image/png';
		$size = filesize($fichier_tmp);

		// variable de téléversement
		$_FILES['logo_on'] = array(
			'name' => $name,
			'tmp_name' => $fichier_tmp,
			'type' => $type,
			'size' => $size,
			'error' => ''
		);

		// traitements de « editer_logo »
		$traiter_logo = charger_fonction('traiter','formulaires/editer_logo');
		$res = $traiter_logo('produitdemo',$id_produitdemo);

		// suppression du fichier temporaire
		include_spip('inc/flock');
		supprimer_fichier(find_in_path($fichier_tmp));

	}

}

?>
