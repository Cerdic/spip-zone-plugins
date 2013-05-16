<?php
/**
 * Gestion du formulaire de d'édition de almanach
 *
 * @plugin     Import_ics
 * @copyright  2013
 * @author     Amaury
 * @licence    GNU/GPL
 * @package    SPIP\Import_ics\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_almanach
 *     Identifiant du almanach. 'new' pour un nouveau almanach.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un almanach source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du almanach, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_almanach_identifier_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_almanach)));
}

/**
 * Chargement du formulaire d'édition de almanach
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_almanach
 *     Identifiant du almanach. 'new' pour un nouveau almanach.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un almanach source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du almanach, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_almanach_charger_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('almanach',$id_almanach,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de almanach
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_almanach
 *     Identifiant du almanach. 'new' pour un nouveau almanach.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un almanach source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du almanach, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_almanach_verifier_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('almanach',$id_almanach, array('titre', 'url', 'id_article'));
}

/**
 * Traitement du formulaire d'édition de almanach
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_almanach
 *     Identifiant du almanach. 'new' pour un nouveau almanach.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un almanach source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du almanach, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_almanach_traiter_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	include_spip('lib/iCalcreator.class');


	# on passe par un fichier temp car notre librairie fonctionne comme ca
	$tmp = _DIR_TMP . 'ics-'.md5('url');
	ecrire_fichier($tmp, str_replace("\r\n", "\n", $u));

	$cal = new vcalendar();
	$cal->setConfig( 'filename', $tmp );
	$cal->parse();

sql_insertq('spip_almanachs_liens',array('id_almanach'=>$id_almanach,'id_objet'=>'1','objet'=>'evenement'));
	return formulaires_editer_objet_traiter('almanach',$id_almanach,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>