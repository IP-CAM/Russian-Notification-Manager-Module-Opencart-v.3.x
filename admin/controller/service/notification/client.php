<?php
class ControllerServiceNotificationClient extends Controller {
    protected $error;
    protected $keys = [
        'status',
        'provider',
        'provider_info',
        'register_customer',
        'new_order',
        'admin_register_customer',
        'admin_new_order',
        'admin_phones'
    ];

    protected $setting;

    public function index(){
        $data = [];

        $this->load->model('service/notification/client');
        $this->setting = $this->model_service_notification_client;

        $this->load->model('localisation/order_status');
        $order_statuses = $this->model_localisation_order_status->getOrderStatuses();

        $this->fillKeys($order_statuses, $data);

        $data['order_statuses'] = $order_statuses;

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->save();
		}

        $this->fillData($data);

        $this->fillLang($data);

        $data['provider_info'] = $data['provider_info'] ? json_encode($data['provider_info']) : '{}';
        $data['provider_form'] = $data['provider_form'] ? json_encode($data['provider_form']) : false;

		$this->document->setTitle($data['heading_title']);
        
        $this->breadcrumbs($data);

        $data['providers'] = array();
        
        $data['user_token'] = $this->session->data['user_token'];

        $data['action'] = $this->url->link('service/notification/client', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$files = glob(DIR_SYSTEM . 'notifprovider/*.php');

		foreach ($files as $file) {
			$data['providers'][] =  basename($file, '.php');
        }

        $data['data'] = $data;
        
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('service/notification/client', $data));
    }

    public function fillKeys($order_statuses, &$data){
        foreach($order_statuses as $order_status){
            $this->keys[] = 'order_status_'.$order_status['order_status_id'];
            $this->keys[] = 'admin_order_status_'.$order_status['order_status_id'];
        }
    }

    public function fillData(&$data){
        foreach($this->keys as $key){
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } else {
                $data[$key] = $this->setting->get($key);
            }
        }
        $data['open_whatsapp'] = $this->url->link('service/notification/whatsapp', 'user_token='.$this->session->data['user_token']);
    }

    public function fillLang(&$data){
        foreach($this->load->language('service/notification/client') as $key => $val){
            $data[$key] = $val;
        }

        $data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
    }

    public function breadcrumbs(&$data){
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('service/notification/client', 'user_token=' . $this->session->data['user_token'], true)
		);
    }

    protected function save(){
        $this->setting->clear();

        $this->setting->set($this->request->post);

        $this->session->data['success'] = $this->language->get('text_success');

        $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
    }

    public function validate(){
		if (!$this->user->hasPermission('modify', 'service/notification/client')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
    }
    
    public function install(){
        $this->load->model('service/notification/client');
        $this->model_service_notification_client->install();

        $this->load->model('user/user_group');

        $this->load->model('setting/event');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'service/notification/client');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'service/notification/client');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'service/notification/whatsapp');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'service/notification/whatsapp');
        
        $this->removeOldModule();
    }

    public function uninstall(){
        $this->load->model('service/notification/client');
        $this->model_service_notification_client->uninstall();

        $this->load->model('setting/event');
        $this->model_setting_event->deleteEvent('before_order_history');

        $this->model_setting_event->deleteEvent('after_add_customer');
    }

    public function removeOldModule(){
        $files = [
            'admin/view/image/115314453928476.png',
            'admin/controller/extension/module/alertclient.php',
            'admin/language/ru-ru/extension/module/alertclient.php',
            'admin/model/extension/module/alertclient.php',
            'catalog/controller/extension/module/alertclient.php',
            'catalog/controller/smsgate/alertclient.php',
            'catalog/model/extension/module/alertclient.php',
            'admin/view/template/extension/module/alertclient/alertclient.twig',
            'admin/view/template/extension/module/alertclient/allsends.twig',
            'system/smsgate/alphasms.php',
            'system/smsgate/atomic.php',
            'admin/view/template/extension/module/alertclient/dialog.twig',
            'system/smsgate/intisSMS.php',
            'system/library/sms.php',
            'system/smsgate/smsaero.php',
            'system/smsgate/smsc.php',
            'system/smsgate/smsint.php',
            'system/smsgate/smsru.php',
            'system/smsgate/whatsapp.php'
        ];
        $dir = str_replace('admin/','',DIR_APPLICATION);
        foreach($files as $file){
            $file = $dir . $file;
            if(is_file($file)){
                unlink($file);
            }
        }
    }
}