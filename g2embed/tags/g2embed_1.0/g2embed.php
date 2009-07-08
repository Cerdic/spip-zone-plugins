<?php
// PLUGIN G2EMBED : Gallery2/Spip integration
// Author : Philippe GRISON - UPS 2259 - CNRS
// last modified : 8 July 2009
// Licence: GPL
// ---------------------------------------------------------------------------------------- //
// DECLARATION DES FONCTIONS 
// ---------------------------------------------------------------------------------------- //

// OUVRE UNE SESSION DE GALLERY ---------------------------------------------------------- //
function call_gallery2_embed() {
// This code assumes that Gallery2 is installed in a directory at the root of the website
// Include g2 path : $my_g2Uri, $my_embedUri
include(dirname(__FILE__) . '/g2embed_options.php');
require_once(dirname(__FILE__) . $my_g2embed);
// global parameter
global $g2data;

if ($GLOBALS['auteur_session']) { // test if author is a SPIP member 

	if (!headers_sent()) { header('Content-Type: text/html; charset=UTF-8');}
	
	// Initialisation de la langue de l'interface 
	$lang  =$GLOBALS['auteur_session']['lang'] ;
	if( $lang =='' ){$lang = 'en';}
	
	// initiate G2 
	$ret = GalleryEmbed::init(array('g2Uri' => $my_g2Uri, 'embedUri' =>  $my_embedUri, 'activeUserId' => $GLOBALS['auteur_session']['id_auteur']));	
	
	$info="";			
	if ($ret) {
		 /* Error! */
		 /* Did we get an error because the user doesn't exist in g2 yet? */	 
		$ret2 =GalleryEmbed::isExternalIdMapped($GLOBALS['auteur_session']['id_auteur'],'GalleryUser');   
		$info="creation d'un utilisateur dans Gallery2";	
		 if ($ret2 && $ret2->getErrorCode() & ERROR_MISSING_OBJECT) {
			 /* The user does not exist in G2 yet. Create in now on-the-fly */		 
				$extUserId = $GLOBALS['auteur_session']['id_auteur'];
				$args = array('username' => $GLOBALS['auteur_session']['nom']);
				$ret = GalleryEmbed::createUser($extUserId, $args) ;															
			 if ($ret) {
			 /* An error during user creation. Not good, print an error or do whatever is appropriate * in your emApp when an error occurs */
				 print "An error occurred during the on-the-fly user creation <br>";
				 print $ret->getAsHtml();
				 exit;
			 }
		 } else {
			 /* The error we got wasn't due to a missing user, it was a real error */
			 if ($ret2) {
				 print "An error occurred while checking if a user already exists<br>";
				 print $ret2->getAsHtml();
			 }
			 print "An error occurred while trying to initialize G2<br>";
			 print $ret->getAsHtml();
			 exit;
		 }
	 }
	 /* change the cookie path */ 
	 $ret = GalleryCoreApi::setPluginParameter('module', 'core', 'cookie.path', '/');
		 if ($ret) {
			 print $ret->getAsHtml();
			 exit;
		 }    
	 /* At this point we know that either the user either existed already before or that it was just created - proceed with the normal request to G2 */	 
	 $g2data = GalleryEmbed::handleRequest();	 	 
	}
}

// APPEL DE GALLERY EN MODE GUEST
function call_gallery2_embed_guest() {
// This code assumes that Gallery2 is installed in a directory at the root of the website
// Include g2 path : $my_g2Uri, $my_embedUri
	include(dirname(__FILE__) . '/g2embed_options.php');
	require(dirname(__FILE__) . $my_g2embed);
	// global parameter
	global $g2data;
		
	if (!headers_sent()) { header('Content-Type: text/html; charset=UTF-8');}	
	
	// initiate G2 	
	$ret = GalleryEmbed::init(array('g2Uri' => $my_g2Uri, 'embedUri' =>  $my_embedUri_guest, 'activeUserId' => ''));	
	 if ($ret) {
     /* Error! */
         print "An error occurred while trying to initialize G2<br>";
         print $ret->getAsHtml();
         exit;
 	}
	
	// AJUSTE L INTERFACE
	/*	
	// modification du theme : NE MARCHE PAS ?!
	$ret = GalleryEmbed::setThemeForRequest('siriux');
	handleStatus($ret);
	*/	
	
	// supprime la zone a droite 
	GalleryCapabilities::set('showSidebarBlocks', false);

	$g2data = GalleryEmbed::handleRequest();	
	// si l'initialisation s'est bien passé
	return true ;
}

// CLOSE THE SESSION OF GALLERY ----------------------------------------------------------- //
function exit_gallery2_embed() { 
	include_once(dirname(__FILE__) . '/g2embed_options.php');
	require_once(dirname(__FILE__) . $my_g2embed);
	$ret = GalleryEmbed::logout(array( 'embedUri' => $my_embedUri)); 
}

// INITIALISATION  DE GALLERY EN FULLINIT ----------------------------------------- //
function init_gallery2_embed() {
	include(dirname(__FILE__) . '/g2embed_options.php');					  				
	require_once(dirname(__FILE__) . $my_g2embed);		
	if (!headers_sent()) { header('Content-Type: text/html; charset=UTF-8');}
	$ret = GalleryEmbed::init(array('fullInit' => True, 'embedUri' => $my_embedUri, 'g2Uri' => $my_g2Uri));
	if ($ret) {return 'GalleryEmbed::init failed, error message: ' . $ret->getAsHtml();}
}

// CLOSE  GALLERY  FULLINIT (REQUIRED) ---------------------------------------------------- //
function close_gallery2_embed() { 
	include_once(dirname(__FILE__) . '/g2embed_options.php');
	require_once(dirname(__FILE__) . $my_g2embed);
	$ret = GalleryEmbed::done(); 
}


// ---------------------------------------------------------------------------------------- //
//  PIPELINES Declarations
// ---------------------------------------------------------------------------------------- //

$started=False;

function g2_call($flux){
	global $g2data;
	$type_page='gallery2'; 
	if ($GLOBALS['auteur_session']) { // test if author is a SPIP member  
		call_gallery2_embed(); // call the API gallery2
	}
	return $flux;
}

function g2_head($flux) {
	global $g2data;
	if (!$g2data['isDone']) { $flux.= $g2data['headHtml'];}
	// ajoute l'appel a la  feuille de style g2embed.css
	$self_ref = dirname($_SERVER['PHP_SELF']);
	$flux .= '<link rel="stylesheet" type="text/css" href="'.$self_ref.'/plugins/gallery2/g2embed.css" />';
	return $flux;
}

function g2_body($flux) {
	global $g2data;
	if (!$g2data['isDone']) {  // to display G2 BODY content inside embedding application :
	  $flux .=  $g2data['bodyHtml'];    
	}
	return $flux;
}


function g2_exit($flux){
	$type_page='gallery2'; 
	exit_gallery2_embed(); // exit the API gallery2
	return $flux;
}

// APPEL de GALLERY en FULL INIT
function g2_init($flux){
	init_gallery2_embed(); //  initialisation de  gallery2
	return $flux;
}

function g2_close($flux){
	close_gallery2_embed(); // ferme la session de Gallery ouverte par g2_init
	return $flux;
}

// APPEL de GALLERY en MODE GUEST
function g2_call_guest($flux){
	call_gallery2_embed_guest(); //  initialisation de  gallery2
	return $flux;
}

?>
