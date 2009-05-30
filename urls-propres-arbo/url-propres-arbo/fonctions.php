<?php
	function url_a_la_racine ( $url ) {
        if ( $url == '' )
            return '';
        $racine = $GLOBALS['meta']['adresse_site'];
        $suffixe = preg_replace(',^.*(ecrire/|spip.php)?([^/]*)$,U', "\\1\\2", $url);
        return "$racine/$suffixe";
    }

	function balise_SELF($p)
	{	
		$p->code = 'quote_amp($_SERVER[\'REQUEST_URI\'])';
		$p->interdire_scripts = false;
		return $p;
	}
	
	function balise_ENV($p, $src = NULL) {
		// le tableau de base de la balise (cf #META ci-dessous)
		if (!$src) $src = '$Pile[0]';
	
		$_nom = interprete_argument_balise(1,$p);
		$_sinon = interprete_argument_balise(2,$p);
	
		if (!$_nom) {
			// cas de #ENV sans argument : on retourne le serialize() du tableau
			// une belle fonction [(#ENV|affiche_env)] serait pratique
			$p->code = 'serialize('.$src.')';
		} else {
			if($_nom=='\'self\'')
			{	$p = balise_SELF($p);	}
			else
			{
				// admet deux arguments : nom de variable, valeur par defaut si vide
				$p->code = $src."[$_nom]";
				if ($_sinon)
					$p->code = 'sinon('. 
						$p->code.",$_sinon)";
			}
		}
		#$p->interdire_scripts = true;

		return $p;
	}

	function public_parametrer($fond, $local='', $cache='')  {
		// verifier que la fonction assembler est bien chargee (cf. #608)
		$assembler = charger_fonction('assembler', 'public');
	
		// distinguer le premier appel des appels par inclusion
		if (!is_array($local)) {
			global $contexte;
			// ATTENTION, gestion des URLs personnalises (propre etc):
			// 1. $contexte est global car cette fonction le modifie.
			// 2. $fond est passe par reference, pour la meme raison
			// Bref,  les URL dites propres ont une implementation sale.
			// Interdit de nettoyer, faut assumer l'histoire.
			include_spip('inc/filtres'); // pour normaliser_date
			$contexte = calculer_contexte();
			if (function_exists("recuperer_parametres_url")) {
				recuperer_parametres_url($fond, nettoyer_uri());
				// remettre les globales (bouton "Modifier cet article" etc)
				foreach ($contexte as $var=>$val) {
					if (substr($var,0,3) == 'id_') $GLOBALS[$var] = $val;
				}
			}
			$local = $contexte;
	
			// si le champ chapo commence par '=' c'est une redirection.
			// avec un eventuel raccourci Spip
			// si le raccourci a un titre il sera pris comme corps du 302
			if ($fond == 'article'
			AND $id_article = intval($local['id_article'])) {
				$m = sql_chapo($id_article);
				if ($m[0]=='=') {
					include_spip('inc/texte');
					// les navigateurs pataugent si l'URL est vide
					if ($m = chapo_redirige(substr($m,1)))
						if ($url = calculer_url($m[3]))
						{
							if(!strstr($url,'http://'))
							{	$url=$GLOBALS['meta']['adresse_site'].'/'.$url;	}
							return array('texte' => "<"
						. "?php header('Location: "
						. texte_script(str_replace('&amp;', '&', $url))
						. "'); echo '"
						.  addslashes($m[1])
						. "'?" . ">",
							'process_ins' => 'php');
						}
				}
			}
		}
	
		// Choisir entre $fond-dist.html, $fond=7.html, etc?
		$id_rubrique_fond = 0;
		// Chercher le fond qui va servir de squelette
		if ($r = sql_rubrique_fond($local))
			list($id_rubrique_fond, $lang) = $r;
	
		// Si inc-urls ou un appel dynamique veut fixer la langue, la recuperer
		if (isset($local['lang']))
			$lang = $local['lang'];
	
		if (!isset($lang))
			$lang = $GLOBALS['meta']['langue_site'];
	
		if(!_DIR_PLUGIN_SQUELETTESMOTS){
			$lang_select = false;		
		}
		
		
		if (!$GLOBALS['forcer_lang']
		AND $lang <> $GLOBALS['spip_lang']
		) {
			lang_select($lang);
			$lang_select = true;
		}
	
		$styliser = charger_fonction('styliser', 'public');
		if(_DIR_PLUGIN_SQUELETTESMOTS){
			list($skel,$mime_type, $gram, $sourcefile) =
				$styliser($fond, $id_rubrique_fond, $GLOBALS['spip_lang'], $local);
		}else{
			list($skel,$mime_type, $gram, $sourcefile) =
				$styliser($fond, $id_rubrique_fond, $GLOBALS['spip_lang']);
		}
		
		// Charger le squelette en specifiant les langages cibles et source
		// au cas il faudrait le compiler (source posterieure au resultat)
		// et appliquer sa fonction principale sur le contexte.
		// Passer le nom du cache pour produire sa destruction automatique
	
		$composer = charger_fonction('composer', 'public');
	
		if ($fonc = $composer($skel, $mime_type, $gram, $sourcefile)){
			spip_timer($a = 'calcul page '.rand(0,1000));
			$page = $fonc(array('cache' => $cache), array($local));
	
			// spip_log: un joli contexte
			$info = array();
			foreach($local as $var => $val)
				if($val)
					$info[] = "$var='$val'";
			spip_log("calcul ("
				.spip_timer($a)
				.") [$skel] "
				. join(', ',$info)
				.' ('.strlen($page['texte']).' octets)'
			);
	
			// Si #CACHE{} n'etait pas la, le mettre a $delais
			if (!isset($page['entetes']['X-Spip-Cache']))
				$page['entetes']['X-Spip-Cache'] = $GLOBALS['delais'];
	
		} else
			$page = array();
	
		if ($GLOBALS['var_mode'] == 'debug') {
			include_spip('public/debug');
			if(_DIR_PLUGIN_SQUELETTESMOTS){
				debug_dumpfile ($page['texte'], $fonc, 'resultat');
			}else{
				debug_dumpfile (strlen($page['texte'])?$page['texte']:" ", $fonc, 'resultat');
			}
		}
		$page['contexte'] = $local;
	
		if ($lang_select)
			lang_dselect();
	
		return $page;
	}
?>