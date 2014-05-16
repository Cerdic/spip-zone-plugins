<?php

namespace Sphinx;

/**
 * Classe pour créer des requêtes de sélection Sphinx
 *
 * Quelques exemples :


SPIP

[(#SET{sql, [(#ENV{source,spip}|sphinx_get_query_documents{#ENV*{recherche},#ENV*{tag},#ENV*{auteur}})]})]

PHP

function sphinx_get_query_documents($source, $recherche, $tag = '', $auteur = '', $orderby = '') {
    include_spip('inc/indexer');
    $sq = new \Sphinx\SphinxQLQuery();
    $sq
        ->select('WEIGHT() AS score')
        ->select('*')
        ->select("SNIPPET(content, " . $sq->quote($recherche . ($tag ? " $tag" : '')) . ", 'limit=200') AS snippet")
        ->from($source)
        ->where("MATCH(" . $sq->quote($recherche) . ")");

    if ($orderby) {
        $sq->orderby($orderby);
    }

    if ($tag) {
        if ($tag == '-') {
            $sq->select("IN(properties.tags.fr, " . $sq->quote($tag) . ") AS tag");
        } else {
            $sq->select("(LENGTH(properties.tags.fr) = 0) AS tag");
        }
        $sq->where("tag = 1");
    }

    if ($auteur) {
        $sq->select("IN(properties.authors, " . $sq->quote($auteur) . ") AS auteur");
        $sq->where("auteur = 1");
    }

    return $sq->get();
}

SPIP

[(#SET{sqlf, [(#ENV{source,spip}|sphinx_get_query_facette_auteurs{#ENV*{recherche},#ENV*{tag},#ENV*{auteur}})]})]

PHP

function sphinx_get_query_facette_auteurs($source, $recherche, $tag = '', $auteur = '', $orderby = '') {

    include_spip('inc/indexer');
    $sq = new \Sphinx\SphinxQLQuery();
    $sq
        ->select('COUNT(*) AS c')
        ->select('GROUPBY() AS facette')
        ->from($source)
        ->where("MATCH(" . $sq->quote($recherche) . ")")
        ->groupby("properties.authors")
        ->orderby("c DESC")
        ->limit("30")
        ;

    return $sq->get();
}



 */
class SphinxQLQuery{
    private $select  = [];
    private $from    = [];
    private $where   = [];
    private $groupby = [];
    private $orderby = [];
    private $limit   = '';

    public function select($select) {
        $this->select[] = $select;
        return $this;
    }

    public function from($from) {
        $this->from[] = $from;
        return $this;
    }


    public function where($where) {
        $this->where[] = $where;
        return $this;
    }

    public function orderby($orderby) {
        $this->orderby[] = $orderby;
        return $this;
    }

    public function groupby($groupby) {
        $this->groupby[] = $groupby;
        return $this;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function quote($recherche) {
        return _q($recherche);
    }

    public function get() {
        $query = [];
        if ($this->select)   $query[] = 'SELECT '   . implode(',', $this->select);
        if ($this->from)     $query[] = 'FROM '     . implode(',', $this->from);
        if ($this->where)    $query[] = 'WHERE '    . implode(' AND ', $this->where);
        if ($this->groupby)  $query[] = 'GROUP BY ' . implode(',', $this->groupby);
        if ($this->orderby)  $query[] = 'ORDER BY ' . implode(',', $this->orderby);
        if ($this->limit)    $query[] = 'LIMIT '    . $this->limit;
        return implode(' ', $query);
    }

    public function __tostring() {
        return $this->get();
    }
}


