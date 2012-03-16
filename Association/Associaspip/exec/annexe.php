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
		$plan = sql_countsel('spip_asso_plan');
		$exercice = intval(_request('exercice'));
		if(!$exercice){
			/* on recupere l'id_exercice dont la date "fin" est "la plus grande" */
			$exercice = sql_getfetsel('id_exercice','spip_asso_exercices', '', '', 'fin DESC');
			if(!$exercice)
				$exercice = 0;
		}
		$exercice_data = sql_asso1ligne('exercice', $exercice);
		association_onglets(_T('asso:titre_onglet_comptes'));
		// INTRO : rappel de l'exercicee affichee
		echo totauxinfos_intro($exercice_data['intitule'],'exercice',$exercice);
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone('cpte_resultat_titre_general',  generer_url_ecrire('compte_resultat', "exercice=$exercice"), 'finances.jpg')
		. association_icone('bilan', generer_url_ecrire('bilan', "exercice=$exercice"), 'finances.jpg');
		$res .= association_icone('bouton_retour',  generer_url_ecrire('comptes', "exercice=$exercice"), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		debut_cadre_association('finances.jpg', 'annexe_titre_general', $exercice_data['intitule']);
		echo _T('asso:non_implemente');
		fin_page_association();
	}
}

?>