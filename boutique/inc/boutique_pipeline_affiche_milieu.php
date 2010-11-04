<?php 
function boutique_affiche_milieu($flux){
include_spip('inc/autoriser');
include_spip('inc/utils');
include_spip('inc/composer');
include_spip('inc/assembler');
include_spip('inc/presentation');
if (($flux["args"]["exec"]=="naviguer"&& $flux["args"]["id_rubrique"]) )
{
//moteur de recherche

	//$chaine.="</ul></div>";
	//$chaine.= debut_cadre_enfonce('messagerie-24.gif', true);
	$chaine.= debut_cadre_relief("../plugins/petite_boutique/images/chariot.png", true);
	$chaine .= bouton_block_depliable( _T('Produits de cette rubrique'),true);
	$chaine.= debut_block_depliable(true);
	$chaine .= $bouton_article;
	$id_rubrique=_request(id_rubrique); 
	$ressource = sql_select("*","spip_produits","rubrique=$id_rubrique",'maj DESC');
while($res = sql_fetch($ressource)){
$titre=$res['nom']; 
$id_produit=$res['id_produit']; 
$prix=$res['prix'];
/*$statut=$res['statute']; $id_auteur=$res['id_auteur'];
$ressources = sql_select("*","spip_auteurs","id_auteur='1'");
while($val = sql_fetch($ressources)){
$nom=$val['nom'];$id_auteur=$val['id_auteur'];
if ($statut=='clos'){$puce='<img src="../prive/images/puce-orange.gif">';}else{*/$puce='<img src="../prive/images/puce-verte.gif">';//}
$chaine.='<div style="float:left;width:99%;padding:3px;margin-bottom:3px;margin-top:3px;" id=""><div style="float:left;">'.$puce.'&nbsp;<a href="?exec=produit_voir&id_produit='.$res['id_produit'].'">'.$titre.'</a></div><div style="float:left;margin-left: 12px;">'.$prix.'&nbsp;&euro;</div><div style="float:right;margin-right:10px;font-size:10px"><strong>N&deg; '.$res['id_produit'].'</strong></div></div> ';
//}
}
	$chaine .= fin_block();//$chaine.=fin_cadre_enfonce(true);

	$chaine.=fin_cadre_relief(true);
	$flux["data"] .= $chaine;
//fin moteur
}

return $flux;

}

?> 
