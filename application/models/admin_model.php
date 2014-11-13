<?php

/**
 * Admin_Models
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Models.Admin_Models
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://www.ci2.lcl/
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Kaliningrad');

/**
 * Класс Admin содержит методы админки
 *
 * @category PHP
 * @package  Models.Admin_Models
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Admin_model extends CI_Model
{

	var $payment_name;

	/**
	 * Унифицированный метод-конструктор __construct()
	 *
	 * @author Ермашевский Денис
	 */
	function __construct()
	{
		parent::__construct();
		$this -> load -> helper('file');
		$this -> load -> helper('date');
	}

	/**
	 * Метод получения логов
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	public function getLogs()
	{

		$this -> db -> select('*');
		$this -> db -> from('log4php_log');
		$res = $this -> db -> get();

		$data = array();
		if (0 < $res -> num_rows) {

			foreach ($res -> result() as $row) {
				$admin = new Admin_model();
				$admin -> id = $row -> id;
				$admin -> timestamp = $row -> timestamp;
				$admin -> logger = $row -> logger;
				$admin -> level = $row -> level;
				$admin -> method = $row -> method;
				$admin -> user = $row -> user;
				$admin -> message = $row -> message;
				$admin -> thread = $row -> thread;
				$admin -> file = $row -> file;
				$admin -> line = $row -> line;
				$data[$admin -> id] = $admin;
			}
		}
		return $data;
	}

	public function getServiceType()
	{
		$servicesType = array();
		$this -> db -> select('id, service_description,marker');
		$this -> db -> from('services');
		$services = $this -> db -> get();
		if (0 < $services -> num_rows) {
			foreach ($services -> result() as $fields) {
				$tmp = new Admin_model();
				$tmp -> id = $fields -> id;
				$tmp -> service_description = $fields -> service_description;
				$tmp -> marker = $fields -> marker;
				$servicesType[$tmp -> id] = $tmp;
			}
		}
		return $servicesType;
	}

	public function linkedSelects()
	{
		switch ($_POST['action']) {

			case "showAssortmentList":



				$this -> db -> select('id, payment_name');
				$this -> db -> from('assortment');
				$this -> db -> like('marker_service', $_POST['marker']);
				$services = $this -> db -> get();
				if (0 < $services -> num_rows) {
					echo '<select size="1" name="assortmentList" onchange="javascript:selectCity();">';
					foreach ($services -> result() as $numRow => $row) {
						echo '<option value="' . $row -> id . '">' . $row -> payment_name . '</option>';
					};
					echo '</select>';
				}

				break;

			case "showTariffList":

				$this -> db -> select('id, tariff_name, price, id_assortment');
				$this -> db -> from('tariffs');
				$this -> db -> where('id_assortment', $_POST['id_assortment']);
				$services = $this -> db -> get();
				if (0 < $services -> num_rows) {
					echo '<select size="1" name="tariffList">';
					foreach ($services -> result() as $numRow => $row) {
						echo '<option value="' . $row -> id . '">' . $row -> tariff_name . '</option>';
					};
					echo '</select>';
				} else {
					echo "Тарифные планы для данной номенклатуры отсутствуют";
				}

				break;

			case "searchTariff":

				$searchResult = array();

				$this -> db -> select('*');
				$this -> db -> from('customer_service');
				$this -> db -> where('tariffs', $_POST['id_tariff']);
				$this -> db -> where('end_date', null, FALSE);
				$services = $this -> db -> get();
				if (0 < $services -> num_rows) {

					foreach ($services -> result() as $numRow => $row) {
						$tmp = new Admin_model();
						$tmp -> id = $row -> id;
						$tmp -> uniq_id = $row -> uniq_id;
						$tmp -> id_account = $row -> id_account;
						$tmp -> payment_name = $row -> payment_name;
						$tmp -> resources = $row -> resources;
						$tmp -> identifier = $row -> identifier;
						$tmp -> name = $row -> name;
						$tmp -> tariffs = $row -> tariffs;
						$tmp -> datepicker1 = $row -> datepicker1;
						$tmp -> end_date = $row -> end_date;
						$tmp -> period = $row -> period;
						$searchResult[$tmp -> id] = $tmp;
					}

					echo json_encode($searchResult);
				} else {
					echo "Данный тариф не используется на лицевых счетах клиентов";
				}

				break;
		}
	}

	function updateEndDateForCustomerAssortments($id, $modify_end_date)
	{
		$this -> db -> where('id', $id);
		$this -> db -> set('end_date', $modify_end_date);
		$this -> db -> update('customer_service');
	}

	function insertRow($uniq_id, $id_account, $payment_name, $resources, $identifier, $tariffs, $new_date, $period)
	{
		$date = new DateTime($new_date);
		$modify_new_date = $date -> format('Y-m-d');

		$data = array(
			'uniq_id' => $uniq_id,
			'id_account' => $id_account,
			'payment_name' => $payment_name,
			'resources' => $resources,
			'identifier' => $identifier,
			'tariffs' => $tariffs,
			'datepicker1' => $modify_new_date,
			'period' => $period,
		);

		$this -> db -> insert('customer_service', $data);
	}

	/**
	 * Метод возвращает список услуг
	 *
	 * @author Ермашевский Денис
	 * @return mixed $service_arr;
	 *
	 */
	function getServiceTypes()
	{
		$service_arr = array();
		$res = $this -> db -> get('services');
		if (0 < $res -> num_rows) {
			foreach ($res -> result() as $service) {
				$tmp = new Admin_model();
				$tmp -> id = $service -> id;
				$tmp -> service_description = $service -> service_description;
				$tmp -> marker = $service -> marker;
				$service_arr[$tmp -> id] = $tmp;
			}
			return $service_arr;
		}
	}

	function buildReport($month, $id_service)
	{
		$checkbox = filter_input(INPUT_POST, 'checkbox');
		$reportArray = array();
		if ( ! in_array(8, $id_service)) {
			$createDate = DateTime::createFromFormat('m', $month);
			$start_date_period = $createDate -> format('Y-m-01');
			$end_date_period = $createDate -> format('Y-m-t');

			$this -> db -> select('customer_payments.id, clients_accounts.bindings_name, inn, accounts , SUM( amount ) AS summ, payment_name, amount as price,COUNT( payment_name ) AS counter, id_service');
			$this -> db -> from('customer_payments');
			$this -> db -> join('clients_accounts', 'clients_accounts.id =  customer_payments.id_account', 'inner');
			$this -> db -> join('clients', 'clients_accounts.id_clients =  clients.id', 'inner');
			$this -> db -> join('customer_service', 'customer_service.id =  customer_payments.id_assortment_customer', 'inner');
			$this -> db -> where('customer_payments.period_start between "' . date($start_date_period) . '" and "' . date($end_date_period) . '"');
			$this -> db -> where('customer_payments.period_end between "' . date($start_date_period) . '" and "' . date($end_date_period) . '"');
			$this -> db -> where('clients_accounts.id_service !=', 8);
			$this -> db -> where_in('clients_accounts.id_service', $id_service);
			$this -> db -> group_by('customer_payments.id_account');
			$this -> db -> group_by('customer_payments.amount');
			$this -> db -> group_by('clients_accounts.bindings_name');
			$this -> db -> group_by('customer_service.payment_name');

			$report_rows = $this -> db -> get();
		} else {
			$createDate = DateTime::createFromFormat('m', $month);
			$start_date_period = $createDate -> format('Y-m-01');
			$end_date_period = $createDate -> format('Y-m-t');
			$this -> db -> select('customer_payments.id, clients_accounts.bindings_name, inn, accounts, SUM( customer_payments.amount ) AS price, IFNULL(round(SUM(REPLACE( customer_discounts.amount, ",","." )),2),"00.00") as discount, id_service', FALSE);
			$this -> db -> from('customer_payments');
			$this -> db -> join('clients_accounts', 'clients_accounts.id =  customer_payments.id_account', 'left');
			$this -> db -> join('clients', 'clients_accounts.id_clients =  clients.id', 'inner');
			$this -> db -> join('customer_discounts', 'customer_discounts.id_account =  customer_payments.id_account', 'left');
			$this -> db -> join('customer_service', 'customer_service.id =  customer_payments.id_assortment_customer', 'inner');
			$this -> db -> where_in('clients_accounts.id_service', $id_service);
			$this -> db -> where('customer_payments.period_start between "' . date($start_date_period) . '" and "' . date($end_date_period) . '"');
			$this -> db -> where('customer_payments.period_end between "' . date($start_date_period) . '" and "' . date($end_date_period) . '"');
			$this -> db -> group_by('customer_payments.id_account');
			$this -> db -> group_by('customer_payments.amount');
			$this -> db -> group_by('clients_accounts.bindings_name');

			$report_rows = $this -> db -> get();
		}
		if ($checkbox == 'true') {

			if (0 < $report_rows -> num_rows) {

				foreach ($report_rows -> result() as $row) {

					if (empty($row -> payment_name)) {
						$tmp = new Admin_model();
						$tmp -> id = $row -> id;
						$tmp -> accounts = $row -> accounts;
						$tmp -> bindings_name = $row -> bindings_name;
						$tmp -> inn = $row -> inn;
						$tmp -> payment_name = 'Услуги связи';
						$tmp -> counter = 1;
						$tmp -> price = $row -> price - $row -> discount;
						$tmp -> summ = $row -> price - $row -> discount;
						$reportArray[$tmp -> id] = $tmp;
					} else {
						$tmp = new Admin_model();
						$tmp -> id = $row -> id;
						$tmp -> accounts = $row -> accounts;
						$tmp -> inn = $row -> inn;
						$tmp -> payment_name = $row -> payment_name;
						$tmp -> bindings_name = $row -> bindings_name;
						$tmp -> counter = $row -> counter;
						$tmp -> price = $row -> price;
						$tmp -> summ = $row -> summ;
						$reportArray[$tmp -> id] = $tmp;
					}
					if ( ! empty($row -> payment_name) && $row -> payment_name == 'Предоставление местного телефонного соединения с использованием повременной оплаты местных телефонных соединений за 1 минуту') {
						$tmp = new Admin_model();
						$tmp -> id = $row -> id;
						$tmp -> accounts = $row -> accounts;
						$tmp -> payment_name = $row -> payment_name;
						$tmp -> inn = $row -> inn;
						$tmp -> bindings_name = $row -> bindings_name;
						$tmp -> summ = $row -> summ;
						if ($row -> id_service == 3) {
							$tmp -> counter = $row -> summ / 0.60;
							$tmp -> price = 0.60;
						} else {
							$tmp -> counter = $row -> summ / 0.51;
							$tmp -> price = 0.51;
						}
						$reportArray[$tmp -> id] = $tmp;
					}
				}
			}
		}
		if ($checkbox == 'false') {
			if (0 < $report_rows -> num_rows) {

				foreach ($report_rows -> result() as $row) {

					if (empty($row -> payment_name)) {
						$tmp = new Admin_model();
						$tmp -> id = $row -> id;
						$tmp -> accounts = $row -> accounts;
						$tmp -> inn = $row -> inn;
						//$tmp -> bindings_name = $row -> bindings_name;
						$tmp -> payment_name = 'Услуги связи';
						$tmp -> counter = 1;
						$tmp -> price = $row -> price - $row -> discount;
						$tmp -> summ = $row -> price - $row -> discount;
						$reportArray[$tmp -> id] = $tmp;
					} else {
						$tmp = new Admin_model();
						$tmp -> id = $row -> id;
						$tmp -> accounts = $row -> accounts;
						$tmp -> inn = $row -> inn;
						$tmp -> payment_name = $row -> payment_name;
						//$tmp -> bindings_name = $row -> bindings_name;
						$tmp -> counter = $row -> counter;
						$tmp -> price = $row -> price;
						$tmp -> summ = $row -> summ;
						$reportArray[$tmp -> id] = $tmp;
					}
					if ( ! empty($row -> payment_name) && $row -> payment_name == 'Предоставление местного телефонного соединения с использованием повременной оплаты местных телефонных соединений за 1 минуту' && $row -> id_service != 8) {
						$tmp = new Admin_model();
						$tmp -> id = $row -> id;
						$tmp -> accounts = $row -> accounts;
						$tmp -> inn = $row -> inn;
						$tmp -> payment_name = $row -> payment_name;
						//$tmp -> bindings_name = $row -> bindings_name;
						$tmp -> summ = $row -> summ;
						if ($row -> id_service == 3) {
							$tmp -> counter = $row -> summ / 0.60;
							$tmp -> price = 0.60;
						} else {
							$tmp -> counter = $row -> summ / 0.51;
							$tmp -> price = 0.51;
						}
						$reportArray[$tmp -> id] = $tmp;
					}
				}
			}
		}
		return $reportArray;
	}

	function getMinAmounts($month, $id_service)
	{

		$createDate = DateTime::createFromFormat('m', $month);
		$start_date_period = $createDate -> format('Y-m-01');
		$end_date_period = $createDate -> format('Y-m-t');

		$this -> db -> select('customer_payments.id, customer_payments.id_client, customer_service.id as customer_service_id, clients_accounts.bindings_name, inn,  customer_payments.id_account, accounts, SUM( customer_payments.amount ) AS price, IFNULL(round(SUM(REPLACE( customer_discounts.amount, ",","." )),2),"00.00") as discount, id_service', FALSE);
		$this -> db -> from('customer_payments');
		$this -> db -> join('clients_accounts', 'clients_accounts.id =  customer_payments.id_account', 'left');
		$this -> db -> join('clients', 'clients_accounts.id_clients =  clients.id', 'inner');
		$this -> db -> join('customer_discounts', 'customer_discounts.id_account =  customer_payments.id_account', 'left');
		$this -> db -> join('customer_service', 'customer_service.id =  customer_payments.id_assortment_customer', 'inner');
		$this -> db -> where_in('clients_accounts.id_service', $id_service);
		$this -> db -> where('customer_payments.period_start between "' . date($start_date_period) . '" and "' . date($end_date_period) . '"');
		$this -> db -> where('customer_payments.period_end between "' . date($start_date_period) . '" and "' . date($end_date_period) . '"');
		$this -> db -> group_by('customer_payments.id_account');
		$this -> db -> group_by('clients_accounts.bindings_name');

		$report_rows = $this -> db -> get();


		if (0 < $report_rows -> num_rows) {

			foreach ($report_rows -> result() as $row) {

				if (empty($row -> payment_name) && ($row -> price - $row -> discount) < 200 && $this -> checkMinPaid($row -> id_account) === 1) {
					$tmp = new Admin_model();
					$tmp -> id = $row -> id;
					$tmp -> accounts = $row -> accounts;
					$tmp -> bindings_name = $row -> bindings_name;
					$tmp -> id_account = $row -> id_account;
					$tmp -> inn = $row -> inn;
					$tmp -> payment_name = 'Услуги связи';
					$tmp -> counter = 1;
					$tmp -> oldsumm = $row -> price - $row -> discount;
					$tmp -> newsumm = 200;
					$tmp -> difference = 200 - ($row -> price - $row -> discount);
					$tmp -> customer_service_id = $row -> customer_service_id;
					$tmp -> start_date_period = $start_date_period;
					$tmp -> end_date_period = $end_date_period;
					$tmp -> id_client = $row -> id_client;
					#$this -> additional_charge($id_assortment_customer, $id_account, $amount, $period_start, $period_end, $id_client);
					$reportArray[$tmp -> id] = $tmp;
				}
			}
		}
		return $reportArray;
	}

	function additional_charge($id_assortment_customer, $id_account, $amount, $period_start, $period_end, $id_client)
	{
		$data = array(
			'id_assortment_customer' => $id_assortment_customer,
			'id_account' => $id_account,
			'amount' => $amount,
			'period_start' => $period_start,
			'period_end' => $period_end,
			'id_client' => $id_client
		);

		$this -> db -> insert('customer_payments', $data);
	}

	function checkMinPaid($id_account)
	{

		$this -> db -> select('*');
		$this -> db -> from('customer_service');
		$this -> db -> where('id_account', $id_account);
		$this -> db -> where('payment_name', 'Минимальный платеж');
		$this -> db -> where('end_date IS NULL', null, false);
		$query = $this -> db -> get();
		$rowcount = $query -> num_rows();
		return $rowcount;
	}

	function buildMergeReport($month1, $month2, $id_service)
	{
		$reportArray = array();
		if ( ! in_array(8, $id_service)) {
			$createDate1 = DateTime::createFromFormat('m', $month1);
			$start_date_period1 = $createDate1 -> format('Y-m-01');
			$end_date_period1 = $createDate1 -> format('Y-m-t');

			$createDate2 = DateTime::createFromFormat('m', $month2);
			$start_date_period2 = $createDate2 -> format('Y-m-01');
			$end_date_period2 = $createDate2 -> format('Y-m-t');

			$this -> db -> select('customer_payments.id, clients_accounts.bindings_name, accounts , SUM( amount ) AS summ1, payment_name, amount as price1,COUNT( payment_name ) AS counter1, account2, payment_name2, price2, counter2, summ2, id_service');
			$this -> db -> from('customer_payments');
			$this -> db -> join('clients_accounts', 'clients_accounts.id =  customer_payments.id_account', 'inner');
			$this -> db -> join('customer_service', 'customer_service.id =  customer_payments.id_assortment_customer', 'inner');
			$this -> db -> join("(SELECT `customer_payments`.`id`, `clients_accounts`.`bindings_name`, `accounts` as `account2`, SUM( amount ) AS summ2, `payment_name` as `payment_name2`,
				`amount` as price2, COUNT( payment_name ) AS counter2, `id_service` as `id_services`
				FROM (`customer_payments`)
				LEFT JOIN `clients_accounts` ON `clients_accounts`.`id` =  `customer_payments`.`id_account`
				LEFT JOIN `customer_service` ON `customer_service`.`id` =  `customer_payments`.`id_assortment_customer`
				WHERE `customer_payments`.`period_start` between '" . $start_date_period2 . "' and '" . $end_date_period2 . "'
				AND `customer_payments`.`period_end` between '" . $start_date_period2 . "' and '" . $end_date_period2 . "'
				AND `clients_accounts`.`id_service` != 8
				AND `clients_accounts`.`id_service` IN ('" . implode(",", $id_service) . "') 
				GROUP BY `customer_payments`.`id_account`, `customer_payments`.`amount`, `clients_accounts`.`bindings_name`,
				`customer_service`.`payment_name`) as `A`", "A.account2 = accounts AND A.payment_name2 = payment_name", "left");
			$this -> db -> where('customer_payments.period_start between "' . date($start_date_period1) . '" and "' . date($end_date_period1) . '"');
			$this -> db -> where('customer_payments.period_end between "' . date($start_date_period1) . '" and "' . date($end_date_period1) . '"');
			$this -> db -> where('clients_accounts.id_service !=', 8);
			$this -> db -> where_in('clients_accounts.id_service', $id_service);
			$this -> db -> group_by('customer_payments.id_account');
			$this -> db -> group_by('customer_payments.amount');
			$this -> db -> group_by('clients_accounts.bindings_name');
			$this -> db -> group_by('customer_service.payment_name');
			$this -> db -> group_by('A.payment_name2');

			$report_rows = $this -> db -> get();
		} else {
			$createDate1 = DateTime::createFromFormat('m', $month1);
			$start_date_period1 = $createDate1 -> format('Y-m-01');
			$end_date_period1 = $createDate1 -> format('Y-m-t');

			$createDate2 = DateTime::createFromFormat('m', $month2);
			$start_date_period2 = $createDate2 -> format('Y-m-01');
			$end_date_period2 = $createDate2 -> format('Y-m-t');

			$this -> db -> select('customer_payments.id, clients_accounts.bindings_name, accounts , SUM( amount ) AS summ1, payment_name, amount as price1,COUNT( payment_name ) AS counter1, account2, payment_name2, price2, counter2, summ2, id_service', FALSE);
			$this -> db -> from('customer_payments');
			$this -> db -> join('clients_accounts', 'clients_accounts.id =  customer_payments.id_account', 'left');
			$this -> db -> join('customer_discounts', 'customer_discounts.id_account =  customer_payments.id_account', 'left');
			$this -> db -> join("(SELECT `customer_payments`.`id`, `clients_accounts`.`bindings_name`, `accounts` as `account2`, SUM( amount ) AS summ2, `payment_name` as `payment_name2`,
				`amount` as price2, COUNT( payment_name ) AS counter2, `id_service` as `id_services`
				FROM (`customer_payments`)
				INNER JOIN `clients_accounts` ON `clients_accounts`.`id` =  `customer_payments`.`id_account`
				INNER JOIN `customer_service` ON `customer_service`.`id` =  `customer_payments`.`id_assortment_customer`
				WHERE `customer_payments`.`period_start` between " . $start_date_period2 . " and " . $end_date_period2 . "
				AND `customer_payments`.`period_end` between " . $start_date_period2 . " and " . $end_date_period2 . "
				AND `clients_accounts`.`id_service` != 8
				AND `clients_accounts`.`id_service` IN ('" . implode(",", $id_service) . "') 
				GROUP BY `customer_payments`.`id_account`, `customer_payments`.`amount`, `clients_accounts`.`bindings_name`,
				`customer_service`.`payment_name`) as `A`", "A.account2 = accounts", "LEFT");
			$this -> db -> where_in('clients_accounts.id_service', $id_service);
			$this -> db -> where('customer_payments.period_start between "' . date($start_date_period1) . '" and "' . date($end_date_period1) . '"');
			$this -> db -> where('customer_payments.period_end between "' . date($start_date_period1) . '" and "' . date($end_date_period1) . '"');
			$this -> db -> group_by('customer_payments.id_account');
			$this -> db -> group_by('customer_payments.amount');
			$this -> db -> group_by('clients_accounts.bindings_name');

			$report_rows = $this -> db -> get();
		}

		if (0 < $report_rows -> num_rows) {

			foreach ($report_rows -> result() as $row) {

				if (empty($row -> payment_name)) {
					$tmp = new Admin_model();
					$tmp -> id = $row -> id;
					$tmp -> accounts = $row -> accounts;
					$tmp -> bindings_name = $row -> bindings_name;
					$tmp -> payment_name = 'Услуги связи';
					$tmp -> counter1 = 1;
					$tmp -> price1 = $row -> price1 - $row -> discount;
					$tmp -> summ1 = $row -> price1 - $row -> discount;
					$reportArray[$tmp -> id] = $tmp;
				} else {
					$tmp = new Admin_model();
					$tmp -> id = $row -> id;
					$tmp -> accounts = $row -> accounts;
					$tmp -> account2 = $row -> account2;
					$tmp -> payment_name = $row -> payment_name;
					$tmp -> payment_name2 = $row -> payment_name2;
					$tmp -> bindings_name = $row -> bindings_name;
					$tmp -> counter1 = $row -> counter1;
					$tmp -> counter2 = $row -> counter2;
					$tmp -> price1 = $row -> price1;
					$tmp -> price2 = $row -> price2;
					$tmp -> summ1 = $row -> summ1;
					$tmp -> summ2 = $row -> summ2;
					$reportArray[$tmp -> id] = $tmp;
				}
				if ( ! empty($row -> payment_name) && $row -> payment_name == 'Предоставление местного телефонного соединения с использованием повременной оплаты местных телефонных соединений за 1 минуту') {
					$tmp = new Admin_model();
					$tmp -> id = $row -> id;
					$tmp -> accounts = $row -> accounts;
					$tmp -> payment_name = $row -> payment_name;
					$tmp -> bindings_name = $row -> bindings_name;
					$tmp -> summ1 = $row -> summ1;
					if ($row -> id_service == 3) {
						$tmp -> counter1 = $row -> summ1 / 0.60;
						$tmp -> price1 = 0.60;
					} else {
						$tmp -> counter1 = $row -> summ1 / 0.51;
						$tmp -> price1 = 0.51;
					}
					$reportArray[$tmp -> id] = $tmp;
				}
			}
		}
		return $reportArray;
	}

	function getData()
	{
		echo $this -> db -> count_all_results('compare_balance');
	}

	function getDataPostbilling()
	{
		echo $this -> db -> count_all_results('compare_balance_pb');
	}

}

//End of file admin_model.php
//Location: ./models/admin_model.php