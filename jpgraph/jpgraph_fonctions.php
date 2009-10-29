<?php

if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');
define('_DIR_JPGRAPH_LIB', _DIR_LIB . 'jpgraph-3.0.6/');


//
// cree un hash unique 
// (a ameliorer activer le cache de jpgraph ou celui de spip ?)
function jpgraph_name_hash($type="graph",$largeur,$hauteur,$donnee) {
    // repertoire IMG/jpgraph dispo ?
    if (!is_dir(_DIR_VAR."jpgraph/")) {                                     
                   if (!mkdir (_DIR_VAR."jpgraph/", 0777)) // on essaie de le creer  
                        spip_log("plugin jgraph: impossible de creer le reperoitre image");
    }

    // creer le nom unique
    $donnee[] = $largeur;
    $donnee[] = $hauteur;
    $hash = md5(serialize($donnee));
    return _DIR_VAR."jpgraph/$type-$hash.png";
}


// Gestion de la forme du marqueur
function jpgraph_nom_marqueur($plot,$marqueur) {
switch($marqueur) {
	case 'carre': $plot->mark->SetType(MARK_SQUARE); break;
	case 'triangle': $plot->mark->SetType(MARK_UTRIANGLE); break;
	case 'triangle_bas': $plot->mark->SetType(MARK_DTRIANGLE); break;
	case 'losange': $plot->mark->SetType(MARK_DIAMOND); break;
	case 'cercle': $plot->mark->SetType(MARK_CIRCLE); break;
	case 'cercle_plein': $plot->mark->SetType(MARK_FILLEDCIRCLE); break;
	case 'croix': $plot->mark->SetType(MARK_CROSS); break;
	case 'croix_x': $plot->mark->SetType(MARK_X); break;
	case 'etoile': $plot->mark->SetType(MARK_STAR); break;
	default: $plot->mark->SetType(MARK_SQUARE); break;
}
}

//
// filtre pour creer des courbes simples
//
function filtre_jpgraph($str,
	$type_graphe="courbe",
	$donnee="",
	$legende="",
	$largeur=400,
	$hauteur=300,
	$titre="",
	$couleur="orange",
	$couleur_fond="",
	$fond_degrade="",
	$epaisseur="",
	$marqueur_forme="",
	$marqueur_couleur="",
	$marqueur_epaisseur="",
	$marqueur_couleur_fond="",
	$style=""){
   
    // traiter les donnees
    $type_graphe = strtolower(trim($type_graphe));  // pour pb avec les modeles si du blanc en fin de ligne
    $couleur = trim($couleur);
    $couleur_fond = trim($couleur_fond);
    $epaisseur = (int) $epaisseur;
    $marqueur_forme = trim($marqueur_forme);
    $marqueur_couleur=trim($marqueur_couleur);
    $marqueur_epaisseur=(int) $marqueur_epaisseur;
    $marqueur_couleur_fond = trim($marqueur_couleur_fond);
    $style = trim ($style);
    
    $donnee =  explode(";", $donnee);    
    foreach ($donnee as $key => $value)
         $donnee[$key] = (float) $value;    
    if (count($donnee)<2) 
        $donnee[] = "1";   // securite pour empecher les erreurs si donnnee pas renseigne  
    
    if ($fond_degrade) {
	$fond_degrade = explode(";", $fond_degrade);
	foreach ($fond_degrade as $key => $value) $fond_degrade[$key] = trim($value);
	if (count($fond_degrade)<2) {
		$fond_degrade[0]='white@0.5';
		$fond_degrade[1]='orange@0.5';
	}
    }
    
    $legende =  explode(";", $legende); 
    foreach ($legende as $key => $value)  
            $legende[$key] = utf8_decode($value);
    $largeur = (int) $largeur;    if (($largeur<=0) OR ($largeur>1600)) $largeur = 400;
    $hauteur = (int) $hauteur;    if (($hauteur<=0) OR ($hauteur>1600)) $hauteur = 300;
    
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
			    $plot->SetColor($couleur);
			    if ($style=='marches') $plot->SetStepStyle();
			    if ($fond_degrade) $plot->SetFillGradient($fond_degrade[0],$fond_degrade[1]);
			    if ($couleur_fond) $plot->SetFillColor($couleur_fond);
			    if ($epaisseur) $plot->SetWeight($epaisseur);
			    if ($marqueur_forme) {
			    jpgraph_nom_marqueur($plot,$marqueur_forme);
			    if ($marqueur_couleur) $plot->mark->SetColor($marqueur_couleur);
			    if ($marqueur_couleur_fond) $plot->mark->SetFillColor($marqueur_couleur_fond);
			    if ($marqueur_epaisseur)$plot->mark->SetWidth($marqueur_epaisseur);
			    }
			    
                            // titre & legende 
                            $graph->title->Set(utf8_decode($titre));
                            if (count($legende)>1) 
                                $graph->xaxis->SetTickLabels($legende);  
                            break;
                            
        case "barre":       $graph = new Graph($largeur,$hauteur);
                            $graph->SetScale('textlin');
                            // Create the linear plot
                            $plot = new BarPlot($donnee);
                            $plot->SetFillColor($couleur);    
                            // titre & legende 
                            $graph->title->Set(utf8_decode($titre));
                            if (count($legende)>1)
                                $graph->xaxis->SetTickLabels($legende);                                                     
                            break;
                            
         case "camembert":  $graph = new PieGraph($largeur,$hauteur);    
                            // Create the linear plot
                            $plot = new PiePlot($donnee);
                            $plot->SetTheme("earth");    
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