<?php
function autoriser_rubrique_voir($faire, $type, $id, $qui, $opt) {
	static $rub_exclues=NULL;
	if ($rub_exclues===NULL){
		$rub_exclues = accesgroupes_combin_prive();
		$rub_exclues = array_flip($rub_exclues);
	}
	//echo "<pre>".print_r($rub_exclues)."</pre>";
	if (isset($rub_exclues[$id]))
	return false;
	return true;
}

function autoriser_article_voir($faire, $type, $id, $qui, $opt) {
	//echo "$faire $type $id $qui $opt";
	return accesgroupes_autoriser($id,$type);
	
}

// fct pour construire et renvoyer le tableau des rubriques à accès restreint dans la partie PRIVE
// 		 clone de la fct accesgroupes_liste_rubriques_restreintes() de inc/accesgroupes_fonctions.php 
function accesgroupes_combin_prive($id_parent = 0) {
	$id_parent = intval($id_parent); // securite
	static $Trub_restreintes; // nécessaire pour que la suite ne soit éxécutée qu'une fois par hit (même si on à n BOUCLES)
	if (!is_array($Trub_restreintes)) {
		$Trub_restreintes = array();
		// attaquer à la racine pour mettre tout de suite les éventuels secteurs restreints dans le tableau ce qui accélèrera la suite
		$sql1 = "SELECT id_rubrique, id_parent, id_secteur FROM spip_rubriques";
		$result1 = spip_query($sql1);
		while ($row1 = spip_fetch_array($result1)) {
			$rub_ec = $row1['id_rubrique'];
			$parent_ec = $row1['id_parent'];
			$sect_ec = $row1['id_secteur'];
			// si le parent ou le secteur est déja dans le tableau : vu le principe d'héritage pas la peine d'aller plus loin :)
			/*	 if (in_array($parent_ec, $Trub_restreintes) OR in_array($sect_ec, $Trub_restreintes)) {
					$Trub_restreintes[] = $rub_ec;
				}
			// sinon c'est plus couteux : il faut faire le test complet de la restriction de la rubrique pour espace public
				else {*/
			if (accesgroupes_verif_acces($rub_ec, 'prive') == 1 OR accesgroupes_verif_acces($rub_ec, 'prive') == 2) {
				$Trub_restreintes[] = $rub_ec;
			}
			//	 }
		}
	}
	//echo '<br>tableau des rubriques = ';
	//print_r($Trub_restreintes);
	return $Trub_restreintes;
}

function accesgroupes_autoriser($id,$type){
	static $rub_exclues=NULL;
	if ($rub_exclues===NULL){
		$rub_exclues = accesgroupes_combin_prive(0);
		$rub_exclues = array_flip($rub_exclues);
	}
	switch($type){
	case 'article':
		$table = "articles";
		$champ = "id_article";
		break;
	case 'breve';
		$table = "breves";
		$champ = "id_breve";
		break;
	case 'forum';
		$table = "forum";
		$champ = "id_forum";
		break;
	case 'syndic':
		$table = "syndic";
		$champ = "id_syndic";
		break;
	default:
	}
	// Trouver la rubrique d'appartenance...
	$sql = "select id_rubrique from spip_$table where spip_$champ = $id limit 1";
	$res = spip_query($sql);
	while ($row=spip_fetch_array($res));
	// Tester si la rubrique troucée fait partie des rubriques exclues.
	if (in_array($row['id_rubrique'],array($rub_exclues))){
		return  false;
	}else{
		return  true;
	}
}
?>