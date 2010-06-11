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



/**
 * Lecture de la configuration
 *
 * lire_config() permet de recuperer une config depuis le php<br>
 * memes arguments que la balise (forcement)<br>
 * $cfg: la config, lire_config('montruc') est un tableau<br>
 * lire_config('/table/champ') lit le valeur de champ dans la table des meta 'table'<br>
 * lire_config('montruc/sub') est l'element "sub" de cette config equivalent a lire_config('/meta/montruc/sub')<br>
 *
 * $unserialize est mis par l'histoire
 *
 * @param  string  $cfg          la config
 * @param  mixed   $def          un défaut optionnel
 * @param  boolean $unserialize  n'affecte que le dépôt 'meta'
 * @return string
 */
function spip_bonux_lire_config($cfg='', $def=null, $unserialize=true) {
	static $store = array();

	// lire le stockage sous la forme /table/valeur
	// ou valeur qui est en fait implicitement /meta/valeur
	// ou conteneur/valeur qui est en fait implicitement /meta/conteneur/valeur

	// par defaut, sur la table des meta
	$table = 'meta';
	$cfg = explode('/',$cfg);
	// si le premier argument est vide, c'est une syntaxe /table/
	if (!reset($cfg)) {
		array_shift($cfg);
		$table = array_shift($cfg);
		if (!isset($GLOBALS[$table]))
			lire_metas($table);
		if (!isset($GLOBALS[$table]))
			return $def;
	}

	$r = $GLOBALS[$table];
	// si on a demande #CONFIG{/meta,'',0} il faut serializer
	if (!count($cfg) AND !$unserialize)
		$r = serialize($r);
	while($conteneur = array_shift($cfg)) {
		$r = isset($r[$conteneur])?$r[$conteneur]:null;
		// deserializer tant que c'est necessaire
		if ($r  AND is_string($r) AND (count($cfg) OR $unserialize))
			$r = unserialize($r);
	}

	if (is_null($r)) return $def;
	return $r;
}


if (!function_exists('balise_CONFIG')) {
/**
 * #CONFIG retourne lire_config()
 *
 * Le 3eme argument permet de controler la serialisation du resultat
 * (mais ne sert que pour le depot 'meta') qui doit parfois deserialiser
 *
 * ex: |in_array{#CONFIG{toto,#ARRAY,1}}.
 *
 * Ceci n'affecte pas d'autres depots et |in_array{#CONFIG{toto/,#ARRAY}} sera equivalent
 * #CONFIG{/tablemeta/champ,defaut} lit la valeur de 'champ' dans la table des meta 'tablemeta'
 *
 * @param  Object $p  Arbre syntaxique du compilo
 * @return Object
 */
function balise_CONFIG($p) {
	if (!$arg = interprete_argument_balise(1,$p)) {
		$arg = "''";
	}
	$sinon = interprete_argument_balise(2,$p);
	$unserialize = sinon(interprete_argument_balise(3,$p),"false");

	$p->code = 'spip_bonux_lire_config(' . $arg . ',' .
		($sinon && $sinon != "''" ? $sinon : 'null') . ',' . $unserialize . ')';

	return $p;
}
}
?>