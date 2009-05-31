<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Function de presentations des pages tabbord
+--------------------------------------------+
*/


function tranches_liste($encours,$nligne,$fl) {
	global $_GET;
	
	$gt=12; // nombre de tranches par ligne ::: modifiable a loisir !!
		
	$fract=ceil($nligne/$fl);
	
	$lgt=1;	
	for ($i=0; $i<$fract; $i++) {
		if(($i+1)==$lgt*$gt) { $br = "<br />"; $lgt++; }
		else { $br =''; }
		$debaff=($i*$fl)+1;
		$f_aff=($i*$fl)+$fl;
		$liais=$i*$fl;
		if ($f_aff<$nligne) { $finaff=$f_aff; $sep = "|"; }
		else { $finaff=$nligne; $sep = ""; }
		
		// recup toutes variabla exec, ... et prepa variable url
		$gethref='';
		$iet=0;
		foreach ($_GET as $k => $v) {
			if($k=='vl') { $v=$liais; }
			$fet = ($iet==0) ? "?" : "&";
			$gethref.=$fet.$k."=".$v;
			$iet++;
		}
		// toujours repasser 'vl', on verifie
		if(!isset($_GET['vl'])) {
			$fet = ($iet==0) ? "?" : "&";
			$gethref.=$fet."vl=".$liais;
			$iet++;#h.04/02
		}
		
		// affichage -- tranche en cours
		if ($debaff==$encours) {
			echo "<span style='background-color:#efefef;'>&nbsp;<b>$debaff - $finaff</b>&nbsp;</span>$sep$br";
		}
		else {
			// derniere
			if(($i+1)==$fract) 
				{ echo "&nbsp;<a href='".$gethref."'>".$debaff." - ".$finaff."</a>&nbsp;".$sep.$br; }
			// std, prem et/ou intermediaire
			else 
				{ echo "&nbsp;<a href='".$gethref."'>".$debaff."</a>&nbsp;".$sep.$br; }
		}
	}
}



# Affiche les puces correspondantes ...
function icone_statut_objet_tabbord($objet,$statut) {
	if($objet=='article' || $objet=='syndic'){
		switch ($statut) {
			case 'publie':
				$puce = 'puce-verte';
				$title = _T('info_article_publie');
				break;
			case 'prepa':
				$puce = 'puce-blanche';
				$title = _T('info_article_redaction');
				break;
			case 'prop':
				$puce = 'puce-orange';
				$title = _T('info_article_propose');
				break;
			case 'refuse':
				$puce = 'puce-rouge';
				$title = _T('info_article_refuse');
				break;
			case 'poubelle':
				$puce = 'puce-poubelle';
				$title = _T('info_article_supprime');
				break;
		}
	}
	elseif($objet=='breve') {
		$puces = array(
		       0 => 'puce-orange-breve',
		       1 => 'puce-verte-breve',
		       2 => 'puce-rouge-breve',
		       3 => 'puce-blanche-breve');

		switch ($statut) {
			case 'prop':
				$puce = $puces[0];
				$title = _T('titre_breve_proposee');
				break;
			case 'publie':
				$puce = $puces[1];
				$title = _T('titre_breve_publiee');
				break;
			case 'refuse':
				$puce = $puces[2];
				$title = _T('titre_breve_refusee');
				break;
			default:
				$puce = $puces[3];
				$title = '';
		}
	}
	elseif($objet=='rubrique') {

		switch ($statut) {
			case 'publie':
				$puce = 'puce-verte';
				$title = $statut;
				break;
			default:
				$puce = 'puce-blanche';
				$title = $statut;
		}
	}
	elseif($objet=='mot') {
		switch ($statut) {
			case 'oui':
				$puce = 'puce-verte';
				$title = _T('tabbord:oui');
				break;
			case 'non':
				$puce = 'puce-poubelle';
				$title = _T('tabbord:non');
				break;
			default:
				$puce = 'puce-blanche';
				$title = _T('tabbord:non_def');
		}
	}
	
	$puce = "$puce.gif";
	Return http_img_pack("$puce", "ico", "", $title);
}


// groupe d'icones menu tabbord
function menu_gen_tabbord() {
	
	echo "<div style='float:left; margin-right:5px; min-height:70px;'>"; 
	echo "<img src='"._DIR_PLUGIN_TABBORD."/img_pack/ico_tabbord1.png' alt='ico_tab' />";
	echo "</div>";
	gros_titre(_T('tabbord:titre_plugin'));
	echo "<div style='clear:both;'></div>";
	
	if(_request('exec')!='tabbord_gen') {
		debut_boite_info();
		icone_horizontale(_T('tabbord:titre_plugin'),generer_url_ecrire("tabbord_gen"),"../"._DIR_PLUGIN_TABBORD."/img_pack/tabbord-24.png","",true,"");
		fin_boite_info();
		echo "<br />";
	}
	
	debut_boite_info();
	icone_horizontale(_T('tabbord:espace_disque'),generer_url_ecrire("tabbord_volume"),"../"._DIR_PLUGIN_TABBORD."/img_pack/disque-24.png","",true,"");
	icone_horizontale(_T('tabbord:taille_base'),generer_url_ecrire("tabbord_base"),"../"._DIR_PLUGIN_TABBORD."/img_pack/base-24.png","",true,"");
	fin_boite_info();
	
	echo "<br />";
	
	debut_boite_info();
	icone_horizontale(_T('tabbord:liste_rubrique_s'),generer_url_ecrire("tabbord_liste","objet=rubrique"),'rubrique-24.gif',"",true,"");
	icone_horizontale(_T('tabbord:liste_article_s'),generer_url_ecrire("tabbord_liste","objet=article"),'article-24.gif',"",true,"");
	icone_horizontale(_T('tabbord:liste_breve_s'),generer_url_ecrire("tabbord_liste","objet=breve"),'breve-24.gif',"",true,"");	
	icone_horizontale(_T('tabbord:liste_mot_s'),generer_url_ecrire("tabbord_mots"),'mot-cle-24.gif',"",true,"");
	icone_horizontale(_T('tabbord:liste_document_s'),generer_url_ecrire("tabbord_documents"),'doc-24.gif',"",true,"");
	icone_horizontale(_T('tabbord:liste_petition_s'),generer_url_ecrire("tabbord_petitions"),'suivi-petition-24.gif',"",true,"");
	icone_horizontale(_T('tabbord:liste_site_s'),generer_url_ecrire("tabbord_sites"),'site-24.gif',"",true,"");
	icone_horizontale(_T('tabbord:liste_auteur_s'),generer_url_ecrire("tabbord_auteurs"),'redacteurs-24.gif',"",true,"");
	icone_horizontale(_T('tabbord:table_metas'),generer_url_ecrire("tabbord_metas"),"../"._DIR_PLUGIN_TABBORD."/img_pack/metas-24.gif","",true,"");
	icone_horizontale(_T('tabbord:arbre_rubriques'),generer_url_ecrire("tabbord_arbre"),"../"._DIR_PLUGIN_TABBORD."/img_pack/tab_arbre.gif","",true,"");
	fin_boite_info();
	
	echo "<br />";
	
	//
	debut_boite_info();
		echo _T('tabbord:credits');
	fin_boite_info();
	
	echo "<br />";
	
}



?>
