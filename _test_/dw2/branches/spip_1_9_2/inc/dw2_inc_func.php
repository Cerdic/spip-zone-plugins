<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Fonctions communes ...
+--------------------------------------------+
*/



//
// affichage des tranches dans les tableaux
function tranches($encours, $nligne, $fl) {
	global $_GET;
	global $_POST;#h.04/02
	$fract=ceil($nligne/$fl);
	
	$gt=12; // nombre de tranches par ligne ::: modifiable a loisir !!

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
			// exception ajout_det '$k!=sel' #h.04/02
			if($k!='sel') {
				$fet = ($iet==0) ? "?" : "&";
				$gethref.=$fet.$k."=".$v;
				$iet++;
			}
		}
		// toujours repasser 'vl', on verifie
		if(!isset($_GET['vl'])) {
			$fet = ($iet==0) ? "?" : "&";
			$gethref.=$fet."vl=".$liais;
			$iet++;#h.04/02
		}
		//#h.04/02
		// exception ajout_det
		foreach($_POST as $pk => $pv) {
			if($pk=='obj' || $pk=='md' || $pk=='tp' || $pk=='cdw') {
				$fet = ($iet==0) ? "?" : "&";
				$gethref.=$fet.$pk."=".$pv;
				$iet++;
			}
			// except dw2_stats_prd : dates .. date 1
			if($pk=='prdd') {
				$pv = $pv[2]."-".$pv[1]."-".$pv[0];
				
				$fet = ($iet==0) ? "?" : "&";
				$gethref.=$fet.$pk."=".$pv;
				$iet++;
			}
			if($pk=='prdf') {
				if(!empty($pv[0])) {
					$pv=$pv[2]."-".$pv[1]."-".$pv[0];
					$fet = ($iet==0) ? "?" : "&";
					$gethref.=$fet.$pk."=".$pv;
					$iet++;
				}
			}
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




// relier un doc à son article, rubrique, secteur ... d'appartenance.
// uniquement article ou rubrique.
function prepa_appart_doc($doctype, $iddoctype)
	{
	if($doctype!="breve") { $sup_select="id_secteur,"; }
	$query="SELECT $sup_select titre, id_rubrique, statut  
			FROM spip_".$doctype."s 
			WHERE id_".$doctype." = $iddoctype";
	$result=spip_query($query);
	$row=spip_fetch_array($result);

		$tt_doctype=supprimer_numero($row['titre']);
		$id_rub=$row['id_rubrique'];
		$id_sect=$row['id_secteur'];
		$statut=$row['statut'];
		
		switch ($statut)
			{
			case 'publie': $puce = 'verte'; break;
			case 'prepa': $puce = 'blanche'; break;
			case 'prop': $puce = 'orange'; break;
			case 'prive': $puce = 'orange'; break;
			case 'refuse': $puce = 'rouge'; break;
			case 'poubelle': $puce = 'poubelle'; break;
			}
		
		if($doctype=="article" || $doctype=="breve") {
			$r_rub=spip_query("SELECT titre FROM spip_rubriques WHERE id_rubrique=$id_rub");
			$l_rub=spip_fetch_array($r_rub);
			$tt_rubrique=supprimer_numero($l_rub['titre']);
			}
		
		if($id_sect!=$id_rub) {
			$r_sect=spip_query("SELECT titre FROM spip_rubriques WHERE id_rubrique=$id_sect");
			$l_sect=spip_fetch_array($r_sect);
			$tt_secteur=supprimer_numero($l_sect['titre']);
			}
		else { $id_sect=''; }
	return $det_doc=array($tt_doctype,$puce,$id_rub,$tt_rubrique,$id_sect,$tt_secteur);
	}


//
// Fabrique l'html de l'appartenance du document selon chaque $page_affiche
// 
function aff_appart_doc($doctype, $iddoctype) {
	global $page_affiche; // h.31/01 sert encore a qqchose ??
	
	if(!isset($doctype)) {
	return $aff_appart = 
		"<span class='verdana2'>"._T('dw:doc_sans_origine')."</span>\n";
	}
	
	$det_doc=prepa_appart_doc($doctype, $iddoctype);
	$tt_doctype=$det_doc[0];
	$id_rub=$det_doc[2];
	$tt_rubrique=$det_doc[3];
	$id_sect=$det_doc[4];
	$tt_secteur=$det_doc[5];
	$puce="<img src='"._DIR_IMG_DW2."puce-".$det_doc[1]."-breve.gif' border='0' valign='absmiddle'>";
	

	if(_request('exec')=="dw2_outils") {
		switch ($doctype) {
		case 'article':
			$url = generer_url_ecrire("articles", "id_article=".$iddoctype);
			$title = _T('dw:voir_article');
			break;
		case 'breve' :
			$url = generer_url_ecrire("breves_voir", "id_breve=".$iddoctype);
			$title = _T('dw:voir_breve');
			break;
		case 'rubrique' :
			$url = generer_url_ecrire("naviguer", "id_rubrique=".$iddoctype);
			$title = _T('dw:voir_rubrique');
			$indic = "R.";
			break;
		}
		
		$aff_appart = 
		"<span class='verdana2'>".$indic.$iddoctype."</span> . <span class='verdana3'>".
		$puce." <a href='".$url."' title='".$title."'>".$tt_doctype."</a></span>\n";
	}

	else {
		switch ($doctype) {
		case 'article':
			$intitule = _T('dw:doc_de_article');
			$url = generer_url_ecrire("articles", "id_article=".$iddoctype);
			$title = _T('dw:voir_article');
			break;
		case 'breve' :
			$intitule = _T('dw:doc_de_breve');
			$url = generer_url_ecrire("breves_voir", "id_breve=".$iddoctype);
			$title = _T('dw:voir_breve');
			break;
		case 'rubrique' :
			$intitule = _T('dw:doc_de_rubrique');
			$url = generer_url_ecrire("naviguer", "id_rubrique=".$iddoctype);
			$title = _T('dw:voir_rubrique');
			break;
		}
		
		$aff_appart =
		$intitule." [".$iddoctype."] ".$puce." <a href='".$url."' title='".$title."'>".$tt_doctype."</a><br />";
			if(!empty($tt_rubrique)) {
			$aff_appart.=_T('dw:dans_rub')." [".$id_rub."] <a href='".generer_url_ecrire("naviguer", "id_rubrique=".$id_rub)."' title='"._T('dw:voir_rubrique')."'>".$tt_rubrique."</a><br />";
				if($id_sect!='') {
				$aff_appart.= _T('dw:rub_sect')." [".$id_sect."] <a href='".generer_url_ecrire("naviguer", "id_rubrique=".$id_sect)."' title='"._T('dw:voir_rubrique')."'>".$tt_secteur."</a><br />";
				}
			}
	}
	return $aff_appart;
}



//
function liste_documents_art_rub($id_objet,$objet,$mode,$type) {
	
	$q=spip_query("SELECT sdo.id_document, sd.id_type, sd.titre, sd.descriptif, 
					sd.fichier, sd.taille, sd.mode 
					FROM spip_documents_".$objet."s sdo 
					LEFT JOIN spip_documents sd ON sdo.id_document = sd.id_document 
					WHERE sdo.id_".$objet." = $id_objet $type $mode
					");
	$ret_lesdocs=array();
	while($r=spip_fetch_array($q)) {
		// ? dans dw2 ?
		$dw=spip_fetch_array(spip_query("SELECT id_document FROM spip_dw2_doc WHERE id_document=".$r['id_document']));
		$dw=$dw['id_document'];
		
		$id_doc=$r['id_document'];
		$md_doc=$r['mode'];
		$nomfichier = substr(strrchr($r['fichier'],'/'), 1);
		
		// {{ comment. de DEV. SPIP ==> ".. a supprimer avec spip_types_documents .."" }}
		$extension = spip_fetch_array(spip_query("SELECT extension FROM spip_types_documents WHERE id_type=".$r['id_type']));
		$extension = $extension['extension'];
		
		$ret_lesdocs[$id_doc]['fichier']=$nomfichier;
		$ret_lesdocs[$id_doc]['mode']=$md_doc;
		$ret_lesdocs[$id_doc]['extension']=$extension;
		$ret_lesdocs[$id_doc]['titre']=$r['titre'];
		$ret_lesdocs[$id_doc]['descriptif']=$r['descriptif'];
		$ret_lesdocs[$id_doc]['dw']=$dw;
		$ret_lesdocs[$id_doc]['taille']=$r['taille'];
		
		/*
		$ret.=
			"<tr class='tr_liste verdana2'".
			(eregi("msie", $browser_name) ? " onmouseover=\"changeclass(this,'tr_liste_over');\" onmouseout=\"changeclass(this,'tr_liste');\"" :'').
			">\n".
			"<td>".$aff_md."</td>\n".
			"<td><div align='center'>\n".$image."</div></td>\n".
			"<td>".wordwrap($nomfichier,30,' ',1)."<br />".
				"<b>".typo($r['titre'])."</b> :: ".typo($r['descriptif']).
			"</td>\n<td><div align='center'>";
		// enregistrable ?
		if($dw){ $ret.="DW2"; }
		else {
			$ret.="[x]";
		}
		$ret.=
			"</div></td>\n".
			"</tr>\n";
		*/
	}
	#return $ret;
	return $ret_lesdocs;
}



//
// maj taille fichier
function controle_size_doc($id,$url,$id_serveur,$heberge,$anc_taille) {

	if ($id_serveur=='0') {
		if($heberge=='local') {
			$taille = @filesize("..".$url);
		}
		else {
			$buffer = '';
			if($fd = fopen($url, "r")) {
				while (!feof($fd)) {
					$buffer.= fgets($fd, 4096);
				}
				fclose ($fd);
			}
			$taille = strlen($buffer);
		}
	}
	else {
		// funct. connexion_serv ..
		include_spip("inc/dw2_inc_deloc");
		
		//prepa connexion serveur
		$nomfichier = substr(strrchr($url,'/'), 1);
		$query ="SELECT * FROM spip_dw2_serv_ftp WHERE id_serv='$id_serveur'";
		$result= spip_query($query);
		$row = spip_fetch_array($result);
		$ftp_server = $row['serv_ftp'];				// ftp.machin.net
		$port = $row['port'];
		$ftp_user_name = $row['login'];	
		$ftp_user_pass = $row['mot_passe'];
		$host_dir = $row['host_dir'];					//  /host_dir/   ou vide
		if ($host_dir=='') { $host_dir='/'; }								
		$repert_distant = $row['chemin_distant'];		//  doss1/doss2/
		$repertoire_dest = $host_dir.$repert_distant;	//  /host_dir/doss1/doss2/
		
		// connexion
		$retour_conex = connexion_serv($ftp_server, $port, $ftp_user_name, $ftp_user_pass, $repertoire_dest);
		$conex=$retour_conex[0];
		$message_conex=$retour_conex[1];
		if($conex) {
			$taille = ftp_size($conex, $nomfichier);
			@ftp_quit($conex);
		} else {
			$taille='0';
		}
	}
	if ($taille!='0')
		{ spip_query("UPDATE spip_documents SET taille='$taille' WHERE id_document='$id'"); }
	
	return $a=array($taille,$anc_taille);
}



// refonte légère de la function taille_en_octets de spip
// pour un usage en popup !
function taille_octets ($taille) {
	if ($taille < 1024) {$taille = $taille." "._T('dw:abrv_oc');}
	else if ($taille < 1024*1024) {
		$taille = (floor($taille / 102.4)/10)." "._T('dw:abrv_ko');
	} else {
		$taille = (floor(($taille / 1024) / 102.4)/10)." "._T('dw:abrv_mo');
	}
	return $taille;
}

	
//
// total des Docs actifs
function total_compteur_actif() {
	$query=spip_query("SELECT SUM(total) AS tac FROM spip_dw2_doc WHERE statut='actif'");
	$row=spip_fetch_array($query);
	return $row['tac'];
}


//
// premiere date des stats (annee)+(Ymd) --> pour dates : selecteur annee
function premiere_date_stats_dw2() {
	$pd=spip_query("SELECT DATE_FORMAT(date,'%Y') as annee, DATE_FORMAT(date,'%Y-%m-%d') as debstat FROM spip_dw2_stats LIMIT 0,1");
	$rd=spip_fetch_array($pd);
	if(!$annee_stats = $rd['annee']) { $annee_stats=date('Y'); $debut_stats = date('Y-m-d'); }
	else { $debut_stats = $rd['debstat']; }
	return array($annee_stats,$debut_stats);	
}

//
//recup date periode passees en post ou get et restitue
// le sql where_date ; detail date 1 et 2 ; nbr jour (diff_date)
function traitement_dates_periode($prdd,$prdf) {
	$periode=array();
	
	if(is_array($prdd)){
		$ch_prdd = $prdd[2]."-".$prdd[1]."-".$prdd[0];
		$jour1=$prdd[0]; $mois1=$prdd[1]; $annee1=$prdd[2];
		if($prdf[0]=='00' || $prdf[1]=='00' || $prdf[2]=='' ) {
			$where_date="ds.date='$ch_prdd'";
		}
		else {
			$ch_prdf = $prdf[2]."-".$prdf[1]."-".$prdf[0];
			$where_date = "ds.date BETWEEN '$ch_prdd' AND '$ch_prdf'";
			$jour2=$prdf[0]; $mois2=$prdf[1]; $annee2=$prdf[2];
		}
	}
	else {
		$ch_prdd=$prdd;
		$tb_prdd=explode('-',$prdd);
		$jour1=$tb_prdd[2]; $mois1=$tb_prdd[1]; $annee1=$tb_prdd[0];
		$tb_prdf=explode('-',$prdf);
		if($tb_prdf[2]=='00' || $tb_prdf[1]=='00' || $tb_prdf[0]=='') {
			$where_date="ds.date='$ch_prdd'";
		}
		else {
			$ch_prdf=$prdf;
			$where_date = "ds.date BETWEEN '$ch_prdd' AND '$ch_prdf'";
			$tb_prdf=explode('-',$prdf);
			$jour2=$tb_prdf[2]; $mois2=$tb_prdf[1]; $annee2=$tb_prdf[0];
		}
	}
	
	// appel de la page - $prdd pas defini ! on def. aujourd'hui :
	if(!isset($prdd)) {
		$jour1=date('d'); $mois1=date('m'); $annee1=date('Y');
		$where_date="ds.date='$annee1-$mois1-$jour1'";
	}
	
	// diff dates ==> nombre de jour
	$mkd1 = mktime(0,0,0,$mois1,$jour1,$annee1);
	$mkd2 = mktime(0,0,0,$mois2,$jour2,$annee2);
	if(isset($jour2) && ($jour2!='00' || $mois2!='00' || $annee2!='')) {
		$diff_date = (abs($mkd2 - $mkd1)/86400)+1;
	}
	else { $diff_date = '1'; }

	$periode['date1']['jour']=$jour1;
	$periode['date1']['mois']=$mois1;
	$periode['date1']['annee']=$annee1;
	$periode['date2']['jour']=$jour2;
	$periode['date2']['mois']=$mois2;
	$periode['date2']['annee']=$annee2;
	$periode['sql']=$where_date;
	$periode['diff']=$diff_date;

	return $periode;
}

?>
