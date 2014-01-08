<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_editer_asso_fonctions_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$erreur = '';

	// cette action peut etre appelee selon trois modes
	if (strpos($arg, '-')) { // mode d'appel 1 : directement depuis un squelette avec en argument <id_groupe>-<id_auteur>
		list($id_groupe, $id_auteur) = explode('-', $arg);
		$erreur = iou_fonction($id_groupe, $id_auteur, _request('fonctions') );
	} else { // mise a jour par lot...
		$id_auteur = association_recuperer_entier('id_auteur'); // editer_asso_fonctions2membre
		$id_groupe = association_recuperer_entier('id_groupe'); // editer_asso_fonctions2groupe
		$fonctions = association_recuperer_liste('fonctions', TRUE);
		if ( $id_auteur==$arg ) { // mettre a jour les fonctions des membres dans le groupe
			foreach ($fonctions as $id_auteur => $fonction)
				$erreur .= iou_fonction ($id_groupe, $id_auteur, $fonction);
			if ( $erreur )
				$erreur = _T('asso:erreur_sgbdr');
		} elseif ( $id_groupe==$arg ) { // mettre a jour les fonctions du membre dans les groupes
			foreach ($fonctions as $id_groupe => $fonction)
				$erreur .= iou_fonction ($id_groupe, $id_auteur, $fonction);
			if ( $erreur )
				$erreur = _T('asso:erreur_sgbdr');
		} else // mauvais parametres d'appel
			$erreur = _L("argument $arg incompris");
	}

	return $erreur;
}

/**
 * Insert Or Update :
 * on met a jour la fonction d'un membre d'un groupe existants
 * on ajoute un membre a un groupe si le couple est inexistant
 *
 * @param $id_groupe int
 * @param $id_auteur int
 * @param $fonction string
 * @return string
 *   Vide en cas de modification avec succes, sinon message generique...
 */
function iou_fonction($id_groupe, $id_auteur, $fonction) {
	if ( sql_countsel('spip_asso_fonctions', "id_groupe=$id_groupe AND id_auteur=$id_auteur") )
		sql_updateq('spip_asso_fonctions', array(
			'fonction' => $fonction,
		), "id_groupe=$id_groupe AND id_auteur=$id_auteur");
	else
		sql_insertq('spip_asso_fonctions', array(
			'fonction' => $fonction,
			'id_groupe' => $id_groupe,
			'id_auteur' => $id_auteur,
		) );
	if ( sql_countsel('spip_asso_fonctions', "id_groupe=$id_groupe AND id_auteur=$id_auteur and fonction=".sql_quote($fonction)) )
		return _T('asso:erreur_sgbdr');
	else
		return '';
}

?>