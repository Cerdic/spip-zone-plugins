<?php

include_spip('base/abstract_sql');
include_spip('base/db_mysql.php');
include_spip('inc/utils.php');

function formulaires_ajoutfraisdon_charger_dist($id_fraisdon=0){
	$valeurs = array(
		'anneecomptable'=> date('Y'),
		'datefrais'=> date('d-m-Y'),
		'typefrais'=> '1frais',
		'km'=> '',
		'coef'=> lire_config("spip_fraisdon/coefkm_fraisdon", "1"),
		'montant'=> '',
		'titre'=> '',
		'choixremb'=> '1remboursement',
		'id_auteur'=> '',
		'etat'=> '',
		'id_fraisdon_modif'=> ''
	);

	if ($id_fraisdon > 0) {
		$result= spip_query("SELECT * FROM spip_fraisdons WHERE id_fraisdon=$id_fraisdon");
		if (sql_count($result) > 0) {
			$row = spip_fetch_array($result);
			$id_fraisdon= $row['id_fraisdon'];
			$valeurs['anneecomptable']= $row['anneecomptable'];
			$valeurs['typefrais']= $row['typefrais'];
			$valeurs['km']= $row['km'];
			$valeurs['coef']= $row['coef'];
			$valeurs['montant']= $row['montant'];
			$valeurs['titre']= $row['titre'];
			$valeurs['choixremb']= $row['choixremb'];
			$valeurs['etat']= $row['etat'];
			$valeurs['id_auteur']= $row['id_auteur'];
			$datefrais= $row['datefrais'];
			$valeurs['datefrais']= substr($datefrais,8,2) ."-". substr($datefrais,5,2) ."-". substr($datefrais,0,4);
		}
	} else {
		$id_fraisdon= 0;
		$valeurs['id_auteur']= $GLOBALS['auteur_session']['id_auteur'];
		$valeurs['etat']= "1saisie";
	}
	$valeurs['id_fraisdon_modif']= $id_fraisdon;
	$valeurs['id_fraisdon_valid']= '';
	$valeurs['id_fraisdon_suppr']= '';
	$valeurs['_hidden'] = "<input type='hidden' name='id_fraisdon_modif' value='$id_fraisdon' />";

	return $valeurs;
}

function formulaires_ajoutfraisdon_traiter_dist(){
	$id_fraisdon= _request('id_fraisdon_modif');
	$id_auteur= _request('id_auteur');
	$anneecomptable= _request('anneecomptable');
	$datefrais= _request('datefrais');
	$datefrais= substr($datefrais,6,4) .'-'.substr($datefrais,3,2) .'-'.substr($datefrais,0,2);
	$typefrais= _request('typefrais');
	$titre= _request('titre');
	$km= _request('km');
	$coef= _request('coef');
	$montant= _request('montant');
	$choixremb= _request('choixremb');
	$etat= _request('etat');

	if ($id_fraisdon > 0) {
		$query= "UPDATE spip_fraisdons SET id_auteur=$id_auteur, anneecomptable=$anneecomptable, datefrais="._q($datefrais).", typefrais="._q($typefrais).", titre="._q($titre).", km=$km, coef=$coef, montant=$montant, choixremb="._q($choixremb).", etat="._q($etat)." WHERE id_fraisdon=$id_fraisdon";
		$result= sql_query($query);
		$msg= _T('fraisdon:notefrais_modif');
	} else {
		$regroupement= '';
		$fields= "(id_auteur, regroupement, anneecomptable, datefrais, typefrais, titre, km, coef, montant, choixremb, etat)";
		$values= "($id_auteur, "._q($regroupement).", $anneecomptable, "._q($datefrais).", "._q($typefrais).", "._q($titre).", $km, $coef, $montant, "._q($choixremb).", "._q($etat).")";
		$result= sql_insert("spip_fraisdons", $fields, $values);
		$msg= _T('fraisdon:notefrais_enreg');
	}

	return array('message_ok'=>$msg);
}

?>
