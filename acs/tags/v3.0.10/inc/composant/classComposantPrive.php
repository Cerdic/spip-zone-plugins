<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Classe CEdit
 *
 * Chaque composant ACS peut définir une classe <MonComposant>
 * qui étend la classe CEdit en définissant des méthodes de l'interface ICEdit
 * Ce sont des points d'entrée logiciels pour les objets de classe CEdit,
 * un peu comparables aux pipelines spip, mais en technologie objet explicite
 */

abstract class CEdit implements ICEdit {
}

interface ICEdit {
	public function update();
}

/**
 * Classe AdminComposant
 *
 * Interface d'admin de composants pour ACS
 */
class AdminComposant {
	/**
	 * Constructeur
	 * Instancie (au besoin) un objet CEdit à l'exécution
	 * pour en adopter les méthodes implémentées.
	 * @param $class classe du composant
	 * @param $nic numero d'instance du composant
	 * @param $debug mode debug
	 */
	function __construct($class, $nic=0, $debug = false) {
		global $_POST;
		
		include_spip('inc/xml'); // spip_xml_load()
		include_spip('inc/traduire');
		include_spip('inc/acs_version');
		
		$this->debug = $debug;
		$this->class = $class; // Classe de composant (nom)
		$this->nic = $nic != 0 ? $nic : '';	// Numéro d'instance du composant
		$this->fullname = "acs".ucfirst($class).$this->nic;
		$this->errors = array();
		$this->vars = array();
		$this->cvars = array(); // Variables issues d'un autre composant
		$this->necessite = array(); // Dependances du composant (plugins et composants)
		$this->nb_widgets = 0; // Nb de variables de type widget
		$this->rootDir = find_in_path('composants/'.$class);// Dossier racine du composant
		$this->icon = $this->rootDir.'/images/'.$class.'_icon.gif';
		if (!is_readable($this->icon))
			$this->icon = _DIR_PLUGIN_ACS."/images/composant-24.gif";
		$this->optionnel = 'oui';
		$this->enable = true;

		// Lit les paramètres de configuration du composant, en tenant compte d'un overide eventuel
		if (is_readable($this->rootDir.'/ecrire/composant.xml'))
			$config = spip_xml_load($this->rootDir.'/ecrire/composant.xml');
		else
			$config = spip_xml_load(find_in_path('composants/'.$class.'/ecrire/composant.xml'));
		// Affecte ses paramètres de configuration à l'objet Composant
		$c = $config['composant'][0];
		$this->nom = $c['nom'][0];
		$this->version = $c['version'][0];
		$this->group = $c['group'][0];

		// Lit les dépendances (plugins: necessite, composants: necessite_composant)
		if (spip_xml_match_nodes(',^necessite,',$c,$needs)){
			foreach(array_keys($c) as $tag){
				list($tag,$att) = spip_xml_decompose_tag($tag);
				if ($att) $this->necessite[($att['set'] ? 'composant':'plugin')][] = $att;
			}
			krsort($this->necessite); // on met les plugins avant les composants
		}

		if (is_array($c['param'])) {
			foreach($c['param'] as $param) {
				if (is_array($param) && is_array($param['nom']) && is_array($param['valeur']))
					$this->$param['nom'][0] = $param['valeur'][0];
				else
					$this->$param['nom'][0] = true;
			}
		}
		
		// Active le composant non optionnel, si nécessaire
		if (($this->optionnel=='non') || ($this->optionnel =='no') || ($this->optionnel =='false')) {
			if ($GLOBALS['meta'][$this->fullname.'Use'] != 'oui') {
				ecrire_meta($this->fullname.'Use','oui');
				$this->enable = true;
				$updated = true;
			}
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

		$this->vars[0] = array('nom' => 'Use', 'valeur' => $GLOBALS['meta'][$this->fullname.'Use']);

		if (is_array($c['variable'])) {
			foreach($c['variable'] as $k=>$var) {
				if (!is_array($var))
					continue; // Peut se produire en cas d'erreur dans composant.xml
				foreach($var as $varname=>$value) {
					if (count($value) > 1)
							$v = $value;
					else
						$v = $value[0];
					if ($this->debug)
						$this->errors['vars'] .= $class.($nic ? '#'.$nic : '').'->vars['.$k.']['.$varname.'] = '.htmlentities((is_array($v) ? 'Array('.implode($v, ', ').')' : $v))."<br />\n"; // dbg composant
					$this->vars[$k+1][$varname] = $v;
					if (($varname == 'type') && ($v == 'widget'))
						$this->nb_widgets++;
					if ($varname == 'valeur') { // Default values
						if (substr($v,0,4) == '=acs') {
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
			$this->errors[] = nl2br(dbg($_POST));
			$this->errors[] = "\n<br />\n";
		}
		// Suppression de l'instance du composant
		if (_request('del_composant')=='delete') {
			foreach ($this->vars as $var) {
				$v = $this->fullname.$var['nom'];
				effacer_meta($v);
			}
			$updated = true;
		}
		// Mise à jour
		if (_request('maj_composant')=='oui') {
			$md5 = md5($this->fullname);
			foreach ($this->vars as $var) {
				unset($nv);
				$v = $this->fullname.$var['nom'];
				switch($var['type']) {
					case 'bord' :
						// on ne traite surtout pas les variables non postées
						if (!isset($_POST[$v.'Color_'.$md5])) continue 2;

						if ($_POST[$v.'Color_'.$md5] === '') {
							$nv = '';
						}
						else {
							$postedColor = $_POST[$v.'Color_'.$md5];
							// si la valeur postee commence par "=", c'est une référence à la valeur d'un autre composant
							if (substr($postedColor, 0, 1) == '=')
								$nv = $postedColor;
							else
								$nv = array('Width' => $_POST[$v.'Width_'.$md5],
														'Style' => $_POST[$v.'Style_'.$md5],
														'Color' => $_POST[$v.'Color_'.$md5]
											);
						}
						if (is_array($nv))
							$nv = serialize($nv);
						break;
						
					case 'key' :
						$postedGroup = $_POST[$v.'Group_'.$md5];
						$postedKey = $_POST[$v.'Key_'.$md5];
						$nv = serialize(array('Group' => $postedGroup, 'Key' => $postedKey));
						break;
						
					default:
						// on ne traite surtout pas les variables non postées
						if (!isset($_POST[$v.'_'.$md5]))
							continue 2;

						$nv = $_POST[$v.'_'.$md5];
				}

				// On continue si rien à faire
				if ($nv == $GLOBALS['meta'][$v])
					continue;

				// Si la nouvelle valeur est vide
				if ($nv === '') {
					// et que la variable n'a jamais été initialisée,
					if (isset($var['valeur']) && !isset($GLOBALS['meta'][$v])) {
						// on lui affecte la valeur par défaut
						if (substr($var['valeur'], 0, 4) == '=acs')
							$nv = $GLOBALS['meta'][$var['valeur']];
						else
							$nv = $var['valeur'];
					}
				}
				ecrire_meta($v, $nv);
				$updated = true;
			}
		}
		if (isset($updated)) {
			if (isset($this->update)) {
				include_spip('composants/'.$class.'/ecrire/'.$class);
				$cObj = 'acs'.ucfirst($class).'Edit';
				if(class_exists($cObj)) {
					$$cObj = new $cObj();
					if (($$cObj instanceof CEdit) && is_callable(array($$cObj, 'update'))) {
						if (!$$cObj->update())
							$this->errors[] = $cObj.'->update '._T('acs:failed').' '.implode(' ', $$cObj->errors);
					}
					else
						$this->errors[] = $cObj.'->update '._T('acs:not_callable');
				}
					else
						$this->errors[] = $cObj.'->update '._T('acs:not_found');
			}
			ecrire_meta("acsDerniereModif", time());
			ecrire_metas(); // SPIP ecrit en BDD
			lire_metas(); // SPIP relit toutes les metas en BDD
			touch_meta(false); // Force la reecriture du cache SPIP des metas
			unset($updated);
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
		$s = $this->class.'_'.$string;
		$t = _T('acs:'.$s);

		// On retourne la chaine si elle n'a pas été traduite (test pré-SPIP 3) :
		if ($t == str_replace('_', ' ', $s))
			return $string;

		// On s'adapte à SPIP 3 svn :
		if (substr($t, 0, 12) == '<blink style')
			return $string;

		// On retourne la traduction
		return $t;
	}
	
/**
 * Méthode gauche: affiche la colonne gauche dans spip admin
 * @return html code
 */
	function gauche() {
		global $spip_version_code;

		if ($this->T('description') != 'description')
			$r .= '<div>'.$this->T('description').'</div><br />';

		if ($this->T('info') != 'info')
			$r .= '<div class="onlinehelp" style="text-align: justify">'.$this->T('info').'</div><br />';

		$n = 999;
		$r .= '<div class="onlinehelp">'.
		acs_plieur('plieur_pu'.$n,
			'pu'.$n,
			'#plieur_pu'.$n,
			true,
			'if (typeof done'.$n.' == \'undefined\') {
				AjaxSqueeze(\'?exec=composant_get_infos&c='.$this->class.($this->nic ? '&nic='.$this->nic: '').'\', \'puAjax'.$n.'\');
				done'.$n.' = true;
			}',
			_T('acs:dev_infos')
		).
		'</div>
		<div class="pu'.$n.'">';
		
		if (count($this->cvars))
			$r .= '<br /><div class="onlinehelp">'._T('acs:references_autres_composants').'</div>'.
						'<div class="onlinehelplayer">'.$this->get_cvars_html().'</div>';
		$r .= '<div id="puAjax'.$n.'" class="puAjax'.$n.'"></div>';
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
		include_spip('inc/composant/classControles');
		$r = '<script type="text/javascript" src="'._DIR_PLUGIN_ACS.'inc/picker/picker.js"></script>';
		$r .= "<input type='hidden' name='maj_composant' value='oui' />".
					'<input type="hidden" name="composant" value="'.$this->class.'" />'.
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
			$r .= acs_bouton_radio($varname, "oui", _T('acs:oui'), $var == "oui", "changeVisible(this.checked, '$varconf', 'block', 'none');",$this->enable);
			$r .= acs_bouton_radio($varname, "non", _T('acs:non'), $var == "non", "changeVisible(this.checked, '$varconf', 'none', 'block');",$this->enable);
			$r .= '</div>';
		}

		$r .= '<div id="'.$varconf.'" '.(isset($this->display) ? 'style="'.$this->display.'"' : '').' class="c_config">';
		if (($mode != 'controleur') && isset($this->preview) && ($this->preview != 'non')	&& ($this->preview != 'no') && ($this->preview != 'false')) {
			$url = '../?page=wrap&c=composants/'.$this->class.'/'.$this->class.($this->nic ? '&amp;nic='.$this->nic : '').'&v='.$GLOBALS['meta']['acsDerniereModif'].'&var_mode=recalcul';
		switch($this->preview_type) {
			case 'inline':
				 require_once _DIR_ACS.'balise/acs_balises.php';
				 $preview = '<script type="text/javascript" src="../spip.php?page=acs.js"></script><link rel="stylesheet" href="../spip.php?page='.$GLOBALS['acsSet'].'.css" type="text/css" media="projection, screen, tv" /><div id="'.$this->fullname.'" style="border:0;overflow: auto; width: 100%; height: '.(is_numeric($this->preview) ? $this->preview : 80).'px">'.recuperer_fond('vues/composant', array(
				 'c' => 'composants/'.$this->class.'/'.$this->class,
				 'nic' => $this->nic,
				 'lang' => $GLOBALS['spip_lang']
				 )).'</div>';
				 break;
			 default:
				 $preview = '<iframe id="'.$this->fullname.'" width="100%" height="'.(is_numeric($this->preview) ? $this->preview : 80).'px" frameborder="0" style="border:0; background:'.$GLOBALS['meta']['acsFondColor'].'" src="'.$url.'"></iframe>';
		}
			$r .= '<fieldset class="apercu"><legend><a href="javascript:void(0)" onclick=" findObj(\''.$this->fullname.'\').src=\''.$url.'\';return false;" title="'._T('admin_recalculer').'">'._T('previsualisation').'</a></legend>'.$preview.'</fieldset>';
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
			$ctl = 'ctl'.ucfirst($var['type']);
			if (class_exists($ctl)) {
				$ctl = new $ctl($this->class, $this->nic, $v, $$v, $var, md5($this->fullname));
				if (method_exists($ctl, "draw"))
					$controls[$var['nom']] = $ctl->draw();
			}
			else
				$controls[$var['nom']] = $ctl."() undefined.<br />" ;
		}

		// Recherche une mise en page et y remplace les variables par des contrôles
		$mis_en_page = array();
		if (find_in_path('composants/'.$this->class.'/ecrire/'.$this->class.'_mep.html')) {
			$mep .= recuperer_fond('composants/'.$this->class.'/ecrire/'.$this->class.'_mep', array('lang' => $GLOBALS['spip_lang'], 'nic' => $this->nic));
			foreach ($controls as $nom=>$html) {
				$tag = '&'.$nom.'&';
				if (strpos($mep, $tag) !== false)
					$mis_en_page[] = $nom;
				$mep = str_replace($tag, $html, $mep);
			}
			// en mode controleur
			if ($mode=='controleur') {
				$mep = preg_replace('%<admin>(.*?)</admin>%s', '', $mep);
				// on ajoute si besoin est la liste des widgets, invisible
				if ($this->nb_widgets > 0)
					$mep .= liste_widgets(false);
			}
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
 * Méthode help: retourne l'aide d'une variable ou d'un composant
 */
	function help($var=NULL) {
		$help_src = 'acs:'.$this->class.($var ? '_'.$var : '').'_help';
		$help = _T($help_src);
		if ($help != $this->class.($var ? ' '.$var : '').' help')
			return $help;
		else
			return false;
	}
/**
 * Méthode nextInstance: retourne un numéro d'instance de composant inutilisé
 */
	function nextInstance() {
		$i = composant_instances($this->class);
 		return count($i) + 1;
	}
}

?>