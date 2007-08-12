<?php

function id_abonnement_to_libelle($id_abonnement){
$abonnement = spip_fetch_array(spip_query("select libelle from spip_abonnements where id_abonnement ='$id_abonnement'")) ;
return $abonnement['libelle'] ;
}

function id_abonnement_to_montant($id_abonnement){
$abonnement = spip_fetch_array(spip_query("select montant from spip_abonnements where id_abonnement ='$id_abonnement'")) ;
return $abonnement['montant'] ;
}

?>