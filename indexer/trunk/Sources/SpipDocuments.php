<?php

namespace Spip\Indexer\Sources;

use \Indexer\Sources\SourceInterface;

class SpipDocuments implements SourceInterface {

    /** SPIP récent ? spip_xx_liens ou spip_xx_yy */
    private $tables_liens = true;

    public function __construct() {}

    public function __toString() { return get_class($this); }

    /**
     * Retourne les documents ayant certaines conditions
     *
     * @param mixed $start     Condition qui remplira `$column >= $start`
     * @param mixed $end       Condition qui remplira `$column < $end`
     * @param string $column   Colonne affectée
     * @return \Indexer\Sources\Documents[]
     */
    public function getDocuments($start = 0, $end = 0, $column = 'id_element') {}

    /**
     * Retourne tous les documents (Peut provoquer des problèmes de mémoire !)
     * @return \Indexer\Sources\Documents[]
     */
    public function getAllDocuments() {
        return $this->getDocuments();
    }



    /** @param bool $bool */
    public function setTablesLiens($bool) {
        $this->tables_liens = $bool;
    }

    public function getObjectId($objet, $id_objet){
        return crc32($GLOBALS['meta']['adresse_site'] . $objet) + intval($id_objet);
    }

    public function getAuthorsProperties($objet, $id_objet) {
        if ($this->tables_liens) {
            $auteurs = sql_allfetsel('a.nom', 'spip_auteurs AS a, spip_auteurs_liens AS al', [
                "al.id_objet = " . intval($id_objet),
                "al.objet    = " . sql_quote($objet),
                "a.id_auteur = al.id_auteur",
            ]);
        } else {
            $auteurs = sql_allfetsel('a.nom', 'spip_auteurs AS a, spip_auteurs_articles AS al', [
                "al.id_article = " . intval($id_objet),
                "a.id_auteur = al.id_auteur",
            ]);
        }
        return array_map('array_shift', $auteurs);
    }


    public function getTagsProperties($objet, $id_objet) {
        if ($this->tables_liens) {
            $tags = sql_allfetsel('m.titre', 'spip_mots AS m, spip_mots_liens AS ml', [
                "ml.id_objet = " . intval($id_objet),
                "ml.objet    = " . sql_quote($objet),
                "m.id_mot = ml.id_mot",
            ]);
        } else {
            $tags = sql_allfetsel('m.titre', 'spip_mots AS m, spip_mots_articles AS ml', [
                "ml.id_article = " . intval($id_objet),
                "m.id_mot = ml.id_mot",
            ]);
        }
        return array_map('array_shift', $tags);
    }


    public function getBounds() {
        return ['min' => 0, 'max' => 0];
    }

    /**
     * Crée un tableau de parts
     *
     * @param int $count
     * @return array
    **/
    public function getParts($count) {
        $bornes = $this->getBounds();
        $parts = [];
        for ($i = $bornes['min']; $i <= $bornes['max']; $i += $count) {
            $parts[] = [
                'start' => $i,
                'end'   => $i + $count
            ];
        }
        return $parts;
    }
}
