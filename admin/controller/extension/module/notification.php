<?php
class ControllerExtensionModuleNotification extends Controller {
    public function index(){
        $this->response->redirect($this->url->link('service/notification/client', 'token=' . $this->session->data['token'], true));
    }

    public function install(){
        $this->load->controller('service/notification/client/install');
    }

    public function uninstall(){
        $this->load->controller('service/notification/client/install');
    }
}