<?php
/**
 * Plugin Campagnes publicitaires
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/*
 * Déclarer les champs du formulaire en utilisant l'API de Saisies
 */
function formulaires_editer_campagne_saisies_dist($id_campagne='new', $retour=''){
	$saisies = array(
		array(
			'saisie' => 'encarts',
			'options' => array(
				'nom' => 'id_encart',
				'label' => _T('campagne:champ_id_encart_label'),
				'obligatoire' => 'oui',
				'cacher_option_intro' => 'oui',
				'defaut' => _request('id_encart')
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('campagne:champ_titre_label'),
				'obligatoire' => 'oui'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'url',
				'label' => _T('campagne:champ_url_label'),
				'obligatoire' => 'oui'
			),
			'verifier' => array(
				'type' => 'url',
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'media',
				'label' => _T('campagne:champ_media_label'),
				'type' => 'file',
				'inserer_fin' => (
					include_spip('inc/filtres_images')
					and $fichier = sql_getfetsel(
						'fichier',
						'spip_documents as d left join spip_documents_liens as l on d.id_document=l.id_document',
						array('l.objet='.sql_quote('campagne'), 'l.id_objet='.intval($id_campagne))
					)
				) ? image_reduire(_DIR_IMG.$fichier, 300,300) : ''
			)
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'descriptif',
				'label' => _T('campagne:champ_descriptif_label'),
				'rows' => 4,
			),
			'verifier' => array(
				'type' => 'taille',
				'options' => array(
					'max' => 140
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'restrictions_publication',
				'label' => _T('campagne:champ_restrictions_publication_label'),
				'explication' => _T('campagne:champ_restrictions_publication_explication'),
			),
			'saisies' => array(
				array(
					'saisie' => 'date',
					'options' => array(
						'nom' => 'date_debut',
						'label' => _T('campagne:champ_date_debut_label'),
					),
				),
				array(
					'saisie' => 'date',
					'options' => array(
						'nom' => 'date_fin',
						'label' => _T('campagne:champ_date_fin_label'),
					),
				),
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'restrictions_affichage',
				'label' => _T('campagne:champ_restrictions_affichage_label'),
			),
			'saisies' => array(
				array(
					'saisie' => 'textarea',
					'options' => array(
						'nom' => 'contextes',
						'label' => _T('campagne:champ_contextes_label'),
						'explication' => _T('campagne:champ_contextes_explication'),
						'rows' => 5,
					),
				)
			)
		),
	);
	
	// Si on est admin, on peut lier explicitement la pub à un annonceur
	if (autoriser('configurer')){
		array_unshift($saisies,array(
			'saisie' => 'annonceurs',
			'options' => array(
				'nom' => 'id_annonceur',
				'label' => _T('campagne:champ_id_annonceur_label'),
				'defaut' => _request('id_annonceur')
			)
		));
	}
	
	return $saisies;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_campagne_identifier_dist($id_campagne='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_campagne)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_campagne_charger_dist($id_campagne='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('campagne',$id_campagne,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	unset($valeurs['id_campagne']);
	foreach (array('date_debut', 'date_fin') as $champ){
		if ($valeurs[$champ] == '0000-00-00'){ $valeurs[$champ] = ''; }
	}
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_campagne_verifier_dist($id_campagne='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	include_spip('action/ajouter_documents');
	include_spip('inc/campagnes');
	$erreurs = formulaires_editer_objet_verifier('campagne',$id_campagne, array('titre', 'url'));
	
	// Infos sur l'encart
	$encart = sql_fetsel('largeur, hauteur, type', 'spip_encarts', 'id_encart = '._request('id_encart'));
	
	// Le média n'est obligatoire que si c'est un encart de type image et qu'il n'y en a pas déjà un
	if (!$_FILES['media']['name']
		and $encart['type'] == 'image'
		and !sql_getfetsel(
			'id_document',
			'spip_documents_liens',
			array('objet='.sql_quote('campagne'), 'id_objet='.intval($id_campagne))
		)){
		$erreurs['media'] = _T('info_obligatoire');
	}
	
	// On cherche des infos sur le fichier mais seulement s'il y en a un
	if ($_FILES['media']['name']){
		if ($infos = fixer_fichier_upload($_FILES['media'])){
			// On vérifie d'abord que le fichier est un format accepté
			if (!in_array($infos['extension'], array('gif', 'jpg', 'png', 'swf'))){
				$erreurs['media'] = _T('campagne:erreur_upload_format');
			}
			else{
				// Si c'est bon on récupère les dimensions
				$tailles = renseigner_taille_dimension_image($infos['fichier'], $infos['extension']);
				$infos = array_merge($infos, $tailles);
			
				// Et on vérifie que les deux correspondent
				if ($infos['largeur'] != $encart['largeur'] or $infos['hauteur'] != $encart['hauteur']){
					$erreurs['media'] = _T('campagne:erreur_upload_taille', array('largeur'=>$encart['largeur'], 'hauteur'=>$encart['hauteur']));
				}
			}
		}
		else{
			$erreurs['media'] = _T('campagne:erreur_upload_campagne');
		}
	}
	// Dans tous les cas on supprime le document car il sera re-copié avec la fonction de SPIP
	if ($infos)
		spip_unlink($infos['fichier']);
	
	// S'il y a des dates, on vérifie le format et l'ordre
	$date_debut = campagnes_verifier_date_saisie('debut', $erreurs);
	$date_fin = campagnes_verifier_date_saisie('fin', $erreurs);
	if ($date_debut and $date_fin and $date_fin < $date_debut){
		$erreurs['restrictions_publication'] = _T('campagne:erreur_date_avant_apres');
	}
	// Soit aucune date soit les deux
	if (($date_debut and !$date_fin) or (!$date_debut and $date_fin)){
		$erreurs['restrictions_publication'] = _T('campagne:erreur_date_deux');
	}
	
	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_campagne_traiter_dist($id_campagne='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	include_spip('inc/campagnes');
	
	// On met les dates au format SQL ou on supprime si pas les deux
	$erreurs = array();
	$date_debut = campagnes_verifier_date_saisie('debut', $erreurs);
	$date_fin = campagnes_verifier_date_saisie('fin', $erreurs);
	if ($date_debut and $date_fin){
		$date_debut = date('Y-m-d', $date_debut);
		set_request('date_debut', $date_debut);
		$date_fin = date('Y-m-d', $date_fin);
		set_request('date_fin', $date_fin);
	}
	else{
		set_request('date_debut', '0000-00-00');
		set_request('date_fin', '0000-00-00');
	}
	
	// Si on a pas d'annonceur explicite, on en cherche un via l'auteur actuel
	if (!_request('id_annonceur')
		and include_spip('inc/session')
		and $id_auteur = session_get('id_auteur')
		and $id_annonceur = sql_getfetsel('id_annonceur', 'spip_annonceurs', 'id_auteur = '.$id_auteur)
	){
		set_request('id_annonceur', $id_annonceur);
	}
	
	// On enregistre dans l'objet éditorial
	$retours = formulaires_editer_objet_traiter('campagne',$id_campagne,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	
	// On ne s'occupe du fichier que s'il y en a un
	if ($retours['id_campagne'] and $_FILES['media']['name']){
		// S'il existe déjà un document pour cette pub, alors on modifie juste le fichier
		$id_document = sql_getfetsel('id_document', 'spip_documents_liens', 'objet = '.sql_quote('campagne').' and id_objet = '.$retours['id_campagne']);
	
		// On ajoute le document à la pub
		include_spip('action/ajouter_documents');
		$ajouter_doc = charger_fonction('ajouter_un_document', 'action/');
		$ajouter_doc($id_document > 0 ? $id_document : false, $_FILES['media'], 'campagne', $retours['id_campagne'], 'auto');
	}
	
	// On vérifie si on doit aussi changer automatiquement le statut
	if ($retours['id_campagne'] and $date_debut and $date_fin){
		include_spip('action/editer_objet');
		$jourdhui = date('Y-m-d');
		
		if ($jourdhui >= $date_debut and $jourdhui <= $date_fin){
			objet_instituer('campagne', $retours['id_campagne'], array('statut' => 'publie'));
		}
		elseif ($jourdhui < $date_debut){
			objet_instituer('campagne', $retours['id_campagne'], array('statut' => 'prepa'));
		}
		elseif ($jourdhui > $date_fin){
			objet_instituer('campagne', $retours['id_campagne'], array('statut' => 'obsolete'));
		}
	}
	
	return $retours;
}


?>
