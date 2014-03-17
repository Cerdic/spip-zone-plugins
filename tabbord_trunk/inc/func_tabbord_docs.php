<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Functions liees a tabbord_documents
+--------------------------------------------+
*/

# genere tableau des doc (base dw2)
function tableau_def_docs() {

	$query = "SELECT id_document, id_vignette, id_type, fichier, largeur, 
			hauteur, taille, distant, mode, DATE_FORMAT(date,'%d/%m/%Y') as datepub 
			FROM spip_documents ";
			
	// ... avec 'vignette' (miniature)
	$cond = "WHERE id_vignette !='0' ";
	
	$tab_small=array();
	
	$result = spip_query($query.$cond);
	while ($row=spip_fetch_array($result))
		{
		$iddoc=$row['id_document'];
		$idvign=$row['id_vignette'];
		$urlfichier=$row['fichier'];
		$fichier=substr(strrchr($row['fichier'],'/'), 1);
		
		$tab_spipimg[$iddoc]['id_document']=$iddoc;
		$tab_spipimg[$iddoc]['fichier']=$fichier;
		$tab_spipimg[$iddoc]['id_vignette']=$idvign;
		$tab_spipimg[$iddoc]['urlfichier']=$urlfichier;
		$tab_spipimg[$iddoc]['id_type']=$row['id_type'];
		$tab_spipimg[$iddoc]['largeur']=$row['largeur'];
		$tab_spipimg[$iddoc]['hauteur']=$row['hauteur'];
		$tab_spipimg[$iddoc]['taille']=$row['taille'];
		$tab_spipimg[$iddoc]['mode']= $row['mode'];
		$tab_spipimg[$iddoc]['distant']= $row['distant'];
		$tab_spipimg[$iddoc]['date']=$row['datepub'];
		
		// table des miniatures type '-s' et autre vignette personnalisee
		$tab_small[]=$idvign;
		}
	
	reset($tab_small);
	
	// chercher doc pas dans tab_small (sans miniature)
	$result2=spip_query($query);
	while ($row2=spip_fetch_array($result2))
		{
		$iddoc=$row2['id_document'];
		$idvign=$row2['id_vignette'];
		$urlfichier=$row2['fichier'];
		$fichier=substr(strrchr($row2['fichier'],'/'), 1);
		
		if(!in_array($iddoc,$tab_small))
			{
			$tab_spipimg[$iddoc]['fichier']=$fichier;
			$tab_spipimg[$iddoc]['id_document']=$iddoc;
			$tab_spipimg[$iddoc]['id_vignette']=$idvign;
			$tab_spipimg[$iddoc]['urlfichier']=$urlfichier;
			$tab_spipimg[$iddoc]['id_type']=$row2['id_type'];
			$tab_spipimg[$iddoc]['largeur']=$row2['largeur'];
			$tab_spipimg[$iddoc]['hauteur']=$row2['hauteur'];
			$tab_spipimg[$iddoc]['taille']=$row2['taille'];
			$tab_spipimg[$iddoc]['mode']= $row2['mode'];
			$tab_spipimg[$iddoc]['distant']= $row2['distant'];
			$tab_spipimg[$iddoc]['date']=$row2['datepub'];
			}
		}
	return $tab_spipimg;
}


//
// Retourne tableau du proprio d un document
function origine_document($id_doc) {
	$tab_ido=array(
		'article'=>'articles', 
		'rubrique'=>'rubriques', 
		'breve'=>'breves');
		#'syndic'=>'syndic');
	
	while (list($k,$v) = each($tab_ido)) {
		$requete=spip_query("SELECT id_".$k." FROM spip_documents_".$v." WHERE id_document = $id_doc");
		if(spip_num_rows($requete)) {
			$lg=spip_fetch_array($requete);
			$iddoctype=$lg['id_'.$k];
			$doctype=$k;
			break;
		}
	}
	if(isset($doctype)) {	
		$query=spip_query("SELECT statut, titre FROM spip_".$doctype."s WHERE id_".$doctype." = $iddoctype");
		$row=spip_fetch_array($query);
		$statut=($row['statut']=="publie") ? '1' : '0';
		$titre=$row['titre'];
	}
	return $origine_doc=array($doctype,$iddoctype,$titre,$statut);
}


// retourne tbl de docs/types
function totaux_types_documents($tbl_docs) {
	foreach($tbl_docs as $c => $s) {
		$tbl_types[$s['id_type']]['nb']++;
		$r=spip_fetch_array(spip_query("SELECT titre, extension FROM spip_types_documents WHERE id_type=".$s['id_type']));
		$tbl_types[$s['id_type']]['ext']=$r['extension'];
		$tbl_types[$s['id_type']]['titre']=$r['titre'];
	}
	ksort($tbl_types);
	return $tbl_types;
}



?>
