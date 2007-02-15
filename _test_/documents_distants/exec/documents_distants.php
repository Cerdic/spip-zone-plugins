<?

function exec_documents_distants(){
	global $documents;
	global $id;
	global $type_lien;
	global $valider;
	

	global $retour;
	
	$retour[0] =='oui' ? $retour = $retour[0] : $retour;
	
	if ($valider)
		{importer_document($documents,$type_lien,$id,$retour);
		}
	
	$commencer_page= charger_fonction('commencer_page', 'inc');
	
	include_spip('public/assembler');
	echo $commencer_page($titre=_T('documentsdistants:importer'));
	
	
	debut_gauche();
	debut_droite();
	
	debut_cadre_formulaire();
	echo gros_titre(_T('documentsdistants:importer'));
	
	echo recuperer_fond('documents_distants',Array('id'=>$id,'type_lien'=>$type_lien,'retour'=>$retour));
	fin_cadre_formulaire();
	
	echo fin_gauche();
	echo fin_page();
	}


function importer_document($documents_distants,$type_lien,$id,$retour)
	{
	$id2=$id;
	settype($id,'integer');
	settype($id,'string');// amha il y a moyen de faire plus simple
	
	$tableau =explode(";",$documents_distants);
	if (!($documents_distants and $id and $id2==$id)){return;}
	
	include_spip('inc/distant');
	include_spip('inc/indexation');
	foreach ($tableau as $documents_distants){
		
		if ($infos=recuperer_infos_distantes(trim($documents_distants))){
		
		
		
			$request='INSERT INTO  `spip_documents` (`fichier`,`titre`,`taille`,`date`,`largeur`,`distant`,`hauteur`,`mode`,`id_type`) VALUES ("'.$documents_distants.'","'.$infos['titre'].'","'.$infos['taille'].'",NOW(),"'.$infos['largeur'].'","oui","'.$infos['hauteur'].'","document","'.$infos['id_type'].'") ; 
			  ';
			 
			 spip_query($request); // il doit y avoir moyen de se servir de  spip_mysql_insert(), si quelqu'un sait comment ...
			$dernier_document = spip_insert_id();
					
			spip_query('INSERT INTO  `spip_documents_'.$type_lien.'` VALUES ("'.$dernier_document.'","'.$id.'")');
			
			marquer_indexer('spip_documents', $dernier_document);
		}
		
		}
	
	if($retour="oui") {
		include_spip('inc/headers');
		$type_lien == 'articles' ?  $url = './?exec=articles&id_article='.$id: pass ;
		$type_lien == 'rubriques' ? $url = './?exec=naviguer&id_rubrique='.$id : pass;
		$type_lien == 'breves' 	 ?	$url = './?exec=breves_voir&id_breve='.$id : pass;
		
		
		redirige_par_entete($url); //pas sur
		}
	}

?>