<?php
/**
 * Interface d'administration des Forums
 *
 * (c) 2009 - Cedric Morin
 * Distribue sous licence GPL3
 *
 */

function exec_controle_forum_dist()
{
	if (!autoriser('publierdans','rubrique',_request('id_rubrique'))) {
		include_spip('inc/minipres');
		echo minipres();
	} 
	else 
	{

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('titre_page_forum_suivi'), "forum", "forum-controle");

		echo gros_titre(_T('titre_forum_suivi'),'',false);

		echo debut_gauche('', true);
		echo debut_boite_info(true);
		echo _T('info_gauche_suivi_forum_2'), aide("suiviforum");

		// Afficher le lien RSS

		$type = _request('type')?_request('type'):"public";
		echo bouton_spip_rss("forums_$type");

		echo fin_boite_info(true);
			
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'controle_forum', 'type'=>$type),'data'=>''));
		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'controle_forum', 'type'=>$type),'data'=>''));
			
		echo debut_droite('', true);
		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'controle_forum', 'type'=>$type),'data'=>''));

		echo recuperer_fond('',array_merge($_GET,array('fond'=>'prive/listes/controle_forum')),array('ajax'=>true));
		echo fin_gauche(), fin_page();
	}
}

?>