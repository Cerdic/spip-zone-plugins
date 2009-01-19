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
	if (!autoriser('publierdans','rubrique',_request('id_rubrique'))
	  OR ($id_article = _request('id_article') AND !autoriser('modererforum', 'article', $id_article))
	  ) {
		include_spip('inc/minipres');
		echo minipres();
	} 
	else 
	{
		exec_controle_forum_args(_request('type'),$_GET,'prive/controler_forum');
	}
}

function exec_controle_forum_args($type,$contexte=array(),$fond = 'prive/controler_forum'){

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('titre_page_forum_suivi'), "forum", "forum-controle");

		echo debut_gauche('', true);
		echo debut_boite_info(true);
		echo _T('info_gauche_suivi_forum_2'), aide("suiviforum");

		// Afficher le lien RSS

		$type = $type?$type:"public";
		echo bouton_spip_rss("forums_$type");

		echo fin_boite_info(true);

		if ($id_article=$contexte['id_article']){
			$res = icone_horizontale(_T('icone_retour'), generer_url_ecrire("articles","id_article=$id_article"), "article-24.gif","rien.gif", false);
			$res .= icone_horizontale(_T('icone_statistiques_visites'), generer_url_ecrire("statistiques_visites","id_article=$id_article"), "statistiques-24.gif","rien.gif", false);
			echo bloc_des_raccourcis($res);
		}

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'controle_forum', 'type'=>$type),'data'=>''));
		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'controle_forum', 'type'=>$type),'data'=>''));
			
		echo debut_droite('', true);
		echo gros_titre(_T('titre_forum_suivi'),'',false);
		
		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'controle_forum', 'type'=>$type),'data'=>''));

		echo recuperer_fond('',array_merge($contexte,array('fond'=>$fond)),array('ajax'=>true));
		echo fin_gauche(), fin_page();
}

?>