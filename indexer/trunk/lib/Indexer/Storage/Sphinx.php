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
        $query = "
            REPLACE INTO $this->indexName
                (id,  title, summary, content, date, uri, properties, signature)
            VALUES
        ";

        // insertion document par document
        // il semble que sphinxql n'aime pas plusieurs lignes d'un coup.
        foreach ($documents as $document) {
            $data = $this->reformatDocument($document);
            $data = array_map(array($this->sphinxql, 'escape_string'), $data);
            $q = $query . "('" . implode("', '", $data) . "')";
            if (!$this->sphinxql->query($q)) {
                echo "<pre>".print_r($this->sphinxql->errors(), true)."</pre>";
                echo "<pre>".print_r($q, true)."</pre>";
                exit;
            }          
        }

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
                    echo "<pre>".print_r($this->sphinxql->errors(), true)."</pre>";
                    exit;
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
        $this->replaceDocuments([$document]);
    }


    public function reformatDocument(Document $document) {
        return [
           "id" => $document->id,
           "title" => $document->title,
           "summary" => $document->summary,
           "content" => $document->content,
           "date" => strtotime($document->date),
           "uri" => $document->uri,
           "properties" => json_encode($document->properties),
            "signature" => $this->signer($document),
        ];
    }

    public function signer($doc) {
        include_spip('inc/securiser_action');
        return md5(secret_du_site().json_encode($doc));
    }

}
