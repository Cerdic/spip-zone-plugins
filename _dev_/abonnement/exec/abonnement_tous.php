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
$abo_duree = _request('abo_duree');
$abo_commentaire = _request('abo_commentaire');

if($abo_libelle AND intval($abo_montant) AND intval($abo_duree))
{
spip_query("INSERT INTO spip_abonnements (libelle,duree,montant,commentaire) VALUES ("._q($abo_libelle).","._q($abo_montant).","._q($abo_duree).","._q($abo_commentaire) .")");
include_spip('inc/headers');
redirige_par_entete(generer_url_ecrire("abonnement_tous"));
}
elseif(_request('valider')) echo "erreur : les valeurs ne conviennent pas";

if(_request('supprimer_abo')){
 spip_query("DELETE FROM spip_abonnements WHERE id_abonnement='"._request('supprimer_abo')."'");
include_spip('inc/headers');
redirige_par_entete(generer_url_ecrire("abonnement_tous"));
}
	debut_page("abonnements", "", "");
	
	echo recuperer_fond('inc/abonnement_tous');
	
	fin_page();	
}
?>
