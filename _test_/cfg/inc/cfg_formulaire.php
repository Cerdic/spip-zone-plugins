<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


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
// stockage du fond compile par recuperer_fond()
	var $fond_compile = '';
// configuration des types
//TODO traductions
	var $types = array(
		  'id' => array('#^[a-z_]\w*$#i', 'lettre ou &#095; suivie de lettres, chiffres ou &#095;'),
		  'idnum' => array('#^\d+$#', 'chiffres', 'intval'),
		  'pwd' => array('#^.{5}#', 'minimum 5 caract&egrave;res;'));
	
	/*
	 * Constructeur de la classe
	 */
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
		
		/*
		 * Cas des champs multi, si des champs (Y)
		 * sont declares id par la classe cfg_id,
		 * <input type='x' name='Yn' class='cfg_id'>
		 * on les ajoute dans le chemin pour retrouver les donnees
		 * #CONFIG{.../y1/y2/y3/...}
		 * 
		 */
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
		$classto = 'cfg_' . trim($this->storage);
		include_spip('inc/' . $classto);
		$this->sto = new $classto($this, $this->optsto);
		$this->val = $this->sto->lire();
	}


	/*
	 * Determine l'arborescence ou CFG doit chercher les valeurs deja enregistrees
	 * si nom=toto, casier=chose/truc, cfg_id=2, 
	 * cfg cherchera dans #CONFIG{toto/chose/truc/2}
	 * 
	 */
	function nom_config()
	{
	    return $this->nom . ($this->casier ? '/' . $this->casier : '') .
	    		($this->cfg_id ? '/' . $this->cfg_id : '');
	}

	/*
	 * La vue est le nom du fond CFG a lire.
	 * 
	 * On lit le fond et on recupere 
	 * - les parametres CFG contenus dans les balises [(#REM) param=valeur]
	 * - ainsi que les nom des champs de formulaires
	 * 
	 */
	function set_vue($vue)
	{
		// lecture de la vue.
		$this->vue = $vue;
		$fichier = find_in_path($nom = 'fonds/cfg_' . $this->vue .'.html');
		if (!lire_fichier($fichier, $this->controldata)) {
			return _L('erreur_lecture_') . $nom;
		}
		
		// recherche et stockage des parametres de cfg
		$this->recuperer_parametres();
		// recherche et stockage des noms de champs de formulaire
		return $this->recuperer_noms_champs();
	}



	/*
	 * 
	 * Recherche et stockage
	 * des parametres passes a CFG
	 * par #REM ou <!--
	 * 
	 */
	function recuperer_parametres(){
		// cas de #REM
		preg_replace_callback('/(\[\(#REM\) ([a-z0-9]\w+)(\*)?=)(.*?)\]/sim',
					array(&$this, 'post_params'), $this->controldata);
					
		// cas de <!--
		// liste des post-proprietes de l'objet cfg, lues apres recuperer_fond()
		// et stockees dans <!-- param=valeur -->
 		$this->recuperer_parametres_post_compile();

	}
	
	
	function recuperer_parametres_post_compile(){
		$this->rempar = array(array());
		if (preg_match_all('/<!-- [a-z0-9]\w+\*?=/i', $this->controldata, $this->rempar)) {
			// il existe des champs <!-- param=valeur -->, on les stocke
			$this->recuperer_fond();
			$this->current_rempar = 0;
			$this->fond_compile = preg_replace_callback('/(<!-- ([a-z0-9]\w+)(\*)?=)(.*?)-->/sim',
								array(&$this, 'post_params'), $this->fond_compile);
			// s'il en reste : il y a un probleme !
			if (preg_match('/<!-- [a-z0-9]\w+\*?=/', $this->fond_compile)) {
				die('erreur manque parametre externe: '
					. htmlentities(var_export($this->rempar, true)));
			}
		}		
	}
	
	
	/*
	 * 
	 * Recherche et stockage
	 * des noms des champs (y) du formulaire
	 * <input type="x" name="y"... />
	 * 
	 */	
	function recuperer_noms_champs(){	
		// recherche d'au moins un champ de formulaire pour savoir si la vue est valide
		$this->recuperer_fond();
		if (!preg_match_all(
		  '#<(?:(select|textarea)|input type="(text|password|checkbox|radio|hidden)") name="(\w+)(\[\])?"(?: class="[^"]*?(?:type_(\w+))?[^"]*?(?:cfg_(\w+))?[^"]*?")?( multiple=)?[^>]*?>#ims',
						$this->fond_compile, $matches, PREG_SET_ORDER)) {
			return _L('pas_de_champs_dans_') . $nom;
		}
		
		// stockage des champs trouves dans $this->champs
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
	 
	
	/*
	 * 
	 * Compiler le fond CFG si ce n'est pas fait
	 * 
	 */
	function recuperer_fond($contexte = array(), $forcer = false){
		if (!$this->fond_compile OR $forcer){
			include_spip('inc/presentation'); // offrir les fonctions d'espace prive
			include_spip('public/assembler');
			$this->fond_compile = recuperer_fond(
					'fonds/cfg_' . $this->vue,
					$this->val 
						? array_merge($contexte, $this->val) 
						: $contexte);
		}
	}
	
	
	/*
	 * Verifie les autorisations 
	 * d'affichage du formulaire
	 * (parametre autoriser=quoi)
	 */
	function autoriser()
	{
		include_spip('inc/autoriser');
		return autoriser($this->autoriser);
	}

	/*
	 * Log le message passe en parametre
	 * $this->log('message');
	 */
	function log($message)
	{
		($GLOBALS['auteur_session'] && ($qui = $GLOBALS['auteur_session']['login']))
		|| ($qui = $GLOBALS['ip']);
		spip_log('cfg (' . $this->nom_config() . ') par ' . $qui . ': ' . $message);
	}

	
	/*
	 * Modifie ou supprime les donnees postees par le formulaire
	 */
	function modifier($supprimer = false)
	{
		// suppression ?
		if ($supprimer) {
			$ok = $this->sto->modifier($supprimer);
			$this->message .= ($msg = $ok ? _L('config_supprimee') : _L('erreur_suppression'))
								. ' <b>' . $this->nom_config() . '</b>';
			$this->log($msg);
		}
		// sinon verification du type des valeurs postees
		else if (($this->message = $this->controle())) {
		}
		// si valeurs valides, ont elles changees ? 
		else if (!$this->log_modif) {
			$this->message .= _L('pas_de_changement') . ' <b>' . $this->nom_config() . '</b>';
		}
		// si elles ont changees, on modifie !
		else {
			$ok = $this->sto->modifier();
			$this->message .= ($msg = $ok ? _L('config_enregistree') : _L('erreur_enregistrement'))
								. ' <b>' . $this->nom_config() . '</b>';
			$this->log($msg . ' ' . $this->log_modif);
		}
		// pipeline 'cfg_post_edition'
		$this->message = pipeline('cfg_post_edition',array('args'=>array('nom_config'=>$this->nom_config()),'data'=>$this->message));
	}


	/*
	 * Gere le traitement du formulaire qui a ete valide.
	 * 
	 */
	function traiter()
	{
		// est on autorise ?
		if (!$this->_permise) {
			return;
		}
	
		// enregistrement ou suppression ?
		$enregistrer = $supprimer = false;
		if ($this->message ||
			! (($enregistrer = _request('_cfg_ok')) ||
							($supprimer = _request('_cfg_delete')))) {
			return;
		}
	
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$securiser_action();
		// suppression
		if ($supprimer) {
			$this->modifier('supprimer');
		// sinon modification
		// seulement si les types de valeurs attendus sont corrects
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
			if (defined('_COMPAT_CFG_192')) ecrire_metas();
			include_spip('inc/headers');
			redirige_par_entete(parametre_url(self(),null,null,'&'));
		}
	}

	/*
	 * Verifie les valeurs postees.
	 * - stocke les valeurs qui ont changees dans $this->val[$nom_champ] = 'nouvelle_valeur'
	 * - verifie que les types de valeurs attendus sont corrects ($this->types)
	 */
	function controle()
	{
	    $return = '';

		foreach ($this->champs as $name => $def) {
			// enregistrement des valeurs postees
			$oldval = $this->val[$name];
		    $this->val[$name] = _request($name);
		    if ($oldval != $this->val[$name]) {
		    	$this->log_modif .= $name . ':' .
		    		var_export($oldval, true) . '/' . var_export($this->val[$name], true) .', ';
		    }
		    // verification du type de valeur attendue
		    // cela est defini par un nom de class css (class="type_idnum")
		    // 'idnum' etant defini dans $this->types['idnum']...
		    if (!empty($def['typ']) && isset($this->types[$def['typ']])) {
		    	if (!preg_match($this->types[$def['typ']][0], $this->val[$name])) {
		    		$return .= _L($name) . '&nbsp;:<br />' .
		    		  $this->types[$def['typ']][1] . '<br />';
		    	}
		    }
	    }
	    // donner un identifiant au formulaire ?
		$this->new_id = '';
		$sep = '';
		foreach ($this->champs_id as $name) {
			$this->new_id .= $sep . $this->val[$name];
			$sep = '/';
	    }
	    return $return;
	}

	/*
	 * Fabriquer les balises des champs d'apres un modele fonds/cfg_<driver>.html
	 * $contexte est un tableau (nom=>valeur)
	 * qui sera enrichi puis passe a recuperer_fond
	 */
	function formulaire($contexte = array())
	{
		if (!find_in_path('fonds/cfg_' . $this->vue . '.html'))
			return '';

		include_spip('inc/securiser_action');
	    $arg = 'cfg0.0.0-' . $this->nom . '-' . $this->vue;
		$contexte['_cfg_'] =
			'?exec=cfg&cfg=' . $this->nom .
			'&cfg_vue=' . $this->vue .
			'&cfg_id=' . $this->cfg_id .
			'&base_url=' . $this->base_url .
		    '&lang=' . $GLOBALS['spip_lang'] .
		    '&arg=' . $arg .
		    '&hash=' .  calculer_action_auteur('-' . $arg);

		// recuperer le fond avec le contexte
		// forcer le calcul.
		$this->recuperer_fond($contexte, true);
		//$this->recuperer_parametres_post_compile();
		return $this->fond_compile;
	}
	
	
	/* 
	 * callback pour interpreter les parametres objets du formulaire
	 * commun avec celui de set_vue()
	 * 
	 * Parametres : 
	 * - $regs[2] = 'param'
	 * - $regs[3] = '*' ou ''
	 * - $regs[4] = 'valeur'
	 * 
	 * Lorsque des parametres sont passes dans le formulaire 
	 * [(#REM) param=valeur] ou <!-- param=valeur -->
	 * stocker $this->param=valeur
	 * 
	 * Si <!-- param*=valeur -->
	 * Stocker $this->param[]=valeur
	 * 
	 */
	function post_params($regs) {
		// a priori, eviter l'injection du motif
		if (isset($this->rempar)) {
			if (!isset($this->rempar[0][$this->current_rempar])
				|| $regs[1] != $this->rempar[0][$this->current_rempar++]) {
				die("erreur parametre interne: " . htmlentities(var_export($regs[1], true)));
			}
		}
		// $regs[3] peut valoir '*' pour signaler un tableau
		$regs[4] = trim($regs[4]);
		
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
