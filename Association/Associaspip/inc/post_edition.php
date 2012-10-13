<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function association_post_edition($flux) {
	$id = $flux['args']['id_objet'];
	if ($id AND $flux['args']['table']=='spip_auteurs') {
		if ($GLOBALS['association_metas']['import_auteur_auto']) {
			update_spip_asso_membre($id);
		}
	}
}

function update_spip_asso_membre($id_auteur) {
	$auteur = sql_fetsel('statut, nom', 'spip_auteurs', "id_auteur=$id_auteur");
	if ($auteur['statut']=='5poubelle') { // auteur a la poubelle : on le met aussi a la poubelle dans asso_membres si il est present dans la table
		if (sql_getfetsel('id_auteur', 'spip_asso_membres', "id_auteur=$id_auteur")) {
			sql_updateq('spip_asso_membres', array('statut_interne' => 'sorti'), "id_auteur=$id_auteur");
		}
		return;
	}
	$modif = array();
	$nom = $auteur['nom']; // on recupere les noms et prenoms dans le champ nom de l'auteur SPIP
	if ($nom) {
		/* selection du format d'import du champ non */
		if ($GLOBALS['association_metas']['import_nom_auteur']=='prenom_nom') {
			list($prenom, $nom) = preg_split('/\s+/', $nom, 2);
			if (!$nom) { // il n'y avait qu'une seule chaine : on la met dans le nom et le prenom reste vide
				$nom = $prenom;
				$prenom = '';
			}
		} elseif ($GLOBALS['association_metas']['import_nom_auteur']=='nom') {
			$prenom = '';
		} else { // defaut: format nom prenom
			list($nom, $prenom) = preg_split('/\s+/', $nom, 2); //!\ on ne sait pas gerer le cas ou le nom de famille contient un espace
		}
	} else { // s'il est vide, le nom sera Adherent XX
		$nom = _T('asso:activite_entete_adherent').' '.$id_auteur;
		$prenom = '';
	}
	$membre = sql_fetsel('id_auteur,statut_interne', 'spip_asso_membres', "id_auteur=$id_auteur");
	if ($membre['id_auteur']) { // si l'auteur est deja present dans la base : on en modifie pas les noms/prenoms qui peuvent etre edite directement dans la page d'edition du membre
		if ($membre['statut_interne']=='sorti') {
			$modif['statut_interne'] = 'prospect'; // si un auteur est edite mais correspond a un membre sorti, on le repasse en prospect
			sql_updateq('spip_asso_membres', $modif, "id_auteur=$id_auteur");
		}
	} else { // sinon on l'ajoute avec comme statut par defaut prospect
		$modif['nom_famille'] = $nom;
		$modif['prenom'] = $prenom;
		$modif['statut_interne'] = 'prospect';
		$modif['id_auteur'] = $id_auteur;
		if ($GLOBALS['association_metas']['categorie_par_defaut']!='') { // on verifie s'il existe une categorie par defaut
			$modif['categorie'] = $GLOBALS['association_metas']['categorie_par_defaut'];
		}
		sql_insertq('spip_asso_membres', $modif);
	}
}

?>