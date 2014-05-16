<?php

namespace Spip\Indexer\Sources;

use \Indexer\Sources\Document;

class Articles extends SpipDocuments {



    public function getDocuments($start = 0, $end = 0, $column = 'id_article') {

        $where = [];
        if ($start) $where[] = "$column >= $start";
        if ($end)   $where[] = "$column < $end";

        $all = sql_allfetsel(
            [
                'id_article AS id',
                'titre', 'soustitre', 'surtitre',
                'texte', 'chapo', 'ps',
                'date', 'date_redac',
                'lang'
            ],
            'spip_articles',
            $where, // Where
            '', // Gr By
            '', // Or By
            '' // Limit
        );

        $documents = [];
        foreach ($all as $article) {
            $documents[] = $this->createDocumentArticle($article);
        }
        return $documents;
    }



    public function createDocumentArticle($article) {
         return new Document([
            'id'           => $this->getObjectId('article', $article['id']),
            'title'        => supprimer_numero($article['titre']),
            'summary'      => $article['surtitre'] . $article['soustitre'] . $article['chapo'],
            'content'      => $article['texte'],
            'date'         => (substr($article['date_redac'],0,4) == '0000') ? $article['date'] : $article['date_redac'],
            'uri'          => generer_url_entite_absolue($article['id'], 'article'),
            'properties'   =>
            [
                'authors'  => $this->getAuthorsProperties('article', $article['id']),
                'tags'     => $this->getTagsProperties('article', $article['id']),
                'objet'    => 'article',
                'id_objet' => $article['id'],
                'lang'     => $article['lang']
            ]
        ]);
    }


    public function getBounds() {
        return $bornes = sql_fetsel(['MIN(id_article) AS min', 'MAX(id_article) AS max'], 'spip_articles');
    }


}
