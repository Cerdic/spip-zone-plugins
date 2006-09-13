<?php

// FIXME integrer table_prefix 

//---------------------------------------
// Parametres
//---------------------------------------
define("DEBUG_S2S",false);        // mode debug ?
define("STATUT_DEFAUT","prop");   // statut des articles importés: prop(proposé),publie(publié)
define("PREVENIR_EMAIL",true);    // prevenir par email à chaque nouvelle syndication ?
// si oui, sur quel email envoie le report ?
define("EMAIL_S2S", $GLOBALS['meta']['adresse_suivi'] ); // par defaut, adresse de suivi editorial, possible de forcer un email "machin@foo.org" 


//---------------------------------------
// Fonctions
//---------------------------------------
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SPIP2SPIP',(_DIR_PLUGINS.end($p)));

 // ajout bouton 
function spip2spip_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['naviguer']->sousmenu['spip2spip']= new Bouton(
			"../"._DIR_PLUGIN_SPIP2SPIP."/img_pack/icon.png",  // icone
			_T("spiptospip:titre")	// titre
			);
		}
		return $boutons_admin;
}

//
// verifie s'il s'agit du bon format de backend
// a terme peut etre utile pour recuperer le numero de version
function is_spip2spip_backend($str) {  
	// Chercher un numero de version
	if (ereg('(spip2spip)[[:space:]](([^>]*[[:space:]])*)version[[:space:]]*=[[:space:]]*[\'"]([-_a-zA-Z0-9\.]+)[\'"]', $str, $regs)) 
	     return true;
    	 return false;
}

// 
// parse le backend xml spip2spip 
// basée sur la fonction originale: analyser_backend()
function analyser_backend_spip2spip($rss){  
  include_ecrire("inc_texte.php"); # pour couper()
	include_ecrire("inc_filtres.php"); # pour filtrer_entites()
		
	$xml_tags = array('surtitre','titre','soustitre','descriptif','chapo','texte','ps','auteur','link','lang','keyword','licence','documents'); 
	
	$syndic_regexp = array(
				'item'           => ',<item[>[:space:]],i',
				'itemfin'        => '</item>',
				
				'surtitre'       => ',<surtitre[^>]*>(.*?)</surtitre[^>]*>,ims',
				'titre'          => ',<titre[^>]*>(.*?)</titre[^>]*>,ims',
				'soustitre'      => ',<soustitre[^>]*>(.*?)</soustitre[^>]*>,ims',
				'descriptif'     => ',<descriptif[^>]*>(.*?)</descriptif[^>]*>,ims',				
				'chapo'          => ',<chapo[^>]*>(.*?)</chapo[^>]*>,ims',				
				'texte'          => ',<texte[^>]*>(.*?)</texte[^>]*>,ims',				
				'ps'             => ',<ps[^>]*>(.*?)</ps[^>]*>,ims',
				'auteur'         => ',<auteur[^>]*>(.*?)</auteur[^>]*>,ims',
				'link'           => ',<link[^>]*>(.*?)</link[^>]*>,ims',
        'lang'           => ',<lang[^>]*>(.*?)</lang[^>]*>,ims',
        'keyword'        => ',<keyword[^>]*>(.*?)</keyword[^>]*>,ims',
        'licence'        => ',<licence[^>]*>(.*?)</licence[^>]*>,ims',
        'documents'       => ',<documents[^>]*>(.*?)</documents[^>]*>,ims',
	);
	
	$xml_doc_tags = array('id','url','titre','desc');
	
	$document_regexp = array(		
  			'document'       => ',<document[>[:space:]],i',
				'documentfin'    => '</document>',
        	
				'id'             => ',<id[^>]*>(.*?)</id[^>]*>,ims',
        'url'            => ',<url[^>]*>(.*?)</url[^>]*>,ims',
				'titre'          => ',<titre[^>]*>(.*?)</titre[^>]*>,ims',
				'desc'           => ',<desc[^>]*>(.*?)</desc[^>]*>,ims',
	);
	
	// fichier backend correct ?
	if (!is_spip2spip_backend($rss)) return _T('avis_echec_syndication_01');
	
	// Echapper les CDATA
	$echappe_cdata = array();
	if (preg_match_all(',<!\[CDATA\[(.*)]]>,Uims', $rss,
	$regs, PREG_SET_ORDER)) {
		foreach ($regs as $n => $reg) {
			$echappe_cdata[$n] = $reg[1];
			$rss = str_replace($reg[0], "@@@SPIP_CDATA$n@@@", $rss);
		}
	}
	
	$items = array();
	if (preg_match_all($syndic_regexp['item'],$rss,$r, PREG_SET_ORDER))
	foreach ($r as $regs) {
		$debut_item = strpos($rss,$regs[0]);
		$fin_item = strpos($rss,
			$syndic_regexp['itemfin'])+strlen($syndic_regexp['itemfin']);
		$items[] = substr($rss,$debut_item,$fin_item-$debut_item);
		$debut_texte = substr($rss, "0", $debut_item);
		$fin_texte = substr($rss, $fin_item, strlen($rss));
		$rss = $debut_texte.$fin_texte;
	}

	// Analyser chaque <item>...</item> du backend et le transformer en tableau
	if (!count($items)) return _T('avis_echec_syndication_01');
	
	foreach ($items as $item) {
	 
		$data = array();
    
		// Date
		$la_date = "";
		if (preg_match(",<date>([^<]*)</date>,Uims",$item,$match))		$la_date = $match[1];
		if ($la_date)  		$la_date = my_strtotime($la_date);
		if ($la_date < time() - 365 * 24 * 3600	OR $la_date > time() + 48 * 3600)		$la_date = time();
		$data['date'] = $la_date;
			
		// Recuperer les autres tags du xml
		foreach ($xml_tags as $xml_tag) {		  
		  if (preg_match($syndic_regexp[$xml_tag],$item,$match)) $data[$xml_tag] = $match[1];
  				                                              else $data[$xml_tag] = "";
    }	
    
    // On parse le noeud documents
    if ($data['documents'] != "") {         
          
          $documents = array();
          if (preg_match_all($document_regexp['document'],$data['documents'],$r2, PREG_SET_ORDER))
          	foreach ($r2 as $regs) {
          		$debut_item = strpos($data['documents'],$regs[0]);
          		$fin_item = strpos($data['documents'],
          			$document_regexp['documentfin'])+strlen($document_regexp['documentfin']);
          		$documents[] = substr($data['documents'],$debut_item,$fin_item-$debut_item);
          		$debut_texte = substr($data['documents'], "0", $debut_item);
          		$fin_texte = substr($data['documents'], $fin_item, strlen($data['documents']));
          		$data['documents'] = $debut_texte.$fin_texte;
          }
          
          if (count($documents)) {          
              foreach ($documents as $document) {                 
                 $data_node = array();
                 foreach ($xml_doc_tags as $xml_doc_tag) {
                    if (preg_match($document_regexp[$xml_doc_tag],$document,$match)) $data_node[$xml_doc_tag] = $match[1]; 
  				                                                                      else $data_node[$xml_doc_tag] = "";
  				       } 
                $portfolio[] = $data_node;                                                     
              }             
              $data['documents'] =  serialize($portfolio);
          }       
    }			                                                  
	
		// Nettoyer les donnees et remettre les CDATA en place
		foreach ($data as $var => $val) {
			$data[$var] = filtrer_entites($data[$var]);
			foreach ($echappe_cdata as $n => $e)
				$data[$var] = str_replace("@@@SPIP_CDATA$n@@@",$e, $data[$var]);
			$data[$var] = trim(textebrut($data[$var]));
		}

		//$data['item'] = $item;  //utile pour spip2spip ?		
		$articles[] = $data;    
	}
 
  return $articles;
}

//
// recuperer rubrique (normalement uniquement) lié à un mot
function get_id_rubrique($mot) { 
    $id_group_spip2spip = get_id_groupemot("- spip2spip -");
    $sql = "SELECT id_mot FROM spip_mots WHERE titre='".addslashes($mot)."' AND id_groupe='$id_group_spip2spip'"; // extra plus large utiliser  LIKE ?   
    $result = spip_query($sql);    
    while ($row = spip_fetch_array($result)) {
        $id_mot = (int) $row['id_mot'];
        $sql2 = "SELECT id_rubrique FROM spip_mots_rubriques WHERE id_mot='$id_mot' LIMIT 1";       
        $result2 = spip_query($sql2);
        while ($row2 = spip_fetch_array($result2)) {
          return $row2['id_rubrique'];
        }
        return false;
    }
    return false;
}

//
// recupère id d'un groupe de mots-clés
function get_id_groupemot($titre) {
    $sql = "SELECT id_groupe FROM spip_groupes_mots WHERE titre='".addslashes($titre)."'"; 
    $result = spip_query($sql);
    while ($row = spip_fetch_array($result)) {
       return $row['id_groupe'];
    }
    return false;  
}

//
// recupère id d'un mot
function get_id_mot($titre) {
    $sql = "SELECT id_mot FROM spip_mots WHERE titre='".addslashes($titre)."'"; 
    $result = spip_query($sql);
    while ($row = spip_fetch_array($result)) {
       return $row['id_mot'];
    }
    return false;  
}

//
// recupere id d'un auteur selon son nom ou le creer
function get_id_auteur($name) {
    if (trim($name)=="") return false;    
    $sql = "SELECT id_auteur FROM spip_auteurs WHERE nom='".addslashes($name)."'";
    $result = spip_query($sql);
    while ($row = spip_fetch_array($result)) {
       return $row['id_auteur'];
    }
    // auteur inconnu, on le cree ...
    $sql = "INSERT INTO spip_auteurs (nom, statut) VALUES (\"$name\", '1comite')";
    $result = spip_query($sql);
    return spip_insert_id();
}

//
// restaure le formatage des extra
function convert_extra($texte,$documents) {
	$texte = convert_ln($texte); 
	$texte = convert_img($texte,$documents);
	return $texte;
}

//
// restaure le formatage des img et doc avec le tableau fourni
function convert_img($texte,$documents) {
  $original = $texte;
	foreach($documents as $k=>$val) {	      
	   $texte = preg_replace("/__IMG$k(.*?)__/i", "<img$val$1>",$texte);
	   $texte = preg_replace("/__DOC$k(.*?)__/i", "<doc$val$1>",$texte);
     // changement ? (PHP<5, pas de parametre count)
    if ($original != $texte) passe_document_mode_vignette($val);  	   
  }	
  
  //$texte = preg_replace("/__(IMG|DOC)(.*?)__/i", "",$texte); // nettoyage des codes qui resteraient eventuellement
  $texte = preg_replace("/__(.*?)__/i", "",$texte); // expression plus large (pour prevoir la compatabilite future si ajout d'autres extras)
	return $texte;
}

//
// restaure le formatage des ln
function convert_ln($texte) {
	$texte = str_replace("__LN__","\n\n",$texte); 
	return $texte;
}

//
// passe un document en mode vignette (ou autre)
function passe_document_mode_vignette($id_document,$mode="vignette") {
   global $table_prefix;
   
   $sql="UPDATE ".$table_prefix."_documents SET mode = '$mode' WHERE id_document='$id_document' LIMIT 1";                        
   spip_query($sql);
} 

//
// layout invert la navigation
function insert_shortcut() {
  /* debug only
  debut_raccourcis();
  //icone_horizontale(....);  
  echo "<ul>\n";
  echo "<li><a href='spip2spip.php'>home</a><br />&nbsp;</li>\n";
  echo "<li><a href='spip2spip-install.php'>install</a></li>\n";
  echo "<li><a href='spip2spip-cron.php'>complete syndication</a></li>\n";
  echo "<li><a href='../spip2spip-cron.php'>cron</a></li>\n";
  echo "</ul>\n";
  fin_raccourcis();
  */
}

?>
