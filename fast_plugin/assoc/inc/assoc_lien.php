<?php


// todo : faire en sorte que le message d'erreur puisse etre parser avant
// le lancement de la requete sql , par exemple en mettant le mot 'erreur' dans le message

include_spip("inc/assoc_static_type");



class find_table_association{

	
	
	public function basic_find_sql($type,$id,$lien="*"){
		$a = new assoc_static_type();
		$les_types = $a->type();
		
		/* on verifie que le type existe */
		if ($les_types[$type]){
			/* si le lien a ete preciser on verifie que le type existe egalement */
			if ($lien!="*" && !$les_types[$lien]){
				return "le type de lien $lien n'existe pas";
			}
			/* enfin on recupere les valeurs numerique  du type de lien si necessaire
			 * ainsi que la valeur numerique du type recherche , on genere la reque sql correspondante 
			 * et on recupere les resultats
			 */
			$type = $les_types[$type];
			if ($lien != "*"){
				$lien = $les_types[$lien];
				$sql = "select * from association where type_id=$type and type_lien=$lien and id=$id";
			}else{
				$sql = "select * from association where type_id=$type and id=$id";
			}
			return $sql;
		}else{
			return "$type est un type de donnée inconnu";
		}
	}
	
	
	public function template_admin($sql){
		$a = new assoc_static_type();
		$les_types = $a->type_id();
		$res = spip_query($sql);
		$modif = "<div id='assoc-zone-modif'>";
		$retour = "<div id='assoc-liste'>";
		$script = "<script>";
		while ($row = spip_fetch_array($res)){
			//	id_lien 	 	type_lien 	
			
			$id = $row["id"];
			$id_lien = $row["id_lien"];
			$type_id = $row["type_id"];
			$type_lien  =$row["type_lien"];
			$desc = $row["descriptif"];
			$titre = $row["titre"];
			$type = $les_types[$row["type_lien"]];
			
			$obj = $row["keys"];
			
			$retour .= "<p class='titre-lien-assoc-admin' id='lien$obj' onclick='lien$obj.deplier()'>
							<img src='../plugins/assoc/img/$type"."_mini.png' class='align-middle' />&nbsp;&nbsp;
							<span class='titre_aff'>$titre</span>
							<img src='../plugins/assoc/img/add.png' class='align-middle' onclick='lien$obj.inserer($obj)'/>
						</p>";
			$modif .="<div class='display-none contour' id='modif$obj'>
							<p ondblclick='lien$obj.titre()' class='le_titre'>$titre 
								<img src='../plugins/assoc/img/crayon.png' class='align-middle'>
								<input type='text' class='invisible letitre' />
							</p>
							<p ondblclick='lien$obj.texte()' class='le_texte'>$desc
								<img src='../plugins/assoc/img/crayon.png' class='align-middle'>
								<textarea rows='3' cols='15' class='invisible letexte' ></textarea>
							</p>
							<input type='button' value='Supprimer' onclick='lien$obj.supprimer()' />
						</div>";
			
			// prevoir un script pour les endroits ou cela n'a pas erte effectue
			// plus exactement pour faire une transition avec l'ancienne version
			// le mieux serait de faire un script mysql pour affecter les liens
			
			$script .="lien$obj = new obj_assoc($obj); \n";
		}
		$retour .= "</div>";
		$modif .= "</div>";
		$script .= "</script>";
		return $retour.$modif.$script;
		
	}
	
	// les parametres de cette fonction sont les suivants
	// $champs : les champs que l'on souhaite recupere, sous forme de tableau 
	// le premier element etant la cle primaire de la table
	// $table : table dans laquelle on va recuperer les elements
	// $type_id : table dans laquelle on a effecyuer l'association
	// $type_lien : table contenant le type d'element associe
	// $id_lien : id de l'element du lien sur lequelle on fait la recherche
	// $template indique si l'on veux recuperer le resultat de la recherche 
	// sous forme de texte (alors $template est un tableau avec une syntaxe de type spip)
	// sinon il renvoie un tableau avec toutes les donnees
	public function recup_lien_element(array $champs,$table,$type_id,$type_lien, $id_lien, $template = false){
		$a = new assoc_static_type();
		$les_types = $a->type();
		$type_id = $les_types[$type_id];
		$type_lien = $les_types[$type_lien];
		$tab = array();
		$final = array();
		$retour_texte="";
		
		
		// on recupere tous les id_article en relation avec $id_lien
		$sql ="select `keys`,id from association where type_id=$type_id and  type_lien= $type_lien and id_lien=$id_lien";
		$res = spip_query($sql);
		while ($row = spip_fetch_array($res)){
			$tab[] = $row["id"];
			$key[] = $row["keys"];
		}
	
		$ch_texte = implode(",",$champs);
		$primaire = $champs[0];
		for($i=0;$i < count($tab);$i++){
			$id = $tab[$i];
			$cle = $key[$i];
			
			$sql = "select $ch_texte from $table where $primaire = $id";
			$res = spip_query($sql);
			$row = spip_fetch_array($res);
			
			// s'il y a un template on ve generer le  html
			// en rajoutant la cle si necessaire la cle dans le template
			if ($template){
				$trouver = "#".strtoupper($primaire);
				$temp = str_replace("#KEYS",$cle,$template);
				$partiel .= str_replace($trouver,$id,$temp);
			}
			
			for($u=1;$u < count($champs);$u++){
				 if (!$template) {
				 	$final[$id][$champs[$u]] = $row[$champs[$u]];
				 }else{
				 	$trouver = "#".strtoupper($champs[$u]);
				 	$part = str_replace($trouver,$row[$champs[$u]],$partiel);
				 	$partiel = $part;
				 }
			}
			
			
			
			if ($template){
				$retour_texte .= $partiel;
				$partiel = "";
			}

		}
		if ($template) return $retour_texte;
		return $final;
		
	}
	
}



?>