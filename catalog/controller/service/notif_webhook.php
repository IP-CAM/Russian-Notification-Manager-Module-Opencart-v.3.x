<?php
class ControllerServiceNotifWebhook extends Controller {
    public function index(){
        $notif = new Notif();

        $notif->webhook($this->request);

        echo json_encode("ok");
    }
}