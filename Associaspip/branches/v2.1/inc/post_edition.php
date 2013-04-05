<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function association_post_edition($flux){
	$id = $flux['args']['id_objet'];
	if ($id
	AND $flux['args']['table']=='spip_auteurs') {
		update_spip_asso_membre($id);
	}
}

function update_spip_asso_membre($id_auteur)
{
	$auteur = sql_fetsel('statut, nom, bio, email', 'spip_auteurs', "id_auteur=$id_auteur");

	if ($auteur['statut'] == '5poubelle') { /* auteur a la poubelle: on le met aussi a la poubelle dans asso_membres si il est present dans la table */
		if (sql_getfetsel('id_auteur', 'spip_asso_membres', "id_auteur=$id_auteur")) {
			sql_updateq('spip_asso_membres', array('statut_interne' => 'sorti'), "id_auteur=$id_auteur");
		}
		return;
	}

	/* on recupere dans la bio les champs fonction, telephone, mobile, adresse, code postal et ville: 1 par ligne (sauf code postal et ville) */
	$bio = $auteur['bio'];
	if (preg_match_all('/(.+)$/m', $bio, $r)
	AND preg_match('/^\s*(\d{5})\s+(.*)/', $r[0][4], $m))
	      $modif = array(
		'fonction' => trim($r[0][0]),
		'telephone' => telephone_std($r[0][1]),
		'mobile' => telephone_std($r[0][2]),
		'adresse' => trim($r[0][3]),
		'code_postal' => $m[1],
		'ville' => trim($m[2])
			     );
	else $modif = array();
	$modif['email'] = $auteur['email'];

	/* on recupere les noms et prenoms dans le champ nom de l'auteur SPIP */
	$nom = $auteur['nom'];
	if ($nom) {
		/* selection du format d'import du champ non */
		if ($GLOBALS['association_metas']['import_nom_auteur'] == "prenom_nom") {
			list($prenom, $nom) = preg_split('/\s+/', $nom, 2);
			if (!$nom) {/* il n'y avait qu'une seule chaine -> on la met dans le nom et le prenom reste vide */
				$nom = $prenom;
				$prenom = '';
			}
		} elseif ($GLOBALS['association_metas']['import_nom_auteur'] == "nom") {
			$prenom = '';
		} else { // defaut: format nom prenom
			list($nom, $prenom) = preg_split('/\s+/', $nom, 2); /* il faudrait aussi gerer le cas ou le nom de famille contient un espace */
		}
	}
	else {$nom = _T('asso:activite_entete_adherent').' '.$id_auteur; $prenom = '';} /* si il est vide, le nom sera Adherents XX */
	$modif['nom_famille'] = $nom;
	$modif['prenom'] = $prenom;

	/* si l'auteur est deja present dans la base: on ne modifie pas les noms/prenoms/fonction */
	$membre = sql_fetsel('id_auteur,statut_interne', 'spip_asso_membres', "id_auteur=$id_auteur");
	if ($membre['id_auteur']) {
		unset($modif['fonction']);
		unset($modif['nom_famille']);
		unset($modif['prenom']);
		if ($membre['statut_interne'] == 'sorti') $modif['statut_interne'] = 'prospect'; /* si un auteur est edite mais correspond a un membre sorti, on le repasse en prospect */
		sql_updateq('spip_asso_membres', $modif, "id_auteur=$id_auteur");
	} else { /* sinon on ajoute avec comme statut par defaut prospect */
	  $modif['statut_interne'] = 'prospect';
	  $modif['id_auteur'] = $id_auteur;
	  sql_insertq('spip_asso_membres', $modif);
	}
}

function telephone_std($num)
{
	$num = preg_replace('/\D/', '', $num);
	if ($num AND strlen($num) < 10) $num = '0'.$num;
	$num = preg_replace('/(\d\d)/', '\1 ', $num);
	return rtrim($num);
}
?>
