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
		($fond = _request('fond'))? $fond : $nom,
		($id = _request('id'))? $id : ''
		);

	$config->traiter();
	echo $config->sortie();
	return;
}
class cfg
{
	var $nom = '';
	var $fond = '';
	var $id = '';
	var $descriptif = '';
	var $message = '';
	var $liens = array();
	var $champs = array();
	var $val = array();
	
	function cfg($nom, $fond = '', $id = '', $opt = array())
	{
		$this->nom = $nom;
		$this->titre = _L('Configuration') . ' ' . $this->nom;
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
		$this->id = $id;
		$this->lire();
		if ($fond) {
			$this->message .= $this->set_fond($fond);
		}
	}
	
	function lire()
	{
    	$this->val = lire_cfg($this->nom);
	    return $this->val;
	}
	
	function supprimer()
	{
	    effacer_meta($this->nom);
	    $this->val = array();
	    ecrire_metas();
	}
	
	function modifier()
	{
	    ecrire_meta($this->nom, serialize($this->val));
	    ecrire_metas();
	}

	function set_fond($fond)
	{
		$this->fond = $fond;
		$this->boite = _L('Configuration') . ' ' . $this->fond;
		$fichier = find_in_path($nom = 'fonds/cfg_' . $this->fond .'.html');
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
		  '#<(?:(select)|input type=["\'](text|password|checkbox|radio)["\']) name=["\'](\w+)["\'](?: class=["\']type_(\w+).*?["\'])?.+?>#ims',
						$controldata, $matches, PREG_SET_ORDER)) {
			return _L('pas_de_champs_dans_') . $nom;
		}
		foreach ($matches as $regs) {
		    if (!empty($regs[1])) {
		    	$regs[2] = 'select';
		    }
		    $this->champs[$regs[3]] = array('inp' => $regs[2], 'typ' => '');
		    if (!empty($regs[4])) {
		    	$this->champs[$regs[3]]['typ'] = $regs[4];
		    }
	    }
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
	    $contexte['arg'] = 'cfg0.0.0-' . $this->nom . '-' . $this->fond;
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

	function lier()
	{
		$return = '';
		foreach ($this->liens as $lien) {
			$return .= '' . $this->boite_liens($lien, 'test liens');
		}
		return $return;
	}

	function boite_liens($lien)
	{
		$dedans = debut_boite_info(true) .
			'<h4>' . _L($lien) . '</h4><ul><li><a href="' .
			  generer_url_ecrire('cfg', 'cfg=' . $lien) . '"><b>' .
				  _L('Nouveau') . ' ' . $lien . '</b></a></li>';
		if (count($this->val[$lien])) {
			foreach (lire_cfg($lien) as $compte => $info) {
				$dedans .= '<li><a href="' . generer_url_ecrire('cfg', 'cfg=' . $lien .
						'&id=' . $compte ) . '">' .
						 $compte . '</a></li>';
			}
		}
		$dedans .= '</ul>' . fin_boite_info(true);
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
