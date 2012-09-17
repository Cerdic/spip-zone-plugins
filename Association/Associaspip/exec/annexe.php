<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 09/2011                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/* TODO : ce module n'est pas implemente ! Il existe juste pour ne pas generer
 * une erreur lors de son activation dans les raccourcis de compte
 */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_annexe()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
// initialisations
		include_spip('inc/association_comptabilite');
		$ids = association_passe_parametres_comptables();
		$exercice_data = sql_asso1ligne('exercice', $ids['exercice']);
// traitements
		onglets_association('titre_onglet_comptes', 'comptes');
		// INTRO : rappel de l'exercicee affichee
		$infos['exercice_entete_debut'] = association_formater_date($exercice_data['debut'], 'dtstart');
		$infos['exercice_entete_fin'] = association_formater_date($exercice_data['fin'], 'dtend');
		echo association_totauxinfos_intro($exercice_data['intitule'], 'exercice', $ids['exercice'], $infos);
		// datation et raccourcis
		raccourcis_association(array('comptes', "exercice=$exercice"), array(
			'encaisse_titre_general' => array('finances-24.png', array('encaisse', "exercice=$exercice") ),
			'cpte_resultat_titre_general' => array('finances-24.png', array('compte_resultat', "exercice=$exercice") ),
			'cpte_bilan_titre_general' => array('finances-24.png', array('bilan', "exercice=$exercice") ),
		));
		debut_cadre_association('finances-24.png', 'annexe_titre_general', $exercice_data['intitule']);
		// Filtres
		filtres_association(array(
			'exercice'=>$ids['exercice'],
			'destination'=>$ids['destination'],
		), 'annexe');
		echo _T('asso:non_implemente');
		// http://www.aquadesign.be/actu/article-3678.php
		// http://www.documentissime.fr/dossiers-droit-pratique/dossier-274-les-documents-comptables-obligatoires/les-comptes-annuels/l-annexe.html
		fin_page_association();
	}
}

?>