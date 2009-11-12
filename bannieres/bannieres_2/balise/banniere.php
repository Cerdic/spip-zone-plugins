<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


// Balise independante du contexte ici
function balise_BANNIERE ($p) {
	return calculer_balise_dynamique($p, 'BANNIERE', array());
}
 
// Balise de traitement des donn�es
function balise_BANNIERE_dyn($position='1',$contexte='',$pays='') {

/*
 * geolocalisation limit�e � la France (id_pays = 70 )-> voir plugin geographie
 * ToDO : tester le numero du pays
 * $contexte doit etre un nombre au format code postal France
 */

if(!is_numeric($contexte)) {
	$contexte='';
}

if ($pays=='70' and $contexte !=''){

	// On est en 'France'

	// On cherche d'abord si il y a une reponse avec le code postal
	$rayon ='local';
	$diffusion = $contexte;

	$data = chercher_banniere($position , $rayon , $diffusion);

	//	Si pas de resultats avec le code postal, on cherche ailleurs
	if ($data ==''){
		
		// On recupere le numero du departement � partir du d�but du code postal
		$departement = substr($contexte,0,2);
		
		// Cas des DOM TOM
		if($departement == '97'){
		$departement = substr($contexte,0,3);
		}
		
		//on recupere l'id_departement et l'id_region � partir de la table spip_geo_departements
		$champs_geo = array('id_departement', 'id_region');
		$where_geo = 'abbr='.sql_quote($departement);
		$ids=sql_fetsel($champs_geo, "spip_geo_departements", $where_geo,'','', $limit = '1');
			
		// Le departement n'a pas �t� trouv�
		if ($ids == '') {
		
		// Que fais-ton si on n'a pas de r�sultats ?
		
		} else {
		
			// On cherche si il y a quelque chose � afficher avec ce departement
			$rayon = 'departement';
			$diffusion = $ids['id_departement'];
			
			$data = chercher_banniere($position , $rayon , $diffusion);
			
			// Si rien dans le departement on s'attaque � la region
			if ($data ==''){
			
				$rayon ='region';
				$diffusion = $ids['id_region'];
	
				$data = chercher_banniere($position , $rayon , $diffusion);
				
				// Si rien dans la region, alors on cherche au niveau national	
				if ($data ==''){
					$rayon = 'pays';
					$diffusion = $pays;
				
					$data = chercher_banniere($position , $rayon , $diffusion);
					
						// rien de configur� alons on affiche la banniere par defaut
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
	// On affiche alors une banni�re g�n�rique ou 'internationale'
	$rayon ='int';
	$diffusion = '';
	
	$data = chercher_banniere($position , $rayon , $diffusion);
	
}


// on recupere les donn�es trouv�es pour afficher la bonne banni�re
$id_objet		= 'id_banniere';
$id				= $data['id_banniere'];
$alt			= $data['alt'];

return afficher_banniere($id_objet , $id , $alt);
}
	
// chercher la bonne banniere
function chercher_banniere($position='', $rayon ='', $diffusion ='') {

	// Champs � r�cup�rer
	$champs = array('id_banniere', 'alt');
	
	
	// dans le cas du local il peut y avoir plusieurs codes postaux s�par�s par des virgules
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
	
	// On r�cup�re les donn�es dans la base 
	$data=sql_fetsel($champs, "spip_bannieres", $where,'','RAND()', $limit = '1');

return $data;

}


// afficher la banniere trouv�e
function afficher_banniere($id_objet='',$id='',$alt='') {

	// recherche du logo
	include_spip('inc/iconifier');
	
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$logo = $chercher_logo($id, $id_objet, 'on');
	
	list($img, $clic) = decrire_logo($id_objet,'on',$id, 170, 170, $logo, $texteon, $script, $flag_modif AND !$logo_s);

	// afficher l'image
	$logo_banniere = '<a href="'.generer_url_action('visit_url','banniere='.$id).'" >
		<img src="'.$logo['0'].'" alt="'.$alt.'">
		</a>
		';

return $logo_banniere;

}
?>