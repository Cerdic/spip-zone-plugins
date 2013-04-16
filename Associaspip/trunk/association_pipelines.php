<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Ajout d'un lien vers la page de membre sur la page d'auteur
**/
function association_affiche_gauche($flux) {
	if ($flux['args']['exec']=='auteur_infos') {
		$id_auteur = $flux['args']['id_auteur'];
		if (autoriser('voir_membres', 'association', $id_auteur)) {
			$flux['data'] .= recuperer_fond('prive/boite/lien_page_auteur', array ('id_auteur' => $id_auteur));
		}
	}
	return $flux;
}

/**
 * Interface avec le plugin "Champs Extras 2" : ajout de
 * asso_activites, asso_comptes, asso_membres, asso_ressources
 * a la liste des objets pouvant recevoir des champs extras...
**/
function association_objets_extensibles($objets){
	return array_merge($objets, array(
		'asso_membre' => _T('asso:membres'), // Adherent(e)s/Membres
		'asso_compte' => _T('asso:comptes'), // Grand Livre/Journal Comptable
		'asso_activite' => _T('asso:activites'), // Inscriptions et Participations financiere des membres aux activites
		'asso_ressource' => _T('asso:ressources'), // Ressources (par ex. livres) pretes
	));
}

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
		if ($GLOBALS['association_metas']['categorie_par_defaut']) {
			// on affecte une categorie definie par defaut en configuration
			// le test parait inutile puisque de toute facon on a "0"
			// ...sauf si la definition en base de donnees est changee pour une autre
			// valeur par defaut qu'on preserve alors.
			$modif['id_categorie'] = $GLOBALS['association_metas']['categorie_par_defaut'];
		}
		sql_insertq('spip_asso_membres', $modif);
	}
}

/**
 * Definition de la periodicite d'execution des taches dans genie/
 *
 * Attention : durees en secondes ; ne pas descendre en dessous de 30 secondes !
 * http://programmer.spip.net/Declarer-une-tache
 * http://contrib.spip.net/Ajouter-une-tache-CRON-dans-un-plugin-SPIP
 */
function association_taches_generales_cron($crontab) {
	$crontab['asso_membres_echus'] = 60*60*24; // Tous les jours (i.ee 24h de 60' de 60")
	return $crontab;
}

?>