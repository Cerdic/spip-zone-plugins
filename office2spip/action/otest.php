<?php

function action_otest() {
	include_spip('inc/securiser_action');
	$secret = substr(md5('otest+'.secret_du_site()), 0,6);


	if (_request('secret') != $secret) {
		if (!autoriser('ecrire'))
			echo "Connectez-vous pour connaitre le secret.";
		else
			echo "Le secret : ".$secret;

		exit;
	}

	include_spip('inc/fonctionsale');

	$rep = sous_repertoire(_DIR_TMP, 'upload_office');

	$dst = array();
	if ($_FILES) {
		foreach ($_FILES as $file) {
			if ($dst = office_load($file, $rep)) {
				unset($file['tmp_name']);
				$file['md5'] = $dst;
				$file['text'] = sale(@file_get_contents($rep.$dst.'.html'));
				if ($g = glob($rep.$dst.'_*')) {
					$file['attachments'] = array();
					foreach($g as $j) {
						$file['attachments'][basename($j)] = base64_encode(file_get_contents($j));
					}
				}
				$res[] = $file;
			}
		}
	}

	echo json_encode($res);
}


function office_load($file, $rep) {
	if (!preg_match(',\.(docx|doc|rtf|xls|xlsx)$,', $file['name'], $r))
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
