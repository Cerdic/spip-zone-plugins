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
 *   en HTML (faux, ) a compiler par SPIP (cas des balises) ou PHP gere par le developpeur
 * @return void
 */
function association_navigation_onglets($titre='', $top_exec='', $INSERT_HEAD=TRUE) {
	$modules = pipeline('modules_asso', array(
		'association' => array('asso:menu2_titre_association', 'assoc_qui.png', array('voir_profil', 'association'), ), // accueil
		'adherents' => array('asso:menu2_titre_gestion_membres', 'annonce.gif', array('voir_membres', 'association'), ), // gestion des membres
		'dons' => array('asso:menu2_titre_gestion_dons', 'dons-24.gif', array('voir_dons', 'association'), ), // gestion des dons
		'ventes' => array('asso:menu2_titre_ventes_asso', 'ventes.gif', array('voir_ventes', 'association'), ), // gestion des ventes
		'activites' => array('asso:menu2_titre_gestion_activites', 'activites.gif', array('voir_activites', 'association'), ), // gestion des activites
		'ressources' => array('asso:menu2_titre_gestion_prets', 'pret-24.gif', array('voir_ressources', 'association'), ), // gestion des ressources
		'comptes' => array('asso:menu2_titre_livres_comptes', 'finances-24.png', array('voir_compta', 'association'), ), // compta
	)); // Liste (en fait tableau PHP) des modules geres par le plugin, sous la forme : 'exec' => array("chaine:langue", "chemin/icone", array("autorisation", ...), )
// Recuperation de la liste des ongles
	$res = '';
	foreach ($modules as $exec=>$params) {
		// autorisation d'acces au module
		if ( is_array($params[2]) && count($params[2]) ) { // autorisation complete/fine
			$acces = call_user_func_array('autoriser', $params[2]);
		} elseif ( $params[2] ) { // autorisation general/globale
			$acces = autoriser($params[2]);
		} else // pas d'autorisation definie = autorise pour tous
			$acces = TRUE;
		// etat d'activation du module en configuration
		if ( in_array($exec, array('association', 'adherents')) )
			$actif = TRUE;
		else
			$actif = $GLOBALS['association_metas'][$exec=='ressources'?'prets':$exec];
		// generation de l'onglet
		if ( $actif && $acces ) {
			$chemin = _DIR_PLUGIN_ASSOCIATION_ICONES.$params[1]; // icone Associaspip
			if ( !file_exists($chemin) )
				$chemin = find_in_path($params[1]); // icone alternative
			$res .= onglet(_T($params[0]), generer_url_ecrire($exec), $top_exec, $exec, $chemin); // http://doc.spip.org/onglet
		}
	}
// Affichage
	if ($INSERT_HEAD) { // mettre ''|0|FALSE|NULL dans la balise (appel dans une page HTML-SPIPee donc et non PHP) pour eviter l'erreur de "Double occurrence de INSERT_HEAD"
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page();
	}
	echo '<div class="table_page">';
	echo '<h1 class="asso_titre">', $titre?association_langue($titre):_T('asso:gestion_de_lassoc', array('nom'=>$GLOBALS['association_metas']['nom']) ), '</h1>'; // Nom du module. cf:  <http://programmer.spip.org/Contenu-d-un-fichier-exec>
	if ($res)
		echo '<div class="bandeau_actions barre_onglet clearfix">', debut_onglet(), $res, fin_onglet(), '</div>'; // Onglets actifs
	echo '</div>';
	if ($INSERT_HEAD) { // Tant qu'a faire, on s'embete pas a le retaper dans toutes les pages...
		echo debut_gauche('',TRUE);
		echo debut_boite_info(TRUE);
	}
}

/**
 * @see association_navigation_onglets
 */
function onglets_association($titre='', $top_exec='', $INSERT_HEAD=TRUE) {
	association_navigation_onglets($titre, $top_exec, $INSERT_HEAD);
}

/**
 * Bloc de raccourci(s)
 *
 * @param string $retour
 *     URL de retour
 * @param array $raccourcis
 *   Tableau des raccourcis definis chacun sous la forme :
 *   'titre' => array('icone', array('url_ecrire', 'parametres_url'), array('permission' ...), ),
 * @return void
 */
function association_navigation_raccourcis($retour='',  $raccourcis=array()) {
	$res = ''; // initialisation
	foreach($raccourcis as $titre => $params) {
		// autorisation d'acces au module
		if ( is_array($params[2]) && count($params[2]) ) { // autorisation a calculer
			$acces = call_user_func_array('autoriser', $params[2]);
		} elseif ( is_scalar($params[2]) ) { // autorisation deja calculee (chaine ou entier ou booleen, evalue en vrai/faux...)
			$acces = autoriser($params[2]);
		} else // pas d'autorisation definie = autorise pour tous
			$acces = TRUE;
		// generation du raccourci
		if ( $acces )
			$res .= icone1_association($titre,  (is_array($params[1])?generer_url_ecrire($params[1][0],$params[1][1]):generer_url_ecrire($params[1])), $params[0]);
	}

	return association_date_du_jour()
	. fin_boite_info(TRUE)
	. bloc_des_raccourcis($res)
	. ($retour ? icone1_association('asso:bouton_retour', $retour, 'retour-24.png') : '');
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
function icone1_association($texte, $lien, $image) {
	$chemin = _DIR_PLUGIN_ASSOCIATION_ICONES.$image; // icone Associaspip
	if ( !file_exists($chemin) )
		$chemin = find_in_path($image); // icone alternative
	return icone_horizontale(association_langue($texte), $lien, $chemin, 'rien.gif', FALSE); // http://doc.spip.org/@icone_horizontale
}

/**
 * Finition propre des pages privee du plugin
 *
 * @param bool $FIN_CADRE_RELIEF
 *   Indique s'il faut (vrai, par defaut) rajouter ou pas (faux) "fin_cadre_relief"
 * @return void
 * @note
 *   Cette fonction remplace et personnalise le couplet final :
 *   echo fin_gauche(), fin_page();
 *   http://programmer.spip.org/Contenu-d-un-fichier-exec
 */
function fin_page_association($FIN_CADRE_RELIEF=TRUE) {
	$copyright = fin_page();
	$copyright = str_replace("<div class='table_page'>", "<div class='table_page contenu_nom_site'>", $copyright); // Pour eliminer le copyright a l'impression
	echo ($FIN_CADRE_RELIEF ? fin_cadre_relief() : '') . fin_gauche() . $copyright;
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
