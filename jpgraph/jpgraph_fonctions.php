<?php

if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');
define('_DIR_JPGRAPH_LIB', _DIR_LIB . 'jpgraph-3.0.7/src/');


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

// extrait option dans un tableau les options eventuelles
// |option=truc;bidule=chouette;machin va creer le tableau suivant
// option['truc']='truc' ; option['bidule']='chouette' ; option['machin']='machin'
// Ainsi les paramètres d'option peuvent être des valeurs predefinies comme truc ou machin, ou plus concretement "histogramme" pour les courbes a tracer en histogrammes
// ou bien des valeurs choisie par le redacteur du style bidule=chouette ou plus concretement couleur_fenetre=red si on decide de coder la couleur du bloc genere (bien souvent gris)
function jpgraph_traitement_option($option) {
	$option = explode(";", $option);
	foreach ($option as $cle=>$val) {
		$val=trim($val);
		if (strpos($val,'=')!== false) {
			$option[trim(substr($val,0,strpos($val,'=')-strlen($val)))]=trim(substr($val,strpos($val,'=')+1));
		}
		else {
			$option[$val]=$val;
		}
	}
	return $option;
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
	$marqueurtrois="",
	$option="")
{


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
    $option=jpgraph_traitement_option($option);

    
    // retrouver jpgraph 
    include_spip(_DIR_JPGRAPH_LIB.'jpgraph');
    switch($type_graphe) {
        case "courbe":      include_spip(_DIR_JPGRAPH_LIB.'jpgraph_line'); break;
        case "barre":       include_spip(_DIR_JPGRAPH_LIB.'jpgraph_bar');  break;
	      case "accbarre":    include_spip(_DIR_JPGRAPH_LIB.'jpgraph_bar');  break;
        case "camembert":   include_spip(_DIR_JPGRAPH_LIB.'jpgraph_pie');  break;
	      case "camembert3d": include_spip(_DIR_JPGRAPH_LIB.'jpgraph_pie'); 
                            include_spip(_DIR_JPGRAPH_LIB.'jpgraph_pie3d'); break;
      	case "baton":       include_spip(_DIR_JPGRAPH_LIB.'jpgraph_scatter');  break;
      	case "point":       include_spip(_DIR_JPGRAPH_LIB.'jpgraph_scatter');  break;
      	case "radar":       include_spip(_DIR_JPGRAPH_LIB.'jpgraph_radar');  break;
        default:            $type_graphe = "courbe";
                            include_spip(_DIR_JPGRAPH_LIB.'jpgraph_line');  
                            break;        
    }
    
     // constantes
	  $marqueur_formes = array("carre"=>MARK_SQUARE,
                     "triangle"=>MARK_UTRIANGLE,
                     "triangle_bas"=>MARK_DTRIANGLE,
                     "losange"=>MARK_DIAMOND,
                     "cercle"=>MARK_CIRCLE,
                     "disque"=>MARK_FILLEDCIRCLE,
                     "croix"=>MARK_CROSS,
                     "croix_x"=>MARK_X,
                     "etoile"=>MARK_STAR); 
    
    
    // creation du graphe
    switch($type_graphe) {
        case "point": $graph = new Graph($largeur,$hauteur);
		$graph->SetScale("linlin");
		$plot = new ScatterPlot($donneedeux,$donnee);
		if (isset($marqueur_formes[$marqueur['nom']])) 
			$plot->mark->SetType($marqueur_formes[$marqueur['nom']]);
		if ($marqueur['contour']) $plot->mark->SetColor($marqueur['contour']);
		if ($marqueur['fond']) $plot->mark->SetFillColor($marqueur['fond']);
		if ($marqueur['epaisseur'])$plot->mark->SetWidth($marqueur['epaisseur']);
		$graph->title->Set(utf8_decode($titre));
		if (count($legende)>1) 
			$graph->xaxis->SetTickLabels($legende);
		if ($legendetrois[0]) $graph->xaxis->title->Set($legendetrois[0]);
		if ($legendetrois[1]) $graph->yaxis->title->Set($legendetrois[1]);
		break;
		
	case "baton": $graph = new Graph($largeur,$hauteur);
		$graph->SetScale("textlin");
		$plot = new ScatterPlot($donnee);
		if (isset($marqueur_formes[$marqueur['nom']])) 
			$plot->mark->SetType($marqueur_formes[$marqueur['nom']]);
		if ($marqueur['epaisseur'])$plot->mark->SetWidth($marqueur['epaisseur']);
		if ($marqueur['contour']) $plot->mark->SetColor($marqueur['contour']);
		if ($marqueur['fond']) $plot->mark->SetFillColor($marqueur['fond']);
		if ($couleur['contour']) $plot->SetColor($couleur['contour']);
		if ($legendetrois[0]) $graph->xaxis->title->Set($legendetrois[0]);
		if ($legendetrois[1]) $graph->yaxis->title->Set($legendetrois[1]);
		$plot->SetImpuls();
		$graph->title->Set(utf8_decode($titre));
		if (count($legende)>1)
			$graph->xaxis->SetTickLabels($legende);
		break;
	
	case "radar": $graph = new RadarGraph($largeur,$hauteur);
		// titre & legende
		$graph->title->Set(utf8_decode($titre));
		if ($legende[0]) $graph->SetTitles($legende);
		//On montre la grille : on proposera cela dans une option ulterieurement
		$graph->grid->Show();
		
		$plot = new RadarPlot($donnee);
		if ($couleur['contour']) $plot->SetColor($couleur['contour']);
		if ($couleur['fond']) $plot->SetFillColor($couleur['fond']);
		if (isset($marqueur_formes[$marqueur['nom']])) $plot->mark->SetType($marqueur_formes[$marqueur['nom']]);
		if ($marqueur['contour']) $plot->mark->SetColor($marqueur['contour']);
		if ($marqueur['fond']) $plot->mark->SetFillColor($marqueur['fond']);
		if ($marqueur['epaisseur'])$plot->mark->SetWidth($marqueur['epaisseur']);
		if ($legendedeux[0]) $plot->SetLegend($legendedeux[0]);

		if ($donneetrois) {
			$plot3 = new RadarPlot($donneetrois);
			if ($couleurtrois['contour']) $plot3->SetColor($couleurtrois['contour']);
			if ($couleurtrois['fond']) $plot3->SetFillColor($couleurtrois['fond']);
			if (isset($marqueur_formes[$marqueurtrois['nom']])) $plot3->mark->SetType($marqueur_formes[$marqueurtrois['nom']]);
			if ($marqueurtrois['contour']) $plot3->mark->SetColor($marqueurtrois['contour']);
			if ($marqueurtrois['fond']) $plot3->mark->SetFillColor($marqueurtrois['fond']);
			if ($marqueurtrois['epaisseur'])$plot3->mark->SetWidth($marqueurtrois['epaisseur']);
			$graph->Add($plot3);
			if ($legendedeux[2]) $plot3->SetLegend($legendedeux[2]);
		}
		
		if ($donneedeux) {
			$plot2 = new RadarPlot($donneedeux);
			if ($couleurdeux['contour']) $plot2->SetColor($couleurdeux['contour']);
			if ($couleurdeux['fond']) $plot2->SetFillColor($couleurdeux['fond']);
			if (isset($marqueur_formes[$marqueurdeux['nom']])) $plot2->mark->SetType($marqueur_formes[$marqueurdeux['nom']]);
			if ($marqueurdeux['contour']) $plot2->mark->SetColor($marqueurdeux['contour']);
			if ($marqueurdeux['fond']) $plot2->mark->SetFillColor($marqueurdeux['fond']);
			if ($marqueurdeux['epaisseur'])$plot2->mark->SetWidth($marqueurdeux['epaisseur']);
			$graph->Add($plot2);
			if ($legendedeux[1]) $plot2->SetLegend($legendedeux[1]);
		}
		if (count($legende)>1) $graph->legend->SetReverse();

		break;
	
	case "courbe":      $graph = new Graph($largeur,$hauteur);
                            $graph->SetScale('textlin');
                            // Create the linear plot
                            $plot=new LinePlot($donnee);                            
                            // style & couleur
                            if ($couleur['contour']) $plot->SetColor($couleur['contour']);                      			
                      			if ($couleur['degrade']) $plot->SetFillGradient($couleur['fond'],$couleur['degrade']);
                      			if ($couleur['fond']) $plot->SetFillColor($couleur['fond']);
                      			// L'epaisseur est sorti du modele, voir comment reintegrer ulterieurement
                      			//   if ($epaisseur) $plot->SetWeight($epaisseur);
                      			if (isset($marqueur_formes[$marqueur['nom']])) 
						$plot->mark->SetType($marqueur_formes[$marqueur['nom']]);
                      			if ($marqueur['contour']) $plot->mark->SetColor($marqueur['contour']);
                      			if ($marqueur['fond']) $plot->mark->SetFillColor($marqueur['fond']);
                      			if ($marqueur['epaisseur'])$plot->mark->SetWidth($marqueur['epaisseur']);
					if ($option['histogramme']) $plot->SetStepStyle();
                            // titre & legende 
			    if ($legendedeux[0]) $plot->SetLegend($legendedeux[0]);
			    
			    if ($donneetrois) {
				$plot3=new LinePlot($donneetrois);
				if ($couleurtrois['contour']) $plot3->SetColor($couleurtrois['contour']);
				if ($couleurtrois['degrade']) $plot3->SetFillGradient($couleurtrois['fond'],$couleurtrois['degrade']);
				if ($couleurtrois['fond']) $plot3->SetFillColor($couleurtrois['fond']);
				// L'epaisseur est sorti du modele, voir comment reintegrer ulterieurement
				//   if ($epaisseur) $plot->SetWeight($epaisseur);
				if (isset($marqueur_formes[$marqueurtrois['nom']])) 
					$plot3->mark->SetType($marqueur_formes[$marqueurtrois['nom']]);
				if ($marqueurtrois['contour']) $plot3->mark->SetColor($marqueurtrois['contour']);
				if ($marqueurtrois['fond']) $plot3->mark->SetFillColor($marqueurtrois['fond']);
				if ($marqueurtrois['epaisseur'])$plot3->mark->SetWidth($marqueurtrois['epaisseur']);
				if ($option['histogramme']) $plot3->SetStepStyle();
				$graph->Add($plot3);
				if ($legendedeux[2]) $plot3->SetLegend($legendedeux[2]);
			    }
			    
			    if ($donneedeux) {
				$plot2=new LinePlot($donneedeux);
				if ($couleurdeux['contour']) $plot2->SetColor($couleurdeux['contour']);
				if ($couleurdeux['degrade']) $plot2->SetFillGradient($couleurdeux['fond'],$couleurdeux['degrade']);
				if ($couleurdeux['fond']) $plot2->SetFillColor($couleurdeux['fond']);
				// L'epaisseur est sorti du modele, voir comment reintegrer ulterieurement
				//   if ($epaisseur) $plot->SetWeight($epaisseur);
				if (isset($marqueur_formes[$marqueurdeux['nom']])) 
					$plot2->mark->SetType($marqueur_formes[$marqueurdeux['nom']]);
				if ($marqueurdeux['contour']) $plot2->mark->SetColor($marqueurdeux['contour']);
				if ($marqueurdeux['fond']) $plot2->mark->SetFillColor($marqueurdeux['fond']);
				if ($marqueurdeux['epaisseur'])$plot2->mark->SetWidth($marqueurdeux['epaisseur']);
				if ($option['histogramme']) $plot2->SetStepStyle();
				$graph->Add($plot2);
				if ($legendedeux[1]) $plot2->SetLegend($legendedeux[1]);
			    }
			    
			    if ($legendetrois[0]) $graph->xaxis->title->Set($legendetrois[0]);
			    if ($legendetrois[1]) $graph->yaxis->title->Set($legendetrois[1]);
			    
                            $graph->title->Set(utf8_decode($titre));
                            if (count($legende)>1) 
                                {$graph->xaxis->SetTickLabels($legende); $graph->legend->SetReverse();}  
                            break;
                            
        case "barre":       $graph = new Graph($largeur,$hauteur);
                            $graph->SetScale('textlin');
                            // Create the linear plot
                            $plot1 = new BarPlot($donnee);
                            // style & couleur
                      			if ($couleur['contour']) $plot1->SetColor($couleur['contour']);
                      			// Le degrade pour les barres est tres specifique (un peu comme le modele de marqueur) et doit etre traite dans une fonction supplementaire, sera fait ulterieurement
                      			if ($couleur['degrade']) $plot1->SetFillGradient($couleur['fond'],$couleur['degrade'],GRAD_VER);
                      			
                      			//petit patch en attendant d'uniformiser la doc : pour l'instant la doc indique que couleur=blue doit remplir en bleu les barres, alors que l'uniformisation des
                      			// couleurs indiquera plutot qu'il s'agit d'une couleur de contour et non une couleur de fond
                      			if (($couleur['contour']) AND (!$couleur['fond'])) $plot1->SetFillColor($couleur['contour']);
                      			//Devra etre probablement supprime ulterieurement, ou alors on garde cela, dans le cas d'oubli de la couleur de fond
                      			
                      			if ($couleur['fond']) $plot1->SetFillColor($couleur['fond']);
					if ($legendedeux[0]) $plot1->SetLegend($legendedeux[0]);
                           $group_plot[0]= $plot1;
			   
			   if ($donneedeux) {
				$plot2 = new BarPlot($donneedeux);
				if ($couleurdeux['contour']) $plot2->SetColor($couleurdeux['contour']);
				if (($couleurdeux['contour']) AND (!$couleurdeux['fond'])) $plot2->SetFillColor($couleurdeux['contour']);
				if ($couleurdeux['degrade']) $plot2->SetFillGradient($couleurdeux['fond'],$couleurdeux['degrade'],GRAD_VER);
                      		if ($couleurdeux['fond']) $plot2->SetFillColor($couleurdeux['fond']);
				if ($legendedeux[1]) $plot2->SetLegend($legendedeux[1]);
				$group_plot[1]= $plot2;
			   }

			   if ($donneetrois) {
				$plot3 = new BarPlot($donneetrois);
				if ($couleurtrois['contour']) $plot3->SetColor($couleurtrois['contour']);
				if (($couleurtrois['contour']) AND (!$couleurtrois['fond'])) $plot3->SetFillColor($couleurtrois['contour']);
				if ($couleurtrois['degrade']) $plot3->SetFillGradient($couleurtrois['fond'],$couleurtrois['degrade'],GRAD_VER);
                      		if ($couleurtrois['fond']) $plot3->SetFillColor($couleurtrois['fond']);
				if ($legendedeux[2]) $plot3->SetLegend($legendedeux[2]);
				$group_plot[2]= $plot3;
			   }

			    $plot = new  GroupBarPlot ($group_plot);
			    // titre & legende 
                            $graph->title->Set(utf8_decode($titre));
                            if (count($legende)>1)
                                {$graph->xaxis->SetTickLabels($legende); $graph->legend->SetReverse();}
			if ($legendetrois[0]) $graph->xaxis->title->Set($legendetrois[0]);
			if ($legendetrois[1]) $graph->yaxis->title->Set($legendetrois[1]);
                            break;
	
         case "accbarre":       $graph = new Graph($largeur,$hauteur);
                            $graph->SetScale('textlin');
                            // Create the linear plot
                            $plot1 = new BarPlot($donnee);
                            // style & couleur
                      			if ($couleur['contour']) $plot1->SetColor($couleur['contour']);
                      			// Le degrade pour les barres est tres specifique (un peu comme le modele de marqueur) et doit etre traite dans une fonction supplementaire, sera fait ulterieurement
                      			if ($couleur['degrade']) {$plot1->SetFillGradient($couleur['fond'],$couleur['degrade'],GRAD_VER); $plot1->SetWeight(0);}
                      			
                      			//petit patch en attendant d'uniformiser la doc : pour l'instant la doc indique que couleur=blue doit remplir en bleu les barres, alors que l'uniformisation des
                      			// couleurs indiquera plutot qu'il s'agit d'une couleur de contour et non une couleur de fond
                      			if (($couleur['contour']) AND (!$couleur['fond'])) $plot1->SetFillColor($couleur['contour']);
                      			//Devra etre probablement supprime ulterieurement, ou alors on garde cela, dans le cas d'oubli de la couleur de fond
                      			
                      			if ($couleur['fond']) $plot1->SetFillColor($couleur['fond']);
					if ($legendedeux[0]) $plot1->SetLegend($legendedeux[0]);
					
                           $group_plot[0]= $plot1;
			   
			   if ($donneedeux) {
				$plot2 = new BarPlot($donneedeux);
				if ($couleurdeux['contour']) $plot2->SetColor($couleurdeux['contour']);
				if (($couleurdeux['contour']) AND (!$couleurdeux['fond'])) $plot2->SetFillColor($couleurdeux['contour']);
				if ($couleurdeux['degrade']) {$plot2->SetFillGradient($couleurdeux['fond'],$couleurdeux['degrade'],GRAD_VER); $plot2->SetWeight(0);}
                      		if ($couleurdeux['fond']) $plot2->SetFillColor($couleurdeux['fond']);
				if ($legendedeux[1]) $plot2->SetLegend($legendedeux[1]);
				$group_plot[1]= $plot2;
			   }

			   if ($donneetrois) {
				$plot3 = new BarPlot($donneetrois);
				if ($couleurtrois['contour']) $plot3->SetColor($couleurtrois['contour']);
				if (($couleurtrois['contour']) AND (!$couleurtrois['fond'])) $plot3->SetFillColor($couleurtrois['contour']);
				if ($couleurtrois['degrade']) {$plot3->SetFillGradient($couleurtrois['fond'],$couleurtrois['degrade'],GRAD_VER); $plot3->SetWeight(0);}
                      		if ($couleurtrois['fond']) $plot3->SetFillColor($couleurtrois['fond']);
				if ($legendedeux[2]) $plot3->SetLegend($legendedeux[2]);
				$group_plot[2]= $plot3;
			   }

			    $plot = new AccBarPlot($group_plot);
			    // titre & legende 
                            $graph->title->Set(utf8_decode($titre));
                            if (count($legende)>1)
                                {$graph->xaxis->SetTickLabels($legende); $graph->legend->SetReverse();}
			if ($legendetrois[0]) $graph->xaxis->title->Set($legendetrois[0]);
			if ($legendetrois[1]) $graph->yaxis->title->Set($legendetrois[1]);
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
			    //Detacher une, plusieurs ou toutes les parties du camembert
			    if ($option['camembert_detacher']) 
			    {
				$ecartement=20;
				if (isset($option['camembert_ecart'])) $ecartement=(int) $option['camembert_ecart'];
				if ($option['camembert_detacher']=='tout') $plot->ExplodeAll($ecartement);
				else 
				{
					$detachement = explode(",", $option['camembert_detacher']);
					foreach ($donnee as $key => $value)
					{
						$secteur[$key]=0;
						foreach ($detachement as $key1 => $value1) {if ($key==((int)$value1-1)) $secteur[$key]=$ecartement;}
					}
					$plot->Explode($secteur);
				}
			    }
			    //Option de resolution fine
			    if ($option['resolution_fine']) $graph->SetAntiAliasing();
                            // titre & legende 
                            $graph->title->Set(utf8_decode($titre));
                            if (count($legende)>1) 
                                $plot->SetLegends($legende);
                            break;
	case "camembert3d":  $graph = new PieGraph($largeur,$hauteur);
			    // Create the linear plot
                            $plot = new PiePlot3D($donnee);
			    //Choisir un autre angle de visualisation en 3D
			    if ($option['camembert_angle']) $plot->SetAngle((int) $option['camembert_angle']);
                            // style & couleur
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
			    //Detacher une, plusieurs ou toutes les parties du camembert
			    if ($option['camembert_detacher']) 
			    {
				$ecartement=20;
				if (isset($option['camembert_ecart'])) $ecartement=(int) $option['camembert_ecart'];
				if ($option['camembert_detacher']=='tout') $plot->ExplodeAll($ecartement);
				else 
				{
					$detachement = explode(",", $option['camembert_detacher']);
					foreach ($donnee as $key => $value)
					{
						$secteur[$key]=0;
						foreach ($detachement as $key1 => $value1) {if ($key==((int)$value1-1)) $secteur[$key]=$ecartement;}
					}
					$plot->Explode($secteur);
				}
			    }
			    //Option de resolution fine
			    if ($option['resolution_fine']) $graph->SetAntiAliasing();
                            // titre & legende 
                            $graph->title->Set(utf8_decode($titre));
                            if (count($legende)>1) 
                                $plot->SetLegends($legende);        
                            break;
    }
    
    //*********Options du graphe************
    //Ombre du graphe
    if ($option['graphe_ombre']) $graph->SetShadow();
    //Couleur de la bordure du graphe
    if ($option['graphe_bordure_couleur']) $graph->SetFrame(true,$option['graphe_bordure_couleur'],2);
    //Couleur de fond de la partie graphique du graphe
    if ($option['graphe_couleur']) $graph->SetColor($option['graphe_couleur']);
    //Couleur de fond des marges du graphe
    if ($option['graphe_couleur_fond']) $graph->SetMarginColor($option['graphe_couleur_fond']);
    //Dégradé en fond de graphe...
    if (isset($option['graphe_couleur_degrade1']) AND isset($option['graphe_couleur_degrade2'])) 
    {
	$style_degrades=array('horizontal1'=>GRAD_VER,'horizontal2'=>GRAD_MIDVER,'vertical1'=>GRAD_HOR,'vertical2'=>GRAD_MIDHOR,'horizontal3'=>GRAD_LEFT_REFLECTION,'horizontal4'=>GRAD_RIGHT_REFLECTION,'centre1'=>GRAD_CENTER,'centre2'=>GRAD_WIDE_MIDHOR,'centre3'=>GRAD_WIDE_MIDVER,);
	foreach ($style_degrades as $cle=>$val)
	{
		if ($option['graphe_style_degrade']==$cle)
		{
			if ($option['graphe_domaine_degrade']=='tout') {$graph->SetBackgroundGradient($option['graphe_couleur_degrade1'],$option['graphe_couleur_degrade2'],$val,BGRAD_FRAME);}
			if ($option['graphe_domaine_degrade']=='marge') {$graph->SetBackgroundGradient($option['graphe_couleur_degrade1'],$option['graphe_couleur_degrade2'],$val,BGRAD_MARGIN);}
			if ($option['graphe_domaine_degrade']=='graphique') {$graph->SetBackgroundGradient($option['graphe_couleur_degrade1'],$option['graphe_couleur_degrade2'],$val,BGRAD_PLOT);}
		}
	}
    }
    
    //Effectuer une rotation du graphe
    if ($option['graphe_angle']) $graph->SetAngle((int)$option['graphe_angle']);
    //Style des axes...
    // if ($option['graphe_axe_style']=='double') $graph->SetAxisStyle(AXSTYLE_BOXIN);
    
    //Espacement des graduations verticales
    if ($option['graduation_verticale_espacement']=='proche') $graph->SetTickDensity(TICKD_DENSE);
    if ($option['graduation_verticale_espacement']=='normal') $graph->SetTickDensity(TICKD_NORMAL);
    if ($option['graduation_verticale_espacement']=='large') $graph->SetTickDensity(TICKD_SPARSE);
    
    // Couleur du titre
    if ($option['titre_couleur']) $graph->title->SetColor($option['titre_couleur']);

    //************Fin des options************
    
    
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