<?

function exec_documents_distants(){
	global $documents;
	global $id;
	global $type_lien;
	global $valider;
	echo $valider;
	$commencer_page= charger_fonction('commencer_page', 'inc');
	if ($valider)
		{importer_document($documents,$type_lien,$id);
		echo "spiup";
		}
	include_spip('public/assembler');
	echo $commencer_page($titre=_T('documentsdistants:importer'));
	
	
	debut_gauche();
	debut_droite();
	
	debut_cadre_formulaire();
	echo gros_titre(_T('documentsdistants:importer'));
	
	echo recuperer_fond('documents_distants');
	fin_cadre_formulaire();
	
	echo fin_gauche();
	echo fin_page();
	}


function importer_document($documents_distants,$type_lien,$id)
	{
	$tableau =explode(";",$documents_distants);
	if (!($documents_distants and $id)){return;}
	
	include_spip('inc/distant');
	include_spip('inc/indexation');
	foreach ($tableau as $documents_distants){
		
		
		$infos=recuperer_infos_distantes($documents_distants);
		
		$request='INSERT INTO  `spip_documents` (`fichier`,`titre`,`taille`,`date`,`largeur`,`distant`,`hauteur`,`mode`,`id_type`) VALUES ("'.$documents_distants.'","'.$infos['titre'].'","'.$infos['taille'].'",NOW(),"'.$infos['largeur'].'","oui","'.$infos['hauteur'].'","document","'.$infos['id_type'].'") ; 
		  ';
		 
		 spip_query($request); // il doit y avoir moyen de se servir de  spip_mysql_insert(), si quelqu'un sait comment ...
		$dernier_document = spip_insert_id();
				
		spip_query('INSERT INTO  `spip_documents_'.$type_lien.'` VALUES ("'.$dernier_document.'","'.$id.'")');
		
		marquer_indexer('spip_documents', $dernier_document);
			
		}
		
	}

?>