<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_fromphpbb - first step form      #
#  Authors : 2004+ Jean-Luc Bechennec - Chryjs, 2007            #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : chryjs!@!free!.!fr                                 #
# [fr] Menu d'accueil pour la migration d'un forum phpBB        #
# [en] Home page base of phpBB forum migration                  #
#---------------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spipbb');

if ( !empty($setmodules) and spipbb_is_configured() and $GLOBALS['spipbb']['configure']=='oui' )
{
	$file = basename(__FILE__);
	$modules['outils']['fromphpbb'] = $file;
	return;
}

// ------------------------------------------------------------------------------
// [fr] Methode exec
// [en] Exec method
// [fr] Affiche la page complete spip privee avec le formulaire
// [en] Provides the full spip private space form
// ------------------------------------------------------------------------------
function exec_spipbb_admin_fromphpbb()
{
	spip_log(__FILE__." exec_spipbb_admin_fromphpbb() CALL",'spipbb');
	if ( !spipbb_is_configured() or ($GLOBALS['spipbb']['configure']!='oui') 
		 or $GLOBALS['spipbb']['config_id_secteur'] != 'oui'
		 or empty($GLOBALS['spipbb']['id_secteur']) ) {
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('spipbb_admin_configuration', ''));
		exit;
	}

	global $connect_statut;
	// [fr] Pour le moment l acces est reserve a l administrateur, a voir plus tard
	// [fr] pour tester plutot en fonction rubrique de l import comme pour les articles...
	// [en] For now the access is only allowed to the admin, will check it later
	// [en] in order to check it for each rubrique like for the articles...

	if ($connect_statut != '0minirezo') {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo "<strong>"._T('avis_non_acces_page')."</strong>";
		echo fin_page();
		exit;
	}

	// [fr] initialisations
	// [en] initialize
	if (!function_exists('filtrer_entites')) @include_spip('inc/filtres');
	$row['titre'] = filtrer_entites(_T('info_nouvel_article'));

	// [fr] La conf pre-existante domine
	// [en] Pre-existing config leads
	$row['id_rubrique'] = $GLOBALS['spipbb']['id_secteur'];
	if (!$row['id_rubrique']) {
		if ($connect_id_rubrique)
			$row['id_rubrique'] = $id_rubrique = $connect_id_rubrique[0];
		else {
			//$r = sql_query("SELECT id_rubrique FROM spip_rubriques ORDER BY id_rubrique DESC LIMIT 1");
			//$row_rub = sql_fetch($r);
			$row_rub = sql_fetsel('id_rubrique','spip_rubriques','','',array('id_rubriques DESC'),'0,1');
			$row['id_rubrique'] = $id_rubrique = $row_rub['id_rubrique'];
		}
		if (!autoriser('creerarticledans','rubrique',$row['id_rubrique'] )){
			// [fr] manque de chance, la rubrique n'est pas autorisee, on cherche un des secteurs autorises
			// [en] too bad , this rubrique is not allowed, we look for the first allowed sector
			//$res = sql_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent=0");
			$res = sql_select('id_rubrique','spip_rubriques',array('id_parent=0'));
			while (!autoriser('creerarticledans','rubrique',$row['id_rubrique'] ) && $row_rub = sql_fetch($res)){
				$row['id_rubrique'] = $row_rub['id_rubrique'];
			}
		}
	}
	// [fr] recuperer les donnees du secteur
	// [en] load the sector datas
	//$r = sql_query("SELECT id_secteur FROM spip_rubriques WHERE id_rubrique=$id_rubrique");
	//$row_rub = sql_fetch($r);
	$row_rub = sql_fetsel('id_secteur','spip_rubriques',array("id_rubrique=".$GLOBALS['spipbb']['id_secteur']));
	$row['id_secteur'] = $row_rub['id_secteur'];
	$id_rubrique = $row['id_rubrique'];

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:fromphpbb_titre'), "configuration", 'spipbb');

	if (!$row
	   OR !autoriser('creerarticledans','rubrique',$GLOBALS['spipbb']['id_secteur'])) {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		echo fin_page();
		exit;
	}

	echo gros_titre(_T('spipbb:titre_spipbb'),'',false) ;

	if (spipbb_is_configured() AND $GLOBALS['spipbb']['config_id_secteur'] == 'oui' ) {
		echo debut_grand_cadre(true);
		echo afficher_hierarchie($GLOBALS['spipbb']['id_secteur']);
		echo fin_grand_cadre(true);
	}

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo  _T('spipbb:fromphpbb_titre');
	echo fin_boite_info(true);
	echo spipbb_admin_gauche($GLOBALS['spipbb']['id_secteur'],'spipbb_admin_fromphpbb');

	echo creer_colonne_droite($id_rubrique,true);
	echo debut_droite($id_rubrique,true);

	echo spipbb_fromphpbb_formulaire($row);
	echo fin_gauche(), fin_page();
} // exec_spipbb_admin_fromphpbb

// ------------------------------------------------------------------------------
// [fr] Genere le formulaire de saisie des parametres de migration
// [en] Generates the form to fill with migration parameters
// ------------------------------------------------------------------------------
function spipbb_fromphpbb_formulaire($row=array())
{
	$assembler = charger_fonction('assembler', 'public'); // recuperer_fond est dedans
	if (!function_exists('recuperer_fond')) include_spip('public/assembler');

	// [fr] On va essayer de "deviner" ou on peut trouver un fichier de conf phpbb
	// [en] We try to "guess" where is the phpbb config file
	$phpbb_subdirs = array('.', 'forum','phpBB','phpBB2','phpBB3','FORUM','PHPBB','PHPBB2','PHPBB3');
	$phpbb_roots = array( realpath(_DIR_RACINE), $GLOBALS['_SERVER']['DOCUMENT_ROOT'] );
	$phpbb_conf = array();
	$liste_fichiers = "";
	$radio=0;

	while ( list($k,$rootdir) = each($phpbb_roots) ) {
		while ( list($key, $subdir) = each($phpbb_subdirs) ) {
			$filename = $rootdir."/".$subdir."/config.php" ;
			if ( file_exists($filename) AND is_readable($filename) ) {
				@include_once($filename);
				if (defined('PHPBB_INSTALLED') and (substr($dbms,0,5)=="mysql") ) {
					$conf['filename'] = $filename;
					// est-ce necessaire ?
					$conf['dbms'] = $dbms;
					$conf['dbname'] = $dbname;
					$conf['dbuser'] = $dbuser;
					$conf['dppasswd'] = $dbpasswd ;
					$conf['table_prefix'] = $table_prefix;
					$phpbb_conf[]=$conf;
					$contexte = array( 
						'filename'=>$conf['filename'],
						'key'=>$radio,
						);
					$liste_fichiers .= recuperer_fond("prive/spipbb_admin_fromphpbb_fichiers", $contexte) ;
					$radio++;
				} // defined and mysql only
			} // file_exists
		} // while subdirs
	} // while rootdirs


	$aider = charger_fonction('aider', 'inc');
	$config = "";
	$id_rubrique = $row['id_rubrique'];
	$id_secteur = $row['id_secteur'];
	$retour ="exec=spipbb_admin_fromphpbb";

	include_spip('inc/editer_article');
	$choix_rubrique = editer_article_rubrique($id_rubrique, $id_secteur, $config, $aider);

	$contexte = array( 
			'lien_action' => generer_action_auteur('spipbb_fromphpbb_action',$id_rubrique,$retour) ,
			'exec_script' => 'spipbb_fromphpbb_action',
			'phpbb_liste_fichiers' => $liste_fichiers,
			'choix_rubrique' => $choix_rubrique,
			'radio_checked' => $radio,
			'phpbb_test' => 'oui',
			);
	$res = recuperer_fond("prive/spipbb_admin_fromphpbb",$contexte) ;

	return $res;
} // spipbb_fromphpbb_formulaire

// ------------------------------------------------------------------------------
// Fonction supprimee en SVN... a remplacer ?
// etait dans : inc/editer_article
// ------------------------------------------------------------------------------
if (!function_exists('')) {
function editer_article_rubrique($id_rubrique, $id_secteur, $config, $aider)
{
	$chercher_rubrique = charger_fonction('chercher_rubrique', 'inc');

	$opt = $chercher_rubrique($id_rubrique, 'article', $config['restreint']);

	$msg = _T('titre_cadre_interieur_rubrique') .
	  ((preg_match('/^<input[^>]*hidden[^<]*$/', $opt)) ? '' : $aider("artrub"));

	if ($id_rubrique == 0) $logo = "racine-site-24.gif";
	elseif ($id_secteur == $id_rubrique) $logo = "secteur-24.gif";
	else $logo = "rubrique-24.gif";

	return debut_cadre_couleur($logo, true, "", $msg) . $opt .fin_cadre_couleur(true);
} } // editer_article_rubrique
?>
