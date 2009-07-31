<?php

// suppression d'une association
function delete_association(){
	$cle = $_POST['cle'];
	$id = $_POST['id'];
	spip_query("DELETE FROM `association` WHERE `cle` = $cle and `id`=$id");
}


/*
 * Modele pour les diferents type de panneaux
 *  
 */
abstract class assoc_panel{

	private $nom;
	private $les_widgets;	
	private $css_jss;
	private $autre_dir="";
	private $autre_js="";
	private $autre_css="";

	
	/* 
	 * Le constructeur est chargé de construire le html  
	 * plus exactement il  recupere les 'widget'
	 * chargé de donnee des possibilite de recherche par exemple
	 */
	function __construct($nom="",$dir="",$js="",$css=""){
		$this->nom  = $nom;
		$this->autre_dir  = $dir;
		$this->autre_js  = $js;
		$this->autre_css  = $css;
	}
	
	function get_panel(){
		// On recupere les widgets
		$this->les_widgets  = $this->widgets();
		// On recupere la/les css et le(s) js
		$this->css_jss =  $this->css_js();
		// On construit le panel
		return  $this->do_panel();
	}
	
	
	// on va recuperer la css en chargeant le dossier
	// css approprie en l'englobant dans des balises styles
	function css_js(){
		
		$nom = $this->nom; 
		$rep = 'assoc';
		if ($this->autre_dir !="") $rep = $this->autre_dir;
		
		
		$fichier = dirname (__FILE__)."/../../../plugins/assoc/css/assoc.css";
		$open = fopen($fichier, "r");
		$retour = fread($open, filesize($fichier) );
		$css =  "<style id='style_assoc_panel'>".$retour."</style>";
		fclose($open);
		
		/* On test si on demande un autre js */
		$file = $nom;
		if ($this->autre_css !="") $file = $this->autre_css;
		$fichier = dirname (__FILE__)."/../../../plugins/$rep/css/$file.css";
		if(file_exists($fichier)){
			$open = fopen($fichier, "r");
			$retour .= fread($open, filesize($fichier) );
			fclose($open);
			$css .=  "<style id='style_assoc_panel'>".$retour."</style>";
		}
		
		/* On test si on demande un autre js */
		$file = $nom;
		if ($this->autre_js !="") $file = $this->autre_js;
		$fichier = dirname (__FILE__)."/../../../plugins/$rep/js/$file.js";
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
		// on recupêre les style globaux
		$css_js = $this->css_jss;
		
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
		$haut_recherche = $haut-16-$haut_insert;
		
		// les resultats
		$large_resultat = 100 - $large_recherche;
		$haut_resultat = $haut - 16 - $haut_insert;
		
		$style = '
			<style type="text/css">
					#panel_conteneur {width : '.$large.'px ; height : '.$haut.'px }
					#panel_recherche { width : '.$large_recherche.'% ; height : '.$haut_recherche.'px}
					#panel_resultat {   width : '.$large_resultat.'% ; height : '.$haut_resultat.'px ; }		
					#panel_assoc {  height : '.$haut_insert.'px ;  }
			
			</style>
		';
	
		
		$retour ="
				<div id='panel_conteneur'>
					<div id='panel_relative'>
						<div id='panel_haut' class='pb' onclick='class_assoc.close()'><span id='fermer_panel' />Fermer</div>
						<div id='panel_recherche' class='pb'>$recherche</div>
						<div id='panel_resultat' class='pb'></div>
						<div id='panel_assoc' class='pb'>$assoc</div>
					</div>
					$css_js
					$style
				</div>
				";

		return $retour;
		
	}
	
	function get_pagination(){
		return "<p id='pagination'>
					<span class='btn-nav' id='retour5' onclick='class_assoc.pagination(-5)'>  </span>
					<span class='btn-nav' id='retour1' onclick='class_assoc.pagination(-1)'></span>
					<span>page</span>
					<span class='btn-nav' id='avance1' onclick='class_assoc.pagination(1)'></span>
					<span class='btn-nav' id='avance5' onclick='class_assoc.pagination(5)'> </span>
				</p>";
	}
	
	// insertion d'une association
	function add(){
		
		include_spip("inc/filtres");
				
		// on securise certaine donne avant l'insertion en base
		$titre = "";
		$descriptif = "";
		if (isset($_POST['titre']))$titre = trim(corriger_caracteres($_POST['titre'])); 
		if (isset($_POST['descriptif']))$descriptif = trim(corriger_caracteres($_POST['descriptif'])); 	
		if (strlen($titre)>150)$titre ="";
		if (strlen($descriptif)>255)$descriptif ="";
		
	
		// on recupere les autres donnees
		$id = $_POST['id'];
		$id_lien = $_POST['id_lien'];
		$type_id = trim(corriger_caracteres($_POST['type_id']));
		$type_lien = trim(corriger_caracteres($_POST['type_lien']));
		if (strlen($type_id)>20)return;
		if (strlen($type_lien)>20)return;
		
		// on test que le lien n'existe pas deja
		// prevoir les tests ci lien deja existant
		$nb = sql_select("'cle'", "association", "id=$id and id_lien=$id_lien and type_id='$type_id' and type_lien='$type_lien'");
		if (sql_count($nb) >=1) return;
		
			
		// on envoi la requete
		//	id 	id_lien 	type_id 	type_lien 	titre 	descriptif 	type 
		$tab = array("id"=>$id,	"id_lien"=> $id_lien, "type_id"=> $type_id,	"type_lien"=>$type_lien, 
			"titre"=>$titre, "descriptif"=> $descriptif);

		sql_insertq("association",$tab);
		
	}
	
	
	// recherche 
	abstract function find();
	
	
	// On va passer nos sympathique widget
	abstract function widgets();
}


class panel_article extends  assoc_panel {
	
	public function find(){
		/* On va recuperer l'ensemble des parametres venant de la recherche */
		$debut = $_POST["debut"];
		$fin = $_POST["fin"];
		$page = $_POST["page"] * 10; 
		$rub = $_POST["rubrique"];

		
		$tab = array ("rubrique" => $rub , "debut" => $debut ,"fin" => $fin ,"page" => $page );
		$xml = new SimpleXMLElement(recuperer_fond("fonds/recherche/article",$tab));

		// on recupere le nbre d'article 
		// s'il n'y en a pas on affiche pas de resultat
		$taille = count($xml->article);
		
		if ($taille==0) return "Aucun résultat pour votre recherche d'article ";

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
			$retour .= "<td class='preselect' onclick='class_assoc.associer($id)'>associer</td>";
			$retour .= "</tr>";
		}
		echo $retour."</table>";
	}

	
	public function widgets(){
		$tab["panel_recherche"] = recuperer_fond("fonds/panel_recherche/panel_article");
		$tab["panel_assoc"] = "<fieldset>
									<legend>Associer</legend>
									<p id='titre_art'></p>
									<input type='button' value='Associer' onclick='class_assoc.associer()' /> 
								</fieldset>
							";
		// Pour la taille : 
		// largeur du panneau global en px
		// hauteur du panneau global en px
		// largeur de la zone de recherche en %
		// hauteur de la zone de insertion en px
		$tab["taille"] = '950,550,23,60';
		return $tab;
	}
	
}


class panel_rubrique extends  assoc_panel {
	
	public function find(){
	
	}

	public function widgets(){
		$tab["panel_recherche"] = recuperer_fond("fonds/panel_recherche/panel_rubrique");
		$tab["panel_assoc"] = "";
		$tab["taille"] = '400,300,100,0';
		return $tab;
	}
	
}


?>