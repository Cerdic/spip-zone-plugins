<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Crayon pour un composant - Sauvegarde
 * Crayon for one component - Store changes
 */
function action_crayons_composant_store_dist() {
  include_spip('inc/crayons');

	lang_select($GLOBALS['auteur_session']['lang']);
	header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);
	$wid = $_POST['crayons'][0];
	$c = 'composants/'.$_POST['composant'].'/'.$_POST['composant'];
	
	// MàJ du composant - Update component
	// l'instanciation d'un objet composant met à jour le composant
	include_spip('lib/composant/classComposantPrive');
	$cprovi = new AdminComposant($_POST['composant']);
	
	// Retourne la vue - Return vue 
  $return[$wid] = vues_dist('composant', $_POST['composant'], 1, array('var_mode'=>'recalcul', 'c'=>$c));
	echo var2js($return);
	exit;
}

function vues_dist($type, $modele, $id, $content){

	// pour ce qui a une {lang_select} par defaut dans la boucle,
	// la regler histoire d'avoir la bonne typo dans le propre()
	// NB: ceci n'a d'impact que sur le "par defaut" en bas
	if (colonne_table($type, 'lang')) {
		lang_select($a = valeur_colonne_table($type, 'lang', $id));
	} else {
		lang_select($a = $GLOBALS['meta']['langue_site']);
	}

  // chercher vues/article_toto.html
  // sinon vues/toto.html
  if (find_in_path(($fond = 'vues/composant').'.html')) {
		$contexte = array(
		    'id_' . $type => $id,
		    'champ' => $modele,
		    'lang' => $GLOBALS['spip_lang']
		);
		$contexte = array_merge($contexte, $content);
		include_spip('public/assembler');
		return recuperer_fond($fond, $contexte);
  }
	// vue par defaut
	else {
    return 'err 404';
	}
}
?>
