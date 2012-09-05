<?php
/**
 * Plugin Formidable Sondage
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajoute un graphique des réponses sur les formulaires
 * de formidables qui sont des sondages et sont déjà répondu 
 *
 * @pipeline formulaire_fond
 * @param array $flux Données du pipeline
 * @return array Données du pipeline
**/
function fsondage_formulaire_fond($flux) {
	if ($flux['args']['form'] == 'formidable') {
		$contexte = $flux['args']['contexte'];
		// si le formulaire n'est pas editable, c'est que c'est possiblement
		// un sondage a afficher !
		if (!$contexte['editable'] and isset($contexte['_formidable'])) {
			$formulaire = $contexte['_formidable'];
			// il faut que l'on ne puisse pas répondre pour afficher le sondage au chargement
			if (!autoriser('repondre', 'formulaire', $formulaire['id_formulaire'], null, array('formulaire'=>$formulaire)))
			{
				$traitements = unserialize($formulaire['traitements']);
				if (is_array($traitements)
				  and isset($traitements['enregistrement'])
				  and isset($traitements['enregistrement']['sondage'])
				  and $traitements['enregistrement']['sondage'])
				{
					// Nous sommes face à un sondage auquel on a déjà répondu !
					// On remplace complètement l'affichage du formulaire
					// par un affichage du résultat de sondage !
					$id_formulaire = $formulaire['id_formulaire'];
					$flux['data'] = recuperer_fond('modeles/fsondage', array(
						'id_formulaire' => $id_formulaire,
					));
				}
			}
		}
	}
	return $flux;
}

/**
 * Complète les chargement des formulaires formidable et de configuration
 * des traitements des formulaires formidable
 * 
 * Ajoute la saisie indiquant que le formulaire est un sondage
 * dans le formulaire des traitements d'un formulaire formidable
 *
 * Complète les infos de chargement du formulaire formidable
 * 
 * @pipeline formulaire_charger
 * @param array $flux Données du pipeline
 * @return array Données du pipeline
**/
function fsondage_formulaire_charger($flux) {

	// traitements des formulaires formidables
	if ($flux['args']['form'] == 'editer_formulaire_traitements') {
		// liste des saisies du formulaire
		$saisies = $flux['data']['_configurer_traitements'];
		#var_dump($saisies[3]['saisies']);
		// saisie indiquant si c'est un sondage ou non
		$saisie_sondage = array(
			'saisie' => 'oui_non',
			'options' => array(
				'nom' => 'traitements[enregistrement][sondage]',
				'label' => '<:fsondage:traiter_enregistrement_option_sondage_label:>',
				'explication' => '<:fsondage:traiter_enregistrement_option_sondage_explication:>',
				'defaut' => '',
				'inserer_fin' => "
					<script type='text/javascript'>
						(function(\$){
						$(document).ready(function() {
							/* Obtenir la saisie de sondage */
							\$sondage = \$('.formulaire_editer_formulaire_traitements .editer_traitements_enregistrement_sondage');
							/* Si c'est un sondage, on ferme les autres options */
							if (\$sondage.find('#champ_traitements_enregistrement_sondage_oui').is(':checked')) {
								\$sondage.parent().find('li:not(.editer_traitements_enregistrement_sondage)').hide();
							}
							/* Si la valeur du sondage change, on ouvre ou ferme les autres options */
							\$sondage.change(function(){
								if (\$sondage.find('#champ_traitements_enregistrement_sondage_oui').is(':checked')) {
									\$sondage.parent().find('li:not(.editer_traitements_enregistrement_sondage)').hide();
								} else {
									\$sondage.parent().find('li:not(.editer_traitements_enregistrement_sondage)').show();
								}
							});
						});
						})(jQuery);
					</script>
				",
			)
		);
		// insertion de notre saisie
		$lieu = saisies_chercher($saisies, 'traitements[enregistrement][multiple]', true);
		#var_dump($lieu);
		$saisies = saisies_inserer($saisies, $saisie_sondage, $lieu);
		// on sauve nos modifications
		$flux['data']['_configurer_traitements'] = $saisies;
	}
	return $flux;
}


/**
 * Lorsqu'on demande un sondage dans le formulaire des traitements d'un
 * formulaire formidable, on applique d'office des valeurs à certains
 * champs que l'on a caché.
 *
 * @pipeline formulaire_charger
 * @param array $flux Données du pipeline
 * @return array Données du pipeline
**/
function fsondage_formulaire_verifier($flux) {
	if ($flux['args']['form'] == 'editer_formulaire_traitements') {
		$traitements_choisis = _request('traitements_choisis');
		$traitements = _request('traitements');
		// on verifie que le traitement 'enregistrement' est coché
		if (is_array($traitements_choisis) and in_array('enregistrement', $traitements_choisis)) {
			// dans ce cas là, on regarde si 'sondage' est coché
			if (is_array($traitements)
			  and is_array($traitements['enregistrement'])
			  and $enr = &$traitements['enregistrement']
			  and isset($enr['sondage'])
			  and $enr['sondage']) {
				// si sondage est là, on met des valeurs obligatoires
				// sur les options d'enregistrement
				$enr = array_merge($enr, array(
					'multiple' => '',
					'modifiable' => '',
					'identification' => 'cookie',
					'moderation' => 'posteriori',
				));
				set_request('traitements', $traitements);
			}
		}
	}
	return $flux;
}

?>
