<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function affiche_controleur($class) {
  $return = array('$erreur'=>'');

  if (preg_match(_PREG_WIDGET, $class, $regs)) {
    list(,$widget,$type,$champ,$id) = $regs;
    include_spip('inc/autoriser');
    if (!autoriser('modifier',$type, $id, NULL, array('champ'=>$champ))) {
        $return['$erreur'] = "$type $id: " . _U('widgets:non_autorise');
    } else {
        $f = charger_fonction($type.'_'.$champ, 'controleurs', true)
        OR $f = charger_fonction($champ, 'controleurs', true)
        OR $f = 'controleur_dist';
        list($html,$status) = $f($regs);
        if ($status) {
            $return['$erreur'] = $html;
        } else {
            $return['$html'] = $html;
        }
    }
  } else {
    $return['$erreur'] = _U('widgets:donnees_mal_formatees');
  }
  return $return;
}

function controleur_dist($regs) {
    list(,$widget,$type,$champ,$id) = $regs;
    $valeur = valeur_colonne_table($type, $champ, $id);
    if ($valeur === false) {
	    return array("$type $id $champ: " . _U('widgets:pas_de_valeur'), 6);
    }

    $options = array();
    // type du widget
    if (in_array($champ, array('chapo', 'texte', 'descriptif', 'ps'))) {
	    $options['hauteurMini'] = 36; // hauteur mini d'un textarea
        $mode = 'texte';
    } else { // ligne, hauteur naturelle
	    $options['hauteurMaxi'] = 0;
        $mode = 'ligne';
	}
    $n = new Widget($widget, array($champ => $valeur), $options);

    $inputAttrs = array(
        'style' => 'width:' . $n->largeur . 'px;' .
         ($n->hauteur ? ' height:' . $n->hauteur . 'px;' : ''));

    $html = $n->formulaire($mode, $inputAttrs);
    $status = NULL;

    return array($html,$status);
}

// Definition des widgets
class Widget {
	// le nom du widget "type-modele-id" comme "article-introduction-237"
    var $name;
    // type, a priori une table, extrait du nom
    var $type;
    // modele, un champ comme "texte" ou un modele, extrait du nom
    var $modele;
    // l'identificateur dans le type, comme un numero d'article
    var $id;
    // la ou les valeurs des champs du widget, tableau associatif champ => valeur
    var $texts = array();
    // une cle unique pour chaque widget demande
    var $key;
    // un md5 associe aux valeurs pour verifier et detecter si elles changent
    var $md5;
	// dimensions indicatives
    var $largeurMini = 100;
    var $largeurMaxi = 700;
    var $hauteurMini = 36;
    var $hauteurMaxi = 700;

	// le constructeur du widget
	// $name : son nom
	// $texts : tableau associatif des valeurs ou valeur unique si widget monochamp
	// $options : options directes du widget (développement)
    function Widget($name, $texts = array(), $options = array()) {
        $this->name = $name;
    	list($this->type, $this->modele, $this->id) = explode('-', $this->name);
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
    }

	// calcul du md5 associe aux valeurs
    function md5() {
        return md5(serialize($this->texts));
    }

	// dimensions indicatives
    function dimension() {
	    // largeur du widget
	    $this->largeur = min(max(intval($_GET['w']),
	    			$this->largeurMini), $this->largeurMaxi);
	    // hauteur maxi d'un textarea selon wh: window height
	    $maxheight = min(max(intval($_GET['wh']) - 50, 400), $this->hauteurMaxi);
	    $this->hauteur = min(max(intval($_GET['h']), $this->hauteurMini), $maxheight);
    }

	// formulaire standard
    function formulaire($contexte = null, $inputAttrs = array()) {
        return '<form method="post" action="' .
        	str_replace('widgets_html', 'widgets_store', self()) . '">' .
        	$this->code() .
        	(($widgetsInput = $this->fond($contexte)) ? $widgetsInput :
		        $this->input($contexte, $inputAttrs)) .
        	$this->boutons() . // array('edit'=>'')
			'</form>';
    }

	// balises input type hidden d'identification du widget
    function code() {
        return
         '<input type="hidden" class="widget-id" name="widgets[]"'
        .' value="'.$this->key.'" />'."\n"
        . '<input type="hidden" name="name_'.$this->key
        .'" value="'.$this->name.'" />'."\n"
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
	    if (!find_in_path(
	     ($fond = 'controleurs/' . $this->type . '_' . $this->modele) . '.html')
	    && !find_in_path(
	     ($fond = 'controleurs/' . $this->modele) .'.html')) {
			return '';
	    }
        include_spip('inc/filtres');
        $contexte['id_' . $this->type] = $this->id;
        $contexte['lang'] = $GLOBALS['spip_lang'];
        $contexte['key'] = $this->key;
        $contexte['largeur'] = $this->largeur;
        $contexte['hauteur'] = $this->hauteur;
        foreach ($this->texts as $champ => $val) {
	        $contexte['name_' . $champ] = 'content_' . $this->key . '_' . $champ;
        }
        include_spip('public/assembler');
        return recuperer_fond($fond, $contexte);
    }

/*
 Fabriquer les balises du ou des champs
	$spec est soit un scalaire 'ligne' ou 'texte' précisant le type de balise
	soit un array($champ=>array('type'=>'...', 'attrs'=>array(attributs specifique du champs)))
	$attrs est un tableau (attr=>val) d'attributs communs ou pour le champs unique
*/
    function input($spec = 'ligne', $attrs = array()) {
        include_spip('inc/filtres');
        $return = '';
        foreach ($this->texts as $champ => $val) {
        	$type = is_array($spec) ? $spec[$champ]['type'] : $spec;
            switch ($type) {
                case 'texte':
                    $input = '<textarea class="widget-active"'
                    . ' name="content_'.$this->key.'_'.$champ.'">'
                    . entites_html($val)
                    . '</textarea>'."\n";
                    break;
                case 'ligne':
                default:
                    $input = '<input class="widget-active" type="text"'
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
 Fabriquer les boutons par defaut du widget
	$boutons: tableau optionnel des boutons à poser (class=>array(img,texte[,url]))
	submit et cancel sont ajoutés par défaut, annullables comme 'cancel'=>''
	le + "editer tout" n'y est pas, au contraire on peut le mettre avec 'edit'=>''
*/
    function boutons($boutons = array('ok', 'cancel')) {
        $widgetsImgPath = dirname(find_in_path('images/cancel.png'));
        if (!isset($boutons['ok'])) {
        	$boutons['submit'] = array('ok', texte_backend(_T('bouton_enregistrer')));
        }
        if (!isset($boutons['cancel'])) {
        	$boutons['cancel'] = array('cancel', texte_backend(_T('widgets:annuler')));
        }
        if (isset($boutons['edit']) && !$boutons['edit']) {
        	$boutons['edit'] = array('edit',
        		texte_backend(_T('widgets:editer_@type@_@id@',
        					array('type'=>$this->type, 'id'=>$this->id))),
        		"ecrire/?exec={$this->type}s_edit&amp;id_{$this->type}={$this->id}");
        }

        $html = '<div class="widget-boutons"><div>';
        foreach ($boutons as $bnam => $bdef) if ($bdef) {
        	$html .= '<a class="widget-' . $bnam .
        		'" title="' . $bdef[1] . '"';
        	if (!empty($bdef[2])) {
        		$html .= ' href="' . $bdef[2] . '"';
        	}
        	$html .= '><img src="' . $widgetsImgPath . '/' .
        		$bdef[0] . '.png" width="20" height="20" /></a>';
        }
        $html .= '</div></div>';
		return $html;
	}
}

//
// Un Widget avec une verification de code de securite
//
class SecureWidget extends Widget {

    function SecureWidget($name, $text='') {
        parent::Widget($name, $text);
    }

    function code() {
        $code = parent::code();
        $secu = md5($GLOBALS['meta']['alea_ephemere']. '=' . $this->name);

        return
            $code
            .'<input type="hidden" name="secu_'.$this->key.'" value="'.$secu.'" />'."\n";
    }
}

function action_widgets_html_dist() {

    header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);

    // CONTROLEUR
    // on affiche le formulaire demande
    include_spip('inc/widgets');
    $return = affiche_controleur($_GET['class']);

    echo var2js($return);
    exit;
}
?>
