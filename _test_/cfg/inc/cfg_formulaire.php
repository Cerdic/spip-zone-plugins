<?php
/*
 * Plugin cfg : la classe fabricant le formulaire
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 */

// la classe cfg represente une page de configuration
class cfg_formulaire
{
// le storage, par defaut metapack: spip_meta serialise
	var $storage = 'metapack';
// l'objet de classe cfg_<storage> qui assure lecture/ecriture des config
	var $sto = null;
// les options de creation de cet objet
	var $optsto = array();
// le "faire" de autoriser($faire), par defaut, autoriser_configurer_dist()
	var $autoriser = 'configurer';
// la config est-elle permise ?
	var $_permise = false;
// en cas de refus, un message informatif [(#REM) refus=...]
	var $refus = '';
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
// cfg doit-il encadrer le formulaire tout seul ?
	var $presentation = 'auto';
// cfg doit-il afficher un lien vers le fond sous forme d'onglet
// dans la page ?exec=cfg
	var $onglet = 'oui'; 
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
// pour tracer les valeurs modifiees
	var $log_modif = '';
// configuration des types
//TODO traductions
	var $types = array(
		  'id' => array('#^[a-z_]\w*$#i', 'lettre ou &#095; suivie de lettres, chiffres ou &#095;'),
		  'idnum' => array('#^\d+$#', 'chiffres', 'intval'),
		  'pwd' => array('#^.{5}#', 'minimum 5 caract&egrave;res;'));
	
	function cfg_formulaire($nom, $vue = '', $cfg_id = '', $opt = array())
	{
		$this->nom = $nom;
		$this->base_url = generer_url_ecrire('');
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}

		// pre-analyser le formulaire
		if ($vue) {
			$erreur = $this->set_vue($vue);
			$this->message .= $erreur;
		}

		$this->_permise = $this->autoriser();
		
		if (_request('_cfg_affiche')) {
			$this->cfg_id = $sep = '';
			foreach ($this->champs_id as $name) {
				$this->cfg_id .= $sep . _request($name);
				$sep = '/';
		    }
	    } else {
			$this->cfg_id = $cfg_id;
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
		preg_replace_callback('/(\[\(#REM\) ([a-z0-9]\w+)(\*)?=)(.*?)\]/sim',
					array(&$this, 'post_params'), $this->controldata);

		include_spip('inc/presentation'); // offrir les fonctions d'espace prive
		include_spip('public/assembler');
		$fond_compile = recuperer_fond('fonds/cfg_' . $this->vue);

		if (!preg_match_all(
		  '#<(?:(select|textarea)|input type="(text|password|checkbox|radio|hidden)") name="(\w+)(\[\])?"(?: class="[^"]*?(?:type_(\w+))?[^"]*?(?:cfg_(\w+))?[^"]*?")?( multiple=)?[^>]*?>#ims',
						$fond_compile, $matches, PREG_SET_ORDER)) {
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
		return autoriser(trim($this->autoriser));
	}

	function log($message)
	{
		($GLOBALS['auteur_session'] && ($qui = $GLOBALS['auteur_session']['login']))
		|| ($qui = $GLOBALS['ip']);
		spip_log('cfg (' . $this->nom_config() . ') par ' . $qui . ': ' . $message);
	}

	function modifier($supprimer = false)
	{
		if ($supprimer) {
			$ok = $this->sto->modifier($supprimer);
			$this->message .= ($msg = $ok ? _L('config_supprimee') : _L('erreur_suppression'))
								. ' <b>' . $this->nom_config() . '</b>';
			$this->log($msg);
		}
		else if (($this->message = $this->controle())) {
		}
		else if (!$this->log_modif) {
			$this->message .= _L('pas_de_changement') . ' <b>' . $this->nom_config() . '</b>';
		}
		else {
			$ok = $this->sto->modifier();
			$this->message .= ($msg = $ok ? _L('config_enregistree') : _L('erreur_enregistrement'))
								. ' <b>' . $this->nom_config() . '</b>';
			$this->log($msg . ' ' . $this->log_modif);
		}
		$this->message = pipeline('cfg_post_edition',array('args'=>array('nom_config'=>$this->nom_config()),'data'=>$this->message));
	}

	function traiter()
	{
		if (!$this->_permise) {
			return;
		}
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
		} elseif (!($this->message = $this->controle())) {
			if ($this->new_id != $this->cfg_id && !_request('_cfg_copier')) {
				$this->modifier('supprimer');
			}
			$this->cfg_id = $this->new_id;
			$this->modifier();
		}
//		$this->message .= print_r($this->champs, true);

		// Il s'est produit une modif, on stocke le message dans une meta
		// et on redirige le client, de maniere a charger la page
		// avec la nouvelle config (ce qui permet par exemple a Autorite
		// de controler d'eventuels conflits generes par les nouvelles autorisations)
		if ($this->message && $this->rediriger) {
			include_spip('inc/meta');
			ecrire_meta('cfg_message_'.$GLOBALS['auteur_session']['id_auteur'], $this->message, 'non');
			if (version_compare($GLOBALS['spip_version_code'],'1.93','<')) ecrire_metas();
			include_spip('inc/headers');
			redirige_par_entete(parametre_url(self(),null,null,'&'));
		}
	}

	function controle()
	{
	    $return = '';
		foreach ($this->champs as $name => $def) {
			$oldval = $this->val[$name];
		    $this->val[$name] = _request($name);
		    if ($oldval != $this->val[$name]) {
		    	$this->log_modif .= $name . ':' .
		    		var_export($oldval, true) . '/' . var_export($this->val[$name], true) .', ';
		    }
		    if (!empty($def['typ']) && isset($this->types[$def['typ']])) {
		    	if (!preg_match($this->types[$def['typ']][0], $this->val[$name])) {
		    		$return .= _L($name) . '&nbsp;:<br />' .
		    		  $this->types[$def['typ']][1] . '<br />';
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
		qui sera enrichi puis passe a recuperer_fond
	*/
	function formulaire($contexte = array())
	{

		if (!find_in_path('fonds/cfg_' . $this->vue . '.html'))
			return '';

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
		include_spip('inc/presentation'); // offrir les fonctions d'espace prive
		include_spip('public/assembler');

		$return = recuperer_fond('fonds/cfg_' . $this->vue,
			$this->val ? array_merge($contexte, $this->val) : $contexte);


		// liste des post-proprietes de l'objet cfg, lues apres recuperer_fond()
		$this->rempar = array(array());
		if (preg_match_all('/<!-- [a-z0-9]\w+\*?=/i', $this->controldata, $this->rempar)) {
/* en reserve au cas ou vraiement pas possible autrement
			$GLOBALS['_current_cfg'] = &this;
			$return = preg_replace_callback('/(<!-- (\w+)(\*)?=)(.*?)-->/sim',
								array(&$GLOBALS['_current_cfg'], 'post_params'), $return);
*/
			$this->current_rempar = 0;
			$return = preg_replace_callback('/(<!-- ([a-z0-9]\w+)(\*)?=)(.*?)-->/sim',
								array(&$this, 'post_params'), $return);
			if (preg_match('/<!-- [a-z0-9]\w+\*?=/', $return)) {
				die('erreur manque parametre externe: '
					. htmlentities(var_export($this->rempar, true)));
			}
		}
		return $return;
	}
	// callback pour interpreter les parametres objets du formulaire
	// commun avec celui de set_vue()
	function post_params($regs) {
		// a priori, eviter l'injection du motif
		if (isset($this->rempar)) {
			if (!isset($this->rempar[0][$this->current_rempar])
				|| $regs[1] != $this->rempar[0][$this->current_rempar++]) {
				die("erreur parametre interne: " . htmlentities(var_export($regs[1], true)));
			}
		}
		if (empty($regs[3])) {
		    $this->{$regs[2]} = $regs[4];
		} elseif (is_array($this->{$regs[2]})) {
		    $this->{$regs[2]}[] = $regs[4];
		}
		// plus besoin de garder ca
		return '';
	}
}
?>
