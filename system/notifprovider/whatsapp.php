<?php
namespace Notif;
final class Whatsapp {
	public function status(){
		$url = '/status';
		return $this->curlGet($url);
	}

	public function getChatInfo($chatId){
		$url = '/dialog';
		return $this->curlGet($url,['chatId' => $chatId]);
	}

	public function setWebhook(){
		$url = '/webhook';
		$data = [
			'webhookUrl' => HTTPS_SERVER . 'index.php?route=service/notif_webhook'
		];
		$this->curlPost($url, [], $data);
	}

	private $request;
	public function webhook($request){
		$this->request = $request;

		$postData = file_get_contents('php://input');

		$this->request->post = $this->request->clean(json_decode($postData, true));

		if(isset($this->request->post['ack'])){
			$this->ack();
		}

		if(isset($this->request->post['messages'])){
			$this->messages();
		}

		if(isset($this->request->post['chatUpdate'])){
			$this->chatUpdate();
		}
	}

	private function ack(){
		foreach($this->request->post['ack'] as $ack){
			$data = \whatsapp\messages::get(['message_id' => $ack['id']]);
			if($ack['status']!='sent'){
				$data['status'] = $ack['status'];
				\whatsapp\messages::edit($data);
			}
		}
	}

	private function messages(){
		foreach($this->request->post['messages'] as $message){
			$data = \whatsapp\messages::get(['message_id' => $message['id']]);
			$data['message_id'] = $message['id'];
			$data['type'] = $message['type'];
			$data['body'] = $message['body'];
			$data['chat_id'] = $message['chatId'];

			\whatsapp\messages::set($data);
		}
	}

	private function chatUpdate(){
		foreach($this->request->post['chatUpdate'] as $chat){
			
		}
	}

	private $hidden = null;

	private $lastId = null;

	/**
	 * phonesOrChatIds : mixed
	 * message : string
	 * messageId : optional|int
	 */
	public function send($data){

		$this->hidden = null;

		$this->chatIds = [];

		if(isset($data['hidden'])){
			$this->hidden = $data['hidden'];
		}

		$phonesOrChatIds = $data['phones'];

		$message = $data['message'];

		$this->fillRecipient($phonesOrChatIds);

		foreach($this->chatIds as $chatId){
			$this->sendToChat($chatId, $message);
		}

		return $this->lastId;
	}

	private $chatIds = [];

	private function fillRecipient($phonesOrChatIds){
		if(!is_array($phonesOrChatIds)){
			$phonesOrChatIds = explode(',', $phonesOrChatIds);
		}

		foreach($phonesOrChatIds as $recipient){
			$phone = preg_replace('/[^0-9]/', '', $recipient);
			$phone = preg_replace('/^(7|8)([0-9]{10})$/', '7$2', $phone);
			if(strlen($phone) == 11){
				$this->chatIds[] = $phone . '@c.us';
			}

			$chatId = preg_replace('/[^0-9\-]/', '', $recipient);
			if(preg_match('/^[0-9\-](@c\.us)/', $chatId)){
				$this->chatIds[$chatId] = $chatId;
			}
		}
	}

	private function sendToChat($chatId, $message){
		$data = [
			'chatId' => $chatId,
			'body' => $message
		];
		$this->sendMessage($data);
	}

	private function sendMessage($data){
		$url = '/sendMessage';

		$response = $this->curlPost($url, [], $data);

		$this->updateMessage($response, $data);
	}

	private function updateMessage($response, $data){
		$chat_id = '';

		if(isset($data['chatId'])){
			$chat_id = $data['chatId'];
		}

		$data = [
			'message_id' => $response['id'],
			'type' => 'chat',
			'status' => 'send',
			'body' => $data['body'],
			'chat_id' => $chat_id
		];

		if($this->hidden !== null){
			$data['hidden'] = $this->hidden;
		}

		$this->lastId = \whatsapp\messages::set($data);
	}

	private function curlGet($url, array $get = []) {
		$url = $this->data['url'] . $url;
		$url .= '?token=' . $this->data['token'];
		if($get){
			$url .= "&" . http_build_query($get);
		}

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
	
	private function curlPost(string $url, array $params = [], array $data = []){
		$url = $this->data['url'] . $url;
		$url .= '?token=' . $this->data['token'];
		if($params){
			$url .= "&" . http_build_query($params);
		}

		$data_string = json_encode($data);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
		);
		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result, true);

		return $result;
	}
}
