<?php

class PlayerRegionsAreas {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAreas($page = 1, $perPage = 50) {
        $page = intval($page);
        $perPage = intval($perPage);
        $sql = "SELECT `label`, `area` FROM `regions`
                ORDER BY `label` ASC
                LIMIT " . ($page - 1) * $perPage . ", " . $perPage . ";";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();

        return $result;
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(1) FROM `regions`;";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();

        return intval($result['COUNT(1)']);
    }
}
