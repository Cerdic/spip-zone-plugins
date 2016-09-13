<?php
/**
 * Plugin PDF_VERSION pour Spip 3.x
 * Licence GPL (c) 2016 Cedric
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function pdf_version_autoriser($flux){
	return $flux;
}

/**
 * Fonction autoriser voirpdfversion generique
 * qui bloque les bot (franchement, ils n'ont rien a faire la)
 * et qui delegue a autoriser (voir) pour les humains
 *
 * Permet d'inserer des autorisations specifiques ou objet par objet
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $opt
 * @return bool
 */
function autoriser_voirpdfversion_dist($faire, $type, $id, $qui, $opt){

	if (_IS_BOT) return false;
	return autoriser('voir', $type, $id, $qui, $opt);

}

/**
 * URL du PDF d'un objet
 * @param int $id_objet
 * @param string $objet
 * @return string
 */
function generer_url_pdf_version($id_objet, $objet){
	return _DIR_RACINE . 'pdf_version.api/' . objet_type($objet) .'-' . intval($id_objet) . '.pdf';
}

/**
 * Compile la balise `#URL_PDF_VERSION` qui génère l'URL de la version PDF d'un objet
 * 
 * @balise
 * @example
 *     ```
 *     #URL_PDF_VERSION{article,123}
 *     ```
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_URL_PDF_VERSION_dist($p) {

	$_objet = interprete_argument_balise(1, $p);
	$_id_objet = interprete_argument_balise(2, $p);

	$p->code = "generer_url_pdf_version($_id_objet, $_objet)";
	$p->interdire_scripts = false;

	return $p;
}


/**
 * Pipeline post_edition : supprimer la version PDF existante d'un objet modifie
 * @param $flux
 * @return mixed
 */
function pdf_version_post_edition($flux){
	if (isset($flux['args']['table'])
	  AND $table = $flux['args']['table']
	  AND $id_objet = $flux['args']['id_objet']){
		$objet = objet_type($table);

		// supprimer la version PDF de l'objet si elle existe
		if (!defined('_DIR_PDF_VERSION')){
			include_spip('inc/pdf_version');
		}
		if (file_exists($f = _DIR_PDF_VERSION . $objet . '-' . $id_objet . '.pdf')){
			@unlink($f);
		}
	}

	return $flux;
}

/**
 * Ajouter un lien 'Voir la version PDF' sur les objets pour lesquels c'est possible
 *
 * @param array $flux
 * @return array
 */
function pdf_version_boite_infos($flux) {
	if ($objet = $flux['args']['type']
	  and $id_objet = $flux['args']['id']) {
		if (trouver_fond('pdf_version/'.$objet)
		  and objet_test_si_publie($objet, $id_objet)) {
			if (autoriser('voirpdfversion', $objet, $id_objet)){
				$url = generer_url_pdf_version($id_objet, $objet);
				$url = parametre_url($url, 'var_mode', 'recalcul'); // forcer la mise a jour
				$flux['data'] .= icone_horizontale(_T('pdf_version:icone_voir_pdf_version'), $url, 'pdf_version', $fonction="", $dummy="", $javascript="");
			}
		}
	}

	return $flux;
}