<?php
/**
 * Gestion du formulaire de d'édition de offre
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


/**
 * Identifier l'input file pour CVT Upload
 *
 * @return array
 *     valeur de l'attribut 'name' de l'input 'file'
 */
function formulaires_editer_offre_fichiers() {
	$offre_pdf = lire_config('emplois/offres/offre_pdf');
	if ( !test_espace_prive() AND $offre_pdf == 'oui' )
		return array('offre_pdf');
}

/**
 * Chargement du formulaire d'édition de offre
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_offre
 *     Identifiant du offre. 'new' pour un nouveau offre.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un offre source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du offre, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_offre_charger_dist($id_offre='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	if (is_numeric(_request('id_offre'))) {
		set_request('id_offre', _request('id_offre'));
	}
	$valeurs = formulaires_editer_objet_charger('offre', $id_offre, $id_rubrique, $lier_trad, $retour, $config_fonc, $row, $hidden);

	// faire en sorte que le formulaire soit editable ou non. Là c'est toujours editable
	// TODO : paramètre de config pour cela ?
	$valeurs['editable'] = true;
	
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de offre
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_offre
 *     Identifiant du offre. 'new' pour un nouveau offre.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un offre source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du offre, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_offre_verifier_dist($id_offre='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = array();

	$verifier = charger_fonction('verifier', 'inc');

	// vérifier la date de fin
	foreach (array('date_fin') AS $champ) {
		$normaliser = null;
		if ($erreur = $verifier(_request($champ), 'date', array('normaliser'=>'datetime'), $normaliser)) {
			$erreurs[$champ] = $erreur;
		// si une valeur de normalisation a ete transmis, la prendre.
		} elseif (!is_null($normaliser)) {
			set_request($champ, $normaliser);
		// si pas de normalisation ET pas de date soumise, il ne faut pas tenter d'enregistrer ''
		} else {
			set_request($champ, null);
		}
	}
	// TODO chercher les autres champs obligatoires
	$champs_obligatoires = array('titre');
	$erreurs += formulaires_editer_objet_verifier('offre', $id_offre, $champs_obligatoires);

	//verifier validité de l'email
	$email = _request('email');
	if ($email and !email_valide($email)) {
		$erreurs['email'] = "email non valide";
	}

	// le formulaire d'upload de fichier n'a lieu que dans l'espace publique
	if (!test_espace_prive()) {
		// Honeypot
		if (strlen(_request('nobot')) > 0) {
			$erreurs['message_erreur'] = _T('pass_rien_a_faire_ici');
		}

		// Gestion de l'upload de fichier
		// tester le type de fichier : on teste $_FILES et pas _request('_fichiers') car sinon, on le teste à chaque passage et pas au premier upload
		$offre_pdf = lire_config('emplois/offres/offre_pdf');
		if ($offre_pdf == 'oui') {
			if (!empty($_FILES['offre_pdf']['tmp_name']) AND $_FILES['offre_pdf']['type'] != 'application/pdf') {
				//unset le fichier qui a quand même été chargé
				if (isset($_FILES['offre_pdf']))
					unset($_FILES['offre_pdf']);
				// envoi erreur
				$erreurs['offre_pdf'] = 'Vous devez choisir un fichier au format PDF';
				$erreurs['message_erreur'] .= "\n Vous devez choisir un fichier au format PDF";
			}
		}
	}

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de offre
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_offre
 *     Identifiant du offre. 'new' pour un nouveau offre.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un offre source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du offre, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_offre_traiter_dist($id_offre='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	if (is_numeric(_request('id_offre'))) {
		$id_offre = _request("id_offre");
	}

	$retours = formulaires_editer_objet_traiter('offre', $id_offre, $id_rubrique, $lier_trad, $retour, $config_fonc, $row, $hidden);

	// on va avoir besoin de l'id_offre 'new' ou id numerique
	$id_offre = $retours['id_offre'];

	// l'upload de PDF a t'il été activé ?
	$offre_pdf = lire_config('emplois/offres/offre_pdf');

	if (!test_espace_prive() and $offre_pdf == 'oui') {

		$fichiers_uploade = _request('_fichiers');

		if (isset($fichiers_uploade['offre_pdf']) AND $fichiers_uploade['offre_pdf']) {

		   // vérifier si le l'offre d'emploi a déjà un PDF
		   $offre_document = sql_getfetsel('id_document_offre',
		      'spip_offres a JOIN spip_documents d ON(a.id_document_offre = d.id_document)',
		      'id_offre = ' . intval($id_offre));
		   
		   // test : soit un numéro du document à mettre à jour, soit 'new'
		   $id_document = $offre_document ? $offre_document : 'new';
		   
		   // ajouter le document et l'associer' à l'offre d'emploi
		   $ajouter_documents = charger_fonction('ajouter_documents', 'action');
		   // utile pour déterminer le mode : pas utile ici -> include_spip('formulaires/joindre_document');

		  	// $mode             = joindre_determiner_mode('auto', $id_offre, 'offre');
		   $nouveaux_docs    = $ajouter_documents($id_document, array($fichiers_uploade['offre_pdf']), 'offre', $id_offre, 'document');

		   $id_document_cree = $nouveaux_docs[0];
		   if (!is_numeric($id_document_cree)) {
		      return array('message_erreur' => _L('Erreur lors de l\'enregistrement du fichier'));
		   }

		   // mettre à jour l'id du document pdf dans l'offre d'emploi
		   sql_updateq('spip_offres', array('id_document_offre' => $id_document_cree), 'id_offre = ' . $id_offre);

		   // attention : prendre en compte la notion de confidentialité
		   // mettre à jour le titre du document
		   // sql_updateq('spip_documents', array('titre' => _L('Affiche') . ' "' . _request('titre') . '"'), 'id_document = ' . $id_document_cree);

		}
		// renvoyer id_document au cas où le formulaire est dans un bloc ajax 
		if (isset($id_document_cree)) {
			set_request('id_document_offre', $id_document_cree);
		}
		
	}

	// Important : passer id_offre dans l'environnement au cas ou le formulaire est dans un bloc ajax
	set_request('id_offre', $id_offre);

	/* traitements supplémentaires si formulaire public */
	if (!test_espace_prive()) {
		// forcer le statut à 'proposer'
		sql_updateq('spip_offres', array('statut' => 'prop'), 'id_offre='.intval($id_offre));

		// surcharger le message de retour de validation
		$retours['message_ok'] = "Votre offre d'emploi vient d'être enregistrée et nous vous en remercions.
		<br>Vous pouvez vérifier / modifier cette offre tant que vous restez sur cette page.
		<br>Ensuite, elle sera publiée dès que nos services l'auront validée.";
	}
	
	return $retours;
}
