<?php

/*
 * Fonctions de conversion de format
 */



function nettoyer_format($t) {

	// resserrer les {}
	$t = preg_replace('/{([.,]+)/', '\1{', $t);
	$t = preg_replace('/}([.,]+)/', '\1}', $t);
	$t = preg_replace(',([^{]){ ,', '\1 {', $t);
	$t = preg_replace(', }([^}]),', '} \1', $t);
	$t = preg_replace(',} {,', ' ', $t);

	$t = preg_replace(", +~,", '~', $t);
	$t = preg_replace(",~ +,", '~', $t);
	$t = preg_replace("/{([?!., ]?)}/", '\1', $t);

#$a = '«';
#for($i=0;$i<strlen($a); $i++)
#	echo ord($a[$i]).'-';exit;

	## attention ici c'est de l'utf8
	$t = str_replace("~\xc2\xbb", "\xc2\xbb", $t);  # guillemet >>
	$t = str_replace("\xc2\xab~", "\xc2\xab", $t);  # <<
	$t = str_replace ("\xe2\x80\x93", '--', $t); # tiret long

	// supprimer les insecables sauf dans les nombres,
	// parce que ca prend le chou (?)
	$t = preg_replace(",(\D)~(\D),", '\1 \2', $t);

	return $t;
}


	// -----------------------------------------------------------------------
	// Definition des regex pour les Conversions 
	// -----------------------------------------------------------------------
	global $conv_formats;       // les regex à appliquer
	global $conv_functions_pre; // les functions à appliquer avant les regex 

	$conv_formats = $conv_functions_pre = array();

	// syntaxe SPIP
	// http://www.spip-contrib.net/IMG/html/antiseche_spip-3.html
	
	// Conversion MediaWiki -> SPIP
  // ref. syntaxe: http://www.mediawiki.org/wiki/Help:Formatting
  $conv_functions_pre['MediaWiki_SPIP'] = array("convertisseur_add_ln","mediawiki_doQuotes");
  $conv_formats['MediaWiki_SPIP'] = array(
      "pattern" => array( 
        'model'  => "{{([^}}]*)}}",   // FIXME si template ds template       
         // applies anywhere        
        'ib' => "<i><b>([^<]*)</b></i>",            
        'b' => "<b>([^<]*)</b>",  
        'i'   => "<i>([^<]*)</i>", 
        'ib_post' => "<ib>([^<]*)</ib>",        
        // only at the beginning of the line         
        'h4'     => "\n=====([^=====]*)=====",
        'h3'     => "\n====([^====]*)====",
        'h2'     => "\n===([^=====]*)===",
        'h1'     => "\n==([^==]*)==",
        'ul_3'     => "\n\*\*\*", 
        'ul_2'     => "\n\*\*",  
        'ul_1'     => "\n\*", 
        'ol_3'     => "\n\#\#\#", 
        'ol_2'     => "\n\#\#",  
        'ol_1'     => "\n\#",  
        'dt'     => "\n\;([^\r]*)", 
        'dd'     => "\n\:([^\r]*)", 
        // TODO: Preformatted text
        
        // links - http://www.mediawiki.org/wiki/Help:Links 
        'comment' => "<!--([^\-]*)-->",
        'link_img'  => "\[\[(Image|Media):([^\[\[]*)\]\]", 
        'link_cat'  => "\[\[(Category|Catégorie|:Category):([^\[\[]*)\]\]",
        'link_user'  => "\[\[(Utilisateur|User):([^|\[]*)\|([^\[]*)\]\]",  // avec pipe
        'link_user2'  => "\[\[(Utilisateur|User):([^\[]*)\]\]", 
        'link_lang'  => "\[\[([^\:\[]*):([^\[]*)\]\]",  
        'link_int'  => "\[\[([^|\[]*)\|([^\[]*)\]\]",                     // avec pipe
        'link_int2'  => "\[\[([^\[\[]*)\]\]", 
        'link_ext0'  => "\nhttp([^ \r]*)", 
        'link_ext1'  => " http([^ \r]*)", 
        'link_ext2'  => "\\[([^\\[ ]*) ([^(\\[|)]*)\\]",                  // support ext., supporte plusieurs blancs
        'ref' => "<ref>", 
        'ref2' => "</ref>",
        
        // TODO: Table (http://www.mediawiki.org/wiki/Help:Tables)
        ),
      "replacement" => array(
        'model'  => "",
        'ib' => "<ib>\\1</ib>", 
        'b' => "{{\\1}}",   
        'i' => "{\\1}", 
        'ib_post' => "{{<i>\\1</i>}}",
        'h4'     => "{{{\\1}}}", 
        'h3'     => "{{{\\1}}}", 
        'h2'     => "{{{\\1}}}",  
        'h1'     => "{{{\\1}}}", 
        'ul_3'     => "-*** ", 
        'ul_2'     => "-** ", 
        'ul_1'     => "-* ", 
        'ol_3'     => "-### ", 
        'ol_2'     => "-## ", 
        'ol_1'     => "-# ",
        'dt'     => "<dt>\\1</dt>", 
        'dd'     => "<dd>\\1</dd>",
        'comment' => "",
        'link_img' => "",
        'link_cat' => "",
        'link_user' => "\\3",
        'link_user2' => "\\2",
        'link_lang' => "",
        'link_int'  => "\\2", 
        'link_int2'  => "\\1",  
        'link_ext0'  => "[->http\\1]", 
        'link_ext1'  => " [->http\\1]",   
        'link_ext2'  => "[\\2->\\1]",
        'ref'  => "[[ ",
        'ref2'  => " ]]",
        
        )
  );
	
  
  // Conversion MoinWiki -> SPIP
  // ref. syntaxe: http://trac.edgewall.org/wiki/WikiFormatting
  // ref. syntaxe: http://moinmo.in/HelpOnFormatting?highlight=%28formatting%29 
  $conv_formats['MoinWiki_SPIP'] = array(
      "pattern" => array(
        'code'   => "{{{([^}}}]*)}}}", // FIXME si } dans {{{ }}}                
        'bold3'  => "'''''([^''''']*)'''''",
        'bold2'  => "''''([^'''']*)''''",
        'bold'   => "'''([^''']*)'''", 
        'i'      => "''([^'']*)''",      
        'under'  => "__([^\_]*)__",  
        'del'    => "~~([^\~]*)~~",
        'h4'     => "==== ([^ ====]*) ====",
        'h3'     => "=== ([^ ===]*) ===",
        'h2'     => "== ([^ ==]*) ==",
        'h'      => "= ([^ =]*) =", 
        'link2'  => "\\[([^\\[]*) ([^(\\[| )]*)\\]", // FIXME si plusieurs espaces blanc
        'cell'   => "\|\|([^\|]*)\|\|",
        'ul'     => "([^ ]*)\*([^ \*]*)",  
        'ul_pas2'=> " -\*", 
        'ul2'    => "  -\*", 
        'ul3'    => "  -\**",        
        'ol2'    => "   ([^ ]*)1.([^ 1.]*)",
        'ol'     => " 1\.([^ 1\.]*)",
        
        ),
      "replacement" => array(
        'code'   => "<code>\\1</code>",              
        'bold3'   => "{{\\1}}",
        'bold2'   => "{{\\1}}", 
        'bold'   => "{{\\1}}", 
        'i'      => "{\\1}",       
        'under'  => "<span class='underline'>\\1</span",
        'del'    => '<del>\\1</del>',
        'h4'     => "{{{\\1}}}", 
        'h3'     => "{{{\\1}}}", 
        'h2'     => "{{{\\1}}}",  
        'h'      => "{{{\\1}}}",                
        'link2'  => "[\\2->\\1]",
        'cell'   => "|\\1|",        
        'ul'     => "-*\\2", 
        'ul_pas2'=> "-*", 
        'ul2'    => "-**",
        'ul3'    => "-***",             
        'ol2'    => "1.#\\2",
        'ol'     => "-#\\1", 
        )
  );
  
  // Conversion BBcode -> SPIP
  // ref. syntaxe: http://en.wikipedia.org/wiki/BBCode
  // voir aussi la version filtre: http://www.spip-contrib.net/Du-BBcode-dans-SPIP  
  // question: detecter si barre enrichie pour adopter la syntaxte etendue ?
  $conv_formats['BBcode_SPIP'] = array(
      "pattern" => array(
        'url'   => "\\[url]([^\\[]*)\\[/url\\]",
        'url2'  => "\\[url=([^\\[]*)\\]([^\\[]*)\\[/url\\]",
        'email' => "\\[email\\]([^\\[]*)\\[/email\\]",
        'email2'=> "\\[email=([^\\[]*)\\]([^\\[]*)\\[/email\\]",
        'color' => "\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]",
        'size'  => "\\[size=([^\\[]*)\\]([^\\[]*)\\[/size\\]",
        //'list'  => "!\[list\](.+)\[/list\]!Umi",
        //'list2' => "!\[\*\](.+)(?=(\[\*\]|</ul>))!Umi",
        'code'  => "\\[code]([^\\[]*)\\[/code\\]",
        'quote' => "\\[quote]([^\\[]*)\\[/quote\\]",
        'b'     => "\\[b]([^\\[]*)\\[/b\\]",
        'i'     => "\\[i]([^\\[]*)\\[/i\\]",
        'center'=> "\\[center]([^\\[]*)\\[/center\\]",
        'img'   => "\\[img]([^\\[]*)\\[/img\\]",
      ),
      "replacement" => array(
        'url'   => "[\\1->\\1]",
        'url2'  => "[\\2->\\1]",
        'email' => "[\\1->mailto:\\1]",
        'email2'=> "[\\2->mailto:\\1]",
        'color' => "<span style=\"color:\\1\">\\2</span>",
        'size'  => "<span style=\"font-size:\\1px\">\\2</span>",
        //'list'  => "<ul> $1 </ul>",
        //'list2' => "<li>$1</li>",
        'code'   => "<code>\\1</code>",
        'quote'  => "<quote>\\1</quote>",
        'b'      => "{{\\1}}",
        'i'      => "{\\1}",
        'center' => "<div style=\"text-align:center:\\1\">\\2</div>",
        'img'    => "<img src=\"\\1\" alt='' />",
      )      
  );
  
  // Conversion SPIP -> txt
  $conv_formats['SPIP_txt'] = array(
      "pattern" => array(
        'h'     => "{{{([^}}}]*)}}}",
        'b'     => "{{([^}}]*)}}",
        'i'     => "{([^}]*)}",
        'url'   => "\\[([^\\[]*)->([^(\\[| )]*)\\]",         
      ),
      "replacement" => array(
        'h'   => "\\1\n",
        'b'   => "* \\1 *",
        'i'   => "\\1",
        'url' => "\\1 (\\2)",       
      )      
  );
  
  // Conversion DotClear -> SPIP
  // http://doc.dotclear.net/1.2/usage/syntaxes
  $conv_formats['DotClear_SPIP'] = array(
      "pattern" => array(
        // faux amis 
        'q3' => '{{([^{]*)\|([^\{]*)\|([^\{]*)}}',       
        'q2' => '{{([^{]*)\|([^\{]*)}}',
        'q' => '{{([^{]*)}}',
        
        // type bloc
        'h3'  => "\n\!\!\!([^\r]*)",
        'h4'  => "\n\!\!([^\r]*)",
        'h5'   => "\n\!([^\r]*)", 
        'ul'   => "\n\* ([^\r]*)",
        'ol'   => "\n# ([^\r]*)",     // FIXME gerer les ss listes
        
        // en ligne
        'br'   => "%%%", 
        'em' => '\'\'([^\']*)\'\'',
        'strong' => '__([^\_]*)__',
        'ins' => '\+\+([^\+]*)\+\+',
        'del' => '--([^\+]*)--',
        'code'=> '@@([^\@]*)@@',
        'img' => '\(\(([^\)]*)\)\)',
        'href_0' => '\[([^\|[]*)\]',                                  // 0 pipe
        'href_3' => '\[([^\[]*)\|([^\[]*)\|([^\[]*)\|([^\[\|]*)\]',   // 3 pipes
        'href_2' => '\[([^\[]*)\|([^\[]*)\|([^\[\|]*)\]',             // 2 pipes
        'href_1' => '\[([^\[]*)\|([^\[\|]*)\]',                       // 1 pipe
        'a' => '~([^~]*)~',
        'acronym' => '\?\?([^\?]*)\|([^\?]*)\?\?',
        'note' => '\$\$([^\$]*)\$\$',
        
        
      ),
      "replacement" => array(
        // faux amis 
        'q3' => '<quote>\\1</quote>',       
        'q2' => '<quote>\\1</quote>',
        'q' => '<quote>\\1</quote>',
        
        // type bloc
        'h3'   => "{{{\\1}}}",
        'h4'   => "{{{\\1}}}", 
        'h5'   => "{{{\\1}}}", 
        'ul'   => "\n-* \\1",
        'ol'   => "\n-# \\1",        
        
        // en ligne
        'br'   => "\n_ ",
        'em'   => "{{\\1}}",
        'strong'   => "{{\\1}}",
        'ins'   => "<ins>\\1</ins>",
        'del'   => "<del>\\1</del>",
        'code'   => "<code>\\1</code>",
        'img'   => "",
        'href_0'   => "[->\\1]",
        'href_3' => '[\\2->\\1]',
        'href_2' => '[\\2->\\1]',
        'href_1' => '[\\2->\\1]',
        'a' => '[\\1<-]',
        'acronym'   => "<acronym  title=\"\\2\">>\\1</acronym>",
        'note' => '[[\\1]]',
      )      
  );
  
  // Conversion XTG -> SPIP
  // format demandé par Jean Luc Girard
  // http://195.13.83.33/twiki/bin/view/FipDoc/QuarkTagsList
  // http://www.macworld.com/downloads/magazine/XPressTagsList.pdf   
  // cf. extract/quark.php
  $conv_formats['XTG_SPIP'] = 'quark';


  // Conversion SLA (Scribus) -> SPIP
  // SLA 1.2 http://docs.scribus.net/index.php?lang=en&sm=scribusfileformat&page=scribusfileformat
  // SLA 1.3 http://wiki.scribus.net/index.php/File_Format_for_Scribus_1.3.x 
  $conv_formats['SLA_SPIP'] = array(
      "pattern" => array(
        'ch'    => " CH=\"([^\"]*)\" ",  // "CH=\"<([^>]*)\""
        'br'    => "&#x5;",
        'sp'    => "&#x1d;",        
        'tag'   => "<([^\>]*)>", 

      ),
      "replacement" => array(
        'ch'   => ">\\1<",
        'br'   => "\n\n\n",
        'sp'   => " ",
        'tag'   => "",
      )      
  );


  $conv_formats['XPressTags'] = 'quark'; // function extract/
  $conv_formats['Word'] = 'doc'; // function extract/
  $conv_formats['RTF'] = 'rtf'; // function extract/
  $conv_formats['PDF'] = 'pdf'; // function extract/

	// FIN INITIALISATION





function conversion_format($conv_in, $format) {
	global $log;

	global $conv_formats;
	global $conv_functions_pre;

	$conv_out = $conv_in;

	// S'agit-il d'un tableau de conversion ?
	// si non, ca peut etre une fonction, par exemple un extracteur
	if (is_array($conv_formats[$format])) {

		// fonctions pre traitement ?
		if (is_array($conv_functions_pre[$format])) {
			include_spip("inc/fonction_convertisseur");
			foreach($conv_functions_pre[$format] as $key=>$pattern)
				$conv_out = $pattern($conv_out);
		}


		// on convertit (en avant les regex!)
		foreach($conv_formats[$format]['pattern'] as $key=>$pattern) {
			$replacement = $conv_formats[$format]['replacement'][$key];
			$conv_out = eregi_replace($pattern, $replacement, $conv_out);
		}
	}

	// c'est un nom de fonction : 'quark' par exemple
	else {
		if (is_string($conv_formats[$format])) {
			$cv = $conv_formats[$format];
			include_spip("extract/$cv");
			if ($cv = $GLOBALS['extracteur'][$cv]) {
				ecrire_fichier(_DIR_TMP.'convertisseur.tmp', $conv_in);
				$conv_out = $cv(_DIR_TMP.'convertisseur.tmp', $charset);
				supprimer_fichier(_DIR_TMP.'convertisseur.tmp');
				include_spip('inc/charsets');
				$conv_out = importer_charset($conv_out, $charset);
			}

			if ($cv AND !$conv_out)
				$log = "<span style='color:red'>"
					._T("convertisseur:erreur_extracteur")
					." $cv</span>";
			}
			if (!$cv)
				$log = "<span style='color:red'>"
					._T("convertisseur:unknown_format")
					."</span>";
		}

	return $conv_out;
}

# callback pour le deballage d'un zip telecharge
# http://www.phpconcept.net/pclzip/man/en/?options-pclzip_cb_pre_extractfunction
// cf. http://doc.spip.org/@callback_deballe_fichier
function callback_admissibles($p_event, &$p_header) {
	if (accepte_fichier_upload2($p_header['filename'])) {
		$p_header['filename'] = _tmp_dir
			. preg_replace(',\W,', '-', basename($p_header['filename']));
		return 1;
	} else {
		return 0;
	}
}

function accepte_fichier_upload2($f) {
	if (!preg_match(",.*__MACOSX/,", $f)
	AND !preg_match(",^\.,", basename($f)))
		return true;
}

function inserer_conversion($texte, $id_rubrique, $f=null) {
	global $log;

	$id_rubrique = intval($id_rubrique);
	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];

	// Verifier que la rubrique existe et qu'on a le droit d'y ecrire
	if (!$t = sql_fetsel('id_rubrique', 'spip_rubriques', 'id_rubrique='.$id_rubrique)) {
		$log = "erreur la rubrique n'existe pas";
		return false;
	}

	// Si $f (chargement zip), on cherche un article du meme $f
	// (valeur stockée dans le PS)
	// dans la meme rubrique,
	// avec le statut prepa, qui nous appartient, et... on l'ecrase
	$ps = 'Conversion depuis '.basename($f);
	$s = spip_query("SELECT a.id_article
		FROM spip_articles AS a,
		spip_auteurs_articles AS aut
		WHERE id_rubrique=$id_rubrique
		AND ps=".sql_quote($ps)."
		AND aut.id_article=a.id_article
		AND aut.id_auteur=".$id_auteur
		);
	if ($t = spip_fetch_array($s)) {
		$id_article = $t['id_article'];
	} else {
		// regler lang + id_secteur
		$q = sql_fetsel('id_secteur,lang', 'spip_rubriques',
			'id_rubrique='.intval($id_rubrique)
		);

		$champs = array(
			'titre' => $ps,
			'statut' => 'prepa',
			'id_rubrique' => $id_rubrique,
			'id_secteur' => $q['id_secteur'],
			'lang' => $q['lang'],
			'ps' => $ps
			);

		// Envoyer aux plugins
		$champs = pipeline('pre_insertion',
			array(
				'args' => array(
					'table' => 'spip_articles',
				),
				'data' => $champs
			)
		);

		$id_article = sql_insertq('spip_articles', $champs);

		pipeline('post_insertion',
			array(
				'args' => array(
					'table' => 'spip_articles',
					'id_objet' => $id_article
				),
				'data' => $champs
			)
		);

		if ($id_article>0
		AND $id_auteur>0) {
			sql_insertq('spip_auteurs_articles',
				array(
				'id_article' => $id_article,
				'id_auteur' => $id_auteur
				)
			);
		}
	}

	// en cas d'echec de l'insertion
	if (!$id_article) {
		$log = "erreur insertion d'article";
		return;
	}

	// Si on a repere des <ins class='titre'> etc, les inserer
	// dans les bons champs ; note : on choisi <ins> pour eviter les erreurs
	// avec <div> qui est plus courant
	$c = array('texte' => $texte);
	foreach (array('surtitre', 'titre', 'soustitre', 'chapo') as $champ) {
		if (preg_match(",<ins class='$champ'>(.*?)</ins>\n*,ims", $texte, $r)
		AND strlen($x = trim($r[1]))) {
			$c[$champ] = $x;
			$c['texte'] = substr_replace($c['texte'], '', strpos($c['texte'], $r[0]), strlen($r[0]));
		}
	}

	$r = '';
	foreach ($c as $var => $val)
		$r .= "$var="._q(trim($val)).', ';

	spip_query("UPDATE spip_articles
		SET $r
		date=NOW(),
		date_modif=NOW()
		WHERE id_article=$id_article"
	);

	return $id_article;
}


?>
