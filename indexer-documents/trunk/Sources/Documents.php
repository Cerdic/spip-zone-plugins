<?php

namespace Spip\Indexer\Sources;

use \Indexer\Sources\Document;

class Documents extends SpipDocuments {

    public function getDocuments($start = 0, $end = 0, $column = 'id_document') {

        $where = array();
        if ($start) $where[] = "$column >= $start";
        if ($end)   $where[] = "$column < $end";

        $all = sql_allfetsel(
            '*',
            'spip_documents',
            $where, // Where
            '', // Gr By
            '', // Or By
            '' // Limit
        );

        $documents = array();
        foreach ($all as $document) {
            $documents[] = $this->createDocumentDocument($document);
        }
        return $documents;
    }

    public function createDocumentDocument($document) {
        $id = $document['id_document'];

        return new Document(array(
            'id'           => $this->getObjectId('document', $id),
            'title'        => supprimer_numero($document['titre']),
            'summary'      => $document['descriptif'],
            'content'      => $document['descriptif'],
            'date'         => $document['date'],
            'uri'          => generer_url_entite_absolue($id, 'document'),
            'properties'   =>
            array(
                'authors'  => $this->getAuthorsProperties('document', $id),
                'tags'     => $this->getTagsProperties('document', $id),
                'objet'    => 'document',
                'id_objet' => $id,
                'lang'     => isset($document['lang']) ? $document['lang'] : ''
            )
        ));
    }

    public function getBounds() {
        return $bornes = sql_fetsel(array('MIN(id_document) AS min', 'MAX(id_document) AS max'), 'spip_documents');
    }
}