<?php

/**
 * Services
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Controllers.Auth
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
 * Класс Auth содержит методы связанные с авторизацией, добавлением, удалением пользователей системы
 *
 * @category PHP
 * @package  Controllers.Auth
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Auth extends CI_Controller
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
		$this -> load -> helper('url');
		$this -> breadcrumbs = array();
		$this -> breadcrumbs[] = anchor('', $this -> config -> item('breadcrumbs_index'));
		//$this->output->enable_profiler(TRUE);
	}

	/**
	 * Метод возвращает список пользователей системы
	 *
	 * @author Ермашевский Денис
	 * @return array $data
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
			//set the flash data error message if there is one
			$this -> data['message'] = (validation_errors()) ? validation_errors() : $this -> session -> flashdata('message');
			//list the users
			$this -> data['users'] = $this -> ion_auth -> users() -> result();
			foreach ($this -> data['users'] as $k => $user) {
				$this -> data['users'][$k] -> groups = $this -> ion_auth -> get_users_groups($user -> id) -> result();
			}
			$this -> load -> view('auth/header');
			$this -> load -> view('auth/index', $this -> data);
			$this -> load -> view('auth/left_sidebar');
//            $this->load->view('auth/footer');
		}
	}

	/**
	 * Метод авторизации пользователей
	 *
	 * @author Ермашевский Денис
	 * @return array $data
	 */
	function login()
	{

		$this -> load -> view('header_login'); //Заголовок страницы
		$this -> data['title'] = 'Login';
		//validate form input
		$this -> form_validation -> set_rules('identity', 'Identity', 'required');
		$this -> form_validation -> set_rules('password', 'Password', 'required');
		if ($this -> form_validation -> run() === TRUE) { //check to see if the user is logging in
			//check for "remember me"
			$remember = (bool) $this -> input -> post('remember');
			if ($this -> ion_auth -> login($this -> input -> post('identity'), $this -> input -> post('password'), $remember)) { //if the login is successful
				//redirect them back to the home page
				if($this->config->item('maintenance_mode') == TRUE && !$this -> ion_auth -> is_admin()){
					$this -> load -> view('auth/maintenance_mode');
				}else{
				$this -> session -> set_flashdata('message', $this -> ion_auth -> messages());
				redirect($this -> config -> item('base_url'), 'refresh');
				}
			} else { //if the login was un-successful
				//redirect them back to the login page
				$this -> session -> set_flashdata('message', $this -> ion_auth -> errors());
				redirect('auth/login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		} else { //the user is not logging in so display the login page
			//set the flash data error message if there is one
			$this -> data['message'] = (validation_errors()) ? validation_errors() : $this -> session -> flashdata('message');
			$this -> data['identity'] = array('name' => 'identity', 'id' => 'identity', 'type' => 'text', 'value' => $this -> form_validation -> set_value('identity'),);
			$this -> data['password'] = array('name' => 'password', 'id' => 'password', 'type' => 'password',);
			$this -> load -> view('auth/login', $this -> data);
			//$this->load->view('auth/footer');
		}
	}


	/**
	 * Метод разлогинивания пользователей
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function logout()
	{
		$this -> data['title'] = 'Logout';
		//log the user out
		$this -> ion_auth -> logout();
		//redirect them back to the page they came from
		redirect('auth', 'refresh');
	}

	/**
	 * Метод смены пароля пользователей
	 *
	 * @author Ермашевский Денис
	 * @return array $data
	 */
	function change_password()
	{
		$this -> form_validation -> set_rules('old', 'Old password', 'required');
		$this -> form_validation -> set_rules('new', 'New Password', 'required|min_length[' . $this -> config -> item('min_password_length', 'ion_auth') . ']|max_length[' . $this -> config -> item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this -> form_validation -> set_rules('new_confirm', 'Confirm New Password', 'required');
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$user = $this -> ion_auth -> user() -> row();
		if ($this -> form_validation -> run() === FALSE) { //display the form
			//set the flash data error message if there is one
			$this -> data['message'] = (validation_errors()) ? validation_errors() : $this -> session -> flashdata('message');
			$this -> data['old_password'] = array('name' => 'old', 'id' => 'old', 'type' => 'password',);
			$this -> data['new_password'] = array('name' => 'new', 'id' => 'new', 'type' => 'password',);
			$this -> data['new_password_confirm'] = array('name' => 'new_confirm', 'id' => 'new_confirm', 'type' => 'password',);
			$this -> data['user_id'] = array('name' => 'user_id', 'id' => 'user_id', 'type' => 'hidden', 'value' => $user -> id,);
			//render
			$this -> load -> view('auth/change_password', $this -> data);
		} else {
			$identity = $this -> session -> userdata($this -> config -> item('identity', 'ion_auth'));
			$change = $this -> ion_auth -> change_password($identity, $this -> input -> post('old'), $this -> input -> post('new'));
			if ($change) { //if the password was successfully changed
				$this -> session -> set_flashdata('message', $this -> ion_auth -> messages());
				$this -> logout();
			} else {
				$this -> session -> set_flashdata('message', $this -> ion_auth -> errors());
				redirect('auth/change_password', 'refresh');
			}
		}
	}

	/**
	 * Метод напоминания пароля пользователей
	 *
	 * @author Ермашевский Денис
	 * @return array $data
	 */
	function forgot_password()
	{
		$this -> form_validation -> set_rules('email', 'Email Address', 'required');
		if ($this -> form_validation -> run() === FALSE) {
			//setup the input
			$this -> data['email'] = array('name' => 'email', 'id' => 'email',);
			//set any errors and display the form
			$this -> data['message'] = (validation_errors()) ? validation_errors() : $this -> session -> flashdata('message');
			$this -> load -> view('header'); //Заголовок страницы
			$this -> load -> view('auth/forgot_password', $this -> data);
		} else {
			//run the forgotten password method to email an activation code to the user
			$forgotten = $this -> ion_auth -> forgotten_password($this -> input -> post('email'));
			if ($forgotten) { //if there were no errors
				$this -> session -> set_flashdata('message', $this -> ion_auth -> messages());
				redirect('auth/login', 'refresh'); //we should display a confirmation page here instead of the login page
			} else {
				$this -> session -> set_flashdata('message', $this -> ion_auth -> errors());
				redirect('auth/forgot_password', 'refresh');
			}
		}
	}

	/**
	 * Метод сброса пароля при напоминании
	 *
	 * @param int $code - код
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	public function reset_password($code)
	{
		$reset = $this -> ion_auth -> forgotten_password_complete($code);
		if ($reset) { //if the reset worked then send them to the login page
			$this -> session -> set_flashdata('message', $this -> ion_auth -> messages());
			redirect('auth/login', 'refresh');
		} else { //if the reset didnt work then send them back to the forgot password page
			$this -> session -> set_flashdata('message', $this -> ion_auth -> errors());
			redirect('auth/forgot_password', 'refresh');
		}
	}

	/**
	 * Метод активации пользователей
	 *
	 * @param int  $id   идентификатор
	 * @param bool $code код
	 *
	 * @author Ермашевский Денис
	 * @return array $data
	 */
	function activate($id, $code = FALSE)
	{
		if ($code !== FALSE)
			$activation = $this -> ion_auth -> activate($id, $code);
		else if ($this -> ion_auth -> is_admin())
			$activation = $this -> ion_auth -> activate($id);
		if ($activation) {
			//redirect them to the auth page
			$this -> session -> set_flashdata('message', $this -> ion_auth -> messages());
			redirect('auth', 'refresh');
		} else {
			//redirect them to the forgot password page
			$this -> session -> set_flashdata('message', $this -> ion_auth -> errors());
			redirect('auth/forgot_password', 'refresh');
		}
	}

	/**
	 * Метод деактивации пользователей
	 *
	 * @param int $id - идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return array $data
	 */
	function deactivate($id = NULL)
	{
		// no funny business, force to integer
		$id = (int) $id;
		$this -> load -> library('form_validation');
		$this -> form_validation -> set_rules('confirm', 'confirmation', 'required');
		$this -> form_validation -> set_rules('id', 'user ID', 'required|is_natural');
		if ($this -> form_validation -> run() === FALSE) {
			// insert csrf check
			$this -> data['csrf'] = $this -> _get_csrf_nonce();
			$this -> data['user'] = $this -> ion_auth -> user($id) -> row();
			$this -> load -> view('header'); //Заголовок страницы
			$this -> load -> view('auth/deactivate_user', $this -> data);
			$this -> load -> view('auth/left_sidebar');
//            $this->load->view('auth/footer'); //Заголовок страницы
		} else {
			// do we really want to deactivate?
			if ($this -> input -> post('confirm') === 'yes') {
				// do we have a valid request?
				if ($this -> _valid_csrf_nonce() === FALSE OR $id !== $this -> input -> post('id')) {
					show_404();
				}
				// do we have the right userlevel?
				if ($this -> ion_auth -> logged_in() AND $this -> ion_auth -> is_admin()) {
					$this -> ion_auth -> deactivate($id);
				}
			}
			//redirect them back to the auth page
			redirect('auth', 'refresh');
		}
	}

	/**
	 * Метод создания пользователей
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function create_user()
	{
		$this -> data['title'] = 'Добавление нового пользователя';
		if ( ! $this -> ion_auth -> logged_in() OR ! $this -> ion_auth -> is_admin()) {
			redirect('auth', 'refresh');
		}
		//validate form input
		$this -> form_validation -> set_rules('first_name', 'First Name', 'required|xss_clean');
		$this -> form_validation -> set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this -> form_validation -> set_rules('email', 'Email Address', 'required|valid_email');
		$this -> form_validation -> set_rules('phone1', 'First Part of Phone', 'required|xss_clean|min_length[3]|max_length[3]');
		$this -> form_validation -> set_rules('phone2', 'Second Part of Phone', 'required|xss_clean|min_length[3]|max_length[3]');
		$this -> form_validation -> set_rules('phone3', 'Third Part of Phone', 'required|xss_clean|min_length[4]|max_length[4]');
		$this -> form_validation -> set_rules('company', 'Company Name', 'required|xss_clean');
		$this -> form_validation -> set_rules('password', 'Password', 'required|min_length[' . $this -> config -> item('min_password_length', 'ion_auth') . ']|max_length[' . $this -> config -> item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this -> form_validation -> set_rules('password_confirm', 'Password Confirmation', 'required');
		if ($this -> form_validation -> run() === TRUE) {
			$username = strtolower($this -> input -> post('first_name')) . ' ' . strtolower($this -> input -> post('last_name'));
			$email = $this -> input -> post('email');
			$password = $this -> input -> post('password');
			$additional_data = array('first_name' => $this -> input -> post('first_name'), 'last_name' => $this -> input -> post('last_name'), 'company' => $this -> input -> post('company'), 'phone' => $this -> input -> post('phone1') . '-' . $this -> input -> post('phone2') . '-' . $this -> input -> post('phone3'),);
		}
		if ($this -> form_validation -> run() === TRUE AND $this -> ion_auth -> register($username, $password, $email, $additional_data)) { //check to see if we are creating the user
			//redirect them back to the admin page
			$this -> session -> set_flashdata('message', 'User Created');
			redirect('auth', 'refresh');
		} else { //display the create user form
			//set the flash data error message if there is one
			$this -> data['message'] = (validation_errors() ? validation_errors() : ($this -> ion_auth -> errors() ? $this -> ion_auth -> errors() : $this -> session -> flashdata('message')));
			$this -> data['first_name'] = array('name' => 'first_name', 'id' => 'first_name', 'type' => 'text', 'value' => $this -> form_validation -> set_value('first_name'),);
			$this -> data['last_name'] = array('name' => 'last_name', 'id' => 'last_name', 'type' => 'text', 'value' => $this -> form_validation -> set_value('last_name'),);
			$this -> data['email'] = array('name' => 'email', 'id' => 'email', 'type' => 'text', 'value' => $this -> form_validation -> set_value('email'),);
			$this -> data['company'] = array('name' => 'company', 'id' => 'company', 'type' => 'text', 'value' => $this -> form_validation -> set_value('company'),);
			$this -> data['phone1'] = array('name' => 'phone1', 'id' => 'phone1', 'type' => 'text', 'value' => $this -> form_validation -> set_value('phone1'),);
			$this -> data['phone2'] = array('name' => 'phone2', 'id' => 'phone2', 'type' => 'text', 'value' => $this -> form_validation -> set_value('phone2'),);
			$this -> data['phone3'] = array('name' => 'phone3', 'id' => 'phone3', 'type' => 'text', 'value' => $this -> form_validation -> set_value('phone3'),);
			$this -> data['password'] = array('name' => 'password', 'id' => 'password', 'type' => 'password', 'value' => $this -> form_validation -> set_value('password'),);
			$this -> data['password_confirm'] = array('name' => 'password_confirm', 'id' => 'password_confirm', 'type' => 'password', 'value' => $this -> form_validation -> set_value('password_confirm'),);
			$this -> load -> view('auth/header');
			$this -> load -> view('auth/create_user', $this -> data);
			$this -> load -> view('auth/left_sidebar');
//            $this->load->view('auth/footer');
		}
	}

	/**
	 * Метод редактирования пользователей
	 *
	 * @author Ермашевский Денис
	 * @return array $data
	 */
	function edit_user()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('auth', 'refresh');
		}
		//validate form input
		$this -> form_validation -> set_rules('first_name', 'First Name', 'required|xss_clean');
		$this -> form_validation -> set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this -> form_validation -> set_rules('email', 'Email', 'required|valid_email');
		$this -> form_validation -> set_rules('phone1', 'Phone part 1', 'required|xss_clean|min_length[3]|max_length[3]');
		$this -> form_validation -> set_rules('phone2', 'Phone part 2', 'required|xss_clean|min_length[3]|max_length[3]');
		$this -> form_validation -> set_rules('phone3', 'Phone part 3', 'required|xss_clean|min_length[4]|max_length[4]');
		$this -> form_validation -> set_rules('company', 'Company', 'required|xss_clean');
		if ($this -> form_validation -> run() === TRUE) {
			$id = (int) $this -> input -> post('id');
			$data = array('first_name' => $this -> input -> post('first_name'), 'last_name' => $this -> input -> post('last_name'), 'username' => strtolower($this -> input -> post('first_name')) . ' ' . strtolower($this -> input -> post('last_name')), 'email' => $this -> input -> post('email'), 'company' => $this -> input -> post('company'), 'phone' => $this -> input -> post('phone1') . '-' . $this -> input -> post('phone2') . '-' . $this -> input -> post('phone3'),);
		}
		if ($this -> form_validation -> run() === TRUE AND $this -> ion_auth -> update_user($id, $data)) { //Не существует. //check to see if we are editing the user
			//redirect them back to the admin page
			//EXECUTE THE RESET PASSWORD HERE IF CHECKED
			$this -> session -> set_flashdata('ion_message', 'User edited');
			redirect('auth/index', 'refresh');
		} else { //display the edit user form
			//set the flash data error message if there is one
			$this -> data['message'] = (validation_errors() ? validation_errors() : ($this -> ion_auth -> errors() ? $this -> ion_auth -> errors() : $this -> session -> flashdata('ion_message')));
			//get posted ID if exists, else the one from uri.
			//in order to get user datas from table
			$id = (isset($id)) ? $id : (int) $this -> uri -> segment(3);
			//get current user datas from table and set default form values
			$user = $this -> ion_auth -> user($id) -> row();
			//passing user id to view
			$this -> data['user_id'] = $user -> id;
			//get phone parts
			$phoneparts = explode('-', $user -> phone);
			//prepare form
			$this -> data['first_name'] = array('name' => 'first_name', 'id' => 'first_name', 'type' => 'text', 'value' => $this -> form_validation -> set_value('first_name', $user -> first_name),);
			$this -> data['last_name'] = array('name' => 'last_name', 'id' => 'last_name', 'type' => 'text', 'value' => $this -> form_validation -> set_value('last_name', $user -> last_name),);
			$this -> data['email'] = array('name' => 'email', 'id' => 'email', 'type' => 'text', 'value' => $this -> form_validation -> set_value('email', $user -> email),);
			$this -> data['company'] = array('name' => 'company', 'id' => 'company', 'type' => 'text', 'value' => $this -> form_validation -> set_value('company', $user -> company),);
			$this -> data['phone1'] = array('name' => 'phone1', 'id' => 'phone1', 'type' => 'text', 'value' => $this -> form_validation -> set_value('phone1', $phoneparts[0]),);
			$this -> data['phone2'] = array('name' => 'phone2', 'id' => 'phone2', 'type' => 'text', 'value' => $this -> form_validation -> set_value('phone2', $phoneparts[1]),);
			$this -> data['phone3'] = array('name' => 'phone3', 'id' => 'phone3', 'type' => 'text', 'value' => $this -> form_validation -> set_value('phone3', $phoneparts[2]),);
			$this -> data['id'] = array('name' => 'id', 'id' => 'id', 'type' => 'hidden', 'value' => $this -> form_validation -> set_value('id', $user -> id),);
			$this -> load -> view('auth/header'); //Заголовок страницы
			$this -> load -> view('auth/edit_user', $this -> data);
			$this -> load -> view('auth/left_sidebar');
//            $this->load->view('auth/footer');
		}
	}

	/**
	 * Метод защиты от CSRF аттак
	 *
	 * @author Ермашевский Денис
	 *
	 * @return array $value
	 *
	 */
	function _get_csrf_nonce()
	{
		$this -> load -> helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this -> session -> set_flashdata('csrfkey', $key);
		$this -> session -> set_flashdata('csrfvalue', $value);
		return array($key => $value);
	}

	/**
	 * Метод защиты от CSRF аттак
	 *
	 * @author Ермашевский Денис
	 *
	 * @return bool TRUE
	 *
	 */
	function _valid_csrf_nonce()
	{
		if ($this -> input -> post($this -> session -> flashdata('csrfkey')) !== FALSE AND $this -> input -> post($this -> session -> flashdata('csrfkey')) === $this -> session -> flashdata('csrfvalue')) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Метод возвращает список типов ЛС
	 *
	 * @author Ермашевский Денис
	 *
	 * @return array servicesType
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
	 * Метод возвращает список тарифов
	 *
	 * @author Ермашевский Денис
	 * @return array $data['tariffs]
	 */
	function getTariffs()
	{
		$this -> load -> model('services_model');
		$data = array();
		$data['tariffs'] = $this -> services_model -> getTariffs();
		return $data['tariffs'];
	}

	/**
	 * Метод добавления новой номенклатуры
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function add_assortment()
	{
		$this -> data['title'] = 'Создание новой номеклатуры';
		if ( ! $this -> ion_auth -> logged_in() OR ! $this -> ion_auth -> is_admin()) {
			redirect('auth', 'refresh');
		}
		//validate form input
		$this -> form_validation -> set_rules('assortment_name', 'Assortment name', 'required|xss_clean');
		$this -> form_validation -> set_rules('serviceType', 'Service Type', 'required|xss_clean');
		$this -> form_validation -> set_rules('paymentType', 'Payment Type', 'required|xss_clean');
		//$this->form_validation->set_rules('name_element', 'Name element', 'required|xss_clean');
		$this -> form_validation -> set_rules('element_form', 'Element form', 'required|xss_clean');
		$this -> form_validation -> set_rules('type_resources', 'Type resources', 'required|xss_clean');
		$this -> form_validation -> set_rules('tariff', 'Tariff', 'required|xss_clean');
		if ($this -> form_validation -> run() === TRUE) { //check to see if we are creating the user
			//redirect them back to the admin page
			$this -> session -> set_flashdata('message', 'Номенклатура добавлена');
			$data['assortment_name'] = $_POST['assortment_name'];
			$data['serviceType'] = $_POST['serviceType'];
			$data['paymentType'] = $_POST['paymentType'];
			$data['name_element'] = $_POST['name_element'];
			$data['element_form'] = $_POST['element_form'];
			$data['default_element_value'] = $_POST['default_element_value'];
			$data['datasource'] = $_POST['datasource'];
			$data['type_resources'] = $_POST['type_resources'];
			$data['tariff'] = $_POST['tariff'];

			$user = $this->ion_auth->user()->row();
			$mdc = new LoggerMDC();
			$mdc->put('username',$user->username);
			$this -> log -> info('Пользователь создал номенклатуру: '.$data['assortment_name']);
			$this -> log -> info($data);

			$this -> load -> model('services_model');
			$this -> services_model -> create_assortment_item($data);
			redirect('services/assortmentList', 'refresh');
		} else { //display the create user form
			//set the flash data error message if there is one
			$this -> data['message'] = (validation_errors() ? validation_errors() : ($this -> ion_auth -> errors() ? $this -> ion_auth -> errors() : $this -> session -> flashdata('message')));
			$this -> data['assortment_name'] = array('name' => 'assortment_name', 'id' => 'assortment_name', 'type' => 'text', 'value' => $this -> form_validation -> set_value('assortment_name'),);
			$this -> data['serviceType'] = array('name' => 'serviceType', 'id' => 'serviceType', 'type' => 'text', 'value' => $this -> form_validation -> set_value('serviceType'),);
			$this -> data['paymentType'] = array('name' => 'paymentType', 'id' => 'paymentType', 'type' => 'text', 'value' => $this -> form_validation -> set_value('paymentType'),);
			$this -> data['tariffList'] = array('name' => 'tariffList', 'id' => 'tariffList', 'type' => 'text', 'value' => $this -> form_validation -> set_value('tariffList'),);
			$this -> data['label_element'] = array('name' => 'label_element', 'id' => 'label_element', 'type' => 'text', 'value' => $this -> form_validation -> set_value('label_element'),);
			$this -> data['name_element'] = array('name' => 'name_element', 'id' => 'name_element', 'type' => 'text', 'value' => $this -> form_validation -> set_value('name_element'),);
			$this -> data['element_form'] = array('name' => 'element_form', 'id' => 'element_form', 'type' => 'text', 'value' => $this -> form_validation -> set_value('element_form'),);
			$this -> data['types_elements'] = array('name' => 'types_elements', 'id' => 'types_elements', 'type' => 'text', 'value' => $this -> form_validation -> set_value('types_elements'),);
			$this -> data['datasource'] = array('name' => 'datasource', 'id' => 'datasource', 'type' => 'text', 'value' => $this -> form_validation -> set_value('datasource'),);
			$this -> data['default_element_value'] = array('name' => 'default_element_value', 'id' => 'default_element_value', 'type' => 'text', 'value' => $this -> form_validation -> set_value('default_element_value'),);
			$this -> data['type_resources'] = array('name' => 'type_resources', 'id' => 'type_resources', 'type' => 'text', 'value' => $this -> form_validation -> set_value('type_resources'),);
			$this -> data['tariff'] = array('name' => 'tariff', 'id' => 'tariff', 'type' => 'text', 'value' => $this -> form_validation -> set_value('tariff'),);
			$this -> load -> view('header');
			$this -> load -> view('auth/add_assortment', $this -> data);
			$this -> load -> view('left_sidebar');
//            $this->load->view('auth/footer');
		}
	}

}

//End of file auth.php
//Location: ./controllers/auth.php