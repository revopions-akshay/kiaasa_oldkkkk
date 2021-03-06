<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Razorpay\Api\Api;
require_once FCPATH . '/vendor/autoload.php'; // change path as needed


class MY_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();

        $this->load->model('datatable/data_table_factory_model');


        // ADMIN MODELS
        $this->backend_models = array(
            0=>'product_m',
            1=>'store_m',
            2=>'state_m',
            3=>'product_inventory_m'

        );
        
        foreach ($this->backend_models as $key => $model_name) {
            $this->load->model('backend/'.$model_name);
        }

    
       // WEB MODELS
        date_default_timezone_set('Asia/Kolkata');
        $this->current_time = date('Y-m-d H:i:s', now('asia/kolkata'));
        $this->today = date('Y-m-d H:i:s');
        $this->year = date('Y');
        $this->month = date('F');
        $this->expiry = date("Y-m-d H:i:s", time() + (60 * 60 * 8));
        require(APPPATH.'third_party/jwt/JWT.php');
        require(APPPATH.'third_party/phpmailer/src/Exception.php');
        require(APPPATH.'third_party/phpmailer/src/PHPMailer.php');
        require(APPPATH.'third_party/phpmailer/src/SMTP.php');
        require(APPPATH.'third_party/paytm/encdec_paytm.php');
        require(APPPATH.'third_party/razorpay/Razorpay.php');
        require(APPPATH.'third_party/sms_helper/smsalert.php');


        $this->uid = $this->session->userdata('uid');
        // $this->role_id = $this->session->userdata('role_id');
        // $this->role_name = $this->session->userdata('role_name');
        // $this->firstname = $this->session->userdata('firstname');
        // $this->lastname = $this->session->userdata('lastname');
        // $this->phone = $this->session->userdata('mobile_no');
        // $this->username = $this->session->userdata('email');
        // $this->is_logged_in = $this->session->userdata('is_logged_in');
        

        


        $this->razorpay = new Api(RAZOR_KEY, RAZOR_KEY_SECRET);
        $this->apikey = '5caca2914e6dc'; // write your apikey in between ''
        $this->senderid = 'BBLONT'; // write your senderid in between ''
        $this->route = 'transactional'; // write your route in between ''
        $this->smsalert = new Smsalert($this->apikey, $this->senderid, $this->route);
        $this->client = new \GuzzleHttp\Client();

    }

    public function create_url($string) {
      $string = strtolower($string);
      $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
      $string = preg_replace("/[\s-]+/", " ", $string);
      $string = preg_replace("/[\s_]/", "-", $string);
      return $string;
    }

    public function change_routes_name_url(){
      $brand_arr = $this->db->get_obj('brand')->result_array();

      foreach ($brand_arr as $key => $value) {
        $newurl = $this->create_url($value['routes_name']);
        $this->db->set('routes_name',$newurl);
        $this->db->where('brid',$value['brid']);
        $this->db->update('brand');
      }

    }


    public function verify_signin() {
      if (!$this->active) {
          echo json_encode(['status'=>403,'message'=>'Please sign in.']);
      } else {
          return;
      }
    }

    
    



// REMOVE FROM MY CONTROLLER
     public function array_from_post($data){
        $result = array();
        foreach ($data as $d) {
            $current_data = $this->input->post($d);
            $current_data = strtolower($current_data);
            $current_data = addslashes($current_data);
            $result[$d] = $current_data;
        }
        return $result;
    }

   // REMOVE FROM MY CONTROLLER
    public function toLowerCase($data){
      foreach ($data as $key => $value) {
        $data[$key] = strtolower($value);
      }
      return $data;
    }

    public function is_logged_in() {
        if (!$this->is_logged_in) {
            redirect();
        } else {
            return;
        }
    }
    
    public function header() {
        $this->is_logged_in();
        $this->data['header_title'] = 'header';
        $this->load->view('admin/includes/header', $this->data);
    }
    public function view($template_view, $data) {
        $this->header();
        $this->load->view($template_view, $data);
        $this->load->view('admin/includes/footer');
    }

    public function web_footer(){
        $this->data['footer_title'] = 'footer';
        $this->load->view('web/includes/footer', $this->data);
    }


    

    

    private function color(){
        return [0=>'red',1=>'blue'];
    }

    


    

    
    

    

     



// CLASS ENDS
}

class TableFactory {
    public function renderTableHead($tableHeadArr, $page_title, $tableId, $pl,$component=null){
        ?>
            <div class="container-fluid page__container page-section">
                
                <div class="page-separator">
                    <div class="page-separator__text"><?php echo ( $pl ? '<a class="btn btn-primary" href="'.base_url($pl).'">Add </a>' : $component) ?></div>
                </div>

                <div class="card mb-lg-32pt">
                    <div class="table-responsive" data-toggle="lists" data-lists-sort-by="js-lists-values-date" data-lists-sort-desc="true" data-lists-values="[&quot;js-lists-values-name&quot;, &quot;js-lists-values-company&quot;, &quot;js-lists-values-phone&quot;, &quot;js-lists-values-date&quot;]">

                        <table class="table mb-0 thead-border-top-0 table-nowrap" id="<?php echo $tableId; ?>">
                            <thead>
                                <tr>
                                    <?php
                                        foreach ($tableHeadArr as $key) {
                                            ?>
                                                <th><?php echo ucwords($key); ?></th>
                                            <?php
                                        }
                                    ?>
                                </tr>
                            </thead>
                            <tbody class="list">
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
    <?php }
// CLASS ENDS
}