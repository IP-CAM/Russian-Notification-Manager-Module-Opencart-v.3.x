<?php
namespace Notif;
final class Alphasms {
	private $url = 'http://api.alfasms.info/?method=push_msg&email={LOGIN}&password={PASSWORD}&text={MESSAGE}&phone={PHONE}&sender_name={NAME}';
	public $data;
	public function send($data) {
		$find = [
			'{LOGIN}',
			'{PASSWORD}',
			'{MESSAGE}',
			'{PHONE}',
			'{NAME}'
		];

		$phones = explode(',', $data['phones']);
		foreach($phones as $phone){
			$phone = preg_replace('/[^0-9]/', '', $phone);

			$replace = [
				$this->data['login'],
				$this->data['password'],
				urlencode($data['message']),
				$phone,
				$this->data['name'],
			];

			$url = str_replace($find, $replace, $this->url);
			$this->curlGet($url);
		}
	}

	private function curlGet($url) {
		$options = array(
		  CURLOPT_URL => $url,
		  CURLOPT_HEADER => 0,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0,
		);
	
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		curl_close($ch);
	
		$result = json_decode($result, true);
	
		return $result;
	}
}
