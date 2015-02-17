<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Crayon pour un composant - Sauvegarde
 * Crayon for one component - Store changes
 */
function action_crayons_composant_store_dist() {
  include_spip('inc/crayons');

  acs_log('ACS: action/crayons_composant_store by '.$GLOBALS['auteur_session']['id_auteur']);
	lang_select($GLOBALS['auteur_session']['lang']);
	header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);
	
	// anti-injection de pagination après mise à jour du composant.
	if (isset($_GET['var_ajax'])) {
		$r = _T('avis_operation_impossible').'<br />'._T('admin_recalculer'); // Il faut recalculer la page !
		echo '<span class="alert alert_box">'.$r.'</span>';
		exit;
	}
	
  // Dernière sécurité :Accès réservé aux admins ACS
  // Last security: access restricted to ACS admins 
  if (!autoriser('acs', 'crayons_composant_store')) {
    echo crayons_var2js(array('$erreur' => _T('avis_operation_impossible')));
    exit;
  }

	$wid = $_POST['crayons'][0];
	$c = 'composants/'.$_POST['composant'].'/'.$_POST['composant'];
	// MàJ du composant - Update component : l'instanciation d'un objet composant fait la mise a jour
	include_spip('inc/composant/classComposantPrive');
	$cprovi = new AdminComposant($_POST['composant'], $_POST['nic']);
	// Retourne la vue - Return vue
	$contexte = array('var_mode'=>'recalcul');
	$contextes_ids = array('id_article','id_rubrique','id_mot','id_groupe','recherche','page');
	foreach($contextes_ids as $cid) {
		if (isset($_POST[$cid]))
			$contexte = array_merge(array($cid=>$_POST[$cid]), $contexte);
	}
	// langue traitee a part pour pouvoir donner une valeur par defaut
	if (isset($_POST['lang']))
		$lang = $_POST['lang'];
	else
		$lang = $GLOBALS['spip_lang'];
	$contexte = array_merge(array('lang'=>$lang), $contexte);
  $return[$wid] = vues_dist('composant', $c, $_POST['nic'], $contexte);
  $return['$erreur'] = '';
	echo crayons_var2js($return);
	exit;
}

function vues_dist($type, $modele, $id, $content){
  if (find_in_path(($fond = 'vues/composant').'.html')) {
		$contexte = array(
		    'nic' => $id,
		    'c' => $modele,
		    'lang' => $GLOBALS['spip_lang']
		);
		$contexte = array_merge($contexte, $content);
		include_spip('public/assembler');
		return recuperer_fond($fond, $contexte);
  }
	// vue par defaut
	else {
		// pour ce qui a une {lang_select} par defaut dans la boucle,
		// la regler histoire d'avoir la bonne typo dans le propre()
		if (colonne_table($type, 'lang')) {
		lang_select($a = valeur_colonne_table($type, 'lang', $id));
		} else {
		lang_select($a = $GLOBALS['meta']['langue_site']);
	}
    return 'err 404';
	}
}
?>
