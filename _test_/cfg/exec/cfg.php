<?php
/*
 * Plugin cfg : ecrire/?exec=cfg&cfg=xxxx
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 */
// la fonction appelee par le core, une simple "factory" de la classe cfg
function exec_cfg_dist($class = null)
{
	// classe standard ?
	if (!$class && ($class = 'cfg') && !class_exists($class)) {
	    class cfg extends cfg_dist { }
	} 

	$config = new $class(
		($nom = _request('cfg'))? $nom : 'cfg',
		($vue = _request('vue'))? $vue : $nom,
		($cfg_id = _request('cfg_id'))? $cfg_id : ''
		);

	$config->traiter();
	echo $config->sortie();
	return;
}

// la classe cfg represente une page de configuration
class cfg_dist
{
// le nom du meta (ou autre) ou va etre stocke la config concernee
	var $nom = '';
// le fond html utilise , en general pour config simple idem $nom
	var $vue = '';
// pour une config multiple , l'id courant
	var $cfg_id = '';
// sous tableau optionel du meta ou va etre stocke le fragment de config
// vide = a la "racine" du meta nomme $nom
	var $casier = '';
// descriptif
	var $descriptif = '';
// compte-rendu des mises a jour, vide == pas d'erreur
	var $message = '';
// liens optionnels sur des sous-config [(#REM) liens*=xxx]
	var $liens = array();
// les champs trouve dans le fond
	var $champs = array();
// les champs index
	var $champs_id = array();
// leurs valeurs
	var $val = array();
	
	function cfg_dist($nom, $vue = '', $cfg_id = '', $opt = array())
	{
		$this->nom = $nom;
		$this->titre = _L('Configuration') . ' ' . $this->nom;
		$this->base_url = generer_url_ecrire('');
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
		$this->cfg_id = $cfg_id;
		if ($vue) {
			$this->message .= $this->set_vue($vue);
		}
		$this->lire();
	}

	function nom_config()
	{
	    return $this->nom . ($this->casier ? '/' . $this->casier : '') .
	    		($this->cfg_id ? '/' . $this->cfg_id : '');
	}
	
// recuperer les valeurs, utilise la fonction commune lire_cfg() de cfg_options.php
	function lire()
	{
    	$this->val = lire_config($this->nom_config());
    	if ($this->cfg_id) {
    		$cles = explode('/', $this->cfg_id);
			foreach ($this->champs_id as $i => $name) {
				$this->val[$name] = $cles[$i];
		    }
    	}
	    return $this->val;
	}
	
// se positionner dans le tableau arborescent
	function & monte_arbre(&$base, $chemin)
	{
		if (!$chemin) {
			return $base;
		}
		foreach (explode('/', $chemin) as $chunk) {
			if (!isset($base[$chunk])) {
				$base[$chunk] = array();
			}
	    	$this->_report[] = array(&$base, $chunk);
	    	$base = &$base[$chunk];
		}
		return $base;
	}
	
// modifier le fragment qui peut etre tout le meta
	function modifier($supprimer = false)
	{
    	($base = lire_config($this->nom)) || ($base = array());
    	$ici = &$base;
    	$this->_report = array();
    	$ici = &$this->monte_arbre($ici, $this->casier);
    	$ici = &$this->monte_arbre($ici, $this->cfg_id);
		foreach ($this->champs as $name => $def) {
			if (isset($def['id'])) {
				continue;
			}
			if ($supprimer) {
				unset($ici[$name]);
			} else {
				$ici[$name] = $this->val[$name];
			}
	    }
		if ($supprimer) {
			for ($i = count($this->_report); $i--; ) {
				if ($this->_report[$i][0][$this->_report[$i][1]]) {
					break;
				}
				unset($this->_report[$i][0][$this->_report[$i][1]]);
			}
		}
		if ($supprimer && !$base) {
		    effacer_meta($this->nom);
		} else {
		    ecrire_meta($this->nom, serialize($base));
	    }
	    ecrire_metas();
	}

	function set_vue($vue)
	{
		$this->vue = $vue;
		$this->boite = _L('Configuration') . ' ' . $this->vue;
		$fichier = find_in_path($nom = 'fonds/cfg_' . $this->vue .'.html');
		if (!lire_fichier($fichier, $controldata)) {
			return _L('erreur_lecture_') . $nom;
		}
		if (preg_match_all('/\[\(#REM\) (\w+)(\*)?=(.*?)\]/sim',
						$controldata, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $regs) {
			    if (empty($regs[2])) {
				    $this->{$regs[1]} = $regs[3];
			    } else {
				    if (!is_array($this->{$regs[1]})) {
				    	continue;
				    }
				    $this->{$regs[1]}[] = $regs[3];
			    }
		    }
		}
		if (!preg_match_all(
		  '#<(?:(select|textarea)|input type="(text|password|checkbox|radio)") name="(\w+)"(?: class="[^"]*?(?:type_(\w+))?[^"]*?(?:cfg_(\w+))?[^"]*?")?[^>]*?>#ims',
						$controldata, $matches, PREG_SET_ORDER)) {
			return _L('pas_de_champs_dans_') . $nom;
		}
		foreach ($matches as $regs) {
			if (substr($regs[3], 0, 5) == '_cfg_') {
				continue;
			}
		    if (!empty($regs[1])) {
		    	$regs[2] = 'select';
		    }
		    $this->champs[$regs[3]] = array('inp' => $regs[2], 'typ' => '');
		    if (!empty($regs[4])) {
		    	$this->champs[$regs[3]]['typ'] = $regs[4];
		    }
		    if (!empty($regs[5])) {
		    	$this->champs[$regs[3]]['cfg'] = $regs[5];
		    	if ($regs[5] == 'id') {
			    	$this->champs[$regs[3]]['id'] = count($this->champs_id);
		    		$this->champs_id[] = $regs[3];
		    	}
		    }
	    }
	    return '';
	}

	function traiter()
	{
		$enregistrer = $supprimer = false;
		if ($this->message ||
			! (($enregistrer = _request('_cfg_ok')) ||
							($supprimer = _request('_cfg_delete')))) {
			return;
		}

		$securiser_action = charger_fonction('securiser_action', 'inc');
		$securiser_action();
		if ($supprimer) {
			$this->modifier('supprimer');
			$this->message .= _L('config_supprimee') . ' <b>' . $this->nom . '</b>';
		} elseif (!($this->message = $this->controle())) {
			if ($this->new_id != $this->cfg_id && !_request('_cfg_copier')) {
				$this->modifier('supprimer');
			}
			$this->cfg_id = $this->new_id;
			$this->modifier();
			$this->message .= _L('config_enregistree') . ' <b>' . $this->nom . '</b>';
		}
//		$this->message .= print_r($this->champs, true);
	}

	function controle()
	{
		$chk = array(
		  'id' => array('#^[a-z_]\w*$#i', _L('lettre ou &#095; suivie de lettres, chiffres ou &#095;')),
		  'idnum' => array('#^\d+$#', _L('chiffres')),
		  'pwd' => array('#^\w+$#',  _L('lettres, &#095; ou chiffres')));
	    $return = '';
		foreach ($this->champs as $name => $def) {
		    $this->val[$name] = _request($name);
		    if (!empty($def['typ']) && isset($chk[$def['typ']])) {
		    	if (!preg_match($chk[$def['typ']][0], $this->val[$name])) {
		    		$return .= _L($name) . '&nbsp;:<br />' .
		    		  $chk[$def['typ']][1] . '<br />';
		    	}
		    }
	    }
		$this->new_id = '';
		$sep = '';
		foreach ($this->champs_id as $name) {
			$this->new_id .= $sep . $this->val[$name];
			$sep = '/';
	    }
	    return $return;
	}

	/*
	 Fabriquer les balises des champs d'apres un modele fonds/cfg_<driver>.html
		$contexte est un tableau (nom=>valeur)
		qui sera enrichi puis passe à recuperer_fond
	*/
	function get_fond($contexte = array())
	{
	    $arg = 'cfg0.0.0-' . $this->nom . '-' . $this->vue;
		$contexte['_cfg_'] =
			'?exec=cfg&cfg=' . $this->nom .
			'&vue=' . $this->vue .
			'&cfg_id=' . $this->cfg_id .
			'&base_url=' . $this->base_url .
		    '&lang=' . $GLOBALS['spip_lang'] .
		    '&arg=' . $arg .
		    '&hash=' .  calculer_action_auteur('-' . $arg);
	    include_spip('public/assembler');
	    return recuperer_fond('fonds/cfg_' . $this->vue,
	    		$this->val ? array_merge($contexte, $this->val) : $contexte);
	}

	function sortie($contexte = array())
	{
		return
			$this->debut_page() .

			$this->get_fond($contexte) .
			
			$this->fin_page();
				
	}

	function lier()
	{
		$return = '';
		foreach ($this->liens as $lien) {
			$return .= $this->boite_liens($lien);
		}
		return $return;
	}

	function boite_liens($lien)
	{
		$dedans = debut_boite_info(true) .
			'<h4>' . _L($lien) . '</h4><div>
<form method="post" action="' . $this->base_url . '">
<input type="hidden" name="exec" value="cfg" />
<input type="hidden" name="cfg" value="' . $lien . '" />
<p><label for="' . $lien . '_">' . _T('cfg:nouveau') . '</label>
<input type="image" id="' . $lien . '_" name="nouveau" value="1" src="../dist/images/creer.gif" style="vertical-align: text-top;"/></p>';
		if (($exi = lire_config($lien))) {
			foreach ($exi as $compte => $info) {
				$dedans .= '
<p><label for="' . $lien . '_' . $compte . '">' . $compte . '</label>
<input type="image" id="' . $lien . '_' . $compte . '" name="cfg_id" value="' . $compte . '" src="../dist/images/triangle.gif" style="vertical-align: text-top;"/></p>';
			}
		}
		$dedans .= '</form></div>' . fin_boite_info(true);
		return $dedans;
	}
	function debut_page()
	{
		include_spip('inc/presentation');

		$commencer_page = charger_fonction('commencer_page', 'inc');
		
		return $commencer_page($this->titre, 'cfg', $this->nom) .
		
			debut_gauche("accueil", true) .
		
			debut_boite_info(true) .
			propre($this->descriptif) .
			fin_boite_info(true) .
		
			($this->message ? 
				debut_boite_info(true) .
				propre($this->message) .
				fin_boite_info(true)
			: '') .
		
			$this->lier() .
		
			debut_droite("", true) .
			
			gros_titre($this->titre, '', false) .
			
			debut_cadre_trait_couleur('', true, '', $this->boite);
	}

	function fin_page()
	{
		return fin_cadre_trait_couleur(true) .
			fin_gauche() .
		
			fin_page();
	}
}
?>
