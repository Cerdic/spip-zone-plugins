<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */
 


/**
 * Tester avec _request si on est dans l'edition de skeleditor et si oui, retourne l'extension du nom du fichier
 *
 * @return string
 */ 
function test_skeleditor_edition() {
$exec = _request('exec');
$filename = _request('f');
	if ($exec == 'skeleditor'
	AND $filename
	AND $infos = pathinfo($filename)
	AND $extension = $infos['extension']) 
	return $extension;
	else
	return false;
	
} 

/**
 * Produit les css dans le header_prive si nécessaire
 *
 * @param string $flux
 * @return string
 */ 
function skeleditor_insert_head_css($flux){
	$extension = test_skeleditor_edition();
		if($extension) {
		static $done = false;
		if (!$done) {
			$done = true;
			$type = "css";
			$css = skeleditor_dir($extension, $type);
			$flux .= $css; 
		}
	}
	return $flux;
}
 
/**
 * Produit les js dans le header_prive si nécessaire
 *
 * @param string $flux
 * @return string
 */ 
function skeleditor_insert_head($flux){
/*
	$extension = test_skeleditor_edition();
		if($extension) {
		$type = "js";
		$script = skeleditor_dir($extension, $type);
		$flux = skeleditor_insert_head_css($flux); // au cas ou il n'est pas implemente
		$flux .= $script;
		}*/
	return $flux;
}

// pas de compresseur si var_inclure
if (_request('var_mode')=='inclure')
	define('_INTERDIRE_COMPACTE_HEAD',true);

function skeleditor_extraire_css($texte){
	$url_base = url_de_base();
	$url_page = substr(generer_url_public('A'), 0, -1);
	$dir = preg_quote($url_page,',').'|'.preg_quote(preg_replace(",^$url_base,",_DIR_RACINE,$url_page),',');

	$css = array();
	// trouver toutes les css pour les afficher dans le bouton
	// repris du compresseur
	foreach (extraire_balises($texte, 'link') as $s) {
		if (extraire_attribut($s, 'rel') === 'stylesheet'
		AND (!($type = extraire_attribut($s, 'type'))
			OR $type == 'text/css')
		AND !strlen(strip_tags($s))
		AND $src = preg_replace(",^$url_base,",_DIR_RACINE,extraire_attribut($s, 'href'))
		AND (
			// regarder si c'est du format spip.php?page=xxx
			preg_match(',^('.$dir.')(.*)$,', $src, $r)
			OR (
				// ou si c'est un fichier
				// enlever un timestamp eventuel derriere un nom de fichier statique
				$src2 = preg_replace(",[.]css[?].+$,",'.css',$src)
				// verifier qu'il n'y a pas de ../ ni / au debut (securite)
				AND !preg_match(',(^/|\.\.),', substr($src2,strlen(_DIR_RACINE)))
				// et si il est lisible
				AND @is_readable($src2)
			)
		)) {
			if ($r)
				$css[$s] = explode('&',
					str_replace('&amp;', '&', $r[2]), 2);
			else{
				$file = preg_replace(",[?]\d+$,","",$src);
				if (strncmp($file,_DIR_VAR,strlen(_DIR_VAR))==0){
					lire_fichier($file,$c);
					if (preg_match(",^\/\*\s*(#@.*)\s*\*\/,Uims",$c,$m)){
						$inc = explode("#@",$m[1]);
						$inc = array_map('trim',$inc);
						$inc = array_filter($inc);
						foreach($inc as $i){
							if (!in_array($i,$css))
								$css["$s:$i"] = $i;
						}
					}
				}
				else
					$css[$s] = $file;
			}
		}
	}
	return $css;
}

function skeleditor_affichage_final($texte){
	if (isset($_COOKIE['spip_admin'])
	  AND $GLOBALS['html']
	  AND isset($GLOBALS['visiteur_session']['statut'])
		AND $GLOBALS['visiteur_session']['statut']
	  AND intval($GLOBALS['visiteur_session']['statut'])<"1comite"
	  AND include_spip("inc/autoriser")
	  AND autoriser("skeleditor")
	){
		if ((defined('_VAR_INCLURE') AND _VAR_INCLURE) OR (isset($GLOBALS['var_inclure']) and $GLOBALS['var_inclure']) ){
			$retour = self();
			$url = generer_url_ecrire('skeleditor','retour='.$retour.'&f=');
			$inserer = "<script type='text/javascript'>jQuery(function(){jQuery('.inclure_blocs h6:first-child').each(function(){
				jQuery(this).html(\"<a class='sepopin' href='$url\"+jQuery(this).html()+\"'>\"+jQuery(this).html()+'<'+'/a>');
			});"
			//."jQuery('a.sepopin').click(function(){if (jQuery.modalbox) jQuery.modalbox(parametre_url(this.href,'var_zajax','contenu'));return false;});"
			."});</script><style>.spip-admin-boutons {display:block;float:left;margin-right:10px; max-height:300px; overflow:auto;} .spip-admin-boutons a{display:block;opacity:0.7;} .spip-admin-boutons:hover,.spip-admin-boutons a:hover {opacity:1.0;}</style>
			</body>";
			$texte = preg_replace(",</body>,",$inserer,$texte);
			
			$css = skeleditor_extraire_css($texte);
			$lienplus = array();
			foreach($css as $src){
				// si c'est un skel, le trouver
				if (is_array($src))
					$src = find_in_path($src[0]."."._EXTENSION_SQUELETTES);
				if ($src)
					$lienplus[] = "<a href='$url".urlencode($src)."'"
			.">".basename($src)."<\/a>";
			}
			if (count($lienplus)){
				$lienplus = implode('',$lienplus);
				$lienplus = "<span class='spip-admin-boutons' id='inclure'>$lienplus<\/span>";
			};

		} else {
			$lienplus = "<a href='".parametre_url(self(),'var_mode','inclure')."' class='spip-admin-boutons' "
			."id='inclure'>"._T('skeleditor:squelettes')."<\/a>";
		}
		if ($lienplus)
			$inserer = "<script type='text/javascript'>/*<![CDATA[*/jQuery(function(){jQuery('#spip-admin').append(\"$lienplus\");});/*]]>*/</script></body>";
			$texte = preg_replace(",</body>,",$inserer,$texte);
	}
	return $texte;
}


?>
