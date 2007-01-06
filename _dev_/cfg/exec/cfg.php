<?php
/*
 * Plugin cfg : ecrire/?exec=cfg&cfg=xxxx
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 */

function exec_cfg_dist()
{
	$config = new cfg(
		($nom = _request('cfg'))? $nom : 'cfg',
		($fond = _request('fond'))? $fond : $nom
		);
//var_dump($config);die();

	$config->traiter();
	echo $config->sortie();
	return;
}
class cfg
{
	var $nom = '';
	var $fond = '';
	var $descriptif = '';
	var $message = '';
	var $champs = array();
	var $val = array();
	
	function cfg($nom, $fond = '', $opt = array())
	{
		$this->nom = $nom;
		$this->titre = _L('Configuration') . ' ' . $this->nom;
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
		$this->lire();
		if ($fond) {
			$this->message .= $this->set_fond($fond);
		}
	}
	
	function lire()
	{
		lire_metas();
	    global $meta;
	    if (empty($meta[$this->nom])) {
	    	$this->val = array();
	    } else {
	    	$this->val = unserialize($meta[$this->nom]);
	    }
	    return $this->val;
	}
	
	function supprimer()
	{
	    effacer_meta($this->nom);
	    $this->val = array();
	}
	
	function modifier()
	{
	    ecrire_meta($this->nom, serialize($this->val));
	}

	function set_fond($fond)
	{
		$this->fond = $fond;
		$this->boite = _L('Configuration') . ' ' . $this->fond;
		$fichier = find_in_path($nom = 'fonds/cfg_' . $this->fond .'.html');
		if (!lire_fichier($fichier, $controldata)) {
			return _L('erreur_lecture_') . $nom;
		}
		if (preg_match_all('/\[\(#REM\) (\w+)=(.*?)\]/sim',
						$controldata, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $regs) {
			    $this->{$regs[1]} = $regs[2];
		    }
		}
		if (!preg_match_all(
		  '#<(?:(select)|input type=["\'](text|password|checkbox|radio)["\']) name=["\'](\w+)["\'](?: class=["\']type_(\w+).*?["\'])?.+?>#ims',
						$controldata, $matches, PREG_SET_ORDER)) {
			return _L('pas_de_champs_dans_') . $nom;
		}
//var_dump($matches);die();
		foreach ($matches as $regs) {
		    if (!empty($regs[1])) {
		    	$regs[2] = 'select';
		    }
		    $this->champs[$regs[3]] = array('inp' => $regs[2], 'typ' => '');
		    if (!empty($regs[4])) {
		    	$this->champs[$regs[3]]['typ'] = $regs[4];
		    }
	    }
//var_dump($this->champs);die();
	    return '';
	}

	function traiter()
	{
		$enregistrer = $supprimer = false;
		if ($this->message ||
			! (($enregistrer = _request('ok')) ||
							($supprimer = _request('delete')))) {
			return;
		}

		$securiser_action = charger_fonction('securiser_action', 'inc');
		$securiser_action();
		if ($supprimer) {
			$this->supprimer();
			$this->message = _L('config_supprimee') . ' <b>' . $this->nom . '</b>';
		} elseif (!($this->message = $this->controle())) {
			$this->modifier();
			$this->message = _L('config_enregistree') . ' <b>' . $this->nom . '</b>';
		}
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
	    return $return;
	}

	/*
	 Fabriquer les balises des champs d'apres un modele fonds/cfg_<driver>.html
		$contexte est un tableau (nom=>valeur)
		qui sera enrichi puis passe à recuperer_fond
	*/
	function get_fond($contexte = array())
	{
		$get =  'cfg=' . $this->nom;
		if ($this->nom != $this->fond) {
			$get .= '&fond=' . $this->fond;
		}
		$contexte['base_url'] = generer_url_ecrire('cfg', $get);
	    $contexte['lang'] = $GLOBALS['spip_lang'];
	    $contexte['arg'] = 'config_sms-0.1.0';
	    $contexte['hash'] =  calculer_action_auteur('-' . $contexte['arg']);

	    include_spip('public/assembler');
	    return recuperer_fond('fonds/cfg_' . $this->fond,
	    		array_merge($contexte, $this->val));
	}

	function sortie($contexte = array())
	{
		return
			$this->debut_page() .

			$this->get_fond($contexte) .
			
			$this->fin_page();
				
	}

	function boite_liens($titre = "", $elements = array())
	{
		if (!$elements) {
			return '';
		}
		$dedans = debut_boite_info(true);
		if ($titre) {
			$dedans .= '<h4>' . _L($titre) . '</h4>';
		}
		if (is_string($elements)) {
			return $dedans . $elements;
		}
		$dedans .= '<ul>';
		foreach ($elements as $elt) {
			$dedans .= '<li>';
			if (!empty($elt['get'])) {
				$dedans .= '<a href="' .
				  generer_url_ecrire('config_sms', $elt['get'] ) . '">' .
				  (empty($elt['name']) ? $elt['get'] : $elt['name']) . '</a>';
			}
			$dedans .=  (empty($elt['desc']) ? '' : '<br />' . $elt['desc']) . '</li>';
		}
		$dedans .= '</ul>' . fin_boite_info(true);
		return $dedans;
	}

	function liens_existants($config)
	{
		$liste = array();
		foreach ($config as $compte => $info) {
			$liste[] = array('get' => 'modifier=' . $compte, 'name' => $compte,
							'desc' => '(' . $info['driver'] . ')');
		}
		return boite_liens(_L('modifier_un_compte'), $liste);
	}

	function liens_nouveaux()
	{
		$liste = array();
		foreach (cherche_prestataires() as $driver => $info) {
			$info['get'] = 'adddriver=' . $driver;
			$liste[] = $info;
		}
		return boite_liens(_L('creer_un_nouveau_compte'), $liste);
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
		
//			$this>liens_existants() .
//			$this->liens_nouveaux() .
		
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
/*

// sans parametre: lecture seule, sans data = suppression sinon update/delete
function meta_cfg($compte = '', $data = array())
{
	// recuperer le tableau de config dans meta , pas tres securit tout ça ...
	lire_metas();
    global $meta;
    if (empty($meta['cfg'])) {
    	$cfg = array();
    } else {
    	$cfg = unserialize($meta['cfg']);
    }
	// pas de compte , c'est juste pour lire
    if (!$compte) {
	    return $cfg;
    }
    // donnees => actualise ou cree , detruit sinon
    if ($data) {
    	$cfg[$compte] = $data;
    } else {
    	unset($cfg[$compte]);
    }
    if (count($cfg)) {
	    ecrire_meta('cfg', serialize($cfg));
    } else {
	    effacer_meta('cfg');
    }
    return $cfg;
}

	if (($addDriver = _request('adddriver'))) {
		$contexte['driver'] = $addDriver;
	} else {
		if (($modifier = _request('modifier'))) {
			$contexte['driver'] = $config[$modifier]['driver'];
		} else {
			$contexte['driver'] = _request('driver');
			$contexte['compte'] = _request('compte');
			$contexte['was_compte'] = _request('was_compte');
		}
	}

	$champs = array('compte' => array('inp' => 'text', 'typ' => 'id'));
	($this->message = empty($contexte['driver']) ? _L('creer_un_compte') : '') ||
	if ($modifier) {
		$contexte['compte'] = $modifier;
		foreach ($champs as $name => $def) {
			if (isset($config[$modifier][$name])) {
				$contexte[$name] = $config[$modifier][$name];
			}
		}

//	$this->message .= print_r($champs, true);
//	$this->message .= print_r($contexte, true);

		if ($supprimer ||
		 (!_request('copier') && $contexte['compte'] != $contexte['was_compte'])) {
			$config = meta_config_sms($contexte['was_compte']);
		}

*/
?>
