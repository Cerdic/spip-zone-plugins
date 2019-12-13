<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function autoriser_testotor_dist($action, $type, $id) {
	echo "<span style='color:mediumblue; background-color:lightblue'>autoriser($action, $type, $id)</span><br>";
	return true;
}

function compile_appel_macro_autoriser ($p) {
	if (!existe_argument_balise(1, $p)) {
		erreur_squelette ("Il faut au moins un argument à la balise #_AUTORISER", $p);
		return "''";
	};
	$autorisation = interprete_argument_balise(1, $p);

	if (erreur_argument_macro ('#_AUTORISER_SI', 'autorisation', $autorisation, $p))
		return "''";

	// l'autorisation peut être appelé avec 0, un ou 2 arguments
	if (!existe_argument_balise(2, $p))
		return "autoriser('.\"$autorisation\".')";

	$type = trim_quote(interprete_argument_balise (2, $p));
	if (erreur_argument_macro ("#_AUTORISER_SI{ $autorisation,...}", 'type', $type, $p))
		return "''";

	if (!existe_argument_balise(3, $p))
		return "autoriser('.\"$autorisation\".', '.\"$type\".')";

	// Le 3eme argument peut être une constante ou un argument calculé
	// Il y a 4 possibilités de passer des id calculées à #_AUTORISER_SI :
	// - Appels directs de #BALISE ou #GET{variable} (non recommandé)
	// - Passer 'env', 'boucle' et 'url' pour chercher l'id_ associé au type dans l'env reçu, dans la boucle immédiatement englobante ou dans l'url
	// Ex : #_AUTORISER{modifier,article,env} ou #_AUTORISER{modifier,article,boucle} ou #_AUTORISER{modifier,article,url}
	//

	$id = trim_quote(interprete_argument_balise (3, $p));

	if (!existe_argument_balise(4, $p)) {
		$id_type_q = "'".id_table_objet(trim($type, "'"))."'";

		// Gérer la présence de motclés env, boucle, url : désormais obsolète ?
		switch($id) {
			case "'env'" :
				$ret = "autoriser('.\"$autorisation\".', '.\"$type\".', '.\"macrosession_pipe({\$Pile[0][$id_type_q]})\".')";
				if (debug_get_mode('_session'))
					echo "Avec 'env' : compile appel autoriser donne <pre>$ret</pre><br>";
				return $ret;

			case "'boucle'" :
				$ret = "autoriser('.\"$autorisation\".', '.\"$type\".', '.\"macrosession_pipe({\$Pile[\$SP][$id_type_q]})\".')";
				if (debug_get_mode('_session'))
					echo "Avec 'boucle' : compile appel autoriser donne <pre>$ret</pre><br>";
				return $ret;

			case "'url'" :
				if (isset($_GET['debug']))
					echo "Avec 'url' : compile appel autoriser($autorisation, $type, _request($id_type_q)<br>";
				$ret = "autoriser('.\"$autorisation\".', '.\"$type\".', '.\"macrosession_pipe(_request($id_type_q))\".')";
				return $ret;

			default :
				// préparer l'expression compilée à être réinjectée (code introspectivement modifié)
				$id = reinjecte_expression_compilee($id);

				// TODO : Récupérer les erreurs de syntaxe
				// Avant on avait une reconnaissance plus ciblée (décompilation de #UNE_BALISE et #GET{unevariable}) suivis de :
				// if (erreur_argument_macro ("#_AUTORISER_SI{ $autorisation, $type, ...}", 'id', $id, $p, 'contexte_ok') return "''";

				return "autoriser('.\"$autorisation\".', '.\"$type\".', '.\"$id\".')";

		}
	};

	// ATTENTION : Les appels à #_AUTORISER_SI avec arguments $qui et $opt n'ont jamais été testés
	$qui = trim_quote(interprete_argument_balise (4, $p));
	if (erreur_argument_macro ("#_AUTORISER_SI{ $autorisation, $type, $id, ...}", 'qui', $qui, $p))
		return "''";
	if (!existe_argument_balise(5, $p))
		return "autoriser('.\"$autorisation\".', ' .\"$type\" .', ' .\"$id\" .', ' .\"$qui\" .')";

	$opt = trim_quote(interprete_argument_balise (5, $p));
	if (erreur_argument_macro ('#_AUTORISER_SI', 'opt', $opt, $p))
		return "''";
	return "autoriser('.\"$autorisation\".', '.\"$type\" .', ' .\"$id\" .', ' .\"$qui\" .', ' .\"$opt\" .')";
}

function balise__AUTORISER_SI_dist($p) {
	$p->interdire_scripts = false;

	// Appelé uniquement au recalcul
	$p->code = V_OUVRE_PHP . 'if ('.compile_appel_macro_autoriser ($p).') { ' . V_FERME_PHP;
	return $p;
}

function balise__AUTORISER_SINON_dist($p) {
	return balise__SESSION_SINON_dist($p);
}

function balise__AUTORISER_FIN_dist($p) {
	return balise__SESSION_FIN_dist($p);
}

