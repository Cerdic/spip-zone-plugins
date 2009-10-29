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
	$donnee2="",
	$donnee3="",
	$legende="",
	$legende2="",
	$legende3="",
	$largeur=400,
	$hauteur=300,
	$couleur="orange",
	$couleur2="",
	$couleur3="",
	$marqueur="",
	$marqueur2="",
	$marqueur3="")
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
    if ($donnee2) $donnee2 = jpgraph_traitement_donnees($donnee2);
    if ($donnee3) $donnee3 = jpgraph_traitement_donnees($donnee3);
    if ($legende) $legende = jpgraph_traitement_legendes($legende);
    if ($legende2) $legende2 = jpgraph_traitement_legendes($legende2);
    if ($legende3) $legende3 = jpgraph_traitement_legendes($legende2);
    $largeur = (int) $largeur;    if (($largeur<=0) OR ($largeur>1600)) $largeur = 400;
    $hauteur = (int) $hauteur;    if (($hauteur<=0) OR ($hauteur>1600)) $hauteur = 300;
    $couleur = jpgraph_traitement_couleurs($couleur);
    if ($couleur2) $couleur2 = jpgraph_traitement_couleurs($couleur2);
    if ($couleur3) $couleur3 = jpgraph_traitement_couleurs($couleur3);
    if ($marqueur) $marqueur = jpgraph_traitement_marqueur($marqueur);
    if ($marqueur2) $marqueur2 = jpgraph_traitement_marqueur($marqueur2);
    if ($marqueur3) $marqueur3 = jpgraph_traitement_marqueur($marqueur3);

    
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
                            $graph->title->Set(utf8_decode($titre));
                            if (count($legende)>1) 
                                $graph->xaxis->SetTickLabels($legende);  
                            break;
                            
        case "barre":       $graph = new Graph($largeur,$hauteur);
                            $graph->SetScale('textlin');
                            // Create the linear plot
                            $plot = new BarPlot($donnee);
                            // style & couleur
                      			if ($couleur['contour']) $plot->SetColor($couleur['contour']);
                      			// Le degrade pour les barres est tres specifique (un peu comme le modele de marqueur) et doit etre traite dans une fonction supplementaire, sera fait ulterieurement
                      			// if ($couleur['degrade']) $plot->SetFillGradient($couleur['fond'],$couleur['degrade']);
                      			
                      			//petit patch en attendant d'uniformiser la doc : pour l'instant la doc indique que couleur=blue doit remplir en bleu les barres, alors que l'uniformisation des
                      			// couleurs indiquera plutot qu'il s'agit d'une couleur de contour et non une couleur de fond
                      			if (($couleur['contour']) AND (!$couleur['fond'])) $plot->SetFillColor($couleur['contour']);
                      			//Devra etre probablement supprime ulterieurement, ou alors on garde cela, dans le cas d'oubli de la couleur de fond
                      			
                      			if ($couleur['fond']) $plot->SetFillColor($couleur['fond']);                             
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
                            //$plot->SetTheme("earth"); 		   
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