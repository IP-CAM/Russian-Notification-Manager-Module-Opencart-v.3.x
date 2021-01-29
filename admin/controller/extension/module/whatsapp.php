<?php
class ControllerExtensionModuleWhatsapp extends Controller {
    public function index(){
        $this->response->redirect($this->url->link('service/notification/whatsapp', 'user_token=' . $this->session->data['user_token'], true));
    }
}