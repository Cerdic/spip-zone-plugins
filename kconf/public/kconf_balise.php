<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_KCONF_dist($p,$id_objet=0,$I='rubrique') {
	// de public/balises:
	$clef = interprete_argument_balise(1,$p);
	if ($clef) {
		if (($id = interprete_argument_balise(2,$p)) !== null)
			$id_objet = $id;
		if ($interface = interprete_argument_balise(3,$p))
			$I = $interface;
		$code = "kconf_balise($I,$id_objet,$clef)";
	} else
		$code = "''";
	$p->code=$code;
	
	$p->interdire_scripts = false;
	return $p;
}

function balise_KCONF_RUB_dist($p) {
	$id_rubrique = champ_sql('id_rubrique', $p);
	return balise_KCONF_dist($p,$id_rubrique,'rubrique');
}

function balise_KCONF_ART_dist($p) {
	$id_article = champ_sql('id_article', $p);
	return balise_KCONF_dist($p,$id_article,'article');
}

function balise_KCONF_LOGO_dist($p,$id_objet=0,$I='rubrique') {
	// de public/balises:
	$clef = interprete_argument_balise(1,$p);
	if ($clef) {
		if (($id = interprete_argument_balise(2,$p)) !== null)
			$id_objet = $id;
		if ($interface = interprete_argument_balise(3,$p))
			$I = $interface;
		$code = "kconf_balise_logo($I,$id_objet,$clef)";
	} else
		$code = "''";
	$p->code=$code;
	
	$p->interdire_scripts = false;
	return $p;
}

function balise_KCONF_LOGO_RUB_dist($p) {
	$id_rubrique = champ_sql('id_rubrique', $p);
	return balise_KCONF_LOGO_dist($p,$id_rubrique,'rubrique');
}

function balise_KCONF_LOGO_ART_dist($p) {
	$id_article = champ_sql('id_article', $p);
	return balise_KCONF_LOGO_dist($p,$id_article,'article');
}

function kconf_balise($I,$id_objet,$clef) {
// 	spip_log("balise cherche $I,$id_objet,$clef");
	list($val,$type,$cascade) = kconf_recevoir_valeur($I,intval($id_objet),$clef);
// 	spip_log("balise trouve $val,$type,$cascade");
	return $val;
}

function kconf_balise_logo($I,$id_objet,$clef) {
	list($val,$type,$cascade) = kconf_recevoir_valeur($I,intval($id_objet),$clef);
	if ($val) {
		$val = _DIR_LOGOS.$val;
		if ($taille = @getimagesize($val)) {
			$taille = " ".$taille[3];
		}
		$val = "<img src='$val' $taille />";
	}
	return $val;
}

// Utiliser kconf comme critère (super expérimental) (pause de gros problèmes avec les valeurs par défaut)
// Ce critère ne doit jamais être utilisé
// Et encore moins documenté !
function critere_kconf($idb, &$boucles, $crit) {
	if (isset($crit->param[0])) {
		$t = $crit->param[0];
		if ($t[0]->type == 'texte') {
			$t = $t[0]->texte;
			list($k,$v) = explode(" ",$t);
			$ret = "s:".strlen($k).":\"$k\";a:3:{i:0;s:".strlen($v).":\"$v\"";
// 			spip_log("critere_kconf $k, $v, $ret");
			$boucle = &$boucles[$idb];
			$boucle->from["kconf"] = "spip_kconf_rubriques";
			$boucle->where[] = array ("'AND'",
				array("'='","'rubriques.id_rubrique'","'kconf.id_rubrique'"),
				array("'LIKE'","'kconf.valeur'","'\'%$ret%\''"),
			);
		}
	}
}

?>
