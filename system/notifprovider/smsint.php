<?php
namespace Notif;
final class Smsint{
	private $url = 'https://lcab.smsint.ru/lcabApi/sendSms.php?login={LOGIN}&password={PASSWORD}&txt={MESSAGE}&to={PHONES}&sender={NAME}';
	public $data;
	public function send($data) {

		$find = [
			'{LOGIN}',
			'{PASSWORD}',
			'{NAME}',
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
			$this->data['name'],
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
