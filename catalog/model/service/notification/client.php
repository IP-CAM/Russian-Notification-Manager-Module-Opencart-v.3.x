<?php
class ModelServiceNotificationClient extends Model {
    public function get($name){
        $sql = "SELECT `data` as data FROM `" . DB_PREFIX . "notification_setting` WHERE `name` = '".$this->db->escape($name)."'";
        $query = $this->db->query($sql)->row;
        return isset($query['data']) ? json_decode($query['data'], 1) : [];
    }

    public function getSettings($data){
        $settings = [];
        foreach($data as $name){
            $settings[$name] = $this->getSetting($name);
        }
        return $settings;
    }
}