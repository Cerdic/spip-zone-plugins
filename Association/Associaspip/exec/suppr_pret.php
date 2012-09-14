<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_suppr_pret()
{
	if (!autoriser('associer', 'activites')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id_pret = intval(_request('id_pret'));
		$id_ressource = intval(_request('id_ressource'));
		onglets_association('titre_onglet_prets', 'ressources');
		$pret = sql_fetsel('*', 'spip_asso_prets', "id_pret=$id_pret" ) ;
		$ressource = sql_fetsel('*', 'spip_asso_ressources', "id_ressource=$id_ressource" ) ;
		$infos['entete_article'] = $ressource['intitule'];
		$auteur = sql_fetsel('*', 'spip_asso_membres', "id_auteur=$pret[id_emprunteur]");
		$infos['entete_nom'] = association_calculer_nom_membre($auteur['sexe'], $auteur['prenom'], $auteur['nom_famille'],'span');
		$infos['prets_entete_date_sortie'] = association_formater_date($pret['date_sortie'],'dtstart');
		$infos['prets_entete_date_retour'] = association_formater_date($pret['date_retour'],'dtend');
		echo association_totauxinfos_intro('', 'pret', $id_pret, $infos );
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('pret-24.gif', 'prets_titre_suppression_prets');
		echo association_bloc_suppression('pret', "$id_pret-$id_ressource");
		fin_page_association();
	}
}

?>