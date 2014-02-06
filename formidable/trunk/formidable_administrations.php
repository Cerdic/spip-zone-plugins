<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin
 *
 * @package SPIP\Formidable\Installation
**/

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Installation/maj des tables de formidable...
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function formidable_upgrade($nom_meta_base_version, $version_cible){
	// Création des tables
	include_spip('base/create');
	include_spip('base/abstract_sql');

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array(
			'spip_formulaires',
			'spip_formulaires_reponses',
			'spip_formulaires_reponses_champs',
			'spip_formulaires_liens')),
		array('formidable_importer_forms'),
		array('formidable_importer_forms_donnees'),
	);

	// Ajout du choix de ce qu'on affiche à la fin des traitements
	$maj['0.4.0'] = array(array('maj_tables',array('spip_formulaires')));
	// Ajout d'une URL de redirection
	$maj['0.5.0'] = array(array('maj_tables',array('spip_formulaires')));
	// Modif du type du message de retour pour pouvoir mettre plus de chose
	$maj['0.5.1'] = array(array('sql_alter','TABLE spip_formulaires CHANGE message_retour message_retour text NOT NULL default ""'));
	// Passer le champ saisies en longtext pour permettre d'y stocker des formulaires longs
	$maj['0.5.2'] = array(array('sql_alter','TABLE spip_formulaires CHANGE saisies saisies longtext NOT NULL default ""'));
	// Ajouter un champ date de création
	$maj['0.5.3'] = array(array('sql_alter','TABLE spip_formulaires ADD date_crea datetime NOT NULL DEFAULT "0000-00-00 00:00:00"'));
	// Renommer la date de création (pas d'abbréviations dans les noms)
	$maj['0.5.5'] = array(array('sql_alter','TABLE spip_formulaires CHANGE date_crea date_creation datetime NOT NULL DEFAULT "0000-00-00 00:00:00"'));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Désinstallation/suppression des tables de formidable
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function formidable_vider_tables($nom_meta_base_version){

	include_spip('inc/meta');
	include_spip('base/abstract_sql');

	// On efface les tables du plugin
	sql_drop_table('spip_formulaires');
	sql_drop_table('spip_formulaires_reponses');
	sql_drop_table('spip_formulaires_reponses_champs');
	sql_drop_table('spip_formulaires_liens');

	// on efface les champs d'import de f&t si il y a lieu
	$trouver_table = charger_fonction("trouver_table","base");
	if ($trouver_table('spip_forms')){
		sql_alter("TABLE spip_forms DROP id_formulaire");
	}
	if ($trouver_table('spip_forms_donnees')){
		sql_alter("TABLE spip_forms_donnees DROP id_formulaires_reponse");
	}


	// On efface la version entregistrée
	effacer_meta($nom_meta_base_version);
}

/**
 * Importer les formulaires de f&t
 */
function formidable_importer_forms(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($trouver_table('spip_forms')){
		sql_alter("TABLE spip_forms ADD id_formulaire bigint(21) NOT NULL DEFAULT 0");

		include_spip("echanger/formulaire/forms");

		$forms = sql_allfetsel("*","spip_forms",'id_formulaire=0 AND type_form='.sql_quote('')." OR type_form=".sql_quote('sondage'),'','id_form');
		foreach($forms as $form){
			$formulaire = array();
			// configurer le formulaire (titre etc)
			forms_configure_formulaire($form,$formulaire);

			$fields = sql_allfetsel("*","spip_forms_champs","id_form=".intval($form['id_form']),"","rang");
			foreach($fields as $field){
				$choix = sql_allfetsel("*","spip_forms_champs_choix","id_form=".intval($form['id_form'])." AND champ=".sql_quote($field['champ']),'','rang');
				if (count($choix))
					$field['choix'] = $choix;

				if ($saisie = forms_champ_vers_saisie($field))
					$formulaire['saisies'][] = $saisie;
			}

			// les traitements
			forms_configure_traitement_formulaire($form,$formulaire);
			$id_formulaire = forms_importe_en_base($formulaire);
			spip_log("Import spip_forms #".$form['id_form']." en spip_formulaires #$id_formulaire","maj"._LOG_INFO_IMPORTANTE);

			sql_update('spip_forms',array('id_formulaire'=>$id_formulaire),'id_form='.intval($form['id_form']));

			if (time()>_TIME_OUT)
				return;
		}

	}

	include_spip("inc/drapeau_edition");
	debloquer_tous($GLOBALS['visiteur_session']['id_auteur']);
}

function formidable_importer_forms_donnees(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($trouver_table('spip_forms')){
		sql_alter("TABLE spip_forms_donnees ADD id_formulaires_reponse bigint(21) NOT NULL DEFAULT 0");

		// 2 champs de plus pour ne pas perdre des donnees
		sql_alter("TABLE spip_formulaires_reponses ADD url varchar(255) NOT NULL default ''");
		sql_alter("TABLE spip_formulaires_reponses ADD confirmation varchar(10) NOT NULL default ''");

		// table de correspondance id_form=>id_formulaire
		$rows = sql_allfetsel("id_form,id_formulaire","spip_forms","id_formulaire>0");
		$trans = array();
		foreach($rows as $row)
			$trans[$row['id_form']] = $row['id_formulaire'];

		$rows = sql_allfetsel("*","spip_forms_donnees",sql_in('id_form',array_keys($trans))." AND id_formulaires_reponse=0",'','id_donnee','0,100');
		do {

			foreach($rows as $row){

				#var_dump($row);
				$reponse = array(
					"id_formulaire" => $trans[$row['id_form']],
					"date" => $row["date"],
					"ip" => $row["ip"],
					"id_auteur" => $row["id_auteur"],
					"cookie" => $row["cookie"],
					"statut" => $row["statut"],
					"url" => $row["url"],
					"confirmation" => $row["confirmation"],
				);

				#var_dump($reponse);
				$id_formulaires_reponse = sql_insertq("spip_formulaires_reponses",$reponse);
				#var_dump($id_formulaires_reponse);
				if ($id_formulaires_reponse){
					$donnees = sql_allfetsel("$id_formulaires_reponse as id_formulaires_reponse,champ as nom,valeur","spip_forms_donnees_champs","id_donnee=".intval($row['id_donnee']));
					sql_insertq_multi("spip_formulaires_reponses_champs",$donnees);
					// et on marque la donnee pour ne pas la rejouer
					sql_update("spip_forms_donnees",array("id_formulaires_reponse"=>$id_formulaires_reponse),"id_donnee=".intval($row['id_donnee']));
				}
				if (time()>_TIME_OUT)
					return;
			}

			if (time()>_TIME_OUT)
				return;

		} while ($rows = sql_allfetsel("*","spip_forms_donnees",sql_in('id_form',array_keys($trans))." AND id_formulaires_reponse=0",'','id_donnee','0,100'));

	}

}
