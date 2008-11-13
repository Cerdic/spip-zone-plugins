<?php

function select_lang($les_langues, $nom, $value, $style){
	$les_langues = explode(",",$les_langues);
	$select .= '<select name="'.$nom.'" class="'.$style.'">';
	
	foreach ($les_langues as $option){
		$select .= '<option value="'.$option.'"';
		if ($value == $option) { $select .= ' selected="selected" '; }
		$select .= '>'.traduire_nom_langue($option).'</option>';
	}
	
	$select .= '</select>';
	return $select;
}

function generer_url_inscription(){
	return "";
}

function calculer_prix_tvac($prix_htva, $taux_tva){
	if ($taux_tva == 0){
		$taux_tva = lire_config('echoppe/taux_de_tva_par_defaut',21);
	}
	$prix_ttc = $prix_htva + ($prix_htva * ($taux_tva / 100));
	$prix_ttc = round($prix_ttc, lire_config('echoppe/nombre_chiffre_apres_virgule',2));
	return $prix_ttc;
}

function calculer_taux_tva($taux_tva){
	if ($taux_tva == 0){
		$taux_tva = lire_config('echoppe/taux_de_tva_par_defaut',21);
	}
	return $taux_tva;
}

function vide_si_zero($_var){
	if ($_var == 0){
		$_var = ""; 
	}
	return $_var;
}
function zero_si_vide($_var){
	if ($_var == ""){
		$_var = 0; 
	}
	return $_var;
}
function calculer_url_achat($_var){
	if (isset($_var)){
		$_page = lire_config('echoppe/squelette_panier','echoppe_panier');
		$url_result = generer_url_public($_page,'id_produit='.$_var);
		$url = generer_url_action('echoppe_ajouter_panier','id_produit='.$_var.'&quantite=1&achat_rapide=non');
		return $url;
	}
	
}
function calculer_url_achat_rapide($_var){
	if (isset($_var)){
		$url = generer_url_action('echoppe_ajouter_panier','id_produit='.$_var.'&quantite=1&achat_rapide=oui');
		return $url;
	}
	
}
function calculer_stock($_id_produit){
	$_sql_stock = "SELECT quantite FROM spip_echoppe_stock_produits WHERE id_produit = '$_id_produit';";
	$_res_stock = spip_query($_sql_stock);
	while($_la_quantite = spip_fetch_array($_res_stock)){
		$_quantite = $_quantite + $_la_quantite['quantite'];
	}
	$_quantite = zero_si_vide($_quantite);
	return $_quantite;
}

function calculer_balise_url_echoppe($p, $nom){
	$_id = interprete_argument_balise(1,$p);
    if (!$_id) $_id = champ_sql('id_'.$nom, $p);
    $p->code = "vider_url(generer_url_public($nom,'id_'.$nom.'='.$_id))";
    $p->interdire_scripts = false;
    return $p;
}

/*=============================BALISES===============================*/
function balise_PRIX_TVAC($p){
	$_prix = champ_sql('prix_base_htva', $p);
	$_tva = champ_sql('tva', $p);
	$p->code = "calculer_prix_tvac($_prix,$_tva)";
	return $p;
}

function balise_TAUX_TVA($p){
	$_tva = champ_sql('tva', $p);
	$p->code = "calculer_taux_tva($_tva)";
	return $p;
}

function balise_HAUTEUR($p){
	$_hauteur = champ_sql('hauteur', $p);
	$p->code = "vide_si_zero($_hauteur)";
	return $p;
}
function balise_POIDS($p){
	$_poids = champ_sql('poids', $p);
	$p->code = "vide_si_zero($_poids)";
	return $p;
}
function balise_LARGEUR($p){
	$_largeur = champ_sql('largeur', $p);
	$p->code = "vide_si_zero($_largeur)";
	return $p;
}
function balise_LONGUEUR($p){
	$_longueur = champ_sql('longueur', $p);
	$p->code = "vide_si_zero($_longueur)";
	return $p;
}
function balise_URL_ACHAT($p){
	$_id_produit = champ_sql('id_produit', $p);
	$p->code = "calculer_url_achat($_id_produit)";
	return $p;
}
function balise_URL_ACHAT_RAPIDE($p){
	$_id_produit = champ_sql('id_produit', $p);
	$p->code = "calculer_url_achat_rapide($_id_produit)";
	return $p;
}

function balise_URL_PRODUIT_dist($p) {
    return  calculer_balise_url_echoppe($p, 'produit');
}

function balise_URL_CATEGORIE_dist($p) {
    return  calculer_balise_url_echoppe($p, 'categorie');
}

function generer_URL_PANIER($p){
	$_page = lire_config('echoppe/squelette_panier','echoppe_panier');
	$_url = generer_url_public($_page);
	return $_url;
}


function balise_TOTAL_STOCK($p){
	$_id_produit = champ_sql('id_produit', $p);
	$p->code = "calculer_stock($_id_produit)";
	$p->interdire_scripts = false;
	return $p;
}


/*=============================BOUCLES================================*/

function boucle_ECHOPPE_PRODUITS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mstatut = $id_table .'.statut';
	// Restreindre aux elements publies, sauf si le critere statut est utilise
	if (!isset($boucle->modificateur['criteres']['statut'])) {
		array_unshift($boucle->where,array("'<>'", "'$mstatut'", "'\\'poubelle\\''"));
		array_unshift($boucle->where,array("'<>'", "'$mstatut'", "'\\'propose\\''"));
		array_unshift($boucle->where,array("'<>'", "'$mstatut'", "'\\'prepa\\''"));
	}
	return calculer_boucle($id_boucle, $boucles); 
}

function boucle_ECHOPPE_HIERARCHIE_dist($id_boucle, &$boucles) {
	//var_dump($id_boucle);
	//var_dump($boucles);
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table . ".id_categorie";
	// Si la boucle mere est une boucle RUBRIQUES il faut ignorer la feuille
	// sauf en presence du critere {tout} (vu par phraser_html)
	$boucle->hierarchie = 'if (!($id_categorie = intval('
	. calculer_argument_precedent($boucle->id_boucle, 'id_categorie', $boucles)
	. ")))\n\t\treturn '';\n\t"
	. '$hierarchie = '
	. (isset($boucle->modificateur['tout']) ? '",$id_categorie"' : "''")
	. ";\n\t"
	. 'while ($id_categorie = sql_getfetsel("id_parent","spip_echoppe_categories","id_categorie=" . $id_categorie,"","","", "", $connect)) { 
		$hierarchie = ",$id_categorie$hierarchie";
	}
	if (!$hierarchie) return "";
	$hierarchie = substr($hierarchie,1);';
	
	$boucle->where = array('"echoppe_categories.id_categorie IN ($hierarchie)"');
    
    $order = "FIELD($id_table, \$hierarchie)";
	if ($boucle->default_order[0] != " DESC")
		$boucle->default_order[] = "\"$order\"";
	else
		$boucle->default_order[0] = "\"$order DESC\"";
	return calculer_boucle($id_boucle, $boucles); 
}
?>
