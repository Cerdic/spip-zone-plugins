<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Classe Composant
 *
 * Chaque composant ACS peut définir une classe <MonComposant>
 * qui étend la classe Composant en définissant des méthodes de l'interface Icomposant
 * Ce sont des points d'entrée logiciels pour les objets de classe Composant,
 * un peu comparables aux pipelines spip, mais en technologie objet
 */

abstract class Composant implements Icomposant {
}

interface Icomposant {
  public function afterUpdate();
}

/**
 * Classe AdminComposant
 *
 * "Interface d'admin de composants pour ACS
 */

class AdminComposant {
  // Constructeur
  // Instancie (au besoin) un objet Composant à l'éxécution
  // pour en adopter les méthodes implémentées.
  function __construct($type, $nic=0, $debug = false) {
    global $_POST;
    
    include_spip('inc/xml'); // spip_xml_load()
    include_spip('inc/traduire');
    
    $this->debug = $debug;
    $this->type = $type; // Classe de composant (nom)
    $this->nic = $nic != 0 ? $nic : '';  // Numéro d'instance du composant
    $this->fullname = "acs".ucfirst($type).$this->nic;
    $this->errors = array();
    $this->vars = array();
    $this->cvars = array(); // Variables issues d'un autre composant
    $this->rootDir = find_in_path('composants/'.$type);// Dossier racine du composant
    $this->icon = $this->rootDir.'/images/'.$type.'_icon.gif';
    if (!is_readable($this->icon))
			$this->icon = _DIR_PLUGIN_ACS."/images/composant-24.gif";
		$this->optionnel = 'oui';
		$this->enable = true;

		// Lit les paramètres de configuration du composant
		$config = spip_xml_load($this->rootDir.'/ecrire/composant.xml');
		// Affecte ses paramètres de configuration à l'objet Composant
		$c = $config['composant'][0];
		$this->nom = $c['nom'][0];
		$this->version = $c['version'][0];
		
		// Les versions 1.9.2 de SPIP ne définissaient pas cette fonction introduite en 1.9.3dev puis en 2.0.0
		if (!is_callable('spip_xml_match_nodes'))
			include_spip('inc/backport_1.9.2');

		// Lit les dépendances (necessite)
		$this->necessite = array();
		if (spip_xml_match_nodes(',^necessite,',$c,$needs)){
		foreach(array_keys($c) as $tag){
			list($tag,$att) = spip_xml_decompose_tag($tag);
			if ($att) $this->necessite[] = $att;
			}
		}

		if (is_array($c['param'])) {
			foreach($c['param'] as $param) {
			  if (is_array($param) && is_array($param['nom']) && is_array($param['valeur']))
					$this->$param['nom'][0] = $param['valeur'][0];
			  else
					$this->$param['nom'][0] = true;
			}
		}
		
		// Active le composant non optionnel
		if (($this->optionnel=='non') || ($this->optionnel =='no') || ($this->optionnel =='false')) {
			ecrire_meta($this->fullname.'Use','oui');
			$this->enable = true;
			$updated = true;
		}
		else { // Regarde si le composant optionnel doit être activé
			// Désactive le composant s'il dépend de plugins non activés
			if (strpos($this->optionnel, 'plugin') === 0) {
			  $plugins_requis = explode(' ', substr($this->optionnel, 7));
			  foreach ($plugins_requis as $plug) {
					$plug = strtoupper(trim($plug));
					if (!acs_get_from_active_plugin($plug)) {
						$this->enable = false;
					}
			  }
			}
			// Désactive le composant si "optionnel" est égal à une variable de configuration non égale à "oui"
			// (si optionnel ne vaut pas oui, yes, ou true, il s'agit d'un nom de variable meta)
			elseif (isset($this->optionnel) && 
						   ($this->optionnel != 'oui') && 
						   ($this->optionnel != 'yes') && 
						   ($this->optionnel != 'true') && 
						   ($GLOBALS['meta'][$this->optionnel] != 'oui') 
						 ) {
			  ecrire_meta($this->fullname.'Use','non');
			  $this->enable = false;
			  $updated = true;
			}
		}
		
		$this->vars[0] = array('nom' => 'Use',
												   'valeur' => $GLOBALS['meta'][$this->fullname.'Use']
												  );
		if (is_array($c['variable'])) {
			foreach($c['variable'] as $k=>$var) {
				foreach($var as $varname=>$value) {
					if (count($value) > 1)
						  $v = $value;
					else
						$v = $value[0];
					if ($this->debug)
						$this->errors['vars'] .= $type.($nic ? '#'.$nic : '').'->vars['.$k.']['.$varname.'] = '.htmlentities((is_array($v) ? 'Array('.implode($v, ', ').')' : $v))."<br />\n"; // dbg composant
					$this->vars[$k+1][$varname] = $v;
					if ($varname == 'valeur') { // Default values
						if (substr($v,0,3) == 'acs') {
							if (!in_array($v, $this->cvars)) $this->cvars[ ] = $v;
							$v = $GLOBALS['meta'][$v];
						}
					}
				}
			}
		}
		
		// Mise à jour
		if ($this->debug && $_POST) {
			include_spip('balise/acs_balises');
			$this->errors[] = "\n<br />\$_POST=\n";
			$this->errors[] = dbg($_POST);
			$this->errors[] = "\n<br />\n";
		}
		if (_request('maj_composant')=='oui') {
			foreach ($this->vars as $var) {
				$v = $this->fullname.$var['nom'];
				switch($var['type']) {
			  	case 'bord' :
			  		$v2u = array('Width', 'Style', 'Color');
						$couleur = $GLOBALS['meta'][$v.'Color'];
						// Valeurs par defaut pour un Bord herite d'un autre composant 
						if (substr($couleur, 0, 1) == '=') {
							$vb = substr($couleur, 1, -5);
							if ($_POST[$v.'Width_'.md5($this->fullname)] === '') {
								ecrire_meta($v.'Width', '='.$vb.'Width');
								$updated = true;
							}
						  if ($_POST[$v.'Style_'.md5($this->fullname)] === '') {
								ecrire_meta($v.'Style', '='.$vb.'Style');
								$updated = true;
							}
						}
			  		break;
			  	default:
			  		$v2u = array('');
				}
			  foreach($v2u as $w) {
			  	$w = $v.$w;
					$posted = $w.'_'.md5($this->fullname);
					if (!isset($_POST[$posted])) continue; // on ne traite surtout pas les variables non postées
					if ($_POST[$posted] == $GLOBALS['meta'][$v]) continue;
					if ($_POST[$posted] === '') {
						if (isset($var['valeur'])) { // Valeur par defaut - default value
							if (substr($var['valeur'], 0, 3) == 'acs')
								$nv = $GLOBALS['meta'][$var['valeur']];
							else
								$nv = $var['valeur'];
						}
					}
					else
						$nv = $_POST[$posted];
					ecrire_meta($w, $nv);
					unset($nv);
					$updated = true;
			  }
			}

			if (isset($updated)) {
			  if (isset($this->afterUpdate)) {
					@include_once($this->rootDir.'/ecrire/'.$type.'.php');
					$cObj = 'acs'.ucfirst($type);
					if(class_exists($cObj)) {
						$$cObj = @new $cObj();
						if (($$cObj instanceof Composant) && is_callable(array($$cObj, 'afterUpdate'))) {
						  if (!$$cObj->afterUpdate())
								$this->errors[] = $cObj.'->afterUpdate '._T('acs:failed').' '.implode(' ', $$cObj->errors);
						}
						else
						  $this->errors[] = $cObj.'->afterUpdate '._T('acs:not_callable');
					}
						else
						  $this->errors[] = $cObj.'->afterUpdate '._T('acs:not_found');
			  }
			  ecrire_meta("acsDerniereModif", time());
			  ecrire_metas(); // SPIP ecrit en BDD
			  lire_metas(); // SPIP relit toutes les metas en BDD
			  touch_meta(false); // Force la reecriture du cache SPIP des metas
			  unset($updated);
			}
		}
  }

/**
 * Méthode getcvars: retourne du code html pour les variables du composant
 * faisant référence à une variable définie par un autre composant
 * pour leurs valeurs par défaut
 */
  function get_cvars_html() {
		foreach($this->cvars as $k =>$var) {
			if (!isset($GLOBALS['meta'][$var]))
			  $class = ' alert';
			else
			  $class = '';
			$this->cvars[$k] = '<a class="nompage'.$class.'" title="'.$GLOBALS['meta'][$var].'">'.substr($this->cvars[$k], 3).'</a>';
		}
		return implode(', ', $this->cvars);
  }

  function T($string) {
		return _T('acs:'.$this->type.'_'.$string);
  }
  
/**
 * Méthode gauche: affiche la colonne gauche dans spip admin
 * @return html code
 */
  function gauche() {
		global $spip_version_code;

		if ($this->T('description') != $this->type.' description')
			$r .= '<div>'.$this->T('description').'</div><br />';

		if ($this->T('info') != $this->type.' info')
			$r .= '<div class="onlinehelp" style="text-align: justify">'.$this->T('info').'</div><br />';
			
		if ($this->T('help') != $this->type.' help')
			$r .= '<div class="onlinehelp" onclick=\'$("#help_context").slideToggle("slow");\' style="cursor:pointer;"><img src="'._DIR_PLUGIN_ACS.'images/aide.gif" onmouseover=\'$("#help_context").slideToggle("slow");\' /> '._T('icone_aide_ligne').'</div><div id="help_context" class="onlinehelp pliable" style="text-align: justify">'.$this->T('help').'</div><br />';

		$n = 999;
		$r .= '<div class="onlinehelp">'.acs_plieur('plieur_pu'.$n, 'pu'.$n, '#', false, 'if (typeof done'.$n.' == \'undefined\') {AjaxSqueeze(\'?exec=composant_get_infos&c='.$this->type.($this->nic ? '&nic='.$this->nic: '').'\',\'puAjax'.$n.'\'); done'.$n.' = true;}', _T('acs:dev_infos') ).'</div><div class="pu'.$n.' pliable">';
		if (count($this->cvars))
			$r .= '<br /><div class="onlinehelp">'._T('acs:references_autres_composants').'</div>'.
						'<div class="onlinehelplayer">'.$this->get_cvars_html().'</div>';
		$r .= '<div id="puAjax'.$n.'" class="puAjax'.$n.'"></div>';
		$r .= '<div>'._T('version').' '.$this->type.' <b>'.(($this->version != ACS_VERSION) ? '<span class="alert">'.$this->version.'</span>' : $this->version).'</b></div>';
		/*
		if ($spip_version_code < $this->version_spip_min)
			$r .= '<div class="alert">'._T('acs:spip_trop_ancien', array('min' => spip_version_texte($this->version_spip_min))).'</div>';
		elseif ($spip_version_code > $this->version_spip_max)
			$r .= '<div class="alert">'._T('acs:spip_non_supporte', array('max' => spip_version_texte($this->version_spip_max))).'</div>';
*/
		$r .= '</div>';
		return $r;
  }

/**
 * Méthode edit: affiche un editeur pour les variables du composant
 * @param mode : mode d'affichage (espace prive ou controleur) 
 * @return html code
 */
  function edit($mode=false) {
		include_spip('public/assembler');
  	include_spip('lib/composant/controles');
		$r = '<script type="text/javascript" src="'._DIR_PLUGIN_ACS.'lib/picker/picker.js"></script>';
		$r .= "<input type='hidden' name='maj_composant' value='oui' />".
					'<input type="hidden" name="composant" value="'.$this->type.'" />'.
					'<input type="hidden" name="nic" value="'.$this->nic.'" />';

		$varconf = $this->fullname.'Config';
		if (($mode != 'controleur') && ($this->optionnel!='non') && ($this->optionnel!='no') && ($this->optionnel!='false')) {
			$varname = $this->fullname.'Use';
			if (isset($GLOBALS['meta'][$varname]) && $GLOBALS['meta'][$varname])
			  $var = $GLOBALS['meta'][$varname];
			else
			  $var = 'non';
			if ($var == "oui")
			  $this->display = "display: block;";
			else
			  $this->display = "display: none;";
			$varname .= '_'.md5($this->fullname);
			$nc = $this->T('nom');
			if ($nc==str_replace('_', ' ', $this->T('nom')))
				$nc = ucfirst($this->nom);
			$r .= '<div align="'.$GLOBALS['spip_lang_right'].'" style ="font-weight: normal"><label>'._T('acs:use').' '.$nc.' '.$this->nic.' : </label>';
			// acs_bouton_radio($nom, $valeur, $titre, $actif = false, $onClick="", $enable=true)
			$r .= acs_bouton_radio($varname, "oui", _T('item_oui'), $var == "oui", "changeVisible(this.checked, '$varconf', 'block', 'none');",$this->enable);
			$r .= acs_bouton_radio($varname, "non", _T('item_non'), $var == "non", "changeVisible(this.checked, '$varconf', 'none', 'block');",$this->enable);
			$r .= '</div>';
		}

		$r .= '<div id="'.$varconf.'" '.(isset($this->display) ? 'style="'.$this->display.'"' : '').'>';
		if (($mode != 'controleur') && isset($this->preview) && ($this->preview != 'non')  && ($this->preview != 'no') && ($this->preview != 'false')) {
			$url = '../?page=wrap&c=composants/'.$this->type.'/'.$this->type.'&v='.$GLOBALS['meta']['acsDerniereModif'].'&var_mode=recalcul';
			$r .= '<fieldset class="apercu"><legend><a href="javascript:void(0)" onclick=" findObj(\''.$this->fullname.'\').src=\''.$url.'\';" title="'._T('admin_recalculer').'">'._T('previsualisation').'</a></legend><iframe id="'.$this->fullname.'" width="100%" height="'.(is_numeric($this->preview) ? $this->preview : 80).'px" frameborder="0" style="border:0; background:'.$GLOBALS['meta']['acsFondColor'].'" src="'.$url.'"></iframe></fieldset>';
		}
		// Affiche les variables paramétrables du composant:
		$controls = array();
		foreach ($this->vars as $var) {
			if ($var['nom'] === 'Use')
			  continue;
			$v = $var['nom'];
			if (isset($GLOBALS['meta'][$this->fullname.$v]))
			  $$v = $GLOBALS['meta'][$this->fullname.$v];
			elseif (isset($var['valeur'])) {
			  $default = $var['valeur'];
			  if ((substr($default,0,3) =='acs') && isset($GLOBALS['meta'][$default]))
					$$v = $GLOBALS['meta'][$default];
			  elseif (substr($default,0,3) !='acs')
					$$v = $default;
			}
			$draw = 'ctl'.ucfirst($var['type']);
			if (is_callable($draw)) {
			  $controls[$var['nom']] = $draw($this->type, $this->nic, $v, $$v, $var, md5($this->fullname));
			}
			else $controls[$var['nom']] = $draw."() undefined.<br />" ;
		}

		// Recherche une mise en page et y remplace les variables par des contrôles
		$mis_en_page = array();
		if (is_readable($this->rootDir.'/ecrire/'.$this->type.'_mep.html')) {
			$mep .= recuperer_fond('composants/'.$this->type.'/ecrire/'.$this->type.'_mep', array('lang' => $GLOBALS['spip_lang']));
			foreach ($controls as $nom=>$html) {
			  $tag = '&'.$nom.'&';
			  if (strpos($mep, $tag) !== false)
			  	$mis_en_page[] = $nom;
			  $mep = str_replace($tag, $html, $mep);
			}
			if ($mode=='controleur')
				$mep = preg_replace('%<admin>(.*?)</admin>%s', '', $mep);
		}
		// Ajoute les contrôles non mis en page
		foreach ($controls as $nom=>$html) {
			if (!in_array($nom, $mis_en_page)) $r.= $html;
		}

		if (isset($mep)) $r .= '<div align="'.$GLOBALS['spip_lang_right'].'">'.$mep.'</div>';
		$r .= '</div><table width="100%"><tr><td>';

		if (count($this->errors))
			$r .= '<div class="alert">'.implode('<br />', $this->errors).'</div>';
		$r .= '</td>';
		$r .= '</tr></table>';
		return $r;
  }
/**
 * Méthode page: retourne un tableau des pages qui utilisent ce composant
 */
  function pages() {
		include_once(_DIR_PLUGIN_ACS.'lib/cGetPages.php');
		return cGetPages($this->type);
  }
}

?>