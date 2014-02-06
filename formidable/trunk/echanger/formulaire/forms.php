<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Importation d'un formulaire forms&table

function echanger_formulaire_forms_importer_dist($fichier){
	include_spip('inc/xml');
	$arbre = spip_xml_load($fichier, false);
	
	if ($arbre and is_array($arbre) and isset($arbre['forms'])){
		foreach($arbre['forms'] as $forms){
			foreach ($forms['form'] as $form){
				$formulaire = array();

				$form_source = array(
					'id_form' => intval(trim(spip_xml_aplatit($form['id_form']))),
					'titre' => trim(spip_xml_aplatit($form['titre'])),
					'descriptif' => trim(spip_xml_aplatit($form['descriptif'])),
					'texte' => trim(spip_xml_aplatit($form['texte'])),
					'email' => unserialize(trim(spip_xml_aplatit($form['email']))),
					'moderation' => trim(spip_xml_aplatit($form['moderation'])),
					'modifiable' => trim(spip_xml_aplatit($form['modifiable'])),
					'multiple' => trim(spip_xml_aplatit($form['multiple'])),
					'champconfirm' => trim(spip_xml_aplatit($form['champconfirm'])),
				);

				// configurer le formulaire (titre etc)
				forms_configure_formulaire($form_source,$formulaire);

				// ajouter les champs de saisies
				foreach($form['fields'] as $fields){
					foreach($fields['field'] as $field){
						$champ = array(
							'champ'=>trim(spip_xml_aplatit($field['champ'])),
							'titre'=>trim(spip_xml_aplatit($field['titre'])),
							'type'=>trim(spip_xml_aplatit($field['type'])),
							'obligatoire'=>trim(spip_xml_aplatit($field['obligatoire'])),
							'taille'=>trim(spip_xml_aplatit($field['taille'])),
							'extra_info'=>trim(spip_xml_aplatit($field['extra_info'])),
							'aide'=>trim(spip_xml_aplatit($field['aide'])),
							'saisie'=>trim(spip_xml_aplatit($field['saisie'])),
						);

						// Les choix pour les types select et multiple
						if(isset($field['les_choix']) and is_array($field['les_choix'])){
							$champ['choix'] = array();
							foreach($field['les_choix'] as $les_choix){
								foreach($les_choix['un_choix'] as $un_choix){
									$champ['choix'][] = array(
										'choix'=>trim(spip_xml_aplatit($un_choix['choix'])),
										'titre'=>trim(spip_xml_aplatit($un_choix['titre'])),
									);
								}
							}
						}

						if ($saisie = forms_champ_vers_saisie($champ))
							$formulaire['saisies'][] = $saisie;
					}
				}
				
				// les traitements
				forms_configure_traitement_formulaire($form_source,$formulaire);
				$id_formulaire = forms_importe_en_base($formulaire);
			}
		}
	}
	
	if ($id_formulaire){
		return $id_formulaire;
	}
	else{
		return _T('formidable:erreur_importer_forms');
	}
}

/**
 * Importer le tableau $formulaire en base
 * @param array $formulaire
 * @return bool|int
 */
function forms_importe_en_base($formulaire){
	include_spip('action/editer_formulaire');
	// On insère un nouveau formulaire
	$id_formulaire = formulaire_inserer();

	if (is_array($formulaire['saisies']))
		$formulaire['saisies'] = serialize($formulaire['saisies']);
	if (is_array($formulaire['traitements']))
		$formulaire['traitements'] = serialize($formulaire['traitements']);

	// si l'identifiant existe deja (multiples imports du meme form)
	// le dater
	if (sql_countsel("spip_formulaires","identifiant=".sql_quote($formulaire['identifiant']))){
		$formulaire['identifiant'] .= "_".date('Ymd_His');
	}

	// Si ok on modifie les champs de base
	if ($id_formulaire>0
		AND !($erreur = formulaire_modifier($id_formulaire, $formulaire))){

		return $id_formulaire;
	}

	return false;
}

/**
 * Configuration de l'objet formulaire formidable a partir du form f&t
 * @param array $form
 * @param array $formulaire
 */
function forms_configure_formulaire($form,&$formulaire){

	// Le titre
	$formulaire['titre'] = ($form['titre'] ? $form['titre'] : _T('info_sans_titre'));

	// Generer un identifiant
	// si id_form fourni, on s'en sert
	if (isset($form['id_form'])){
		$formulaire['identifiant'] = "form_import_".$form['id_form'];
	}
	else {
		$formulaire['identifiant'] = "form_import_".preg_replace(",\W,","_",strtolower($formulaire['titre']));
	}

	// Le descriptif
	$formulaire['descriptif'] = (isset($form['descriptif']) ? $form['descriptif'] : '');

	// Le message de retour si ok
	$formulaire['message_retour'] = (isset($form['texte']) ? $form['texte'] : '');

	if (!isset($formulaire['traitements']))
		$formulaire['traitements'] = array();

	if (!isset($formulaire['saisies']))
		$formulaire['saisies'] = array();
}

/**
 * Configurer les traitements
 *
 * @param array $form
 * @param array $formulaire
 */
function forms_configure_traitement_formulaire($form,&$formulaire){
	// Le traitement email
	if ($form['champconfirm']){
		if (!isset($formulaire['traitements']['email']))
			$formulaire['traitements']['email'] = array();
		$formulaire['traitements']['email']['champ_courriel_destinataire_form'] = $form['champconfirm'];
	}

	if (is_array($form['email'])){

		if ($email_defaut = $form['email']['defaut']){
			if (!isset($formulaire['traitements']['email']))
				$formulaire['traitements']['email'] = array();
			$formulaire['traitements']['email']['destinataires_plus'] = $email_defaut;
		}

		// TODO email route : feature qui n'existe pas dans formidable
		if ($route = $form['email']['route']){

		}
	}

	// Le traitement enregistrement : toujours
	$formulaire['traitements']['enregistrement'] = array(
		'moderation' => ($form['moderation'] == 'priori') ? 'priori' : 'posteriori',
		'modifiable' => ($form['modifiable'] == 'oui') ? 'on' : '',
		'multiple' => ($form['multiple'] == 'non') ? '' : 'on'
	);

}


/**
 * Transforme un champ f&t en Saisie
 * @param array $champ
 *   string champ
 *   string titre
 *   string type
 *   string obligatoire
 *   string taille
 *   string aide
 *   string extra_info
 *   string saisie oui/non
 *   array choix
 *     string choix
 *     string titre
 * @return array|bool
 */
function forms_champ_vers_saisie($champ){

	// Le truc par défaut
	$saisie = array(
		'saisie' => 'input',
		'options' => array('size'=>40)
	);

	// On essaye de traduire tous les types de champs
	$type = $champ['type'];
	switch ($type){
		case 'texte':
			$saisie['saisie'] = 'textarea';
			unset($saisie['options']['size']);
			$saisie['options']['rows'] = 5;
			$saisie['options']['cols'] = 40;
			break;
		case 'password':
			$saisie['options']['type'] = 'password';
			break;
		case 'date':
			$saisie['saisie'] = 'date';
			$saisie['verifier'] = array(
				'type' => 'date'
			);
			break;
		case 'num':
		case 'monnaie':
			$saisie['verifier'] = array(
				'type' => 'entier'
			);
			if ($taille = $champ['taille'])
				$saisie['verifier']['options'] = array('max' => (pow(10, $taille)-1));
			break;
		case 'email':
			$saisie['verifier'] = array(
				'type' => 'email'
			);
			break;
		case 'telephone':
			$saisie['verifier'] = array(
				'type' => 'telephone'
			);
			break;
		case 'select':
			unset($saisie['options']['size']);
			$liste = $champ['extra_info'];
			if ($liste == 'radio')
				$saisie['saisie'] = 'radio';
			else
				$saisie['saisie'] = 'selection';
			break;
		case 'multiple':
			$saisie['saisie'] = 'checkbox';
			unset($saisie['options']['size']);
			break;
		case 'fichier':
			// TODO saisie file
		case 'separateur':
			// TODO fieldset ?
			$saisie = null;
	}

	// On continue seulement si on a toujours une saisie
	if (!$saisie)
		return false;

	// Les choix pour les types select et multiple
	if(isset($champ['choix']) and is_array($champ['choix'])){
		$saisie['options']['datas'] = array();
		foreach($champ['choix'] as $un_choix){
			$choix = $un_choix['choix'];
			$titre = $un_choix['titre'];
			$saisie['options']['datas'][$choix] = $titre;
		}
	}

	// Le nom
	$saisie['options']['nom'] = $champ['champ'];

	// Le label
	$saisie['options']['label'] = $champ['titre'];

	// Obligatoire
	if ($champ['obligatoire'] == 'oui')
		$saisie['options']['obligatoire'] = 'on';

	// Explication éventuelle
	if ($explication = $champ['aide'])
		$saisie['options']['explication'] = $explication;

	if ($champ['saisie']=='non'){
		$saisie['options']['disable'] = 'on';
		// masquer en JS, fallback
		$saisie['options']['afficher_si'] = 'false';
	}

	return $saisie;
}
