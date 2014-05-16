<?php

namespace Indexer\Storage;

use Indexer\Sources\Document;


class Sphinx implements StorageInterface {

    /** @var SprinxQL|null */
    private $sphinxql = null;

    /** @var string Nom de l'index */
    private $indexName = '';


    public function __construct(\Sphinx\SphinxQL $sphinxql, $indexName) {
        $this->sphinxql = $sphinxql;
        $this->indexName = $indexName;
    }


    public function replaceDocuments($documents){
        $query = "
            REPLACE INTO $this->indexName
                (id,  title, summary, content, date, uri, properties, signature)
            VALUES
                (:id, :title, :summary, :content, :date, :uri, :properties, :signature)
        ";
        $prepare = $this->sphinxql->prepare($query);

        foreach ($documents as $document) {
            $data = $this->reformatDocument($document);
            try {
                $prepare->execute($data);
            } except (PDOException $e) {
                echo "<div>".$e->getMessage()."</div>";
            }
        }
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
