<?php

include_spip('public/boucles');
include_spip('base/echoppe');


//global $tables_jointures;

//$tables_jointures['spip_echoppe_categories'][] = 'spip_echoppe_categories_descriptions';
//$tables_jointures['spip_echoppe_categories_produits'][] = 'spip_echoppe_produits';

function generer_logo($nom_fichier){
	
	$logo = '<img src="IMG/'.$nom_fichier.'" alt="'.textebrut($nom_fichier).'" />';
	if (strlen($nom_fichier) > 0) return $logo;
	
}

function calculer_prix_tvac($prix_htva, $taux_tva){
	if ($taux_tva == 0){
		$taux_tva = lire_config('echoppe/taux_de_tva_par_defaut',21);
	}
	$prix_ttc = $prix_htva + ($prix_htva * ($taux_tva * 100));
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
function calculer_url_achat($_var){
	if (isset($_var)){
		$url = generer_url_public('achat_produit','id_produit='.$_var);
		return $url;
	}
	
}
function calculer_url_achat_rapide($_var){
	if (isset($_var)){
		$url = generer_url_action('ajouter_panier','id_produit='.$_var.'&nombre=1');
		return $url;
	}
	
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

function balise_TOKEN_PANIIER($p){
	$_token_panier = $GLOBALS['auteur_session']['echoppe']['token_panier'];
	$p->code = "$_token_panier";
	return $p;
}
?>
