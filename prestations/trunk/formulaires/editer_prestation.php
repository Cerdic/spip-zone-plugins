<?php
/**
 * Gestion du formulaire de d'édition de prestation
 *
 * @plugin     Prestations
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Prestations\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('base/objets');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_prestation
 *     Identifiant du prestation. 'new' pour un nouveau prestation.
 * @param string $objet
 *     Type de l'objet parent
 * @param int $id_objet
 *     Identifiant de l'objet parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un prestation source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du prestation, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_prestation_identifier_dist($id_prestation = 'new', $objet='', $id_objet=0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_prestation)));
}

/**
 * Saisies du formulaire d'édition de prestation
 *
 * @param int|string $id_prestation
 *     Identifiant du prestation. 'new' pour un nouveau prestation.
 * @param string $objet
 *     Type de l'objet parent
 * @param int $id_objet
 *     Identifiant de l'objet parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un prestation source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du prestation, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_prestation_saisies_dist($id_prestation = 'new', $objet='', $id_objet=0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$saisies = array(
		array(
			'saisie' => 'hidden',
			'options' => array(
				'nom' => 'id_prestation',
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('prestation:champ_titre_label'),
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'prestations_types',
			'options' => array(
				'nom' => 'id_prestations_type',
				'label' => _T('prestation:champ_id_prestations_type_label'),
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'prix_unitaire_ht',
				'label' => _T('prestation:champ_prix_unitaire_ht_label'),
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'quantite',
				'label' => _T('prestation:champ_quantite_label'),
				'inserer_fin' => <<<'EOT'
	<script type="text/javascript">
	/*<![CDATA[*/
	;(function($){
		$(function(){
			$('#champ_quantite')
				.on('keyup', function() {
					if ($(this).val()) {
						$('#champ_quantite_relative').val('');
					}
				});
		});
	})(jQuery);
	/*]]>*/
	</script>
EOT
			),
			'verifier' => array(
				'type' => 'decimal',
				'options' => array(
					'normaliser' => true,
				),
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'quantite_relative',
				'label' => _T('prestation:champ_quantite_relative_label'),
				'inserer_fin' => <<<'EOT'
	<script type="text/javascript">
	/*<![CDATA[*/
	;(function($){
		$(function(){
			$('#champ_quantite_relative')
				.on('keyup', function() {
					if ($(this).val()) {
						$('#champ_quantite').val('');
					}
				});
		});
	})(jQuery);
	/*]]>*/
	</script>
EOT
			),
			'verifier' => array(
				'type' => 'decimal',
				'options' => array(
					'normaliser' => true,
				),
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'quantite_relative_type',
				'label_case' => _T('prestation:champ_quantite_relative_type_label'),
				'afficher_si' => '@quantite_relative@ != ""',
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'quantite_relative_rang',
				'label_case' => _T('prestation:champ_quantite_relative_rang_label'),
				'afficher_si' => '@quantite_relative@ != ""',
			),
		),
		array(
			'saisie' => 'prestations_unites',
			'options' => array(
				'nom' => 'id_prestations_unite',
				'label' => _T('prestation:champ_id_prestations_unite_label'),
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'taxe',
				'label' => _T('prestation:champ_taxe_label'),
			),
			'verifier' => array(
				'type' => 'decimal',
				'options' => array(
					'normaliser' => true,
				),
			),
		),
	);
	
	return $saisies;
}

/**
 * Chargement du formulaire d'édition de prestation
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_prestation
 *     Identifiant du prestation. 'new' pour un nouveau prestation.
 * @param string $objet
 *     Type de l'objet parent
 * @param int $id_objet
 *     Identifiant de l'objet parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un prestation source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du prestation, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_prestation_charger_dist($id_prestation = 'new', $objet='', $id_objet=0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	include_spip('inc/autoriser');
	
	if (intval($id_prestation) and !autoriser('modifier', 'prestation', intval($id_prestation))) {
		$valeurs = false;
	}
	elseif (!intval($id_prestation)) {
		if (!$objet or !$id_objet or !autoriser('creerprestationdans', $objet, $id_objet)) {
			$valeurs = false;
		}
		else {
			$valeurs = formulaires_editer_objet_charger('prestation', $id_prestation, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
		}
	}
	else {
		$valeurs = formulaires_editer_objet_charger('prestation', $id_prestation, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	}
	
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de prestation
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_prestation
 *     Identifiant du prestation. 'new' pour un nouveau prestation.
 * @param string $objet
 *     Type de l'objet parent
 * @param int $id_objet
 *     Identifiant de l'objet parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un prestation source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du prestation, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_prestation_verifier_dist($id_prestation = 'new', $objet='', $id_objet=0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$erreurs = formulaires_editer_objet_verifier('prestation', $id_prestation, array('titre', 'id_prestations_unite'));

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de prestation
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_prestation
 *     Identifiant du prestation. 'new' pour un nouveau prestation.
 * @param string $objet
 *     Type de l'objet parent
 * @param int $id_objet
 *     Identifiant de l'objet parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un prestation source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du prestation, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_prestation_traiter_dist($id_prestation = 'new', $objet='', $id_objet=0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	// Pour une création
	if (intval($id_prestation) <= 0) {
		set_request('objet', objet_type($objet));
		set_request('id_objet', intval($id_objet));
	}
	
	$retours = formulaires_editer_objet_traiter('prestation', $id_prestation, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	
	return $retours;
}
