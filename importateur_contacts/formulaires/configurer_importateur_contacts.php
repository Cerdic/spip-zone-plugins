<?php
/**
 * Plugin Importateur de contacts
 * 
 * Formulaire de configuration du plugin
 * 
 * @package SPIP\Importateur_Contacts\Formulaires
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_importateur_contacts_saisies_dist(){
	include_spip('inc/importateur_contacts');
	
	$saisies = array();
	
	$fournisseurs = importateur_contacts_lister_fournisseurs();
	if (empty($fournisseurs)){
		$saisies[] = array(
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'rien',
				'texte' => _T('importateurcontacts:erreur_aucun_fournisseur')
			)
		);
	}
	else{
		foreach ($fournisseurs as $nom_fournisseur => $fournisseur){
			foreach ($fournisseur['moteurs'] as &$moteur)
				$moteur = _T('importateurcontacts:configurer_fournisseur_active_avec_moteur', array('moteur'=>$moteur));
			
			$saisies[] = array(
				'saisie' => 'selection',
				'options' => array(
					'nom' => "fournisseurs_choisis[$nom_fournisseur]",
					'label' => $fournisseur['titre'],
					'datas' => $fournisseur['moteurs'],
					'defaut' => $fournisseur['moteur_choisi'],
					'option_intro' => _T('importateurcontacts:configurer_fournisseur_desactive')
				)
			);
		}
	}
	
	return $saisies;
}

?>
