<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function aeres_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		
		if (version_compare($version_actuelle,'0.0','=')) {
			// Mise à jour des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			//creer_base();
			maj_tables('spip_tickets'); 
			// Ajout types documents manquants
			$types_docs = array();
			$types_docs[] = array(
				'extension' => 'ris',
				'titre' => 'RIS',
				'mime_type' => 'application/x-research-info-systems',
				'inclus' => 'non',
				'upload' => 'oui',
				'media' => 'file'
			);
			$types_docs[] = array(
				'extension' => 'bib',
				'titre' => 'BibTeX',
				'mime_type' => 'application/x-bibtex',
				'inclus' => 'non',
				'upload' => 'oui',
				'media' => 'file'
			);
			$types_docs[] = array(
				'extension' => 'rdf',
				'titre' => 'RDF',
				'mime_type' => 'application/rdf+xml',
				'inclus' => 'non',
				'upload' => 'oui',
				'media' => 'file'
			);
			sql_replace_multi('spip_types_documents',$types_docs); //sql_replace au cas où ces extensions sont déjà déclarées (pas de doublons)
		}
		
		ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
	}
}

// Désinstallation
function aeres_vider_tables($nom_meta_version_base){
	// On efface la version enregistrée
	effacer_meta($nom_meta_version_base);
	
	// On efface la configuration
	effacer_meta('aeres');
}

?>
