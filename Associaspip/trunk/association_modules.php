<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/presentation'); // utilise par "onglet1_association" (pour "onglet") puis aussi dans les pages appelantes
include_spip('inc/autoriser'); // utilise par "onglet1_association" (pour le test "autoriser") puis aussi dans les pages appelantes



/*****************************************
 * @defgroup association_navigation
 * Affichage HTML : boutons de navigation intra/inter-modules
 *
 * @return string $res
 *   code HTML du bouton et autres elements connexes
 * @ note
 *   Ces elements destines a l'affichage des modules sont dans un fichier separe
 * qui sera autonome quand il ne fera plus appel a aucune fonction de association_options.php (association_langue association_date_du_jour)
 * @note
 *   Les anciens appels de procedure doivent etre remplace par des appel de fonction :
 * icone1_association : association_navigation_raccourci1
 * onglets_association : echo association_navigation_onglets
 * raccourcis_association : echo association_navigation_raccourcis
** @{ */

/**
 * Afficher le titre de la/le page/module courante puis (en dessous) les onglets
 * des differents modules actives dans la configuration
 *
 * @param string $titre
 *   Chaine de langue du titre de la page
 * @param string $top_exec
 *   Nom du fichier "exec" de la page principale du module
 * @param bool $INSERT_HEAD
 *   Indique s'il s'agit d'une page exec classique en PHP (vrai, par defaut) ou
 *   en HTML (faux, alors) a compiler par SPIP (cas des balises) ou par le dev
 * @return string $res
 *   Debut de la page HTML de l'espace prive
 */
function association_navigation_onglets($titre='', $top_exec='', $INSERT_HEAD=TRUE) {
	$res = '';
// Recuperation de la liste des ongles
	// modules natifs toujours actifs
	$modules_actifs = array(
		array('menu2_titre_association', 'assoc_qui.png', array('association'), array('association','voir_profil'), ),
		array('menu2_titre_gestion_membres', 'annonce.gif', array('adherents'), array('association','voir_membres'), ),
	);
	// modules natifs actives en configuration
	foreach ( array('dons'=>'dons-24.gif', 'ventes'=>'ventes.gif', 'activites'=>'activites.gif', 'ressources'=>'pret-24.gif', 'comptes'=>'finances-24.png') as $module=>$icone ) {
		if ( $GLOBALS['association_metas'][$module=='ressources'?'prets':$module] )
			$modules_actifs[] = array("menu2_titre_gestion_$module", $icone, array($module), array('association', $module='comptes'?'voir_compta':"voir_$module") );
	}
	$modules_externes = pipeline('associaspip', array()); // Tableau des modules ajoutes par d'autres plugins : 'prefixe_plugin'=> array( 0=>array(bouton,onglet,actif), 1=>array(bouton,config,actif) )
	foreach ( $modules_externes as $plugin=>$boutons ) {
		if ( test_plugin_actif($plugin) )
			$modules_actifs[] = $boutons[0];
	}
// Dessin de la liste des ongles
	$onglets_actifs = '';
	foreach ($modules_actifs as $module) {
		if ( association_acces($module[3]) ) { // generation de l'onglet
			$chemin = _DIR_PLUGIN_ASSOCIATION_ICONES.$module[1]; // icone Associaspip
			if ( !file_exists($chemin) )
				$chemin = find_in_path($module[1]); // icone alternative
			$onglets_actifs .= onglet(association_langue($module[0]), generer_url_ecrire($module[2][0],$module[2][1]), $top_exec, $module[2][0], $chemin); // http://doc.spip.org/onglet
		}
	}
// Affichage
	if ($INSERT_HEAD) { // mettre ''|0|FALSE|NULL dans la balise (appel dans une page HTML-SPIPee donc et non PHP) pour eviter l'erreur de "Double occurrence de INSERT_HEAD"
		$commencer_page = charger_fonction('commencer_page', 'inc');
		$res = $commencer_page();
	}
	$res .= '<div class="table_page">';
	$res .= '<h1 class="asso_titre">'. ( $titre?association_langue($titre):_T('asso:gestion_de_lassoc', array('nom'=>$GLOBALS['association_metas']['nom']) ) ) .'</h1>'; // Nom du module. cf:  <http://programmer.spip.org/Contenu-d-un-fichier-exec>
	if ($onglets_actifs)
		$res .= '<div class="bandeau_actions barre_onglet clearfix">'. debut_onglet() .$onglets_actifs. fin_onglet() .'</div>'; // Onglets actifs
	$res .= '</div>';
	if ($INSERT_HEAD) { // Tant qu'a faire, on s'embete pas a le retaper dans toutes les pages...
		$res .= debut_gauche('',TRUE);
		$res .= debut_boite_info(TRUE);
	}
	return $res;
}

/**
 * Bloc de raccourci(s)
 *
 * @param array $raccourcis
 *   Liste des raccourcis definis chacun sous la forme :
 *   array('titre', 'icone', array('url_ecrire', 'parametres_url'), array('permission' ...), ),
 *   toutefois si le 3e element est une chaine, on la prend comme URL
 * @param string $identifiant
 *   Identifiant interne de la liste de raccourcis
 * (attention, les entiers sont reserves a usage interne !
 * les modules doivent utiliser une chaine alphabetique !)
 * @return string $res
 *   Bloc HTML du tableau des raccourcis precede de la fermeture du bloc infos (qui precede toujours dans ce plugin)
 */
function association_navigation_raccourcis($raccourcis=array(), $identifiant='') {
	$res = ''; // initialisation
	if ($identifiant) { // bloc identifie = extensible
		$modules_externes = pipeline('associaspip', array()); // Tableau des modules ajoutes par d'autres plugins : 'prefixe_plugin'=> array( 0=>array(bouton,onglet,actif), 1=>array(bouton,config,actif) )
		foreach ( $modules_externes as $plugin=>$boutons ) {
			if ( test_plugin_actif($plugin) )
				$raccourcis[] = $boutons[$identifiant];
		}
	}
	foreach($raccourcis as $params) {
		list($titre, $image, $url, $aut) = $params;
		if ( association_acces($aut) ) { // generation du raccourci
			if (is_array($url))
				$url = generer_url_ecrire($url[0],$url[1]);
			$res .= association_navigation_raccourci1($titre, $url, $image);
		}
	}

	return association_date_du_jour()
	. fin_boite_info(TRUE)
	. (count($res)?bloc_des_raccourcis($res):''); // tester si le tableau est vide (ce qui peut arriver si on n'a l'autorisation pour aucun bouton) et ne pas afficher un bloc sans bouton (c'est disgracieux et troublant)
}

/**
 * Dessin d'un raccourci du bloc des raccourcis
 *
 * @param string $texte
 *   Libelle du bouton
 * @param string $lien
 *   URL vers lequel revoie le bouton
 * @param string $image
 *   Icone du bouton (place devant le libelle)
 * @return string
 *   HTML du raccourci (icone+texte+lien)
 */
function association_navigation_raccourci1($texte, $lien, $image) {
	$chemin = _DIR_PLUGIN_ASSOCIATION_ICONES.$image; // icone Associaspip
	if ( !file_exists($chemin) )
		$chemin = find_in_path($image); // icone alternative
	return icone_horizontale(association_langue($texte), $lien, $chemin, 'rien.gif', FALSE); // http://doc.spip.org/@icone_horizontale
}

/** @} */


// Procedures de mise en page des modules

/**
 * Finition propre des pages privee du plugin
 *
 * @param bool $FIN_CADRE_RELIEF
 *   Indique s'il faut (vrai, par defaut) rajouter ou pas (faux) "fin_cadre_relief"
 * @return void
 * @note
 *   Cette fonction remplace le couplet final :
 *   echo fin_gauche(), fin_page();
 *   http://programmer.spip.org/Contenu-d-un-fichier-exec
 */
function fin_page_association($FIN_CADRE_RELIEF=TRUE) {
	if ($FIN_CADRE_RELIEF)
		echo fin_cadre_relief(true);
	echo $fin, fin_gauche(), fin_page();
}

/**
 * Cadre en relief debutant la colonne centrale/principale essentiellement
 *
 * @param string $icone
 *   Icone associe a la page, souvent celui du module.
 * @param string $titre
 *   Chaine de langue du titre
 * @param bool $DEBUT_DROITE
 *   Indique s'il faut ajouter (vrai, par defaut) ou pas "debut_droite()" avant
 * @return void
 */
function debut_cadre_association($icone, $titre, $DEBUT_DROITE=TRUE) {
	if ($DEBUT_DROITE)
		echo debut_droite('',TRUE);
	$chemin = _DIR_PLUGIN_ASSOCIATION_ICONES.$icone; // icone Associaspip
	if ( !file_exists($chemin) )
		$chemin = find_in_path($icone); // icone alternative
	debut_cadre_relief($chemin, FALSE, '', association_langue($titre) );
}

?>