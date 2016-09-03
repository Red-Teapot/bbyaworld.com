<?php

class PlayerRegionsAreas {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAreas($page = 1, $perPage = 50, $order = 'label', $order_dir = 'ASC') {
        $page = intval($page);
        $perPage = intval($perPage);

        $order = (strtolower($order) == 'label' ? 'label' : 'area');
        $order_dir = (strtolower($order_dir) == 'asc' ? 'ASC' : 'DESC');

        $sql = "SELECT `label`, `area` FROM `regions`
                ORDER BY `" . $order . "` " . $order_dir . "
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
