<?php
class ControllerServiceNotif extends Controller {
    private $notif;
    public function beforeOrderHistory($args){
        $order_id = $args[0];
        $order_status_id = $args[1];

        $comment = '';
        $notify = false;
        $override = false;
        if(isset($args[2])){
            $comment = $args[2];
        }
        if(isset($args[3])){
            $notify = $args[3];
        }
        if(isset($args[4])){
            $override = $args[4];
        }

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($order_id);

		if((int)$order_info['order_status_id'] === (int)$order_status_id){
			return false;
        }

        $this->load->model('service/notification/client');
        $setting = $this->model_service_notification_client;

        $admin_phones = $setting->get('admin_phones');

        if((int)$order_info['order_status_id'] === 0){
            $status_info = $setting->get('new_order');
            $admin_status_info = $setting->get('admin_new_order');
        }else{
            $status_info = $setting->get('order_status_' . $order_status_id);
            $admin_status_info = $setting->get('admin_order_status_' . $order_status_id);
        }

        $status = $setting->get('status');

        $this->notif = new Notif();

        if($status && $status_info && (isset($status_info['status']) && $status_info['status']) && $status_info['message']){
            $this->orderHistoryCustomerNotif([
                'order_id' => $order_id,
                'order_info' => $order_info,
                'status_info' => $status_info
            ]);
        }

        if($status && $admin_status_info && (isset($admin_status_info['status']) && $admin_status_info['status']) && $admin_status_info['message']){
            $this->orderHistoryAdminNotif([
                'order_id' => $order_id,
                'order_info' => $order_info,
                'status_info' => $admin_status_info,
                'phones' => $admin_phones
            ]);
        }
    }

    public function orderHistoryCustomerNotif($data){
        extract($data);
        $find = [
            '{ID}',
            '{DATE}',
            '{TIME}',
            '{SUM}',
            '{NAME}',
            '{COMMENT}',
            '{STATUS}'
        ];
        
        $replace = [
            $order_id,
            date('d-m-Y', strtotime($order_info['date_added'])),
            date('H:i', strtotime($order_info['date_added'])),
            $this->currency->format($order_info['total'], $order_info['currency_code']),
            trim($order_info['firstname'] . ' ' . $order_info['lastname']),
            $order_info['comment'],
            $order_info['order_status']
        ];
        
        $message = str_replace($find, $replace, $status_info['message']);

        $this->notif->send(['phones' => $order_info['telephone'], 'message' => $message, 'hidden' => true]);
    }

    public function orderHistoryAdminNotif($data){
        extract($data);
        $find = [
            '{ID}',
            '{DATE}',
            '{TIME}',
            '{SUM}',
            '{NAME}',
            '{COMMENT}',
            '{STATUS}'
        ];
        
        $replace = [
            $order_id,
            date('d-m-Y', strtotime($order_info['date_added'])),
            date('H:i', strtotime($order_info['date_added'])),
            $this->currency->format($order_info['total'], $order_info['currency_code']),
            trim($order_info['firstname'] . ' ' . $order_info['lastname']),
            $order_info['comment'],
            $order_info['order_status']
        ];
        
        $message = str_replace($find, $replace, $status_info['message']);

        $this->notif->send(['phones' => $phones, 'message' => $message, 'hidden' => true]);
    }

    public function afterAddCustomer($args){

        $this->load->model('account/customer');

        $customer_info = $args[0];

        $customer_id = $args[1];

        $this->load->model('service/notification/client');
        $setting = $this->model_service_notification_client;

        $status_info = $setting->get('register_customer');

        $admin_status_info = $setting->get('admin_register_customer');

        $status = $setting->get('status');

        $admin_phones = $setting->get('admin_phones');

        $this->notif = new Notif();

        if($status && $status_info && (isset($status_info['status'])) && $status_info['status'] && $status_info['message']){
            $this->addCustomerCustomerNotif([
                'status_info' => $status_info,
                'customer_info' => $customer_info
            ]);
        }

        if($status && $admin_status_info && (isset($admin_status_info['status'])) && $admin_status_info['status'] && $admin_status_info['message']){
            $this->addCustomerAdminNotif([
                'status_info' => $admin_status_info,
                'customer_info' => $customer_info,
                'phones' => $admin_phones
            ]);
        }
    }

    public function addCustomerCustomerNotif($data){
        extract($data);
        $find = [
            '{PASSWORD}',
            '{DATE}',
            '{TIME}',
            '{EMAIL}',
            '{PHONE}',
            '{NAME}'
        ];
        
        $replace = [
            $customer_info['password'],
            date('d-m-Y', strtotime($customer_info['date_added'])),
            date('H:i', strtotime($customer_info['date_added'])),
            $customer_info['email'],
            $customer_info['telephone'],
            trim($customer_info['firstname'] . ' ' . $customer_info['lastname'])
        ];
        
        $message = str_replace($find, $replace, $status_info['message']);

        $this->notif->send(['phones' => $customer_info['telephone'], 'message' => $message, 'hidden' => true]);
    }

    public function addCustomerAdminNotif($data){
        extract($data);
        $find = [
            '{PASSWORD}',
            '{DATE}',
            '{TIME}',
            '{EMAIL}',
            '{PHONE}',
            '{NAME}'
        ];
        
        $replace = [
            $customer_info['password'],
            date('d-m-Y', strtotime($customer_info['date_added'])),
            date('H:i', strtotime($customer_info['date_added'])),
            $customer_info['email'],
            $customer_info['telephone'],
            trim($customer_info['firstname'] . ' ' . $customer_info['lastname'])
        ];
        
        $message = str_replace($find, $replace, $status_info['message']);

        $this->notif->send(['phones' => $phones, 'message' => $message, 'hidden' => true]);
    }
}