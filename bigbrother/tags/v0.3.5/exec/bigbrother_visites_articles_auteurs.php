<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#


if (!defined("_ECRIRE_INC_VERSION")) return;

find_in_path('presentation.php', 'inc/', true);
find_in_path('minipres.php', 'inc/', true);


function exec_bigbrother_visites_articles_auteurs_dist(){

	$id_article = intval(_request('id_article'));
	$id_auteur = intval(_request('id_auteur'));

	if ($id_article <= 0 and $id_auteur <= 0)
		echo minipres(_T('bigbrother:erreur_statistiques'));
	else{

		// On vérifie si l'auteur exsite
		if ($id_auteur > 0){
			// On récupère l'auteur
			$auteur = sql_fetsel(
				'id_auteur, nom',
				'spip_auteurs',
				'id_auteur = '.$id_auteur
			);

			if (!$auteur){
				echo minipres(_T('public:aucun_auteur'));
				return;
			}

		}

		// On vérifie si l'article existe
		if ($id_article > 0){
			// On récupère l'auteur
			$article = sql_fetsel(
				'id_article, titre',
				'spip_articles',
				'id_article = '.$id_article
			);

			if (!$article){
				echo minipres(_T('public:aucun_article'));
				return;
			}

		}

		// Si tout s'est bien passé on affiche les stats
		if ($id_auteur > 0 and $id_article > 0)
			echo bigbrother_visites_article_auteur($article, $auteur);
		elseif ($id_auteur > 0)
			echo bigbrother_visites_auteur($auteur);
		elseif ($id_article > 0)
			echo bigbrother_visites_article($article);

	}

}


// Affiche le détail des visites d'un auteur sur un article
function bigbrother_visites_article_auteur($article, $auteur){

	pipeline('exec_init',array('args'=>array('exec'=>'bigbrother_visites_articles_auteurs','id_auteur'=>$auteur['id_auteur'], 'id_article' => $article['id_article']),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');

	echo $commencer_page($auteur['nom'],"auteurs","redacteurs");

	echo debut_gauche('', true);

	echo debut_boite_info(true);
	echo icone_horizontale(
		_T('bigbrother:voir_statistiques_auteur'),
		generer_url_ecrire('bigbrother_visites_articles_auteurs','id_auteur='.$auteur['id_auteur']),
		find_in_path('auteur-24.gif', 'images/', false),
		'',
		false
	);
	echo icone_horizontale(
		_T('bigbrother:voir_statistiques_article'),
		generer_url_ecrire('bigbrother_visites_articles_auteurs','id_article='.$article['id_article']),
		find_in_path('article-24.gif', 'images/', false),
		'',
		false
	);
	echo fin_boite_info(true);

	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',
		array('args' => array(
			'exec' => 'bigbrother_visites_articles_auteurs',
			'id_auteur' => $auteur['id_auteur'],
			'id_article' => $article['id_article']),
			'data'=>'')
		);
	echo debut_droite('', true);

	echo debut_cadre_relief("redacteurs-24.gif", true,'','','auteur-voir');
	echo "<h1>"._T('bigbrother:visites_article_auteur', array('nom' => $auteur['nom'], 'titre' => $article['titre']))."</h1>";
	echo recuperer_fond(
		'fonds/bigbrother_statistiques_article_auteur',
		array(
			'id_auteur' => $auteur['id_auteur'],
			'id_article' => $article['id_article'],
			'mode' => 'prive'
		)
	);
	echo fin_cadre_relief(true);

	echo fin_gauche(), fin_page();

}


// Affiche les statistiques de visites d'articles d'un auteur
// On affiche tous les articles qu'il a visité
// et à côté le cumul du temps qu'il y a passé
function bigbrother_visites_auteur($auteur){

	pipeline('exec_init',array('args'=>array('exec'=>'bigbrother_visites_articles_auteurs','id_auteur'=>$auteur['id_auteur']),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');

	echo $commencer_page($auteur['nom'],"auteurs","redacteurs");

	echo debut_gauche('', true);

	echo debut_boite_info(true);
	echo icone_horizontale(
		_T('lien_voir_auteur'),
		generer_url_ecrire('auteur_infos','id_auteur='.$auteur['id_auteur']),
		find_in_path('auteur-24.gif', 'images/', false),
		'',
		false
	);
	echo fin_boite_info(true);

	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',
		array('args' => array(
			'exec'=>'bigbrother_visites_articles_auteurs',
			'id_auteur'=>$auteur['id_auteur']),
			'data'=>'')
		);
	echo debut_droite('', true);

	echo debut_cadre_relief("redacteurs-24.gif", true,'','','auteur-voir');
	echo "<h1>".$auteur['nom']."</h1>";
	echo recuperer_fond(
		'fonds/bigbrother_statistiques_auteur',
		array(
			'id_auteur' => $auteur['id_auteur'],
			'mode' => 'prive'
		)
	);
	echo fin_cadre_relief(true);

	echo fin_gauche(), fin_page();

}


// Affiche les statistiques de visites d'un article par des auteurs
// On affiche tous les auteurs qui l'ont visité
// et à côté le cumul du temps qu'ils y ont passé
// puis le cumul du tout et la moyenne
function bigbrother_visites_article($article){

	pipeline('exec_init',array('args'=>array('exec'=>'bigbrother_visites_articles_auteurs','id_article'=>$article['id_article']),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');

	echo $commencer_page($article['titre'],"articles","redacteurs");

	echo debut_gauche('', true);

	echo debut_boite_info(true);
	echo icone_horizontale(
		_T('icone_modifier_article'),
		generer_url_ecrire('articles','id_article='.$article['id_article']),
		find_in_path('article-24.gif', 'images/', false),
		'',
		false
	);
	echo fin_boite_info(true);

	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',
		array('args' => array(
			'exec'=>'bigbrother_visites_articles_auteurs',
			'id_article'=>$article['id_article']),
			'data'=>'')
		);
	echo debut_droite('', true);

	echo debut_cadre_relief("article-24.gif", true,'','','article-voir');
	echo "<h1>".$article['titre']."</h1>";
	echo recuperer_fond(
		'fonds/bigbrother_statistiques_article',
		array(
			'id_article' => $article['id_article'],
			'mode' => 'prive'
		)
	);
	echo fin_cadre_relief(true);

	echo fin_gauche(), fin_page();

}

?>
