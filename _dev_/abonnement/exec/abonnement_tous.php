<?php
/**
* Copyright (c) 2007
* BoOz  
**/
include_spip('public/assembler');
function exec_abonnement_tous() {

	global $connect_statut, $connect_toutes_rubriques, $table_prefix;

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
$abo_libelle = _request('abo_libelle');
$abo_montant = _request('abo_montant');
$abo_montant = str_replace(',','.',$abo_montant); // On accepte que les points.
$abo_duree = _request('abo_duree');
$abo_periode = _request('abo_unitee');
$abo_commentaire = _request('abo_commentaire');
$id_abonnement = _request('id_abonnement');

if($abo_libelle AND ( intval($abo_montant) OR $abo_montant == '0' )  AND intval($abo_duree) AND _request('valider')){

spip_query("INSERT INTO spip_abonnements (libelle,duree, periode, montant,commentaire) VALUES ("._q($abo_libelle).","._q($abo_duree).","._q($abo_periode).","._q($abo_montant).","._q($abo_commentaire) .")");
include_spip('inc/headers');
redirige_par_entete(generer_url_ecrire("abonnement_tous"));
}
elseif(_request('valider')) echo "erreur : les valeurs ne conviennent pas";

if($abo_libelle AND ( intval($abo_montant) OR $abo_montant == '0' ) AND intval($abo_duree) AND _request('modifier')){
	
spip_query("UPDATE spip_abonnements SET libelle="._q($abo_libelle).",duree="._q($abo_duree).",periode="._q($abo_periode).",montant="._q($abo_montant).",commentaire="._q($abo_commentaire)." WHERE id_abonnement="._q($id_abonnement) );
include_spip('inc/headers');
redirige_par_entete(generer_url_ecrire("abonnement_tous"));
}
elseif(_request('modifier')) echo "erreur : les valeurs ne conviennent pas n";


if(_request('supprimer_abo')){
 spip_query("DELETE FROM spip_abonnements WHERE id_abonnement='"._request('supprimer_abo')."'");
include_spip('inc/headers');
redirige_par_entete(generer_url_ecrire("abonnement_tous"));
}
	debut_page("abonnements", "", "");

	echo recuperer_fond('inc/abonnement_tous',array("id_abonnement"=>"$id_abonnement"));
	
	fin_page();
}
?>