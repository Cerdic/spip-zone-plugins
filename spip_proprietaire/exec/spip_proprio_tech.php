<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
//ini_set('display_errors', 1);error_reporting(E_ALL);

function spip_proprio_exporter($what=array()){
	$code = '';
	if(isset($what['configuration']) && $what['configuration']=='oui') {
		$conf = spip_proprio_recuperer_config();
		$code .= "\n".'$proprio_config = '.var_export($conf, true).";\n";
	}
	if(isset($what['languages']) && $what['languages']=='oui') {
		spip_proprio_proprietaire_texte();
		$code .= "\n".'$proprio_i18n_proprietaire_fr = '.var_export($GLOBALS['i18n_proprietaire_fr'], true).";\n";
	}

	$code = "// Exportation config SPIP Proprio\n// Site d'origine : ".$GLOBALS['meta']['nom_site']."\n// Cree le : ".date("Y-m-d H:i:s")."\n".$code;
	$log = ecrire_fichier(_DIR_DUMP.'spiproprio_export_'.date('Ymd').'.php.gz', '<'."?php\n$code\n?".'>', true);
	return $log;
}

function spip_proprio_importer($file=null){
	if (is_null($file)) return;
	$ok = false;

	$archive = _DIR_DUMP.$file;
	if (@file_exists($archive) AND $gz = gzopen($archive, "rb")) {
		$php='';
	    while(!gzeof($gz)) {
	        $text = gzgets($gz, 1024);
	        if(!substr_count($text, '<?php') && !substr_count($text, '?>'))
	        	$php .= $text;
	    }
//	    var_export($php); exit;
	    eval("$php");
	    if (isset($proprio_config)) {
			include_spip('inc/meta');
			$ok = ecrire_meta(_META_SPIP_PROPRIO, serialize($proprio_config), 'non');
			ecrire_metas();
			
		}
		if (isset($proprio_i18n_proprietaire_fr)) {
			$ok = creer_fichier_textes_proprietaire($proprio_i18n_proprietaire_fr);
		}
	}
	return $ok;
}

function liste_proprio_dump() {
	$str = '';
	$liste_dump = preg_files(_DIR_DUMP,'\.php\.gz?$',50,false);
	if ($liste_dump && count($liste_dump))
		foreach($liste_dump as $i=>$file) {
			$filename = substr($file, strrpos($file, '/')+1);
			$filename_short = str_replace('.php.gz', '', $filename);
			$str = "<option value='$filename'>$filename_short</option>";
		}
	return $str;
}

function exec_spip_proprio_tech() {
	global $connect_statut, $spip_lang_right, $spip_lang_left;
	if ($connect_statut != "0minirezo" ) { include_spip('inc/minipres'); echo minipres(); exit; }
	include_spip('inc/presentation');
	$commencer_page = charger_fonction('commencer_page', 'inc');
	$titre_page = _T('spip_proprio:proprietaire_export_import');

// --------
// Traitement du formulaire
	$msg_export = $msg_import = false;
	if ($a = _request('do_proprio_export')) {
		$datas = array(
			'configuration' => _request('configuration'),
			'languages' => _request('languages'),
		);
		if( $ok = spip_proprio_exporter($datas) )
			$msg_export = _T('spip_proprio:ok_export', array('fichier'=>_DIR_DUMP.'spip_proprio_export.php'));
		else $msg_export = _T('spip_proprio:erreur_export');
	}
	elseif ($a = _request('do_proprio_import')) {
		$archive = _request('import_archive');
		if( $ok = spip_proprio_importer($archive) )
			$msg_import = _T('spip_proprio:ok_import');
		else $msg_import = _T('spip_proprio:erreur_import');
	}
// -------

	$contenu = debut_cadre_trait_couleur("rien.gif", true, "", _T('spip_proprio:outil_exporter'))
		.( $msg_export ? "<p><strong>".$msg_export."</strong></p>" : '' )
		."<form method='get' action='' enctype='multipart/form-data'><div>
			<input type='hidden' name='exec' value='spip_proprio_tech' />
			<input type='hidden' name='do_proprio_export' value='oui' />
			<p class='editer'><label>
				<input type='checkbox' class='checkbox' name='configuration' id='configuration' value='oui' />
				"._T('spip_proprio:exporter_configuration')."</label>
			</p>
			<p class='editer'><label>
				<input type='checkbox' class='checkbox' name='languages' id='languages' value='oui' />
				"._T('spip_proprio:exporter_fichiers_langues')."</label>
			</p>
			<span><input type='submit' value='"._T('spip_proprio:bouton_exporter')."' class='fondo' style='float: right' /></span>
		</div></form>"
		. fin_cadre_trait_couleur(true);
	$liste = liste_proprio_dump();
	if($liste)
		$contenu .= debut_cadre_trait_couleur("rien.gif", true, "", _T('spip_proprio:outil_importer'))
		.( $msg_import ? "<p><strong>".$msg_import."</strong></p>" : '' )
		."<form method='get' action='' enctype='multipart/form-data'><div>
			<input type='hidden' name='exec' value='spip_proprio_tech' />
			<input type='hidden' name='do_proprio_import' value='oui' />
			<p class='editer'><label>
				"._T('spip_proprio:importer_dump')."
				<select name='import_archive' id='import_archive'>
				".$liste."
				</select>
				</label>
			</p>
			<span><input type='submit' value='"._T('spip_proprio:bouton_importer')."' class='fondo' style='float: right' /></span>
		</div></form>"
		. fin_cadre_trait_couleur(true);
	$info_texte = icone_horizontale(_T('spip_proprio:proprietaire_retour_plateforme'), generer_url_ecrire('spip_proprio'), find_in_path('images/idisk-dir-24.png'), 'rien.gif', false);
	$info_supp = _T("spip_proprio:proprietaire_export_import_texte_supp");
	$icone = find_in_path('images/stock_export.png');


	echo($commencer_page(_T('spip_proprio:spip_proprio')." - ".$titre_page, 'configuration', "configuration")),
		"<br /><br />", debut_gauche('', true),
		debut_cadre_relief($icone, true, "", $titre_page), $info_supp, $info_texte, fin_cadre_relief(true), 
		"<br class='nettoyeur' />", creer_colonne_droite('', true), debut_droite('', true),
		gros_titre($titre_page,'', false), $contenu, fin_gauche(), fin_page();
}

?>