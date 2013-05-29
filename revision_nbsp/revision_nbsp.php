<?php

/*
 * revision_nbsp
 *
 * Dans l'espace prive, souligne en grise les espaces insecables
 *
 * Auteur : fil@rezo.net
 * © 2005-2007 - Distribue sous licence GNU/GPL
 *
 * l'icone <edit-find-replace.png> est tiree de Tango Desktop Project
 * http://tango.freedesktop.org/Tango_Desktop_Project -- sous licence
 * http://creativecommons.org/licenses/by-sa/2.5/
 *
 */


	function RevisionNbsp_revision_nbsp($letexte) {
		if (!_DIR_RESTREINT
		AND !defined('_STOP_REVISION_NBSP')
		) {
			$letexte = echappe_html($letexte, '', true, ',(<img[^<]*>),Ums');

			// NBSP classique
			$letexte = str_replace('&nbsp;',
				'<span style="border-bottom:2px solid #ccc;">&nbsp;</span>', $letexte);
			// NBSP utf8
			$letexte = str_replace("\xc2\xa0",
				'<span style="border-bottom:2px solid #ccc;">&nbsp;</span>', $letexte);

			// redondant avec le nouveau systeme
			$letexte = str_replace("\n_ ",
				"<span style='color:orange;'>&para;</span>\n_ ", $letexte);

			return $letexte;
		} else
			return $letexte;
	}

	function RevisionNbsp_ajoute_bouton_corriger_les_notes($x) {
		global $nb_notes;
		if ($GLOBALS['auteur_session']['statut'] == '0minirezo')
		if ($x['args']['exec'] == 'articles') {
			$id_article = intval($x['args']['id_article']);
			$t = sql_fetch(spip_query("SELECT texte FROM spip_articles WHERE id_article=$id_article"));
			if ($c = notes_automatiques($t['texte'])) {
				$x['data'] .= "<br />\n"
				.debut_boite_info(true)
				.icone_horizontale(
					'Transformer les ' . $nb_notes . ' notes de cet article.',
					generer_url_action('corriger_notes', 'id_article='.$id_article),
						_DIR_PLUGIN_REVISIONNBSP."edit-find-replace.png",  # grml!!
						"rien.gif", false)
				.fin_boite_info(true);
			}
#			else $x['data'] .= "<div>pas de notes a corriger</div>";
		}

		// ajouter le css des nbsp;
		$x['data'] .= '<style type="text/css">small.fine {background-color:#ccc;}</style>';

		return $x;
	}


	function notes_automatiques($texte) {
		global $nb_notes; // sale

		// Attraper les notes
		$regexp = ', *\[\[(.*?)\]\],msS';
		if (strpos($texte, '[[')
		AND $s = preg_match_all($regexp, $texte, $matches, PREG_SET_ORDER)
		AND $s==1
		AND preg_match(",^ *<>(.*),s", $matches[0][1], $r)) {
			$lesnotes = $r[1];
			$letexte = trim(str_replace($matches[0][0], '', $texte));

			$num = 0;
			while (($a = strpos($lesnotes, '('.(++$num).')')) !== false
			AND (
				($b = strpos($letexte, '('.($num).')')) !== false
				OR ($b = strpos($letexte, '['.($num).'])')) !== false
			)) {
				if (!isset($debut))
					$debut = trim(substr($lesnotes, 0, $a));

				$lanote = substr($lesnotes,$a+strlen('('.$num.')'));

				$lanote = preg_replace(
				',[(]'.($num+1).'[)].*,s', '',$lanote
				);
				$lesnotes = substr($lesnotes, $a+strlen('('.$num.')')+strlen($lanote));
				$lanote = trim($lanote);
				$lanote = (strlen($lanote) ? "[[\n  ".$lanote."\n]]" : '');

				$letexte = substr($letexte,0,$b)
					. $lanote
					. substr($letexte,$b+strlen('('.$num.')'));
			}

			$nb_notes = $num-1;

			if (strlen($suite = trim($lesnotes)))
				$letexte.= '[[<> '.$suite.' ]]';

			if (isset($debut)) {
				return (strlen($debut)?"\n\n[[<>$debut ]]":'') . $letexte;
			}
		}


		//  Cas deux : on recherche des notes en derniers paragraphes,
		// commencant par (1), on les reinjecte en [[<> ... ]] et on
		// relance la fonction sur cette onstruction.
		else {
			$texte = trim($texte);
			if (preg_match_all(',^[(](\d+)[)].*$,UmS', $texte, $regs)
			AND preg_match(',^(.*\n)([(]1[)].*)$,UmsS', $texte, $u)) {
				$notes = $u[2];
				$texte = $u[1];
				return notes_automatiques("$texte\n\n[[<> $notes ]]");
			} 
		}

	}

?>