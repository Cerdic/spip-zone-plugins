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
	$quer_cat=spip_query("SELECT titre FROM spip_rubriques WHERE id_rubrique = $rub_select");
	while ($row_cat=spip_fetch_array($quer_cat)) {
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
		$requete=spip_query("SELECT id_".$k." FROM spip_documents_".$v." WHERE id_document = $id_doc");
			#if($ok_ido=$res=spip_num_rows($requete)) {
			if(spip_num_rows($requete)) {
				$lg=spip_fetch_array($requete);
				$iddoctype=$lg['id_'.$k];
				$doctype=$k;
				break;
			}
		}
	if(isset($doctype)) {	
		$query=spip_query("SELECT statut FROM spip_".$doctype."s WHERE id_".$doctype." = $iddoctype");
		$row=spip_fetch_array($query);
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
		$resul=spip_query($quet);
		$ro=spip_fetch_array($resul);
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
		$rs_cat=spip_query($req_cat);
		$ro_cat=spip_fetch_array($rs_cat);
		$idsect=$ro_cat['id_secteur'];
		$idrub=$ro_cat['id_rubrique'];

		if ($typecat=="secteur")
			{ $class_cat=$idsect; }
		else
			{ $class_cat=$idrub; }
			
		// enregistre le Doc
		spip_query("INSERT INTO spip_dw2_doc (id_document, nom, url, total, dateur, doctype, id_doctype, categorie, date_crea) 
			VALUES('$id_doc','$nomfichier','$url','0','','$doctype','$iddoctype','".select_categorie_doc($class_cat)."',NOW())");
	}
	if($heberge) {
		spip_query("UPDATE spip_dw2_doc SET heberge='$heberge' WHERE id_document = $id_doc");
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
	$query="SELECT sd.id_document FROM spip_documents sd 
			LEFT JOIN spip_dw2_doc dw ON sd.id_document = dw.id_document 
			WHERE sd.mode = 'document' $where 
			AND dw.id_document IS NULL";
		
	$result=spip_query($query);
	if(spip_num_rows($result)) {
		while ($row=spip_fetch_array($result)) {
			$doc=$row['id_document'];
			ajout_doc_catalogue($doc,$typecat);
		}
	}
}

?>
