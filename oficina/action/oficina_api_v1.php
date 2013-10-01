<?php

/*
 *

 curl "http://site/?email=user@server.tld&key=b375bb198e1b59667a3713cd464941f029c32234&url=http://nanolinenturkey.org/v1/bursaries.docx"
 
 # &format=json pour debug ou communiquer proprement

 # &filter=readability,tidy,spip,sale pour appliquer les filtres
 # ou &filter[]=readability&filter[]=sale

 il faut cliquer sur http://site/?action=cron pour activer les jobs


 *
 */


 
function action_oficina_api_v1_dist() {
	$r = oficina_v1_traiter(_request('signature'));

	if (_request('format') == 'json') {
		header('Content-Type: text/plain; charset=utf8');
		echo json_encode($r);
	} else if (_request('format') == 'dl') {
		include_spip('inc/headers');
		http_status($r['code']);

		if ($r['content']) {
			header('Content-Type: text/html; charset=utf8');
			echo $r['content'];
		}
		else {
			echo $r['message'];
		}
	}
	else {
		header('Content-Type: text/html; charset=utf8');
		echo $r['code'].": ".$r['message'];
		echo oficina_form();
		if ($r['content']) {
			echo "<hr />". $r['content'];
		}
	}

	flush();

	cron();
}


function oficina_v1_traiter($signature='') {
	include_spip('inc/filtres_mini');

	// verifier la cle
	include_spip('inc/securiser_action');
	if ($_REQUEST['key'] != oficina_key($_REQUEST['email'])) {
		return array(
			'code' => '403', # Forbidden
			'message' => 'Please authenticate with your email and API key',
			'debug' => var_export($_REQUEST,true),
		);
	}

	/*
	 * permettre de solliciter des clés pour autrui
	 */
	if ($for = _request('for')
	AND _request('email') == 'admin@server.tld'  # todo : autoriser()
	) {
		return array(
			'code' => 200,
			'message' => 'key for '.htmlspecialchars($for).' = '.oficina_key($for)
		);
	}

	$tmp = sous_repertoire(_DIR_TMP, 'oficina');
	$tmp = sous_repertoire($tmp, _request('email'));
	define ('_DIR_OFICINA', $tmp);

	// method	= GET
	if (!$_FILES) {

		if ($url = _request('url')
		AND preg_match(',^https?://,', $url)) {
			if (strlen($signature) == 0)
				$signature = md5($url);
		}


		if (strlen($signature) == 0) {
			return array(
				'code' => 404, # Not Found
				'message' => 'Please give me something (url or file)'
			);
		}

		$dest = md5($signature);

		if (file_exists($convert = _DIR_OFICINA.$dest.'.html')) {
			$html = file_get_contents($convert);
			$html = oficina_images($html, _request('images'));

			$html = oficina_filtrer($html, _request('filter'));

			return array(
				'code' => 200, # OK
				'message' => 'Here is your file',
				'content' => $html,
			);
		}

		// en cours de download
		if ($url) {
			if (@file_exists($tmpu = _DIR_OFICINA.$dest.'.url')) {
				if (count(glob(_DIR_OFICINA.$dest.'.*')) == 1) {
					return array(
						'code' => 204, # No Content
						'message' => 'Please wait for download to finish',
					);
				}
			// lancer le chargement ?
			} else {
				ecrire_fichier($tmpu, $url);
				oficina_lancer_chargement($url);
				return array(
					'code' => 204, # Not Found
					'message' => 'Starting download',
				);
			}
		}

		if (file_exists($convert = _DIR_OFICINA.$dest.'.json')) {
			return array(
				'code' => 404, # OK
				'message' => 'URL did not return a file',
			);
		}


		// en cours de conversion ou pas
		if (glob(_DIR_OFICINA.$dest.'.*')) {
			return array(
				'code' => 204, # No Content
				'message' => 'Please wait for conversion',
			);
		}

		return array(
			'code' => 404, # Not Found
			'message' => 'Please upload a file',
		);
	}
	
	// upload
	if ($_FILES) {
		// accepter ou pas
		foreach ($_FILES as $file) {
			$tmp = _DIR_OFICINA.'tmp.'.getmypid();
			if (@move_uploaded_file($file['tmp_name'], $tmp)) {
				#$finfo = new finfo(FILEINFO_MIME); 
				#$fres = $finfo->file($file);
				#$fres = mime_content_type($tmp);
				#$fres = exec("file $tmp");

				preg_match(',\.([^.]*)$,', $file['name'], $r);
				$ext = $r[1];

				switch($ext) {
					case 'html':
					case 'htm':
						$ext = 'html';
						break;
					case 'docx':
					case 'doc':
					case 'rtf':
					case 'odt':
						break;
					default:
						return array(
							'code' => 406, # Not Acceptable ?
							'message' => 'File format rejected: '.var_export($file,true),
						);
				}

				if (strlen($signature)==0)
					$signature = md5_file($tmp);

				$dest = md5($signature).'.'.$ext;
				rename($tmp, _DIR_OFICINA.$dest);

				oficina_lancer_conversion(_DIR_OFICINA.$dest);
				$url =  url_absolue($signature, url_absolue($_SERVER['REQUEST_URI']));

				return array(
					'code' => 201, # Created
					'message' => 'File accepted. '.$url,
					'url' => $url,
#					'local' => $dest, # debug
				);
			}
		}
	}

}


// s'il y a job_queue, creer le job, sinon lancer tout de suite
function oficina_lancer_conversion($local) {
	include_spip('inc/queue');
	if ($_REQUEST['no_queue'] OR !function_exists('queue_add_job')) {
		include_spip('inc/oficina');
		oficina_convertir($local);
	} else {
		queue_add_job('oficina_convertir', 'convertir '.$local, array($local), 'action/oficina_api_v1' /* 'inc/oficina' */, $no_duplicate = true);
	}
}

// lancer le download d'une URL
function oficina_lancer_chargement($url) {
	include_spip('inc/queue');
	if ($_REQUEST['no_queue'] OR !function_exists('queue_add_job')) {
		include_spip('inc/oficina');
		oficina_charger($url);
	} else {
		queue_add_job('oficina_charger', 'charger '.$url, array($url), 'action/oficina_api_v1' /* 'inc/oficina' */, $no_duplicate = true);
	}
}


# a mettre dans inc/oficina

function oficina_charger($url) {
	$mark = _DIR_OFICINA.md5(md5($url));
	$exts = array('txt', 'html', 'doc', 'docx', 'rtf', 'odt');

	include_spip('inc/distant');

	if ($a = recuperer_infos_distantes($url)
#	AND $a['url'] = $url
	AND ( in_array($ext = $a['extension'], $exts )
		OR in_array($ext = preg_replace(',^.*\.,', '', $url), $exts )
	)
	AND $doc = recuperer_page($url)) {

		$dest = $mark.'.'.$ext;
		ecrire_fichier($dest, $doc);

		return oficina_lancer_conversion($dest);
	}
	else {
		$dest = $mark.'.json';
		ecrire_fichier($dest, json_encode($a));
		return false;
	}
}


// calcule la version HTML du fichier $local, les images en lien
function oficina_convertir($local) {
	preg_match(',^(.*)\.([^.]+)$,', basename($local), $r);
	$dir = dirname($local);
	$base = $r[1];
	$ext = $r[2];

	switch($ext) {
		case 'html':
			return true;
		case 'doc':
		case 'docx':
		case 'rtf':
		case 'odt':
			return oficina_unoconv($local);
		default:
			return false;
	}
}

// convertir via unoconv
function oficina_unoconv($doc) {
	exec($c = "unoconv --format=html $doc 2>&1", $output, $returnvar);
	if ($returnvar) {
		spip_log('err'.$c.': '.join("\n", $output), 'oficina');
		return false;
	}

	// ici utiliser eventuellement tidy car le resultat n'est pas propre
	# http://www.php.net/manual/fr/tidy.examples.basic.php

	// + gerer les images

	return true;
}

function oficina_images($html, $images) {
	include_spip('inc/filtres');
	foreach(extraire_balises(strtolower($html), 'img') as $img) {
		if ($src = extraire_attribut($img, 'src')
		AND preg_match(',^[0-9a-f_]+\w+\.(jpg|gif|png)$,', $src, $regs)) {
			if ($images
			AND $i = @file_get_contents(_DIR_OFICINA.$regs[0])) {
				$base64 = 'data:image/'.str_replace('jpg', 'jpeg', $regs[1]).';base64,'.base64_encode($i);
				$repl = inserer_attribut($img, 'src', $base64);
			}
			else
				$repl = extraire_attribut($img, 'alt');

			$html = preg_replace('/'.preg_quote($img,'/').'/i', $repl, $html);
		}
	}

	return $html;
}

function oficina_filtrer($html, $filter) {
	if (!is_array($filter))
		$filter = explode(',', $filter);

	foreach($filter as $f) {
		switch($f) {

			case 'readability':
				include_spip('inc/readability');
				if (function_exists('readability_html'))
					$html = readability_html($html);
				break;

			case 'tidy':
				$tidy = new tidy;
				$tidy->parseString($html, oficina_tidy_config(), 'utf8');
				$tidy->cleanRepair();
				$html = (string) $tidy;
				break;

			case 'spip':
			case 'sale':
				include_spip('inc/fonctionsale');
				if (function_exists('sale')) {
					$html = sale($html);
					if (!_request('format'))
						$html = "<pre>".htmlspecialchars($html)."</pre>"; // pour affichage
				}
				break;
		}
	}

	return $html;
}

function oficina_tidy_config() {
	return array(
        'show-body-only' => false,
        'clean' => true,
        'char-encoding' => 'utf8',
        'add-xml-decl' => true,
        'add-xml-space' => true,
        'output-html' => false,
        'output-xml' => false,
        'output-xhtml' => true,
        'numeric-entities' => false,
        'ascii-chars' => false,
        'doctype' => 'strict',
        'bare' => true,
        'fix-uri' => true,
        'indent' => true,
        'indent-spaces' => 4,
        'tab-size' => 4,
        'wrap-attributes' => true,
        'wrap' => 0,
        'indent-attributes' => true,
        'join-classes' => false,
        'join-styles' => false,
        'enclose-block-text' => true,
        'fix-bad-comments' => true,
        'fix-backslash' => true,
        'replace-color' => false,
        'wrap-asp' => false,
        'wrap-jste' => false,
        'wrap-php' => false,
        'write-back' => true,
        'drop-proprietary-attributes' => true,
        'hide-comments' => false,
        'hide-endtags' => false,
        'literal-attributes' => false,
        'drop-empty-paras' => true,
        'enclose-text' => true,
        'quote-ampersand' => true,
        'quote-marks' => false,
        'quote-nbsp' => true,
        'vertical-space' => true,
        'wrap-script-literals' => false,
        'tidy-mark' => true,
        'merge-divs' => false,
        'repeated-attributes' => 'keep-last',
        'break-before-br' => true,
    );
}



function oficina_form() {
	return "<form method='get' action='./'>"

		. "email: "
		. "<input type='text' name='email' value='".htmlspecialchars(_request('email'))."' />"

		. "<br/>key: "
		. "<input type='text' name='key' value='".htmlspecialchars(_request('key'))."' />"

		. "<br/>URL: "
		. "<input type='text' name='url' value='".htmlspecialchars(_request('url'))."' /> (http://....[doc,docx,rtf,odt]) &nbsp; "
		. "<input type='submit' />"

		. "<br />format: "
		. "<input type='text' name='format' value='".htmlspecialchars(_request('format'))."' /> (json,dl)"

		. "<br />filter: "
		. "<input type='text' name='filter' value='".htmlspecialchars(_request('filter'))."' /> (readability,tidy,sale)"

		. "<br />images: "
		. "<input type='checkbox' name='images'"
			.(_request('images') ? ' checked="checked"':'')
			." /> (inline)"

		. "</form>";

}

