<?php

class PlayerRegionsAreas {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAreas($order = 'nickname', $order_dir = 'asc') {
        $owner_order = (strtolower($order) == 'nickname' ? '`owner_nickname`' : 'SUM(`area`)');
        $order_dir = (strtolower($order_dir) == 'asc' ? 'ASC' : 'DESC');

        $sql = "SELECT `label`, `area`, `owner_nickname`, `area_number` FROM `regions` ORDER BY `area_number` ASC;";
        $stmt = $this->db->query($sql);
        $areas = $stmt->fetchAll();
        
        $sql = "SELECT `owner_nickname`, SUM(`area`) FROM `regions`
                GROUP BY `owner_nickname` ORDER BY " . $owner_order . " " . $order_dir . ";";
        $stmt = $this->db->query($sql);
        $owners = $stmt->fetchAll();
        $result = [];
        
        foreach($owners as $owner) {
            $result[$owner['owner_nickname']] = [
                'total_area' => floatval($owner['SUM(`area`)']),
                'areas' => [],
            ];
        }
        
        foreach($areas as $area) {
            $result[$area['owner_nickname']]['areas'][] = [
                'name' => $area['name'],
                'label' => $area['label'],
                'area' => $area['area'],
                'area_number' => intval($area['area_number']),
            ];
        }

        return $result;
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(1) FROM `regions`;";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();

        return intval($result['COUNT(1)']);
    }
}
