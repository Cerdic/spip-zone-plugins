<?php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_GINS_PREFIX', 'gins');

function exec_get_infos_spip_dist() {

	global $connect_statut;
	
	// Accès aux admins
	$flag_editable = ($connect_statut == "0minirezo");

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('gins:informations');
	$rubrique = 'infoconfig2';
	$sous_rubrique = _GINS_PREFIX;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('gins:gins') . ' - ' . $titre_page, $rubrique, $sous_rubrique));
	
	// la configuration spiplistes est réservée aux admins 
	if(!$flag_editable) {
		include_spip('inc/minipres');
		echo minipres();
	}
	else
	{

		include_spip('inc/plugin');
		
		$dir_icones = _DIR_PLUGIN_GINS.'images/';

		$page_result = '<div style="margin-top:3em">' . PHP_EOL
			. gros_titre(_T('titre_configuration'),'', false)
			. '</div>' . PHP_EOL
			. debut_gauche($rubrique, true)
			. debut_boite_info(true)
				. '<div id="gins-logo" style="background-image: url('._DIR_PLUGIN_GINS.'/images/gins-128.png'.');"></div>'
				. _T('gins:info_gauche_info_spip')
			. fin_boite_info(true)
			. pipeline('affiche_gauche', array('args'=>array('exec'=>'info_spip'),'data'=>''))
			. creer_colonne_droite('', true)
			. bloc_des_raccourcis(
				icone_horizontale(
					_T('gins:inventaire_auteurs'),
					generer_url_public(
						'lister_auteurs'
					),
					$dir_icones.'tableau-24.gif','rien.gif', false
				)
				.
				icone_horizontale(
					_T('gins:inventaire_doc_lies'),
					generer_url_public(
						'lister_documents_liens'
					),
					$dir_icones.'tableau-24.gif','rien.gif', false
				)
			)
			. debut_droite($rubrique, true)
			;
		
		$corps = array();
		
		// Audit SPIP
		$ii = 'configspip';
		$contexte = array();
		$comptes_auteurs = recuperer_fond('compter_auteurs', $contexte);
		$tailles_articles = recuperer_fond('compter_articles', $contexte);
		$tailles_rubriques = recuperer_fond('compter_rubriques', $contexte);
		$comptes_forums = recuperer_fond('compter_forums', $contexte);
		$comptes_documents = recuperer_fond('compter_documents', $contexte);
		$infos = array(
			  _T('gins:SPIP_version_code_revision_base') => $GLOBALS['spip_version_affichee']
				. ' ('.$GLOBALS['spip_version_code'].') '
				. (
					($svn_revision = version_svn_courante(_DIR_RACINE))
					? '['.(($svn_revision < 0) ? 'SVN ' : '') . abs($svn_revision).'] '
					: ''
				  )
				. '/' . $GLOBALS['spip_version_base'] . '/'
			, _T('gins:SPIP_plugins_actifs') => liste_plugin_actifs()
			, _T('gins:SPIP_plugins_librairies') => plugins_liste_librairies()
			, _T('gins:SPIP_nombre_auteurs_vali/tous') => $comptes_auteurs
			, _T('gins:SPIP_nombre_articles_publ/tous') => $tailles_articles
			, _T('gins:SPIP_nombre_rubriques_publ/tous') => $tailles_rubriques
			, _T('gins:SPIP_nombre_forums_publ/tous') => $comptes_forums
			, _T('gins:SPIP_nombre_documents_publ/tous') => $comptes_documents
		);
		$corps[$ii] = gins_lister_values(
			$infos
			, _T('gins:'.$ii)
		);

		// Audit systeme
		$ii = 'configsyst';
		$infos = array(
			  'OS' => php_uname() . ' - ' . (PHP_INT_SIZE * 8).' bits'
			, 'Apache version' => apache_get_version()
			, 'Apache modules' => apache_get_modules()
			, 'PHP version' => phpversion()
			, 'Zend version' => zend_version()
			, 'jQuery' => '<script type="text/javascript">' . PHP_EOL
					. '//<![CDATA[' . PHP_EOL
					. 'document.write("<span style=\"color:green\">" + jQuery.fn.jquery + "</span>")' . PHP_EOL
					. '//]]>' . PHP_EOL
					. '</script>' . PHP_EOL
					. '<noscript>'
					. '<span style="color:red">' . _T('gins:jquery_inactif') . '</span>'
					. '</noscript>' . PHP_EOL
			, 'Place disque libre' => gins_decodeSize(disk_free_space(_DIR_RACINE))
		);
		$corps[$ii] = gins_lister_values(
			$infos
			, _T('gins:'.$ii)
		);
	
		// Les constantes déclarées
		$ii = 'constantes';
		$corps[$ii] = gins_lister_values(
			get_defined_constants(1)
			, _T('gins:'.$ii)
		);
	
		// Les valeurs de configuration PHP (php.ini, ...)
		$ii = 'phpconfig';
		$corps[$ii] = gins_lister_values(
			ini_get_all()
			, _T('gins:'.$ii)
		);
		
		// Menu de haut de page
		$menu = '';
		$jamaisvu = true;
		foreach($corps as $key=>$results)
		{
			$menu .= '<li><a href="#gins-'.$key.'" class="'.($jamaisvu ? 'highlight' : '').'" name="'.$key.'">'
				._T('gins:'.$key).'</a></li>'.PHP_EOL;
			$jamaisvu = false;
		}
		
		$page_result .=
			  '<div id="gins-menu" >'.PHP_EOL
			. '<h2>'._T('gins:menu_proprietes').'</h2>'.PHP_EOL
			. '<lu>'.PHP_EOL
			. $menu
			. '</lu>'.PHP_EOL
			. '</div>'.PHP_EOL
			;
		
		$jamaisvu = true;
		foreach($corps as $key=>$results)
		{
			$page_result .=
				  '<div id="gins-'.$key.'" class="gins-item '.$key.($jamaisvu ? ' highlight' : '').'">'
				. $results
				. '</div>'.PHP_EOL
				;
			$jamaisvu = false;
		}
		
		// Fin de la page
		echo(
			  '<div id="gins-contenu">'.PHP_EOL
			. $page_result.PHP_EOL
			. '</div>'.PHP_EOL
		);
		
		echo pipeline('affiche_milieu', array('args'=>array('exec'=>$sous_rubrique),'data'=>''));
	}
	
	echo fin_gauche(), fin_page();
	
	
} // fin exec_get_infos_spip_dist()


/**
 * Les valeurs à afficher en listes
 * @return string html code
 **/
function gins_lister_values($array, $titre)
{
	$result = '';
	foreach($array as $key => $val)
	{
		$result .= '<li><strong>'.$key.'</strong>: '.gins_dump_value($val).'</li>'.PHP_EOL;
	}
	$result = ''
		. debut_cadre_trait_couleur($icone, true, '', $titre)
		. '<ul>' . PHP_EOL
		. $result
		. '</ul>' . PHP_EOL
		. fin_cadre_trait_couleur(true)
		;
	return($result);
}

/**
 * (récursif) décortique les valeurs à afficher
 * @return string html code
 **/
function gins_dump_value($value)
{
	if(($g = gettype($value)) == 'array')
	{
		$r = '';
		foreach($value as $key=>$val)
		{
			$r .= '<li>'.$key.': '.gins_dump_value($val).'</li>'.PHP_EOL;
		}
		$value =
			  '<ul>'.PHP_EOL
			. $r.PHP_EOL
			. '</ul>'.PHP_EOL
			;
	}
	elseif($g == 'boolean')
	{
		$value = '<span style=\"color:blue\">'.($value ? 'TRUE' : 'FALSE').'</span>'.PHP_EOL;
	}
	elseif($value === NULL)
	{
		$value = '<span style=\"color:grey\">NULL</span>'.PHP_EOL;
	}
	return($value);
}

/**
 * Acronymes tailles (octets, etc.)
 * @return string résultat interprété taille en octets
 * @see: http://www.php.net/manual/fr/function.disk-free-space.php#81207
 * */
function gins_decodeSize( $bytes )
{
    $types = array(_T('gins:B'), _T('gins:KB'), _T('gins:MB'), _T('gins:GB'), _T('gins:TB'));
    for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
    return( round($bytes, 2) . ' ' . $types[$i] );
}
