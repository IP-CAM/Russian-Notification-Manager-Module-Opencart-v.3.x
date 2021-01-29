<?php
namespace Whatsapp;
class Messages{
    private static $db;
    private static function init(){
        if(!self::$db){
            self::$db = New \Db(DB_DRIVER,DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE,DB_PORT);
        }
    }
    public static function set($data){

        if($mess = self::get($data)){
            $data = array_merge($mess, $data);
            return self::edit($data);
        }

        self::init();

        $sql = "INSERT INTO `" . DB_PREFIX . "notification_messages` SET 
        `message_id` = '".self::$db->escape($data['message_id'])."',
        `type` = '".self::$db->escape($data['type'])."',";

        if(isset($data['status'])){
            $sql .= "`status` = '".self::$db->escape($data['status'])."',";
        }
        if(isset($data['hidden'])){
            $sql .= "`hidden` = '".(int)$data['hidden']."',";
        }

        $sql .= "`body` = '".self::$db->escape(json_encode($data['body']))."',
        `chat_id` = '".self::$db->escape($data['chat_id'])."',";

        $sql .= "`date_added` = NOW(),
        `date_modified` = NOW()";
        self::$db->query($sql);

        $id = self::$db->getLastId();

        if(!isset($data['hidden']) || !$data['hidden']){
            chats::set($data);
        }

        if(preg_match('/^(false_)(.*)$/',$data['message_id'])){
            $sql = "DELETE FROM `" . DB_PREFIX . "notification_setting` WHERE `name` = 'new_message'";
            self::$db->query($sql);

            $sql = "INSERT INTO `" . DB_PREFIX . "notification_setting` SET `name` = 'new_message', `data` = '".json_encode(time())."'";
            self::$db->query($sql);
        }

        return $id;
    }

    public static function edit($data){
        self::init();
        $sql = "UPDATE `" . DB_PREFIX . "notification_messages` SET 
        `status` = '".self::$db->escape($data['status'])."',";

        if(!empty($data['body'])){
            $sql .= "`body` = '".self::$db->escape(json_encode($data['body']))."',";
        }
        
        $sql .= "`date_modified` = NOW()";

        $sql .= " WHERE `message_id` = '".self::$db->escape($data['message_id'])."'";

        self::$db->query($sql);

        if(isset($data['hidden']) && !$data['hidden']){
            chats::edit($data);
        }

        return $data['id'];
    }

    public static function get($data){
        self::init();
        $sql = "SELECT * FROM `" . DB_PREFIX . "notification_messages`";

        $sql .= " WHERE `message_id` = '".self::$db->escape($data['message_id'])."'";

        $message = self::$db->query($sql)->row;

        if($message){
            self::fill($message);
        }

        return $message;
    }

    public static function getMessages($data){
        self::init();
        $sql = "SELECT * FROM `" . DB_PREFIX . "notification_messages` WHERE id > 0";

        if(isset($data['chat_id'])){
            $sql .= " AND `chat_id` = '".self::$db->escape($data['chat_id'])."'";
        }

        if(isset($data['hidden']) && $data['hidden']){
            $sql .= " AND `hidden` = 0";
        }

        if(isset($data['hidden']) && !$data['hidden']){
            $sql .= " AND `hidden` = 1";
        }

        if(isset($data['date']) && strtotime($data['date'])){
            $sql .= " AND `date_added` > '".date('Y-m-d H:i:s', strtotime($data['date']))."'";
        }

        $sql .= " ORDER BY date_added DESC";

        $sql .= " LIMIT " . (int) $data['start'] . "," . (int)$data['limit'];
        
        $messages = [];
        foreach(self::$db->query($sql)->rows as $message){
            self::fill($message);
            $messages[] = $message;
        }

        return $messages;
    }

    public static function fill(&$message){
        if($message){
            if(preg_match('/^(true_)(.*)$/',$message['message_id'])){
                $message['direction'] = 'from-me';
            }else{
                $message['direction'] = 'to-me';
            }
            $message['body'] = json_decode($message['body'], 1);
        }
    }

    public static function getLastMessage($data){
        $fdata = [
            'start' => 0,
            'limit' => 1,
            'hidden' => true,
            'chat_id' => $data['chat_id']
        ];

        $query = self::getMessages($fdata);

        return isset($query[0]) ? $query[0] : [];
    }

    public static function total($data){
        self::init();
        $sql = "SELECT* FROM `" . DB_PREFIX . "notification_messages` WHERE id > 0";

        if(isset($data['chat_id'])){
            $sql .= " AND `chat_id` = '".self::$db->escape($data['chat_id'])."'";
        }

        if(isset($data['hidden']) && $data['hidden']){
            $sql .= " AND `hidden` = 0";
        }

        if(isset($data['hidden']) && !$data['hidden']){
            $sql .= " AND `hidden` = 1";
        }

        if(isset($data['date']) && strtotime($data['date'])){
            $sql .= " AND `date_added` > '".date('Y-m-d H:i:s', strtotime($data['date']))."'";
        }
        
        return self::$db->query($sql)->row['total'];
    }

    public static function createTable(){
        self::init();

        $sql = "SHOW TABLES FROM `".DB_DATABASE."` LIKE '" . DB_PREFIX . "notification_messages'";

        if(self::$db->query($sql)->row){
            return false;
        }

        $sql = "CREATE TABLE `" . DB_PREFIX . "notification_messages` (
            `id` int NOT NULL,
            `chat_id` varchar(255) NOT NULL,
            `message_id` varchar(255) NOT NULL,
            `type` varchar(255) NOT NULL,
            `status` varchar(255) NOT NULL,
            `hidden` int NOT NULL DEFAULT '0',
            `option` varchar(255) NOT NULL,
            `body` text NOT NULL,
            `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
            `date_modified` datetime DEFAULT CURRENT_TIMESTAMP
          )";
        self::$db->query($sql);

        $sql = "ALTER TABLE `" . DB_PREFIX . "notification_messages`
            ADD PRIMARY KEY (`id`),
            ADD UNIQUE KEY `message_id` (`message_id`),
            ADD KEY `chat_id` (`chat_id`),
            ADD KEY `hidden` (`hidden`)";
        self::$db->query($sql);

        $sql = "ALTER TABLE `" . DB_PREFIX . "notification_messages`
            MODIFY `id` int NOT NULL AUTO_INCREMENT;";
        self::$db->query($sql);
    }
}