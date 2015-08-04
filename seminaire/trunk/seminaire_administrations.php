<?php
/**
 * Plugin Séminaires
 * Licence GNU/GPL
 * 
 * @package SPIP\Seminaires\Administration
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'installation du plugin et de mise à jour.
 * 
 * On crée deux groupes de mots, un utilisé sur les évènements, un autres sur les articles séminaires
 * On ajoute trois champs à la table spip_evenements
 * 
 * @param string $nom_meta_base_version
 * 	Le nom de la meta d'installation
 * @param float $version_cible
 * 	Le numero de version de la base
 */
function seminaire_upgrade($nom_meta_base_version, $version_cible) {

	include_spip('inc/cextras');
	include_spip('base/seminaire');
	include_spip('inc/meta');

	cextras_api_upgrade(seminaire_declarer_champs_extras(), $maj['create']);

	$maj = array();

	$maj['create']= array(
						array('maj_tables',array('spip_evenements')),
						array('seminaire_pre_configuration',array()),
						array('seminaire_creation_groupes',array())
					);
	/**
	 * Copie de abstract vers descriptif
	 * on change name en attendee
	 */
	$maj['1.0.1'] = array(
						array('sql_update','spip_evenements', array('descriptif'=>'abstract')),
						array('sql_alter',"TABLE spip_evenements DROP abstract"),
						array('sql_alter',"TABLE spip_evenements ADD attendee text NOT NULL"),
						array('sql_update',"spip_evenements", array('attendee'=>'name')),
						array('sql_alter',"TABLE spip_evenements DROP name")
					);

	$maj['1.0.2'] = array(
						array('sql_alter',"TABLE spip_evenements ADD id_mot integer NOT NULL"),
					);
	$maj['1.0.3'] = array(
						array('sql_alter',"TABLE spip_evenements DROP id_mot"),
					);
	
	cextras_api_upgrade(seminaire_declarer_champs_extras(), $maj['1.1.0']);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function seminaire_pre_configuration(){
	include_spip('inc/config');
	/**
	 * Activer les mots clés et leur configuration avancée s'ils ne le sont pas déjà
	 */
	ecrire_config("articles_mots", "oui");
	ecrire_config("config_precise_groupes", "oui");
	/**
	 * Ajouter la prise en compte des documents sur les évènements
	 */
	$documents_objets = explode(',',lire_config('documents_objets'));
	if(!in_array('spip_evenements', $documents_objets)){
		$documents_objets[] = 'spip_evenements';
		ecrire_config('documents_objets',implode(',',$documents_objets));
	}
	
	/**
	 * Activer les révisions sur les événements
	 */
	$versions = lire_config('objets_versions');
	if(!in_array('spip_evenements',$versions)){
		$versions[] = 'spip_evenements';
		ecrire_config('objets_versions',$versions);
	}
}

function seminaire_creation_groupes(){

	include_spip('action/editer_groupe_mots');
	include_spip('action/editer_mot');
	include_spip('inc/config');

	$conf_seminaire = lire_config('seminaire',array());
	
	/**
	 * Creer le groupe de mots clés Type pour les types d'événements
	 */
	if (!($id_groupe = lire_config('seminaire/groupe_mot_type'))){
		$id_groupe_type = groupe_mots_inserer('evenements');
		if(is_numeric($id_groupe_type)){
			$infos_groupe_type = array('titre'=>'Type de séminaire', 'descriptif'=>_T('seminaire:mots_cles_techniques_kitcnrs'),'unseul' => 'oui', 'minirezo'=>'oui','comite'=>'oui');
			$modif_groupe_type = groupe_mots_modifier($id_groupe_type, $infos_groupe_type);
			$conf_seminaire['groupe_mot_type'] = $id_groupe_type;
		}else
			die((_T('seminaire:erreur_install_groupe_technique')));
		
		$types_statuts = array('Séminaire','Groupe de travail','Événement important');
		foreach ($types_statuts as $type) {
			$id_mot = mot_inserer($id_groupe_type);
			if(is_numeric($id_mot)){
				$modif_mot = array('titre'=>$type);
				mot_modifier($id_mot,$modif_mot);
			}
		}
	}

	/** 
	 * Création du groupe de mots clés Catégorie 
	 */
	if (!($id_groupe = lire_config('seminaire/groupe_mot_categorie'))){
		$id_groupe_categorie = groupe_mots_inserer('articles');
		if(is_numeric($id_groupe_categorie)){
			$infos_groupe_categorie = array('titre'=>'Catégorie de séminaire', 'descriptif'=>_T('seminaire:mots_cles_categories'), 'unseul' => 'oui','minirezo'=>'oui','comite'=>'oui');
			$modif_groupe_categorie = groupe_mots_modifier($id_groupe_categorie, $infos_groupe_categorie);
			$conf_seminaire['groupe_mot_categorie'] = $id_groupe_categorie;
		}
	}
	
	ecrire_config('seminaire',$conf_seminaire);
}
/**
 * Fonction de désinstallation du plugin.
 */
function seminaire_vider_tables($nom_meta_base_version) {
	/* 
	* On récupère l'identifiant du groupe de mot utilisé pour typer 
	* les séminaires, puis on identifie les mots qui y sont liés, ensuite
	* on nettoie les tables de liaison événements/mots et on supprime ces 
	* et le groupe
	*/
	$id_groupe_mot_type = lire_config('seminaire/groupe_mot_type');
	$tous_les_types = sql_allfetsel('id_mot','spip_mots','id_groupe='.intval($id_groupe_mot_type));
		foreach ($tous_les_types as $l) {
			sql_delete("spip_mots_liens","id_mot=".$l);
			sql_delete("spip_mots","id_mot=".$l);
		};
	sql_delete("spip_groupes_mots","id_groupe=".intval($id_groupe_mot_type));

	/* 
	* On récupère l'identifiant du groupe de mot utilisé pour typer 
	* les articles de séminaires, puis on identifie les mots qui y sont liés, ensuite
	* on nettoie les tables de liaison événements/mots et on supprime ces 
	* et le groupe
	*/
	$id_groupe_mot_categorie = lire_config('seminaire/groupe_mot_categorie');
	$tous_les_types = sql_allfetsel('id_mot','spip_mots','id_groupe='.intval($id_groupe_mot_categorie));
		foreach ($tous_les_types as $l) {
			sql_delete("spip_mots_liens","id_mot=".$l);
			sql_delete("spip_mots","id_mot=".$l);
		};
	sql_delete("spip_groupes_mots","id_groupe=".intval($id_groupe_mot_categorie));

	/*
	* On supprime tous les articles qui son marqués seminaire
	*/
	sql_delete("spip_articles","seminaire='on'");
	/* 
	* Tant qu'à faire, on supprime la colonne seminaire dans la table articles
	* et ceux de la table evenements
	*/

	sql_alter("TABLE spip_articles DROP COLUMN seminaire");
	sql_alter("TABLE spip_evenements DROP COLUMN attendee");
	sql_alter("TABLE spip_evenements DROP COLUMN origin");
	sql_alter("TABLE spip_evenements DROP COLUMN notes");


	effacer_config("seminaire");
	effacer_meta($nom_meta_base_version);
}

?>