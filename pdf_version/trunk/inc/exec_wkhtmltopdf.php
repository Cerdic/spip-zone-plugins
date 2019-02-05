<?php
/**
 * Plugin PDF_VERSION pour Spip 3.x
 * Licence GPL (c) 2016 Cedric
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Appel de wkhtml, soit par ligne de commande, soit par API HTTP sur un site qui en dispose en ligne de commande
 * @param $html_file
 * @param $pdf_file
 * @param array $args
 */
function inc_exec_wkhtmltopdf_dist($html_file, $pdf_file, $args = array()) {

	include_spip('inc/config');

	$methode = lire_config('pdf_version/methode','exec');
	if ($methode === 'exec'
	  and $wkhtmltopdf_path = lire_config('pdf_version/wkhtmltopdf_path','/usr/local/bin/wkhtmltopdf')) {

		// Appel de wkhtmltopdf en ligne de commande

		$args[] = $html_file;
		$args[] = $pdf_file;

		$cmd = $wkhtmltopdf_path;
		foreach ($args as $k=>$v){
			if (!is_numeric($k)){
				$cmd .= " $k";
			}
			$cmd .= ' '.escapeshellarg($v);
		}

		$cmd .= '  2>&1';
		spip_log($cmd,"wkhtmltopdf");

		$output = array();
		$return_var = 0;
		exec($cmd, $output, $return_var);
		spip_log(implode("\n",$output),"wkhtmltopdf");
		if ($return_var) {
			spip_log("Erreur $return_var","wkhtmltopdf" . _LOG_ERREUR);
		}

		return true;
	}

	if($methode === 'http'
	  and $wkhtmltopdf_api_url = lire_config('pdf_version/wkhtmltopdf_api_url')) {

		// Appel de wkhtmltopdf via API http

		// lire le html et le renseigner dans les arguments
		$html = "";
		lire_fichier($html_file, $html);
		$args['html'] = normalise_retours_ligne_html($html);
		if (isset($args['--header-html'])) {
			lire_fichier($args['--header-html'], $args['--header-html']);
			$args['--header-html'] = normalise_retours_ligne_html($args['--header-html']);
		}
		if (isset($args['--footer-html'])) {
			lire_fichier($args['--footer-html'], $args['--footer-html']);
			$args['--footer-html'] = normalise_retours_ligne_html($args['--footer-html']);
		}

		include_spip('inc/distant');
		if(function_exists('recuperer_url')) {
			$res = recuperer_url($wkhtmltopdf_api_url, array(
				'methode' => 'POST',
				'datas' => $args,
				'file' => $pdf_file,
			));
		}
		else {
			$res = recuperer_page($wkhtmltopdf_api_url, $pdf_file, false, null, $args);
		}

		spip_log($wkhtmltopdf_api_url . " $html_file -> $pdf_file : " . var_export($res, true), "wkhtmltopdf");

		return true;
	}

	spip_log("methode $methode inconnue ou non configuree","wkhtmltopdf");
	return false;

}

function &normalise_retours_ligne_html(&$texte) {
	$texte = str_replace("\r\n", "\n", $texte);
	$texte = str_replace("\r", "\n", $texte);
	$texte = str_replace("\n", "\r\n", $texte);

	return $texte;
}
