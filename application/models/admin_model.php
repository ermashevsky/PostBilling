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
					};

					echo json_encode($searchResult);
				} else {
					echo "Данный тариф не используется на лицевых счетах клиентов";
				}

				break;
		};
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

		$reportArray = array();
		if ($id_service == 'all') {
			$createDate = DateTime::createFromFormat('m', $month);
			$start_date_period = $createDate -> format('Y-m-01');
			$end_date_period = $createDate -> format('Y-m-t');

			$this -> db -> select('customer_service.id, clients_accounts.accounts, customer_service.payment_name, COUNT(payment_name) as counter, tariffs.price as price, COUNT( payment_name ) * price AS summ');
			$this -> db -> from('customer_service');
			$this -> db -> join('clients_accounts', 'clients_accounts.id =  customer_service.id_account', 'inner');
			$this -> db -> join('tariffs', 'tariffs.id =  customer_service.tariffs', 'inner');
			$this -> db -> where('customer_service.tariffs!=', '""', FALSE);
			$this -> db -> where('customer_service.end_date', null);
			$this -> db -> or_where('customer_service.end_date >=', date($end_date_period));
			$this -> db -> or_where('customer_service.end_date between ' . date($start_date_period) . ' and ' . date($end_date_period));
			$this -> db -> where('customer_service.datepicker1 <=', date($start_date_period));
			$this -> db -> or_where('customer_service.datepicker1 between ' . date($start_date_period) . ' and ' . date($end_date_period));
			$this -> db -> group_by('customer_service.payment_name', FALSE);
			$this -> db -> group_by('customer_service.id_account');
			$this -> db -> group_by('price');
			$report_rows = $this -> db -> get();
		} else {
			$createDate = DateTime::createFromFormat('m', $month);
			$start_date_period = $createDate -> format('Y-m-01');
			$end_date_period = $createDate -> format('Y-m-t');

			$this -> db -> select('customer_service.id, clients_accounts.accounts, customer_service.payment_name, COUNT(payment_name) as counter, tariffs.price as price, COUNT( payment_name ) * price AS summ, clients_accounts.id_service');
			$this -> db -> from('customer_service');
			$this -> db -> join('clients_accounts', 'clients_accounts.id =  customer_service.id_account', 'inner');
			$this -> db -> join('tariffs', 'tariffs.id =  customer_service.tariffs', 'inner');
			$this -> db -> where('customer_service.tariffs!=', '""', FALSE);
			$this -> db -> where('customer_service.end_date', null);
			$this -> db -> or_where('customer_service.end_date >=', date($end_date_period));
			$this -> db -> or_where('customer_service.end_date between ' . date($start_date_period) . ' and ' . date($end_date_period));
			$this -> db -> or_where('customer_service.datepicker1 between ' . date($start_date_period) . ' and ' . date($end_date_period));
			$this -> db -> where('customer_service.datepicker1 <=', date($start_date_period));
			$this -> db -> group_by('customer_service.payment_name', FALSE);
			$this -> db -> group_by('customer_service.id_account');
			$this -> db -> group_by('price');
			$this -> db -> having('clients_accounts.id_service', $id_service);
			$report_rows = $this -> db -> get();
		}
		if (0 < $report_rows -> num_rows) {
			foreach ($report_rows -> result() as $row) {
				$tmp = new Admin_model();
				$tmp -> id = $row -> id;
				$tmp -> accounts = $row -> accounts;
				$tmp -> payment_name = $row -> payment_name;
				$tmp -> counter = $row -> counter;
				$tmp -> price = $row -> price;
				$tmp -> summ = $row -> summ;
				$reportArray[$tmp -> id] = $tmp;
			}
		}
		return $reportArray;
	}

	function getData()
	{
		echo $this -> db -> count_all_results('compare_balance');
	}
}

//End of file admin_model.php
//Location: ./models/admin_model.php