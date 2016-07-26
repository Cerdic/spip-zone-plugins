<?php
/**
 * Gestion du formulaire de d'édition de cv
 *
 * @plugin     Emplois
 * @copyright  2016
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Emplois\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');


function formulaires_editer_cv_identifier_dist($id_cv='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// dans l'espace public, si connecté, on test si CV déjà déposé
	if ( !test_espace_prive()) {
		$id_auteur = session_get('id_auteur');
	}
	
	return serialize(array(intval($id_cv)));
}

function formulaires_editer_cv_fichiers(){
	if ( !test_espace_prive())
		return array('cv_pdf');
}

function formulaires_editer_cv_charger_dist($id_cv='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('cv', $id_cv, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

	// faire en sorte que le formulaire soit editable ou non. Là c'est toujours editable
	// TODO : paramètre de config pour cela ?
	$valeurs['editable'] = true;
	
	return $valeurs;
}


function formulaires_editer_cv_verifier_dist($id_cv='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('cv', $id_cv);

	// tester le type de fichier : on teste $_FILES et pas _request('_fichiers') car sinon, on le teste à chaque passage et pas au premier upload
	// note : $_FILES['seul']['tmp_name'] = si vide, si nulle, ou false (c'est la cas le plus propre)
	if (!test_espace_prive()) {
		if ($_FILES['cv_pdf']['tmp_name'] AND $_FILES['cv_pdf']['type'] != 'application/pdf') {

			//unset le fichier qui a quand même été chargé
			if (isset($_FILES['cv_pdf']))
				unset($_FILES['cv_pdf']);
			// envoi erreur
			$erreurs['cv_pdf'] = 'Vous devez choisir un fichier au format PDF';
		}
	}
	

	return $erreurs;
}


function formulaires_editer_cv_traiter_dist($id_cv='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$nom = session_get('nom');
	set_request('nom', $nom);
	$retours = formulaires_editer_objet_traiter('cv', $id_cv, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

	// on va avoir besoin de l'id_cv 'new' ou id numerique
	$id_cv = $retours['id_cv'];

	if (!test_espace_prive()) {
		$fichiers_uploade = _request('_fichiers');

		if ($fichiers_uploade['cv_pdf']) {

		   // vérifier si le cv a déjà un PDF
		   $cv_document = sql_getfetsel('id_document_cv',
		      'spip_cvs a JOIN spip_documents d ON(a.id_document_cv = d.id_document)',
		      'id_cv = ' . intval($id_cv));

		   // test : soit un numéro du document à mettre à jour, soit 'new'
		   $id_document    = $cv_document ? $cv_document : 'new';

		   // ajouter le document et l'associer' au CV
		   $ajouter_documents = charger_fonction('ajouter_documents', 'action');
		   // utile pour déterminer le mode : pas utile ici -> include_spip('formulaires/joindre_document');

		  	// $mode             = joindre_determiner_mode('auto', $id_cv, 'cv');
		   $nouveaux_docs    = $ajouter_documents($id_document, array($fichiers_uploade['cv_pdf']), 'cv', $id_cv, 'document');
		   // debug : d($nouveaux_docs);

		   $id_document_cree = $nouveaux_docs[0];
		   if (!is_numeric($id_document_cree)) {
		      return array('message_erreur' => _L('Erreur lors de l\'enregistrement du fichier'));
		   }

		   // mettre à jour l'id du document pdf dans le cv
		   sql_updateq('spip_cvs', array('id_document_cv' => $id_document_cree), 'id_cv = ' . $id_cv);

		   // attention : prendre en compte la notion de confidentialité
		   // mettre à jour le titre du document
		   // sql_updateq('spip_documents', array('titre' => _L('Affiche') . ' "' . _request('titre') . '"'), 'id_document = ' . $id_document_cree);

		   $retours['message_ok'] = "Votre CV a bien été enregistrée";
		}
	}

	return $retours;
}