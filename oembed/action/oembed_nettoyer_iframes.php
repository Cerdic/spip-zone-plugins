<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Conversion des iframe en lien embed
 * appeler manuellement avec ?action=oembed_nettoyer_iframes
 * autorise pour les seuls webmestres
 */
function action_oembed_nettoyer_iframes_dist(){
	include_spip('inc/autoriser');
	include_spip('inc/filtres');
	include_spip('action/editer_objet');
	if (!autoriser('webmestre')){
		die('Pas autorise');
	}
	echo "<h1>Conversion des &lt;iframe&gt; en liens oembed</h1>";
	$simu = true;
	if (_request('modif')) $simu=false;
	if ($simu) echo "<p><strong>mode SIMULATION</strong> (ajoutez &modif=1 dans l'url pour modifier les contenus)</p>";

	$tables = array(
		'spip_articles'=>array('descriptif','chapo','texte','ps'),
	);

	foreach ($tables as $table=>$champs){
		$objet = objet_type($table);

		foreach($champs as $champ){
			$primary = id_table_objet($table);
			$res = sql_select("$primary,$champ", $table, "$champ LIKE '%iframe%'");
			while ($row = sql_fetch($res)){
				$pre = "$primary=".$row[$primary].":$champ:";

				$texte = $row[$champ];
				$iframes = extraire_balises($texte, "iframe");
				if (count($iframes)){
					foreach ($iframes as $iframe){
						$url = "";
						$src = extraire_attribut($iframe, "src");
						if (strncmp($src, "//", 2)==0)
							$src = "http:" . $src;
						if (strpos($iframe, "youtube")!==false){
							if (strpos($src, "/embed/")!==false){
								$url = str_replace("?", "&", $src);
								$url = str_replace("/embed/", "/watch?v=", $url);
								echo "$pre Youtube $url<br />";
							}
							if (!$url){
								var_dump($row);
								var_dump($iframe);
								die('youtube inconnue');
							}
						} elseif (strpos($iframe, "dailymotion")!==false) {
							if (strpos($src, "/embed/")!==false){
								$url = str_replace("/embed/", "/", $src);
								$url = explode("?",$url);
								$url = reset($url);
								#var_dump($url);
								echo "$pre DailyMotion $url<br />";
							}
							if (!$url){
								var_dump($row);
								var_dump($iframe);
								die('dailymotion inconnue');
							}
						} elseif (strpos($iframe, "player.vimeo")!==false) {
							if (strpos($src, "/video/")!==false){
								$url = str_replace("/video/", "/", $src);
								$url = str_replace("player.vimeo","vimeo",$url);
								$url = explode("?",$url);
								$url = reset($url);
								#var_dump($url);
								echo "$pre Vimeo $url<br />";
							}
							if (!$url){
								var_dump($row);
								var_dump($iframe);
								die('vimeo inconnue');
							}
						} elseif (strpos($iframe, "soundcloud")!==false) {
							// un peu complique :
							// il faut faire une requete oembed sur l'url api, avec iframe=false
							// pour recuperer du html avec un lien vers la page soundcloud
							parse_str(end(explode("?",$src)),$args);
							$api_url = $args['url'];
							include_spip("inc/oembed");
							include_spip("inc/distant");
							$provider = oembed_verifier_provider($api_url);
							$data_url = parametre_url(url_absolue($provider['endpoint'],url_de_base()),'url',$api_url,'&');
							$data_url = parametre_url($data_url,'format','json','&');
							$data_url = parametre_url($data_url,'iframe','false','&');
							$json = recuperer_page($data_url);
							$json = json_decode($json,true);
							$link = extraire_balise($json['html'],'a');
							if ($url = extraire_attribut($link,"href")){
								echo "$pre SoundCloud $url<br />";
							}
							if (!$url){
								var_dump($row);
								var_dump($iframe);
								die('soundcloud inconnue');
							}
						} else {
							echo "$pre iframe inconnue : ".entites_html($iframe)."<br />";
						}
						if ($url){
							$texte = str_replace($iframe, "\n" . $url . "\n", $texte);
							if (preg_match(",<center>\s*" . preg_quote($url, ",") . ".*</center>,Uims", $texte, $m)){
								$texte = str_replace($m[0], "\n" . $url . "\n", $texte);
							}
							$texte = preg_replace(",\s+" . preg_quote($url, ",") . "\s+,ims", "\n" . $url . "\n", $texte);
						}
					}
					if ($texte!==$row[$champ]){
						echo "$pre Corrige $champ <br />";
						if ($simu) {
							objet_modifier($objet,$row[$primary],array($champ=>$texte));
						}
						//sql_updateq($champ, array($champ => $texte), "$primary=" . intval($row[$primary]));
					}
				}
			}
		}
	}
}
