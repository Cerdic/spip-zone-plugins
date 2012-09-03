<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  ajouté en 11/2011 par Marcel BOLLA ... à partir de edit_categorie.php      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/navigation_modules');

function exec_edit_exercice()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_exercice = intval(_request('id'));
		onglets_association('exercices_budgetaires_titre');
		// INTRO : resume ressource
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_comptes', ""), )); // bof, le nombre d'operations est deja indique sur la page de comptes por l'exercice selectionne
		$infos['entete_duree'] = association_formater_duree(sql_getfetsel("TIMESTAMPDIFF(day,debut,fin) AS duree_jours", 'spip_asso_exercices', "id_exercice=$id_exercice"), 'D'); // voir note dans "/exec/exercices.php" au sujet de TIMESTAMPDIFF sachant que la simple diffrence "fin-debut" peut donner des resultats surprenants...
		echo association_totauxinfos_intro(sql_getfetsel('intitule', 'spip_asso_exercices', "id_exercice=$id_exercice" ), 'exercice', $id_exercice, $infos);
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('calculatrice.gif', 'exercice_budgetaire_titre');
		echo recuperer_fond('prive/editer/editer_asso_exercices', array (
			'id_exercice' => $id_exercice
		));
		fin_page_association();
	}
}

?>