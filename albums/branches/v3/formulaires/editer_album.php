<?php
/**
 * Gestion du formulaire de d'édition d'un album
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_album
 *     Identifiant de l'album. 'new' pour un nouvel album.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier l'album créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un album source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de l'album, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_album_identifier_dist($id_album='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_album), $associer_objet));
}

/**
 * Chargement du formulaire d'édition d'un album
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_album
 *     Identifiant de l'album. 'new' pour un nouvel album.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le album créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un album source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de l'album, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_album_charger_dist($id_album='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('album',$id_album,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// lorsqu'on créé un album associé à un objet...
	if (
		!intval($id_album)
		AND $associer_objet
		AND list($objet, $id_objet) = explode('|', $associer_objet)
	){
		// le publier d'office
		$valeurs['statut'] = 'publie';
		// donner un titre par défaut selon la configuration
		include_spip('inc/config');
		if (lire_config('albums/utiliser_titre_defaut') == 'on'){
			$valeurs['titre_defaut'] = generer_info_entite($id_objet, $objet, 'titre');
		}
	}

	return $valeurs;

}

/**
 * Vérifications du formulaire d'édition d'un album
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_album
 *     Identifiant de l'album. 'new' pour un nouvel album.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le album créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un album source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de l'album, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_album_verifier_dist($id_album='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('album',$id_album,array());
}

/**
 * Traitement du formulaire d'édition d'un album
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_album
 *     Identifiant de l'album. 'new' pour un nouvel album.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le album créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un album source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de l'album, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_album_traiter_dist($id_album='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	// traitements génériques
	$res = formulaires_editer_objet_traiter('album',$id_album,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// peupler le titre à posteriori s'il est vide lors de la création (création rapide d'un album)
	if (!intval($id_album='new') AND !_request('titre') AND $res['id_album']){
		objet_modifier("album",$res['id_album'],array('titre' => _T('album:info_nouvel_album')." "._T('info_numero_abbreviation').$res['id_album']));
	}

	// un lien a prendre en compte ?
	if ($associer_objet AND $id_album = $res['id_album']) {
		if (list($objet, $id_objet) = explode('|', $associer_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('album' => $id_album), array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = ancre_url(parametre_url($res['redirect'],'id_album','','&'),'album'.$res['id_album']);
			}
		}
	}

	// FIXME : cas particulier aux squelettes de l'espace privé
	// Rechargement ajax en cas d'édition rapide sur place
	if (!$res['redirect']){
		$id_album = $res['id_album'];
		$js = "if (window.jQuery) jQuery(function(){ajaxReload('album$id_album');});";
		$js = "<script type='text/javascript'>$js</script>";
		if (isset($res['message_erreur']))
			$res['message_erreur'].= $js;
		else
			$res['message_ok'] .= $js;
	}

	return $res;

}


?>
