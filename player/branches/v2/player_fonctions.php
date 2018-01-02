<?php
/**
 * Plugin Lecteur (mp3)
 * Licence GPL
 * 2007-2011
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Code JS a inserer dans la page pour faire fonctionner le player
 * @param $player
 * @return string
 */
function player_call_js() {
	include_spip('inc/filtres');
	lire_fichier(find_in_path("javascript/mejs-enclosure.min.js"),$enclosure);
	lire_fichier(find_in_path("javascript/mejs-init.min.js"),$init);
	$mejspath = timestamp(find_in_path("lib/mejs/mediaelement-and-player.min.js"));
	$flux = '<script type="text/javascript">/*<![CDATA[*/'
		. "var mejspath='$mejspath';\n$enclosure\n$init\n"
		. "/*]]>*/</script>\n";

	return $flux;
}


/**
 * inserer systematiquement le CSS dans la page
 * @param string $flux
 * @return string
 */
function player_insert_head_css($flux){
	$css = timestamp(find_in_path('css/player.css'));
	$flux .= "\n".'<link href="'.$css.'" rel="stylesheet" />';

	return $flux;
}

/**
 * Inserer systematiquement le JS dans la page
 * @param string $flux
 * @return string
 */
function player_insert_head($flux){
	if (test_espace_prive()
		OR (defined('_PLAYER_AFFICHAGE_FINAL') AND !_PLAYER_AFFICHAGE_FINAL)){
		$cfg = (isset($GLOBALS['meta']['player'])?unserialize($GLOBALS['meta']['player']):array());
		if (isset($cfg['insertion_auto']) AND in_array('inline_mini',$cfg['insertion_auto'])){
			$flux .= player_call_js();
		}
	}
	return $flux;
}


/**
 * Inserer JS+CSS dans la page si elle contient un player
 * (a la demande)
 * @param string $flux
 * @return string
 */
function player_affichage_final($flux){
	if ((!defined('_PLAYER_AFFICHAGE_FINAL')
	  OR _PLAYER_AFFICHAGE_FINAL)
	  AND $GLOBALS['html']){
		// inserer le head seulement si presente d'un rel='enclosure'
		// il faut etre pas trop stricte car on peut avoir rel='nofollow encolsure' etc...
		if ((strpos($flux,'enclosure')!==false)){
			$cfg = (isset($GLOBALS['meta']['player'])?unserialize($GLOBALS['meta']['player']):array());
			if (isset($cfg['insertion_auto']) AND in_array('inline_mini',$cfg['insertion_auto'])){
				// on pourrait affiner la detection avec un preg ?
				$ins = player_call_js();
				$p = stripos($flux,"</body>");
				if ($p)
					$flux = substr_replace($flux,$ins,$p,0);
				else
					$flux .= $ins;
			}
		}
	}
	return $flux;
}


/**
 * enclosures
 * ajout d'un rel="enclosure" sur les liens mp3 absolus
 * appele en pipeline apres propre pour traiter les [mon son->http://monsite/mon_son.mp3]
 * peut etre appele dans un squelette apres |liens_absolus
 *
 * @param $texte
 * @return mixed
 */
function player_post_propre($texte) {

	$reg_formats="mp3";
	// plus vite
	if (stripos($texte,".$reg_formats")!==false
	  AND stripos($texte,"<a")!==false){

		$cfg = unserialize($GLOBALS['meta']['player']);
		// insertion du mini-player inline
		if (isset($cfg['insertion_auto'])
			AND in_array('inline_mini',$cfg['insertion_auto'])){
			$texte = preg_replace_callback(
				",<a(\s[^>]*href=['\"]?(http://[a-zA-Z0-9\s()\/\:\._%\?+'=~-]*\.($reg_formats))['\"]?[^>]*)>,Uims",
				'player_enclose_link',
				$texte
				);
		}
		if (isset($cfg['insertion_auto'])
			AND in_array('player_end',$cfg['insertion_auto'])){

			preg_match_all(",<a(\s[^>]*href=['\"]?(http://[a-zA-Z0-9\s()\/\:\._%\?+'=~-]*\.($reg_formats))['\"]?[^>]*)>(.*)</a>,Uims",$texte,$matches,PREG_SET_ORDER);
			if (count($matches)){
				foreach ($matches as $m){
					$url = $m[2];
					$titre = $m[4];
					$titre = ((!strlen(textebrut($titre)) OR tester_url_absolue($titre))?player_joli_titre($url):$titre);
					$emb = recuperer_fond("modeles/player",array('url_document'=>$url,'titre'=>$titre));
					if ($wrap_embed_html = charger_fonction("wrap_embed_html","inc",true)){
						$emb = $wrap_embed_html(' ',$emb);
					}
					$texte .= $emb;
				}
			}
		}

	}

	return $texte;
}

/**
 * Ajouter enclosure sur un lien mp3
 *
 * @param $regs
 * @return mixed|string
 */
function player_enclose_link($regs){
	$rel = extraire_attribut($regs[0],'rel');
	$rel = ($rel?"$rel ":"")."enclosure";
	return inserer_attribut($regs[0],'rel',$rel);
}

/**
 * Un filtre pour afficher de joli titre a partir du nom du fichier
 * @param $titre
 * @return mixed|string
 */
function player_joli_titre($titre){
	$titre=basename($titre);
	$titre=preg_replace('/.mp3/','',$titre);
	$titre=preg_replace('/^ /','',$titre);
	$titre = preg_replace("/_/i"," ", $titre );
	$titre = preg_replace("/'/i"," ",$titre );

	return $titre ;
}