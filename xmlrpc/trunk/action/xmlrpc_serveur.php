<?php
/**
 * Plugin xmlrpc
 * 
 * Auteurs : kent1 (http://www.kent1.info)
 * © 2011-2012 - GNU/GPL v3
 * 
 * Action serveur xml-rpc
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_xmlrpc_serveur_dist(){
	if(_request('rsd')){
		$rsd = recuperer_fond('inclure/inc-rsd');
		echo $rsd;
		exit;
	}
	
	global $HTTP_RAW_POST_DATA;
	if (!isset($HTTP_RAW_POST_DATA)){
		$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
	}
	
	if ( isset($HTTP_RAW_POST_DATA) )
		$HTTP_RAW_POST_DATA = trim($HTTP_RAW_POST_DATA);

	include_spip('lib/ixr/ixr_library');
	include_spip('inc/utils');
	include_spip('inc/filtres');
	include_spip('inc/lien');
	include_spip('inc/autoriser');
	include_spip('public/quete');
	
	class spip_xmlrpc_server extends IXR_Server {
		/**
		 * Chargement des méthodes disponibles
		 */
		function __construct() {
			$this->methodes = array(
				/**
				 * SPIP API
				 */
	            'spip.auth' => 'this:auth',
	            'spip.logout' => 'this:logout',
	            'spip.creer_objet' => 'this:spip_creer_objet',
	            'spip.lire_objet' => 'this:spip_lire_objet',
	            'spip.supprimer_objet' => 'this:spip_supprimer_objet',
	            'spip.modifier_objet' => 'this:spip_modifier_objet',
	            'spip.lire_article' => 'this:spip_lire_article',
	            'spip.lire_auteur' => 'this:spip_lire_auteur',
	            'spip.lire_document' => 'this:spip_lire_document',
	            'spip.lire_forum' => 'this:spip_lire_forum',
	            'spip.lire_rubrique' => 'this:spip_lire_rubrique',
	            'spip.lire_mot' => 'this:spip_lire_mot',
	            'spip.liste_articles' => 'this:spip_liste_articles',
	            'spip.liste_auteurs' => 'this:spip_liste_auteurs',
	            'spip.liste_documents' => 'this:spip_liste_documents',
	            'spip.liste_mots' => 'this:spip_liste_mots',
	            'spip.liste_rubriques' => 'this:spip_liste_rubriques',
	            'spip.liste_forums' => 'this:spip_liste_forums'
	        );
			$this->methodes = pipeline('xmlrpc_methodes',$this->methodes);
		}
		
		/**
		 * Fonction de démarage du serveur
		 */
		function serve_request() {
			$this->IXR_Server($this->methodes);
		}
		
		/**
		 * Fonction qui regarde si le serveur est fermé ...
		 * 
		 * @return bool : true si pas fermé, false s'il l'est
		 */
		function verifier_access(){
			$options = @unserialize($GLOBALS['meta']['xmlrpc']);
			if(!is_array($options))
				$options = array();
			
			if($options['ferme'] == 'on'){
				$this->error = new IXR_Error( 405, texte_backend(_T('xmlrpc:erreur_xmlrpc_desactive')));
				return false;
			}
			return true;
		}
		
		/**
		 * Fonction d'identification
		 * 
		 * NB : si on a un cookie d'itenfication et une session valide, celle ci est utilisée
		 * 
		 * Arguments :
		 * -* 0 string : le login de l'utilisateur
		 * -* 1 string : le mot de passe
		 * ex : array('mon login','mon_pass');
		 * return : l'array de la session de l'utilisateur 
		 * ou un bool false (0) en cas d'échec
		 */
		function auth($args=array()){
			$auth_methode = 'spip';
			if(is_array($GLOBALS['visiteur_session']) && $GLOBALS['visiteur_session'] > 1){
				return $GLOBALS['visiteur_session'];
			}
			$login = $args[0];
			include_spip('inc/auth');
			$res = auth_identifier_login(trim($login), trim($args[1]));
			/**
			 * Si on n'a pas un array => mauvaise identification
			 */
			if(!is_array($res)){
				$erreur = attribut_html(_T('xmlrpc:erreur_mauvaise_identification'));
				$this->error = new IXR_Error(403,$erreur);
				return false;
			}
			// sinon on loge l'auteur identifie, et on finit en envoyant les infos de l'auteur
			auth_loger($res);
			return $this->spip_lire_auteur(array('id_auteur'=>$res['id_auteur']));
		}
		
		/**
		 * Fonction de logout
		 */
		function logout(){
			global $visiteur_session;
			if (is_numeric($visiteur_session['id_auteur'])) {
				include_spip('inc/auth');
				auth_trace($visiteur_session, '0000-00-00 00:00:00');
				// le logout explicite vaut destruction de toutes les sessions
				if (isset($_COOKIE['spip_session'])) {
					$session = charger_fonction('session', 'inc');
					$session($visiteur_session['id_auteur']);
					spip_setcookie('spip_session', $_COOKIE['spip_session'], time()-3600);
				}
			}
			return true;
		}
		
		function read($args){
			$crud = charger_fonction('crud','action');
			$res = $crud('read',$args['objet'],$args['id_objet']);
			if($res['success'] != 1 OR !$res['success']){
				$erreur = strlen($res['message'])?$res['message']:_T('xmlrpc:erreur_lecture',array('objet'=>$args['objet'],'id_objet' => $args['id_objet']));
				$this->error = new IXR_Error(-32601, texte_backend($erreur));
				return false;
			}
			/**
			 * Si on a un argument "champs_demandes", on enlève du résultat ce qui n'y parait pas
			 */
			if(is_array($args['champs_demandes']) && (count($args['champs_demandes']) > 0)){
				foreach ($res['result'][0] as $champ => $valeur){
					if(!in_array($champ,array(id_table_objet($args['objet']))) && !in_array($champ,$args['champs_demandes']))
						unset($res['result'][0][$champ]);
				}
			}
			return $res;
		}
		
		function create($args){
			if(!is_array($args['set'])){
				$this->error = new IXR_Error(-32601, texte_backend(_T('xmlrpc:erreur_argument_set')));
				return false;
			}
			$crud = charger_fonction('crud','action');
			$res = $crud('create',$args['objet'],$args['id_objet'],$args['set']);
			if($res['success'] != 1 OR !$res['success']){
				$this->error = new IXR_Error(-32601, texte_backend($res['message']));
				return false;
			}

			return $res;
		}
		
		function update($args){
			if(!is_array($args['set'])){
				$this->error = new IXR_Error(-32601, texte_backend(_T('xmlrpc:erreur_argument_set')));
				return false;
			}
			$crud = charger_fonction('crud','action');
			$res = $crud('update',$args['objet'],$args['id_objet'],$args['set']);
			if($res['success'] != 1 OR (!$res['success'])){
				$this->error = new IXR_Error(-32601, texte_backend($res['message']));
				return false;
			}
			return $res;
		}
		
		function delete($args){
			$crud = charger_fonction('crud','action');
			$res = $crud('delete',$args['objet'],$args['id_objet']);
			if($res['success'] != 1){
				$this->error = new IXR_Error(-32601, texte_backend($res['message']));
				return false;
			}
			if($res['result']['row'] == false){
				$message = _T('crud:erreur_edition_objet',array('objet'=>$args['objet'],'id'=>$args['id_objet']));
				return new IXR_Error(-32601, attribut_html($message));
			}
			return $res;
		}
		
		/**
		 * Crée un objet
		 * On vérifie si une fonction spécifique à un objet est disponible
		 * sinon on tente un create() sur le type d'objet
		 * 
		 * Paramètres :
		 * -* login string
		 * -* pass string
		 * -* objet string : obligatoire
		 * -* set array : obligatoire - le contenu des champs à mettre en base
		 */
		function spip_creer_objet($args){
			if(!$args['objet'] OR !is_array($args['set']))
				return new IXR_Error(-32601, _T('xmlrpc:erreur_arguments_obligatoires',array('arguments'=>'objet, set')));
			
			$methode = 'spip.creer_'.$args['objet'];
			
			foreach($args['set'] as $key => $value){
				if(is_numeric($key)){
					if(preg_match('/=/',$value)){
						$values = explode('=',$value,2);
						$set[$values[0]] = $values[1];
					}else
						return new IXR_Error(-32601, _T('xmlrpc:erreur_array'));
				}else{
					$set[$key] = $value;
				}
			}
			$args['set'] = $set;
			/**
			 * On regarde si la méthode spip.creer_$args['objet'] existe ... 
			 * On l'utilise si possible
			 */
			if ($this->hasMethod($methode)) {
				$struct = $this->call($methode, $args);
			}
			else {
				$struct = $this->create($args);
				if(!$struct)
					return $this->error ? $this->error : new IXR_Error(-32601, _T('xmlrpc:erreur_impossible_creer_objet',$args));
            }
			if(isset($struct['result']))
				$struct = $struct['result']['row'];
			return $struct;
		}

		/**
		 * Met à jour un objet
		 * On vérifie si une fonction spécifique à un objet est disponible
		 * sinon on tente un update() sur l'objet spécifié
		 * 
		 * Paramètres :
		 * -* login string
		 * -* pass string
		 * -* objet string : obligatoire
		 * -* id_objet int : obligatoire
		 * -* set array : obligatoire - les champs à mettre à jour
		 */
		function spip_modifier_objet($args){
			if(!$args['objet'] OR !intval($args['id_objet']) OR !is_array($args['set']))
				return new IXR_Error(-32601, _T('xmlrpc:erreur_arguments_obligatoires',array('arguments'=>'objet,id_objet,set')));
			
			$methode = 'spip.modifier_'.$args['objet'];
			
			foreach($args['set'] as $key => $value){
				if(is_numeric($key)){
					if(preg_match('/=/',$value)){
						$values = explode('=',$value,2);
						$set[$values[0]] = $values[1];
					}else
						return new IXR_Error(-32601, _T('xmlrpc:erreur_array'));
				}else{
					$set[$key] = $value;
				}
			}
			$args['set'] = $set;

			/**
			 * On regarde si la méthode spip.modifier_$args['objet'] existe ... 
			 * On l'utilise si possible
			 */
			if ($this->hasMethod($methode)) {
				$struct = $this->call($methode, $args);
			}
			else {
				$struct = $this->update($args);
				if(!$struct)
					return $this->error ? $this->error : new IXR_Error(-32601, _T('xmlrpc:erreur_impossible_modifier_objet',$args));
            }
			if(isset($struct['result']))
				$struct = $struct['result']['row'];
			return $struct;
		}
		
		/**
		 * Supprime un objet
		 * On vérifie si une fonction spécifique à un objet est disponible
		 * sinon on tente un delete() sur l'objet en question
		 * 
		 * Paramètres :
		 * -* login string
		 * -* pass string
		 * -* objet string : obligatoire
		 * -* id_objet int : obligatoire
		 */
		function spip_supprimer_objet($args){
			if(!$args['objet'] OR !intval($args['id_objet']))
				return new IXR_Error(-32601, _T('xmlrpc:erreur_arguments_obligatoires',array('arguments'=>'objet,id_objet')));
			
			$methode = 'spip.supprimer_'.$args['objet'];
			
			/**
			 * On regarde si la méthode spip.creer_$args['objet'] existe ... 
			 * On l'utilise si possible
			 */
			if ($this->hasMethod($methode)) {
				$struct = $this->call($methode, $args);
			}
			else {
				$struct = $this->create($args);
				if(!$struct)
					return new IXR_Error(-32601, _T('xmlrpc:erreur_impossible_supprimer_objet',$args));
            }
			if(isset($struct['result']))
				$struct = $struct['result'][0];
			return $struct;
		}

		/**
		 * Récupère le contenu d'un objet
		 * On vérifie si une fonction spécifique à un objet est disponible
		 * sinon on tente un read() sur l'objet en paramètre
		 * 
		 * Paramètres :
		 * -* login string
		 * -* pass string
		 * -* objet string : obligatoire
		 * -* id_objet int : obligatoire
		 */
		function spip_lire_objet($args){
			if(!$args['objet'] OR !$args['id_objet'])
				return new IXR_Error(-32601, _T('xmlrpc:erreur_arguments_obligatoires',array('arguments'=>'objet, id_objet')));
			
			$args[id_table_objet($args['objet'])] = $args['id_objet'];
			$methode = 'spip.lire_'.$args['objet'];
			
			/**
			 * On regarde si la méthode spip.lire_$args['objet'] existe ... 
			 * On l'utilise si possible
			 */
			if ($this->hasMethod($methode)) {
				$struct = $this->call($methode, $args);
			}
			else {
				$struct = $this->read($args);
				if(!$struct)
					return new IXR_Error(-32601, _T('xmlrpc:erreur_impossible_lire_objet',$args));
            }
			if(isset($struct['result']))
				$struct = $struct['result'][0];
			return $struct;
		}
		
		/**
		 * Récupère le contenu d'un article
		 * 
		 * Arguments possibles :
		 * -* login string
		 * -* pass string
		 * -* id_article int : obligatoire
		 */
		function spip_lire_article($args){
			if(!intval($args['id_article']) > 0){
				$erreur = _T('xmlrpc:erreur_identifiant',array('objet'=>'article'));
				return new IXR_Error(-32601, attribut_html($erreur));
			}
			
			$from[] = 'spip_articles';
			$where[] = 'id_article='.intval($args['id_article']);
			
			$statut = sql_getfetsel('statut',$from,$where);
			$args_article = array_merge($args,array('id_objet'=>$args['id_article'],'objet'=>'article'));
			
			/**
			 * Si on est identifié
			 * Si on a un id_article non publié dans la requète on regarde si on a le droit de le modifier
			 */
			if(($statut != 'publie') && is_array($GLOBALS['visiteur_session']) && autoriser('modifier','article',$args['id_article'],$GLOBALS['visiteur_session'])){
				$res = $this->read($args_article);
				if(!$res)
					return $this->error;
				$logo = quete_logo('id_article','on', $res['result'][0]['id_article'], '', false);
				if(is_array($logo))
					$res['result'][0]['logo'] = url_absolue($logo[0]);
				$res['result'][0]['modifiable'] = 1;
				$article_struct = $res['result'][0];
			}
			/**
			 * Cas où l'on n'a pas de user/pass
			 * On liste l'article uniquement si publié
			 */
			else if($statut == 'publie'){
				$res = $this->read($args_article);
				if(!$res)
					return $this->error;
				$res['result'][0]['url'] = url_absolue(generer_url_entite($args['id_article'],'article'));
				$logo = quete_logo('id_article','on', $res['result'][0]['id_article'], '', false);
				if(is_array($logo))
					$res['result'][0]['logo'] = url_absolue($logo[0]);
				if(autoriser('modifier','article',$args['id_article'],$GLOBALS['visiteur_session']))
					$res['result'][0]['modifiable'] = 1;
				else
					$res['result'][0]['modifiable'] = 0;
				$article_struct = $res['result'][0];
			}
			/**
			 * Sinon on envoit une erreur
			 */
			else{
				$erreur = _T('xmlrpc:erreur_objet_inexistant',array('objet'=>'article','id_objet'=>$args['id_article']));
				return new IXR_Error(-32601, attribut_html($erreur));
			}
			return $article_struct;
		}

		/**
		 * Récupère le contenu d'un auteur
		 * 
		 * Arguments possibles :
		 * -* login string
		 * -* pass string
		 * -* id_auteur int : obligatoire
		 */
		function spip_lire_auteur($args){
			if(!intval($args['id_auteur']) > 0){
				$erreur = _T('xmlrpc:erreur_identifiant',array('objet'=>'auteur'));
				return new IXR_Error(-32601, attribut_html($erreur));
			}
			
			$from = 'spip_auteurs';
			$where = 'id_auteur='.intval($args['id_auteur']);
			
			$statut = sql_getfetsel('statut',$from,$where);
			$args_auteur = array_merge($args,array('id_objet'=>$args['id_auteur'],'objet'=>'auteur'));
			
			/**
			 * Si on est identifié
			 * Si on a un id_auteur dans la requète on liste les articles publiés de l'id_auteur en question
			 * Sinon on liste l'ensemble des articles de l'auteur logué qu'il soit publiés ou non
			 */
			if(autoriser('modifier','auteur',$args['id_auteur'],$GLOBALS['visiteur_session'])){
				$res = $this->read($args_auteur);
				if(!$res)
					return $this->error;
				$a_enlever = array(
							'en_ligne',
							'pass',
							'low_sec',
							'htpass',
							'alea_actuel',
							'alea_futur',
							'prefs',
							'cookie_oubli',
							'source',
							'extra',
							'composition',
							'composition_lock',
							'auth');
			
				foreach($a_enlever as $enleve){
					unset($res['result'][0][$enleve]);
				}
				if(in_array($statut,array("6forum","0minirezo","1comite")))
					$res['result'][0]['url'] = url_absolue(generer_url_entite($args['id_auteur'],'auteur'));
				$logo = quete_logo('id_auteur','on', $res['result'][0]['id_auteur'], '', false);
				if(is_array($logo))
					$res['result'][0]['logo'] = url_absolue($logo[0]);
				$res['result'][0]['modifiable'] = 1;
				$auteur_struct = $res['result'][0];
			}
			/**
			 * Cas où l'on n'a pas de user/pass
			 * On liste l'auteur uniquement s'il existe (pas dans la poubelle)
			 */
			else if(in_array($statut,array("6forum","0minirezo","1comite"))){
				$res = $this->read($args_auteur);
				if(!$res)
					return $this->error;
				$a_enlever = array(
							'en_ligne',
							'email',
							'login',
							'pass',
							'low_sec',
							'htpass',
							'alea_actuel',
							'alea_futur',
							'prefs',
							'cookie_oubli',
							'source',
							'extra',
							'composition',
							'composition_lock',
							'auth');
			
				foreach($a_enlever as $enleve){
					unset($res['result'][0][$enleve]);
				}
				$res['result'][0]['url'] = url_absolue(generer_url_entite($args['id_auteur'],'auteur'));
				$logo = quete_logo('id_auteur','on', $res['result'][0]['id_auteur'], '', false);
				if(is_array($logo))
					$res['result'][0]['logo'] = url_absolue($logo[0]);
				$res['result'][0]['modifiable'] = 0;
				$auteur_struct = $res['result'][0];
			}
			/**
			 * Sinon on envoit une erreur
			 */
			else{
				$erreur = _T('xmlrpc:erreur_objet_inexistant',array('objet'=>'auteur','id_objet'=>$args['id_auteur']));
				return new IXR_Error(-32601, attribut_html($erreur));
			}

			return $auteur_struct;
		}

		/**
		 * Récupère le contenu d'un document
		 * 
		 * Arguments possibles :
		 * -* login string
		 * -* pass string
		 * -* id_document : obligatoire
		 */
		function spip_lire_document($args){
			if(!intval($args['id_document']) > 0){
				$erreur = _T('xmlrpc:erreur_identifiant',array('objet'=>'document'));
				return new IXR_Error(-32601, attribut_html($erreur));
			}
			
			$from = 'spip_documents';
			$where = 'id_document='.intval($args['id_document']);
			
			$args_document = array_merge($args,array('id_objet'=>$args['id_document'],'objet'=>'document'));
			$res = $this->read($args_document);
			if(!$res){
				return $this->error;
			}

			include_spip('inc/documents');
			$res['result'][0]['fichier'] = url_absolue(get_spip_doc($res['result'][0]['fichier']));
			if(intval($res['result'][0]['id_vignette']) > 0)
				$res['result'][0]['vignette'] = url_absolue(get_spip_doc(sql_getfetsel('fichier','spip_documents','id_document='.intval($res['result'][0]['id_vignette']))));
			else
			if(autoriser('modifier','document',$args['id_document'],$GLOBALS['visiteur_session']))
				$res['result'][0]['modifiable'] = 1;
			else
				$res['result'][0]['modifiable'] = 0;

			$document_struct = $res['result'][0];
			
			return $document_struct;
		}

		/**
		 * Récupère le contenu d'une rubrique
		 * 
		 * Arguments possibles :
		 * -* login string
		 * -* pass string
		 * -* id_rubrique int : obligatoire
		 */
		function spip_lire_rubrique($args){
			if(!intval($args['id_rubrique']) > 0){
				$erreur = _T('xmlrpc:erreur_identifiant',array('objet'=>'rubrique'));
				return new IXR_Error(-32601, attribut_html($erreur));
			}
			
			$from = 'spip_rubriques';
			$where = 'id_rubrique='.intval($args['id_rubrique']);
			
			$statut = sql_getfetsel('statut',$from,$where);
			$args_rubrique = array_merge($args,array('id_objet'=>$args['id_rubrique'],'objet'=>'rubrique'));
			
			/**
			 * Si on est identifié
			 * Si on a un id_rubrique non publié dans la requète on regarde si on a le droit de créer un article dedans
			 */
			if(($statut != 'publie') && is_array($GLOBALS['visiteur_session']) && autoriser('creerarticledans','rubrique',$args['id_rubrique'],$GLOBALS['visiteur_session'])){
				$res = $this->read($args_rubrique);
				if(!$res)
					return $this->error;
				if(autoriser('modifier','rubrique',$args['id_rubrique'],$GLOBALS['visiteur_session']))
					$res['result'][0]['modifiable'] = 1;
				else
					$res['result'][0]['modifiable'] = 0;
				$logo = quete_logo('id_rubrique','on', $res['result'][0]['id_rubrique'], '', false);
				if(is_array($logo))
					$res['result'][0]['logo'] = url_absolue($logo[0]);
				$rubrique_struct = $res['result'][0];
			}
			/**
			 * Cas où l'on n'a pas de user/pass
			 * On liste la rubrique uniquement si publié
			 */
			else if($statut == 'publie'){
				$res = $this->read($args_rubrique);
				if(!$res)
					return $this->error;
				$res['result'][0]['url'] = url_absolue(generer_url_entite($args['id_rubrique'],'rubrique'));
				if(autoriser('modifier','rubrique',$args['id_rubrique'],$GLOBALS['visiteur_session']))
					$res['result'][0]['modifiable'] = 1;
				else
					$res['result'][0]['modifiable'] = 0;
				$logo = quete_logo('id_rubrique','on', $res['result'][0]['id_rubrique'], '', false);
				if(is_array($logo))
					$res['result'][0]['logo'] = url_absolue($logo[0]);
				$rubrique_struct = $res['result'][0];
			}
			/**
			 * Sinon on envoit une erreur
			 */
			else{
				$erreur = _T('xmlrpc:erreur_objet_inexistant',array('objet'=>'rubrique','id_objet'=>$args['id_rubrique']));
				return new IXR_Error(-32601, attribut_html($erreur));
			}

			return $rubrique_struct;
		}

		/**
		 * Récupère le contenu d'un mot
		 * 
		 * Arguments possibles :
		 * -* login string
		 * -* pass string
		 * -* id_mot int : obligatoire
		 */
		function spip_lire_mot($args){
			if(!intval($args['id_mot']) > 0){
				$erreur = _T('xmlrpc:erreur_identifiant',array('objet'=>'mot'));
				return new IXR_Error(-32601, attribut_html($erreur));
			}
			
			$args_mot = array_merge($args,array('objet'=>'mot','id_objet'=>$args['id_mot']));
			$res = $this->read($args_mot);
			
			if(!$res)
				return $this->error;
			if(autoriser('modifier','mot',$args['id_mot'],$GLOBALS['visiteur_session']))
				$res['result'][0]['modifiable'] = 1;
			else
				$res['result'][0]['modifiable'] = 0;
			$logo = quete_logo('id_mot','on', $res['result'][0]['id_mot'], '', false);
			if(is_array($logo))
				$res['result'][0]['logo'] = url_absolue($logo[0]);
			$mot_struct = $res['result'][0];

			return $mot_struct;
		}
		/**
		 * Récupère le contenu d'un forum
		 * 
		 * Arguments possibles :
		 * -* login string
		 * -* pass string 
		 * -* id_forum int : obligatoire
		 */
		function spip_lire_forum($args){
			if(!intval($args['id_forum']) > 0){
				$erreur = _T('xmlrpc:erreur_identifiant',array('objet'=>'forum'));
				return new IXR_Error(-32601, attribut_html($erreur));
			}
			
			$args_forum = array_merge($args,array('objet'=>'forum','id_objet'=>$args['id_forum']));
			$res = $this->read($args_forum);
			
			if(!$res)
				return $this->error;
			if(autoriser('modifier','forum',$args['id_forum'],$GLOBALS['visiteur_session']))
				$res['result'][0]['modifiable'] = 1;
			else
				$res['result'][0]['modifiable'] = 0;

			$forum_struct = $res['result'][0];

			return $forum_struct;
		}
		/**
		 * Récupère la liste des objet
		 * On vérifie si une fonction spécifique à un objet est disponible
		 * sinon on tente un read() sur l'objet en paramètre
		 */
		function spip_liste_objets($args){
			if(!$args['objet'] OR !$args['id_objet'])
				return new IXR_Error(-32601, _T('xmlrpc:erreur_arguments_obligatoires',array('arguments'=>'objet, id_objet')));
			
			$args[id_table_objet($args['objet'])] = $args['id_objet'];
			$methode = 'spip.lire_'.$args['objet'];
			
			/**
			 * On regarde si la méthode spip.lire_$args['objet'] existe ... 
			 * On l'utilise si possible
			 */
			if ($this->hasMethod($methode)) {
				$struct = $this->call($methode, $args);
			}
			else {
				$struct = $this->read($args);
				if(!$struct)
					return new IXR_Error(-32601, _T('xmlrpc:erreur_impossible_lire_objet',$args));
            }
			return $struct;
		}
		/**
		 * Récupère la liste des articles
		 * 
		 * Arguments possibles :
		 * -* login string
		 * -* pass string
		 * -* id_rubrique int
		 * -* id_secteur int
		 * -* id_auteur int
		 * -* where array : conditions à ajouter dans la clause where du select
		 * -* tri array (un array de champs pour trier)
		 * -* limite int
		 */
		function spip_liste_articles($args){
			$objet = 'article';
			
			$what[] = 'articles.id_article';
			
			if (version_compare($GLOBALS['spip_version_branche'], '2.3', '>=')){
				$from = 'spip_articles as articles LEFT JOIN spip_auteurs_liens AS auteurs ON articles.id_article=auteurs.id_objet AND auteurs.objet="article"';
			}else{
				$from = 'spip_articles as articles LEFT JOIN spip_auteurs_articles AS auteurs ON articles.id_article=auteurs.id_article';
			}
			$where = is_array($args['where']) ? $args['where'] : array();
			$order = is_array($args['tri']) ? $args['tri'] : array('!date');
			
			if(intval($args['id_rubrique'])){
				$where[] = 'articles.id_rubrique='.intval($args['id_rubrique']);
			}
			if(intval($args['id_secteur'])){
				$where[] = 'articles.id_secteur='.intval($args['id_secteur']);
			}
			if(intval($args['id_auteur'])){
				$where[] = 'auteurs.id_auteur='.intval($args['id_auteur']);
			}
			
			if(is_string($args['recherche']) AND strlen($args['recherche']) > 3){
				$prepare_recherche = charger_fonction('prepare_recherche', 'inc');
				list($rech_select, $rech_where) = $prepare_recherche($args['recherche'], $objet, $where);
				$what[] = $rech_select;
				$from .= ' INNER JOIN spip_resultats AS resultats ON ( resultats.id = articles.id_article ) ';
				$where[] = 'resultats.'.$rech_where;
			}
			
			$articles_struct = array();
			
			/**
			 * Si on est identifié
			 * Si on a un id_auteur dans la requète on liste les articles publiés de l'id_auteur en question
			 * Sinon on liste l'ensemble des articles de l'auteur logué qu'il soit publiés ou non
			 */
			if(is_array($GLOBALS['visiteur_session'])){
				$articles_struct = array();
				if(!$args['id_auteur']){
					$where[] = 'auteurs.id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']);
				}else{
					$where[] = 'articles.statut="publie"';
				}
			}
			/**
			 * Cas où l'on n'a pas de user/pass
			 * On liste les articles publiés uniquement
			 */
			else{
				$where[] = 'articles.statut="publie"';
			}

			if($arts = sql_select($what,$from,$where,array(),$order,$args['limite'])){
				while($art = sql_fetch($arts)){
					$struct=array();
					$args['id_article'] = $art['id_article'];
					/**
					 * On utilise la fonction lire_article pour éviter de dupliquer trop de code
					 */
					$struct = $this->spip_lire_article($args);
					$articles_struct[] = $struct;
				}
			}

			return $articles_struct;
		}
		
		/**
		 * Récupère la liste des auteurs 
		 * 
		 * Arguments possibles :
		 * -* statut
		 * -* recherche
		 * -* where array : conditions à ajouter dans la clause where du select
		 * -* tri array (un array de champs pour trier)
		 * -* limite int : le nombre de résultats à retourner
		 */
		function spip_liste_auteurs($args){
			$objet = 'auteur';
			
			$what[] = 'auteurs.id_auteur';
			$from = 'spip_auteurs AS auteurs';
			$where = is_array($args['where']) ? $args['where'] : array();
			$order = is_array($args['tri']) ? $args['tri'] : array('!id_auteur');
			
			if(is_string($args['recherche']) AND strlen($args['recherche']) > 3){
				$prepare_recherche = charger_fonction('prepare_recherche', 'inc');
				list($rech_select, $rech_where) = $prepare_recherche($args['recherche'], $objet, $where);
				$what[] = $rech_select;
				$from .= ' INNER JOIN spip_resultats AS resultats ON ( resultats.id = auteurs.id_auteur ) ';
				$where[] = 'resultats.'.$rech_where;
			}
			$auteurs_struct = array();
			
			/**
			 * Si on est identifié
			 * Si on a le droit de modifier l'auteur en question, on affiche son mail et son login
			 */
			if(is_array($GLOBALS['visiteur_session'])){
				$where[] = 'auteurs.statut IN ("6forum","0minirezo","1comite")';
			}else{
				$where[] = 'auteurs.statut IN ("6forum","0minirezo","1comite")'; 
			}
			if($auts = sql_select($what,$from,$where,array(),$order,$args['limite'])){
				while($aut = sql_fetch($auts)){
					$struct=array();
					$args['id_auteur'] = $aut['id_auteur'];
					/**
					 * On utilise la fonction lire_auteur pour éviter de dupliquer trop de code
					 */
					$struct = $this->spip_lire_auteur($args);
					$auteurs_struct[] = $struct;
				}
			}
			return $auteurs_struct;
		}

		/**
		 * Récupère la liste des documents
		 * 
		 * Arguments possibles :
		 * -* login string
		 * -* pass string
		 * -* id_objet
		 * -* objet
		 * -* where array : conditions à ajouter dans la clause where du select
		 * -* recherche
		 * -* tri array (un array de champs pour trier)
		 * -* limite int : le nombre de résultats à retourner
		 */
		function spip_liste_documents($args){
			$objet = 'document';
			
			$what[] = 'documents.id_document';
			$from = 'spip_documents as documents LEFT JOIN spip_documents_liens as lien ON documents.id_document=lien.id_document';
			$where = is_array($args['where']) ? $args['where'] : array();
			$order = is_array($args['tri']) ? $args['tri'] : array('!id_document');
			
			if(intval($args['id_objet']) && $args['objet']){
				$where[] = 'lien.id_objet='.intval($args['id_objet']).' AND lien.objet='.sql_quote($args['objet']);
			}
			
			if(is_string($args['recherche']) AND strlen($args['recherche']) > 3){
				$prepare_recherche = charger_fonction('prepare_recherche', 'inc');
				list($rech_select, $rech_where) = $prepare_recherche($args['recherche'], $objet, $where);
				$what[] = $rech_select;
				$from .= ' INNER JOIN spip_resultats AS resultats ON ( resultats.id = documents.id_document ) ';
				$where[] = 'resultats.'.$rech_where;
			}
			
			$documents_struct = array();
	
			if($documents = sql_select($what,$from,$where,array(),$order,$args['limite'])){
				while($document = sql_fetch($documents)){
					$struct=array();
					$args['id_document'] = $document['id_document'];
					/**
					 * On utilise la fonction lire_document pour éviter de dupliquer trop de code
					 */
					$struct = $this->spip_lire_document($args);
					$documents_struct[] = $struct;
				}
			}

			return $documents_struct;
		}

		/**
		 * Récupère la liste des mots clés
		 * 
		 * Arguments possibles :
		 * -* login string
		 * -* pass string
		 * -* id_groupe
		 * -* where array : conditions à ajouter dans la clause where du select
		 * -* recherche
		 * -* tri array (un array de champs pour trier)
		 * -* limite int : le nombre de résultats à retourner
		 */
		function spip_liste_mots($args){
			$objet = 'mot';
			
			$what[] = 'mots.id_mot';
			$from = 'spip_mots as mots';
			$where = is_array($args['where']) ? $args['where'] : array();
			$order = is_array($args['tri']) ? $args['tri'] : array('!id_mot');
			
			if(intval($args['id_groupe'])){
				$where[] = 'mots.id_groupe='.intval($args['id_groupe']);
			}
			
			if(is_string($args['recherche']) AND strlen($args['recherche']) > 3){
				$prepare_recherche = charger_fonction('prepare_recherche', 'inc');
				list($rech_select, $rech_where) = $prepare_recherche($args['recherche'], $objet, $where);
				$what[] = $rech_select;
				$from .= ' INNER JOIN spip_resultats AS resultats ON ( resultats.id = rubriques.id_rubrique ) ';
				$where[] .= 'resultats.'.$rech_where;
			}

			$mots_struct = array();
	
			if($mots = sql_select($what,$from,$where,array(),$order,$args['limite'])){
				while($mot = sql_fetch($mots)){
					$struct=array();
					$args['id_mot'] = $mot['id_mot'];
					/**
					 * On utilise la fonction lire_mot pour éviter de dupliquer trop de code
					 */
					$struct = $this->spip_lire_mot($args);
					$mots_struct[] = $struct;
				}
			}

			return $mots_struct;
		}

		/**
		 * Récupère la liste des rubriques
		 * 
		 * Arguments possibles :
		 * -* id_parent
		 * -* id_secteur
		 * -* login string
		 * -* pass string
		 * -* where array : conditions à ajouter dans la clause where du select
		 * -* recherche
		 * -* tri array (un array de champs pour trier)
		 * -* limite int : le nombre de résultats à retourner
		 */
		function spip_liste_rubriques($args){
			$objet = 'rubrique';
			
			$where = is_array($args['where']) ? $args['where'] : array();
			$where[] = 'rubriques.id_rubrique > 0';
			$what[] = 'rubriques.id_rubrique';
			$from = 'spip_rubriques AS rubriques';
			$order = is_array($args['tri']) ? $args['tri'] : array('!id_rubrique');
			
			if(intval($args['id_parent'])){
				$where[] = 'rubriques.id_parent='.intval($args['id_parent']);
			}
			if(intval($args['id_secteur'])){
				$where[] = 'rubriques.id_secteur='.intval($id_secteur);
			}
			
			if(is_string($args['recherche']) AND strlen($args['recherche']) > 3){
				$prepare_recherche = charger_fonction('prepare_recherche', 'inc');
				list($rech_select, $rech_where) = $prepare_recherche($args['recherche'], $objet, $where);
				$what = $rech_select;
				$from .= ' INNER JOIN spip_resultats AS resultats ON ( resultats.id = rubriques.id_rubrique ) ';
				$where[] = 'resultats.'.$rech_where;
			}
			
			$categories_struct = array();
			
			if(is_array($GLOBALS['visiteur_session'])){
				
			}
			/**
			 * Cas où l'on n'a pas de user/pass
			 * On liste les rubriques publiées
			 */
			else{
				
			}
			if($cats = sql_select($what,$from,$where,array(),$order,$args['limite'])){
				while($cat = sql_fetch($cats)){
					$struct=array();
					if(autoriser('creerarticledans','rubrique',$cat['id_rubrique'],$GLOBALS['visiteur_session'])){
						$args['id_rubrique'] = $cat['id_rubrique'];
						/**
						 * On utilise la fonction lire_mot pour éviter de dupliquer trop de code
						 */
						$struct = $this->spip_lire_rubrique($args);
						$categories_struct[] = $struct;
					}
				}
			}
			return $categories_struct;
		}

		/**
		 * Récupère la liste des forums
		 * 
		 * Arguments possibles :
		 * -* id_thread
		 * -* recherche
		 * -* login string
		 * -* pass string
		 * -* where array : conditions à ajouter dans la clause where du select
		 * -* tri array (un array de champs pour trier)
		 * -* limite int : le nombre de résultats à retourner
		 */
		function spip_liste_forums($args){
			$objet = 'forum';
			
			$what[] = 'forums.id_forum'; 
			$from = 'spip_forum as forums';
			$where = is_array($args['where']) ? $args['where'] : array();
			$order = is_array($args['tri']) ? $args['tri'] : array('!id_forum');
			
			if(is_string($args['recherche']) AND strlen($args['recherche']) > 3){
				$prepare_recherche = charger_fonction('prepare_recherche', 'inc');
				list($rech_select, $rech_where) = $prepare_recherche($args['recherche'], $objet, $where,null,true);
				$what[] = $rech_select;
				$from .= ' INNER JOIN spip_resultats AS resultats ON ( resultats.id = forums.id_forum ) ';
				$where[] = $rech_where;
			}
			
			if($args['id_auteur']){
				$where[] = 'forums.id_auteur='.intval($args['id_auteur']);
			}
			$forums_struct = array();

			$where[] = 'forums.statut="publie"';
				
			if($forums = sql_select($what,$from,$where,array(),$order,$args['limite'])){
				while($forum = sql_fetch($forums)){
					$struct=array();
					$args['id_forum'] = $forum['id_forum'];
					/**
					 * On utilise la fonction lire_forum pour éviter de dupliquer trop de code
					 */
					$struct = $this->spip_lire_forum($args);
					$forums_struct[] = $struct;
				}
			}
			return $forums_struct;
		}
	}

	/**
	 * On lance le serveur
	 */
	pipeline('xmlrpc_server_class');
	$server = $GLOBALS['spip_xmlrpc_serveur'] = new spip_xmlrpc_server();
	$server->serve_request();
}
?>