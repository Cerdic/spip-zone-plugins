<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

function Forms_nouveau_champ($id_form,$type){
	$res = spip_query("SELECT champ FROM spip_forms_champs WHERE id_form="._q($id_form)." AND type="._q($type));
	$n = 1;
	$champ = $type.'_'.strval($n);
	while ($row = spip_fetch_array($res)){
		$lenumero = split('_', $row['champ'] );
		$lenumero = intval(end($lenumero));
		if ($lenumero>= $n) $n=$lenumero+1;
	}
	$champ = $type.'_'.strval($n);
	return $champ;
}
function Forms_insere_nouveau_champ($id_form,$type,$titre,$champs=""){
	if (!strlen($champs))
		$champ = Forms_nouveau_champ($id_form,$type);
	$rang = 0;
	$res = spip_query("SELECT max(rang) AS rangmax FROM spip_forms_champs WHERE id_form="._q($id_form));
	if ($row = spip_fetch_array($res))
		$rang = $row['rangmax'];
	$rang++;
	spip_abstract_insert(
		'spip_forms_champs',
		'(id_form,champ,rang,titre,type,obligatoire,extra_info)',
		'('._q($id_form).','._q($champ).','._q($rang).','._q($titre).','._q($type).",'non','')");

	return $champ;
}
function Forms_nouveau_choix($id_form,$champ){
	$n = 1;
	$res = spip_query("SELECT choix FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND champ="._q($champ));
	while ($row = spip_fetch_array($res)){
		$lenumero = split('_', $row['choix']);
		$lenumero = intval(end($lenumero));
		if ($lenumero>= $n) $n=$lenumero+1;
	}
	$choix = $champ.'_'.$n;
	return $choix;
}
function Forms_insere_nouveau_choix($id_form,$champ,$titre){
	$choix = Forms_nouveau_choix($id_form,$champ);
	$rang = 0;
	$res = spip_query("SELECT max(rang) AS rangmax FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND champ="._q($champ));
	if ($row = spip_fetch_array($res))
		$rang = $row['rang'];
	$rang++;
	spip_abstract_insert("spip_forms_champs_choix","(id_form,champ,choix,titre,rang)","("._q($id_form).","._q($champ).","._q($choix).","._q($titre).","._q($rang).")");
	return $choix;
}

?>