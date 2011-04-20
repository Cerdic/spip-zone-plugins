<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite
	include_spip('base/abstract_sql');


// Balise independante du contexte ici
function balise_BANNIERE ($p) {
	return calculer_balise_dynamique($p, 'BANNIERE', array());
}
 
// Balise de traitement des données
function balise_BANNIERE_dyn($position='1',$contexte='',$pays='') {

/*
 * geolocalisation limitée à la France -> voir plugin geographie
 * ToDO : tester le numero du pays
 * $contexte doit etre au format code postal France
 */

if(!is_numeric($contexte)) {
	$contexte='';
}
if (defined('_DIR_PLUGIN_GEOGRAPHIE')) $geo = 'oui';

if ($geo=='oui' and $pays=='70' and $contexte !=''){

	// On est en 'France'

	// On cherche d'abord si il y a une reponse avec le code postal
	$rayon ='local';
	$diffusion = $contexte;

	$data = chercher_banniere($position , $rayon , $diffusion);

	//	Si pas de resultats avec le code postal, on cherche ailleurs
	if ($data ==''){
		
		// On recupere le numero du departement à partir du début du code postal
		$departement = substr($contexte,0,2);
		
		// Cas des DOM TOM
		if($departement == '97'){
		$departement = substr($contexte,0,3);
		}
		
		//on recupere l'id_departement et l'id_region à partir de la table spip_geo_departements
		$champs_geo = array('id_departement', 'id_region');
		$where_geo = 'abbr='.sql_quote($departement);
		$ids=sql_fetsel($champs_geo, "spip_geo_departements", $where_geo,'','', $limit = '1');
			
		// Le departement n'a pas été trouvé
		if ($ids == '') {
		
		// Que fais-ton si on n'a pas de résultats ?
		
		} else {
		
			// On cherche si il y a quelque chose à afficher avec ce departement
			$rayon = 'departement';
			$diffusion = $ids['id_departement'];
			
			$data = chercher_banniere($position , $rayon , $diffusion);
			
			// Si rien dans le departement on s'attaque à la region
			if ($data ==''){
			
				$rayon ='region';
				$diffusion = $ids['id_region'];
	
				$data = chercher_banniere($position , $rayon , $diffusion);
				
				// Si rien dans la region, alors on cherche au niveau national	
				if ($data ==''){
					$rayon = 'pays';
					$diffusion = $pays;
				
					$data = chercher_banniere($position , $rayon , $diffusion);
					
						// rien de configuré alons on affiche la banniere par defaut
						if ($data ==''){
							$rayon = 'int';
							$diffusion = '';
							
							$data = chercher_banniere($position , $rayon , $diffusion);
							
							}
				
				}
			}
		}
	}
} else {


	// On est PAS en 'France' ou on n'a pas d'autre infos
	// On affiche alors une bannière générique ou 'internationale'
	$rayon ='int';
	$diffusion = '';
	
	$data = chercher_banniere($position , $rayon , $diffusion);
	
}

if ($data) {
	// on recupere les données trouvées pour afficher la bonne bannière
	$id_objet		= 'id_banniere';
	$id				= $data['id_banniere'];
	$alt			= $data['alt'];
	
	return afficher_banniere($id_objet , $id , $alt);

} else {
	return;
}

}
	
// chercher la bonne banniere
function chercher_banniere($position='', $rayon ='', $diffusion ='') {

	// Champs à récupérer
	// $champs = array('id_banniere', 'alt');
	
	
	// dans le cas du local il peut y avoir plusieurs codes postaux séparés par des virgules
	// dans les autres cas il n'y a qu'une valeur
	if ($rayon =='local'){
		$ou = 'diffusion LIKE "%'.$diffusion.'%"';
	} else {
		$ou = 'diffusion ='.sql_quote($diffusion);
	}
	
	// construction des conditions de la requette
	$where = array( 
		'debut<=CURRENT_DATE()',
		'fin>=CURRENT_DATE()',
		'rayon='.sql_quote($rayon),
		'position='.sql_quote($position),
		$ou,
	);
	
	// On récupère les données dans la base 
	// $data=sql_fetsel($champs, "spip_bannieres", $where,'','RAND()', $limit = '1');
	$data=sql_fetsel("*", "spip_bannieres", $where,'','RAND()', $limit = '1');

return $data;

}


//
// afficher le document associe a la banniere
//
function afficher_banniere($id_objet='',$id='',$alt='') {

	// Chercher dans la base le document associe à la banniere
	$type = "banniere"; 
	$select = "D.id_document, D.extension, D.titre,  D.descriptif,  D.fichier, D.largeur, D.hauteur";
	$from = "spip_documents AS D LEFT JOIN spip_documents_liens AS L ON  L.id_document=D.id_document"; 
	$where = "L.id_objet=$id AND L.objet='$type' AND D.extension $img IN ('gif', 'jpg', 'png', 'swf')";
	$order = "0+D.titre, D.date";

	$document = sql_fetsel($select, $from, $where, '','RAND()', $limit = '1');
	$url = sql_getfetsel ('site', 'spip_bannieres', 'id_banniere='.$id);

  if ($document){  
   
    
  	 
  	// cas du flash: 
    // on passe les donnees ds FlashVars
    // que le flash peut recuperer ou non  	  	 
  	if ($document['extension'] == 'swf'){  
  	    $url_site = rawurlencode($GLOBALS['meta']['adresse_site']);
        $logo_banniere =
            "<object type='application/x-shockwave-flash' data='"._DIR_IMG.$document['fichier']."' id='bandeau' width='".$document['largeur']."' height='".$document['hauteur']."'>
              <param name='movie' value='"._DIR_IMG.$document['fichier']."' />
              <param name='quality' value='high' />
              <param name='menu' value='false' />          
              <param name='wmode' value='transparent' />
              <param name='FlashVars' value='url_site=$url_site&amp;action=visit_url&amp;banniere=$id&amp;url=".rawurlencode($url)."' />
            </object>";    
  
  	} else {   
  	   //Todo : s'assurer que c'est une image
      $logo_banniere = '<img src="'._DIR_IMG.$document['fichier'].'" alt="'.$alt.'" width="'.$document['largeur'].'" height="'.$document['hauteur'].'" border="0" />';
  
  	} 
      
  } else {    
     // rien dans la base peut etre un logo ? - ancien systeme.   
  	include_spip('inc/iconifier');
  	
  	$chercher_logo = charger_fonction('chercher_logo', 'inc');
  	$logo = $chercher_logo($id, $id_objet, 'on');
  	
  	list($img, $clic) = decrire_logo($id_objet,'on',$id, 170, 170, $logo, $texteon, $script, $flag_modif AND !$logo_s);
  
  	// si on a trouve on l'affiche
  	$logo_banniere = '<img src="'.$logo['0'].'" alt="'.$document['titre'].'" width="'.$document['largeur'].'" height="'.$document['hauteur'].'" border="0" />';
  }

	// rechercher l'url de destination
	if($url && $document['extension'] != 'swf') {
		$lien = '<a href="'.generer_url_action('visit_url','banniere='.$id.'&url='.rawurlencode($url)).'" title="'.$document['titre'].'" class="banniere">';
		$lien .= $logo_banniere.$document['descriptif'].'</a>';
	} else {
		$lien = $logo_banniere;
	}

  return $lien;

}
?>