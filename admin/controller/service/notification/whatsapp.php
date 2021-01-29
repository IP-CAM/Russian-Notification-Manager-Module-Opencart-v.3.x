<?php
class ControllerServiceNotificationWhatsapp extends Controller {
    private $error;

    public function index(){
        $filter_data = [
            'start' => 0,
            'limit' => 50,
        ];

        if(!$this->permission()){
            $filter_data['user_id'] = (int)$this->user->getId();
        }

        $chats = whatsapp\chats::getChats($filter_data);

        $this->fillLang($data);

        $data['user_token'] = $this->session->data['user_token'];

        $data['chats'] = [];

        $data['messages'] = [];

        $whatsapp = new Notif();

        $i = 0;
        foreach($chats as $chat){
            $i++;
            $last_message = whatsapp\messages::getLastMessage($chat);

            if($last_message){
                $chat['date_added'] = date('H:i', strtotime($last_message['date_added']));
                $chat['last_message'] = mb_substr($last_message['body'], 0, 50);

                if(mb_strlen($last_message['body']) > 50){
                    $chat['last_message'] . "...";
                }
            }else{
                $chat['date_added'] = date('H:i');
                $chat['last_message'] = '';
            }

            $data['chats'][] = $chat;
        }

        $data['back'] = $this->url->link('service/notification/client', 'user_token=' . $this->session->data['user_token'], true);

        $data['time'] = time();

        $this->response->setOutput($this->load->view('service/notification/whatsapp', $data));
    }

    public function fillLang(&$data){
        foreach($this->load->language('service/notification/whatsapp') as $key => $val){
            $data[$key] = $val;
        }
    }

    public function getMessages(){
        if(isset($this->request->post['date'])){
            $filter_data['date'] = $this->request->post['date'];
        }

        if(isset($this->request->post['chat_id'])){
            $filter_data['chat_id'] = $this->request->post['chat_id'];
        }else{
            return false;
        }

        $filter_data['start'] = 0;
        $filter_data['limit'] = 50;

        if(!$this->permission()){
            $filter_data['user_id'] = (int)$this->user->getId();
        }

        $messages = whatsapp\messages::getMessages($filter_data);
        
        foreach($messages as $key => $message){
            $messages[$key]['date'] = date('d/m', strtotime($message['date_added']));
            $messages[$key]['time'] = date('H:i', strtotime($message['date_added']));
        }

        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode(array_reverse($messages)));
    }

    public function getChats(){
        $filter_data['start'] = 0;
        $filter_data['limit'] = 10;

        if(!$this->permission()){
            $filter_data['user_id'] = (int)$this->user->getId();
        }

        $chats = whatsapp\chats::getChats($filter_data);
        
        foreach($chats as $key => $chat){
            $last_message = whatsapp\messages::getLastMessage($chat);
            $chats[$key]['date_added'] = date('H:i', strtotime($last_message['date_added']));
            $chats[$key]['last_message'] = mb_substr($last_message['body'], 0, 50);

            if(mb_strlen($last_message['body']) > 50){
                $chats[$key]['last_message'] . "...";
            }
        }

        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode(array_reverse($chats)));
    }

    public function send(){
        $this->response->addHeader('Content-Type: application/json');

        $message = trim($this->request->post['message']);
        if(!$message){
            return $this->response->setOutput(json_encode("no message"));
        }

        $chat_id = trim($this->request->post['chat_id']);
        if(!$chat_id){
            return $this->response->setOutput(json_encode("no chat_id"));
        }

        new Notif(['phones' => $chat_id, 'message' => $message]);

		$this->response->setOutput(json_encode("ok"));
    }

    public function check(){
        $this->load->model('service/notification/client');
        $setting = $this->model_service_notification_client;
        $check = $setting->get('new_message');
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['time'=>$check]));
    }

    public function permission(){
		if (!$this->user->hasPermission('modify', 'service/notification/client')) {
			return false;
		}

		return true;
    }
}