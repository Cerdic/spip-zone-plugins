<?php



/*
 * Modele pour les diferents type de panneaux
 *  
 */
abstract class assoc_panel{
	

	private $nom;
	private $les_widgets;	
	
	/* 
	 * Le constructeur est chargé de construire le html  
	 * plus exactement il  recupere les 'widget'
	 * chargé de donnee des possibilite de recherche par exemple
	 */
	function __construct($nom){
		$this->nom  = $nom;
	}
	
	function get_panel(){
		// On recupere les widgets
		$this->les_widgets  = $this->widgets();
		// On recupere la css
		$retour .=  $this->css_js();
		// On construit le panel
		$retour .=  $this->do_panel();
		return  $retour;
	}
	
	
	// on va recuperer la css en chargeant le dossier
	// css approprie en l'englobant dans des balises styles
	function css_js(){
		$nom = $this->nom; 
		$fichier = dirname (__FILE__)."/../../../plugins/assoc/css/assoc.css";
		$open = fopen($fichier, "r");
		$retour = fread($open, filesize($fichier) );
		fclose($open);
		
		$fichier = dirname (__FILE__)."/../../../plugins/assoc/css/assoc_$nom.css";
		if(file_exists($fichier)){
			$open = fopen($fichier, "r");
			$retour .= fread($open, filesize($fichier) );
			fclose($open);
			$css =  "<style id='style_assoc_panel'>".$retour."</style>";
		}
		
		$fichier = dirname (__FILE__)."/../../../plugins/assoc/js/assoc_$nom.js";
		if(file_exists($fichier)){
			$open = fopen($fichier, "r");
			$retour = fread($open, filesize($fichier) );
			fclose($open);
			$js =  "<script id='js_assoc_panel'>".$retour."</script>";
		}
		return $css.$js;
		
	}
	
	// fonction qui va construire le panneau
	// il va egalement recuperer les widgets
	function do_panel(){
		$zone = $this->zone;
		$recherche = $this->les_widgets['panel_recherche'];
		$assoc = $this->les_widgets['panel_assoc'];
		
		// On va recuperer la taille de chaque element
		$a = $this->les_widgets['taille'];
		$taille = explode(",",$a);
		$large = $taille[0];
		$haut = $taille[1];
		
		$haut_insert = $taille[3];
		
		// la recherche
		$large_recherche = $taille[2];
		$haut_recherche = $haut-16;
		
		// les resultats
		$large_resultat = $large - $large_recherche;
		$haut_resultat = $haut - 16 - $haut_insert;
		
		// zone d'insertion
		$large_insert = $large - $large_recherche;
		$top_insert = $haut-$haut_insert;
		
		$style = '
			<style>
					#panel_conteneur {width : '.$large.'px ; height : '.$haut.'px };
					#panel_recherche {width : '.$large_recherche.'px ; height : '.$haut_recherche.'px};
					#panel_resultat {  left : '.$large_recherche.'px ; width : '.$large_resultat.'px ; height : '.$haut_resultat.'px ; }		
					##assoc_panel_assoc {  top : '.$top_insert.'px ; left : '.$large_recherche.'px ; width : '.$large_insert.'px ; height : '.$haut_insert.'px ;  }
			
			</style>
		';
		
	
		
		$retour =$style."
				<div id='panel_conteneur'>
					<div id='panel_relative'>
						<div id='panel_haut'><span id='fermer_panel' onclick='class_assoc.close()'/>Fermer</div>
						<div id='panel_recherche'>$recherche</div>
						<div id='panel_resultat'></div>
						<div id='panel_assoc'>$assoc</div>
					</div>
				</div>
				";

		return $retour;
		
	}
	
	function get_pagination(){
		return "<p id='pagination'>
					<span class='btn-nav' id='retour5' onclick='assoc_object.pagination(-5)'>  </span>
					<span class='btn-nav' id='retour1' onclick='assoc_object.pagination(-1)'></span>
					<span>page</span>
					<span class='btn-nav' id='avance1' onclick='assoc_object.pagination(1)'></span>
					<span class='btn-nav' id='avance5' onclick='assoc_object.pagination(5)'> </span>
				</p>";
	}

	
	// recherche 
	abstract function find();
	
	// affichage de la recherche
	abstract function style_find();
	
	// On va passer nos sympathique widget
	abstract function widgets();
}


class assoc_panel_spip extends  assoc_panel {
	
	public function find(){
		/* On va recuperer l'ensemble des parametres venant de la recherche */
		$debut = $_POST["debut"];
		$fin = $_POST["fin"];
		$page = $_POST["page"] * 10; 
		$rub = $_POST["rubrique"];
		$secteur = $_POST["secteur"];
		
		if($secteur!="" && $secteur != 193){
			$tab = array ("rubrique" => $rub , "secteur" => $secteur , "debut" => $debut ,"fin" => $fin ,"page" => $page );
			$xml = new SimpleXMLElement(recuperer_fond("fonds/recherche/assoc_art_find",$tab));
		}
		if($secteur == 193){
			$tab = array ( "debut" => $debut ,"fin" => $fin ,"page" => $page );
			$xml = new SimpleXMLElement(recuperer_fond("fonds/recherche/assoc_forum_find",$tab));
		}		
		if($secteur=="") {
			$tab = array ("page" => $page );
			$xml = new SimpleXMLElement(recuperer_fond("fonds/recherche/assoc_art_touterub_find",$tab));
		}
		// on recupere le nbre d'article 
		// s'il n'y en a pas on affiche pas de resultat
		$taille = count($xml->article);
		
		if ($taille==0){
			echo "Aucun résultat pour votre recherche d'article ";
			return;
		}
		
		$pagin = $this->get_pagination();
		$pagin = str_replace("page",$_POST["page"],$pagin);
		
		$retour = "$pagin<table><tr><td width='100'><strong>Date</strong></td><td width='180'><strong>Titre</strong></td><td width='190'><strong>texte</strong></td><td width='90'><strong>Rub</strong></td><td width='60'></td></tr>";

		for($i=0; $i <$taille;$i++){
			$id = $xml->article[$i]->id;
			
			$retour .= "<tr>";
			$retour .= "<td>".$xml->article[$i]->date."</td>";
			$retour .= "<td class='titre' value='$id'>".$xml->article[$i]->titre."</td>";
			$retour .= "<td>".$xml->article[$i]->texte."</td>";
			$retour .= "<td>".$xml->article[$i]->rubrique."</td>";
			$retour .= "<td class='preselect' onclick='assoc_object.preselect(this,\"article\")'>associer</td>";
			$retour .= "</tr>";
		}
		echo $retour."</table>";
	}

	
	public function style_find(){
	}
	
	public function widgets(){
		$tab["panel_recherche"] = recuperer_fond("fonds/panel/assoc_rub_select");
		$tab["panel_assoc"] = "<p id='preselect_element'></p>";
		
		// Pour la taille : 
		// largeur du panneau global 
		// hauteur du panneau global 
		// largeur de la zone de recherche
		// hauteur de la zone de insertion
		$tab["taille"] = '650,500,120,60';
		return $tab;
	}
	
}


class assoc_panel_mag2008 extends  assoc_panel {
	
	public function find(){
		/* On va recuperer l'ensemble des parametres venant de la recherche */
		$debut = $_POST["debut"];
		$fin = $_POST["fin"];
		$page = $_POST["page"] * 10; 
		$rub = $_POST["rubrique"];
		$secteur = $_POST["secteur"];
	
		
		if($secteur!="" && $secteur != 193){
			$tab = array ("rubrique" => $rub , "secteur" => $secteur , "debut" => $debut ,"fin" => $fin ,"page" => $page );
			$xml = new SimpleXMLElement(recuperer_fond("fonds/assoc_art_find",$tab));
		}
		if($secteur == 193){
			$tab = array ( "debut" => $debut ,"fin" => $fin ,"page" => $page );
			$xml = new SimpleXMLElement(recuperer_fond("fonds/assoc_forum_find",$tab));
		}
		
		if($secteur=="") {
			$tab = array ("page" => $page );
			$xml = new SimpleXMLElement(recuperer_fond("fonds/assoc_art_touterub_find",$tab));
		}

		
		// on recupere le nbre d'article 
		// s'il n'y en a pas on affiche pas de resultat
		$taille = count($xml->article);
		
		if ($taille==0){
			echo "Aucun résultat pour votre recherche d'article ";
			return;
		}

		$pagin = $this->get_pagination();
		$pagin = str_replace("page",$_POST["page"],$pagin);
		
		$retour = "$pagin<table><tr><td width='100'><strong>Date</strong></td><td width='180'><strong>Titre</strong></td><td width='190'><strong>Texte</strong></td><td width='90'><strong>Rub</strong></td><td width='60'></td></tr>";

		for($i=0; $i <$taille;$i++){
			$id = $xml->article[$i]->id;
			
			$retour .= "<tr>";
			$retour .= "<td>".$xml->article[$i]->date."</td>";
			$retour .= "<td class='titre' value='$id'>".$xml->article[$i]->titre."</td>";
			$retour .= "<td>".$xml->article[$i]->texte."</td>";
			$retour .= "<td>".$xml->article[$i]->rubrique."</td>";
			$retour .= "<td class='preselect' onclick='assoc_object.preselect(this,\"article\")'>associer</td>";
			$retour .= "</tr>";
		}
		echo $retour."</table>";
	}

	
	public function style_find(){
	}
	
	public function widgets(){
		$tab["assoc_panel_bt_haut"] = bt_haut_classique::get_widget();
		$tab["assoc_panel_recherche"] = recherche_basic::get_widget();
		$tab["assoc_panel_assoc"] = association_mag2008::get_widget();
		$tab["assoc_panel_bt_bas"] = selection_basic_inserer::get_widget();
		return $tab;
	}
	
}

class assoc_panel_video extends  assoc_panel {
	
	public function find(){
		// recherche sur les videos par date
		if ($_POST["type"]=="date"){

			$page = $_POST["page"] * 10;
			$tab = array ( "debut" => $_POST["debut"] ,"fin" => $_POST["fin"] ,"page" => $page  ,"num" => 10);
			$xml = new SimpleXMLElement(recuperer_fond("fonds/assoc_omm_video",$tab));
			$taille = count($xml->omm);
			
			
			$pagin ="<p id='pagination'>
						<span id='retour5' onclick='assoc_object.pagination_video(-5)'>  </span>
						<span id='retour1' onclick='assoc_object.pagination_video(-1)'></span>
						<span>page</span>
						<span  id='avance1' onclick='assoc_object.pagination_video(1)'></span>
						<span  id='avance5' onclick='assoc_object.pagination_video(5)'> </span>
					</p>";
			$pagin = str_replace("page",$_POST["page"],$pagin);
			if ($taille==0){
				echo "$pagin Aucun résultat pour votre recherche de video";
				return;
			}
			
			$retour = "<div id='table_resultat'>";
			$retour .= "$pagin<table><tr><td width='100'>Date</td><td width='180'>Titre</td><td width='190'>texte</td><td width='10'></td><td width='40'></td></tr>";

			for($i=0; $i <$taille;$i++){
				$id = $xml->omm[$i]->id;
				$retour .= "<tr id='tr$id'>";
				$retour .= "<td>".$xml->omm[$i]->date."</td>";
				$retour .= "<td class='titre' value='$id'>".$xml->omm[$i]->titre."</td>";
				$retour .= "<td class='desc'>".$xml->omm[$i]->texte."</td>";
				$retour .= "<td><span class='masquer' id='la_video$id'>".$xml->omm[$i]->embed."<span></td>";
				$retour .= "<td class='preselect' onclick='assoc_object.preselect_titre_desc($id)'>associer</td>";
				$retour .= "</tr>";
			}
			echo $retour."</table></div>";
			
		}
	}

	
	public function mot_find(){
		// On appelle la fonction de recherche sur les 
		// tables ayant les champs titre et descriptif
		// On fait les tests avant l'appelle de la fonction
		$mots = trim($_POST["mot"]);
		$type = $_POST["type"];
		
		// on verifie qu'au moins 4 careactères ont été saisi
		if (strlen($mots) < 4 ) {
			echo "Pour effectuer une recherche vous devez écrire 4 caractères minimun ";
			return;
		}
		
		include_spip("inc/assoc_recherche_mot");
		// on cree un tableau avec les champs que l'on souhaite recuperer
		// le premier correspond a l'id
		$champs = array("id_video","titre","descriptif","date");
		
		
		// on  va recuperer un tableau avec les resultats
		$a = new assoc_recherche_mots("omm_video",$mots,$type,$champs);
		$valid = $a->get_valid_search();
		// On test si on effectue une recherche
		if (!$valid["valid"]){
			echo $valid["erreur"];
		}else{
			if ($valid["recherche"]=="seul") $tab = $a->recherche_seul();
			if ($valid["recherche"]=="tous") $tab = $a->recherche_tous();
		}
		
		// s'il y a des resultats on les affiches
		if (count($tab)==0){
			echo "<br><br>Il n'y a pas de résultats à votre recherche.";
			return;
		}else{
			include_spip("inc/assoc_rendu_recherche");
			echo rendu_recherche_mot_video_exec($tab,"video");
		}
		
	}
	
	public function exec_find(){
		// recherche sur les videos par date
		if ($_POST["type"]=="date"){
			$tab = array ( "debut" => $_POST["debut"] ,"fin" => $_POST["fin"] ,"page" => 0 ,"num" => 500);
			$xml = new SimpleXMLElement(recuperer_fond("fonds/assoc_omm_video",$tab));
			$taille = count($xml->omm);
			if ($taille==0){
				echo "Aucun résultat pour votre recherche de video";
				return;
			}
			// alex
						$retour .= "<table><tr class='titre_tab_omm'><td width='100'>Date</td><td width='120'>Titre</td>
						<td width='190'>Texte</td><td width='10'></td><td width='200'></td></tr>";
			for($i=0; $i <$taille;$i++){
				$id = $xml->omm[$i]->id;
				$valid = $xml->omm[$i]->valid;
				
				$sup = "&nbsp;|&nbsp;<span class='resul' onclick='delete_omm($id,\"video\")'>Supprimer</span>";
				if ($valid=="oui") $sup = "";
				$voir ="<span class='resul' onclick='show_omm($id,\"video\")'>Voir</span>";
				$img = "<img src='../plugins/assoc/img/video_mini.png'/>&nbsp;&nbsp;";
				
				
				$retour .= "<tr id='tr$id'>";
				$retour .= "<td>".$xml->omm[$i]->date."</td>";
				$retour .= "<td class='titre' value='$id'>".$xml->omm[$i]->titre."</td>";
				$retour .= "<td>".$xml->omm[$i]->texte."</td>";
				$retour .= "<td><span class='masquer' id='la_video$id'></span></td>";
				$retour .= "<td>$img$voir &nbsp;|&nbsp;<a href='?exec=edit_video&id_video=$id'>Editer</a>$sup</td>";
				$retour .= "</tr>";
			}
			echo $retour."</table>";
			
		}
	}
	
	public function style_find(){
	}
	
	public function widgets(){
		$tab["assoc_panel_bt_haut"] = bt_haut_classique::get_widget();
		$tab["assoc_panel_onglet"] = onglet_omm::get_widget();
		$tab["assoc_panel_recherche"] = recherche_date::get_widget();
		$tab["assoc_panel_assoc"] = association_video::get_widget();
		$tab["assoc_panel_bt_bas"] = selection_basic_associer::get_widget();
		return $tab;
	}
	
}


class assoc_panel_video_home extends  assoc_panel {
	
	public function find(){
		// recherche sur les videos par date
		if ($_POST["type"]=="date"){

			$page = $_POST["page"] * 10;
			$tab = array ( "debut" => $_POST["debut"] ,"fin" => $_POST["fin"] ,"page" => $page  ,"num" => 10);
			$xml = new SimpleXMLElement(recuperer_fond("fonds/assoc_omm_video",$tab));
			$taille = count($xml->omm);
			
			
			$pagin ="<p id='pagination'>
						<span id='retour5' onclick='assoc_object.pagination_video(-5)'>  </span>
						<span id='retour1' onclick='assoc_object.pagination_video(-1)'></span>
						<span>page</span>
						<span  id='avance1' onclick='assoc_object.pagination_video(1)'></span>
						<span  id='avance5' onclick='assoc_object.pagination_video(5)'> </span>
					</p>";
			$pagin = str_replace("page",$_POST["page"],$pagin);
			if ($taille==0){
				echo "$pagin Aucun résultat pour votre recherche de video";
				return;
			}
			
			$retour = "<div id='table_resultat'>";
			$retour .= "$pagin<table><tr><td width='100'>Date</td><td width='180'>Titre</td><td width='190'>texte</td><td width='10'></td><td width='40'></td></tr>";

			for($i=0; $i <$taille;$i++){
				$valid = "associer";
				if ( (string)$xml->omm[$i]->valid =="") $valid="";
				$id = $xml->omm[$i]->id;
				$retour .= "<tr id='tr$id'>";
				$retour .= "<td>".$xml->omm[$i]->date."</td>";
				$retour .= "<td class='titre' value='$id'>".$xml->omm[$i]->titre."</td>";
				$retour .= "<td class='desc'>".$xml->omm[$i]->texte."</td>";
				$retour .= "<td><span class='masquer' id='la_video$id'>".$xml->omm[$i]->embed."<span></td>";
				if($valid !="") $retour .= "<td class='preselect' onclick='assoc_object.preselect_titre_desc($id)'>$valid</td>";
				$retour .= "</tr>";
			}
			echo $retour."</table></div>";
			
		}
	}

	
	public function mot_find(){
		// On appelle la fonction de recherche sur les 
		// tables ayant les champs titre et descriptif
		// On fait les tests avant l'appelle de la fonction
		$mots = trim($_POST["mot"]);
		$type = $_POST["type"];
		
		// on verifie qu'au moins 4 careactères ont été saisi
		if (strlen($mots) < 4 ) {
			echo "Pour effectuer une recherche vous devez écrire 4 caractères minimun ";
			return;
		}
		
		include_spip("inc/assoc_recherche_mot");
		// on cree un tableau avec les champs que l'on souhaite recuperer
		// le premier correspond a l'id
		$champs = array("id_video","titre","descriptif","date");
		
		
		// on  va recuperer un tableau avec les resultats
		$a = new assoc_recherche_mots("omm_video",$mots,$type,$champs);
		$valid = $a->get_valid_search();
		// On test si on effectue une recherche
		if (!$valid["valid"]){
			echo $valid["erreur"];
		}else{
			if ($valid["recherche"]=="seul") $tab = $a->recherche_seul();
			if ($valid["recherche"]=="tous") $tab = $a->recherche_tous();
		}
		
		// s'il y a des resultats on les affiches
		if (count($tab)==0){
			echo "<br><br>Il n'y a pas de résultats à votre recherche.";
			return;
		}else{
			include_spip("inc/assoc_rendu_recherche");
			echo rendu_recherche_mot_video_exec($tab);
		}
		
	}
	
	public function exec_find(){
		// recherche sur les videos par date
		if ($_POST["type"]=="date"){
			$tab = array ( "debut" => $_POST["debut"] ,"fin" => $_POST["fin"] ,"page" => 0 ,"num" => 500);
			$xml = new SimpleXMLElement(recuperer_fond("fonds/assoc_omm_video",$tab));
			$taille = count($xml->omm);
			if ($taille==0){
				echo "Aucun résultat pour votre recherche de video";
				return;
			}
			
			$retour .= "<table><tr><td width='100'>Date</td><td width='180'>Titre</td><td width='10'></td><td width='40'></td></tr>";
			for($i=0; $i <$taille;$i++){
				$id = $xml->omm[$i]->id;
				$retour .= "<tr id='tr$id'>";
				$retour .= "<td>".$xml->omm[$i]->date."</td>";
				$retour .= "<td class='titre' value='$id'>".$xml->omm[$i]->titre."</td>";
				$retour .= "<td><span class='masquer' id='la_video$id'><span></td>";
				$retour .= "<td><a href='?exec=edit_video&id_video=$id'>Editer</a></td>";
				$retour .= "</tr>";
			}
			echo $retour."</table>";
			
		}
	}
	
	public function style_find(){
	}
	
	public function widgets(){
		$tab["assoc_panel_bt_haut"] = bt_haut_classique::get_widget();
		$tab["assoc_panel_onglet"] = onglet_omm::get_widget();
		$tab["assoc_panel_recherche"] = recherche_date::get_widget();
		$tab["assoc_panel_assoc"] = association_video::get_widget();
		$tab["assoc_panel_bt_bas"] = selection_video_home_inserer::get_widget();
		return $tab;
	}
	
}


class assoc_panel_actuphonore extends  assoc_panel {
	
	public function find(){
		// recherche sur les videos par date
		if ($_POST["type"]=="date"){

			$page = $_POST["page"] * 10;
			$tab = array ( "debut" => $_POST["debut"] ,"fin" => $_POST["fin"] ,"page" => $page  ,"num" => 10);
			$xml = new SimpleXMLElement(recuperer_fond("fonds/assoc_omm_actuphonore",$tab));
			$taille = count($xml->omm);
			
			
			$pagin ="<p id='pagination'>
						<span id='retour5' onclick='assoc_object.pagination_video(-5)'>  </span>
						<span id='retour1' onclick='assoc_object.pagination_video(-1)'></span>
						<span>page</span>
						<span  id='avance1' onclick='assoc_object.pagination_video(1)'></span>
						<span  id='avance5' onclick='assoc_object.pagination_video(5)'> </span>
					</p>";
			$pagin = str_replace("page",$_POST["page"],$pagin);
			if ($taille==0){
				echo "$pagin Aucun résultat pour votre recherche de doc phonore";
				return;
			}
			
			$retour = "<div id='table_resultat'>";
			$retour .= "$pagin<table><tr><td width='100'>Date</td><td width='180'>Titre</td><td width='190'>texte</td><td width='10'></td><td width='40'></td></tr>";

			for($i=0; $i <$taille;$i++){
				$id = $xml->omm[$i]->id;
				$retour .= "<tr id='tr$id'>";
				$retour .= "<td>".$xml->omm[$i]->date."</td>";
				$retour .= "<td class='titre' value='$id'>".$xml->omm[$i]->titre."</td>";
				$retour .= "<td class='desc'>".$xml->omm[$i]->texte."</td>";
				$retour .= "<td class='preselect' onclick='assoc_object.preselect_titre_desc($id)'>associer</td>";
				$retour .= "</tr>";
			}
			echo $retour."</table></div>";
			
		}
	}

	
	public function mot_find(){
		// On appelle la fonction de recherche sur les 
		// tables ayant les champs titre et descriptif
		// On fait les tests avant l'appelle de la fonction
		$mots = trim($_POST["mot"]);
		$type = $_POST["type"];
		
		// on verifie qu'au moins 4 careactères ont été saisi
		if (strlen($mots) < 4 ) {
			echo "Pour effectuer une recherche vous devez écrire 4 caractères minimun ";
			return;
		}
		
		include_spip("inc/assoc_recherche_mot");
		// on cree un tableau avec les champs que l'on souhaite recuperer
		// le premier correspond a l'id
		$champs = array("id_actuphonore","titre","descriptif","date");
		
		
		// on  va recuperer un tableau avec les resultats
		$a = new assoc_recherche_mots("omm_actuphonore",$mots,$type,$champs);
		$valid = $a->get_valid_search();
		// On test si on effectue une recherche
		if (!$valid["valid"]){
			echo $valid["erreur"];
		}else{
			if ($valid["recherche"]=="seul") $tab = $a->recherche_seul();
			if ($valid["recherche"]=="tous") $tab = $a->recherche_tous();
		}
		
		// s'il y a des resultats on les affiches
		if (count($tab)==0){
			echo "<br><br>Il n'y a pas de résultats à votre recherche.";
			return;
		}else{
			include_spip("inc/assoc_rendu_recherche");
			echo rendu_recherche_mot_video_exec($tab,"actuphonore");
		}
		
	}
	
	public function exec_find(){
		
		// recherche sur les videos par date
		if ($_POST["type"]=="date"){
			$tab = array ( "debut" => $_POST["debut"] ,"fin" => $_POST["fin"] ,"page" => 0 ,"num" => 500);
			$xml = new SimpleXMLElement(recuperer_fond("fonds/assoc_omm_actuphonore",$tab));
			$taille = count($xml->omm);
			if ($taille==0){
				echo "Aucun résultat pour votre recherche de doc phonore";
				return;
			}
			// alex
						$retour .= "<table><tr class='titre_tab_omm'><td width='100'>Date</td><td width='120'>Titre</td>
						<td width='190'>Texte</td><td width='10'></td><td width='200'></td></tr>";
			for($i=0; $i <$taille;$i++){
				$id = $xml->omm[$i]->id;
				$valid = $xml->omm[$i]->valid;
				
				$sup = "&nbsp;|&nbsp;<span class='resul' onclick='delete_omm($id,\"actuphonore\")'>Supprimer</span>";
				if ($valid=="oui") $sup = "";
				$voir ="<span class='resul' onclick='show_omm($id,\"actuphonore\")'>Voir</span>";
				$img = "<img src='../plugins/assoc/img/actuphonore_mini.png'/>&nbsp;&nbsp;";
				
				$retour .= "<tr id='tr$id'>";
				$retour .= "<td>".$xml->omm[$i]->date."</td>";
				$retour .= "<td class='titre' value='$id'>".$xml->omm[$i]->titre."</td>";
				$retour .= "<td>".$xml->omm[$i]->texte."</td>";
				$retour .= "<td><span class='masquer' id='la_video$id'></span></td>";
				$retour .= "<td>$img$voir &nbsp;|&nbsp;<a href='?exec=edit_actuphonore&id_actu=$id'>Editer</a>$sup</td>";
				$retour .= "</tr>";
			}
			echo $retour."</table>";
			
		}
	}
	
	public function style_find(){
	}
	
	public function widgets(){
		$tab["assoc_panel_bt_haut"] = bt_haut_classique::get_widget();
		$tab["assoc_panel_recherche"] = recherche_date::get_widget();
		$tab["assoc_panel_assoc"] = association_video::get_widget();
		$tab["assoc_panel_bt_bas"] = selection_basic_associer::get_widget();
		return $tab;
	}
	
}


class assoc_panel_actuphonore_home extends  assoc_panel {
	
public function find(){
		// recherche sur les videos par date
		if ($_POST["type"]=="date"){

			$page = $_POST["page"] * 10;
			$tab = array ( "debut" => $_POST["debut"] ,"fin" => $_POST["fin"] ,"page" => $page  ,"num" => 10);
			$xml = new SimpleXMLElement(recuperer_fond("fonds/assoc_omm_actuphonore",$tab));
			$taille = count($xml->omm);
			
			
			$pagin ="<p id='pagination'>
						<span id='retour5' onclick='assoc_object.pagination_video(-5)'>  </span>
						<span id='retour1' onclick='assoc_object.pagination_video(-1)'></span>
						<span>page</span>
						<span  id='avance1' onclick='assoc_object.pagination_video(1)'></span>
						<span  id='avance5' onclick='assoc_object.pagination_video(5)'> </span>
					</p>";
			$pagin = str_replace("page",$_POST["page"],$pagin);
			if ($taille==0){
				echo "$pagin Aucun résultat pour votre recherche de doc phonore";
				return;
			}
			
			$retour = "<div id='table_resultat'>";
			$retour .= "$pagin<table><tr><td width='100'>Date</td><td width='180'>Titre</td><td width='190'>texte</td><td width='10'></td><td width='40'></td></tr>";

			for($i=0; $i <$taille;$i++){
				$valid = "associer";
				if ( (string)$xml->omm[$i]->valid =="") $valid="";
				$id = $xml->omm[$i]->id;
				$retour .= "<tr id='tr$id'>";
				$retour .= "<td>".$xml->omm[$i]->date."</td>";
				$retour .= "<td class='titre' value='$id'>".$xml->omm[$i]->titre."</td>";
				$retour .= "<td class='desc'>".$xml->omm[$i]->texte."</td>";
				if($valid !="") $retour .= "<td class='preselect' onclick='assoc_object.preselect_titre_desc($id)'>$valid</td>";
				$retour .= "</tr>";
			}
			echo $retour."</table></div>";
			
		}
	}

	
	public function mot_find(){
		// On appelle la fonction de recherche sur les 
		// tables ayant les champs titre et descriptif
		// On fait les tests avant l'appelle de la fonction
		$mots = trim($_POST["mot"]);
		$type = $_POST["type"];
		
		// on verifie qu'au moins 4 careactères ont été saisi
		if (strlen($mots) < 4 ) {
			echo "Pour effectuer une recherche vous devez écrire 4 caractères minimun ";
			return;
		}
		
		include_spip("inc/assoc_recherche_mot");
		// on cree un tableau avec les champs que l'on souhaite recuperer
		// le premier correspond a l'id
		$champs = array("id_video","titre","descriptif","date");
		
		
		// on  va recuperer un tableau avec les resultats
		$a = new assoc_recherche_mots("omm_video",$mots,$type,$champs);
		$valid = $a->get_valid_search();
		// On test si on effectue une recherche
		if (!$valid["valid"]){
			echo $valid["erreur"];
		}else{
			if ($valid["recherche"]=="seul") $tab = $a->recherche_seul();
			if ($valid["recherche"]=="tous") $tab = $a->recherche_tous();
		}
		
		// s'il y a des resultats on les affiches
		if (count($tab)==0){
			echo "<br><br>Il n'y a pas de résultats à votre recherche.";
			return;
		}else{
			include_spip("inc/assoc_rendu_recherche");
			echo rendu_recherche_mot_video_exec($tab);
		}
		
	}
	
	public function exec_find(){
		// recherche sur les videos par date
		if ($_POST["type"]=="date"){
			$tab = array ( "debut" => $_POST["debut"] ,"fin" => $_POST["fin"] ,"page" => 0 ,"num" => 500);
			$xml = new SimpleXMLElement(recuperer_fond("fonds/assoc_omm_actuphonore",$tab));
			$taille = count($xml->omm);
			if ($taille==0){
				echo "Aucun résultat pour votre recherche de doc phonore";
				return;
			}
			
			$retour .= "<table><tr><td width='100'>Date</td><td width='180'>Titre</td><td width='10'></td><td width='40'></td></tr>";
			for($i=0; $i <$taille;$i++){
				$id = $xml->omm[$i]->id;
				$retour .= "<tr id='tr$id'>";
				$retour .= "<td>".$xml->omm[$i]->date."</td>";
				$retour .= "<td class='titre' value='$id'>".$xml->omm[$i]->titre."</td>";
				$retour .= "<td><span class='masquer' id='la_video$id'><span></td>";
				$retour .= "<td><a href='?exec=edit_video&id_video=$id'>Editer</a></td>";
				$retour .= "</tr>";
			}
			echo $retour."</table>";
			
		}
	}
	
	public function style_find(){
	}
	
	public function widgets(){
		$tab["assoc_panel_bt_haut"] = bt_haut_classique::get_widget();
		$tab["assoc_panel_onglet"] = onglet_omm::get_widget();
		$tab["assoc_panel_recherche"] = recherche_date::get_widget();
		$tab["assoc_panel_assoc"] = association_video::get_widget();
		$tab["assoc_panel_bt_bas"] = selection_actuphonore_home_inserer::get_widget();
		return $tab;
	}
	
}



?>