<?php
class ControllerExtensionModuleWhatsapp extends Controller {
    public function index(){
        $this->response->redirect($this->url->link('service/notification/whatsapp', 'token=' . $this->session->data['token'], true));
    }
}