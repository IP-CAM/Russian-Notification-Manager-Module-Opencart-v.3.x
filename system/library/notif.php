<?php
class Notif {
	private $provider;
	private $data;
	private $db;
	/**
	 * @param mixed $phones
	 * @param string $message
	 * @param int | null @messageId
	 */

	public function __construct($data = []) {
		if(!$this->provider){
			$this->db = New Db(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

			$this->provider = $this->getSetting('provider');

			$status = $this->getSetting('status');

			if(!$status){
				return;
			}

			$this->data = $this->getSetting('provider_info');

			$this->_include();

			$class = 'Notif\\' . $this->provider;

			$this->provider = new $class;
			$this->provider->db = $this->db;
			$this->provider->data = $this->data;

			if(isset($data['message']) && isset($data['phones']) && $data['message'] && $data['phones']){
				$this->provider->send($data);
			}
		}
	}

	public function __call(string $method, $params){
		if(method_exists($this->provider, $method)){
			return call_user_func_array([$this->provider, $method], $params);
		}
	}

	private function getSetting($name){
        $sql = "SELECT `data` as data FROM `" . DB_PREFIX . "notification_setting` WHERE `name` = '".$this->db->escape($name)."'";
        return json_decode($this->db->query($sql)->row['data'], 1);
	}
	
	private function _include(){
		if (file_exists(DIR_SYSTEM . 'notifprovider/' . $this->provider . '.php')) {
			require_once(DIR_SYSTEM . 'notifprovider/' . $this->provider . '.php');
		} else {
			trigger_error('Error: Could not load file ' . DIR_SYSTEM . 'notifprovider/' . $this->provider . '.php!');
			exit();
		}
	}
}