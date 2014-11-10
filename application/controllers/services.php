<?php
/**
 * Services
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Controllers.Services
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
 * Класс Services содержит методы взаимодействия клиента с услугами
 *
 * @category PHP
 * @package  Controllers.Services
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Services extends CI_Controller
{
	public $log;
	/**
	 * Унифицированный метод-конструктор __construct()
	 *
	 * @author Ермашевский Денис
	 */
	function __construct()
	{
		date_default_timezone_set('Europe/Kaliningrad');
		$this->log = Logger::getLogger(__CLASS__);
		parent::__construct();
		$this -> load -> library('ion_auth');
		$this -> load -> library('session');
		$this -> load -> library('form_validation');
		$this -> load -> library('getcsv');
		$this -> load -> database();
		$this -> load -> helper('url', 'form');
		$this -> breadcrumbs = array();
		$this -> breadcrumbs[] = anchor('', $this -> config -> item('breadcrumbs_index'));
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
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		} elseif ( ! $this -> ion_auth -> is_admin()) {
			//redirect them to the home page because they must be an administrator to view this
			redirect($this -> config -> item('base_url'), 'refresh');
		} else {
			$this -> load -> model('services_model');
			$data = array();
			$data['groups'] = $this -> services_model -> get_service_groups();
			$this -> load -> view('header');
			$this -> load -> view('auth/service_groups', $data);
			$this -> load -> view('left_sidebar');
			//$this->load->view('auth/footer');
		}
	}

	/**
	 * Метод возвращает список номенклатуры
	 * для представления Создание групп номенклатур
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function getAssortmentList()
	{
		$this -> load -> model('services_model');
		$data = array();
		$data['assortmentlist'] = $this -> services_model -> getAssortmentList();
		return $data['assortmentlist'];
	}

	/**
	 * Метод возвращает список номенклатуры
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function assortmentList()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('auth/login', 'refresh');
		} else {
			$this -> load -> model('services_model');
			$data = array();
			$data['assortmentlist'] = $this -> services_model -> getAssortmentList();
			$this -> load -> view('header');
			$this -> load -> view('assortmentList', $data);
			$this -> load -> view('left_sidebar');
		}
	}

	/**
	 * Метод возвращает услугу по идентификатору
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function getServiceByID()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> getServiceById($id);
		echo json_encode($data);
	}

	/**
	 * Метод редактирования наименования услуги по идентификатору
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function editServiceByID()
	{
		$data['id'] = @$_POST['id'];
		$data['service_description'] = @$_POST['service_description'];

		$user = $this->ion_auth->user()->row();
		$this -> log -> warn('Пользователь '.$user->username.' отредактировал описание услуги: '.$data['service_description']);

		$this -> load -> model('services_model');
		$data = $this -> services_model -> editServiceByID($data);
	}

	/**
	 * Метод возвращает данные о использовании услуги по идентификатору
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function getServiceUsed()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> getServiceUsed($id);
		echo json_encode($data);
	}

	/**
	 * Метод удаления услуги по идентификатору
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function deleteService()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> getServiceByID($id);
		foreach ($data as $row):
			$row->service_description;
			$row->marker;
		endforeach;
		$user = $this->ion_auth->user()->row();
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$mdc->put('service_description',$row->service_description);
		$mdc->put('marker',$row->marker);
		$this -> log -> fatal('Пользователь '.$user->username.' удалил услугу: ');
		$this -> services_model -> deleteService($id);
	}

	/**
	 * Метод возвращает группу по идентификатору
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function getServiceGroupByID()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> getServiceGroupByID($id);
		echo json_encode($data);
	}
	/**
	 * Апдейт идентификатора на ТК
	 *
	 * @author Denis ermashevsky <ermashevsky@gmail.com>
	 */
	function updateIdentificator()
	{
		$id = trim($this -> input -> post('id'));
		$identifier = trim($this -> input -> post('identifier'));
		$id_account = trim($this -> input -> post('id_account'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> updateIdentifier($id, $identifier);

		if($data):

			$user = $this->ion_auth->user()->row();
			$mdc = new LoggerMDC();
			$mdc->put('username',$user->username);
			$this -> log -> warn('Пользователь изменил идентификатор на лицевом счете. ID записи: '.$id.' значение нового идентификатора:'.$identifier.' ID лицевого счета:'.$id_account);

		endif;
	}

	function whoBusyPhone()
	{
		$resource = trim($this -> input -> post('resource'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> whoBusyPhone($resource);
		echo json_encode($data);
	}

	/**
	 * Метод редактирования группы по идентфикатору
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function editServiceGroupByID()
	{
		$data['id'] = @$_POST['id'];
		$data['services_groups'] = @$_POST['services_groups'];
		print_r($data);
		$this -> load -> model('services_model');
		$data = $this -> services_model -> editServiceGroupByID($data);
	}

	/**
	 * Метод возвращает номеклатуру из группы по идентфикатору
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function getAssortmentItemGroup()
	{

		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> getAssortmentItemGroup($id);
		echo json_encode($data);
	}

	/**
	 * Метод удаления номеклатуры из группы по идентификатору
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function deleteAssortmentItemGroup()
	{

		$id = trim($this -> input -> post('id'));
		$id_group = trim($this -> input -> post('id_group'));
		$this -> load -> model('services_model');
		$this -> services_model -> deleteAssortmentItemGroup($id, $id_group);
	}

	/**
	 * Метод добавления номеклатуры в группу по идентификатору
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function addAssortmentItemGroup()
	{
		$id = trim($this -> input -> post('id'));
		$id_group = trim($this -> input -> post('id_group'));
		$this -> load -> model('services_model');
		$this -> services_model -> addAssortmentItemGroup($id, $id_group);
	}

	/**
	 * Метод возвращает список типов ЛС
	 *
	 * @return array servicesType
	 * @author Ермашевский Денис
	 *
	 */
	function getServiceTypeList()
	{
		$this -> load -> model('services_model');
		$data = array();
		$data['servicesType'] = $this -> services_model -> getServiceType();
		return $data['servicesType'];
	}

	/**
	 * Метод создает группы номенклатур
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function createServiceGroup()
	{
		$this -> form_validation -> set_rules('group_name', 'group_name', 'required|xss_clean');
		$this -> form_validation -> set_rules('assortment_selected', 'assortment_selected', 'required|xss_clean');
		if ($this -> form_validation -> run() === TRUE) { //check to see if we are creating the user
			//redirect them back to the admin page
			$this -> session -> set_flashdata('message', 'Group Created');
			$data['group_name'] = $_POST['group_name'];
			$data['serviceType'] = $_POST['serviceType'];
			$data['assortment_selected'] = $_POST['assortment_selected'];
			//Рабочий вариант - допилить
			$this -> load -> model('services_model');
			$this -> services_model -> add_group_service($data);
			redirect('services', 'refresh');
		} else { //display the create user form
			//set the flash data error message if there is one
			$this -> data['message'] = (validation_errors() ? validation_errors() : $this -> session -> flashdata('message'));
			$this -> data['group_name'] = array('name' => 'group_name', 'value' => $this -> form_validation -> set_value('group_name'),);
			$this -> data['assortment_selected'] = array('name' => 'assortment_selected', 'value' => $this -> form_validation -> set_value('assortment_selected'),);
			$this -> load -> view('header');
			$this -> load -> view('auth/addservicegroup', $this -> data);
			$this -> load -> view('left_sidebar');
		}
	}

	/**
	 * Возвращает список групп номенклатур
	 *
	 * @param int $id - входной параметр
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 *
	 */
	function get_Assortment_Group($id)
	{
		$data = array();
		$id = (int) $id;
		$this -> load -> model('services_model');
		$data['assortment_group'] = $this -> services_model -> getAssortmentGroup($id);
		$this -> load -> view('header');
		$this -> load -> view('auth/assortment_groups', $data);
		$this -> load -> view('left_sidebar');
	}

	/**
	 * Метод возвращает номеклатуру по идентфикатору
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function getAssortmentById()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> getAssortmnentsById($id);
		echo json_encode($data);
	}

	/**
	 * Метод редактирования номеклатуры по идентификатору
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function editAssortment()
	{
		$data['id'] = @$_POST['id'];
		$data['payment_name'] = @$_POST['payment_name'];
		$user = $this->ion_auth->user()->row();
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$this -> log -> warn('Пользователь отредактировал номеклатуру: '.$data['payment_name']);
		$this -> load -> model('services_model');
		$data = $this -> services_model -> editAssortment($data);
	}

	/**
	 * Метод показывает наличие номеклатуры в группе
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function getAssortmentGroupInfo()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$this -> services_model -> getAssortmentGroupInfo($id);
	}

	/**
	 * Метод возвращает 1 или 0 в зависимости от наличия начислений
	 * по номеклатуре в группе
	 *
	 * @return json $data;
	 */
	function getAssortmentInfoPayments()
	{

		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> getAssortmentInfoPayments($id);
		echo json_encode($data);
	}

	/**
	 * Метод возвращает количество строк в зависимости от наличия начислений по номеклатурам в группе
	 *
	 * @author Ермашевский Денис
	 * @return json $data;
	 */
	function getAssortmentsPaymentsInGroup()
	{
		$uniq_id = trim($this -> input -> post('uniq_id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> getAssortmentsPaymentsInGroup($uniq_id);

		echo json_encode($data);
	}

	/**
	 * Метод удаления номенклатуры у клиента если нет начислений
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function deleteAssortmentItem($id=null)
	{

		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$user = $this->ion_auth->user()->row();
		$data = $this->services_model->getAssortmentItemByID($id);
		foreach ($data as $row):
			$row->id;
			$row->id_account;
			$row->payment_name;
			$row->datepicker1;
			$row->end_date;
		endforeach;

		$mdc = new LoggerMDC();
		$mdc->put('username', $user->username);
		$mdc->put('id', 'ID записи '.$id);
		$mdc->put('id_account', 'ID лицевого счета: '.$row->id_account);
		$mdc->put('payment_name', 'Наименование номенклатуры: '.$row->payment_name);
		$mdc->put('datepicker1', 'Дата начала: '.$row->datepicker1);
		$mdc->put('end_date', 'Дата окончания: '.$row->end_date);

		$this -> log -> fatal('Пользователь удалил номенклатуру с ЛС клиента');
		$this -> services_model -> deleteAssortmentItem($id);
	}

	/**
	 * Метод удаления номеклатуры из общего списка номеклатур
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function deleteAssortment()
	{

		$id = trim($this -> input -> post('id'));
		$user = $this->ion_auth->user()->row();
		$this -> load -> model('services_model');
		$data = $this->services_model->getAssortmentByID($id);
		foreach($data as $rows):

		endforeach;
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$mdc->put('id','ID '.$id);
		$mdc->put('payment_name',' Наименование номенклатуры: '.$rows->payment_name);
		$mdc->put('marker_service',' Тип услуги: '.$rows->marker_service);
		$mdc->put('payment_type',' Периодичность начислений: '.$rows->payment_type);
		$mdc->put('tariff',' Номенклатура тарифицируемая (1/0): '.$rows->tariff);
		$this -> log -> fatal('Пользователь удалил номенклатуру из общего списка номенклатур');

		$this -> services_model -> deleteAssortment($id);
	}

	/**
	 * Метод удаления группы номеклатур по уникальному идентификатору группы
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function deleteGroupAssortments()
	{
		$uniq_id = $this -> input -> post('uniq_id');
		$user = $this->ion_auth->user()->row();
		$this -> load -> model('services_model');
		$data = $this->services_model->getAssortmentGroupByUniqID($uniq_id);
		$new_arr = array();
		foreach ($data as $row):
				$tmp['id'] = $row -> id;
				$tmp['id_account'] = $row -> id_account;
				$tmp['payment_name'] = $row -> payment_name;
				$tmp['identifier'] = $row -> identifier;
				$tmp['datepicker1'] = $row -> datepicker1;
				$tmp['end_date'] = $row -> end_date;
				$new_arr[$tmp['id']]=$tmp;
		endforeach;
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$this -> log -> fatal('Пользователь удалил группу номенклатур с ЛС клиента');
		$this -> log -> fatal($new_arr);
		$this -> services_model -> deleteGroupAssortments($uniq_id);
	}

	/**
	 * Метод удаления ЛС клиента
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function deleteClientAccounts()
	{
		$id = trim($this -> input -> post('id'));
		$id_client = trim($this -> input -> post('id_client'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> deleteClientAccounts($id, $id_client);
		echo json_encode($data);
	}

	/**
	 * Метод удаления ЛС клиента
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function deleteAccount()
	{
		$id = trim($this -> input -> post('id'));
		$id_client = trim($this -> input -> post('id_client'));
		$this -> load -> model('services_model');
		$data = $this->services_model->getAccountInfo($id);
		foreach($data as $rows):
			$mydata['id']=$rows->id;
			$mydata['bindings_name']=$rows->bindings_name;
			$mydata['accounts']=$rows->accounts;
		endforeach;
		$user = $this->ion_auth->user()->row();
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$this -> log -> fatal('Пользователь удалил клиенту ЛС');
		$this -> log -> fatal($mydata);
		$this -> services_model -> deleteAccount($id, $id_client);
	}

	/**
	 * Получение списка тарифов
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function getAllTariffs()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('auth/login', 'refresh');
		} else {
			$this -> load -> model('services_model');

			$data['allTariffs'] = $this -> services_model -> getTariffs();
			$data['serviceType'] = $this -> services_model -> getServiceType();
			$this -> load -> view('header');
			$this -> load -> view('allTariffs', $data);
			$this -> load -> view('left_sidebar');
		}
	}

	/**
	 * Метод возвращает список тарифов у номеклатуры (фильтрация)
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function getFilterTariffs()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('auth/login', 'refresh');
		} else {
			$this -> load -> model('services_model');
			$id = trim($this -> input -> post('id_assortment', TRUE));
			$data = $this -> services_model -> getFilterTariffs($id);

			echo json_encode($data);
		}
	}

	/**
	 * Метод возвращает тариф по индентификатору
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function getTariffId()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> getTariffById($id);
		echo json_encode($data);
	}

	/**
	 * Метод возвращает тариф по индентификатору
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function getTariffById()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> getTariffId($id);
		echo json_encode($data);
	}

	/**
	 * Метод редактирования тарифа
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function editTariff()
	{
		$data['id'] = @$_POST['id'];
		$data['tariff_name'] = @$_POST['tariff_name'];
		$data['price'] = @$_POST['price'];

		$user = $this->ion_auth->user()->row();
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$this -> log -> warn('Пользователь отредактировал тариф: '.$data['tariff_name']);
		$this -> log -> warn($data);

		$this -> load -> model('services_model');
		$data = $this -> services_model -> editTariff($data);
	}

	/**
	 * Метод добавления новых тарифов в систему
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function add_tariff()
	{
		$this -> form_validation -> set_rules('tariff_name', 'tariff_name', 'required|xss_clean');
		$this -> form_validation -> set_rules('price', 'price', 'required|xss_clean');
		$this -> form_validation -> set_rules('id_assortment', 'Assortment name', 'required|xss_clean');
		if ($this -> form_validation -> run() === TRUE) { //check to see if we are creating the user
			//redirect them back to the admin page
			$this -> session -> set_flashdata('message', 'Group Created');
			$data['tariff_name'] = $_POST['tariff_name'];
			$data['price'] = $_POST['price'];
			$data['id_assortment'] = $_POST['id_assortment'];

			$user = $this->ion_auth->user()->row();
			$mdc = new LoggerMDC();
			$mdc->put('username',$user->username);
			$this -> log -> info('Пользователь создал тариф: '.$data['tariff_name']);
			$this -> log -> info($data);

			$this -> load -> model('services_model');
			$this -> services_model -> add_tariff($data);
			redirect('services', 'refresh');
		} else {
			$this -> data['message'] = (validation_errors() ? validation_errors() : $this -> session -> flashdata('message'));
			$this -> data['tariff_name'] = array('name' => 'tariff_name', 'value' => $this -> form_validation -> set_value('tariff_name'),);
			$this -> data['price'] = array('name' => 'price', 'value' => $this -> form_validation -> set_value('price'),);
			$this -> data['id_assortment'] = array('name' => 'id_assortment', 'value' => $this -> form_validation -> set_value('id_assortment'),);
		}
	}

	function delTariffById()
	{

		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> delTariffById($id);
		echo json_encode($data);
	}

	function deleteTariff()
	{
		$id = trim($this -> input -> post('id'));
		$user = $this->ion_auth->user()->row();
		$this -> load -> model('services_model');
		$data = $this->services_model->getTariffId($id);
		foreach($data as $rows):
			$arr['id'] = $rows->id;
			$arr['tariff_name'] = $rows->tariff_name;
			$arr['price'] = $rows->price;
		endforeach;
		$mdc = new LoggerMDC();
		$mdc->put('username',$user->username);
		$this -> log -> fatal('Пользователь удалил тариф: '.$rows->tariff_name);
		$this -> log -> fatal($arr);
		$this -> services_model -> deleteTariff($id);
	}

	/**
	 * Метод возвращает список услуг
	 *
	 * @author Ермашевский Денис
	 * @return array $data['allServices']
	 */
	function getAllServices()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		} else {
			$this -> load -> model('services_model');
			$data['allServices'] = $this -> services_model -> getServiceType();
			$this -> load -> view('header');
			$this -> load -> view('allServices', $data);
			$this -> load -> view('left_sidebar');
		}
	}

	/**
	 * Метод создания нового типа услуги
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function add_service()
	{
		$this -> form_validation -> set_rules('service_description', 'service_description', 'required|xss_clean');
		$this -> form_validation -> set_rules('marker', 'marker', 'required|xss_clean');
		if ($this -> form_validation -> run() === TRUE) { //check to see if we are creating the user
			//redirect them back to the admin page
			$this -> session -> set_flashdata('message', 'Group Created');
			$data['service_description'] = $_POST['service_description'];
			$data['marker'] = $_POST['marker'];

			$user = $this->ion_auth->user()->row();
			$mdc = new LoggerMDC();
			$mdc->put('username',$user->username);
			$this -> log -> info('Пользователь создал услугу: '.$data['service_description']);
			$this -> log -> info($data);

			$this -> load -> model('services_model');
			$this -> services_model -> add_service($data);
			redirect('services/getAllServices', 'refresh');
		} else {
			$this -> data['message'] = (validation_errors() ? validation_errors() : $this -> session -> flashdata('message'));
			$this -> data['service_description'] = array('name' => 'service_description', 'value' => $this -> form_validation -> set_value('service_description'),);
			$this -> data['marker'] = array('name' => 'marker', 'value' => $this -> form_validation -> set_value('marker'),);
		}
	}

	/**
	 * Метод возвращает номеклатуры по идентификатору услуги
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function getAssortmentByServiceId()
	{
		$id = trim($this -> input -> post('id'));

		$this -> load -> model('services_model');
		$data = $this -> services_model -> getAssortmentByServiceId($id);
		echo json_encode($data);
	}

	/**
	 * Метод добавляет ЛС клиенту по идентификатору
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function add_accounts()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> add_client_accounts($id);
		$data['count'] = count($data);
		echo json_encode($data);
	}

	/**
	 * Метод добавляет ЛС клиенту по идентификатору
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function add_accounts2()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> add_client_accounts2($id);
		$data['count'] = count($data);
		echo json_encode($data);
	}

	/**
	 * Метод проверяет начисления у клиента
	 *
	 * @param mixed $data массив
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data;
	 */
	function addCustomerPayments($data)
	{
		$this -> load -> model('services_model');
		$data = $this -> services_model -> addCustomerPayments($data);
	}

	/**
	 * Метод начислений сумм за период предоставления услуг
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function getCustomerServiceFromPeriod()
	{
		$unixtime = now();

		$lastDayCurrentMonth = 30;
		$currentDayOfMonth = 15;

	//$lastDayCurrentMonth = date('t', $unixtime);
	//$currentDayOfMonth = date('j', $unixtime);

		$this -> load -> model('services_model');
		$this -> services_model -> getAssortmentWithResources();

		if ($lastDayCurrentMonth === '31') {
			$lastDayCurrentMonth --;
		}
		if ($lastDayCurrentMonth === '29') {
			$lastDayCurrentMonth ++;
		}
		$halfOfMonth = $lastDayCurrentMonth / 2;

		$date = new DateTime(); //создали новый элемент
		$half = new DateTime('2012-04-15');
		$date -> format('d-m-Y'); //получили текущую дату

		if ($currentDayOfMonth === '1') {
			$date -> modify('-1 month'); //отняли от тек даты один месяц - получили дату месяц назад
		}
		$start_date_month = $date -> format('Y-m-01'); //привели к виду начала предыдущего месяца
		$end_date_month = $date -> format('Y-m-t');  // привели к виду конца предыдущего месяца


		$this -> load -> model('services_model');
		$data = $this -> services_model -> getCustomerServicesFromPeriod($start_date_month, $end_date_month);

		/**
		 * Тута должен быть еще foreach и наверное пару условий
		 * 1. Если наступило начало месяца, т.е. первое число делать начисления по month
		 * 2. Если 30 или 31 день в месяце начисления производить 15 числа для half_month
		 * 3. Если 28 или 29 дней в месяце начисления производить 14 числа для half_month
		 * 4. Все начисления производить по формуле: (Цена тарифа/количество дней в месяце)xколичество фактических дней
		 */
		$payments = array();
		foreach ($data as $res) {

			$payments['id_assortment_customer'] = $res -> id_assortment_customer;
			$payments['id_group'] = $res -> id_group;
			$payments['id_account'] = $res -> id_account;
			$payments['payment_name'] = $res -> payment_name;
			$payments['datepicker1'] = $res -> datepicker1;
			$payments['end_date'] = $res -> end_date;
			$payments['period'] = $res -> period;
			$payments['start_date_month'] = $start_date_month;
			$payments['end_date_month'] = $end_date_month;

			if ($payments['datepicker1'] > $payments['start_date_month'] & $payments['end_date'] !== $payments['datepicker1']) {

				$date1 = new DateTime($payments['datepicker1']);
				$date2 = new DateTime($end_date_month);
				$interval = $date2 -> diff($date1);
				$inter_val = $interval -> format('%a');

				if ($currentDayOfMonth === '1' & $payments['period'] === 'month') {
					$payments['price'] = (($res -> price) / $lastDayCurrentMonth) * $inter_val;
					$payments['start_date_month'] = $payments['datepicker1'];
					$this -> addCustomerPayments($payments);
				}
			} else {

				if ($currentDayOfMonth === '1' & $payments['period'] === 'month') {

					$payments['price'] = $res -> price;
					$this -> addCustomerPayments($payments);
				}
			}


			if ($currentDayOfMonth === $halfOfMonth & $payments['period'] === 'half_month') {
				$date_1_half = new DateTime($payments['datepicker1']);
				if ($half > $date_1_half & $payments['datepicker1'] > $payments['start_date_month'] & $payments['end_date'] !== $payments['datepicker1']) {//!!! Тута
					$date1 = new DateTime($payments['datepicker1']);

					$interval = $half -> diff($date1);
					$inter_val = $interval -> format('%a');

					$payments['price'] = (($res -> price) / $lastDayCurrentMonth) * $inter_val;
					$payments['start_date_month'] = $payments['datepicker1'];
					$payments['end_date_month'] = $date -> format('Y.m.15');
					$this -> addCustomerPayments($payments);
				}
			} else {

				if ($currentDayOfMonth === $halfOfMonth & $payments['period'] === 'half_month') {
					$payments['price'] = (($res -> price) / $lastDayCurrentMonth) * $currentDayOfMonth;
					$payments['end_date_month'] = $date -> format('Y.m.15');
					$this -> addCustomerPayments($payments);
				}
			}
			$date_2_half = new DateTime($payments['datepicker1']);
			if ($half < $date_2_half & $payments['end_date'] !== $payments['datepicker1']) {

				$lastDay = new DateTime('2012-04-30');
				$lastDay -> format('Y-m-d');
				$interval = $lastDay -> diff($date_2_half);
				echo $inter_val = $interval -> format('%a');

				if ($currentDayOfMonth === $lastDayCurrentMonth & $payments['period'] === 'half_month') {
					$payments['price'] = (($res -> price) / $lastDayCurrentMonth) * $inter_val;
					$payments['start_date_month'] = $payments['datepicker1'];
					$this -> addCustomerPayments($payments);
				}
			} else {
				if ($currentDayOfMonth === $lastDayCurrentMonth & $payments['period'] === 'half_month') {
					$payments['price'] = (($res -> price) / $lastDayCurrentMonth) * ($currentDayOfMonth / 2);
					$payments['start_date_month'] = $date -> format('Y.m.16');
					$this -> addCustomerPayments($payments);
				}
			}
			if (date('Y-m-d') === $payments['datepicker1'] & date('Y-m-d') === $payments['end_date'] & $payments['period'] === 'single_payment') {
				$payments['price'] = $res -> price;
				$this -> addCustomerPayments($payments);
			}
		}
		print_r($data);
	}

	/**
	 * Метод служебный для быстрого заполнения ресурсами таблиц БД
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function setPhoneNumberPool()
	{
		$this -> load -> model('services_model');
		$this -> services_model -> setPhoneNumberPool();
	}

	/**
	 * Метод удаления начисления по идентификатору
	 *
	 * @author Ермашевский Денис
	 * @return true;
	 */
	function delCustomerPayments()
	{
		$id = trim($this -> input -> post('id'));

		$this -> load -> model('services_model');
		$this -> services_model -> delCustomerPayments($id);
		return TRUE;
	}

	/**
	 * Метод получения данных по услугам клиента
	 *
	 * @author Ермашевский Денис
	 * @return true;
	 */
	function getCustomerServiceInfo()
	{

		$id_group = trim($this -> input -> post('id_group'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> getCustomerServiceInfo($id_group);
		echo json_encode($data);
	}

	/**
	 * Метод чтения CSV файла (устарело, не используется)
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function importData()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('auth/login', 'refresh');
		} else {
			$filePath = 'application/csv/mydata.csv';
			$data['csvData'] = $this -> getcsv -> set_file_path($filePath) -> get_array();

			$this -> load -> view('header');
			$this -> load -> view('importData', $data);
			$this -> load -> view('left_sidebar');
		}
	}

	function testMyDate()
	{
		$datestring = "%Y-%m-%d";
		$time = time();

		echo mdate($datestring, $time);
	}

	function setMaintenanceMode()
	{
		$this->config->set_item('maintenance_mode', 'TRUE');
		echo $this->config->item('maintenance_mode');
	}

	function unsetMaintenanceMode()
	{
		$this->config->set_item('maintenance_mode', 'FALSE');
		echo $this->config->item('maintenance_mode');
	}

	function viewMaintenanceMode()
	{
		echo $this->config->item('maintenance_mode');
	}



}

//End of file services.php
//Location: ./controllers/services.php