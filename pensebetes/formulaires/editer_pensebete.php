<?php
/**
 * Gestion du formulaire de d'édition de pensebete
 *
 * @plugin Pensebetes
 * @copyright  2019
 * @author     Vincent CALLIES
 * @licence    GNU/GPL
 * @package SPIP\Pensebetes\Formulaires
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Chargement des valeurs de SAISIES
 *
 * Aide à la création du formulaire avec le plugin SAISIES
 *
 * @param string $icone_objet
 *     pour afficher si nécessaire l'icône de l'objet auquel le pense-bête sera lié
 *
 */
function mes_saisies_pensebete($icone_objet='') {

$mes_saisies = array(
  array( // le fieldset 
    'saisie' => 'fieldset',
    'options' => array(
      'nom' => 'le_pensebete',
      'label' => _T('pensebete:info_le_pensebete'),
      'icone' => 'pensebete-24',
    ),
    'saisies' => array( // les champs dans le fieldset 
       array( // champ id_pensebete (numéro unique du pense-bête)
          'saisie' => 'hidden',
          'options' => array(
            'nom' => 'id_pensebete',
           )
      ),
       array( // champ id_donneur (numéro identification auteur du pense-bête)
          'saisie' => 'hidden',
          'options' => array(
             'nom' => 'id_donneur',
           )
      ),
        array( // champ date (date de création du pense-bête)
          'saisie' => 'hidden',
          'options' => array(
            'nom' => 'date',
           )
      ),
        array( // champ associer_objet (A-t-on demandé que ce pense-bête soit associé à un objet )
          'saisie' => 'hidden',
          'options' => array(
             'nom' => 'associer_objet',
           )
      ),
        array( // champ id_receveur (numéro identification de l'auteur destinataire du pense-bête)
          'saisie' => 'auteurs',
          'options' => array(
             'nom' => 'id_receveur',
            'label' => _T('pensebete:label_receveur'),
             'obligatoire'=>'oui',
           )
      ),
      array( // champ titre : ligne de texte
          'saisie' => 'input',
          'options' => array(
            'nom' => 'titre',
            'label' => _T('pensebete:label_titre'),
            'maxlength' => 17, // un titre court
          ),
		   'verifier' => array(
			'type' => 'taille',
			'options' => array ('min' => 0, 'max' => 17),
        	)
     ),
     array( // champ texte : un bloc de texte
          'saisie' => 'textarea',
          'options' => array(
            'nom' => 'texte',
            'label' => _T('pensebete:label_texte'),
            'rows'=>3,'cols'=>60,'longueur_max'=>110, // un texte court
           ),
		   'verifier' => array(
			'type' => 'taille',
			'options' => array ('min' => 0, 'max' => 110)
        	),
      ),
  ),
 ),
  array( // le fieldset 
    'saisie' => 'fieldset',
    'options' => array(
      'afficher_si'=> '@associer_objet@ != ""',
      'nom' => 'lassociation',
      'label' => _T('pensebete:info_lassociation'),
      'icone' => $icone_objet,
    ),
    'saisies' => array( // les champs dans le second fieldset 
  array( // hors fieldset : association 
    'saisie' => 'oui_non',
    'options' => array(
      'nom' => 'c_associe',
      'label' => _T('pensebete:texte_associer_pensebete'),
'valeur_oui' => 'oui', 
'valeur_non' => 'non',
	 )
    ),
   )
  )
);

  return $mes_saisies;
}


/**
 * Chargement du formulaire d'édition de pensebete
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier le mot créé à cet objet,
 *     tel que 'article|3'
 * @uses formulaires_editer_objet_charger()
 * @uses mes_saisies_pensebete()
 *
 */
function formulaires_editer_pensebete_charger_dist($id_pensebete='new', $id_rubrique=0, $retour='', $associer_objet, $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('pensebete',$id_pensebete,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);

	// Si nouveau et titre dans l'url : fixer le titre (à 17 caractères)
	if ($id_pensebete == 'oui'
		and strlen($titre = _request('titre'))
	) {
		$valeurs['titre'] = substr ($titre,0,16);
	}

	// A-t-on demandé à ce que le pense-bête soit associé à un objet éditorial ?
	if ($associer_objet) {
		$valeurs['associer_objet']=$associer_objet;
		list($objet, $id_objet) = explode('|', $associer_objet);
		$icone_objet=chemin_image($objet);
		$valeurs['c_associe']='oui';
	} else
		$valeurs['c_associe']='non';

	// s'il n'y a pas d'id_donneur, donner l'id de l'auteur
	if (empty($valeurs['id_donneur']))
		$valeurs['id_donneur']=$GLOBALS['visiteur_session']['id_auteur'];
	$valeurs['_mes_saisies'] = mes_saisies_pensebete($icone_objet);

	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_pensebete_identifier_dist($id_pensebete='new', $id_rubrique=0, $retour='', $associer_objet, $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_pensebete), $associer_objet));
}

/**
 * Vérification du formulaire d'édition de pensebete
 *
 * @uses formulaires_editer_objet_verifier()
 * @uses saisies_verifier()
 *
 */
function formulaires_editer_pensebete_verifier_dist($id_pensebete='new', $id_rubrique=0, $retour='', $associer_objet, $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('pensebete', $id_pensebete);

	// on va chercher le pipeline saisies_verifier() dans son fichier
	include_spip('inc/saisies');
 
	// on charge les saisies
	$mes_saisies = mes_saisies_pensebete();
 
	// saisies_verifier retourne un tableau des erreurs s'il y en a, sinon traiter() prend le relais
	$erreurs = saisies_verifier($mes_saisies);

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de pensebete
 *
 * Le traitement effectue une mise à zéro de l'id_auteur pour éviter des associations considérées comme inutiles.
 *
 * @uses formulaires_editer_objet_traiter()
 * @uses objet_associer()
 *
 */
function formulaires_editer_pensebete_traiter_dist($id_pensebete='new', $id_rubrique=0, $retour='', $associer_objet, $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// éviter que l'auteur soit associé au pense-bête.
	set_request('id_auteur','');
	
	// rompre l'association si nécessaire		
	if (_request('c_associe')=='non'){
		set_request('associer_objet','');
	} else { // on garde la donnée de redirection s'il y a une association
		$redirect=_request('redirect');
	}

	// on traite les données
	$retours = formulaires_editer_objet_traiter('pensebete',_request('id_pensebete'),$id_parent,$lier_trad,_request('retour'),_request('config_fonc'),_request('row'),_request('hidden'));
	
	// associer le pensebete à un objet
	if (_request('associer_objet') AND $id_pensebete=$retours['id_pensebete']) {			
		if (intval(_request('associer_objet'))) {
				// compat avec l'appel de la forme ajouter_id_article
				$objet = 'article';
				$id_objet = intval(_request('associer_objet'));
		} else {
				list($objet, $id_objet) = explode('|', _request('associer_objet'));
		}
		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer( array('pensebete'=>$id_pensebete), array($objet=>$id_objet) );
			// rediriger vers l'objet
			if (isset($redirect)) {
				$retours['redirect'] = parametre_url($redirect, 'id_lien_ajoute', $id_pensebete, '&');
			}
			return $retours;
		}
		else
			return array('message_erreur'=>_T('pensebete:erreur_association'));
	} // si remord par rapport à l'association à l'objet, rediriger vers le pense-bête
	elseif (_request('c_associe')=='non')
		$retours['redirect']=generer_url_ecrire('pensebete','id_pensebete='.$retours['id_pensebete']);

	return $retours;	
}

