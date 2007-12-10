<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| genere affiche l'arborescence des rubriques
+--------------------------------------------+
*/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function cherche_rubrique($idr) {
	// 
	$q = spip_query("SELECT id_rubrique, titre, statut, id_parent, 
				DATE_FORMAT(date,'%d/%m/%Y') as datepub
				FROM spip_rubriques 
				WHERE id_parent='$idr' 
				ORDER BY id_rubrique");

	while($r=spip_fetch_array($q)){
		$sq=spip_query("SELECT id_article FROM spip_articles 
						WHERE id_rubrique=".$r['id_rubrique']." AND statut='publie'");
		$nart=spip_num_rows($sq);
		$tbl_rub[$r['id_rubrique']]['id_parent']=$r['id_parent'];
		$tbl_rub[$r['id_rubrique']]['titre']=$r['titre'];
		$tbl_rub[$r['id_rubrique']]['statut']=$r['statut'];
		$tbl_rub[$r['id_rubrique']]['date']=$r['datepub'];
		$tbl_rub[$r['id_rubrique']]['art']=$nart;
		$enfant=cherche_rubrique($r['id_rubrique']);
		$tbl_rub[$r['id_rubrique']]['enfants']=$enfant;

	}
	return $tbl_rub;
}


/*
[titre] => SekiSekoa
[statut] => publie
[date] => 18/05/2006
[art] => 5 //nb articles
[enfants] => Array
*/
            
function affiche_elem_arbre($array,$avec_titre) {

	foreach($array as $k => $v) {
		if($v['id_parent']=='0') { $logorub='secteur';  $haut_secteur='height:5px;';	}
		else { $logorub='rubrique'; }
			
		$ret.="<div class='blocrub_at verdana1'>".
				"<div class='colpuce'>".icone_statut_objet_tabbord('breve',$v['statut'])."</div>".
				"<a href='".generer_url_ecrire('naviguer','id_rubrique='.$k)."'>&nbsp;&nbsp;&nbsp;".
				http_img_pack($logorub.'-12.gif','ico','',textebrut('('.$k.') '.$v['titre'])).
				"&nbsp;";
				
		if($avec_titre) { 
			$ret.=$k."<br />".propre($v['titre'])."</a>";
			$ret.="<br /><span class='datetab'>".$v['date']."&nbsp;</span>";
			if($v['art']>0) {
				$ret.="<div class='lg_art' title='"._T('tabbord:article_publies')."'>"._T('tabbord:leg_art_nbsp').$v['art']."</div>";
			}
		}
		else { $ret.="<br />".$k."</a>"; };
		
		$ret.="</div>";
		
		if(is_array($v['enfants'])) {
			$ret.="<div class='collien'><img src='"._DIR_PLUGIN_TABBORD."/img_pack/relier.gif' alt='rel' /></div>";
			$ret.= "<div class='colint_at'>";
				$ret.= affiche_elem_arbre($v['enfants'],$avec_titre);
			$ret.= "</div>";
		}

		
		$ret.="<div style='clear:both; $haut_secteur'></div>";
	}
	return $ret;	
}


function exec_tabbord_arbre() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;


//
// requis
//
include_spip('inc/tabbord_pres');


//
// prepa
//
// type de liste produite
$objet=_request('objet');
if($objet=='rubrique') {
	$seq = "id_rubrique, id_parent as parent,";
	$date = "date";
	$lien = "naviguer";
}





//
// affichage
//
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('tabbord:titre_plugin'), "suivi", "tabbord_gen", '');

	echo "<br />";


	// Vérifier si Admin principal du site
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	icone_horizontale(_T('tabbord:titre_plugin'),generer_url_ecrire("tabbord_gen"),"../"._DIR_PLUGIN_TABBORD."/img_pack/tabbord-24.png","",true,"");
	
	gros_titre(_T('tabbord:arborescence_').$GLOBALS['meta']['nom_site']);
	
	echo "<div style='clear:both;'></div>";
	echo "<br />";
	
	echo "<div style='margin-left:50px; width:100%;'>";
		#debut_cadre_relief('');
		
		$tbl_rub = cherche_rubrique(0);

		$avec_titre=true;
		if($at=intval(_request('at'))) { $avec_titre=false; }
		 
		echo affiche_elem_arbre($tbl_rub,$avec_titre);
		
		
		#fin_cadre_relief();
	echo "</div>";
	
	echo "<div style='clear:both;'></div>";
	icone_horizontale(_T('tabbord:titre_plugin'),generer_url_ecrire("tabbord_gen"),"../"._DIR_PLUGIN_TABBORD."/img_pack/tabbord-24.png","",true,"");
	
//
//
echo fin_gauche(),fin_page();
}
?>
