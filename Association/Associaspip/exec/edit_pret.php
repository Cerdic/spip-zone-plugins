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

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_edit_pret(){

	$id_pret= intval(_request('id_pret'));
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'activites', $id_pret)) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:prets_titre_edition_prets')) ;
		association_onglets(_T('asso:titre_onglet_prets'));
		echo debut_gauche('',true);
		if ($id_pret) { // modifier
			$pret_ressrc = sql_fetsel("id_ressource", "spip_asso_prets", "id_pret=$id_pret");
			$id_ressource = $pret_ressrc['id_ressource'];
		} else { // ajouter
			$id_ressource = intval(_request('id_ressource'));
		}
		$data = sql_fetsel('*', 'spip_asso_ressources', "id_ressource=$id_ressource" ) ;
		if ($data) {
			echo debut_boite_info(true);
			echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">'._T('asso:ressources_num').'<br />';
			echo '<span class="spip_xx-large">'.$data['id_ressource'].'</span></div>';
			echo '<p>'._T('asso:ressources_libelle_code').': '.$data['code'].'<br />';
			echo $data['intitule'];
			echo '</p>';
			echo fin_boite_info(true);
		}
		echo association_retour();
		echo debut_droite('', true);
		echo recuperer_fond('prive/editer/editer_asso_prets', array (
			'id_ressource' => $id_ressource,
			'id_pret' => $id_pret,
		));
		echo fin_page_association();
	}
}

?>