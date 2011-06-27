<?php

/* fonctions obsoletes
 * Il faut utiliser les boucles adequates !
 
function id_abonnement_to_libelle($id_abonnement){
	return sql_getfetsel('libelle', 'spip_abonnements','id_abonnement='.sql_quote($id_abonnement));
}

function id_abonnement_to_montant($id_abonnement){
	return sql_getfetsel('montant', 'spip_abonnements','id_abonnement='.sql_quote($id_abonnement));
}

function id_article_to_titre($id_article){
	return sql_getfetsel('titre', 'spip_articles','id_article='.sql_quote($id_article));
}

function article_visible_par_abonne($id_auteur,$id_article){
	$res = sql_getfetsel(
		"a.id_article",
		array(
			"spip_auteurs_elargis_articles AS a",
			"spip_auteurs_elargis AS b",
			"spip_auteurs AS c"
		),
		array(
			"a.id_auteur_elargi = b.id_auteur",
			"b.id_auteur = c.id_auteur",
			"c.id_auteur = " . sql_quote($id_auteur),
			"a.id_article = " . sql_quote($id_article),
			"a.statut_paiement = " . sql_quote("ok"),
		)
	);

	if($res) 
		return true ;
	else
		return false ;	
}



function pecho_breve($string){
$id_breve = str_replace('breve','',$string) ;
return $id_breve ;
}

*/

function ajouter_des_jours($date,$nb){
	if(!intval($nb))
		return;

	return date('Y-m-d H:i:s',mktime(0, 0, 0, date("m") , date("d") + $nb, date("Y")));
	//$date = sql_fetch(sql_query("select DATE_ADD('$date', INTERVAL $nb DAY) as ladate"));
	//return $date['ladate'];
}

?>
