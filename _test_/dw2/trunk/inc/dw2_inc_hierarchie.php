<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| - Generer tableau hierarchique  :
|   Docs, articles, rubriques
| - Niveau restriction / dependance
+-------------------------------------------+
*/

//
function titre_maitre_dependance($table,$id) {
	if($table) {
		$q=spip_query("SELECT titre FROM spip_".$table."s WHERE id_$table=$id");
		$r=spip_fetch_array($q);
		return $r['titre'];
	} 
	else return;
}

// recup id_parent
function mum_rub($id_rubrique) {
	if($id_rubrique=='') return;
	$r=spip_query("SELECT id_parent FROM spip_rubriques WHERE id_rubrique=$id_rubrique");
	$s=spip_fetch_array($r);
	if ($s['id_parent']!=$id_rubrique) {
		return $s['id_parent'];
	}
	else return '';
}

// reucp rub parent article
function mum_art($id_article) {
	if($id_article=='') return;
	$q=spip_query("SELECT id_rubrique FROM spip_articles WHERE id_article=$id_article");
	$r=spip_fetch_array($q);
	return $r['id_rubrique'];
}

// genere tableau hierarchie rubriques
function complete_rub($id_rubrique) {
	$hierarchie=array();
	while($id_rubrique = mum_rub($id_rubrique)) {
		$hierarchie[]=$id_rubrique;
	}
	return $hierarchie;
}

// former tableau hierarchie du doc
function hierarchie_doc($id_document) {

	$q=spip_query("SELECT doctype, id_doctype FROM spip_dw2_doc WHERE id_document=$id_document");
	$r=spip_fetch_array($q);
	$doctype=$r['doctype'];
	$id_doctype=$r['id_doctype'];
	
	$hierarchie=array();
	
	if($doctype=='article') {
		$hierarchie['art']=$id_doctype;
		$prim_rub = mum_art($id_doctype);
	}
	else {
		$hierarchie['art']='0';
		$prim_rub=$id_doctype;
	}
	$hierarchie['rubs'] = complete_rub($prim_rub);
array_unshift($hierarchie['rubs'],$prim_rub);
	return $hierarchie;
}

// former tableau hierarchie de article
function hierarchie_art($id_article) {
	$hierarchie=array();
	$prim_rub = mum_art($id_article);
	$hierarchie['rubs']=complete_rub($prim_rub);
	array_unshift($hierarchie['rubs'],$prim_rub);
	return $hierarchie;
}

// former tableau hierarchie de rubrique
function hierarchie_rub($id_rubrique) {
	$hierarchie=array();
	$hierarchie['rubs']=complete_rub($id_rubrique);
	return $hierarchie;
}



// analyse dépendance restriction . produit array
// h.19/01/07 ... optimiser cette fonction ##################
// $perso .. si oui = 'true'
function dependance_restriction($id_objet, $type, $hierarchie, $perso="") {

	if($type=='document') {
		$q=spip_query("SELECT restreint FROM spip_dw2_acces_restreint WHERE id_document=$id_objet");
		if(spip_num_rows($q) && $perso){
			$r=spip_fetch_array($q);
			return $retour=array($r['restreint'],$type,$id_objet);
		}
		else {
			$q=spip_query("SELECT restreint FROM spip_dw2_acces_restreint WHERE id_article=".$hierarchie['art']);
			if(spip_num_rows($q)) {
				$r=spip_fetch_array($q);
				return $retour=array($r['restreint'],'article',$hierarchie['art']);
			}
			else {
				foreach($hierarchie['rubs'] as $idrub) {
					$q=spip_query("SELECT restreint FROM spip_dw2_acces_restreint WHERE id_rubrique=".$idrub);
					if(spip_num_rows($q)) {
						$r=spip_fetch_array($q);
						return $retour=array($r['restreint'],'rubrique',$idrub);
					}
				}
				// aucune dépendance donc : libre (0)
				return $retour=array('0');
			}
		}
	}
	
	#
	if($type=='article') {
		$q=spip_query("SELECT restreint FROM spip_dw2_acces_restreint WHERE id_article=$id_objet");
		if(spip_num_rows($q) && $perso) {
			$r=spip_fetch_array($q);
			return $retour=array($r['restreint'],$type,$id_objet);
		}
		else {
			foreach($hierarchie['rubs'] as $idrub) {
				$q=spip_query("SELECT restreint FROM spip_dw2_acces_restreint WHERE id_rubrique=".$idrub);
				if(spip_num_rows($q)) {
					$r=spip_fetch_array($q);
					return $retour=array($r['restreint'],'rubrique',$idrub);
				}
			}
			// aucune dépendance donc : libre (0)
			return $retour=array('0');
		}
	}
	
	#
	if($type=='rubrique') {
		$q=spip_query("SELECT restreint FROM spip_dw2_acces_restreint WHERE id_rubrique=$id_objet");
		if(spip_num_rows($q) && $perso){
			$r=spip_fetch_array($q);
			return $retour=array($r['restreint'],$type,$id_objet);
		}
		else {
			foreach($hierarchie['rubs'] as $idrub) {
				$q=spip_query("SELECT restreint FROM spip_dw2_acces_restreint WHERE id_rubrique=".$idrub);
				if(spip_num_rows($q)) {
					$r=spip_fetch_array($q);
					return $retour=array($r['restreint'],'rubrique',$idrub);
				}
			}
			// aucune dépendance donc : libre (0)
			return $retour=array('0');
		}
		
	}
	
	#
	if($type=='racine') {
		$q=spip_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent='0'");
		$secteurs=array();
		while($r=spip_fetch_array($q)) {
			$secteurs[]=$r['id_rubrique'];
		}
		reset($secteurs);
		$i=0;
		$res=array();
		foreach($secteurs as $id) {
			$u=spip_query("SELECT restreint FROM spip_dw2_acces_restreint WHERE id_rubrique=$id");
			if(spip_num_rows($u)) {
				$t=spip_fetch_array($u);
				array_push($res,$t['restreint']);
				if($i>0 && $t['restreint']!=$res[0]) {
					// dependance variable donc 0
					return $retour=array('0');
				}
				$i++;
			}
			else {
				// au moins 1 secteur non enreg donc 0
				return $retour=array('0');
			}
		}
		// tous secteurs enreg. avec mm restreint
		return $retour=array($res[0],'site','0');
	}
//
}


// une icone pour niveau de restrict dans le rubriquage
function icone_niveau_restreint($niveau) {
	$puces = array(
		       0 => 'dot-bleue.gif',
		       1 => 'dot-verte.gif',
		       2 => 'dot-orange.gif',
		       3 => 'dot-rouge.gif');
	
	switch ($niveau) {
		case "0": $puce = $puces[0]; break;
		case "1": $puce = $puces[1]; break;
		case "2": $puce = $puces[2]; break;
		case "3": $puce = $puces[3]; break;
	}
	$inser_puce = "<img src='"._DIR_IMG_DW2.$puce." 'style='margin: 1px; vertical-align:baseline;' border='0' />";
	return $inser_puce;
}

function selecteur_restreindre($restreint) {
	$check = " checked='checked'";
	echo "<table width='100%' cellspacing='0' cellpadding='2' border='0' align='center'>\n";
	echo "<tr><td colspan='4'>";
	echo _T('dw:rest_telecharge_pour');
	echo "</td></tr>\n";
	echo "<tr>\n";
	
	echo "<td width='25%'><div align='center' class='boite_filet_a'>".$ico=icone_niveau_restreint('0').
		"&nbsp;"._T('dw:restreint_val_0').
		"<br /><input type='radio' name='restreint' value='0'";
		if($restreint=='0') { echo $check; }
	echo " /></div></td>\n";
	
	echo "<td width='25%'><div align='center' class='boite_filet_b'>".$ico=icone_niveau_restreint('1').
		"&nbsp;"._T('dw:restreint_val_1').
		"<br /><input type='radio' name='restreint' value='1'";
		if($restreint=='1') { echo $check; }
	echo " /></div></td>\n";
	
	echo "<td width='25%'><div align='center' class='boite_filet_b'>".$ico=icone_niveau_restreint('2').
		"&nbsp;"._T('dw:restreint_val_2').
		"<br /><input type='radio' name='restreint' value='2'";
		if($restreint=='2') { echo $check; }
	echo " /></div></td>\n";

	echo "<td width='25%'><div align='center' class='boite_filet_b'>".$ico=icone_niveau_restreint('3').
		"&nbsp;"._T('dw:restreint_val_3').
		"<br /><input type='radio' name='restreint' value='3'";
		if($restreint=='3') { echo $check; }
	echo " /></div></td>\n";

	echo "</tr>\n";
	echo "<tr><td colspan='4'>\n";
	echo "<div class='bloc_bouton_r'><input type=submit value="._T('dw:modifier')." class='fondo' /></div>\n";
	echo "</td></tr></table>\n";
}



?>
