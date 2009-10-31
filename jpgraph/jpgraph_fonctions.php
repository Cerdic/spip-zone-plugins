<?php

if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');
define('_DIR_JPGRAPH_LIB', _DIR_LIB . 'jpgraph-3.0.6/');


//
// cree un hash unique 
// (a ameliorer activer le cache de jpgraph ou celui de spip ?)
function jpgraph_name_hash($type="graph",$largeur,$hauteur,$donnee) {
    // repertoire local/cache-jpgraph dispo ?
    if (!is_dir(_DIR_VAR."cache-jpgraph/")) {                                     
                   if (!mkdir (_DIR_VAR."cache-jpgraph/", 0777)) // on essaie de le creer  
                        spip_log("plugin jgraph: impossible de creer le reperoitre image");
    }

    // creer le nom unique
    $donnee[] = $largeur;
    $donnee[] = $hauteur;
    $hash = md5(serialize($donnee));
    return _DIR_VAR."cache-jpgraph/$type-$hash.png";
}


// On passe les donnee dans un tableau
function jpgraph_traitement_donnees($donnee) {
	$donnee =  explode(";", $donnee);
	foreach ($donnee as $key => $value)
	        $donnee[$key] = (float) $value;    
	if (count($donnee)<2) 
        $donnee[] = "1";   // securite pour empecher les erreurs si donnnee pas renseigne  
	return $donnee;
}

// On passe les legendes dans un tableau
function jpgraph_traitement_legendes($legende) {
	$legende =  explode(";", $legende); 
	foreach ($legende as $key => $value)  
		$legende[$key] = utf8_decode(trim($value));
	return $legende;
}

// extrait couleur ds un tableau: contour / fond / degrade
function jpgraph_traitement_couleurs($couleur) {
	$couleur = explode(";", $couleur);
	$couleur['contour']=trim($couleur[0]);
	if ($couleur[1]) $couleur['fond']=trim($couleur[1]);
	if ($couleur[2]) $couleur['degrade']=trim($couleur[2]);
	return $couleur;
}

// extrait marqueur dans un tableau: nom / epaisseur / contour / fond
function jpgraph_traitement_marqueur($marqueur) {
	$marqueur = explode(";", $marqueur);
	$marqueur['nom'] = trim ($marqueur[0]);
	if ($marqueur[1]) $marqueur['epaisseur'] = (int) $marqueur[1];
	if ($marqueur[2]) $marqueur['contour'] = trim ($marqueur[2]);
	if ($marqueur[3]) $marqueur['fond'] = trim ($marqueur[3]);
	return $marqueur;
}


//--------------------------------------------
// filtre jgraph
//--------------------------------------------
function filtre_jpgraph($str,
	$type_graphe="courbe",
	$titre="",
	$donnee="",
	$donneedeux="",
	$donneetrois="",
	$legende="",
	$legendedeux="",
	$legendetrois="",
	$largeur=400,
	$hauteur=300,
	$couleur="orange",
	$couleurdeux="",
	$couleurtrois="",
	$marqueur="",
	$marqueurdeux="",
	$marqueurtrois="")
{
	
	  // constantes
	  $marqueur_formes = array("carre"=>MARK_SQUARE,
                     "triangle"=> MARK_UTRIANGLE,
                     "triangle_bas"=> MARK_DTRIANGLE,
                     "losange"=> MARK_DIAMOND,
                     "cercle"=> MARK_CIRCLE,
                     "disque"=> MARK_FILLEDCIRCLE,
                     "croix"=> MARK_CROSS,
                     "croix_x" => MARK_X,
                     "etoile" => MARK_STAR);
   
    // traiter les parametres en entree
    $type_graphe = strtolower(trim($type_graphe));  // pour pb avec les modeles si du blanc en fin de ligne
    $donnee = jpgraph_traitement_donnees($donnee);
    if ($donneedeux) $donneedeux = jpgraph_traitement_donnees($donneedeux);
    if ($donneetrois) $donneetrois = jpgraph_traitement_donnees($donneetrois);
    if ($legende) $legende = jpgraph_traitement_legendes($legende);
    if ($legendedeux) $legendedeux = jpgraph_traitement_legendes($legendedeux);
    if ($legendetrois) $legendetrois = jpgraph_traitement_legendes($legendetrois);
    $largeur = (int) $largeur;    if (($largeur<=10) OR ($largeur>1600)) $largeur = 400;
    $hauteur = (int) $hauteur;    if (($hauteur<=10) OR ($hauteur>1600)) $hauteur = 300;
    $couleur = jpgraph_traitement_couleurs($couleur);
    if ($couleurdeux) $couleurdeux = jpgraph_traitement_couleurs($couleurdeux);
    if ($couleurtrois) $couleurtrois = jpgraph_traitement_couleurs($couleurtrois);
    if ($marqueur) $marqueur = jpgraph_traitement_marqueur($marqueur);
    if ($marqueurdeux) $marqueurdeux = jpgraph_traitement_marqueur($marqueurdeux);
    if ($marqueurtrois) $marqueurtrois = jpgraph_traitement_marqueur($marqueurtrois);

    
    // retrouver jpgraph 
  	$cwd = getcwd();
  	chdir(realpath(_DIR_JPGRAPH_LIB));
    require_once ('src/jpgraph.php');
    switch($type_graphe) {
        case "courbe":      require_once ('src/jpgraph_line.php'); break;
        case "barre":       require_once ('src/jpgraph_bar.php');  break;
        case "camembert":   require_once ('src/jpgraph_pie.php');  break;
        default:            $type_graphe = "courbe";
                            require_once ('src/jpgraph_line.php');  
                            break;        
    }
    chdir($cwd);
    
    
    // creation du graphe
    switch($type_graphe) {
        case "courbe":      $graph = new Graph($largeur,$hauteur);
                            $graph->SetScale('textlin');
                            // Create the linear plot
                            $plot=new LinePlot($donnee);                            
                            // style & couleur
                            if ($couleur['contour']) $plot->SetColor($couleur['contour']);                      			
                      			if ($couleur['degrade']) $plot->SetFillGradient($couleur['fond'],$couleur['degrade']);
                      			if ($$couleur['fond']) $plot->SetFillColor($couleur['fond']);
                      			// L'epaisseur est sorti du modele, voir comment reintegrer ulterieurement
                      			//   if ($epaisseur) $plot->SetWeight($epaisseur);
                      			if (isset($marqueur_formes[$marqueur['nom']])) 
                  			                         $plot->mark->SetType($marqueur_formes[$marqueur['nom']]); 
                      			if ($marqueur['couleur']) $plot->mark->SetColor($marqueur['couleur']);
                      			if ($marqueur['fond']) $plot->mark->SetFillColor($marqueur['fond']);
                      			if ($marqueur['epaisseur'])$plot->mark->SetWidth($marqueur['epaisseur']);
                            // titre & legende 
			    $plot->SetLegend($legendedeux[0]);
			    
			    if ($donneetrois) {
				$plot3=new LinePlot($donneetrois);
				if ($couleurtrois['contour']) $plot3->SetColor($couleurtrois['contour']);
				if ($couleurtrois['degrade']) $plot3->SetFillGradient($couleurtrois['fond'],$couleurtrois['degrade']);
				if ($couleurtrois['fond']) $plot3->SetFillColor($couleurtrois['fond']);
				// L'epaisseur est sorti du modele, voir comment reintegrer ulterieurement
				//   if ($epaisseur) $plot->SetWeight($epaisseur);
				if (isset($marqueur_formes[$marqueurtrois['nom']])) 
					$plot3->mark->SetType($marqueur_formes[$marqueurtrois['nom']]);
				if ($marqueurtrois['couleur']) $plot3->mark->SetColor($marqueurtrois['couleur']);
				if ($marqueurtrois['fond']) $plot3->mark->SetFillColor($marqueurtrois['fond']);
				if ($marqueurtrois['epaisseur'])$plot3->mark->SetWidth($marqueurtrois['epaisseur']);
				$graph->Add($plot3);
				if ($legendedeux[2]) $plot3->SetLegend($legendedeux[2]);
			    }
			    
			    if ($donneedeux) {
				$plot2=new LinePlot($donneedeux);
				if ($couleurdeux['contour']) $plot2->SetColor($couleurdeux['contour']);
				if ($couleurdeux['degrade']) $plot2->SetFillGradient($couleurdeux['fond'],$couleurdeux['degrade']);
				if ($$couleurdeux['fond']) $plot2->SetFillColor($couleurdeux['fond']);
				// L'epaisseur est sorti du modele, voir comment reintegrer ulterieurement
				//   if ($epaisseur) $plot->SetWeight($epaisseur);
				if (isset($marqueur_formes[$marqueurdeux['nom']])) 
					$plot2->mark->SetType($marqueur_formes[$marqueurdeux['nom']]);
				if ($marqueurdeux['couleur']) $plot2->mark->SetColor($marqueurdeux['couleur']);
				if ($marqueurdeux['fond']) $plot2->mark->SetFillColor($marqueurdeux['fond']);
				if ($marqueurdeux['epaisseur'])$plot2->mark->SetWidth($marqueurdeux['epaisseur']);
				$graph->Add($plot2);
				if ($legendedeux[1]) $plot2->SetLegend($legendedeux[1]);
			    }
			    
                            $graph->title->Set(utf8_decode($titre));
                            if (count($legende)>1) 
                                $graph->xaxis->SetTickLabels($legende);  
                            break;
                            
        case "barre":       $graph = new Graph($largeur,$hauteur);
                            $graph->SetScale('textlin');
                            // Create the linear plot
                            $plot1 = new BarPlot($donnee);
                            // style & couleur
                      			if ($couleur['contour']) $plot1->SetColor($couleur['contour']);
                      			// Le degrade pour les barres est tres specifique (un peu comme le modele de marqueur) et doit etre traite dans une fonction supplementaire, sera fait ulterieurement
                      			// if ($couleur['degrade']) $plot->SetFillGradient($couleur['fond'],$couleur['degrade']);
                      			
                      			//petit patch en attendant d'uniformiser la doc : pour l'instant la doc indique que couleur=blue doit remplir en bleu les barres, alors que l'uniformisation des
                      			// couleurs indiquera plutot qu'il s'agit d'une couleur de contour et non une couleur de fond
                      			if (($couleur['contour']) AND (!$couleur['fond'])) $plot1->SetFillColor($couleur['contour']);
                      			//Devra etre probablement supprime ulterieurement, ou alors on garde cela, dans le cas d'oubli de la couleur de fond
                      			
                      			if ($couleur['fond']) $plot1->SetFillColor($couleur['fond']);                             
                           $group_plot[0]= $plot1;
			   
			   if ($donneedeux) {
				$plot2 = new BarPlot($donneedeux);
				if ($couleurdeux['contour']) $plot2->SetColor($couleurdeux['contour']);
				if (($couleurdeux['contour']) AND (!$couleurdeux['fond'])) $plot2->SetFillColor($couleurdeux['contour']);
                      		if ($couleurdeux['fond']) $plot2->SetFillColor($couleurdeux['fond']);
				$group_plot[1]= $plot2;
			   }

			   if ($donneetrois) {
				$plot3 = new BarPlot($donneetrois);
				if ($couleurtrois['contour']) $plot3->SetColor($couleurtrois['contour']);
				if (($couleurtrois['contour']) AND (!$couleurtrois['fond'])) $plot3->SetFillColor($couleurtrois['contour']);
                      		if ($couleurtrois['fond']) $plot3->SetFillColor($couleurtrois['fond']);
				$group_plot[2]= $plot3;
			   }

			    $plot = new  GroupBarPlot ($group_plot);
			    // titre & legende 
                            $graph->title->Set(utf8_decode($titre));
                            if (count($legende)>1)
                                $graph->xaxis->SetTickLabels($legende);                                                
                            break;
                            
         case "camembert":  $graph = new PieGraph($largeur,$hauteur);    
                            // Create the linear plot
                            $plot = new PiePlot($donnee);
                            // style & couleur
                            if ($couleur['contour']) $plot->SetColor($couleur['contour']);
                            switch ($couleur['fond']) {
				case "earth":
					$plot->SetTheme('earth');
					break;
				case "water":
					$plot->SetTheme('water');
					break;
				case "sand":
					$plot->SetTheme('sand');
					break;
				case "pastel":
					$plot->SetTheme('pastel');
					break;
				default:
					$plot->SetTheme('earth');
					break;
                            }	   
                            // titre & legende 
                            $graph->title->Set(utf8_decode($titre));
                            if (count($legende)>1) 
                                $plot->SetLegends($legende);        
                            break;
    }
    
    // Attacher le trace au graph
    $graph->Add($plot);  
    
    // export du graphe dans un fichier   
    $filename = jpgraph_name_hash($type_graphe,$largeur,$hauteur,$donnee);
    @unlink($filename); // http://jpgraph.intellit.nl/index.php?topic=4547.msg11823
    $graph->Stroke($filename);
    
    $titre = str_replace("'","&#039",$titre);
    return "<span class='spip_documents jgraph'><img src='$filename' alt='$titre' width='$largeur' height='$hauteur' /></span>";

}


?>