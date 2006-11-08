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

    // type du widget
    if (in_array($champ,
    array('chapo', 'texte', 'descriptif', 'ps')))
        $mode = 'texte';
    else
        $mode = 'ligne';

    // taille du widget
    $w = intval($_GET['w']);
    $h = intval($_GET['h']);
    $wh = intval($_GET['wh']); // window height
    if ($w<100) $w=100;
    if ($w>700) $w=700;
    if ($mode == 'texte') {
        if ($h<36) $h=36; #ici on pourrait mettre minimum 3*$_GET['em']
    }
    else // ligne, hauteur naturelle
        $h='';#$hx = htmlspecialchars($_GET['em']);

    // hauteur maxi d'un textarea -- pas assez ? trop ?
    $maxheight = min(max($wh-50,400), 700);
    if ($h>$maxheight) $h=$maxheight;

    $inputAttrs = array(
        'style' => "width:${w}px;" . ($h ? " height:${h}px;" : ''));

    $valeur = valeur_colonne_table($type, $champ, $id);
    if ($valeur !== false) {
        $n = new Widget($widget, array($champ => $valeur));
        $widgetsAction = str_replace('widgets_html', 'widgets_store', self());
        $widgetsCode = $n->code();
        $widgetsInput = $n->input($mode, $inputAttrs);
        $widgetsImgPath = dirname(find_in_path('images/cancel.png'));

        // title des boutons
        $OK = texte_backend(_T('bouton_enregistrer'));
        $Cancel = texte_backend(_L('Annuler'));
        $Editer = texte_backend(_L("&Eacute;diter $type $id"));
        $url_edit = "ecrire/?exec={$type}s_edit&amp;id_{$type}=$id";

        $html =
        <<<FIN_FORM

<form method="post" action="{$widgetsAction}">
  {$widgetsCode}
  {$widgetsInput}
  <div class="widget-boutons">
  <div>
    <a class="widget-submit" title="{$OK}">
      <img src="{$widgetsImgPath}/ok.png" width="20" height="20" />
    </a>
    <a class="widget-cancel" title="{$Cancel}">
      <img src="{$widgetsImgPath}/cancel.png" width="20" height="20" />
    </a>
    <a href="{$url_edit}" title="{$Editer}" class="widget-full">
      <img src="{$widgetsImgPath}/edit.png" width="20" height="20" />
    </a>
  </div>
</div>
</form>

FIN_FORM;
        $status = NULL;

    }
    else {
        $html = "$type $id $champ: " . _U('widgets:pas_de_valeur');
        $status = 6;
    }

    return array($html,$status);
}

// Definition des widgets
class Widget {
    var $name;
    var $texts = array();
    var $key;
    var $md5;
    var $clean;

    function Widget($name, $texts = array()) {
        $this->name = $name;
        $this->texts = $texts;
        $this->key = strtr(uniqid('wid', true), '.', '_');
    }

    function md5() {
        return md5(serialize($this->texts));
    }

    function code() {
        return
         '<input type="hidden" class="widget-id" name="widgets[]"'
        .' value="'.$this->key.'" />'."\n"
        . '<input type="hidden" name="name_'.$this->key
        .'" value="'.$this->name.'" />'."\n"
        . '<input type="hidden" name="md5_'.$this->key
        .'" value="'.$this->md5().'" />'."\n"
        . '<input type="hidden" name="fields_'.$this->key
        .'" value="'.join(',',array_keys($this->texts)).'" />'
        ."\n"
        ;
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
