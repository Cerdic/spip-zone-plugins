<?php

namespace Spip\Indexer\Sources;

use \Indexer\Sources\Document;

class Articles extends SpipDocuments {


    public function getAllDocuments() {
        $all = sql_allfetsel(
            [
                'id_article AS id',
                'titre', 'soustitre', 'surtitre',
                'texte', 'chapo', 'ps',
                'date', 'date_redac'
            ],
            'spip_articles',
            '', // Where
            '', // Gr By
            '', // Or By
            '1000' // Limit
        );

        $documents = [];
        foreach ($all as $article) {
            $documents[] = $this->createDocumentArticle($article);
        }
        return $documents;
    }



    public function createDocumentArticle($article) {
         return new Document([
            'id'         => intval($article['id']) * 100 + 1,
            'title'      => $article['titre'],
            'summary'    => $article['surtitre'] . $article['soustitre'] . $article['chapo'],
            'content'    => $article['texte'],
            'date'       => (substr($article['date_redac'],0,4) == '0000') ? $article['date'] : $article['date_redac'],
            'uri'        => generer_url_entite_absolue($article['id'], 'article'),
            'properties' => [
                'authors' => $this->getAuthorsProperties('article', $article['id']),
                'tags'    => $this->getTagsProperties('article', $article['id'])
            ]
        ]);
    }
}
