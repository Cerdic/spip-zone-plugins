<?php
/**
 * Gestion du formulaire de d'édition de cv
 *
 * @plugin     Emplois
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Emplois\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');

// nécessaire pour CVT Upload
function formulaires_editer_cv_fichiers(){
	$cv_pdf = lire_config('emplois/cvs/cv_pdf');
	if ( !test_espace_prive()  AND $cv_pdf == 'oui')
		return array('cv_pdf');
}

function formulaires_editer_cv_charger_dist($id_cv='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	// récupérer l'id_cv : important si formulaire dans bloc ajax
	$id_auteur = session_get('id_auteur');
	$id_cv = sql_getfetsel('id_cv', 'spip_cvs', 'id_auteur='.intval($id_auteur));
	is_null($id_cv) ? $id_cv = 'new' : $id_cv;

	$valeurs = formulaires_editer_objet_charger('cv', $id_cv, $id_rubrique, $lier_trad, $retour, $config_fonc, $row, $hidden);

	// espace public : renvoyer les infos sur l'auteur
	$valeurs['nom'] = session_get('nom');
	$valeurs['email'] = session_get('email');

	// faire en sorte que le formulaire soit editable ou non. Là c'est toujours editable
	// TODO : paramètre de config pour cela ?
	$valeurs['editable'] = true;

	return $valeurs;
}


function formulaires_editer_cv_verifier_dist($id_cv='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = array();

	// récupérer l'id_cv : important si formulaire dans bloc ajax
	$id_cv = _request('id_cv');

	$erreurs = formulaires_editer_objet_verifier('cv', $id_cv);

	// tester le type de fichier : on teste $_FILES et pas _request('_fichiers') car sinon, on le teste à chaque passage et pas au premier upload
	$cv_pdf = lire_config('emplois/cvs/cv_pdf');
	if (!test_espace_prive() AND $cv_pdf == 'oui') {
		if (!empty($_FILES['cv_pdf']['tmp_name']) AND $_FILES['cv_pdf']['type'] != 'application/pdf') {

			//unset le fichier qui a quand même été chargé
			if (isset($_FILES['cv_pdf']))
				unset($_FILES['cv_pdf']);
			
			// envoi erreur
			$erreurs['cv_pdf'] = 'Vous devez choisir un fichier au format PDF';
		}
	}
	return $erreurs;
}


function formulaires_editer_cv_traiter_dist($id_cv='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	
	// récupérer l'id_cv : important si formulaire dans bloc ajax
	$id_cv = _request('id_cv');
	
	$retours = formulaires_editer_objet_traiter('cv', $id_cv, $id_rubrique, $lier_trad, $retour, $config_fonc, $row, $hidden);

	// on va avoir besoin de l'id_cv
	$id_cv = $retours['id_cv'];

	// enregistrer l'id_auteur et son nom
	$id_auteur = session_get('id_auteur');
	$nom = session_get('nom');
	sql_updateq('spip_cvs', array('id_auteur' => $id_auteur, 'nom' => $nom), 'id_cv = ' . $id_cv);

	// l'upload de PDF a t'il été activé ?
	$cv_pdf = lire_config('emplois/cvs/cv_pdf');

	if (!test_espace_prive() AND $cv_pdf == 'oui') {
		$fichiers_uploade = _request('_fichiers');

		if (isset($fichiers_uploade['cv_pdf']) AND $fichiers_uploade['cv_pdf']) {

			// vérifier si le cv a déjà un PDF
			$cv_document = sql_getfetsel('id_document_cv',
			'spip_cvs a JOIN spip_documents d ON(a.id_document_cv = d.id_document)',
			'id_cv = ' . intval($id_cv));

			// test : soit un numéro du document à mettre à jour, soit 'new'
			$id_document    = $cv_document ? $cv_document : 'new';

			// ajouter le document et l'associer' au CV
			$ajouter_documents = charger_fonction('ajouter_documents', 'action');

			// $mode             = joindre_determiner_mode('auto', $id_cv, 'cv');
			$nouveaux_docs    = $ajouter_documents($id_document, array($fichiers_uploade['cv_pdf']), 'cv', $id_cv, 'document');

			$id_document_cree = $nouveaux_docs[0];
			if (!is_numeric($id_document_cree)) {
				return array('message_erreur' => _L('Erreur lors de l\'enregistrement du fichier'));
			}

			// mettre à jour l'id du document pdf dans le cv
			sql_updateq('spip_cvs', array('id_document_cv' => $id_document_cree), 'id_cv = ' . $id_cv);
		}
		   
		// renvoyer id_document et l'id_cv au cas où le formulaire est dans un bloc ajax 
		if (isset($id_document_cree)) {
			set_request('id_document_cv', $id_document_cree);
		}
		set_request('id_cv', $id_cv);

		/* traitements supplémentaires si formulaire public */
		if (!test_espace_prive()) {
			// forcer le statut à 'proposer'
			sql_updateq('spip_cvs', array('statut' => 'prop'), 'id_cv='.intval($id_cv));

			// surcharger le message de retour de validation
			$retours['message_ok'] = "Votre CV a bien été enregistré et nous vous en remercions.
					<br>Il sera publié dès que nos services l'auront validé.";
		}
	}

	return $retours;
}