<?php

//
// Ouvrir n'importe quel fichier avec LibreOffice et le renvoyer au format SPIP
//
// usage de ce webservice :
// curl -F "file=@toto.odt" "http://SERVEUR/?action=otest&secret=xxxx&fmt=spip"
// renvoie le fichier au format SPIP, dans un JSON
//
// - format de sortie dans &fmt=(spip|html|text)
// - images (en base64) avec &attachments=1
//
// TODO :
// - utiliser job_queue pour passer en asynchrone
// - codes de retours propres (ex: 404 : inconnu, 503 = pas encore converti)
// - etc.

function action_otest() {
	office_controler_secret();

	$rep = sous_repertoire(_DIR_TMP, 'upload_office');

	$dst = array();
	if ($_FILES) {
		foreach ($_FILES as $file) {
			if ($dst = office_load($file, $rep)) {
				unset($file['tmp_name']);
				$file['md5'] = $dst;

				$html = @file_get_contents($rep.$dst.'.html');
				switch(_request('fmt')) {
					case 'html':
						$file['html'] = $html;
						break;
					case 'spip':
						include_spip('inc/fonctionsale');
						$file['spip'] = sale($html);
					default:
						include_spip('inc/filtres');
						include_spip('inc/charset');
						$file['text'] = unicode_to_utf_8(html2unicode(
							trim(supprimer_tags($html))
						));
						break;
				}

				if (_request('attachments')) {
					if ($g = glob($rep.$dst.'_*')) {
						$file['attachments'] = array();
						foreach($g as $j) {
							$file['attachments'][basename($j)] = base64_encode(file_get_contents($j));
						}
					}
				}
				$res[] = $file;
			}
		}
	}

	echo json_encode($res);
}


function office_load($file, $rep) {
	if (!preg_match(',\.(docx|doc|rtf|xls|xlsx|odt)$,', $file['name'], $r))
		return;

	$ext = $r[1];

	if ($tmp = tempnam($rep,'oo')
	AND @move_uploaded_file($file['tmp_name'], $tmp)
	AND $dst = md5_file($tmp)
	AND rename($tmp, $rep.$dst.'.'.$ext)) {
		# ici on pourrait job_queue
		office_convertir($rep.$dst.'.'.$ext);
		return $dst;
	}
}


function office_convertir($doc) {
	exec("unoconv --format=html $doc");
}

// securiser en demandant le secret
function office_controler_secret() {
	include_spip('inc/securiser_action');
	$secret = substr(md5('otest+'.secret_du_site()), 0,6);

	if (_request('secret') != $secret) {
		if (!autoriser('ecrire'))
			echo "Connectez-vous pour connaitre le secret.";
		else
			echo "Le secret : ".$secret;

		exit;
	}
}
