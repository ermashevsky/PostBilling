<?php

/**
 * Clients_model
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Models.Clients_Model
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://www.ci2.lcl/
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Класс Clients содержит методы работы  с данными клиентов
 *
 * @category PHP
 * @package  Models.Clients_Model
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Clients_model extends CI_Model
{

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $id;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $client_name;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $account;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $inn;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $client_manager;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $phone_number;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $client_email;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $bindins_name;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $accounts;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $dateFrom;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $DateTo;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $service_description;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $marker;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $target;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $id_account;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $payment_name;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $type_resources;

	/**
	 * Identity.
	 *
	 * @var $id Clients_model
	 */
	var $discount;

	/**
	 * Унифицированный метод-конструктор __construct()
	 *
	 * @author Ермашевский Денис
	 */
	function __construct()
	{
		parent::__construct();
		$this -> load -> library('ion_auth');
	}

	/**
	 * Метод возвращает список клиентов
	 *
	 * @param string $where       условие
	 * @param int    $limit_from  диапазон
	 * @param int    $limit_count сколько выводить
	 *
	 * @author Ермашевский Денис
	 * @return mixed $results;
	 *
	 */
	function get_client_list($where = FALSE, $limit_from = FALSE, $limit_count = FALSE)
	{
		$results = array();

//		if ($limit_from)
//			$limit_from = intval($limit_from);
//		else
//			$limit_from = NULL;
//		if ($limit_count)
//			$limit_count = intval($limit_count);
//		else
//			$limit_count = NULL;
//'customer_encashment', 'customer_encashment.id_account = clients_accounts.id', 'left'
//		$this -> db -> select("clients.id,clients.client_name,clients.inn, round(sum( REPLACE(customer_payments.amount,',','.') ),2) as amount, IFNULL(payment.payments,'00.00') as payment, GROUP_CONCAT(DISTINCT services.marker) as `accounts`",false);
//		$this -> db -> from('clients');
//		$this -> db -> join('clients_accounts', 'clients_accounts.id_clients = clients.id');
//		$this -> db -> join('services', 'services.id = clients_accounts.id_service');
//		$this -> db -> join('customer_payments', 'customer_payments.id_account = clients_accounts.id', 'left');
//		$this -> db -> join("(SELECT *,round(sum( REPLACE( amount, ',','.' ) ),2) as payments FROM customer_encashment group by id_client) AS payment",'payment.id_client=clients_accounts.id_clients','left');
//		$this -> db -> group_by('client_name');
		$this -> db -> select("clients.id, clients.client_name, nachislenie, oplata, inn, GROUP_CONCAT(DISTINCT services.marker) as `accounts`", false);
		$this -> db -> from('clients');
		$this -> db -> join('clients_accounts', 'clients_accounts.id_clients = clients.id');
		$this -> db -> join('services', 'services.id = clients_accounts.id_service');
		$this -> db -> join("(SELECT id_client,round(sum( REPLACE( amount, ',','.' ) ),2) as nachislenie FROM customer_payments group by id_client) AS payment", 'payment.id_client=clients_accounts.id_clients', 'left');
		$this -> db -> join("(SELECT id_client,round(sum( REPLACE( amount, ',','.' ) ),2) as oplata FROM customer_encashment group by id_client) AS ehcashment", 'ehcashment.id_client=clients_accounts.id_clients', 'left');
		$this -> db -> group_by('id');
		$this -> db -> order_by('client_name');

		$res = $this -> db -> get();
		if (0 < $res -> num_rows) {
			foreach ($res -> result() as $row) {
				$tmp = new Clients_model();
				$tmp -> id = $row -> id;
				$tmp -> client_name = $row -> client_name;
				$tmp -> nachislenie = $row -> nachislenie;
				$tmp -> oplata = $row -> oplata;
				$tmp -> account = $row -> accounts;
				$tmp -> inn = $row -> inn;
				$results[$tmp -> id] = $tmp;
			}
		}
		return $results;
	}

	function countAccount($id_account)
	{
		$this -> db -> select('*');
		$this -> db -> from('clients_accounts');
		$this -> db -> where('id_clients', $id_account);
		$query = $this -> db -> get();
		return $query -> num_rows;
	}

	/**
	 * Метод поиска клиентов по имени при
	 * добавлении нового клиента для исключения
	 * задвоения.
	 * @author Денис Ермашевский <egrad77@mail.ru>
	 * @return array Retun Array
	 */
	function getSubject($search)
	{
		$this -> db -> select("id, inn, client_name");
		//$whereCondition = array('client_name' =>$search);
		$this -> db -> like('inn', $search);
		//$this->db->not_like('inn',0000000000);
		$this -> db -> from('clients');
		$query = $this -> db -> get();
		return $query -> result();
	}

	function getIdentifier($search)
	{
		$this -> db -> select("id_clients, bindings_name, accounts, end_date");
		//$whereCondition = array('client_name' =>$search);
		$this -> db -> join('clients_accounts', 'clients_accounts.id = customer_service.id_account');
		$this -> db -> where('identifier', $search);
		$this -> db -> from('customer_service');
		$query = $this -> db -> get();
		return $query -> result_array();
	}

	function getAccount($search)
	{
		$this -> db -> select("id, id_clients, bindings_name, accounts");
		//$whereCondition = array('client_name' =>$search);
		$this -> db -> like('accounts', $search);
		$this -> db -> from('clients_accounts');
		$query = $this -> db -> get();
		return $query -> result();
	}

	function getByAccount($search)
	{
		$this -> db -> select("id, id_clients, bindings_name, accounts");
		//$whereCondition = array('client_name' =>$search);
		$this -> db -> like('accounts', $search);
		$this -> db -> from('clients_accounts');
		$this -> db -> group_by('id_clients');
		$query = $this -> db -> get();
		return $query -> result();
	}

	function getByPhone($search)
	{
		$this -> db -> select("clients_accounts.id, clients_accounts.id_clients, clients_accounts.bindings_name, clients_accounts.accounts");
		$this -> db -> like('free_phone_pool.resources', $search);
		$this -> db -> from('clients_accounts');
		$this -> db -> group_by('clients_accounts.bindings_name');
		$this -> db -> join('customer_service', 'customer_service.id_account =  clients_accounts.id', 'inner');
		$this -> db -> join('free_phone_pool', 'free_phone_pool.id =  customer_service.resources', 'inner');
		$query = $this -> db -> get();
		return $query -> result();
	}

	function getByDate($search)
	{

		$date = new DateTime($search);

		$this -> db -> select("clients_accounts.id, clients_accounts.id_clients, clients_accounts.bindings_name, clients_accounts.accounts");
		$this -> db -> like('customer_service.datepicker1', $date -> format('Y-m-d'));
		$this -> db -> from('clients_accounts');
		$this -> db -> group_by('clients_accounts.bindings_name');
		$this -> db -> join('customer_service', 'customer_service.id_account =  clients_accounts.id', 'inner');
		//$this->db->join('free_phone_pool', 'free_phone_pool.id =  customer_service.resources','inner');
		$query = $this -> db -> get();
		return $query -> result();
	}

	function getPaymentByAccounts($id)
	{
		$id = (int) $id;
		$client_payment = array();
		$this -> db -> select('clients_accounts.accounts,clients_accounts.id_service,clients_accounts.id as id_account, IFNULL(SUM(customer_payments.amount),"00.00") as amount, IFNULL(round(payment.payments,2),"00.00") as payment, IFNULL(round(discount.discounts,2),"00.00") as discount', false);
		$this -> db -> from('clients');
		$this -> db -> where('clients_accounts.id_clients', $id);
		$this -> db -> group_by('clients_accounts.accounts');
		$this -> db -> join('clients_accounts', 'clients_accounts.id_clients = clients.id', 'left');
		$this -> db -> join('customer_payments', 'customer_payments.id_account = clients_accounts.id', 'left');
		$this -> db -> join("(SELECT *, round(sum( REPLACE( amount, ',','.' ) ),2) as payments FROM customer_encashment group by id_account) AS payment", 'payment.id_account=clients_accounts.id', 'left');
		$this -> db -> join("(SELECT *, round(sum( REPLACE( amount, ',','.' ) ),2) as discounts FROM customer_discounts group by id_account) AS discount", 'discount.id_account=clients_accounts.id', 'left');
		$client = $this -> db -> get();
		if (0 < $client -> num_rows) {
			foreach ($client -> result() as $info) {
				$tmp = new Clients_model();
				$tmp -> id_client = $id;
				$tmp -> id_account = $info -> id_account;
				$tmp -> accounts = $info -> accounts;
				$tmp -> id_service = $info -> id_service;
				$tmp -> amount = $info -> amount;
				$tmp -> payment = $info -> payment;
				$tmp -> discount = $info -> discount;

				$client_payment[$tmp -> id_account] = $tmp;
			}
		}
		return $client_payment;
	}

	function rdate($format, $timestamp = null, $case = 0)
	{
		if ($timestamp === null)
			$timestamp = time();

		static $loc = 'Январ,ь,я,е,ю,ём,е
  Феврал,ь,я,е,ю,ём,е
  Март, ,а,е,у,ом,е
  Апрел,ь,я,е,ю,ем,е
  Ма,й,я,е,ю,ем,е
  Июн,ь,я,е,ю,ем,е
  Июл,ь,я,е,ю,ем,е
  Август, ,а,е,у,ом,е
  Сентябр,ь,я,е,ю,ём,е
  Октябр,ь,я,е,ю,ём,е
  Ноябр,ь,я,е,ю,ём,е
  Декабр,ь,я,е,ю,ём,е';

		if (is_string($loc)) {
			$months = array_map('trim', explode("\n", $loc));
			$loc = array();
			foreach ($months as $monthLocale) {
				$cases = explode(',', $monthLocale);
				$base = array_shift($cases);

				$cases = array_map('trim', $cases);

				$loc[] = array(
					'base' => $base,
					'cases' => $cases,
				);
			}
		}

		$m = (int) date('n', $timestamp) - 1;

		$F = $loc[$m]['base'] . $loc[$m]['cases'][$case];

		$format = strtr($format, array(
			'F' => $F,
			'M' => substr($F, 0, 3),
		));

		return date($format, $timestamp);
	}

	function rudate($format, $timestamp = 0, $nominative_month = false)
	{
		if ( ! $timestamp)
			$timestamp = time();
		elseif ( ! preg_match("/^[0-9]+$/", $timestamp))
			$timestamp = strtotime($timestamp);

		$F = $nominative_month ? array(1 => "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь") : array(1 => "Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");
		$M = array(1 => "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
		$l = array("Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота");
		$D = array("Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб");

		$format = str_replace("F", $F[date("n", $timestamp)], $format);
		$format = str_replace("M", $M[date("n", $timestamp)], $format);
		$format = str_replace("l", $l[date("w", $timestamp)], $format);
		$format = str_replace("D", $D[date("w", $timestamp)], $format);

		return date($format, $timestamp);
	}

	function getPaymentsGroupByMonth($id_client, $id_account)
	{

		$client_payments_month = array();
		$this -> db -> select('customer_payments.id, accounts, MONTH(  `period_start` ) AS months, YEAR(  `period_start` ) AS years, ROUND( SUM(  `amount` ) , 2 ) AS amount, period_start', FALSE);
		$this -> db -> from('customer_payments');
		$this -> db -> join('clients_accounts', 'clients_accounts.id = customer_payments.id_account', 'left');
		$this -> db -> where('customer_payments.id_client', $id_client);
		$this -> db -> where('customer_payments.id_account', $id_account);
		$this -> db -> group_by('months, years');
		$this -> db -> order_by('period_start');
		$pay_by_month = $this -> db -> get();
		if (0 < $pay_by_month -> num_rows) {
			foreach ($pay_by_month -> result() as $data) {
				$tmp = new Clients_model();
				$tmp -> id = $data -> id;
				$tmp -> accounts = $data -> accounts;
				$tmp -> period = $this -> rudate("M Y", strtotime('01-' . $data -> months . '-' . $data -> years));
				$tmp -> months = $data -> months;
				$tmp -> years = $data -> years;
				$tmp -> amount = $data -> amount;
				$tmp -> period_start = $data -> period_start;

				$client_payments_month[$tmp -> id] = $tmp;
			}
		}
		return $client_payments_month;
	}

	function getAccountListByIdClient($id, $id_client)
	{
		$this -> db -> select('id,accounts');
		$this -> db -> from('clients_accounts');
		$this -> db -> where('id_clients', $id_client);
		$this -> db -> where_not_in('id', $id);
		$client = $this -> db -> get();
		$accountList = array();
		if (0 < $client -> num_rows) {
			foreach ($client -> result() as $info) {
				$tmp = new Clients_model();
				$tmp -> id = $info -> id;
				$tmp -> accounts = $info -> accounts;
				$accountList[$tmp -> id] = $tmp;
			}
		}

		return $accountList;
	}

	function copyAccount2Account($old_id_account, $id_client, $newCopyAccount, $close_date, $open_date)
	{
		$this -> db -> select('*');
		$this -> db -> from('customer_service');
		$this -> db -> where('id_account', $old_id_account);
		$date = new DateTime($open_date);
		$formatted_open_date = $date -> format('Y-m-d');
		$client = $this -> db -> get();
		$accountList = array();
		if (0 < $client -> num_rows) {
			foreach ($client -> result() as $info) {

				$info -> id;
				$new_uniq_id = md5($info -> uniq_id);
				$info -> payment_name;
				$info -> resources;
				$info -> identifier;
				$info -> name;
				$info -> tariffs;
				$info -> datepicker1;
				$info -> end_date;
				$info -> period;


				$data = array(
					'uniq_id' => $new_uniq_id,
					'id_account' => $newCopyAccount,
					'payment_name' => $info -> payment_name,
					'resources' => $info -> resources,
					'identifier' => $info -> identifier,
					'tariffs' => $info -> tariffs,
					'datepicker1' => $formatted_open_date,
					'period' => 'month'
				);

				$this -> db -> insert('customer_service', $data);

				$user = $this -> ion_auth -> user() -> row();
				$mdc = new LoggerMDC();
				$mdc -> put('username', $user -> username);
				$this -> log -> warn('Пользователь скопировал номенклатуру ' . $info -> payment_name . ' на лицевой счет с ID=' . $newCopyAccount . ' клиента с id=' . $id_client . ' указав дату начала ' . $open_date);
				$this -> log -> warn($data);

				if ($this -> db -> affected_rows() == 1):

					$date = new DateTime($close_date);
					$formatted_close_date = $date -> format('Y-m-d');
					if ($info -> end_date == ''):

						$data = array(
							'end_date' => $formatted_close_date,
						);
						$this -> db -> where('id', $info -> id);
						$this -> db -> where('uniq_id', $info -> uniq_id);
						$this -> db -> update('customer_service', $data);

						$this -> log -> warn('Пользователь при копировании номенклатур указал дату окончания ' . $close_date . ' на номенклатуре ' . $info -> payment_name . ' на лицевом счете с ID=' . $old_id_account);

					endif;

				endif;
			}
		}
		return 'Процесс копирования номенклатур успешно завершен.';
	}

	/**
	 * Метод возвращает список ЛС клиента
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return mixed $client_account;
	 *
	 */
	function get_client_accounts($id)
	{
		$id = (int) $id;
		$client_account = array();
		$this -> db -> select('*');
		$this -> db -> from('clients');
		$this -> db -> where('clients.id', $id);
		$this -> db -> join('clients_accounts', 'clients_accounts.id_clients = clients.id');
		$client = $this -> db -> get();
		if (0 < $client -> num_rows) {
			foreach ($client -> result() as $info) {
				$tmp = new Clients_model();
				$tmp -> id = $info -> id;
				$tmp -> id_client = $id;
				$tmp -> bindings_name = $info -> bindings_name;
				$tmp -> accounts = $info -> accounts;
				$tmp -> dateFrom = $info -> dateFrom;
				$tmp -> DateTo = $info -> DateTo;
				$tmp -> client_name = $info -> client_name;
				$tmp -> client_address = $info -> client_address;
				$tmp -> post_client_address = $info -> post_client_address;
				$tmp -> inn = $info -> inn;
				$tmp -> kpp = $info -> kpp;
				$tmp -> account = $info -> account;
				$tmp -> account_date = $info -> account_date;
				$tmp -> client_manager = $info -> client_manager;
				$tmp -> phone_number = $info -> phone_number;
				$tmp -> client_email = $info -> client_email;
				$tmp -> id_service = $info -> id_service;
				$client_account[$tmp -> id] = $tmp;
			}
			return $client_account;
		}
	}

	/**
	 * Метод возвращает список услуг
	 *
	 * @author Ермашевский Денис
	 * @return mixed $service_arr;
	 *
	 */
	function getServiceType()
	{
		$service_arr = array();
		$res = $this -> db -> get('services');
		if (0 < $res -> num_rows) {
			foreach ($res -> result() as $service) {
				$tmp = new Clients_model();
				$tmp -> id = $service -> id;
				$tmp -> service_description = $service -> service_description;
				$tmp -> marker = $service -> marker;
				$service_arr[$tmp -> id] = $tmp;
			}
			return $service_arr;
		}
	}

	/**
	 * Метод добавляет клиента
	 *
	 * @param mixed $data массив данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 *
	 */
	function add_client($data)
	{

		$sql = "INSERT INTO clients (client_name, client_address, post_client_address, account,account_date, inn, kpp, client_manager, phone_number, client_email) VALUES ('" . $data['client_name'] . "','" . $data['client_address'] . "','" . $data['post_client_address'] . "','" . $data['account'] . "','" . date("Y-m-d", strtotime($data['date_account'])) . "','" . $data['inn'] . "','" . $data['kpp'] . "','" . $data['client_manager'] . "','" . $data['phone_number'] . "','" . $data['client_email'] . "')";
		$this -> db -> query($sql);
		$last_id = $this -> db -> insert_id();
		$account_num = $data['account'];
		if (isset($last_id) AND isset($account_num)) {
			$last_id = (int) $last_id;

			$my_arr = array();

			foreach ($data['assortment_selected'] as $markers) {
				$get_marker = 'select id,marker from services where id =' . $markers;
				$query = $this -> db -> query($get_marker);
				array_push($my_arr, $query -> row());
			}
			foreach ($my_arr as $client_accounts) {
				$sql_query = "insert into clients_accounts (`bindings_name`,`accounts`,`id_service`,`id_clients`) values ('" . $data['client_name'] . "','" . $data['account'] . "/" . $client_accounts -> marker . "', '" . $client_accounts -> id . "',$last_id)";
				$this -> db -> query($sql_query);
			}
		}
	}

	/**
	 * Метод добавляет клиента из csv файла
	 *
	 * @param mixed $data массив данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 *
	 */
	function import_client_db($data)
	{
		//Сделать проверку на добавление клиента и на существование ЛС у клиента.Это разные проверки должны быть
		foreach ($data as $data):
			$this -> db -> select('id');
			$this -> db -> from('services');
			$this -> db -> like('marker', $data['id_service']);
			$query = $this -> db -> get();
			$row = $query -> row_array();
			$this -> db -> select('id');
			$this -> db -> from('clients');
			$this -> db -> like('client_name', $data['client_name']);
			$res = $this -> db -> get();
			if (0 == $res -> num_rows) {
				echo $sql = "INSERT INTO clients (client_name, client_address, post_client_address, account,account_date, inn, kpp, client_manager, phone_number, client_email) VALUES ('" . $data['client_name'] . "','" . $data['client_address'] . "','" . $data['post_client_address'] . "','" . $data['account'] . "','" . date("Y-m-d", strtotime($data['date_account'])) . "','" . $data['inn'] . "','" . $data['kpp'] . "','" . $data['client_manager'] . "','" . $data['phone_number'] . "','" . $data['client_email'] . "')";
				$this -> db -> query($sql);
				$last_id = $this -> db -> insert_id();
			} else {
				$last_id = $res -> row('id');
			}
			$this -> db -> select('id');
			$this -> db -> from('clients_accounts');
			$this -> db -> like('accounts', $data['account']);
			$res2 = $this -> db -> get();
			if (0 == $res2 -> num_rows) {
				echo $sql2 = "INSERT INTO clients_accounts (bindings_name, accounts, id_service, id_clients) VALUES ('" . $data['client_name'] . "','" . $data['account'] . "','" . $row['id'] . "','" . $last_id . "')";
				$this -> db -> query($sql2);
				$idaccount = $this -> db -> insert_id();
			} else {
				$idaccount = $res2 -> row('id');
			}

			$this -> db -> select('id');
			$this -> db -> from('service_groups');
			$this -> db -> like('services_groups', $data['services_groups']);
			$id_group = $this -> db -> get();
			if (0 == $id_group -> num_rows) {
				echo $sql3 = "INSERT INTO service_groups (services_groups, id_services) VALUES ('" . $data['services_groups'] . "','" . $row['id'] . "')";
				$this -> db -> query($sql3);
				$idgroup = $this -> db -> insert_id();
			} else {
				$idgroup = $id_group -> row('id');
			}

			$this -> db -> select('id');
			$this -> db -> from('tariffs');
			$this -> db -> where('tariff_name', $data['tariff1']);
			$result1 = $this -> db -> get();
			if (0 < $result1 -> num_rows) {
				echo $tariff1 = $result1 -> row('id');
			} else {
				$tariff1 = '';
			}
			if ( ! empty($data['value1'])) {
				$this -> db -> select('id');
				$this -> db -> from('free_phone_pool');
				$this -> db -> like('resources', $data['value1']);
				$phone_res = $this -> db -> get();

				if (0 < $phone_res -> num_rows) {
					echo $value1 = $phone_res -> row('id');
					$set = array('status' => 'busy');
					$this -> db -> where('id', $value1);
					$this -> db -> update('free_phone_pool', $set);
				}
			} else {
				$value1 = '';
			}

			$token = md5(uniqid(""));
			if ( ! empty($data['assortment_1'])) {
				$sql4 = "INSERT INTO `customer_service` (`uniq_id`,`id_account`,`payment_name`, `resources`, `tariffs`, `datepicker1`,`period`) VALUES ('" . $token . "','" . $idaccount . "','" . $data['assortment_1'] . "', '" . $value1 . "',  '" . $tariff1 . "', '" . date("Y-m-d", strtotime($data['start_date'])) . "','month')";
				$this -> db -> query($sql4);
			}

			$this -> db -> select('id');
			$this -> db -> from('tariffs');
			$this -> db -> where('tariff_name', $data['tariff2']);
			$result2 = $this -> db -> get();
			if (0 < $result2 -> num_rows) {
				echo $tariff2 = $result2 -> row('id');
			} else {
				$tariff2 = '';
			}

			if ( ! empty($data['assortment_2'])) {
				$sql5 = "INSERT INTO `customer_service` (`uniq_id`,`id_account`,`payment_name`, `resources`, `tariffs`, `datepicker1`,`period`) VALUES ('" . $token . "','" . $idaccount . "','" . $data['assortment_2'] . "', '" . $data['value2'] . "',  '" . $tariff2 . "', '" . date("Y-m-d", strtotime($data['start_date'])) . "','month')";
				$this -> db -> query($sql5);
			}

			$this -> db -> select('id');
			$this -> db -> from('tariffs');
			$this -> db -> where('tariff_name', $data['tariff3']);
			$result3 = $this -> db -> get();
			if (0 < $result3 -> num_rows) {
				echo $tariff3 = $result3 -> row('id');
			} else {
				$tariff3 = '';
			}

			if ( ! empty($data['assortment_3'])) {
				$sql6 = "INSERT INTO `customer_service` (`uniq_id`,`id_account`,`payment_name`, `resources`, `tariffs`, `datepicker1`,`period`) VALUES ('" . $token . "','" . $idaccount . "','" . $data['assortment_3'] . "', '" . $data['value3'] . "',  '" . $tariff3 . "', '" . date("Y-m-d", strtotime($data['start_date'])) . "','month')";
				$this -> db -> query($sql6);
			}

			$this -> db -> select('id');
			$this -> db -> from('tariffs');
			$this -> db -> where('tariff_name', $data['tariff4']);
			$result4 = $this -> db -> get();
			if (0 < $result4 -> num_rows) {
				echo $tariff4 = $result4 -> row('id');
			} else {
				$tariff4 = '';
			}

			if ( ! empty($data['assortment_4'])) {
				$sql7 = "INSERT INTO `customer_service` (`uniq_id`,`id_account`,`payment_name`, `resources`, `tariffs`, `datepicker1`,`period`) VALUES ('" . $token . "','" . $idaccount . "','" . $data['assortment_4'] . "', '" . $data['value4'] . "',  '" . $tariff4 . "', '" . date("Y-m-d", strtotime($data['start_date'])) . "','month')";
				$this -> db -> query($sql7);
			}

		endforeach;
	}

	/**
	 * Метод добавляет клиента из csv файла
	 *
	 * @param mixed $data массив данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 *
	 */
	function import_client_db_dn($data)
	{
		//Сделать проверку на добавление клиента и на существование ЛС у клиента.Это разные проверки должны быть
		foreach ($data as $data):
			print_r($data);
			$this -> db -> select('id');
			$this -> db -> from('services');
			$this -> db -> like('marker', $data['id_service']);
			$query = $this -> db -> get();
			$row = $query -> row_array();
			print_r($row);
			$this -> db -> select('id');
			$this -> db -> from('clients');
			$this -> db -> like('client_name', $data['client_name']);
			$res = $this -> db -> get();
			if (0 == $res -> num_rows) {
				echo $sql = "INSERT INTO clients (client_name, client_address, post_client_address, account,account_date, inn, kpp, client_manager, phone_number, client_email) VALUES ('" . $data['client_name'] . "','" . $data['client_address'] . "','" . $data['post_client_address'] . "','" . $data['account'] . "','" . date("Y-m-d", strtotime($data['date_account'])) . "','" . $data['inn'] . "','" . $data['kpp'] . "','" . $data['client_manager'] . "','" . $data['phone_number'] . "','" . $data['client_email'] . "')";
				$this -> db -> query($sql);
				$last_id = $this -> db -> insert_id();
			} else {
				$last_id = $res -> row('id');
			}
			$this -> db -> select('id');
			$this -> db -> from('clients_accounts');
			$this -> db -> like('accounts', $data['account']);
			$res2 = $this -> db -> get();
			if (0 == $res2 -> num_rows) {
				echo $sql2 = "INSERT INTO clients_accounts (bindings_name, accounts, id_service, id_clients) VALUES ('" . $data['client_name'] . "','" . $data['account'] . "','" . $row['id'] . "','" . $last_id . "')";
				$this -> db -> query($sql2);
				$idaccount = $this -> db -> insert_id();
			} else {
				$idaccount = $res2 -> row('id');
			}

			$this -> db -> select('id');
			$this -> db -> from('service_groups');
			$this -> db -> like('services_groups', $data['services_groups']);
			$id_group = $this -> db -> get();
			if (0 == $id_group -> num_rows) {
				echo $sql3 = "INSERT INTO service_groups (services_groups, id_services) VALUES ('" . $data['services_groups'] . "','" . $row['id'] . "')";
				$this -> db -> query($sql3);
				$idgroup = $this -> db -> insert_id();
			} else {
				$idgroup = $id_group -> row('id');
			}

			$this -> db -> select('id');
			$this -> db -> from('tariffs');
			$this -> db -> where('tariff_name', $data['tariff1']);
			$result1 = $this -> db -> get();
			if (0 < $result1 -> num_rows) {
				echo $tariff1 = $result1 -> row('id');
			} else {
				$tariff1 = '';
			}
			if ( ! empty($data['value1'])) {
				$this -> db -> select('id');
				$this -> db -> from('free_phone_pool');
				$this -> db -> like('resources', $data['value1']);
				$phone_res = $this -> db -> get();

				if (0 < $phone_res -> num_rows) {
					echo $value1 = $phone_res -> row('id');
					$set = array('status' => 'busy');
					$this -> db -> where('id', $value1);
					$this -> db -> update('free_phone_pool', $set);
				}
			} else {
				$value1 = '';
			}

			$token = md5(uniqid(""));
			if ( ! empty($data['assortment_1'])) {
				$sql4 = "INSERT INTO `customer_service` (`uniq_id`,`id_account`,`payment_name`, `resources`, `tariffs`, `datepicker1`,`period`) VALUES ('" . $token . "', '" . $idaccount . "','" . $data['assortment_1'] . "', '" . $value1 . "',  '" . $tariff1 . "', '" . date("Y-m-d", strtotime($data['start_date'])) . "','month')";
				$this -> db -> query($sql4);
			}

			$this -> db -> select('id');
			$this -> db -> from('tariffs');
			$this -> db -> where('tariff_name', $data['tariff2']);
			$result2 = $this -> db -> get();
			if (0 < $result2 -> num_rows) {
				echo $tariff2 = $result2 -> row('id');
			} else {
				$tariff2 = '';
			}

			if ( ! empty($data['assortment_2']) & ! empty($data['line_count'])) {
				$sql5 = "INSERT INTO `customer_service` (`uniq_id`,`id_account`,`payment_name`, `resources`, `tariffs`, `datepicker1`,`period`) VALUES ('" . $token . "', '" . $idaccount . "','" . $data['assortment_2'] . "', '" . $data['value2'] . "',  '" . $tariff2 . "', '" . date("Y-m-d", strtotime($data['start_date'])) . "','month')";
				for ($i = 0; $i < $data['line_count']; $i ++ ) {
					$this -> db -> query($sql5);
				}
			}

		endforeach;
	}

	/**
	 * Метод добавляет клиента из csv файла
	 *
	 * @param mixed $data массив данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 *
	 */
	function import_client_db_tk($data)
	{
		//Сделать проверку на добавление клиента и на существование ЛС у клиента.Это разные проверки должны быть
		foreach ($data as $data):
			print_r($data);
			$this -> db -> select('id');
			$this -> db -> from('services');
			$this -> db -> like('marker', $data['id_service']);
			$query = $this -> db -> get();
			$row = $query -> row_array();
			print_r($row);
			$this -> db -> select('id');
			$this -> db -> from('clients');
			$this -> db -> like('client_name', $data['client_name']);
			$res = $this -> db -> get();
			if (0 == $res -> num_rows) {
				echo $sql = "INSERT INTO clients (client_name, client_address, post_client_address, account,account_date, inn, kpp, client_manager, phone_number, client_email) VALUES ('" . $data['client_name'] . "','" . $data['client_address'] . "','" . $data['post_client_address'] . "','" . $data['account'] . "','" . date("Y-m-d", strtotime($data['date_account'])) . "','" . $data['inn'] . "','" . $data['kpp'] . "','" . $data['client_manager'] . "','" . $data['phone_number'] . "','" . $data['client_email'] . "')";
				$this -> db -> query($sql);
				$last_id = $this -> db -> insert_id();
			} else {
				$last_id = $res -> row('id');
			}
			$this -> db -> select('id');
			$this -> db -> from('clients_accounts');
			$this -> db -> like('accounts', $data['account']);
			$res2 = $this -> db -> get();
			if (0 == $res2 -> num_rows) {
				echo $sql2 = "INSERT INTO clients_accounts (bindings_name, accounts, id_service, id_clients) VALUES ('" . $data['client_name'] . "','" . $data['account'] . "','" . $row['id'] . "','" . $last_id . "')";
				$this -> db -> query($sql2);
				$idaccount = $this -> db -> insert_id();
			} else {
				$idaccount = $res2 -> row('id');
			}

			$this -> db -> select('id');
			$this -> db -> from('service_groups');
			$this -> db -> like('services_groups', $data['services_groups']);
			$id_group = $this -> db -> get();
			if (0 == $id_group -> num_rows) {
				echo $sql3 = "INSERT INTO service_groups (services_groups, id_services) VALUES ('" . $data['services_groups'] . "','" . $row['id'] . "')";
				$this -> db -> query($sql3);
				$idgroup = $this -> db -> insert_id();
			} else {
				$idgroup = $id_group -> row('id');
			}

			$this -> db -> select('id');
			$this -> db -> from('tariffs');
			$this -> db -> where('tariff_name', $data['tariff1']);
			$result1 = $this -> db -> get();
			if (0 < $result1 -> num_rows) {
				echo $tariff1 = $result1 -> row('id');
			} else {
				$tariff1 = '';
			}
//			if(!empty($data['value1'])){
//			$this -> db -> select('id');
//			$this -> db -> from('free_phone_pool');
//			$this -> db -> like('resources', $data['value1']);
//			$phone_res = $this -> db -> get();
//
//			if (0 < $phone_res -> num_rows) {
//				echo $value1 = $phone_res->row('id');
//				$set=array('status'=>'busy');
//				$this->db->where('id', $value1);
//				$this->db->update('free_phone_pool', $set);
//
//			}
//			}else{
//				$value1='';
//			}

			$token = md5(uniqid(""));
			if ( ! empty($data['assortment_1']) & ($data['assortment_2'] != "Повременный")) {
				$sql4 = "INSERT INTO `customer_service` (`uniq_id`,`id_account`,`payment_name`, `identifier`, `tariffs`, `datepicker1`,`period`) VALUES ('" . $token . "', '" . $idaccount . "','" . $data['assortment_1'] . "', '" . $data['value1'] . "',  '" . $tariff1 . "', '" . date("Y-m-d", strtotime($data['start_date'])) . "','month')";
				$this -> db -> query($sql4);
				echo $sql4;
			}

			$this -> db -> select('id');
			$this -> db -> from('tariffs');
			$this -> db -> where('tariff_name', $data['tariff2']);
			$result2 = $this -> db -> get();
			if (0 < $result2 -> num_rows) {
				echo $tariff2 = $result2 -> row('id');
			} else {
				$tariff2 = '';
			}

			if (($data['assortment_2'] != "Повременный") & ! empty($data['line_count'])) {
				$sql5 = "INSERT INTO `customer_service` (`uniq_id`,`id_account`,`payment_name`, `resources`, `tariffs`, `datepicker1`,`period`) VALUES ('" . $token . "','" . $idaccount . "','" . $data['assortment_2'] . "', '" . $data['value2'] . "',  '" . $tariff2 . "', '" . date("Y-m-d", strtotime($data['start_date'])) . "','month')";
				for ($i = 0; $i < $data['line_count']; $i ++ ) {
					$this -> db -> query($sql5);
					echo $sql5;
				}
			}

			if ( ! empty($data['assortment_3'])) {
				$sql6 = "INSERT INTO `customer_service` (`uniq_id`,`id_account`,`payment_name`, `identifier`, `tariffs`, `datepicker1`,`period`) VALUES ('" . $token . "', '" . $idaccount . "','" . $data['assortment_3'] . "', '" . $data['value3'] . "',  '" . $tariff3 . "', '" . date("Y-m-d", strtotime($data['start_date'])) . "','month')";
				//for($i=0; $i<$data['line_count']; $i++){
				$this -> db -> query($sql6);
				//}
			}

		endforeach;
	}

	/**
	 * Метод возвращает список групп услуг
	 *
	 * @param int $id идентификатор группы
	 *
	 * @author Ермашевский Денис
	 * @return mixed $service_group;
	 *
	 */
	function getServiceGroup($id)
	{
		$service_group = array();
		$this -> db -> select('*');
		$this -> db -> from('service_groups');
		$this -> db -> where('service_groups.id_services', $id);
		$res = $this -> db -> get();
		if (0 < $res -> num_rows) {
			$n = 1;
			foreach ($res -> result() as $service) {
				$tmp = new Clients_model();
				$tmp -> id = $service -> id;
				$tmp -> services_groups = $service -> services_groups;
				$tmp -> marker_service = $service -> marker_service;
				$service_group[$n ++] = $tmp;
			}
			return $service_group;
		}
	}

	/**
	 * Метод возвращает динамически построенную форму
	 *
	 * @param int $id идентификатор группы
	 *
	 * @author Ермашевский Денис
	 * @return mixed $elements_arr;
	 *
	 */
	function getElementsForm($id)
	{
		echo $id;
		$elements_arr = array();
		$this -> db -> select('service_groups.id, service_groups.services_groups, assortment.id as assortment_id, payment_name, payment_type, element_type, target, default_value,type_resources,tariff');
		$this -> db -> from('assortment');
		$this -> db -> join('group_assortment_link', 'group_assortment_link.id_assortments = assortment.id', 'inner');
		$this -> db -> join('service_groups', 'service_groups.id = group_assortment_link.id_group_service', 'inner');
		$this -> db -> join('services', 'services.id = service_groups.id_services', 'inner');
		$this -> db -> where('service_groups.id', $id);
		$typeElements = $this -> db -> get();
		if (0 < $typeElements -> num_rows) {
			$n = 1;
			foreach ($typeElements -> result() as $service) {
				$tmp = new Clients_model();
				$tmp -> id = $service -> id;
				$tmp -> assortment_id = $service -> assortment_id;
				$tmp -> payment_name = $service -> payment_name;
				$tmp -> payment_type = $service -> payment_type;
				$tmp -> element_type = $service -> element_type;
				$tmp -> target = $service -> target;
				$tmp -> type_resources = $service -> type_resources;
				$tmp -> default_value = $service -> default_value;
				$tmp -> tariff = $service -> tariff;
				$tmp -> services_groups = $service -> services_groups;
				$elements_arr[$n ++] = $tmp;
			}
			return $elements_arr;
		}
	}

	/**
	 * Метод возвращает ресурсы из БД
	 *
	 * @param string $table          таблица ресурсов в БД
	 * @param string $type_resources тип ресурса запрашиваемый из таблицы БД
	 *
	 * @author Ермашевский Денис
	 * @return array $phones_arr;
	 */
	function getResources($table, $type_resources = NULL)
	{
		$phones_arr = array();
		$this -> db -> select('*');
		$this -> db -> from($table);
		$this -> db -> where('type', $type_resources);
		//$this -> db -> where('status', 'free');
		$this -> db -> where('date < ', date("Y-m-d", now()));
		$this -> db -> where('id_client', '191');
		$res = $this -> db -> get();
		if (0 < $res -> num_rows) {
			foreach ($res -> result() as $phoneList) {
				$tmp = new Clients_model();
				$tmp -> id = $phoneList -> id;
				$tmp -> resources = $phoneList -> resources;
				$tmp -> type = $phoneList -> type;
				$phones_arr[$tmp -> id] = $tmp;
			}
			return $phones_arr;
		}
	}

	/**
	 * Метод возвращает список тарифов
	 *
	 * @param string $table таблица тарифов в БД
	 *
	 * @author Ермашевский Денис
	 * @return array $tariffs_arr;
	 */
	function getTariffs($table)
	{
		$res = $this -> db -> get($table);
		if (0 < $res -> num_rows) {
			$tariffs_arr = array();
			foreach ($res -> result() as $tariffsList) {
				$tmp = new Clients_model();
				$tmp -> id = $tariffsList -> id;
				$tmp -> tariff_name = $tariffsList -> tariff_name;
				$tmp -> price = $tariffsList -> price;
				$tariffs_arr[$tmp -> id] = $tmp;
			}
		}
		return $tariffs_arr;
	}

	/**
	 * Метод возвращает тариф по идентификатору
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return array $tariff_list;
	 */
	function getTariffById($id)
	{

		$this -> db -> select('*');
		$this -> db -> from('tariffs');
		$this -> db -> where('id_assortment', $id);
		$tariff = $this -> db -> get();
		if (0 < $tariff -> num_rows) {
			$tariff_list = array();
			foreach ($tariff -> result() as $list) {
				$gettingTarif = new Clients_model();
				$gettingTarif -> id = $list -> id;
				$gettingTarif -> tariff_name = $list -> tariff_name;
				$gettingTarif -> price = $list -> price;
				$tariff_list[$gettingTarif -> id] = $gettingTarif;
			}
		}
		return $tariff_list;
	}

	/**
	 * Метод добавления отдельной номеклатуры в группу на ЛС клиента
	 *
	 * @param array $data массив данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 *
	 */
	function add_assortment_item($data)
	{

		if ($data['end_date'] == '') {
			$sql = "INSERT INTO `customer_service` (`uniq_id`, `id_account`,`payment_name`, `resources`, `identifier`, `name`, `tariffs`, `datepicker1`,`period`,`end_date`) VALUES ('" . $data['uniq_id'] . "','" . $data['id_account'] . "','" . $data['payment_name'] . "', '" . $data['resources'] . "',  '" . $data['identifier'] . "', '" . $data['name'] . "',  '" . $data['tariff'] . "', '" . date("Y-m-d", strtotime($data['datepicker1'])) . "','" . $data['period'] . "', NULL)";
			$this -> db -> query($sql);
		} else {
			$sql = "INSERT INTO `customer_service` (`uniq_id`, `id_account`,`payment_name`, `resources`, `identifier`, `name`, `tariffs`, `datepicker1`,`end_date`,`period`) VALUES ('" . $data['uniq_id'] . "','" . $data['id_account'] . "','" . $data['payment_name'] . "', '" . $data['resources'] . "', '" . $data['identifier'] . "', '" . $data['name'] . "',  '" . $data['tariff'] . "', '" . date("Y-m-d", strtotime($data['datepicker1'])) . "','" . date("Y-m-d", strtotime($data['end_date'])) . "','" . $data['period'] . "')";
			$this -> db -> query($sql);
		}
	}

	/**
	 * Метод добавления услуги на ЛС клиента
	 *
	 * @param array $data массив данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 *
	 */
	function add_service_client($data)
	{
		$token = md5(uniqid(""));

		for ($i = 1; $i <= $data['counter']; $i ++) {
			if ($data['period'][$i] === 'single_payment') {
				$sql = "INSERT INTO `customer_service` (`uniq_id`,`id_account`,`payment_name`, `resources`, `identifier`, `tariffs`, `datepicker1`,`end_date`,`period`) VALUES ('" . $token . "', '" . $data['id_account'] . "','" . $data['payment_name'][$i] . "', '" . $data['resources'][$i] . "',  '" . $data['name'][$i] . "',  '" . $data['tariff'][$i] . "', '" . date("Y-m-d", strtotime($data['datepicker1'])) . "','" . date("Y-m-d", strtotime($data['datepicker1'])) . "','" . $data['period'][$i] . "')";
				$this -> db -> query($sql);
			} else {
				$sql = "INSERT INTO `customer_service` (`uniq_id`,`id_account`,`payment_name`, `resources`, `identifier`, `tariffs`, `datepicker1`,`period`) VALUES ('$token', '" . $data['id_account'] . "','" . $data['payment_name'][$i] . "', '" . $data['resources'][$i] . "',  '" . $data['name'][$i] . "',  '" . $data['tariff'][$i] . "', '" . date("Y-m-d", strtotime($data['datepicker1'])) . "','" . $data['period'][$i] . "')";
				$this -> db -> query($sql);
			}
		}
	}

	/**
	 * Список групп на ЛС клиента
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return mixed $customer_group;
	 *
	 */
	function getCustomerGroupById($id)
	{
		$customer_group = array();
		$this -> db -> select('*');
		$this -> db -> from('customer_service');
		$this -> db -> join('service_groups', 'service_groups.id = customer_service.id_group', 'inner');
		$this -> db -> join('free_phone_pool', 'free_phone_pool.id = customer_service.resources', 'left');
		$this -> db -> join('clients_accounts', 'clients_accounts.id = customer_service.id_account');
		$this -> db -> where('customer_service.id_account', $id);
		$this -> db -> group_by('customer_service.uniq_id');
		$this -> db -> order_by('customer_service.id_group');
		$res = $this -> db -> get();
		if (0 < $res -> num_rows) {
			$n = 1;
			foreach ($res -> result() as $customer_service) {
				$tmp = new Clients_model();
				$tmp -> id = $customer_service -> id;
				$tmp -> id_group = $customer_service -> id_group;
				$tmp -> id_account = $customer_service -> id_account;
				$tmp -> accounts = $customer_service -> accounts;
				$tmp -> uniq_id = $customer_service -> uniq_id;
				$tmp -> payment_name = $customer_service -> payment_name;
				$tmp -> resources = $customer_service -> resources;
				$tmp -> services_groups = $customer_service -> services_groups;
				$tmp -> id_services = $customer_service -> id_services;
				$customer_group[$n ++] = $tmp;
			}
			return $customer_group;
		}
	}

	/**
	 * Список номеклатур по идентификатору услуги
	 *
	 * @param int $id идентификатор услуги
	 *
	 * @author Ермашевский Денис
	 * @return mixed $assortments;
	 *
	 */
	function getAssortmnentsByService($id)
	{
		$id = (int) $id;
		$assortments = array();
		$this -> db -> select('assortment.id,payment_name');
		$this -> db -> from('assortment');
		$this -> db -> join('services', 'services.marker = assortment.marker_service', 'inner');
		$this -> db -> where('services.id', $id);
		$res = $this -> db -> get();
		if (0 < $res -> num_rows) {
			$n = 1;
			foreach ($res -> result() as $row) {
				$tmp = new Clients_model();
				$tmp -> id = $row -> id;
				$tmp -> payment_name = $row -> payment_name;
				$assortments[$n ++] = $tmp;
			}
			return $assortments;
		}
	}

	/**
	 * Данные клиента по идентификатору
	 *
	 * @param int $id идентификатор клиента
	 *
	 * @author Ермашевский Денис
	 * @return mixed $client_data_edit;
	 *
	 */
	function getClientsById($id)
	{
		$this -> db -> where('id', $id);
		$res = $this -> db -> get('clients');
		if (0 < $res -> num_rows) {
			$n = 1;
			$client_data_edit = array();
			foreach ($res -> result() as $row) {
				$tmp = new Clients_model();
				$tmp -> id = $row -> id;
				$tmp -> client_name = $row -> client_name;
				$tmp -> client_address = $row -> client_address;
				$tmp -> account = $row -> account;
				$tmp -> account_date = date('d.m.Y', strtotime($row -> account_date));
				$tmp -> inn = $row -> inn;
				$tmp -> client_manager = $row -> client_manager;
				$tmp -> phone_number = $row -> phone_number;
				$tmp -> client_email = $row -> client_email;
				$tmp -> post_client_address = $row -> post_client_address;
				$tmp -> kpp = $row -> kpp;
				$client_data_edit[$n ++] = $tmp;
			}
			return $client_data_edit;
		}
	}

	/**
	 * Редактирование клиента клиента
	 *
	 * @param int $data массив даннх
	 *
	 * @author Ермашевский Денис
	 * @return null
	 *
	 */
	function editClientInfo($data)
	{
		$this -> db -> where('id', $data['id']);
		$this -> db -> update('clients', $data);
	}

	/**
	 * Добавление ЛС клиенту
	 *
	 * @param array $data массив данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 *
	 */
	function add_client_account_item($data)
	{
		$this -> db -> select('marker');
		$this -> db -> from('services');
		$this -> db -> where('id', $data['id_service']);
		$res = $this -> db -> get();
		if (0 < $res -> num_rows) {

			foreach ($res -> result() as $row) {
				$row -> marker;
			}
			$sql_query = "insert into clients_accounts (`bindings_name`,`accounts`,`id_service`,`id_clients`) values ('" . $data['client_name'] . "','" . $data['account'] . "/" . $row -> marker . "', '" . $data['id_service'] . "','" . $data['id_client'] . "')";
			$this -> db -> query($sql_query);
		}
	}

	/**
	 * Добавление ЛС клиенту
	 *
	 * @param array $data массив данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 *
	 */
	function add_client_account_item2($data)
	{
		$this -> db -> select('marker');
		$this -> db -> from('services');
		$this -> db -> where('id', $data['id_service']);
		$res = $this -> db -> get();
		if (0 < $res -> num_rows) {

			foreach ($res -> result() as $row) {
				$row -> marker;
			}
			$sql_query = "insert into clients_accounts (`bindings_name`,`accounts`,`id_service`,`id_clients`) values ('" . $data['client_name'] . "','" . $data['account'] . "', '" . $data['id_service'] . "','" . $data['id_client'] . "')";
			$this -> db -> query($sql_query);
		}
	}

	/**
	 * Список начислений по номеклатуре
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return mixed $customer_payments;
	 *
	 */
	function getCustomerPayments($id)
	{
		$this -> db -> select("id, id_assortment_customer,id_account, amount, period_start, period_end");
		$this -> db -> from('customer_payments');
		$this -> db -> where('id_assortment_customer', $id);
		$this -> db -> order_by("period_start", "asc");
		$res = $this -> db -> get();
		$customer_payments = array();
		$n = 1;
		foreach ($res -> result() as $rows) {
			$tmp = new Clients_model();
			$tmp -> id = $rows -> id;
			$tmp -> id_assortment_customer = $rows -> id_assortment_customer;
			//$tmp -> id_group = $rows -> id_group;
			$tmp -> id_account = $rows -> id_account;
			$tmp -> amount = $rows -> amount;

			$tmp -> period_start = date("d.m.Y", strtotime($rows -> period_start));
			$tmp -> period_end = date("d.m.Y", strtotime($rows -> period_end));
			$customer_payments[$n ++] = $tmp;
		}
		return $customer_payments;
	}

	/**
	 * Список дат по услугам клиента
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return mixed $customer_services;
	 *
	 */
	function getCustomerServices($id)
	{
		$this -> db -> select('datepicker1,end_date');
		$this -> db -> from('customer_service');
		$this -> db -> where('id', $id);

		$res = $this -> db -> get();
		$customer_services = array();
		$n = 1;
		foreach ($res -> result() as $rows) {
			$tmp = new Clients_model();

			$tmp -> datepicker1 = date("d.m.Y", strtotime($rows -> datepicker1));

			if ($rows -> end_date == NULL) {
				$tmp -> end_date = '';
			} else {
				$tmp -> end_date = date("d.m.Y", strtotime($rows -> end_date));
			}
			$customer_services[$n ++] = $tmp;
		}
		return $customer_services;
	}

	function updateEndDate($id, $end_date, $datepicker)
	{

		if ($end_date == '') {
			$date = NULL;
		} else {
			$date = date("Y-m-d", strtotime($end_date));
		}
		$this -> db -> where('id', $id);
		$this -> db -> from('customer_service');
		$this -> db -> select('resources');
		$check_res = $this -> db -> get();
		foreach ($check_res -> result() as $rows) {
			if ( ! is_null($rows -> resources) && $rows -> resources != 'нет') {
				echo "Это ресурсная номенклатура";
				if ($end_date == '') {
					$date = '3000-01-01';
					$this -> db -> where('id', $rows -> resources);
					$this -> db -> set('date', $date);
					$this -> db -> update('free_phone_pool');


					$datepicker1 = date("Y-m-d", strtotime($datepicker));

					$this -> db -> where('id', $id);
					$this -> db -> set('end_date', NULL);
					$this -> db -> set('datepicker1', $datepicker1);
					$this -> db -> update('customer_service');
				} else {
					$this -> db -> where('id', $rows -> resources);
					$this -> db -> set('date', $date);
					$this -> db -> update('free_phone_pool');


					$datepicker1 = date("Y-m-d", strtotime($datepicker));

					$this -> db -> where('id', $id);
					$this -> db -> set('end_date', $date);
					$this -> db -> set('datepicker1', $datepicker1);
					$this -> db -> update('customer_service');
				}
			} else {
				echo "Это не ресурсная номенклатура";

				$datepicker1 = date("Y-m-d", strtotime($datepicker));

				$this -> db -> where('id', $id);
				$this -> db -> set('end_date', $date);
				$this -> db -> set('datepicker1', $datepicker1);
				$this -> db -> update('customer_service');
			}
		}
	}

	/**
	 * Метод возвращает по уникальному индентификатору группы (номенклатур)
	 * начисления по каждой номенклатуре входящей в эту группу (работа с БД)
	 *
	 * @param string $uniq_id уникальный идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return mixed $myarray;
	 *
	 */
	function getAccrualsInGroup($uniq_id)
	{

		$this -> db -> select('customer_service.id, customer_service.payment_name, customer_payments.amount, customer_payments.period_start, customer_payments.period_end, customer_service.period');
		$this -> db -> from('customer_payments');
		$this -> db -> join('customer_service', 'customer_service.id = customer_payments.id_assortment_customer');
		$this -> db -> where('customer_service.uniq_id', $uniq_id);
		$this -> db -> order_by('period_start', 'asc');
		$res = $this -> db -> get();

		$myarray = array();
		$n = 1;
		foreach ($res -> result() as $rows) {
			$tmp = new Clients_model();

			$tmp -> id = $rows -> id;
			$tmp -> payment_name = $rows -> payment_name;
			$tmp -> amount = $rows -> amount;
			$tmp -> period_start = date("d.m.Y", strtotime($rows -> period_start));
			$tmp -> period_end = date("d.m.Y", strtotime($rows -> period_end));
			$tmp -> period = $rows -> period;

			$myarray[$n ++] = $tmp;
		}

		return $myarray;
	}

	/**
	 * Метод устанавливает статус ресурсам
	 *
	 * @param int    $id     идентификатор
	 * @param string $status статус
	 *
	 * @author Ермашевский Денис
	 * @return null
	 *
	 */
	function setResourceStatus($id, $status)
	{

		$sql = 'UPDATE free_phone_pool
	SET status = "' . $status . '", date = "3000-01-01" WHERE id =' . $id;
		$this -> db -> query($sql);
	}

}

//End of file clients_model.php
//Location: ./models/clients_model.php