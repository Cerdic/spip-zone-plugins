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
// en cas de refus, un message informatif [(#REM) refus=...]
	var $refus = '';
// partie du fond cfg a inserer dans le head par le pipeline header_prive (todo insert_head?)
	var $head = '';
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
// afficher ce compte rendu ?
	var $afficher_messages = true;
// liens optionnels sur des sous-config <!-- liens*=xxx -->
	var $liens = array();
// liens optionnels sur des sous-config pour des fonds utilisant un champ multiple  <!-- liens_multi*=xxx -->
	var $liens_multi = array();
// les champs trouve dans le fond
	var $champs = array();
// les champs index
	var $champs_id = array();
// leurs valeurs
	var $val = array();
// nom de la table sql pour storage extra ou table
	var $table = '';
// autoriser l'insertion de nouveau contenu dans une table sans donner d'identifiant ?
	var $autoriser_absence_id = 'non';
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
	function cfg_formulaire($nom, $cfg_id = '', $opt = array())
	{
		$this->nom = $this->vue = $nom;
		$this->cfg_id = $cfg_id;
		$this->base_url = generer_url_ecrire('');
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
	    		
		// charger les donnees du fond demande
		$this->charger();
	}
	
	// pre-analyser le formulaire
	// c'est a dire recuperer les parametres CFG 
	// et les noms des champs du formulaire	
	function charger(){
		// lecture de la vue (fond cfg)
		// il s'agit de recuperer le contenu du fichier
		if ($this->vue) {
			$fichier = find_in_path($nom = 'fonds/cfg_' . $this->vue .'.html');
			if (!lire_fichier($fichier, $this->controldata)) {
				$this->message .=  _T('cfg:erreur_lecture', array('nom' => $nom));
			}
		}
		
		// recherche et stockage des parametres de cfg 
		$this->recuperer_parametres();
			
		// recherche et stockage des noms de champs de formulaire
		$this->message .=  $this->recuperer_noms_champs();
		
		/*
		 * Cas des champs multi, si des champs (Y)
		 * sont declares id par la classe cfg_id,
		 * <input type='x' name='Yn' class='cfg_id'>
		 * on les ajoute dans le chemin pour retrouver les donnees
		 * #CONFIG{.../y1/y2/y3/...}
		 * 
		 */
		if (_request('_cfg_affiche')) {
			$this->cfg_id = implode('/', array_map('_request', $this->champs_id));
	    } 
		
		// creer le storage et lire les valeurs
		$this->storage = strtolower(trim($this->storage));
		$classto = 'cfg_' . $this->storage;
		include_spip('inc/' . $classto);
		$this->sto = new $classto($this, $this->optsto);
		$this->val = $this->sto->lire();
		// stocker le fait que l'on a charge les valeurs
		$this->charger = true;
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
	 * Recherche et stockage
	 * des parametres #REM passes a CFG
	 * (DEPRECIE)
	 */
	function recuperer_parametres_rem(){
		// cas de #REM (deprecie)
		preg_replace_callback('/(\[\(#REM\) ([a-z0-9_]\w+)(\*)?=)(.*?)\]/sim',
					array(&$this, 'post_params'), $this->controldata);
	}
	
	
	// cette fonction recherche et stocke les parametres passes a cfg par <!-- param=valeur -->
	// ces lignes sont alors effacees du code html. Ces proprietes sont lues apres recuperer_fond(),
	// et interpretent donc les balises spip et les chaines de langues
	//
	// si la fonction est appelee 2 fois, les parametres identiques ne seront pas copies
	// sauf si le parametre est un tableau (<!-- param*=valeur -->), les valeurs seront dupliquees
	function recuperer_parametres(){
		
		// pour compatibilite, recuperer l'ancien code #REM
		$this->recuperer_parametres_rem();
		
		$this->rempar = array(array());
		if (preg_match_all('/<!-- [a-z0-9_]\w+\*?=/i', $this->controldata, $this->rempar)) {
			// il existe des champs <!-- param=valeur -->, on les stocke
			$this->recuperer_fond();
			$this->current_rempar = 0;
			$this->fond_compile = preg_replace_callback('/(<!-- ([a-z0-9_]\w+)(\*)?=)(.*?)-->/sim',
								array(&$this, 'post_params'), $this->fond_compile);
			// s'il en reste : il y a un probleme !
			if (preg_match('/<!-- [a-z0-9_]\w+\*?=/', $this->fond_compile)) {
				die('erreur manque parametre externe: '
					. htmlentities(var_export($this->rempar, true)));
			}
		}		
	}
	
	// une fonction pour effacer les parametres du code html
	// ce qui evite de dupliquer les tableaux 
	// (si on utilisait recuperer_parametres() a la place)
	function effacer_parametres(){
			$this->fond_compile = effacer_parametres_cfg($this->fond_compile);		
	}
	
	/*
	 * 
	 * Recherche et stockage
	 * des noms des champs (y) du formulaire
	 * <input type="x" name="y"... />
	 * 
	 */	
	function recuperer_noms_champs(){	
		if (!$this->vue) return;
		
		// recherche d'au moins un champ de formulaire pour savoir si la vue est valide
		$this->recuperer_fond();
		if (!preg_match_all(
		  '#<(?:(select|textarea)|input type="(text|password|checkbox|radio|hidden)") name="(\w+)(\[\])?"(?: class="[^"]*?(?:type_(\w+))?[^"]*?(?:cfg_(\w+))?[^"]*?")?( multiple=)?[^>]*?>#ims',
						$this->fond_compile, $matches, PREG_SET_ORDER)) {
			return _T('cfg:pas_de_champs_dans', array('nom' => $this->vue));
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
			// rendre editable systematiquement
			// sinon, ceux qui utilisent les fonds CFG avec l'API des formulaires dynamiques
			// et mettent des [(#ENV**{editable}|?{' '}) ... ] ne verraient pas leurs variables
			// dans l'environnement vu que CFG ne pourrait pas lire les champs du formulaire
			$contexte['editable'] = "oui";
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
			// dans le cas d'une suppression, il faut vider $this->val qui
			// contient encore les valeurs du formulaire, sinon elles sont 
			// passees dans le fond et le formulaire garde les informations
			// d'avant la suppression
			if ($ok) {
				$this->val = array();
				$msg = _T('cfg:config_supprimee', array('nom' => $this->nom_config()));
			} else {
				$msg = _T('cfg:erreur_suppression', array('nom' => $this->nom_config()));
			}
			$this->message .= $msg;
			$this->log($msg);
		}
		
		// sinon verifier que le controle
		// n'a pas retourne de message d'erreur
		//
		// /!\ cela implique d'avoir lance $this->verifier()
		// a un moment donne (#FORMULAIRE_CFG le teste dans valider.php)
		// $this->message sera vide systematiquement si verifier() n'est pas execute
		else if ($this->message) {
		}
		
		// si elles ont changees, on modifie !
		else {
			$ok = $this->sto->modifier();
			$this->message .= ($msg = $ok 
						? _T('cfg:config_enregistree', array('nom' => $this->nom_config())) 
						: _T('cfg:erreur_enregistrement', array('nom' => $this->nom_config())));
			$this->log($msg . ' ' . $this->log_modif);
		}

		// pipeline 'cfg_post_edition'
		$this->message = pipeline('cfg_post_edition',array('args'=>array('nom_config'=>$this->nom_config()),'data'=>$this->message));
	}


	/*
	 * Enregistre les changements proposes
	 * si l'on est bien authentifie (action)
	 */
	function enregistrer(){

		// enregistrement ou suppression ?
		$enregistrer = $supprimer = false;
		if  (!_request('_cfg_ok') &&  !_request('_cfg_delete')) 
			return false;
	
		if  ((!$supprimer = _request('_cfg_delete')) && $this->message)
			return false;
	
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$securiser_action();
			
		// suppression
		if ($supprimer) {
			$this->modifier('supprimer');
		
		// sinon modification
		// seulement si les types de valeurs attendus sont corrects
		} elseif (!$this->message) {
			
			// lorsque c'est un champ de type multi que l'on modifie
			// et si l'identifiant a change, il faut soit le copier, soit de deplacer
			$this->new_id = implode('/', array_map('_request', $this->champs_id));
			if ($this->new_id != $this->cfg_id && !_request('_cfg_copier')) {
				$this->modifier('supprimer');
			}
			$this->cfg_id = $this->new_id;
			
			
			$this->modifier();
		}
		
		return true;		
	}
	
	
	
	/*
	 * Gere le traitement du formulaire.
	 * 
	 * Si le chargement ou le controle n'ont pas ete fait,
	 * la fonction s'en occupe.
	 * 
	 */
	function traiter()
	{
		if (!$this->charger) $this->charger();
		if (!$this->controler) $this->verifier();
		
		// est on autorise ?
		if (!$this->autoriser()) return;

		// enregistrer les modifs
		if (!$this->enregistrer())
			return;
			
		// Si le fond du formulaire demande expressement une redirection
		// par <!-- rediriger=1 -->, on stocke le message dans une meta
		// et on redirige le client, de maniere a charger la page
		// avec la nouvelle config (ce qui permet par exemple a Autorite
		// de controler d'eventuels conflits generes par les nouvelles autorisations)
		if ($this->rediriger && $this->message) {
			include_spip('inc/meta');
			ecrire_meta('cfg_message_'.$GLOBALS['auteur_session']['id_auteur'], $this->message, 'non');
			if (defined('_COMPAT_CFG_192')) ecrire_metas();
			include_spip('inc/headers');
			redirige_par_entete(parametre_url(self(),null,null,'&'));
		}
	}

	/*
	 * Doit controler la validite des valeurs transmises
	 * (le stockage de ces valeurs devrait etre ailleurs qu'ici)
	 * 
	 * Verifie les valeurs postees.
	 * - stocke les valeurs qui ont changees dans $this->val[$nom_champ] = 'nouvelle_valeur'
	 * - verifie que les types de valeurs attendus sont corrects ($this->types)
	 * 
	 * retourne les messages d'erreur
	 */
	function verifier() {
	    $erreurs = array();
	    
		if (!$this->charger) $this->charger();
		
		// si on a pas poste de formulaire, pas la peine de controler
		// ce qui mettrait de fausses valeurs dans l'environnement
		if  (!_request('_cfg_ok') && !_request('_cfg_delete')) return $erreurs;
		
		// stoockage des nouvelles valeurs
		foreach ($this->champs as $name => $def) {
			// enregistrement des valeurs postees
			$oldval = $this->val[$name];
		    $this->val[$name] = _request($name);
		    
		    // tracer les modifications
		    if ($oldval != $this->val[$name]) {
		    	$this->log_modif .= $name . ':' . var_export($oldval, true) . '/' . var_export($this->val[$name], true) .', ';
		    }
		    
		    // tester la validite des champs
		    // (TODO: scinder $erreurs et $this->message)
		    if ($erreur = $this->controler_champ($name)) {
		    	$this->message .= $erreur."<br />\n";
		    	$erreurs[$name] = $erreur;
		    }
	    }
		
		// si pas de changement, pas la peine de continuer
		if (!$this->log_modif) {
			$this->message .= _T('cfg:pas_de_changement', array('nom' => $this->nom_config()));
			$erreurs['message_erreur'] = _T('cfg:pas_de_changement', array('nom' => $this->nom_config()));
		}

		// stocker le fait que l'on a controle les valeurs
		$this->controler = true;
			
	    return $erreurs;
	}
	
	
	// verification du type de valeur attendue
	// cela est defini par un nom de class css (class="type_idnum")
	// 'idnum' etant defini dans $this->types['idnum']...
	// si le nom du champ possede une traduction, il sera traduit.
	//
	// API a revoir, les controles sont trop sommaire,
	// il faut pouvoir tester une plage de valeur par exemple, simplement
	// une preg n'est pas ideale
	// De plus, le multilinguisme n'est pas fait.
	function controler_champ($name){
		$type = $this->champs[$name]['typ'];
		if (!empty($type) && isset($this->types[$type])) {
			$dtype = $this->types[$type];
			if (!preg_match($dtype[0], $this->val[$name])) {
				// erreur
				return $name . '&nbsp;:' . $dtype[1];
			}
		}
		// pas d'erreur ou pas de test
		return;
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

		$contexte['_cfg_'] = $this->creer_hash_cfg();

		// recuperer le fond avec le contexte
		// forcer le calcul.
		$this->recuperer_fond($contexte, true);
		$this->effacer_parametres(); // pour enlever les <!-- param=valeur --> ... sans dedoubler le contenu lorsque ce sont des tableau (param*=valeur)
		return $this->fond_compile;
	}
	
	
	function creer_hash_cfg(){
		include_spip('inc/securiser_action');
	    $arg = 'cfg0.0.0-' . $this->nom . '-' . $this->vue;
		return 
			'?exec=cfg&cfg=' . $this->nom .
			'?cfg=' . $this->nom .
			'&cfg_vue=' . $this->vue .
			'&cfg_id=' . $this->cfg_id .
			'&base_url=' . $this->base_url .
		    '&lang=' . $GLOBALS['spip_lang'] .
		    '&arg=' . $arg .
		    '&hash=' .  calculer_action_auteur('-' . $arg);		
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
	 * par <!-- param=valeur -->
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


function cfg_get_formulaire($cfg, $cfg_id=""){
	return new cfg_formulaire($cfg, $cfg_id);
}
?>
