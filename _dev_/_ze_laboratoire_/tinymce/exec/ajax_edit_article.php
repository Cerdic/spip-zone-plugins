<?php

# error_log("REQUEST :".var_export($_REQUEST, 1)."\n");

include_spip('base/abstract_sql');
include_spip('inc/texte');
include_spip('inc/sale');

// permet de modifier un element d'article depuis un appel ajax
// A FAIRE : generer l'update qui va bien
function exec_ajax_edit_article_dist() {
	global $flag_auteur, $connect_id_auteur;

	$id_article= _request('id_article');
	$champ= _request('champ');
	$valeur= _request('valeur');
	$valeur= sale($valeur);

	error_log("MODIF DE $id_article:$champ => $valeur");

	$row = spip_abstract_fetsel(array('statut', 'titre', 'id_rubrique'),
					'spip_articles',
					array(array('=', 'id_article', $id_article)));
	if ($row) {
		$statut_article = $row['statut'];
		$id_rubrique = $row['id_rubrique'];
		$statut_rubrique = acces_rubrique($id_rubrique);
	} else {
		$statut_article = '';
		$statut_rubrique = false;
		$id_rubrique = '0';
	}

	$flag_auteur = spip_abstract_fetsel(array('id_auteur'),
					'spip_auteurs_articles',
					array(array('=', 'id_article', $id_article),
						  array('=', 'id_auteur', $connect_id_auteur)),
					'', array(), '1');

	$flag_editable = ($statut_rubrique OR ($flag_auteur AND ($statut_article == 'prepa' OR $statut_article == 'prop' OR $statut_article == 'poubelle')));

	if ($flag_editable) {
		spip_query("UPDATE spip_articles SET $champ='".addslashes($valeur)."', date_modif=NOW(), statut=if(statut='nouveau','prepa',statut) WHERE id_article=$id_article");

		//echo $valeur;
		echo propre($valeur);
	} else {
		echo "pas le droit !!!\n";
	}

}

?>
