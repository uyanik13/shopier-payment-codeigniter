<?php


use Shopier\Models\ShopierResponse;

defined('BASEPATH') OR exit('No direct script access allowed');


class shopierOdemesi extends MX_Controller {

    public $tb_users;
    public $tb_transaction_logs;
    public $payment_type;



    public function __construct(){
        parent::__construct();
        $this->tb_users            = USERS;
        $this->tb_transaction_logs = TRANSACTION_LOGS;
        $this->payment_type		   = "shopier";
        $this->load->model('model', 'help_model');
        $this->shopier_api = new ShopierApi();

    }

    public function index(){
        $path_file = APPPATH."./modules/setting/views/integrations/shopierOdemesi.php";
        if (!file_exists($path_file)) {
            redirect(cn('add_funds'));
        }
        $data = array(
            "module"        => 'add_funds',
            "amount"        => number_format((double)session('amount'), 2, '.', ''),
        );
        $this->template->build('shopierOdemesi/shopierOdemesi_form', $data);
    }

    /**
     *
     * Create payment
     *
     */
    public function create_payment(){
        $user_info = $this->help_model->get("first_name, last_name, email", $this->tb_users, ["id" => session("uid")]);
        $productData = array(
            "name"           => session('amount')." Balance Charge", // Ürün adı
            "amount"         => session('amount'), 				// Ürün fiyatı, 10 TL : 1000
            "extraData"      => session('amount'),				// Why it need to * 100
            "paymentChannel" => "1,2,3",
            "commissionType" => 2
        );
        $billingPhone   = '905456134513';
        $billingAddress   = 'inkılap mah';
        $billing_city   = 'istanbul';
        $billingCountry   = 'turkiye';
        $billing_postcode   = '34768';
        $order_id   =  hash('sha256', microtime() );
        $amount   =  $productData['amount'];

        $userData = array(
            "uid" 		        => session("uid"),
            "first_name" 		=> $user_info->first_name,
            "last_name" 		=> $user_info->last_name,
            "email" 			=> $user_info->email,
            "order_id"      	=> $order_id,
            "amount" 	        => $amount,
            "phone" 	        => $billingPhone,
            "address" 	        => $billingAddress,
            "city" 	            => $billing_city,
            "country" 	        => $billingCountry,
            "post_code" 	    => $billing_postcode,
             );

        $this->shopier_api->create_payment($userData);
        $data = array(
            "ids" 				=> ids(),
            "uid" 				=> session("uid"),
            "type" 				=> $this->payment_type,
            "transaction_id" 	=> $order_id,
            "amount" 	        => $amount,
            "created" 			=> NOW,
        );

        $this->db->insert($this->tb_transaction_logs, $data);


    }

    public function callBack(){
        
        $status =  post('status');
        $invoiceId = post('platform_order_id');
        $transactionId = post('payment_id');
        $installment = post('installment');
        $signature = post('signature');

        $user_info = $this->help_model->get("first_name, last_name, email", $this->tb_users, ["id" => session("uid")]);


        $productData = array(
            "name"           => session('amount')." Balance Charge", // Ürün adı
            "amount"         => session('amount'), 				// Ürün fiyatı, 10 TL : 1000
            "extraData"      => session('amount'),				// Why it need to * 100
            "paymentChannel" => "1,2,3",
            "commissionType" => 2
        );


        $data = array(
            "ids" 				=> ids(),
            "uid" 				=> session("uid"),
            "type" 				=> $this->payment_type,
            "transaction_id" 	=> $transactionId,
            "amount" 	        => $productData['amount'],
            "created" 			=> NOW,
        );


        if($status == 'success') {
            $this->db->insert($this->tb_transaction_logs, $data);
            $transaction_id = $this->db->insert_id();
            $user_balance = get_field($this->tb_users, ["id" => session("uid")], "balance");
            $chagre_fee    = get_option('shopierOdemesi_chagre_fee');
            $user_balance += ($productData['amount'] * 100)/(100 + $chagre_fee);
            $this->db->update($this->tb_users, ["balance" => $user_balance], ["id" => session("uid")]);
            unset_session("real_amount");
            unset_session("amount");
            set_session("transaction_id", $transaction_id);
            redirect(cn("add_funds/success"));
        }else{
            redirect(cn("add_funds/unsuccess"));
        }






    }

}
