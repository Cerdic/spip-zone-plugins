<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 Emmanuel Saint-James
 * @copyright Copyright (c) 201108 Marcel Bolla
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_edit_exercice() {
	if (!autoriser('gerer_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('association_modules');
		$id_exercice = association_passeparam_id('exercice');
		echo association_navigation_onglets('exercices_budgetaires_titre', 'association');
		// INTRO : resume ressource
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_comptes', ""), )); // bof, le nombre d'operations est deja indique sur la page de comptes pour l'exercice selectionne
		$infos['entete_duree'] = association_formater_duree(sql_getfetsel("TIMESTAMPDIFF(day,date_debut,date_fin) AS duree_jours", 'spip_asso_exercices', "id_exercice=$id_exercice"), 'D'); // voir note dans "/exec/exercices.php" au sujet de TIMESTAMPDIFF sachant que la simple diffrence "fin-debut" peut donner des resultats surprenants...
		echo association_tablinfos_intro(sql_getfetsel('intitule', 'spip_asso_exercices', "id_exercice=$id_exercice" ), 'exercice', $id_exercice, $infos);
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			array('tous_les_exercices', 'grille-24.png', array('exercice_comptable', "id=$id_exercice"), array('gerer_compta', 'association') ),
		) );
		debut_cadre_association('calculatrice.gif', 'exercice_budgetaire_titre');
		echo recuperer_fond('prive/editer/editer_asso_exercices', array (
			'id_exercice' => $id_exercice
		));
		fin_page_association();
	}
}

?>