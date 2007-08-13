<?php

function id_abonnement_to_libelle($id_abonnement){
$abonnement = spip_fetch_array(spip_query("select libelle from spip_abonnements where id_abonnement ='$id_abonnement'")) ;
return $abonnement['libelle'] ;
}

function id_abonnement_to_montant($id_abonnement){
$abonnement = spip_fetch_array(spip_query("select montant from spip_abonnements where id_abonnement ='$id_abonnement'")) ;
return $abonnement['montant'] ;
}

function id_article_to_titre($id_article){
$article = spip_fetch_array(spip_query("select titre from spip_articles where id_article ='$id_article'")) ;
return $article['titre'] ;
}


?>