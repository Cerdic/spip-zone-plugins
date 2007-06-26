<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function affiche_controleur($class) {
  $return = array('$erreur'=>'');

  if (preg_match(_PREG_CRAYON, $class, $regs)) {
    list(,$nomcrayon,$type,$champ,$id) = $regs;
    $regs[] = $class;
    include_spip('inc/autoriser');
    if (!autoriser('modifier',$type, $id, NULL, array('champ'=>$champ))) {
        $return['$erreur'] = "$type $id: " . _U('crayons:non_autorise');
    } else {
        $f = charger_fonction($type.'_'.$champ, 'controleurs', true)
        OR $f = charger_fonction($champ, 'controleurs', true)
        OR $f = charger_fonction($type, 'controleurs', true)
        OR $f = 'controleur_dist';
        list($html,$status) = $f($regs);
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

function controleur_dist($regs) {
    list( , $nomcrayon, $type, $champ, $id, $class) = $regs;
    $options = array('class' => $class);
	// Si le controleur est un squelette html, on va chercher
	// les champs qu'il lui faut dans la table demandee
	// Attention, un controleur multi-tables ne fonctionnera
	// que si les champs ont le meme nom dans toutes les tables
	// (par exemple: hyperlien est ok, mais pas nom)
	if (($fichier = find_in_path(
     ($controleur = 'controleurs/' . $type . '_' . $champ) . '.html'))
    || ($fichier = find_in_path(
     ($controleur = 'controleurs/' . $champ) .'.html'))) {
     	if (!lire_fichier($fichier, $controldata)) die('erreur lecture controleur');
        if (preg_match_all('/\bname=(["\'])#ENV\{name_(\w+)\}\1/',
       					$controldata, $matches, PREG_PATTERN_ORDER)) {
	    	$champ = $matches[2];
    	}
    } else {
		$controleur = '';
    }

    $valeur = valeur_colonne_table($type, $champ, $id);
#spip_log("    $valeur = valeur_colonne_table($type, $champ, $id);");
#spip_log($champ);
	if ($valeur === false) {
	    return array("$type $id $champ: " . _U('crayons:pas_de_valeur'), 6);
    }
    if (is_scalar($valeur)) {
    	$valeur = array($champ => $valeur);
    }

    // type du crayon  (a revoir quand le core aura type ses donnees)
    $inputAttrs = array();
    if ($controleur) {
	    $options['hauteurMini'] = 80; // base de hauteur mini
        $option['inmode'] = 'controleur';
	    $options['controleur'] = $controleur;
    } elseif (
   preg_match(",[\n\r],", $valeur[$champ])  // si la valeur fait plusieurs lignes on doit mettre un textarea
    OR
	// on regarde le type tel que defini dans serial (attention il y avait des blob dans les vieux spip)
    ($sqltype = colonne_table($type, $champ)) &&
	   ( in_array($sqltype['type'] , array('mediumtext', 'longblob', 'longtext')) ||
	   (($sqltype['type'] == 'text' || $sqltype['type'] == 'blob') && in_array($champ, array('descriptif', 'bio'))))) {
	    $options['hauteurMini'] = 80; // hauteur mini d'un textarea
        $option['inmode'] = 'texte';
    } else { // ligne, hauteur naturelle
	    $options['hauteurMaxi'] = 0;
        $option['inmode'] = 'ligne';
        if ($sqltype['long']) {
        	$inputAttrs['maxlength'] = is_array($sqltype['long']) ?
        			$sqltype['long'][0] : $sqltype['long'];
        }
	}

    $crayon = new Crayon($nomcrayon, $valeur, $options);
	$inputAttrs['style'] = join($crayon->styles);

	if (!$controleur)
		$inputAttrs['style'] .= 'width:' . $crayon->largeur . 'px;' .
         ($crayon->hauteur ? ' height:' . $crayon->hauteur . 'px;' : '');

    $html = $controleur ? $crayon->formulaire(null, $inputAttrs) :
    				$crayon->formulaire($option['inmode'], $inputAttrs);
    $status = NULL;

    return array($html,$status);
}

// Definition des crayons
class Crayon {
	// le nom du crayon "type-modele-id" comme "article-introduction-237"
    var $name;
    // type, a priori une table, extrait du nom
    var $type;
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
    function Crayon($name, $texts = array(), $options = array()) {
        $this->name = $name;
    	list($this->type, $this->modele, $this->id) = explode('-', $this->name, 3);
    	if (is_scalar($texts)) {
    		$texts = array($this->modele => $texts);
    	}
        $this->texts = $texts;
        $this->key = strtr(uniqid('wid', true), '.', '_');
        $this->md5 = $this->md5();
        foreach ($options as $opt=>$val) {
        	$this->$opt = $val;
        }
		$this->dimension();
        $this->css();
    }

	// calcul du md5 associe aux valeurs
    function md5() {
        return md5(serialize($this->texts));
    }

	// dimensions indicatives
    function dimension() {
	    // largeur du crayon
	    $this->largeur = min(max(intval($_GET['w']),
	    			$this->largeurMini), $this->largeurMaxi);
	    // hauteur maxi d'un textarea selon wh: window height
	    $maxheight = min(max(intval($_GET['wh']) - 50, 400), $this->hauteurMaxi);
	    $this->hauteur = min(max(intval($_GET['h']), $this->hauteurMini), $maxheight);
    }

	// recuperer les elements de style
    function css() {
    	foreach(array('color', 'font-size', 'font-family', 'font-weight', 'line-height') as $property) {
    		if (null !== ($p = _request($property)))
    			$this->styles[] = "$property:$p;";
    	}
    	
    	$property = 'background-color';
		if (!$p = _request($property)
		OR $p == 'transparent') {
			$p = 'white';
		}
		$this->styles[] = "$property:$p;";
    }

	// formulaire standard
    function formulaire($contexte = array(), $inputAttrs = array()) {
    	include_spip('inc/filtres');
        return liens_absolus('<form class="formulaire_spip" method="post" action="' .
        	url_absolue(parametre_url(self(),'action', 'crayons_store')) . '">' .
        	$this->code() .
	        $this->input($contexte, $inputAttrs) .
        	$this->boutons() . // array('edit'=>'')
			'</form>');
    }

	// balises input type hidden d'identification du crayon
    function code() {
        return
         '<input type="hidden" class="crayon-id" name="crayons[]"'
        .' value="'.$this->key.'" />'."\n"
        . '<input type="hidden" name="name_'.$this->key
        .'" value="'.$this->name.'" />'."\n"
        . '<input type="hidden" name="class_' . $this->key
        . '" value="' . $this->class . '" />' . "\n"
        . '<input type="hidden" name="md5_'.$this->key
        .'" value="'.$this->md5.'" />'."\n"
        . '<input type="hidden" name="fields_'.$this->key
        .'" value="'.join(',',array_keys($this->texts)).'" />'
        ."\n"
        ;
    }

/*
 Fabriquer les balises des champs d'apres un modele controleurs/(type_)modele.html
	$contexte est un tableau (nom=>valeur) qui sera enrichi puis passe à recuperer_fond
*/
    function fond($contexte = array()) {
        include_spip('inc/filtres');
        $contexte['id_' . $this->type] = $this->id;
        $contexte['lang'] = $GLOBALS['spip_lang'];
        $contexte['key'] = $this->key;
        $contexte['largeur'] = $this->largeur;
        $contexte['hauteur'] = $this->hauteur;
        foreach ($this->texts as $champ => $val) {
	        $contexte['name_' . $champ] = 'content_' . $this->key . '_' . $champ;
        }
		$contexte['style'] = join(' ',$this->styles);
        include_spip('public/assembler');
        return recuperer_fond($this->controleur, $contexte);
    }

/*
 Fabriquer les balises du ou des champs
	$spec est soit un scalaire 'ligne' ou 'texte' précisant le type de balise
	soit un array($champ=>array('type'=>'...', 'attrs'=>array(attributs specifique du champs)))
	$attrs est un tableau (attr=>val) d'attributs communs ou pour le champs unique
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
                    $input = '
<textarea style="width:100%;" class="crayon-active"'
                    . ' name="content_'.$this->key.'_'.$champ.'">'
                    . entites_html($val)
                    . "</textarea>\n";
                    break;
                case 'ligne':
                default:
                    $input = '<input class="crayon-active" type="text"'
                    . ' name="content_'.$this->key.'_'.$champ.'"'
                    . ' value="'
                    . entites_html($val)
                    . '" />'."\n";
            }
            if (is_array($spec) && isset($spec[$champ]['attrs'])) {
	            foreach ($spec[$champ]['attrs'] as $attr=>$val) {
	                $input = inserer_attribut($input, $attr, $val);
	            }
            }

            foreach ($attrs as $attr=>$val) {
                $input = inserer_attribut($input, $attr, $val);
            }
            $return .= $input;
        }
        return $return;
    }

/*
 Fabriquer les boutons par defaut du crayon
	$boutons: tableau optionnel des boutons à poser (class=>array(img,texte[,url]))
	submit et cancel sont ajoutés par défaut, annullables comme 'cancel'=>''
	le + "editer tout" n'y est pas, au contraire on peut le mettre avec 'edit'=>''
*/
    function boutons($boutons = array()) {
        $crayonsImgPath = dirname(url_absolue(find_in_path('images/cancel.png')));
        if (!isset($boutons['submit'])) {
        	$boutons['submit'] = array('ok', texte_backend(_T('bouton_enregistrer')));
        }
        if (!isset($boutons['cancel'])) {
        	$boutons['cancel'] = array('cancel', texte_backend(_T('crayons:annuler')));
        }
        if (isset($boutons['edit']) && !$boutons['edit']) {
        	$boutons['edit'] = array('edit',
        		texte_backend(_T('crayons:editer_@type@_@id@',
        					array('type'=>$this->type, 'id'=>$this->id))),
        		"ecrire/?exec={$this->type}s_edit&amp;id_{$this->type}={$this->id}");
        }

        $html = '<div class="crayon-boutons"><div>';
        foreach ($boutons as $bnam => $bdef) if ($bdef) {
        	$html .= '<a class="crayon-' . $bnam .
        		'" title="' . $bdef[1] . '"';
        	if (!empty($bdef[2])) {
        		$html .= ' href="' . $bdef[2] . '"';
        	}
        	$html .= '><img src="' . $crayonsImgPath . '/' .
        		$bdef[0] . '.png" width="20" height="20" /></a>';
        }
        $html .= '</div></div>';
		return $html;
	}
}

//
// Un Crayon avec une verification de code de securite
//
class SecureCrayon extends Crayon {

    function SecureCrayon($name, $text='') {
        parent::Crayon($name, $text);
    }

    function code() {
        $code = parent::code();
        $secu = md5($GLOBALS['meta']['alea_ephemere']. '=' . $this->name);

        return
            $code
            .'<input type="hidden" name="secu_'.$this->key.'" value="'.$secu.'" />'."\n";
    }
}

function action_crayons_html_dist() {

    header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);

    // CONTROLEUR
    // on affiche le formulaire demande
    include_spip('inc/crayons');
	lang_select($GLOBALS['auteur_session']['lang']);
    $return = affiche_controleur($_GET['class']);

    echo var2js($return);
    exit;
}
?>
