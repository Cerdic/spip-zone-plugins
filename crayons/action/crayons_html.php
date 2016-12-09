<?php
/**
 * Crayons
 * plugin for spip
 * (c) Fil, toggg 2006-2013
 * licence GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Affiche le controleur (formulaire) d'un crayon
 * suivant la classe CSS décrivant le champ à éditer (produit par `#EDIT`)
 *
 * @param string $class
 *   Class CSS décrivant le champ
 * @param null $c
 *
 * @return array
 *   Tableau avec 2 entrées possibles :
 *   - '$erreur' : texte d'erreur éventuel
 *   - '$html' : code HTML du controleur
**/
function affiche_controleur($class, $c = null) {
	$return = array('$erreur'=>'');

	if (preg_match(_PREG_CRAYON, $class, $regs)) {
		list(,$nomcrayon,$type,$champ,$id) = $regs;
		$regs[] = $class;

		// A-t-on le droit de crayonner ?
		spip_log("autoriser('crayonner', $type, $id, NULL, array('modele'=>$champ)", 'crayons_distant');
		if (!autoriser('crayonner', $type, $id, null, array('modele'=>$champ))) {
			$return['$erreur'] = "$type $id: " . _U('crayons:non_autorise');
		} else {
			// Trouver la fonction de controleur PHP à utiliser
			$f = charger_fonction($type.'_'.$champ, 'controleurs', true)
			or $f = charger_fonction($champ, 'controleurs', true)
			or $f = charger_fonction($type, 'controleurs', true)
			or $f = 'controleur_dist';

			$f = pipeline('crayons_controleur', array(
				'args' => array(
					'nomcrayon' => $nomcrayon,
					'type' => $type,
					'champ' => $champ,
					'id' => $id,
					'class' => $class,
				),
				'data' => $f,
			));

			list($html,$status) = $f($regs, $c);
			if ($status) {
				$return['$erreur'] = $html;
			} else {
				$return['$html'] = $html;
			}
		}
	} else {
		$return['$erreur'] = _U('crayons:donnees_mal_formatees');
	}

	return $return;
}

/**
 * Contrôleur par défaut.
 *
 * Il recherche la présence d'un contrôleur au format html pour éditer le champ ou type de crayon demandé.
 *
 * S'il n'en trouve pas crée un contrôleur en se basant sur le type de champ dans la base de données,
 * mais se limite à afficher soit un 'textarea' (contrôleur texte), soit un 'input' (contrôleur ligne).
 *
 * @param array $regs
 * @param null $c
 * @return array Liste : HTML, erreur
 */
function controleur_dist($regs, $c = null) {
	list( , $nomcrayon, $type, $champ, $id, $class) = $regs;
	$options = array(
		'class' => $class
	);
	list($distant,$table) = distant_table($type);

	// Si le controleur est un squelette html, on va chercher
	// les champs qu'il lui faut dans la table demandee
	// Attention, un controleur multi-tables ne fonctionnera
	// que si les champs ont le meme nom dans toutes les tables
	// (par exemple: hyperlien est ok, mais pas nom)
	if (($fichier = find_in_path(($controleur = 'controleurs/' . $type . '_' . $champ) . '.html'))
		|| ($fichier = find_in_path(($controleur = 'controleurs/' . $champ) .'.html'))) {
		if (!lire_fichier($fichier, $controldata)) {
			die('erreur lecture controleur');
		}
		if (preg_match_all('/\bname=(["\'])#ENV\{name_(\w+)\}\1/', $controldata, $matches, PREG_PATTERN_ORDER)) {
			$champ = $matches[2];
		}
	} else {
		$controleur = '';
	}

	$valeur = valeur_colonne_table($type, $champ, $id);

	#spip_log("$valeur = valeur_colonne_table($type, $champ, $id);");
	#spip_log($champ);

	if ($valeur === false) {
		return array("$type $id $champ: " . _U('crayons:pas_de_valeur'), 6);
	}
/*	if (is_scalar($valeur)) {
		$valeur = array($champ => $valeur);
	}*/

	// type du crayon (a revoir quand le core aura type ses donnees)
	$inputAttrs = array();
	if ($controleur) {
		$options['hauteurMini'] = 80; // base de hauteur mini
		$option['inmode'] = 'controleur';
		$options['controleur'] = $controleur;
	} elseif (preg_match(",[\n\r],", $valeur[$champ])
		or (($champ == 'valeur') && ($id == 'descriptif_site'))
		or
	// on regarde le type tel que defini dans serial
	// (attention il y avait des blob dans les vieux spip)
		($sqltype = colonne_table($type, $champ)) &&
		(in_array($sqltype['type'], array('mediumtext', 'longblob', 'longtext')) ||
		(($sqltype['type'] == 'text' || $sqltype['type'] == 'blob')
		and in_array($champ, array('descriptif', 'bio'))))) {
		// si la valeur fait plusieurs lignes on doit mettre un textarea
		// derogation specifique pour descriptif_site de spip_metas
		$options['hauteurMini'] = 80; // hauteur mini d'un textarea
		$option['inmode'] = 'texte';
	} else { // ligne, hauteur naturelle
		$options['hauteurMaxi'] = 0;
		$option['inmode'] = 'ligne';
		// c'est un nombre entier
		if ($sqltype['long']) {
			// si long est [4,3] sa longueur maxi est 8 (1234,123)
			if (is_array($sqltype['long'])) {
				if (count($sqltype['long']) == 2) {
					$inputAttrs['maxlength'] = $sqltype['long'][0] + 1 + $sqltype['long'][1];
				} else {
					// on ne sait pas ce que c'est !
					$inputAttrs['maxlength'] = $sqltype['long'][0];
				}
			} else {
				$inputAttrs['maxlength'] = $sqltype['long'];
			}
		}
	}

	$crayon = new Crayon($nomcrayon, $valeur, $options, $c);
	$inputAttrs['style'] = implode('', $crayon->styles);

	if (!$controleur) {
		$inputAttrs['style'] .= 'width:' . $crayon->largeur . 'px;' .
		($crayon->hauteur ? ' height:' . $crayon->hauteur . 'px;' : '');
	}

	$html = $controleur ? $crayon->formulaire(null, $inputAttrs) :
					$crayon->formulaire($option['inmode'], $inputAttrs);
	$status = null;

	return array($html,$status);
}

// Definition des crayons
class Crayon {
	// le nom du crayon "type-modele-id" comme "article-introduction-237"
	var $name;
	// type, a priori une table, extrait du nom plus eventuellement base distante
	var $type;
	// table la table a crayonner
	var $table;
	// distant base distante
	var $distant;
	// modele, un champ comme "texte" ou un modele, extrait du nom
	var $modele;
	// l'identificateur dans le type, comme un numero d'article
	var $id;
	// la ou les valeurs des champs du crayon, tableau associatif champ => valeur
	var $texts = array();
	// une cle unique pour chaque crayon demande
	var $key;
	// un md5 associe aux valeurs pour verifier et detecter si elles changent
	var $md5;
	// classe css
	var $class;
	// dimensions indicatives
	var $largeurMini = 170;
	var $largeurMaxi = 700;
	var $hauteurMini = 80;
	var $hauteurMaxi = 700;
	var $largeur;
	// le mode d'entree: texte, ligne ou controleur
	var $inmode = '';
	// eventuellement le fond modele pour le controleur
	var $controleur = '';
	var $styles = array();

	// le constructeur du crayon
	// $name : son nom
	// $texts : tableau associatif des valeurs ou valeur unique si crayon monochamp
	// $options : options directes du crayon (developpement)
	function __construct($name, $texts = array(), $options = array(), $c = null) {
		$this->name = $name;

		list($this->type, $this->modele, $this->id) = array_pad(explode('-', $this->name, 3), 3, '');
		list($this->distant,$this->table) = distant_table($this->type);
		if (is_scalar($texts) || is_null($texts)) {
			$texts = array($this->modele => $texts);
		}
		$this->texts = $texts;
		$this->key = strtr(uniqid('wid', true), '.', '_');
		$this->md5 = $this->md5();
		foreach ($options as $opt => $val) {
			$this->$opt = $val;
		}
		$this->dimension($c);
		$this->css();
	}

	// calcul du md5 associe aux valeurs
	function md5() {
		#spip_log($this->texts, 'crayons');
		return md5(serialize($this->texts));
	}

	// dimensions indicatives
	function dimension($c) {
		// largeur du crayon
		$this->largeur = min(max(intval(_request('w', $c)), $this->largeurMini), $this->largeurMaxi);
		// hauteur maxi d'un textarea selon wh: window height
		$maxheight = min(max(intval(_request('wh', $c)) - 50, 400), $this->hauteurMaxi);
		$this->hauteur = min(max(intval(_request('h', $c)), $this->hauteurMini), $maxheight);
		$this->left = _request('left');
		$this->top = _request('top');
		$this->w = _request('w');
		$this->h = _request('h');
		$this->ww = _request('ww');
		$this->wh = _request('wh');
	}

	// recuperer les elements de style
	function css() {
		foreach (array('color', 'font-size', 'font-family', 'font-weight', 'line-height', 'min-height', 'text-align') as $property) {
			if (null !== ($p = _request($property))) {
				$this->styles[] = "$property:$p;";
			}
		}

		$property = 'background-color';
		if (!$p = _request($property)
			or $p == 'transparent') {
			$p = 'white';
		}
		$this->styles[] = "$property:$p;";
	}

	// formulaire standard
	function formulaire($contexte = array(), $inputAttrs = array()) {
		return
			$this->code() .
			$this->input($contexte, $inputAttrs);
	}

	// balises input type hidden d'identification du crayon
	function code() {
		return
		 '<input type="hidden" class="crayon-id" name="crayons[]"'
		.' value="' . $this->key .'" />'."\n"
		. '<input type="hidden" name="name_'.$this->key
		.'" value="' . $this->name .'" />'."\n"
		. '<input type="hidden" name="class_' . $this->key
		. '" value="' . $this->class . '" />' . "\n"
		. '<input type="hidden" name="md5_'.$this->key
		.'" value="' . $this->md5 . '" />'."\n"
		. '<input type="hidden" name="fields_'.$this->key
		.'" value="'.join(',', array_keys($this->texts)).'" />'
		."\n"
		;
	}

/**
 * Fabriquer les balises des champs d'apres un modele controleurs/(type_)modele.html
 *
 * @param array $contexte
 * 	tableau (nom=>valeur) qui sera enrichi puis passe à recuperer_fond
 * @return string
 *  le contenu de recuperer_fond du controleur
 */
	function fond($contexte = array()) {
		include_spip('inc/filtres');
		$contexte['id_' . $this->type] = $this->id;
		$contexte['id_' . $this->table] = $this->id;
		$contexte['crayon_type'] = $this->type;
		$contexte['crayon_modele'] = $this->modele;
		$contexte['lang'] = $GLOBALS['spip_lang'];
		$contexte['key'] = $this->key;
		$contexte['largeur'] = $this->largeur;
		$contexte['hauteur'] = $this->hauteur;
		$contexte['self'] = _request('self');
		foreach ($this->texts as $champ => $val) {
			$contexte['name_' . $champ] = 'content_' . $this->key . '_' . $champ;
		}
		$contexte['style'] = join(' ', $this->styles);
		include_spip('public/assembler');
		return recuperer_fond($this->controleur, $contexte);
	}

/**
 * Fabriquer les balises du ou des champs
 * $attrs est un tableau (attr=>val) d'attributs communs ou pour le champs unique
 *
 * @param string|array $spec
 *  soit un scalaire 'ligne' ou 'texte' précisant le type de balise
 *  soit un array($champ=>array('type'=>'...', 'attrs'=>array(attributs specifique du champs)))
 * @return string
 * 	le html de l'input
 */
	function input($spec = 'ligne', $attrs = array()) {
		if ($this->controleur) {
			return $this->fond($spec);
		}
		include_spip('inc/filtres');
		$return = '';
		foreach ($this->texts as $champ => $val) {
			$type = is_array($spec) ? $spec[$champ]['type'] : $spec;
			switch ($type) {
				case 'texte':
					$id = uniqid('wid');
					$input = '<textarea style="width:100%;" class="crayon-active"'
					. ' name="content_'.$this->key.'_'.$champ.'" id="'.$id.'">'
					. "\n"
					. entites_html($val)
					. "</textarea>\n";
					break;
				case 'ligne':
				default:
					$input = '<input class="crayon-active text" type="text"'
					. ' name="content_'.$this->key.'_'.$champ.'"'
					. ' value="'
					. entites_html($val)
					. '" />'."\n";
			}

			if (is_array($spec) && isset($spec[$champ]['attrs'])) {
				foreach ($spec[$champ]['attrs'] as $attr => $val) {
					$input = inserer_attribut($input, $attr, $val);
				}
			}

			foreach ($attrs as $attr => $val) {
				$input = inserer_attribut($input, $attr, $val);
			}

			// petit truc crado pour mettre la barre typo si demandee
			// pour faire propre il faudra reprogrammer la bt en jquery
			$meta_crayon = isset($GLOBALS['meta']['crayons']) ? unserialize($GLOBALS['meta']['crayons']) : array();
			if (isset($meta_crayon['barretypo'])
				and $meta_crayon['barretypo']
				and $type == 'texte') {
				// Pas la peine de mettre cette barre si PortePlume est la
				if (!(
						function_exists('chercher_filtre')
						and $f = chercher_filtre('info_plugin')
						and $f('PORTE_PLUME', 'est_actif')
					)
				) {
					include_spip('inc/barre');
					$input = "<div style='width:".$this->largeur."px;height:23px;'>"
						. (function_exists('afficher_barre')
							? afficher_barre("document.getElementById('$id')")
							: '')
						. '</div>'
						. $input;
				}
			}

			$return .= $input;
		}
		return $return;
	}
}

/**
 *	Fabriquer les boutons du formulaire
 *
 *  @param array $boutons
 * 	 Le tableau des boutons
 *  @return string
 * 	 Le html des boutons
 */
function crayons_boutons($boutons = array()) {
	$boutons['submit'] = array('ok', texte_backend(_T('bouton_enregistrer')));
	$boutons['cancel'] = array('cancel', texte_backend(_T('crayons:annuler')));

	$html = '';
	foreach ($boutons as $bnam => $bdef) {
		if ($bdef) {
			$html .= '<button type="button" class="crayon-' . $bnam .
				'" title="' . $bdef[1] . '">' . $bdef[1] . '</button>';
		}
	}

	if ($html) {
		return '<div class="crayon-boutons"><div>'.$html.'</div></div>';
	}
}

function crayons_formulaire($html, $action = 'crayons_store') {
	if (!$html) {
		return '';
	}

	// on est oblige de recreer un Crayon pour connaitre la largeur du form.
	// Pb conceptuel a revoir
	$crayon = new Crayon('');
	$class = ($crayon->largeur < 250 ? ' small' : '');


	include_spip('inc/filtres');
	return liens_absolus(
		'<div class="formulaire_spip">'
		. '<form class="formulaire_crayon'.$class.'" method="post" action="'
		. url_absolue(parametre_url(self(), 'action', $action))
		. '" enctype="multipart/form-data">'
		. $html
		. crayons_boutons()
		. '</form>'
		.'</div>'
	);
}

//
// Un Crayon avec une verification de code de securite
//
class SecureCrayon extends Crayon {

	function __construct($name, $text='') {
		parent::__construct($name, $text);
	}

	function code() {
		$code = parent::code();
		$secu = md5($GLOBALS['meta']['alea_ephemere']. '=' . $this->name);

		return
			$code
			.'<input type="hidden" name="secu_'.$this->key.'" value="'.$secu.'" />'."\n";
	}
}

/**
 * Action affichant le controleur html ou php adéquat
 *
 * on affiche le formulaire demande (controleur associe au crayon)
 * Si le crayon n'est pas de type "crayon", c'est un crayon etendu, qui
 * integre le formulaire requis à son controleur (pour avoir les boutons
 * du formulaire dans un controleur Draggable, par exemple, mais il y a
 * d'autres usages possibles)
 *
 */
function action_crayons_html_dist() {
	include_spip('inc/crayons');

	// Utiliser la bonne langue d'environnement
	if (!isset($GLOBALS['forcer_lang']) or !$GLOBALS['forcer_lang'] or ($GLOBALS['forcer_lang'] === 'non')) {
		lang_select($GLOBALS['auteur_session']['lang']);
	}

	$return = affiche_controleur(_request('class'));
	if (!_request('type') or _request('type') == 'crayon') {
		if (!empty($return['$html'])) {
			$return['$html'] = crayons_formulaire($return['$html']);
		}
	}

	$json = trim(crayons_json_encode($return));

	header('Content-Type: text/plain; charset=utf-8');
	die($json);
}
