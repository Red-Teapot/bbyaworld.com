<?php

class PlayerRegionsAreas {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAreas($order = 'nickname', $order_dir = 'asc') {
        $owner_order = (strtolower($order) == 'nickname' ? '`owner_nickname`' : 'SUM(`area`)');
        $area_order = (strtolower($order) == 'nickname' ? '`label`' : '`area`');
        $order_dir = (strtolower($order_dir) == 'asc' ? 'ASC' : 'DESC');

        $sql = "SELECT `label`, `area`, `owner_nickname` FROM `regions`
                ORDER BY " . $area_order . " " . $order_dir . ";";
        $stmt = $this->db->query($sql);
        $areas = $stmt->fetchAll();
        
        $sql = "SELECT `owner_nickname`, SUM(`area`) FROM `regions`
                GROUP BY `owner_nickname` ORDER BY " . $owner_order . " " . $order_dir . ";";
        $stmt = $this->db->query($sql);
        $owners = $stmt->fetchAll();
        $grouped = [];
        
        foreach($owners as $owner) {
            $grouped[$owner['owner_nickname']] = [
                'total_area' => floatval($owner['SUM(`area`)']),
                'areas' => [],
            ];
        }
        
        $misc = [];
        
        foreach($areas as $area) {
            $elem = [
                'name' => $area['name'],
                'label' => $area['label'],
                'area' => $area['area'],
            ];
            
            if(array_key_exists($area['owner_nickname'], $grouped)) {
                $grouped[$area['owner_nickname']]['areas'][] = $elem;
            } else {
                $misc[] = $elem;
            }
        }

        return [
            'grouped' => $grouped,
            'misc' => $misc,
        ];
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(1) FROM `regions`;";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();

        return intval($result['COUNT(1)']);
    }
}
