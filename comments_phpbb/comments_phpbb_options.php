<?php

$nb_commentaires = array();
$topic_ids = array();
define('ARTICLES_PHPBB_TABLE', $GLOBALS['table_prefix'].'_articles_phpbb');
define('PHPBB_BASE', lire_config('comments_phpbb/phpbb_base'));
define('PHPBB_PREFIX', lire_config('comments_phpbb/phpbb_prefix'));

function calcule_nb_commentaires($id_article)
{
	global $nb_commentaires;

	$id_article = intval($id_article);

	if(!isset($nb_commentaires[$id_article]))
	{
		$query = sql_select(array('COUNT(p.post_id) AS nb_commentaires'),ARTICLES_PHPBB_TABLE.' a, '.PHPBB_PREFIX.'posts p
					WHERE a.id_article='.intval($id_article).'
					AND a.topic_id=p.topic_id');
		$row = spip_fetch_array($query);
		$nb_commentaires[$id_article] = intval($row['nb_commentaires']-1);
	}

	return $nb_commentaires[$id_article];
}

function balise_NB_COMMENTAIRES($p) {
	$_type = $p->type_requete;

	if ($_type == 'articles')
	{
		$_id_article = interprete_argument_balise(1,$p);

		if (!$_id_article)
			$_id_article = champ_sql('id_article', $p);

		$p->code = "calcule_nb_commentaires($_id_article)";
	}

	$p->interdire_scripts = false;
	return $p;
}

function calcule_topic_id($id_article)
{
	global $topic_ids;

	$id_article = intval($id_article);

	if(!isset($topic_ids[$id_article]))
	{
		$query = sql_select('topic_id',ARTICLES_PHPBB_TABLE,'id_article='.intval($id_article));
		$row = sql_fetch($query);
		$topic_ids[$id_article] = intval($row['topic_id']);
	}

	return $topic_ids[$id_article];
}

function balise_TOPIC_ID($p) {
	$_type = $p->type_requete;

	if ($_type == 'articles')
	{
		$_id_article = interprete_argument_balise(1,$p);

		if (!$_id_article)
			$_id_article = champ_sql('id_article', $p);

		$p->code = "calcule_topic_id($_id_article)";
	}

	$p->interdire_scripts = false;
	return $p;
}
?>