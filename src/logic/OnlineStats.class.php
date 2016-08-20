<?php

class OnlineStats {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getStats($page = 1, $perPage = 50) {
        $page = intval($page);
        $perPage = intval($perPage);
        $sql = "SELECT `nickname`, `time` FROM `online_stats`
                WHERE `time` >= 60
                ORDER BY `time` DESC
                LIMIT " . ($page - 1) * $perPage . ", " . $perPage . ";";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();

        return $result;
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(1) FROM `online_stats` WHERE `time` >= 60;";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();

        return intval($result['COUNT(1)']);
    }
}
