<?php

namespace Indexer\Storage;

use Indexer\Sources\Document;

class Sphinx implements StorageInterface {
	/** @var SprinxQL|null */
	private $sphinxql = null;

	/** @var string Nom de l'index */
	private $indexName = '';

	public function __construct(\Sphinx\SphinxQL\SphinxQL $sphinxql, $indexName) {
		$this->sphinxql = $sphinxql;
		$this->indexName = $indexName;
	}

	public function replaceDocuments($documents){
		include_spip('inc/config');
		$this->sphinxql->connect(); // reconnecter en cas de timeout

		$query = "
			REPLACE INTO $this->indexName
				(id,  title, summary, content, date, date_indexation, uri, properties, signature)
			VALUES
		";
		
		
		if (defined('_INDEXER_READONLY') && _INDEXER_READONLY) {
			spip_log($query, 'indexer');
			return true;
		}

		// insertion document par document
		// il semble que sphinxql n'aime pas plusieurs lignes d'un coup.
		foreach ($documents as $document) {
			// On vérifie qu'il y a bien un Document
			if ($document and $document instanceof \Indexer\Sources\Document) {
				// Effacer les documents ayant un drapeau "to_delete"
				if ($document->to_delete === true) {
					$q = "DELETE FROM $this->indexName WHERE id=".$document->id;
				} else {
					$data = $this->reformatDocument($document);
					$data = array_map(array($this->sphinxql, 'escape_string'), $data);
					$q = $query . "('" . implode("', '", $data) . "')";
				}
				
				if (!$this->sphinxql->query($q)) {
					spip_log($this->sphinxql->errors(), 'indexer');
					spip_log($q, 'indexer');
					
					return false;
				}
			}
		}

		return true;

		// par lot de 10 entrées
		/*
		$sep = $values = '';
		$n = 0;
		foreach ($documents as $document) {
			$data = $this->reformatDocument($document);
			$data = array_map(array($this->sphinxql, 'escape_string'), $data);
			$values .= $sep . " ('" . implode("', '", $data) . "')";
			$sep = ',';
			if (++$n == 10) {
				if (!$this->sphinxql->query($query . $values)) {
					spip_log($this->sphinxql->errors(), 'indexer');
					spip_log($q, 'indexer');
					return false;
				}
				$n = 0;
				$sep = $values = '';
			};
		}

		if ($n and !$this->sphinxql->query($query . $values)) {
			echo "<pre>".print_r($this->sphinxql->errors(), true)."</pre>";
			exit;
		}*/
	}

	public function replaceDocument(Document $document){
		return $this->replaceDocuments(array($document));
	}

	public function reformatDocument(Document $document) {
		// Indexation specifique des champs de date sous forme de nombres
		// dans properties.ymd -- https://contrib.spip.net/Indexer-date-32bits
		date_default_timezone_set('UTC');
		$dateu = strtotime($document->date);
		if ($dateu) {
			// recalculer dateu pour les dates floues: 2000-00-00 => 2000-01-01
			$dateu = strtotime(str_replace("-00", "-01", $document->date));
			$document->properties['ymd'] = array(
				'year' => intval(date('Y', $dateu)),
				'yearmonth' => intval(date('Ym', $dateu)),
				'yearmonthday' => intval(date('Ymd', $dateu)),
				'u' => $dateu,
				'datetime' => $document->date
			);
		}

		return array(
			"id"              => $document->id,
			"title"           => $document->title,
			"summary"         => $document->summary,
			"content"         => $document->content,
			"date"            => $dateu,
			"date_indexation" => intval($document->date_indexation),
			"uri"             => $document->uri,
			"properties"      => json_encode($document->properties),
			"signature"       => $this->signer($document),
		);
	}

	public function signer($doc) {
		include_spip('inc/securiser_action');
		return md5(secret_du_site().json_encode($doc));
	}

	public function purgeDocuments($source = null) {
		include_spip('inc/config');
		$source = lire_config('indexer/source', lire_config('adresse_site'));

		$q = "DELETE FROM $this->indexName";
		if ($source) $q .= " WHERE properties.source="._q($source);
		if (!$this->sphinxql->query($q)) {
			spip_log($this->sphinxql->errors(), 'indexer');
			spip_log($q, 'indexer');
			return false;
		}
		return true;
	}
}
