<?php

class ClansCells {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getList() {
        $sql = "SELECT `order`, `name`, `cell_count`, `is_in_council` FROM `clans`
                ORDER BY `order` ASC;";
        $stmt = $this->db->query($sql);
        $clans = $stmt->fetchAll();

        return $clans;
    }
}
