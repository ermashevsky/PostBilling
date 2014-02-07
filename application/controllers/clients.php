<?php

/**
 * Clients
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Controllers.Clients
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://www.ci2.lcl/
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(0);
include ('application/third_party/log4php/Logger.php');
$config_log_file = APPPATH.'config/config_log4php.xml';
Logger::configure($config_log_file);
/**
 * Класс Clients содержит методы работы  с данными клиентов
 *
 * @category PHP
 * @package  Controllers.Clients
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Clients extends CI_Controller
{
	public $log;

	/**
	 * Унифицированный метод-конструктор __construct()
	 *
	 * @author Ермашевский Денис
	 */
	function __construct()
	{

		$this->log = Logger::getLogger(__CLASS__);
		parent::__construct();
		$this -> load -> library('ion_auth');
		$this -> load -> library('session');
		$this -> load -> library('form_validation');
		$this -> load -> database();
		$this -> load -> helper('url', 'form');
		$this -> breadcrumbs = array();
		$this -> breadcrumbs[] = anchor('', $this -> config -> item('breadcrumbs_index'));
		//$this->output->enable_profiler(TRUE);
	}

	/**
	 * Главный метод контроллера
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function index()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('auth/login', 'refresh');
		} else {

			$this -> load -> model('clients_model');
			$data = array();
			$data['clients_list'] = $this -> clients_model -> get_client_list();
			$this -> load -> view('header');
			$this -> load -> view('clients_list', $data);
			$this -> load -> view('left_sidebar');
		}
	}

	/**
	 * Возвращает список лицевых счетов клиента
	 * 
	 * @param int $id идентификатор
	 * 
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function accounts($id = NULL)
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('auth/login', 'refresh');
		} else {
			$id = (int) $id;
			$this -> load -> model('clients_model');
			$data = array();
			$data['client'] = $this -> clients_model -> get_client_accounts($id);
			$this -> load -> view('header');
			$this -> load -> view('client_accounts', $data, $id);
			$this -> load -> view('left_sidebar');
		}
	}
	
	/**
	 * Возвращает список лицевых счетов клиента
	 * 
	 * @param int $id идентификатор
	 * 
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function search_engine()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('auth/login', 'refresh');
		} else {
			$this -> load -> view('header');
			$this -> load -> view('search_engine');
			$this -> load -> view('left_sidebar');
		}
	}

	function countAccount($id_client)
	{
		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> countAccount($id_client);
		return $data;
	}

	function getAccountListByIdClient()
	{
		$id = trim($this -> input -> post('id'));
		$id_client = trim($this -> input -> post('id_client'));
		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> getAccountListByIdClient($id, $id_client);
		echo json_encode($data);

	}

	function copyAccount2Account()
	{
		$old_id_account = trim($this -> input -> post('old_id_account'));
		$id_client = trim($this -> input -> post('id_client'));
		$newCopyAccount = trim($this -> input -> post('newCopyAccount'));
		$close_date = trim($this -> input -> post('close_date'));
		$open_date = trim($this -> input -> post('open_date'));
		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> copyAccount2Account($old_id_account, $id_client, $newCopyAccount, $close_date, $open_date);
		echo json_encode($data);
	}

	/**
	 * Метод возвращает список услуг
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function getServiceList()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('auth/login', 'refresh');
		} else {
			$this -> load -> model('clients_model');
			$data['serviceList'] = $this -> clients_model -> getServiceType();
			return $data['serviceList'];
		}
	}
	/**
	 * Метод поиска клиентов по имени при
	 * добавлении нового клиента для исключения
	 * задвоения.
	 * @author Денис Ермашевский <egrad77@mail.ru>
	 * @return array Retun Array
	 */
	public function searchClientName(){
        $search=  $this->input->post('search');
		$this -> load -> model('clients_model');
        $query = $this -> clients_model -> getSubject($search);
        echo json_encode ($query);
        //$this->load->view('add_clients', $query);
    }

	/**
	 * Метод поиска идентификатора при добавлении нового
	 * @author Денис Ермашевский <egrad77@mail.ru>
	 * @return array Retun Array
	 */
	function searchIdentifiers()
	{
		$search=  $this->input->post('search');
		$this -> load -> model('clients_model');
        $query = $this -> clients_model -> getIdentifier($search);
		echo json_encode ($query);
	}

	/**
	 * Метод поиска лицевого счета при копировании
	 * со счета на счет.
	 * @author Денис Ермашевский <egrad77@mail.ru>
	 * @return array Retun Array
	 */
	public function searchAccount(){
        $search=  $this->input->post('search');
		$this -> load -> model('clients_model');
        $query = $this -> clients_model -> getAccount($search);
        echo json_encode ($query);
        //$this->load->view('add_clients', $query);
    }
	
	/**
	 * Метод поиска лицевого счета при копировании
	 * со счета на счет.
	 * @author Денис Ермашевский <egrad77@mail.ru>
	 * @return array Retun Array
	 */
	public function searchByAccount(){
        $search=  $this->input->post('search');
		$this -> load -> model('clients_model');
        $query = $this -> clients_model -> getByAccount($search);
        echo json_encode ($query);
        //$this->load->view('add_clients', $query);
    }
	
	/**
	 * Метод поиска лицевого счета при копировании
	 * со счета на счет.
	 * @author Денис Ермашевский <egrad77@mail.ru>
	 * @return array Retun Array
	 */
	public function searchByPhone(){
        $search=  $this->input->post('search');
		$this -> load -> model('clients_model');
        $query = $this -> clients_model -> getByPhone($search);
        echo json_encode ($query);
        //$this->load->view('add_clients', $query);
    }


	/**
	 * Метод создания нового клиента
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function add_client()
	{
		$this -> data['title'] = 'Новый клиент';
		$this -> form_validation -> set_rules('client_name', 'Наименование клиента', 'required|xss_clean');
		$this -> form_validation -> set_rules('client_address', 'Адрес клиента', 'required|xss_clean');
		$this -> form_validation -> set_rules('post_client_address', 'Почтовый адрес клиента', 'required|xss_clean');
		$this -> form_validation -> set_rules('account', 'Номер договора', 'required|xss_clean');
		$this -> form_validation -> set_rules('inn', 'ИНН', 'xss_clean|min_length[10]|max_length[12]|integer');
		$this -> form_validation -> set_rules('kpp', 'КПП', 'xss_clean');
		$this -> form_validation -> set_rules('client_manager', 'Контактное лицо', 'xss_clean');
		$this -> form_validation -> set_rules('phone_number', 'Контактный телефон', 'xss_clean|integer');
		$this -> form_validation -> set_rules('client_email', 'Email Address', 'valid_email');
		$this -> form_validation -> set_rules('assortment_selected', 'assortment_selected', 'required|xss_clean');
		//$this->form_validation->set_rules('date_account', 'Date Account', 'required|xss_clean');
		if ($this -> form_validation -> run() === TRUE) { //check to see if we are creating the user
			//redirect them back to the admin page
			$this -> session -> set_flashdata('message', 'User Created');
			$data['client_name'] = $_POST['client_name'];
			$data['client_address'] = $_POST['client_address'];
			$data['post_client_address'] = $_POST['post_client_address'];
			$data['account'] = $_POST['account'];
			$data['inn'] = $_POST['inn'];
			$data['kpp'] = $_POST['kpp'];
			$data['client_manager'] = $_POST['client_manager'];
			$data['phone_number'] = $_POST['phone_number'];
			$data['client_email'] = $_POST['client_email'];
			$data['assortment_selected'] = $_POST['assortment_selected'];
			$data['date_account'] = $_POST['date_account'];

			$user = $this->ion_auth->user()->row();
			$mdc = new LoggerMDC();
			$mdc->put('username',$user->username);
			$this -> log -> info('Пользователь добавил нового клиента: '.$data['client_name'].' с адресом '.$data['client_address'].' и ЛС '.$data['account']);

			$this -> load -> model('clients_model');
			$this -> clients_model -> add_client($data);
			redirect('clients', 'refresh');
		} else {
			$this -> data['message'] = (validation_errors() ? validation_errors() : $this -> session -> flashdata('message'));
			$this -> data['client_name'] = array('name' => 'client_name', 'value' => $this -> form_validation -> set_value('client_name'),);
			$this -> data['client_address'] = array('name' => 'client_address', 'value' => $this -> form_validation -> set_value('client_address'),);
			$this -> data['post_client_address'] = array('name' => 'post_client_address', 'value' => $this -> form_validation -> set_value('post_client_address'),);
			$this -> data['account'] = array('name' => 'account', 'value' => $this -> form_validation -> set_value('account'),);
			$this -> data['inn'] = array('name' => 'inn', 'value' => $this -> form_validation -> set_value('inn'),);
			$this -> data['kpp'] = array('name' => 'kpp', 'value' => $this -> form_validation -> set_value('kpp'),);
			$this -> data['client_manager'] = array('name' => 'client_manager', 'value' => $this -> form_validation -> set_value('client_manager'),);
			$this -> data['phone_number'] = array('name' => 'phone_number', 'value' => $this -> form_validation -> set_value('phone_number'),);
			$this -> data['client_email'] = array('name' => 'client_email', 'value' => $this -> form_validation -> set_value('client_email'),);
			$this -> data['assortment_selected'] = array('name' => 'assortment_selected', 'value' => $this -> form_validation -> set_value('assortment_selected'),);
			$this -> data['date_account'] = array('name' => 'date_account', 'value' => $this -> form_validation -> set_value('date_account'),);
			$this -> load -> view('header');
			$this -> load -> view('add_clients', $this -> data);
			$this -> load -> view('left_sidebar');
		}
	}

	/**
	 * Метод редактирования данных клиента
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function editClientInfo()
	{
		$data['id'] = @$_POST['id'];
		$data['client_name'] = @$_POST['client_name'];
		$data['client_address'] = @$_POST['client_address'];
		$data['post_client_address'] = @$_POST['post_client_address'];
		$data['inn'] = @$_POST['inn'];
		$data['kpp'] = @$_POST['kpp'];
		$data['account_date'] = date('Y-m-d',  strtotime(@$_POST['account_date']));
		$data['client_manager'] = @$_POST['client_manager'];
		$data['client_email'] = @$_POST['client_email'];
		$data['phone_number'] = @$_POST['phone_number'];

		$user = $this->ion_auth->user()->row();
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$this -> log -> warn('Пользователь отредактировал реквизиты клиента: '.$data['client_name'].' '.$data['client_address'].' '.$data['post_client_address'].' '.$data['inn'].' '.$data['kpp'].' '.$data['account_date'].' '.$data['client_email'].' '.$data['phone_number']);

		$this -> load -> model('clients_model');
		$this -> clients_model -> editClientInfo($data);
	}

	/**
	 * Метод получения группы услуг
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function get_group()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> getServiceGroup($id);
		echo json_encode($data);
	}

	/**
	 * Метод получения групп услуг добавленных на ЛС клиента
	 *
	 * @author Ермашевский Денис
	 * @return array $dataGroup;
	 */
	function getCustomerGroup()
	{
//service_groups.services_groups, изъято из запроса customer_service.id_group, - тоже
		$id = trim($this -> input -> post('id'));
		$old_text = '78452';
		$new_text = "(78452)";
		$this -> datatables -> select('customer_service.uniq_id,sum(customer_payments.amount) as `balance`, GROUP_CONCAT(identifier SEPARATOR " ") as identifier, GROUP_CONCAT(DISTINCT REPLACE(`free_phone_pool`.`resources`, "78452", "(78452)")) as resources', FALSE)
				-> from('customer_service', 'free_phone_pool')
				//-> join('service_groups', 'service_groups.id = customer_service.id_group', 'inner')
				-> join('free_phone_pool', 'free_phone_pool.id = customer_service.resources', 'left')
				-> join('clients_accounts', 'clients_accounts.id = customer_service.id_account')
				-> join('customer_payments', 'customer_payments.id_assortment_customer = customer_service.id', 'left')
				-> where('customer_service.id_account', $id)
				-> group_by('customer_service.uniq_id')
				-> add_column('delete', '<a href="' . base_url() . 'admin/profiles/delete/$1"><img src="/assets/images/delete-row.png" alt="Delete" title="Delete" /></a>', 'id');

		echo $this -> datatables -> generate();
	}

	/**
	 * Метод возвращает список номеклатур клиента
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function getCustomerAssortment()
	{
		$id = trim($this -> input -> post('uniq_id'));

		$this -> datatables -> select('customer_service.id, id_account, id_clients, tariffs, payment_name, REPLACE(free_phone_pool.resources, "78452","(78452)") as resources, identifier,name,tariff_name, price, datepicker1, end_date',FALSE)
				-> from('customer_service')
				-> join('tariffs', 'tariffs.id = customer_service.tariffs', 'left')
				-> join('clients_accounts', 'clients_accounts.id = customer_service.id_account', 'left')
				-> join('free_phone_pool', 'free_phone_pool.id = customer_service.resources', 'left')
				-> where('customer_service.uniq_id', $id);
				//-> orderby('identifier');

		echo $this -> datatables -> generate();
	}

	function updateEndDate()
	{
		$id = trim($this -> input -> post('id'));
		$end_date = trim($this -> input -> post('end_date'));
		$datepicker = trim($this -> input -> post('datepicker'));
		$user = $this->ion_auth->user()->row();
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$this -> log -> warn('Пользователь отредактировал даты начала '.$datepicker.' и окончания действия номеклатуры '.$end_date);
		$this -> load -> model('clients_model');
		$this -> clients_model ->updateEndDate($id,$end_date,$datepicker);
	}


	/**
	 * Метод построения динамической формы
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 *
	 */
	function get_forms($id)
	{
		//$id =  trim($this->input->post('id'));
		if ( ! $this -> ion_auth -> logged_in()) {
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		} else {
			$this -> load -> model('clients_model');
			$data['forms'] = $this -> clients_model -> getElementsForm($id);

			$this -> load -> view('header');
			$this -> load -> view('add_forms', $data);
			$this -> load -> view('left_sidebar');
		}
	}

	/**
	 * Метод доавления единицы номенклатуры на ЛС клиента
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function add_assortment_item()
	{
		$data['payment_name'] = @$_POST['payment_name'];
		$data['id_group'] = @$_POST['id_group'];
		$data['uniq_id'] = @$_POST['uniq_id'];
		$data['id_account'] = @$_POST['id_account'];
		$data['resources'] = @$_POST['resources'];
		$data['datepicker1'] = @$_POST['datepicker1'];
		$data['tariff'] = @$_POST['tariff'];
		$data['name'] = @$_POST['name'];
		$data['identifier'] = @$_POST['identifier'];
		$data['period'] = @$_POST['period'];

		if ($data['period'] === 'single_payment') {
			$data['end_date'] = $data['datepicker1'];
		}
		$user = $this->ion_auth->user()->row();
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$mdc->put('id_account','ID лицевого счета: '.$data['id_account']);
		$mdc->put('date','Дата начала действия: '.$data['datepicker1']);
		$mdc->put('tariff','ID тарифа: '.$data['tariff']);
		$this -> log -> info('Пользователь добавил номеклатуру на ЛС клиента: '.$data['payment_name']);

		$this -> load -> model('clients_model');
		$this -> clients_model -> add_assortment_item($data);
	}

	/**
	 * Метод добавления обязательной номенклатуры на ЛС из формы
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function add_service_data()
	{
		foreach ($_POST as $key => $value) {
			$rules[$key] = 'required';
		}
		$this -> form_validation -> set_rules($rules);
		if ($this -> form_validation -> run() === TRUE) { //check to see if we are creating the user
			//redirect them back to the admin page
			$this -> session -> set_flashdata('message', 'Записано');
			for ($i = 1; $i <= $_POST['counter']; $i ++ ) {
				//$data['id_group'] = @$_POST['id_group'];
				$data['id_account'] = @$_POST['id_account'];
				$data['payment_name'][$i] = @$_POST['payment_name'][$i];
				$data['resources'][$i] = @$_POST['resources'][$i];
				$data['name'][$i] = @$_POST['name'][$i];
				$data['tariff'][$i] = @$_POST['tariff'][$i];
				$data['period'][$i] = @$_POST['period'][$i];
				$data['datepicker1'] = $_POST['datepicker1'];
				$id = $data['resources'][$i];
				$data['counter'] = $i;

				if (isset($data['resources'][$i])) {
					$status = 'busy';
					$id;
					$this -> setResourceStatus($id, $status);
				}
			}


			$user = $this->ion_auth->user()->row();
			$mdc = new LoggerMDC();

			$mdc->put('username',$user->username);
//			$mdc->put('id_account','ID лицевого счета: '.$data['id_account']);
//			$mdc->put('date','Дата начала действия: '.$data['datepicker1']);
//			$mdc->put('payment_name','Номенклатура: '.$data['payment_name']);
//			$mdc->put('tariff','ID тарифа: '.$data['tariff']);

			$this -> log -> info('Пользователь добавил группу номенклатур на ЛС клиента:');
			$this -> log -> info($data);

			$this -> load -> model('clients_model');
			$this -> clients_model -> add_service_client($data);

			redirect($_POST['referer'], 'refresh');
		} else { //display the create user form
			//set the flash data error message if there is one
			$this -> data['message'] = (validation_errors() ? validation_errors() : $this -> session -> flashdata('message'));
			$this -> data['payment_name'] = array('name' => 'payment_name', 'value' => $this -> form_validation -> set_value('payment_name'),);
			$this -> data['resources'] = array('name' => 'resources', 'value' => $this -> form_validation -> set_value('resources'),);
			$this -> data['name'] = array('name' => 'name', 'value' => $this -> form_validation -> set_value('name'),);
			$this -> data['tariff'] = array('name' => 'tariff', 'value' => $this -> form_validation -> set_value('tariff'),);
			$this -> data['period'] = array('name' => 'period', 'value' => $this -> form_validation -> set_value('period'),);
			$this -> data['datepicker1'] = array('name' => 'datepicker1', 'value' => $this -> form_validation -> set_value('datepicker1'),);
		}
	}

	/**
	 * Метод устанавливает статус для ресурса (Ресурс: телефонный номер, ip-адрес, порт)
	 *
	 * @param int    $id     идентификатор
	 * @param string $status статус
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function setResourceStatus($id, $status)
	{

		$this -> load -> model('clients_model');
		$this -> clients_model -> setResourceStatus($id, $status);
	}

	/**
	 * Метод возвращает список номенклатуры по id услуги
	 *
	 * @author Ермашевский Денис
	 * @return json $data
	 */
	function getAssortmentByService()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> getAssortmnentsByService($id);
		echo json_encode($data);
	}

	/**
	 * Метод возвращает ресурсы (номера, порты,адреса)
	 *
	 * @author Ермашевский Денис
	 * @return array $data
	 *
	 */
	function getResources()
	{
		$table = trim($this -> input -> post('table'));
		$type_resources = trim($this -> input -> post('type_resources'));
		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> getResources($table, $type_resources);
		echo json_encode($data);
	}

	/**
	 * Метод возвращает информацию о клиенте по ID для редактирования
	 *
	 * @author Ермашевский Денис
	 * @return array $data
	 */
	function getClientById()
	{
		$id = (int) trim($this -> input -> post('id'));
		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> getClientsById($id);
		echo json_encode($data);
	}

//	/**
//	 * Метод генерации карты по адресу клиента
//	 *
//	 * @param string $address адрес
//	 *
//	 * @author Ермашевский Денис
//	 * @return array $data
//	 */
//	function getMap($address)
//	{
//		$this -> load -> library('GMap');
//		$this -> gmap -> GoogleMapAPI();
//		// valid types are hybrid, satellite, terrain, map
//		$this -> gmap -> setMapType('hybrid');
//		// you can also use addMarkerByCoords($long,$lat)
//		// both marker methods also support $html, $tooltip, $icon_file and $icon_shadow_filename
//		$this -> gmap -> addMarkerByAddress($address);
//		$data['headerjs'] = $this -> gmap -> getHeaderJS();
//		$data['headermap'] = $this -> gmap -> getMapJS();
//		$data['onload'] = $this -> gmap -> printOnLoad();
//		$data['map'] = $this -> gmap -> printMap();
//		$data['sidebar'] = $this -> gmap -> printSidebar();
//		return $data;
//	}

	/**
	 * Метод добавления ЛС клиентам
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function add_client_account_item()
	{
		$data['id_client'] = (int) trim($this -> input -> post('id_client'));
		$data['client_name'] = trim($this -> input -> post('client_name'));
		$data['account'] = trim($this -> input -> post('client_account'));
		$data['id_service'] = (int) trim($this -> input -> post('selected'));

		$user = $this->ion_auth->user()->row();
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$this -> log -> info('Пользователь добавил клиенту '.$data['client_name'].' ЛС: '.$data['account']);

		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> add_client_account_item($data);
		return;
	}
/**
	 * Метод добавления ЛС клиентам
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function add_client_account_item2()
	{
		$data['id_client'] = (int) trim($this -> input -> post('id_client'));
		$data['client_name'] = trim($this -> input -> post('client_name'));
		$data['account'] = trim($this -> input -> post('client_account'));
		$data['id_service'] = (int) trim($this -> input -> post('selected'));

		$user = $this->ion_auth->user()->row();
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$this -> log -> info('Пользователь добавил клиенту '.$data['client_name'].' произвольный ЛС: '.$data['account']);

		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> add_client_account_item2($data);
		return;
	}
	/**
	 * Метод возвращает список начислений по услугам клиента
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function getCustomerPayments()
	{
		$id = (int) trim($this -> input -> post('id'));
		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> getCustomerPayments($id);
		echo json_encode($data);
	}

	/**
	 * Метод возвращает список услуг клиента
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function getCustomerServices()
	{
		$id = (int) trim($this -> input -> post('id'));
		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> getCustomerServices($id);
		echo json_encode($data);
	}

	/**
	 * Метод возвращает по уникальному индентификатору группы (номенклатур)
	 * начисления по каждой номенклатуре входящей в эту группу
	 *
	 * @author Ермашевский Денис
	 * @return json $data
	 */
	function getAccrualsInGroup()
	{

		$uniq_id = trim($this -> input -> post('uniq_id'));

		$this -> load -> model('clients_model');
		$data = $this -> clients_model -> getAccrualsInGroup($uniq_id);
		echo json_encode($data);
	}

	function getUserOnline()
	{

		$session = $this -> db -> get('ci_sessions') -> result_array();
		foreach ($session as $sessions) {
			$sessio = $sessions['last_activity'];

			$custom_data = $this -> session -> _unserialize($sessions['user_data']);
		}
		return $custom_data;
	}

}

//End of file clients.php
//Location: ./controllers/clients.php