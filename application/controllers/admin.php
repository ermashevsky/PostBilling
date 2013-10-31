<?php

/**
 * Admin
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Controllers.Admin.Admin
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://www.ci2.lcl/
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(1);
include ('application/third_party/log4php/Logger.php');
$config_log_file = APPPATH . 'config/config_log4php.xml';
Logger::configure($config_log_file);

/**
 * Класс Admin содержит админку
 *
 * @category PHP
 * @package  Controllers.Admin.Admin
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Admin extends CI_Controller
{

	public $log;

	/**
	 * Унифицированный метод-конструктор __construct()
	 *
	 * @author Ермашевский Денис
	 */
	function __construct()
	{
		$this -> log = Logger::getLogger(__CLASS__);
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
			$this -> load -> view('admin/header');
			$this -> load -> view('admin/index', $this -> data);
			$this -> load -> view('admin/left_sidebar');
//            $this->load->view('auth/footer');
		}
	}

	function report()
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
			$this -> load -> view('admin/header');
			$this -> load -> view('admin/for1c');
			$this -> load -> view('admin/left_sidebar');
//            $this->load->view('auth/footer');
		}
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
			$this -> load -> model('admin_model');
			$data['serviceList'] = $this -> admin_model -> getServiceTypes();
			return $data['serviceList'];
		}
	}

	function buildReport()
	{
		$month = $this -> input -> post('month');
		$id_service = $this -> input -> post('id_service');
		$this -> load -> model('admin_model');
		$data = $this -> admin_model -> buildReport($month, $id_service);
		echo json_encode($data);
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
		$this -> form_validation -> set_rules('login', 'Логин', 'required|xss_clean');
		$this -> form_validation -> set_rules('first_name', 'First Name', 'required|xss_clean');
		$this -> form_validation -> set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this -> form_validation -> set_rules('email', 'Email Address', 'required|valid_email');
		$this -> form_validation -> set_rules('phone', 'First Part of Phone', 'required|xss_clean|min_length[2]|max_length[15]');
		$this -> form_validation -> set_rules('company', 'Company Name', 'required|xss_clean');
		$this -> form_validation -> set_rules('password', 'Password', 'required|min_length[' . $this -> config -> item('min_password_length', 'ion_auth') . ']|max_length[' . $this -> config -> item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this -> form_validation -> set_rules('password_confirm', 'Password Confirmation', 'required');

		if ($this -> form_validation -> run() === TRUE) {
			$username = strtolower($this -> input -> post('login'));
			$email = $this -> input -> post('email');
			$password = $this -> input -> post('password');
			$additional_data = array(
				'first_name' => $this -> input -> post('first_name'),
				'last_name' => $this -> input -> post('last_name'),
				'company' => $this -> input -> post('company'),
				'phone' => $this -> input -> post('phone'),);
		}
		if ($this -> form_validation -> run() === TRUE AND $this -> ion_auth -> register($username, $password, $email, $additional_data)) { //check to see if we are creating the user
			//redirect them back to the admin page
			$this -> session -> set_flashdata('message', 'Пользователь успешно добавлен');
			redirect('admin', 'refresh');
		} else { //display the create user form
			//set the flash data error message if there is one
			$this -> data['message'] = (validation_errors() ? validation_errors() : ($this -> ion_auth -> errors() ? $this -> ion_auth -> errors() : $this -> session -> flashdata('message')));

			$this -> data['login'] = array(
				'name' => 'login',
				'id' => 'login',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('login'),
			);
			$this -> data['first_name'] = array(
				'name' => 'first_name',
				'id' => 'first_name',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('first_name'),
			);

			$this -> data['last_name'] = array(
				'name' => 'last_name',
				'id' => 'last_name',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('last_name'),
			);

			$this -> data['email'] = array(
				'name' => 'email',
				'id' => 'email',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('email'),
			);

			$this -> data['company'] = array(
				'name' => 'company',
				'id' => 'company',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('company'),
			);

			$this -> data['phone'] = array(
				'name' => 'phone',
				'id' => 'phone',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('phone'),
			);

			$this -> data['password'] = array(
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
				'value' => $this -> form_validation -> set_value('password'),
			);

			$this -> data['password_confirm'] = array(
				'name' => 'password_confirm',
				'id' => 'password_confirm',
				'type' => 'password',
				'value' => $this -> form_validation -> set_value('password_confirm'),
			);

			$this -> load -> view('admin/header');
			$this -> load -> view('admin/create_user', $this -> data);
			$this -> load -> view('admin/left_sidebar');
//            $this->load->view('auth/footer');
		}
	}

	/**
	 * Метод редактирования пользователей
	 *
	 * @author Ермашевский Денис
	 * @return array $data
	 */
	function edit_user($id)
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('admin', 'refresh');
		}
		$user = $this -> ion_auth -> user($id) -> row();
		$groups = $this -> ion_auth -> groups() -> result_array();
		$currentGroups = $this -> ion_auth -> get_users_groups($id) -> result();

		//validate form input
		$this -> form_validation -> set_rules('first_name', 'First Name', 'required|xss_clean');
		$this -> form_validation -> set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this -> form_validation -> set_rules('email', 'Email', 'required|valid_email');
		$this -> form_validation -> set_rules('phone', 'Телефон', 'required|xss_clean|min_length[2]|max_length[15]');
		$this -> form_validation -> set_rules('company', 'Company', 'required|xss_clean');
		$this -> form_validation -> set_rules('groups', 'Группа', 'xss_clean');

		if ($this -> form_validation -> run() === TRUE) {
			//$id = (int) $this -> input -> post('id');
			$data = array(
				'first_name' => $this -> input -> post('first_name'),
				'last_name' => $this -> input -> post('last_name'),
				'username' => $this -> input -> post('username'),
				'email' => $this -> input -> post('email'),
				'company' => $this -> input -> post('company'),
				'phone' => $this -> input -> post('phone'),);
			//Update the groups user belongs to
			$groupData = $this -> input -> post('groups');

			if (isset($groupData) && ! empty($groupData)) {

				$this -> ion_auth -> remove_from_group('', $id);

				foreach ($groupData as $grp) {
					$this -> ion_auth -> add_to_group($grp, $id);
				}
			}


			//update the password if it was posted
			if ($this -> input -> post('password')) {
				$this -> form_validation -> set_rules('password', 'Password', 'required|min_length[' . $this -> config -> item('min_password_length', 'ion_auth') . ']|max_length[' . $this -> config -> item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this -> form_validation -> set_rules('password_confirm', 'Password Confirmation', 'required');

				$data['password'] = $this -> input -> post('password');
			}
		}
		if ($this -> form_validation -> run() === TRUE) {//check to see if we are editing the user
			//redirect them back to the admin page
			//EXECUTE THE RESET PASSWORD HERE IF CHECKED
			$this -> session -> set_flashdata('ion_message', 'User edited');
			$this -> ion_auth -> update($user -> id, $data);
			redirect('admin/index', 'refresh');
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
			//process the phone number
			if (isset($user -> phone) && ! empty($user -> phone)) {
				$user -> phone = explode(' ', $user -> phone);
			}

			//prepare form
			//pass the user to the view
			$this -> data['user'] = $user;
			$this -> data['groups'] = $groups;
			$this -> data['currentGroups'] = $currentGroups;

			$this -> data['username'] = array(
				'name' => 'username',
				'id' => 'username',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('username', $user -> username),
				'readonly' => 'true',
			);

			$this -> data['first_name'] = array(
				'name' => 'first_name',
				'id' => 'first_name',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('first_name', $user -> first_name),
			);

			$this -> data['last_name'] = array(
				'name' => 'last_name',
				'id' => 'last_name',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('last_name', $user -> last_name),
			);

			$this -> data['email'] = array(
				'name' => 'email',
				'id' => 'email',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('email', $user -> email),
			);

			$this -> data['company'] = array(
				'name' => 'company',
				'id' => 'company',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('company', $user -> company),
			);

			$this -> data['phone'] = array(
				'name' => 'phone',
				'id' => 'phone',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('phone', $user -> phone[0]),
			);

			$this -> data['id'] = array(
				'name' => 'id',
				'id' => 'id',
				'type' => 'hidden',
				'value' => $this -> form_validation -> set_value('id', $user -> id),
			);

			$this -> load -> view('admin/header'); //Заголовок страницы
			$this -> load -> view('admin/edit_user', $this -> data);
			$this -> load -> view('admin/left_sidebar');
//            $this->load->view('auth/footer');
		}
	}

	// create a new group
	function create_group()
	{
		$this -> data['title'] = "Create Group";

		if ( ! $this -> ion_auth -> logged_in() || ! $this -> ion_auth -> is_admin()) {
			redirect('admin', 'refresh');
		}

		//validate form input
		$this -> form_validation -> set_rules('group_name', 'Group name', 'required|alpha_dash|xss_clean');
		$this -> form_validation -> set_rules('description', 'Description', 'xss_clean');

		if ($this -> form_validation -> run() == TRUE) {
			$new_group_id = $this -> ion_auth -> create_group($this -> input -> post('group_name'), $this -> input -> post('description'));
			if ($new_group_id) {
				// check to see if we are creating the group
				// redirect them back to the admin page
				$this -> session -> set_flashdata('message', $this -> ion_auth -> messages());
				redirect("admin", 'refresh');
			}
		} else {
			//display the create group form
			//set the flash data error message if there is one
			echo $this -> data['message'] = (validation_errors() ? validation_errors() : ($this -> ion_auth -> errors() ? $this -> ion_auth -> errors() : $this -> session -> flashdata('message')));

			$this -> data['group_name'] = array(
				'name' => 'group_name',
				'id' => 'group_name',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('group_name'),
			);
			$this -> data['description'] = array(
				'name' => 'description',
				'id' => 'description',
				'type' => 'text',
				'value' => $this -> form_validation -> set_value('description'),
			);
			$this -> load -> view('admin/header');
			$this -> load -> view('admin/create_group', $this -> data);
			$this -> load -> view('admin/left_sidebar');
		}
	}

	function deleteUser()
	{
		$id = $this -> input -> post('id');
		$username = $this -> input -> post('username');
		$result = $this -> ion_auth -> delete_user($id);
		echo json_encode($result);
	}

	//activate the user
	function activate($id, $code = false)
	{
		if ($code !== false) {
			$activation = $this -> ion_auth -> activate($id, $code);
		} else if ($this -> ion_auth -> is_admin()) {
			$activation = $this -> ion_auth -> activate($id);
		}

		if ($activation) {
			//redirect them to the auth page
			$this -> session -> set_flashdata('message', $this -> ion_auth -> messages());
			redirect("admin", 'refresh');
		} else {
			//redirect them to the forgot password page
			$this -> session -> set_flashdata('message', $this -> ion_auth -> errors());
			redirect("admin/forgot_password", 'refresh');
		}
	}

	//deactivate the user
	function deactivate($id)
	{
		$id = $this -> config -> item('use_mongodb', 'ion_auth') ? (string) $id : (int) $id;

		$this -> load -> library('form_validation');
		$this -> form_validation -> set_rules('confirm', 'confirmation', 'required');
		$this -> form_validation -> set_rules('id', 'user ID', 'required|alpha_numeric');

		if ($this -> form_validation -> run() == FALSE) {
			// insert csrf check
			$this -> data['csrf'] = $this -> _get_csrf_nonce();
			$this -> data['edit_user'] = $this -> ion_auth -> user($id) -> row();
			$this -> load -> view('admin/header');
			$this -> _render_page('admin/deactivate_user', $this -> data);
			$this -> load -> view('admin/left_sidebar');
		} else {
			// do we really want to deactivate?
			if ($this -> input -> post('confirm') == 'yes') {
				// do we have a valid request?
//				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
//				{
//					show_error('This form post did not pass our security checks.');
//				}
				// do we have the right userlevel?
				if ($this -> ion_auth -> logged_in() && $this -> ion_auth -> is_admin()) {
					$this -> ion_auth -> deactivate($id);
				}
			}

			//redirect them back to the auth page
			redirect('admin', 'refresh');
		}
	}

	function _get_csrf_nonce()
	{
		$this -> load -> helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this -> session -> set_flashdata('csrfkey', $key);
		$this -> session -> set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	function _valid_csrf_nonce()
	{
		if ($this -> input -> post($this -> session -> flashdata('csrfkey')) !== FALSE &&
				$this -> input -> post($this -> session -> flashdata('csrfkey')) == $this -> session -> flashdata('csrfvalue')) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function _render_page($view, $data = null, $render = false)
	{

		$this -> viewdata = (empty($data)) ? $this -> data : $data;

		$view_html = $this -> load -> view($view, $this -> viewdata, $render);

		if ( ! $render)
			return $view_html;
	}

	function getDatatableLogs()
	{
		$this -> datatables -> select('id, timestamp, logger, level, method, user, message, thread, file, line')
				-> from('log4php_log');
		echo $this -> datatables -> generate();
	}

	function viewLogs()
	{
		$this -> load -> view('admin/header');
		$this -> _render_page('admin/viewLogs');
		$this -> load -> view('admin/left_sidebar');
	}

	function changeTariff()
	{
		$this -> load -> model('admin_model');
		$data = $this -> admin_model -> getServiceType();
		$this -> load -> view('admin/header');
		$this -> load -> view('admin/changeTariff', $data);
		$this -> load -> view('admin/left_sidebar');
	}

	function linkedSelects()
	{
		$action = $this -> input -> post('action');
		$marker = $this -> input -> post('marker');
		$id_assortment = $this -> input -> post('id_assortment');
		$id_tariff = $this -> input -> post('id_tariff');

		$this -> load -> model('admin_model');
		$this -> admin_model -> linkedSelects($action, $marker, $id_assortment, $id_tariff);
	}

	function setEndDateForCustomerAssortments()
	{
		$id = $this -> input -> post('id');
		$new_date = $this -> input -> post('new_date');
		$uniq_id = $this -> input -> post('uniq_id');
		$id_account = $this -> input -> post('id_account');
		$payment_name = $this -> input -> post('payment_name');
		$resources = $this -> input -> post('resources');
		$identifier = $this -> input -> post('identifier');
		$tariffs = $this -> input -> post('new_tariff');
		$period = $this -> input -> post('period');

		$date = new DateTime($new_date);
		$date -> modify('-1 day');
		$modify_end_date = $date -> format('Y-m-d');

		$user = $this -> ion_auth -> user() -> row();
		$mdc = new LoggerMDC();
		$mdc -> put('username', $user -> username);


		$this -> load -> model('admin_model');
		$this -> admin_model -> updateEndDateForCustomerAssortments($id, $modify_end_date);

		$this -> log -> warn('Пользователь произвел смену тарифа на номеклатуре. Дата окончания действия тарифа ' . $modify_end_date . ' у записи c ID ' . $id);

		$this -> admin_model -> insertRow($uniq_id, $id_account, $payment_name, $resources, $identifier, $tariffs, $new_date, $period);

		$this -> log -> info('Пользователь установил новый тариф на номеклатуре. Дата начала действия тарифа ' . $new_date . ' на номеклатуре ' . $payment_name . ' ID лицевого счета: ' . $id_account . ' ID нового тарифа:' . $tariffs);
	}

	/*
	 *
	 * Создание файла остатков
	 *
	 */

	function compare()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		} elseif ( ! $this -> ion_auth -> is_admin()) {
			//redirect them to the home page because they must be an administrator to view this
			redirect($this -> config -> item('base_url'), 'refresh');
		} else {
			$this -> load -> view('admin/header');
			$this -> load -> view('admin/compare');
			$this -> load -> view('admin/left_sidebar');
		}
	}

}

//End of file auth.php
//Location: ./controllers/admin/admin.php