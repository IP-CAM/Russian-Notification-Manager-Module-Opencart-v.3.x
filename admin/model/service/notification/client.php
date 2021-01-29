<?php
class ModelServiceNotificationClient extends Model {
    public function set($data){
        foreach($data as $name => $val){
            $this->delete($name);
            $sql = "INSERT INTO `" . DB_PREFIX . "notification_setting` SET 
            `name` = '".$this->db->escape($name)."',
            `data` = '".$this->db->escape(json_encode($val))."'";
            $this->db->query($sql);
        }
    }

    public function get($name){
        $sql = "SELECT `data` FROM `" . DB_PREFIX . "notification_setting` WHERE `name` = '".$this->db->escape($name)."'";
        $query = $this->db->query($sql)->row;
        return isset($query['data']) ? json_decode($query['data'], 1) : '';
    }

    public function delete($name){
        $sql = "DELETE FROM `" . DB_PREFIX . "notification_setting` WHERE `name` = '".$this->db->escape($name)."'";
        $this->db->query($sql);
    }

    public function getSettings($data){
        $settings = [];
        foreach($data as $name){
            $settings[$name] = $this->get($name);
        }
        return $settings;
    }

    public function clear(){
        $sql = "TRUNCATE `" . DB_PREFIX . "notification_setting`";
        $this->db->query($sql);
    }

    public function getEventByCode($code) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "event` WHERE `code` = '" . $this->db->escape($code) . "' LIMIT 1");

		return $query->row;
	}

    public function createSettingTable(){
        $sql = "SHOW TABLES FROM `".DB_DATABASE."` LIKE '" . DB_PREFIX . "notification_setting'";

        if($this->db->query($sql)->row){
            return false;
        }

        $sql = "CREATE TABLE `" . DB_PREFIX . "notification_setting` (
            `id` int NOT NULL,
            `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            `data` text NOT NULL
          )";
        $this->db->query($sql);

        $sql = "ALTER TABLE `" . DB_PREFIX . "notification_setting`
            ADD PRIMARY KEY (`id`),
            ADD UNIQUE KEY `name` (`name`)";
        $this->db->query($sql);

        $sql = "ALTER TABLE `" . DB_PREFIX . "notification_setting`
            MODIFY `id` int NOT NULL AUTO_INCREMENT;";
        $this->db->query($sql);
    }

    public function install(){
        $this->createSettingTable();
        whatsapp\messages::createTable();
        whatsapp\chats::createTable();
    }

    public function uninstall(){
        $this->db->query("DROP TABLE " . DB_PREFIX . "notification_setting");
        $this->db->query("DROP TABLE " . DB_PREFIX . "notification_messages");
        $this->db->query("DROP TABLE " . DB_PREFIX . "notification_chats");
    }
}