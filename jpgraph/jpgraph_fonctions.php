<?php

if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');
define('_DIR_JPGRAPH_LIB', _DIR_LIB . 'jpgraph-3.0.6/');


//
// cree un hash unique 
//
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
function filtre_jpgraph_courbe($str,$donnee="",$legende="",$largeur=400,$hauteur=300,$titre="",$couleur="orange"){
    // retrouver jpgraph 
  	$cwd = getcwd();
  	chdir(realpath(_DIR_JPGRAPH_LIB));
    require_once ('src/jpgraph.php');
    require_once ('src/jpgraph_line.php');
  	chdir($cwd);
   
    // traiter les donnees
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
    
    // Create the graph. These two calls are always required
    $graph = new Graph($largeur,$hauteur);
    $graph->SetScale('textlin');
    
    // Create the linear plot
    $lineplot=new LinePlot($donnee);
    $lineplot->SetColor($couleur);
    
    // titre & legende 
    $graph->title->Set(utf8_decode($titre));
    if (count($legende)>1) {
        $graph->xaxis->SetTickLabels($legende);
    }
    
    // Add the plot to the graph
    $graph->Add($lineplot);
    
    // Save the graph   
    $filename = jpgraph_name_hash("courbe",$largeur,$hauteur,$donnee);
    @unlink($filename); // http://jpgraph.intellit.nl/index.php?topic=4547.msg11823
    $graph->Stroke($filename);
    
    
    return "<span class='spip_documents jgraph'><img src='$filename' alt='' width='$largeur' height='$hauteur' /></span>";

}

//
// filtre pour creer des barres horizontales
//
function filtre_jpgraph_barre($str,$donnee="",$legende="",$largeur=400,$hauteur=300,$titre="",$couleur="orange"){
    // retrouver jpgraph 
  	$cwd = getcwd();
  	chdir(realpath(_DIR_JPGRAPH_LIB));
    require_once ('src/jpgraph.php');
    require_once ('src/jpgraph_bar.php');
  	chdir($cwd);
   
    // traiter les donnees
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
    
    // Create the graph. These two calls are always required
    $graph = new Graph($largeur,$hauteur);
    $graph->SetScale('textlin');
    
    // Create the linear plot
    $bplot = new BarPlot($donnee);
    $bplot->SetFillColor($couleur);
    
    // titre & legende 
    $graph->title->Set(utf8_decode($titre));
    if (count($legende)>1) {
        $graph->xaxis->SetTickLabels($legende);
    }
    
    // Add the plot to the graph
    $graph->Add($bplot);
    
    // Save the graph   
    $filename = jpgraph_name_hash("barre",$largeur,$hauteur,$donnee);
    @unlink($filename); // http://jpgraph.intellit.nl/index.php?topic=4547.msg11823
    $graph->Stroke($filename);
    
    
    return "<span class='spip_documents jgraph'><img src='$filename' alt='' width='$largeur' height='$hauteur' /></span>";

}

//
// filtre pour creer des barres horizontales
//
function filtre_jpgraph_camembert($str,$donnee="",$legende="",$largeur=400,$hauteur=300,$titre="",$couleur="orange"){
    // retrouver jpgraph 
  	$cwd = getcwd();
  	chdir(realpath(_DIR_JPGRAPH_LIB));
    require_once ('src/jpgraph.php');
    require_once ('src/jpgraph_pie.php');
  	chdir($cwd);
   
    // traiter les donnees
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
    
    // Create the graph. These two calls are always required
    $graph = new PieGraph($largeur,$hauteur);
    
    // Create the linear plot
    $bplot = new PiePlot($donnee);
    $bplot->SetTheme("earth");
    
    // titre & legende 
    $graph->title->Set(utf8_decode($titre));
    if (count($legende)>1) {
        $bplot->SetLegends($legende);            
    }
    
    // Add the plot to the graph
    $graph->Add($bplot);
    
    // Save the graph   
    $filename = jpgraph_name_hash("pie",$largeur,$hauteur,$donnee);
    @unlink($filename); // http://jpgraph.intellit.nl/index.php?topic=4547.msg11823
    $graph->Stroke($filename);
    
    
    return "<span class='spip_documents jgraph'><img src='$filename' alt='' width='$largeur' height='$hauteur' /></span>";

}


?>