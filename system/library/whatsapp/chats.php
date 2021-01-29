<?php
namespace Whatsapp;
class Chats{
    private static $db;
    private static function init(){
        if(!self::$db){
            self::$db = New \Db(DB_DRIVER,DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE,DB_PORT);
        }
    }
    public static function set($data){
        if($chat = self::get($data)){
            $data = array_merge($chat, $data);
            return self::edit($data);
        }
        
        self::init();

        $whatsapp = new \Notif();

        $chatInfo = $whatsapp->getChatInfo($data['chat_id']);

        if(!isset($data['name']) || !$data['name']){
            $data['name'] = $chatInfo['name'];
        }

        $data['user_id'] = 0;

        $data['image'] = $chatInfo['image'];

        $sql = "INSERT INTO `" . DB_PREFIX . "notification_chats` SET 
        `chat_id` = '".self::$db->escape($data['chat_id'])."',
        `name` = '".self::$db->escape($data['name'])."',
        `image` = '".self::$db->escape($data['image'])."',
        `user_id` = '".(int)$data['user_id']."',
        `date_modified` = NOW()";
        self::$db->query($sql);
    }

    public static function edit($data){
        self::init();

        $sql = "UPDATE `" . DB_PREFIX . "notification_chats` SET ";

        if(isset($data['name']) && $data['name']){ 
            $sql .= "`name` = '".self::$db->escape($data['name'])."',";
        }

        if(isset($data['image']) && $data['image']){ 
            $sql .= "`image` = '".self::$db->escape($data['image'])."',";
        }

        if(isset($data['user_id'])){ 
            $sql .= "`user_id` = '".self::$db->escape($data['user_id'])."',";
        }

        $sql .= "`date_modified` = NOW()";

        $sql .= " WHERE `chat_id` = '".self::$db->escape($data['chat_id'])."'";

        self::$db->query($sql);
    }

    public static function get($data){
        self::init();
        $sql = "SELECT * FROM `" . DB_PREFIX . "notification_chats`";

        $sql .= " WHERE `chat_id` = '".self::$db->escape($data['chat_id'])."'";

        // if(isset($data['user_id'])){
        //     $sql .= " AND `user_id` = '".(int)$data['user_id']."'";
        // }

        $chat = self::$db->query($sql)->row;

        self::fill($chat);
        
        return $chat;
    }

    public static function getChats($data){
        self::init();
        $sql = "SELECT * FROM `" . DB_PREFIX . "notification_chats` WHERE id > 0";

        // if(isset($data['user_id'])){
        //     $sql .= " AND user_id = '".(int)$data['user_id']."'";
        // }

        $sql .= " ORDER BY date_modified DESC";

        $sql .= " LIMIT " . (int) $data['start'] . "," . (int)$data['limit'];

        $chats = [];

        foreach(self::$db->query($sql)->rows as $chat){
            self::fill($chat);
            $chats[] = $chat;
        }

        return $chats;
    }

    public static function total($data){
        self::init();
        $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "notification_chats` WHERE id > 0";

        // if(isset($data['user_id'])){
        //     $sql .= " AND user_id = '".(int)$data['user_id']."'";
        // }

        return self::$db->query($sql)->row['total'];
    }

    public static function fill(&$chat){
        if($chat){
            if(!$chat['image']){
                $chat['image'] = '/image/user_icon.svg';
            }
        }
    }

    public static function createTable(){
        self::init();
        $sql = "SHOW TABLES FROM `".DB_DATABASE."` LIKE '" . DB_PREFIX . "notification_chats'";

        if(self::$db->query($sql)->row){
            return false;
        }

        $sql = "CREATE TABLE `" . DB_PREFIX . "notification_chats` (
            `id` int NOT NULL,
            `chat_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
            `name` varchar(255) NOT NULL,
            `image` text NOT NULL,
            `user_id` int NOT NULL,
            `date_modified` datetime DEFAULT CURRENT_TIMESTAMP
            )";
        self::$db->query($sql);

        $sql = "ALTER TABLE `" . DB_PREFIX . "notification_chats`
            ADD PRIMARY KEY (`id`),
            ADD UNIQUE KEY `chat_id` (`chat_id`),
            ADD KEY `user_id` (`user_id`)";
        self::$db->query($sql);
        
        $sql = "ALTER TABLE `" . DB_PREFIX . "notification_chats`
            MODIFY `id` int NOT NULL AUTO_INCREMENT;";
        self::$db->query($sql);
    }
}