<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| fonctions : 
| .. ajout de documents 
| .. origine
| .. enreg auto des docs
+--------------------------------------------+
*/

//
// retourne la Catégorie d'un doc selon choix config
function select_categorie_doc($rub_select)
	{
	$quer_cat=sql_query("SELECT titre FROM spip_rubriques WHERE id_rubrique = $rub_select");
	while ($row_cat=sql_fetch($quer_cat)) {
		if(!function_exists('typo')) { include_spip("inc/texte"); }
		$categorie=typo(supprimer_numero($row_cat['titre']));
		}
	return $categorie;
	}


//
// Retourne tableau du proprio 
function origine_doc($id_doc)
	{
	$tab_ido=array(
		'article'=>'articles', 
		'rubrique'=>'rubriques', 
		'breve'=>'breves');
		#'syndic'=>'syndic');
	
	while (list($k,$v) = each($tab_ido))
		{
		$requete=sql_query("SELECT id_".$k." FROM spip_documents_".$v." WHERE id_document = $id_doc");
			#if($ok_ido=$res=sql_count($requete)) {
			if(sql_count($requete)) {
				$lg=sql_fetch($requete);
				$iddoctype=$lg['id_'.$k];
				$doctype=$k;
				break;
			}
		}
	if(isset($doctype)) {	
		$query=sql_query("SELECT statut FROM spip_".$doctype."s WHERE id_".$doctype." = $iddoctype");
		$row=sql_fetch($query);
		$statut=($row['statut']=="publie") ? '1' : '0';
	}
	return $origine_doc=array($doctype,$iddoctype,$statut);
	}


//
// Ajout des Documents dans le Catalogue de DW2
function ajout_doc_catalogue($doc,$typecat='', $retour='') {	
	
	// si hors appel calc_inclus_auto_doc :
	if(!$typecat) {
		$typecat = $GLOBALS['dw2_param']['type_categorie'];
	}
	
	// origine du doc 
	// on l'a repasse ici because : cal_inclus_auto_doc
	$origine=origine_doc($doc);
	$doctype=$origine[0];
	$iddoctype=$origine[1];

	// si en statut 'publie' OK .. on enregistre
	if($origine[2]=='1') {
		$quet="SELECT id_document, fichier, distant FROM spip_documents WHERE id_document=$doc";
		$resul=sql_query($quet);
		$ro=sql_fetch($resul);
		$distant=$ro['distant'];
		$id_doc=$ro['id_document'];
		
		if($distant=='oui') {
			$url=$ro['fichier'];
			$heberge='distant';
		} else {
			$url='/'.$ro['fichier'];
		}
		
		$nomfichier=substr(strrchr($url,'/'), 1);
			
		// trouver categorie
		$req_cat="SELECT id_secteur, id_rubrique FROM spip_".$doctype."s WHERE id_".$doctype."=".$iddoctype."";
		$rs_cat=sql_query($req_cat);
		$ro_cat=sql_fetch($rs_cat);
		$idsect=$ro_cat['id_secteur'];
		$idrub=$ro_cat['id_rubrique'];

		if ($typecat=="secteur")
			{ $class_cat=$idsect; }
		else
			{ $class_cat=$idrub; }
			
		// enregistre le Doc
		sql_query("INSERT INTO spip_dw2_doc (id_document, nom, url, total, dateur, doctype, id_doctype, categorie, date_crea) 
			VALUES('$id_doc','$nomfichier','$url','0','','$doctype','$iddoctype','".select_categorie_doc($class_cat)."',NOW())");
	}
	if($heberge) {
		sql_query("UPDATE spip_dw2_doc SET heberge='$heberge' WHERE id_document = $id_doc");
	}
		
	if ($retour=="oui") { return $nomfichier; }
}


//
// Enreg. des docs spip vers dw2 en auto
// appel depuis dw2_mesoptions (a chaque hit backoffice !)
# rev. h.02/02/07 ( ++ criteres d'enregistrement des docs)
function calc_inclus_auto_doc($arg='',$typecat) {
	if($arg=='') {
		$where="AND sd.id_type > '3'";
	}
	else {
		$crit=explode(',',$arg);
		if(count($crit)==1) {
			$where="AND (sd.id_type > '3' OR sd.id_type = '$arg')";
		}
		elseif(count($crit)==2) {
			$where="AND (sd.id_type > '3' OR sd.id_type IN ('$crit[0]','$crit[1]'))";
		}
		else {
			$where="";
		}
	}
	$result=sql_select("sd.id_document",
						"spip_documents sd LEFT JOIN spip_dw2_doc dw ON sd.id_document = dw.id_document ",
						"sd.mode = 'document' $where AND dw.id_document IS NULL");

	if(sql_count($result)) {
		while ($row=sql_fetch($result)) {
			$doc=$row['id_document'];
			ajout_doc_catalogue($doc,$typecat);
		}
	}
}

?>