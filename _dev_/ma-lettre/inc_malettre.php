<?php
//
// ajout bouton 
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_MALETTRE',(_DIR_PLUGINS.end($p)));
 
function malettre_ajouterBoutons($boutons_admin) {
		// si on est admin (deactive)		
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) { // admin full
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['naviguer']->sousmenu['malettre']= new Bouton(
			"../"._DIR_PLUGIN_MALETTRE."/img/icon_malettre.png",  // icone
			_T("malettre:ma_lettre")	// titre
			);
		}
		return $boutons_admin;
}

//
// functions
function malettre_get_contents($patron,$id_article=0,$lang='fr') {
  // inspi: spip-listes: exec/import_patron.php (merci booz)  
  $date = date('Y-m-d');
  
	$contexte_patron = array('date' => $date,'patron'=>$patron,'lang'=>$lang, 'id_article'=>$id_article);
	//$texte_patron =  recuperer_fond($patron, $contexte_patron); 	
  // on utilise recupere_page et pas recupere fond pour eviter d'avoir des adresses privees (redirect)   
  $url = generer_url_public("$patron",'',true);
	foreach ($contexte_patron as $k=>$v)
			$url = parametre_url($url,$k,$v,'&');
	$texte_patron = recuperer_page($url) ;
	
	// passer tout ca en unicode pour eviter certains problemes
	//include_spip('inc/charsets');
	//$texte_patron = charset2unicode($texte_patron);
	
  return $texte_patron;	
  			          	
}


?>