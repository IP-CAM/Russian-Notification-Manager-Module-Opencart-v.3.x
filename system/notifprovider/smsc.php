<?php
namespace Notif;
final class Smsc{
	private $url = 'https://smsc.ru/sys/send.php?login={LOGIN}&psw={PASSWORD}&phones={PHONES}&mes={MESSAGE}';
	public $data;
	public function send($data) {
		$find = [
			'{LOGIN}',
			'{PASSWORD}',
			'{MESSAGE}',
			'{PHONES}'
		];

		$phones = explode(',', $data['phones']);
		$p = [];
		foreach($phones as $phone){
			$phone = preg_replace('/[^0-9]/', '', $phone);
			$p[] = $phone;
		}

		$replace = [
			$this->data['login'],
			$this->data['password'],
			urlencode($data['message']),
			implode(',', $p)
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
