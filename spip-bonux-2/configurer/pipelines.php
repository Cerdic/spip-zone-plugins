<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

/**
 * Proposer un chargement par defaut pour les #FORMULAIRE_CONFIGURER_XXX
 *
 * @param array $flux
 * @return array
 */
function spip_bonux_formulaire_charger($flux){
	if ($form = $flux['args']['form']
	  AND strncmp($form,'configurer_',11)==0 // un #FORMULAIRE_CONFIGURER_XXX
		AND !charger_fonction("charger","formulaires/$form/",true) // sans fonction charger()
		) {

		$flux['data'] = spip_bonux_formulaires_configurer_recense($form);
		$flux['data']['editable'] = true;
		if (_request('var_mode')=='configurer'){
			var_dump($flux['data']);
		}
	}
	return $flux;
}

/**
 * Proposer un traitement par defaut pour les #FORMULAIRE_CONFIGURER_XXX
 *
 * @param array $flux
 * @return array
 */
function spip_bonux_formulaire_traiter($flux){
	if ($form = $flux['args']['form']
	  AND strncmp($form,'configurer_',11)==0 // un #FORMULAIRE_CONFIGURER_XXX
		AND !charger_fonction("traiter","formulaires/$form/",true) // sans fonction charger()
		) {

		// charger les valeurs
		// ce qui permet de prendre en charge une fonction charger() existante
		// qui prend alors la main sur l'auto detection
		if ($charger_valeurs = charger_fonction("charger","formulaires/$form/",true))
			$valeurs = call_user_func_array($charger_valeurs,$flux['args']['args']);
		$valeurs = pipeline(
			'formulaire_charger',
			array(
				'args'=>array('form'=>$form,'args'=>$flux['args']['args'],'je_suis_poste'=>false),
				'data'=>$valeurs)
		);
		// ne pas stocker editable !
		unset($valeurs['editable']);

		// recuperer les valeurs postees
		$store = array();
		foreach($valeurs as $k=>$v){
			if (substr($k,0,1)!=='_')
				$store[$k] = _request($k);
		}

		// stocker en base
		// par defaut, dans un conteneur serialize dans spip_meta (idem CFG)
		$conteneur = false;
		$table = 'meta';
		if (isset($valeurs['_meta_table'])) {
			$table = $valeurs['_meta_table'];
			$conteneur = (isset($valeurs['_meta_conteneur'])?$valeurs['_meta_conteneur']:false);
		}
		if ($conteneur)
			$store = array($conteneur => serialize($store));

		foreach($store as $k=>$v){
			ecrire_meta($k, $v, true, $table);
		}

		$flux['data'] = array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
	}
	return $flux;
}

/**
 * Retrouver les champs d'un formulaire en parcourant son squelette
 * et en extrayant les balises input, textarea, select
 * 
 * @param string $form
 * @return array
 */
function spip_bonux_formulaires_configurer_recense($form){
	$valeurs = array();
	
	// traiter d'abord le cas fichier yaml de description

	// TODO

	// sinon cas analyse du squelette
	if ($f = find_in_path($form.'.' . _EXTENSION_SQUELETTES, 'formulaires/')
		AND lire_fichier($f, $contenu)) {

		// par defaut dans la table des meta de SPIP
		$valeurs['_meta_table'] = 'meta';
		// valeurs serializee dans un conteneur homonyme au formulaire
		$valeurs['_meta_conteneur'] = substr($form,11);

		$meta = unserialize($GLOBALS['meta'][$valeurs['_meta_conteneur']]);
		if (!$meta)
			$meta = array();

		for ($i=0;$i<2;$i++) {
			// a la seconde iteration, evaluer le fond avec les valeurs deja trouvees
			// permet de trouver aussi les name="#GET{truc}"
			if ($i==1) $contenu = recuperer_fond("formulaires/$form",$valeurs);

			$balises = array_merge(extraire_balises($contenu,'input'),
				extraire_balises($contenu,'textarea'),
				extraire_balises($contenu,'select'));

			foreach($balises as $b) {
				if ($n = extraire_attribut($b, 'name')
					AND preg_match(",^(\w+)(\[\w*\])?$,",$n,$r)
					AND !in_array($n,array('formulaire_action','formulaire_action_args'))
					AND extraire_attribut($b,'type')!=='submit')
						$valeurs[$r[1]] = (isset($meta[$r[1]])?$meta[$r[1]]:'');
			}
		}
	}

	return $valeurs;
}

?>