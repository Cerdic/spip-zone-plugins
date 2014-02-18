<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


// http://doc.spip.org/@icone_inline
function icone_inline($texte, $lien, $fond, $fonction="", $align="", $ajax=false, $javascript=''){
	global $spip_display;

	if ($fonction == "supprimer.gif") {
		$style = 'icone36 danger';
	} else {
		$style = 'icone36';
		if (strlen($fonction) < 3) $fonction = "rien.gif";
	}
	$style .= " " . substr(basename($fond),0,-4);

	if ($spip_display == 1){
		$hauteur = 20;
		$largeur = 100;
		$title = $alt = "";
	}
	else if ($spip_display == 3){
		$hauteur = 30;
		$largeur = 30;
		$title = "\ntitle=\"$texte\"";
		$alt = $texte;
	}
	else {
		$hauteur = 70;
		$largeur = 100;
		$title = '';
		$alt = $texte;
	}

	$size = 24;
	if (preg_match("/-([0-9]{1,3})[.](gif|png)$/i",$fond,$match))
		$size = $match[1];
	if ($spip_display != 1 AND $spip_display != 4){
		if ($fonction != "rien.gif"){
		  $icone = http_img_pack($fonction, $alt, "$title width='$size' height='$size'\n" .
					  http_style_background($fond, "no-repeat center center"));
		}
		else {
			$icone = http_img_pack($fond, $alt, "$title width='$size' height='$size'");
		}
	} else $icone = '';

	// cas d'ajax_action_auteur: faut defaire le boulot
	// (il faudrait fusionner avec le cas $javascript)
	if (preg_match(",^<a\shref='([^']*)'([^>]*)>(.*)</a>$,i",$lien,$r))
		list($x,$lien,$atts,$texte)= $r;
	else $atts = '';

	if ($align && $align!='center') $align = "float: $align; ";

	$icone = "<a style='$align' class='$style'"
	. $atts
	. (!$ajax ? '' : (' onclick=' . ajax_action_declencheur($lien,$ajax)))
	. $javascript
	. "\nhref='"
	. $lien
	. "'>"
	. $icone
	. (($spip_display == 3)	? '' : "<span>$texte</span>")
	  . "</a>\n";

	if ($align <> 'center') return $icone;
	$style = " style='text-align:center;'";
	return "<div$style>$icone</div>";
}

// Insérer les listes de produits et le bouton de création dans les pages rubriques
function produits_affiche_enfants($flux){
	if ($flux['args']['id_rubrique'] > 0){
		$flux['data'] .= recuperer_fond(
			'prive/objets/liste/produits',
			array('id_rubrique' => $flux['args']['id_rubrique']),
			array(
				'ajax' => true
			)
		);
	
		if (autoriser('creerproduitdans', 'rubrique', $flux['args']['id_rubrique'])){
			//$flux['data'] .= icone_inline(_T('produits:produit_bouton_ajouter'), generer_url_ecrire('produit_edit', 'nouveau=oui&id_rubrique='.$flux['args']['id_rubrique']), find_in_path('prive/themes/spip/images/produits-24.png'), 'creer.gif', 'right');
		}
	}
	
	return $flux;
}

// Compter les produits comme des enfants de rubriques
function produits_objet_compte_enfants($flux){
	if ($flux['args']['objet'] == 'rubrique' and ($id_rubrique = intval($flux['args']['id_objet'])) > 0){
		$statut = $flux['args']['statut'] ? ' and statut='.sql_quote($flux['args']['statut']) : '';
		$flux['data']['produits'] = sql_countsel('spip_produits', 'id_rubrique='.$id_rubrique.$statut);
	}
	return $flux;
}

// Si pas de critère "statut", on affiche que les produits publiés
function produits_pre_boucle($boucle){
	if ($boucle->type_requete == 'produits') {
		$id_table = $boucle->id_table;
		$statut = "$id_table.statut";
		if (!isset($boucle->modificateur['criteres']['statut']) and !isset($boucle->modificateur['tout'])){
			$boucle->where[] = array("'='", "'$statut'", "sql_quote('publie')");
		}
	}
	return $boucle;
}

?>
