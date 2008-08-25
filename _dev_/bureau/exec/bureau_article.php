<?php


function exec_bureau_article_dist() {
	exec_bureau_article_args(intval(_request('id_article')));
}

function exec_bureau_article_args($id_article=0) {

	if ($id_article==0) return;

	include_spip('inc/article_select');
	$select = charger_fonction('article_select','inc');
	$row = $select($id_article);

	bureau_article($id_article,$row);
}

function bureau_article($id_article,$row) {

	include_spip('inc/bureau_presentation');
	include_spip('exec/articles');


	$id_rubrique = $row['id_rubrique'];
	$id_secteur = $row['id_secteur'];
	$statut_article = $row['statut'];
	$titre = $row["titre"];
	$surtitre = $row["surtitre"];
	$soustitre = $row["soustitre"];
	$descriptif = $row["descriptif"];
	$chapo = $row["chapo"];
	$nom_site = $row["nom_site"];
	$url_site = $row["url_site"];
	$texte = $row["texte"];
	$ps = $row["ps"];
	$date = $row["date"];
	$date_redac = $row["date_redac"];
	$extra = $row["extra"];
	$id_trad = $row["id_trad"];

	$dater = charger_fonction('dater', 'inc');
	$editer_mots = charger_fonction('editer_mots', 'inc');
	$editer_auteurs = charger_fonction('editer_auteurs', 'inc');


	$virtuel = (strncmp($row["chapo"],'=',1)!==0) ? '' :
		chapo_redirige(substr($row["chapo"], 1));


	$statut_rubrique = autoriser('publierdans', 'rubrique', $id_rubrique);
	$flag_editable = autoriser('modifier', 'article', $id_article);

	// Est-ce que quelqu'un a deja ouvert l'article en edition ?
	if ($flag_editable
	AND $GLOBALS['meta']['articles_modif'] != 'non') {
		include_spip('inc/drapeau_edition');
		$modif = mention_qui_edite($id_article, 'article');
	} else
		$modif = array();


	// affecter les globales dictant les regles de typographie de la langue
	changer_typo($row['lang']);

	include_spip('exec/articles');
	$contenu = typo($surtitre)
		."<span $dir_lang class='arial1 spip_medium'><b>". typo($surtitre) . "</b></span>"
		. gros_titre($titre,'',false)
		."<span $dir_lang class='arial1 spip_medium'><b>". typo($soustitre) . "</b></span>"
		.debut_cadre_couleur('',true)
		.$date.'<br />'
		.$date_redac.'<br />'
		.fin_cadre_couleur(true)
		.'<b>'.typo($descriptif).'</b><br /><br />'
		.'<b>'.typo($chapo).'</b><br /><br />'
		.typo($texte)
		.typo($ps);



	$menu = '<div>Editer</div>'
		.'<div>Forum</div>'
		.'<div>Supprimer</div>';

	ajax_retour(bureau_fenetre('Article-'.$id_article.' ['.$titre.']',$contenu,$menu,"width:500px;"));
}
?>
