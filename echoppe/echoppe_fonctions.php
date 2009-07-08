<?php
include_spip('inc/echoppe');


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

function generer_liste_squelette_paiment($selection){
	$les_chemins = find_all_in_path('prestataires/paiement/', '.html$');
	
	$select_prestataire = "<select name='modele' class='forml'>";
	foreach ($les_chemins as $key => $value){
		$select_prestataire .= "<option value='$key' ";
		if ($selection == $key) $select_prestataire .= " SELECTED = 'SELECTED' ";
		$select_prestataire .= " >$key</option>";
	}
	$select_prestataire .= "</select>";
	return $select_prestataire;
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

function calculer_url_achat($_var,$quantite,$redirect){
	if (isset($_var)){
		$_page = lire_config('echoppe/squelette_panier','echoppe_panier');
		$args_url = 'id_produit='.$_var;
		$args_url .= "&quantite=".$quantite;
		if ($redirect){
			$args_url .= "&page=".$redirect;
		}else{
			$args_url .= "&page=".$_page."&".$_SERVER["QUERY_STRING"];
		}
		$url = generer_url_action('echoppe_ajouter_panier',$args_url,"&");
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
	$sql_quantite = sql_select('quantite','spip_echoppe_stocks',array("ref_produit = '".$_id_produit."'"));
	while($_la_quantite = sql_fetch($sql_quantite)){
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

function calculer_url_paiement(){
	$action = "generer_formulaire_paiement";
	return generer_url_action($action);
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
	$quantite = interprete_argument_balise(1,$p);
	$redirect = interprete_argument_balise(2,$p);
	//var_dump($redirect);
	if (!$quantite) $quantite = "1";
	if (!$redirect) $redirect = "0";
	$_id_produit = champ_sql('id_produit', $p);
	$p->code = "calculer_url_achat($_id_produit,$quantite,$redirect)";
	return $p;
}
function balise_URL_ACHAT_RAPIDE($p){
	$_id_produit = champ_sql('id_produit', $p);
	$p->code = "calculer_url_achat_rapide($_id_produit)";
	return $p;
}

function balise_URL_PAIEMENT_dist($p){
	$p->code = "calculer_url_paiement()";
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
	$_ref_produit = champ_sql('ref_produit', $p);
	$p->code = "calculer_stock($_ref_produit)";
	$p->interdire_scripts = false;
	return $p;
}

function balise_LISTE_SKEL_PAIEMENT($p){
	$selection = interprete_argument_balise(1,$p);
	$p->code="generer_liste_squelette_paiment($selection)";
	$p->interdire_script = false;
	return $p;
}

function balise_URL_RETOUR_PAIEMENT_OK ($p) {
		$_token_paiemet = date("YmdHms");
		$p->code = "generer_url_paiement_ok($_token_paiemet)";
		$p->interdire_script = false;
		return $p;
}


function balise_URL_RETOUR_PAIEMENT_ERREUR ($p) {
		$_token_paiemet = date("YmdHms");
		$p->code = "generer_url_paiement_erreur($_token_paiemet)";
		$p->interdire_script = false;
		return $p;
}


function generer_url_paiement_ok($token_paiement){
	include_spip('inc/utils');
	include_spip('inc/session');
	$_token_panier = session_get('echoppe_token_panier');
	$_token_client = session_get('echoppe_token_client');
	$_token_paiemet = md5($_token_panier.$_token_client);
	$page = "echoppe_valider_paiement";
	$url = generer_url_action($page,"token_paiement=".$_token_paiemet,"&");
	return $url;
}

function generer_url_paiement_erreur($token_paiement){
	include_spip('inc/utils');
	include_spip('inc/session');
	$_token_panier = session_get('echoppe_token_panier');
	$_token_client = session_get('echoppe_token_client');
	$_token_paiemet = md5($_token_panier.$_token_client);
	$page = "echoppe_invalider_paiement";
	$url = generer_url_action($page,"token_paiement=".$_token_paiemet,"&");
	return $url;
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

function boucle_ECHOPPE_CATEGORIES_dist($id_boucle, &$boucles) {
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
