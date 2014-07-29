<?php
/**
 * Formulaire d'aide à l'insertion de la balise d'un album
 *
 * Ce formulaire permet de générer la balise d'un album en fonction du modèle et des options choisis.
 * Les modèles et leurs paramètres sont décris dans des fichiers yaml.
 * Ces yaml sont compatibles avec ceux du plugin « Insérer modèles », à quelques différences près :
 *
 * - On a un yaml par variante car les options sont trop différentes.
 * - Il y a 2 paramètres supplémentaires pour décrire le modèle : `alias` et `description`.
 * - Pour chaque saisie, l'option `config` permet d'aller chercher la valeur par défaut dans un meta.
 *   Exemple : `config: 'nivoslider/width'`
 * - La saisie `id_modele` n'est là que pour assurer la compatibilité avec « Insérer modèle »,
 *   c'est pourquoi elle est cachée dans ce formulaire via la saisie `id_album` (afficher_si).
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Chargement du formulaire d'insertion de balise d'un album
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @param int|string $id_album
 *     Identifiant de l'album.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_balise_album_charger_dist($id_album=0){

	// sans les plugins adéquats, ni la liste des modèles, on peut pas test
	// mais il faut quand même afficher la balise de base
	include_spip('inc/albums');
	if (
		!defined('_DIR_PLUGIN_YAML') OR !_DIR_PLUGIN_YAML
		OR !defined('_DIR_PLUGIN_SAISIES') OR !_DIR_PLUGIN_SAISIES
		OR !count($liste_modeles = albums_lister_modeles())
	){
		$balise = htmlspecialchars('<album'.$id_album.'>');
		$js_balise = js_balise($balise);
		return array('editable'=>false, '_balise'=>$balise, '_js_balise'=>$js_balise);
	}

	$valeurs = array();

	// Il y a 2 étapes
	$etape = is_null(_request('_etape')) ? 1 : intval(_request('_etape'));
	$valeurs['_etapes'] = 3;

	switch ($etape) {

		// étape 0 : balise de base
		case 1;
			$valeurs['_balise'] = $balise = htmlspecialchars('<album'.$id_album.'>');
			$valeurs['_js_balise'] = js_balise($balise);
			break;

		// étape 1 : choix du modèle
		case 2;
			foreach($liste_modeles as $modele=>$infos)
				$datas_modeles[$modele] = $infos['alias'];
			$saisies_modeles = array(
				array(
					'saisie' => 'radio',
					'options' => array(
						'nom' => 'modele',
						'label' => _T('album:label_modele_choisir'),
						'datas' => $datas_modeles,
						'defaut' => 'album.yaml'
					)
				)
			);
			$valeurs['_saisies'] = $saisies_modeles;
			break;

		// étape 3 : choix des paramètres puis affichage
		case 3;
			// choix des paramètres
			if (_request('choisir')) {
				$modele = _request('modele');
				// déclarer les champs du modèle choisi
				if (
					$infos = infos_modele_album($modele)
					AND isset($infos['parametres'])
					AND is_array($saisies = $infos['parametres'])
				) {
					$valeurs['_saisies'] = $saisies;
					include_spip('inc/config');
					// valeurs des saisies
					foreach($saisies as $saisie=>$params) {
						$nom = $params['options']['nom'];
						$valeurs[$nom] = '';
						// on récupère éventuellement la valeur par défaut dans un meta
						if (isset($params['options']['config']))
							$valeurs[$nom] = lire_config($params['options']['config']);
						// on donne une valeur par défaut à « id_album » afin de cacher le champ « id_modele » (via afficher_si),
						// qui n'est là que pour compatibilité avec le plugin « Insérer modèles »
						$valeurs['id_album'] = $id_album;
					}
				}
			}
			// affichage de la balise
			elseif(_request('generer')) {
				$valeurs['_balise'] = _request('_balise');
				$valeurs['_js_balise'] = _request('_js_balise');
				$valeurs['fini'] = true;
			}
			break;
	}

	return $valeurs;

}

/**
 * Vérifications du formulaire d'insertion de balise d'un album : étape 2
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @param int|string $id_album
 *     Identifiant de l'album.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_balise_album_verifier_3_dist($id_album=0){

	$erreurs = array();
	include_spip('inc/saisies');
	include_spip('inc/albums');
	$modele = _request('modele');
	$infos_modele = infos_modele_album($modele);
	$saisies = $infos_modele['saisies'];
	$erreurs = saisies_verifier($saisies);

	return $erreurs;
}

/**
 * Traitement du formulaire d'insertion de balise d'un album
 *
 * Traiter les champs postés
 *
 * @param int|string $id_album
 *     Identifiant de l'album.
 * @return array
 *     Retours des traitements
 */
function formulaires_balise_album_traiter_dist($id_album=0){

	$res = array();
	$res['editable'] = true;

	include_spip('inc/albums');
	$modele = _request('modele');
	$infos = infos_modele_album($modele);
	$champs = array();
	if (
		$infos = infos_modele_album($modele)
		AND isset($infos['parametres'])
		AND is_array($saisies = $infos['parametres'])
	) {
		foreach($saisies as $saisie=>$params)
			$champs[] = $params['options']['nom'];
	}

	$balise = '<album'.$id_album;
	// d'abord les options connues : variante, classe, align
	if (_request('variante') && _request('variante')!='')
		$balise .= '|'._request('variante');
	if (_request('classe') && _request('classe')!='')
		$balise .= '|'._request('classe');
	if (_request('align') && _request('align')!='')
		$balise .= '|'._request('align');
	// puis les options propres au modèle
	foreach ($champs as $champ) {
		if(
			!in_array($champ,array('modele','id_modele','id_album','classe','align','variante'))
			&& _request($champ) && _request($champ)!=''
		) {
			if($champ == _request($champ))
				$balise .= "|$champ";
			// On transforme les tableaux en une liste
			elseif (is_array(_request($champ)))
				$balise .= "|$champ=".implode(',',_request($champ));
			else
				$balise .= "|$champ="._request($champ);
		}
	}
	$balise .= '>';
	// ajout de <wbr> pour des retours à la ligne corrects
	$balise_txt = preg_replace("/([\|,])/","<wbr>$1",htmlspecialchars($balise));
	set_request('_balise',$balise_txt);
	set_request('_js_balise',js_balise($balise));
	//$res['message_ok'] = _T('album:texte_double_clic_inserer_balise');

	return $res;

}

/**
 * Micro fonction qui renvoie le code js pour insérer une balise dans le texte
 *
 * @param int $balise.
 * @return int
 */
function js_balise($balise) {
	return "barre_inserer('".texte_script($balise)."', $('textarea[name=texte]')[0]);";
}

?>
