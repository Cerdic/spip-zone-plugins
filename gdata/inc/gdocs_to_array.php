<?php



# hors de la fonction, de facon a ce que la class soit chargee
# meme si le resultat est deja dans le cache (sinon le cache est inexploitable).
# cf. iterateur/data.php


function inc_gdocs_to_array($u) {


	try {
		// perform login and set protocol version to 3.0
		$client = gdocs_init();
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		$docs = new Zend_Gdata_Docs($client);
		$f = "https://docs.google.com/feeds/documents/private/full/-/document";
		$list = $docs->getDocumentListFeed($f);

		$u = array();
		foreach ($list->entries as $entry) {  
			$e = array();

			// Find the URL of the HTML view of the document.
			foreach ($entry->link as $link) {
				if ($link->getRel() === 'alternate')
					$e['alternatelink'] = $link->getHref();
			}

			$e['title'] = (string) $entry->title;
			$e['date'] = (string) $entry->published;
			$e['src'] = (string) $entry->content->src;

			$u[] = $e;
		}
	}
	catch (Exception $e) {
		spip_log('ERROR:' . $e->getMessage());
		return false;
	}

	return $u;
}

