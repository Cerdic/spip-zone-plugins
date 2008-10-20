<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

find_in_path('presentation.php', 'inc/', true);
find_in_path('minipres.php', 'inc/', true);


function exec_bigbrother_visites_articles_auteurs_dist(){
	
	$id_article = intval(_request('id_article'));
	$id_auteur = intval(_request('id_auteur'));
	
	if ($id_article <= 0 and $id_auteur <= 0)
		echo minipres(_T('bigbrother:erreur_statistiques'));
	elseif ($id_auteur > 0){
	
		find_in_path('abstract_sql.php', 'base/', true);
		// On récupère l'auteur
		$auteur = sql_fetsel(
			'*',
			'spip_auteurs',
			'id_auteur = '.$id_auteur
		);
		
		if (!$auteur)
			echo minipres(_T('public:aucun_auteur'));
		else
			exec_bigbrother_visites_auteur($auteur);
	
	}
	elseif ($id_article > 0){
	
		find_in_path('abstract_sql.php', 'base/', true);
		// On récupère l'auteur
		$article = sql_fetsel(
			'*',
			'spip_articles',
			'id_article = '.$id_article
		);
		
		if (!$article)
			echo minipres(_T('public:aucun_article'));
		else
			exec_bigbrother_visites_article($article);
	
	}
	
}


// Affiche les statistiques de visites d'articles d'un auteur
// On affiche tous les articles qu'il a visité
// et à côté le cumul du temps qu'il y a passé
function exec_bigbrother_visites_auteur($auteur){

	pipeline('exec_init',array('args'=>array('exec'=>'bigbrother_visites_articles_auteurs','id_auteur'=>$id_auteur),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	
	echo $commencer_page($auteur['nom'],"auteurs","redacteurs");
	
	echo debut_gauche('', true);
	
	
}


// http://doc.spip.org/@articles_edit
function articles_edit($id_article, $id_rubrique, $lier_trad, $id_version, $new, $config_fonc, $row)
{
	$id_article = $row['id_article'];
	$id_rubrique = $row['id_rubrique'];
	$titre = sinon($row["titre"],_T('info_sans_titre'));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	pipeline('exec_init',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article),'data'=>''));
	
	if ($id_version) $titre.= ' ('._T('version')." $id_version)";

	echo $commencer_page(_T('titre_page_articles_edit', array('titre' => $titre)), "naviguer", "articles", $id_rubrique);

	echo debut_grand_cadre(true);
	echo afficher_hierarchie($id_rubrique);
	echo fin_grand_cadre(true);

	echo debut_gauche("",true);

	// Pave "documents associes a l'article"
	
	if (!$new){
		# affichage sur le cote des pieces jointes, en reperant les inserees
		# note : traiter_modeles($texte, true) repere les doublons
		# aussi efficacement que propre(), mais beaucoup plus rapidement
		traiter_modeles(join('',$row), true);
		echo afficher_documents_colonne($id_article, 'article');
	} else {
		# ICI GROS HACK
		# -------------
		# on est en new ; si on veut ajouter un document, on ne pourra
		# pas l'accrocher a l'article (puisqu'il n'a pas d'id_article)...
		# on indique donc un id_article farfelu (0-id_auteur) qu'on ramassera
		# le moment venu, c'est-a-dire lors de la creation de l'article
		# dans editer_article.
		echo afficher_documents_colonne(
			0-$GLOBALS['visiteur_session']['id_auteur'], 'article');
	}

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article),'data'=>''));
	echo creer_colonne_droite("",true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article),'data'=>''));
	echo debut_droite("",true);
	
	$oups = ($lier_trad ?
	     generer_url_ecrire("articles","id_article=$lier_trad")
	     : ($new
		? generer_url_ecrire("naviguer","id_rubrique=".$row['id_rubrique'])
		: generer_url_ecrire("articles","id_article=".$row['id_article'])
		));

	$contexte = array(
	'icone_retour'=>icone_inline(_T('icone_retour'), $oups, "article-24.gif", "rien.gif",$GLOBALS['spip_lang_left']),
	'redirect'=>generer_url_ecrire("articles"),
	'titre'=>$titre,
	'new'=>$new?$new:$row['id_article'],
	'id_rubrique'=>$row['id_rubrique'],
	'lier_trad'=>$lier_trad,
	'config_fonc'=>$config_fonc,
	// passer row si c'est le retablissement d'une version anterieure
	'row'=> $id_version
		? $row
		: null
	);

	$milieu = recuperer_fond("prive/editer/article", $contexte);
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article),'data'=>$milieu));

	echo fin_gauche(), fin_page();
}

?>
