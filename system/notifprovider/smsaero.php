<?php
namespace Notif;
final class Smsaero{
	private $url = 'https://{EMAIL}:{API_KEY}@gate.smsaero.ru/v2/sms/send?{PHONES}&text={MESSAGE}&sign={NAME}';
	public $data;
	public function send($data) {
		$find = [
			'{EMAIL}',
			'{API_KEY}',
			'{MESSAGE}',
			'{PHONES}',
			'{NAME}'
		];

		$phones = explode(',', $data['phones']);
		$p = [];

		foreach($phones as $phone){
			$phone = preg_replace('/[^0-9]/', '', $phone);
			$p[] = $phone;
		}

		$phones = 'numbers[]=' . implode('&numbers[]=', $p);

		$replace = [
			$this->data['email'],
			$this->data['api_key'],
			urlencode($data['message']),
			$phones,
			$this->data['name'],
		];

		$url = str_replace($find, $replace, $this->url);
		$this->curlGet($url);
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
