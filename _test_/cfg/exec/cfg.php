<?php
/*
 * Plugin cfg : ecrire/?exec=cfg&cfg=xxxx
 *
 * Auteur : bertrand@toggg.com
 * � 2007 - Distribue sous licence LGPL
 *
 */
// la fonction appelee par le core, une simple "factory" de la classe cfg
function exec_cfg_dist($class = null)
{
	// classe standard ?
	if (((!$class && ($class = 'cfg')) || $class == 'cfg') && !class_exists($class)) {
	    class cfg extends cfg_dist { }
	} 

	$config = new $class(
		($nom = _request('cfg'))? $nom : 'cfg',
		($vue = _request('vue'))? $vue : $nom,
		($cfg_id = _request('cfg_id'))? $cfg_id : ''
		);

	if (!$config->autoriser()) {
		include_spip('inc/minipres');
		echo minipres(_T('info_acces_refuse') .
			" (cfg {$config->nom} - {$config->vue} - {$config->cfg_id})");
		exit;
	}

	$config->traiter();
	echo $config->sortie();
	return;
}

// la classe cfg represente une page de configuration
class cfg_dist
{
// le storage, par defaut metapack: spip_meta serialise
	var $storage = 'metapack';
// l'objet de classe cfg_<storage> qui assure lecture/ecriture des config
	var $sto = null;
// les options de creation de cet objet
	var $optsto = array();
// le "faire" de autoriser($faire), par defaut, autoriser_defaut_dist(): que les admins complets
	var $autoriser = 'defaut';
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
		$this->base_url = generer_url_ecrire('');
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
		$this->cfg_id = $cfg_id;

		// pre-analyser le formulaire
		if ($vue) {
			$this->message .= $this->set_vue($vue);
		}
		
		// creer le storage et lire les valeurs
		$classto = 'cfg_' . $this->storage;
		include_spip('inc/' . $classto);
		$this->sto = new $classto($this, $this->optsto);
		$this->val = $this->sto->lire();
	}

	function nom_config()
	{
	    return $this->nom . ($this->casier ? '/' . $this->casier : '') .
	    		($this->cfg_id ? '/' . $this->cfg_id : '');
	}

	function set_vue($vue)
	{
		$this->vue = $vue;
		$fichier = find_in_path($nom = 'fonds/cfg_' . $this->vue .'.html');
		if (!lire_fichier($fichier, $this->controldata)) {
			return _L('erreur_lecture_') . $nom;
		}
		$sans_rem = preg_replace_callback('/(\[\(#REM\) (\w+)(\*)?=)(.*?)\]/sim',
						array($this, 'post_params'), $this->controldata);
		if (!preg_match_all(
		  '#<(?:(select|textarea)|input type="(text|password|checkbox|radio)") name="(\w+)(\[\])?"(?: class="[^"]*?(?:type_(\w+))?[^"]*?(?:cfg_(\w+))?[^"]*?")?( multiple=)?[^>]*?>#ims',
						$sans_rem, $matches, PREG_SET_ORDER)) {
			return _L('pas_de_champs_dans_') . $nom;
		}
		foreach ($matches as $regs) {
			if (substr($regs[3], 0, 5) == '_cfg_') {
				continue;
			}
		    if (!empty($regs[1])) {
		    	$regs[2] = strtolower($regs[1]);
			    if ($regs[2] == 'select' && !empty($regs[7])) {
			    	$regs[2] = 'selmul';
			    }
		    }
		    $this->champs[$regs[3]] =
		    	array('inp' => $regs[2], 'typ' => '', 'array' => !empty($regs[4]));
		    if (!empty($regs[5])) {
		    	$this->champs[$regs[3]]['typ'] = $regs[5];
		    }
		    if (!empty($regs[6])) {
		    	$this->champs[$regs[3]]['cfg'] = $regs[6];
		    	if ($regs[6] == 'id') {
			    	$this->champs[$regs[3]]['id'] = count($this->champs_id);
		    		$this->champs_id[] = $regs[3];
		    	}
		    }
	    }
	    return '';
	}

	function autoriser()
	{
		return autoriser($this->autoriser);
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
			$this->sto->modifier('supprimer');
			$this->message .= _L('config_supprimee') . ' <b>' . $this->nom . '</b>';
		} elseif (!($this->message = $this->controle())) {
			if ($this->new_id != $this->cfg_id && !_request('_cfg_copier')) {
				$this->sto->modifier('supprimer');
			}
			$this->cfg_id = $this->new_id;
			$this->sto->modifier();
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
		qui sera enrichi puis passe � recuperer_fond
	*/
	function get_fond($contexte = array())
	{
		include_spip('inc/securiser_action');
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
	    $return = recuperer_fond('fonds/cfg_' . $this->vue,
	    		$this->val ? array_merge($contexte, $this->val) : $contexte);

		// liste des post-proprietes de l'objet cfg, lues apres recuperer_fond()
		$this->rempar = array(array());
		if (preg_match_all('/<!-- \w+\*?=/', $this->controldata, $this->rempar)) {
			$return = preg_replace_callback('/(<!-- (\w+)(\*)?=)(.*?)-->/sim',
								array($this, 'post_params'), $return);
		}
		if (!empty($this->rempar[0])) {
			die("erreur manque parametre externe");
		}
		return $return;
	}
	// callback pour interpreter les parametres objets du formulaire
	// commun avec celui de set_vue()
	function post_params($regs) {
		// a priori, eviter l'injection du motif
		if (isset($this->rempar) && $regs[1] != array_shift($this->rempar[0])) {
			die("erreur parametre interne: " . htmlentities($regs[1]));
		}
		if (empty($regs[3])) {
		    $this->{$regs[2]} = $regs[4];
		} elseif (is_array($this->{$regs[2]})) {
		    $this->{$regs[2]}[] = $regs[4];
		}
		// plus besoin de garder ca
		return '';
	}
	function sortie($contexte = array())
	{
		$formulaire = $this->get_fond($contexte);
		($this->titre && $this->boite)
		 ||	($this->titre && ($this->boite = $this->titre) && !($this->titre = ''))
		 || $this->boite
		 || ($this->boite = _L('Configuration') . ' ' . $this->nom);

		return
			$this->debut_page() .

			$formulaire .
			
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
		$dedans = $simple = '';
		if (($exi = lire_config($lien))) {
			foreach ($exi as $compte => $info) {
				// config simple ?
				if (!is_array($info)) {
					$dedans = '';
					break;
				}
				$dedans .= '
<p><label for="' . $lien . '_' . $compte . '">' . $compte . '</label>
<input type="image" id="' . $lien . '_' . $compte . '" name="cfg_id" value="' . $compte . '" src="../dist/images/triangle.gif" style="vertical-align: text-top;"/></p>';
			}
		}
		if ($dedans) {
			$dedans = '
<p><label for="' . $lien . '_">' . _T('cfg:nouveau') . '</label>
<input type="image" id="' . $lien . '_" name="nouveau" value="1" src="../dist/images/creer.gif" style="vertical-align: text-top;"/></p>' . $dedans;
		} else {
			$simple = '
<input type="image" id="' . $lien . '" name="cfg_id" value="" src="../dist/images/triangle.gif" style="vertical-align: text-top;"/>';
		}
		return debut_boite_info(true) .	'
<form method="post" action="' . $this->base_url . '">
<h4>' . _L($lien) . '
<input type="hidden" name="exec" value="cfg" />
<input type="hidden" name="cfg" value="' . $lien . '" />' . $simple . '</h4><div>' .
			$dedans . '</div></form>' . fin_boite_info(true);
	}
	function debut_page()
	{
		include_spip('inc/presentation');

		$commencer_page = charger_fonction('commencer_page', 'inc');
		
		return $commencer_page($this->boite, 'cfg', $this->nom) .
		
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
			
			($this->titre ? gros_titre($this->titre, '', false) : '') .
			
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
