<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/forms_base_api');
include_spip('action/editer_article');
include_spip('action/editer_rubrique');

function ajouter_article($id_rubrique,$row){
	$id_donnee = $row['id_donnee'];
	$t = Forms_decrit_donnee($id_donnee,false);
	$c = array();
	$c['titre'] = array_shift($t);
	$c['statut'] = 'publie';
	$texte = "";
	foreach($t as $sujet=>$contenu)
		$texte.="{{{ $sujet }}}\n$contenu\n\n";
	$c['texte'] = $texte;
	$id_article = insert_article($id_rubrique);
	articles_set($id_article, $c);
}

function ajouter_rubrique($id_parent,$row){
	$id_donnee = $row['id_donnee'];
	$t = Forms_decrit_donnee($id_donnee,false);
	$c = array();
	$c['titre'] = array_shift($t);
	$texte = "";
	foreach($t as $sujet=>$contenu)
		$texte.="{{{ $sujet }}}\n$contenu\n\n";
	$c['texte'] = $texte;
	$id_rubrique = insert_rubrique($id_parent);
	revisions_rubriques($id_rubrique, $c);
	return $id_rubrique;
}

function creer_arbo($id_parent,$id_form,$niveau,$bgch=-1,$bdte=-1){
	$res = spip_query(
	    "SELECT * FROM spip_forms_donnees WHERE statut!='poubelle' AND id_form="._q($id_form)
	    . " AND niveau="._q($niveau) 
	    . ($bgch>0?" AND bgch>"._q($bgch):"")
	    . ($bdte>0?" AND bdte<"._q($bdte):"")
	    . " ORDER BY bgch"
	  );
	$enfants = array();
	while ($row = spip_fetch_array($res)){
		if ($row['bdte']-$row['bgch']==1)
			ajouter_article($id_parent,$row);
		else {
			$id_rubrique = ajouter_rubrique($id_parent,$row);
			$enfants[$id_rubrique] = $row;
		}
	}
	foreach($enfants as $id_rubrique=>$row)
		creer_arbo($id_rubrique,$id_form,$niveau+1,$row['bgch'],$row['bdte']);
}

function action_outline_exporter_spip_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_form = _request('id_form');
	creer_arbo(0,$id_form,0);
}
?>