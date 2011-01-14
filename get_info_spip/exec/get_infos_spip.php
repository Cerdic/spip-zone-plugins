<?php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

define('_GINS_PREFIX', 'gins');
define('_GINS_PAGINATION', 50);

function exec_get_infos_spip_dist() {

	global $connect_statut;
	
	$eol = PHP_EOL;
	
	// Accès aux admins
	$flag_editable = ($connect_statut == '0minirezo');

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
		
		$is_mysql = defined('_MYSQL_SET_SQL_MODE') && _MYSQL_SET_SQL_MODE;

		$page_result = '<div style="margin-top:3em">' . $eol
			. gros_titre(_T('titre_configuration'),'', false)
			. '</div>' . $eol
			. debut_gauche($rubrique, true)
			. debut_boite_info(true)
				. '<div id="gins-logo" style="background-image: url('._DIR_PLUGIN_GINS.'/images/gins-128.png'.');"></div>'
				. _T('gins:info_gauche_info_spip')
			. fin_boite_info(true)
			. pipeline('affiche_gauche', array('args'=>array('exec'=>'info_spip'),'data'=>''))
			. creer_colonne_droite('', true)
			. bloc_des_raccourcis(
				_T('gins:t_inventaires')
				.
				icone_horizontale(
					_T('gins:inventaire_auteurs'),
					parametre_url(
						generer_url_public(
							'lister_auteurs'
						), 'pagination', _GINS_PAGINATION
					),
					$dir_icones.'tableau-24.gif','rien.gif', false
				)
				.
				icone_horizontale(
					_T('gins:inventaire_doc_lies'),
					parametre_url(
						generer_url_public(
							'lister_documents_liens'
						), 'pagination', _GINS_PAGINATION
					),
					$dir_icones.'tableau-24.gif','rien.gif', false
				)
			)
			. debut_droite($rubrique, true)
			;
		
		$corps = array();
		
		//////////
		// Audit SPIP
		$ii = 'configspip';
		$contexte = array();
		$comptes_auteurs = recuperer_fond('compter_auteurs', $contexte);
		$comptes_articles = recuperer_fond('compter_articles', $contexte);
		$comptes_rubriques = recuperer_fond('compter_rubriques', $contexte);
		$comptes_breves = recuperer_fond('compter_breves', $contexte);
		$comptes_forums = recuperer_fond('compter_forums', $contexte);
		$comptes_documents = recuperer_fond('compter_documents', $contexte);
		$comptes_sites_syndic = recuperer_fond('compter_sites_syndic', $contexte);
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
			, _T('gins:SPIP_nombre_articles_publ/tous') => $comptes_articles
			, _T('gins:SPIP_nombre_rubriques_publ/tous') => $comptes_rubriques
			, _T('gins:SPIP_nombre_breves_publ/toutes') => $comptes_breves
			, _T('gins:SPIP_nombre_forums_publ/tous') => $comptes_forums
			, _T('gins:SPIP_nombre_documents_publ/tous') => $comptes_documents
			, _T('gins:SPIP_nombre_sites_syndic') => $comptes_sites_syndic
		);
		$corps[$ii] = gins_lister_values(
			$infos
			, _T('gins:'.$ii)
		);

		//////////
		// Audit systeme
		$ii = 'configsyst';
		$infos = array(
			  'OS' => php_uname() . ' - ' . (PHP_INT_SIZE * 8).' bits'
			, 'Apache version' => (function_exists('apache_get_version') ? apache_get_version() : null)
			, 'Apache modules' => (function_exists('apache_get_modules') ? apache_get_modules() : null)
			, 'PHP version' => phpversion()
			, 'SQL version' => gins_sqlversion()
			, 'Zend version' => zend_version()
			, 'jQuery' => '<script type="text/javascript">' . $eol
					. '//<![CDATA[' . $eol
					. 'document.write("<span style=\"color:green\">" + jQuery.fn.jquery + "</span>")' . $eol
					. '//]]>' . $eol
					. '</script>' . $eol
					. '<noscript>'
					. '<span style="color:red">' . _T('gins:jquery_inactif') . '</span>'
					. '</noscript>' . $eol
			, 'Place disque libre' => gins_decodeSize(disk_free_space(_DIR_RACINE))
		);
		$corps[$ii] = gins_lister_values(
			$infos
			, _T('gins:'.$ii)
		);
	
		//////////
		// Les constantes déclarées
		$ii = 'constantes';
		$corps[$ii] = gins_lister_values(
			get_defined_constants(1)
			, _T('gins:'.$ii)
		);
	
		//////////
		// Les valeurs de configuration PHP (php.ini, ...)
		$ii = 'phpconfig';
		$corps[$ii] = gins_lister_values(
			ini_get_all()
			, _T('gins:'.$ii)
		);
		
		//////////
		// Les tables SQL (toutes les tables, SPIP et autres)
		$ii = 'tablessql';
		$res = 
			($is_mysql)
			? sql_query('SHOW TABLE STATUS')
			: sql_showbase('%')
			;
		
		if($res)
		{
			$arr = array();
			$sum = 0;
			while($row = sql_fetch($res))
			{
				if($is_mysql) {
					
					$dl = $row['Data_length'];
					$sum += $dl;
					
					$arr[] = $row['Name']
						. ' ('
						. _T('gins:n_row', array('n' => ($n = $row['Rows'])))
						. ($n ? ' : '.gins_decodeSize($dl) : '')
						. ')'
						;
				}
				else {
					$arr[] = array_shift($row);
				}
			}
			
			// ajouter le résultat aux tableaux
			$corps[$ii] = gins_lister_values(
				$arr
				, _T('gins:'.$ii)
				, ($sum ? ' ('.gins_decodeSize($sum).')' : '')
			);
		}

		//////////
		// Menu de haut de page (onglets)
		$menu = '';
		$jamaisvu = true;
		foreach($corps as $key=>$results)
		{
			$menu .= '<li><a href="#gins-'.$key.'" class="'.($jamaisvu ? 'highlight' : '').'" name="'.$key.'">'
				._T('gins:'.$key).'</a></li>'.$eol;
			$jamaisvu = false;
		}
		$page_result .=
			  '<div id="gins-menu" >'.$eol
			. '<h2>'._T('gins:menu_proprietes').'</h2>'.$eol
			. '<lu>'.$eol
			. $menu
			. '</lu>'.$eol
			. '</div>'.$eol
			;
		
		//////////
		// Préparation des vues
		$jamaisvu = true;
		foreach($corps as $key=>$results)
		{
			$page_result .=
				  '<div id="gins-'.$key.'" class="gins-item '.$key.($jamaisvu ? ' highlight' : '').'">'
				. $results
				. '</div>'.$eol
				;
			$jamaisvu = false;
		}
		
		//////////
		// Fin de la page
		echo(
			  '<div id="gins-contenu">'.$eol
			. $page_result.$eol
			. '</div>'.$eol
		);
		
		echo pipeline('affiche_milieu', array('args'=>array('exec'=>$sous_rubrique),'data'=>''));
	}
	
	echo fin_gauche(), fin_page();
	
	
} // fin exec_get_infos_spip_dist()


/**
 * Les valeurs à afficher en listes
 * @return string html code
 **/
function gins_lister_values($array, $titre, $stitre = '')
{
	$eol = PHP_EOL;
	
	$result = '';
	foreach($array as $key => $val)
	{
		$result .= '<li><strong>'.$key.'</strong>: '.gins_dump_value($val).'</li>'.$eol;
	}
	$result = ''
		. debut_cadre_trait_couleur($icone, true, '', $titre.$stitre)
		. '<ul>' . $eol
		. $result
		. '</ul>' . $eol
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
	$eol = PHP_EOL;
	
	if(($g = gettype($value)) == 'array')
	{
		$r = '';
		foreach($value as $key=>$val)
		{
			$r .= '<li>'.$key.': '.gins_dump_value($val).'</li>'.$eol;
		}
		$value =
			  '<ul>'.$eol
			. $r.$eol
			. '</ul>'.$eol
			;
	}
	elseif($g == 'boolean')
	{
		$value = '<span style="color:blue">'.($value ? 'TRUE' : 'FALSE').'</span>'.$eol;
	}
	elseif($value === NULL)
	{
		$value = '<span style="color:grey">NULL</span>'.$eol;
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

/**
 * Identification SQL
 * */
function gins_sqlversion()
{
	$r = array();
	
	if(defined('MYSQL_ASSOC'))
	{
		$r['MySQL'] = mysql_get_server_info();
	}
	
	if(defined('PGSQL_ASSOC'))
	{
		// PostgreSQL 7.4 ou supérieur. ?
		// @todo: à compléter
		$r['PostgreSQL'] = ($s = @pg_version()) ? serialize($s) : 'inconnu';
	}
	
	if(defined('SQLITE_ASSOC'))
	{
		$r['SQLite'] = sqlite_libversion();
	}
	
	return($r);
}
