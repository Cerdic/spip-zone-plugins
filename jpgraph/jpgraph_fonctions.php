<?php

if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');
define('_DIR_JPGRAPH_LIB', _DIR_LIB . 'jpgraph-3.0.6/');


//
// cree un hash unique 
// (a ameliorer activer le cache de jpgraph ou celui de spip ?)
function jpgraph_name_hash($type="graph",$largeur,$hauteur,$donnee) {
    // repertoire IMG/jpgraph dispo ?
    if (!is_dir(_DIR_IMG."jpgraph/")) {                                     
                   if (!mkdir (_DIR_IMG."jpgraph/", 0777)) // on essaie de le creer  
                        spip_log("plugin jgraph: impossible de creer le reperoitre image");
    }

    // creer le nom unique
    $donnee[] = $largeur;
    $donnee[] = $hauteur;
    $hash = md5(serialize($donnee));
    return _DIR_IMG."jpgraph/$type-$hash.png";
}


//
// filtre pour creer des courbes simples
//
function filtre_jpgraph($str,$type_graphe="courbe",$donnee="",$legende="",$largeur=400,$hauteur=300,$titre="",$couleur="orange"){
   
    // traiter les donnees
    $type_graphe = strtolower(trim($type_graphe));  // pour pb avec les modeles si du blanc en fin de ligne
    $couleur = trim($couleur);    
    $donnee =  explode(";", $donnee);    
    foreach ($donnee as $key => $value)
         $donnee[$key] = (float) $value;    
    if (count($donnee)<2) 
        $donnee[] = "1";   // securite pour empecher les erreurs si donnnee pas renseigne  
    
    $legende =  explode(";", $legende); 
    foreach ($legende as $key => $value)  
            $legende[$key] = utf8_decode($value);
    $largeur = (int) $largeur;    if ($largeur<=0) $largeur = 400;
    $hauteur = (int) $hauteur;    if ($hauteur<=0) $hauteur = 300;
    
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