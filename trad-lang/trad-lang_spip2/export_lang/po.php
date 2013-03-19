<?php
/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 * Fichier d'export d'un module de langue en .po (Gettext)
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'export d'une langue d'un module en .po
 * 
 * Les chaînes avec un statut NEW sont vidées
 * Les chaînes avec un statut MODIF sont mises en "fuzzy"
 * 
 * @param string $module 
 * 		Le module à exporter (le champ "module" dans la base)
 * @param string $langue 
 * 		La langue à exporter
 * @param string $dir_lang 
 * 		Le répertoire où stocker les fichiers de langue
 * @return string $fichier 
 * 		Le fichier final
 */
function export_lang_po_dist($module,$langue,$dir_lang){
	$x=$tous=array();
	$contenu = '';
	
	/**
	 * Le fichier final
	 * local/cache-lang/module_lang.po
	 */
	$fichier = $dir_lang."/".$module."_".$langue.".po";

	/**
	 * Les informations du module
	 */	
	$info_module = sql_fetsel('*','spip_tradlang_modules','module='.sql_quote($module));
	$url_trad = url_absolue(generer_url_entite($info_module['id_tradlang_module'],'tradlang_module'));
	
	/**
	 * Gestion des auteurs
	 * 
	 * On ajoute une liste de traducteurs en entête de fichier (non obligatoire)
	 * On ajoute également l'item "Last-Translator : user <email>" dans les métas ($last_auteur)
	 */
	$last_auteur = array();
	if($langue != $info_module['lang_mere']){
		$traducteur = sql_fetsel('id_tradlang,traducteur','spip_tradlangs',"module=".sql_quote($module)." AND lang=".sql_quote($langue),"",'maj DESC','0,1');
		if(is_numeric($traducteur['traducteur']))
			$id_auteur = $traducteur['traducteur'];
		else
			$id_auteur = sql_select('id_auteur','spip_versions','objet="tradlang" AND id_objet='.intval($traducteur['id_tradlang']),"",'id_version DESC','0,1');
		
		$last_auteur = sql_fetsel('nom,email','spip_auteurs','id_auteur='.intval($id_auteur));
		
		$traducteurs[$lang] = array();
		$people_unique = array();
		$liste_traducteurs = sql_select('DISTINCT(traducteur)','spip_tradlangs','module='.sql_quote($module)." and lang=".sql_quote($langue));
		while ($t = sql_fetch($liste_traducteurs)){
			$traducteurs_lang = explode(',',$t['traducteur']);
			foreach($traducteurs_lang as $traducteur){
				if(!in_array($traducteur,$people_unique)){
					if(is_numeric($traducteur) AND $id_auteur=intval($traducteur)){
						$traducteur_supp['nom'] = extraire_multi(sql_getfetsel('nom','spip_auteurs','id_auteur='.$id_auteur));
						$traducteur_supp['lien'] = url_absolue(generer_url_entite($id_auteur,'auteur'),$url_site);
					}else if(trim(strlen($traducteur)) > 0){
						$traducteur_supp['nom'] = trim($traducteur);
						$traducteur_supp['lien'] = '';
					}
					if(isset($traducteur_supp['nom']))
						$traducteurs[$lang][] = $traducteur_supp;
					unset($traducteur_supp);
					$people_unique[] = $traducteur;
				}
			}
		}
		foreach($traducteurs as $lang => $peoples) {
			$trad_texte = "#\n# Traducteurs :\n";
			if ($peoples) {
				foreach ($peoples as $people) {
					$trad_texte .= "# ".$people['nom']." (".$people['lien'].")\n";
				}
			}
		}
	}

	/**
	 * Création de l'entête du fichier généré
	 */
	$contenu .= '# This is a SPIP language file  --  Ceci est un fichier langue de SPIP
# extrait automatiquement de '.$url_trad.'
'
. (isset($trad_texte) ? $trad_texte : '')
. 'msgid ""
msgstr ""
"Project-Id-Version: '.$info_module['nom_mod'].'\n"
'
. (defined('_DIR_PLUGIN_TICKETS') ? '"Report-Msgid-Bugs-To: '.url_absolue(generer_url_public('tickets')).'\n"
':'')
. '"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"X-Generator: SPIP Trad Lang 2.0.5\n"
"X-Poedit-SourceCharset: utf-8\n"
"Language: '.$langue.'\n"
'
. (isset($last_auteur['nom'])?'"Last-Translator: '.extraire_multi($last_auteur['nom']).' <'.$last_auteur['email'].'>\n"
' : '')
.'"Language-Team: SPIP-trad <spip-trad@rezo.net>\n"
';

	/**
	 * Les chaines
	 * 
	 * On crée un bloc pour chaque chaînes sous la forme
	 * 
	 * #, php-format
	 * #| msgid "id_dans_la_base" 
	 * msgid "Item dans la langue originale"
	 * msgstr "Item dans la langue actuelle (traduit), si non traduit, vide"
	 */
	$res=sql_select("id,str,comm,statut","spip_tradlangs","module=".sql_quote($module)." AND lang=".sql_quote($langue),"id");
	while ($row=sql_fetch($res)) {
		$tous[$row['id']] = $row;
	}
	ksort($tous);
	
	foreach ($tous as $row) {
		if (trim($row['comm'])) $row['comm']=" # ".trim($row['comm']); // on rajoute les commentaires ?

		$str = $row['str'];

		$oldmd5 = md5($str);
		$str = unicode_to_utf_8(
			html_entity_decode(
				preg_replace('/&([lg]t;)/S', '&amp;\1', $str),
				ENT_NOQUOTES, 'utf-8')
		);
		$newmd5 = md5($str);

		if ($oldmd5 !== $newmd5) sql_updateq("spip_tradlangs",array('md5'=>$newmd5), "md5=".sql_quote($oldmd5)." AND module=".sql_quote($module));
		$str_original = sql_getfetsel('str','spip_tradlangs','id ='.sql_quote($row['id']).' AND module='.sql_quote($module).' AND lang='.sql_quote($info_module['lang_mere']));

		$x[]=($row['comm'] ? "#".$row['comm']."\n" : "").
"
#, ".(($row['statut'] == 'MODIF') ? "fuzzy, php-format" : "php-format")."
#| msgid \"".$row['id']."\"
msgid \"".str_replace('"','\"',$str_original)."\"
msgstr \"".(($row['statut'] == 'NEW') ? '' : str_replace('"','\"',$str))."\"";
	}

	$contenu .= str_replace("\r\n", "\n", join("\n",$x));
	
	ecrire_fichier($fichier,$contenu);
	return $fichier;
}
?>