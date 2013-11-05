<?php

/**
 * Services_Model
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Models.Services_Model
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://www.ci2.lcl/
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Класс Services_Model содержит методы взаимодействия клиента с услугами
 *
 * @category PHP
 * @package  Models.Services_Model
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Services_model extends CI_Model
{

	/**
	 * Identity.
	 *
	 * @var integer
	 */
	public $id;

	/**
	 * Name of the nomenclature.
	 *
	 * @var string Services_model
	 */
	var $payment_name;

	/**
	 * Type of the nomenclature.
	 *
	 * @var $payment_type Services_model
	 */
	var $payment_type;

	/**
	 * Price.
	 *
	 * @var $price Services_model
	 */
	var $price;

	/**
	 * Name of group.
	 *
	 * @var $group_name Services_model
	 */
	var $group_name;

	/**
	 * Marker of Service.
	 *
	 * @var $marker_service Services_model
	 */
	var $marker_service;

	/**
	 * Service Group.
	 *
	 * @var $services_groups Services_model
	 */
	var $services_groups;

	/**
	 * Service description.
	 *
	 * @var $service_description Services_model
	 */
	var $service_description;

	/**
	 * Marker.
	 *
	 * @var $marker Services_model
	 */
	var $marker;

	/**
	 * Tariff name.
	 *
	 * @var $tariff_name Services_model
	 */
	var $tariff_name;

	/**
	 * Amount.
	 *
	 * @var $amount Services_model
	 */
	var $amount;

	/**
	 * Date.
	 *
	 * @var $date Services_model
	 */
	var $date;

	/**
	 * Унифицированный метод-конструктор __construct()
	 *
	 * @author Ермашевский Денис
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Метод возвращает список групп
	 *
	 * @param string $where       условие
	 * @param int    $limit_from  диапазон выборки
	 * @param int    $limit_count ограничение выборки
	 *
	 * @author Ермашевский Денис
	 * @return array $results;
	 */
	function get_service_groups($where = FALSE, $limit_from = FALSE, $limit_count = FALSE)
	{
		$results = array();

		if ($limit_from)
			$limit_from = intval($limit_from);
		else
			$limit_from = NULL;
		if ($limit_count)
			$limit_count = intval($limit_count);
		else
			$limit_count = NULL;
		$this -> db -> select('service_groups.id,services_groups, marker, service_description');
		$this -> db -> from('service_groups');
		$this -> db -> join('services', 'services.id = service_groups.id_services', 'inner');
		$res = $this -> db -> get();
		if (0 < $res -> num_rows) {
			foreach ($res -> result() as $row) {
				$tmp = new Services_model();
				$tmp -> id = $row -> id;
				$tmp -> services_groups = $row -> services_groups;
				$tmp -> marker = $row -> marker;
				$tmp -> service_description = $row -> service_description;
				$results[$tmp -> id] = $tmp;
			}
		}
		return $results;
	}


	/**
	 * Метод возвращает список номенклатуры
	 *
	 * @author Ермашевский Денис
	 * @return array $assortmentList;
	 */
	function getAssortmentList()
	{
		$assortmentList = array();
		$this -> db -> select('id, payment_name, marker_service,payment_type');
		$this -> db -> from('assortment');
		$assortment = $this -> db -> get();
		if (0 < $assortment -> num_rows) {
			foreach ($assortment -> result() as $fields) {
				$tmp = new Services_model();
				$tmp -> id = $fields -> id;
				$tmp -> payment_name = $fields -> payment_name;
				$tmp -> marker_service = $fields -> marker_service;
				$tmp -> payment_type = $fields -> payment_type;
				$assortmentList[$tmp -> id] = $tmp;
			}
			return $assortmentList;
		}
	}

	/**
	 * Метод возвращает информацию по начислениям номенклатуры
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return int;
	 */
	function getAssortmentInfoPayments($id)
	{

		$this -> db -> select('*');
		$this -> db -> from('customer_payments');
		$this -> db -> where('id_assortment_customer', $id);
		$row = $this -> db -> get();
		return $row -> num_rows();
	}

	/**
	 * Метод возвращает кол-во строк по начислениям для номеклатур в группе
	 *
	 * @param string $uniq_id уникальный идентификатор
	 *
	 * @return json $data
	 */
	function getAssortmentsPaymentsInGroup($uniq_id)
	{
		/**
		 * SELECT *
		 * FROM `customer_payments`
		 * INNER JOIN customer_service ON customer_service.id = customer_payments.id_assortment_customer
		 * WHERE customer_service.uniq_id = 'ad5e7335ed52ca0f29fbef562cd48128'
		 */
		$this -> db -> select('*');
		$this -> db -> from('customer_payments');
		$this -> db -> join('customer_service', 'customer_service.id = customer_payments.id_assortment_customer', 'inner');
		$this -> db -> where('customer_service.uniq_id', $uniq_id);
		$row = $this -> db -> get();
		return $row -> num_rows();
	}

	/**
	 * Удаление номеклатуры из таблицы customer_service
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function deleteAssortmentItem($id)
	{

		$this -> db -> select('resources');
		$this -> db -> from('customer_service');
		$this -> db -> where('id', $id);
		$row = $this -> db -> get();
		foreach ($row -> result() as $res) {
			echo $res -> resources;
			if ($res -> resources !== 'нет') {
				echo $res -> resources;
				$sql = 'UPDATE free_phone_pool SET status = "free" WHERE id ="' . $res -> resources.'"';
				$this -> db -> query($sql);
			}
		}

		$this -> db -> where('id', $id);
		$this -> db -> delete('customer_service');

	}

	/**
	 * Получение номеклатуры из таблицы customer_service
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function getAssortmentItemByID($id)
	{

		$this -> db -> select('*');
		$this -> db -> from('customer_service');
		$this -> db -> where('id', $id);
		$row = $this -> db -> get();
		foreach ($row -> result() as $rows) {
			$tmp = new Services_model();
				$tmp -> id = $rows -> id;
				$tmp -> id_account = $rows -> id_account;
				$tmp -> payment_name = $rows -> payment_name;
				$tmp -> datepicker1 = $rows -> datepicker1;
				$tmp -> end_date = $rows -> end_date;
				$assortmentItem[$tmp -> id] = $tmp;
			}
			return $assortmentItem;

	}

	/**
	 * Получение единичной номеклатуры из таблицы customer_service
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function getAssortmentByID($id)
	{

		$this -> db -> select('*');
		$this -> db -> from('assortment');
		$this -> db -> where('id', $id);
		$row = $this -> db -> get();
		foreach ($row -> result() as $rows) {
			$tmp = new Services_model();
				$tmp -> id = $rows -> id;
				$tmp -> payment_name = $rows -> payment_name;
				$tmp -> marker_service = $rows -> marker_service;
				$tmp -> payment_type = $rows -> payment_type;
				$tmp -> tariff = $rows -> tariff;
				$assortment[$tmp -> id] = $tmp;
			}
			return $assortment;

	}

	/**
	 * Получение группы номенклатур по уникальному идентификатору из таблицы customer_service
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function getAssortmentGroupByUniqID($uniq_id=NULL)
	{

		$this -> db -> select('*');
		$this -> db -> from('customer_service');
		$this -> db -> where('uniq_id', $uniq_id);
		$row = $this -> db -> get();
		foreach ($row -> result() as $rows) {
			$tmp = new Services_model();
				$tmp -> id = $rows -> id;
				$tmp -> id_account = $rows -> id_account;
				$tmp -> identifier = $rows -> identifier;
				$tmp -> payment_name = $rows -> payment_name;
				$tmp -> datepicker1 = $rows -> datepicker1;
				$tmp -> end_date = $rows -> end_date;
				$assortmentGroup[$tmp -> id] = $tmp;
			}
			return $assortmentGroup;

	}

	/**
	 * Удаление номеклатуры по идентификатору
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function deleteAssortment($id)
	{
		$this -> db -> where('id', $id);
		$this -> db -> delete('assortment');
	}

	/**
	 * Удаление группы номеклатур по уникальному идентификатору
	 *
	 * @param string $uniq_id уникальный идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function deleteGroupAssortments($uniq_id)
	{

		$this -> db -> where('uniq_id', $uniq_id);
		$this -> db -> delete('customer_service');
	}

	/**
	 * Метод проверки возможности удаления лицевого счета клиента
	 *
	 * @param int $id        идентификатор
	 * @param int $id_client идентфикатор клиента
	 *
	 * @author Ермашевский Денис
	 * @return string $data
	 */
	function deleteClientAccounts($id, $id_client)
	{

		$sql_query = 'SELECT (
	    SELECT count( * )
	    FROM `clients_accounts`
	    WHERE clients_accounts.`id_clients` =' . $id_client . '
	    ) AS accounts, (
	    SELECT count( * )
	    FROM `customer_service`
	    WHERE customer_service.`id_account` =' . $id . '
	    ) AS services';

		$result = $this -> db -> query($sql_query);

		foreach ($result -> result() as $row) {

			if ($row -> services != 0) {
				$data['result'] = 'В лицевом счете содержатся группы номенклатур. Удаление невозможно';
			}

			if ($row -> services == 0 AND $row -> accounts < 2) {
				$data['result'] = 'Нельзя удалить единственный лицевой счет.';
			}

			if ($row -> services == 0 AND $row -> accounts > 1) {
				$data['result'] = 1;
			}
		}
		return $data;
	}

	/**
	 * Удаление лицевого счета клиента
	 *
	 * @param int $id        идентификатор
	 * @param int $id_client идентфикатор клиента
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function deleteAccount($id, $id_client)
	{
		$this -> db -> where('id', $id);
		$this -> db -> where('id_clients', $id_client);
		$this -> db -> delete('clients_accounts');
	}

	/**
	 * Метод возвращает список номеклатуры в группе
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return mixed $data
	 */
	function getAssortmentGroup($id)
	{
		$assortmentData = array();
		$id = (int) $id;
		$this -> db -> select('assortment.id,assortment.payment_name, assortment.payment_type');
		$this -> db -> from('assortment');
		$this -> db -> join('group_assortment_link', 'group_assortment_link.id_assortments = assortment.id', 'left');
		$this -> db -> where('group_assortment_link.id_group_service', $id);
		$assortment_group = $this -> db -> get();
		if (0 < $assortment_group -> num_rows) {
			$n = 1;
			foreach ($assortment_group -> result() as $assortments_group) {
				$tmp = new Services_model();
				$tmp -> id = $assortments_group -> id;
				$tmp -> payment_name = $assortments_group -> payment_name;
				$tmp -> payment_type = $assortments_group -> payment_type;
				$tmp -> id_group = $id; //Warning
				$assortmentData[$n ++] = $tmp;

			}
			return $assortmentData;
		}else{
			return $assortmentData;
		}
	}

	/**
	 * Метод возвращает список номеклатуры в группе на лицевом счете клиента
	 *
	 * @param int $id_group идентификатор группы
	 *
	 * @author Ермашевский Денис
	 * @return mixed $assortmentList
	 */
	function getCustomerServiceInfo($id_group)
	{

		$this -> db -> select('*');
		$this -> db -> from('customer_service');
		$this -> db -> where('id_group', $id_group);
		$count = $this -> db -> count_all_results(); //
		if ($count > 0) {
			return $count;
		} else {
			$this -> db -> select('id_assortments');
			$this -> db -> from('group_assortment_link');
			$this -> db -> where('id_group_service', $id_group);
			$myList = $this -> db -> get();
			$n = 1;
			$IDs = array();
			foreach ($myList -> result() as $fields) {
				$IDs[$n ++] = $fields -> id_assortments;
			}

			$this -> db -> select('*');
			$this -> db -> from('assortment');
			$this -> db -> where_not_in('id', $IDs);
			$myAssortmentList = $this -> db -> get();
			$assortmentList = array();
			$n = 1;
			foreach ($myAssortmentList -> result() as $row):
				$tmp = new Services_model();
				$tmp -> id = $row -> id;
				$tmp -> payment_name = $row -> payment_name;
				$tmp -> payment_type = $row -> payment_type;
				$assortmentList[$row -> id] = $tmp;
			endforeach;
			return $assortmentList;
		}
	}

	function getAccountInfo($id=NULL)
	{
		$AccountData = array();
		$id = (int) $id;
		$this -> db -> select('*');
		$this -> db -> from('clients_accounts');
		$this -> db -> where('id', $id);
		$account_info = $this -> db -> get();

		foreach ($account_info -> result() as $rows) {
				$tmp = new Services_model();
				$tmp -> id = $rows -> id;
				$tmp -> bindings_name = $rows -> bindings_name;
				$tmp -> accounts = $rows -> accounts;
				$AccountData[$tmp->id] = $tmp;

			}
			return $AccountData;
	}

	/**
	 * Метод возвращает номеклатуру по идентификатору
	 *
	 * @param int $id идентификатор группы
	 *
	 * @author Ермашевский Денис
	 * @return mixed $servicesList
	 */
	function getServiceByID($id)
	{
		$this -> db -> select('*');
		$this -> db -> from('services');
		$this -> db -> where('id', $id);
		$services = $this -> db -> get();
		if (0 < $services -> num_rows) {
			foreach ($services -> result() as $fields) {
				$tmp = new Services_model();
				$tmp -> id = $fields -> id;
				$tmp -> service_description = $fields -> service_description;
				$tmp -> marker = $fields -> marker;
				$servicesList[$tmp -> id] = $tmp;
			}
			return $servicesList;
		}
	}


	/**
	 * Метод редактирования номеклатуры по идентификатору
	 *
	 * @param array $data массив новых данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function editServiceByID($data)
	{
		$sql = 'UPDATE services
	        SET service_description = "' . $data['service_description'] . '" WHERE id =' . $data['id'];
		$this -> db -> query($sql);
	}

	/**
	 * Метод выясняет возможность удаления услуги по идентфикатору
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function getServiceUsed($id)
	{
		$this -> db -> select('*');
		$this -> db -> from('assortment');
		$this -> db -> join('services', 'services.marker = assortment.marker_service', 'inner');
		$this -> db -> where('services.id', $id);
		$count = $this -> db -> count_all_results();
		if ($count > 0) {
			return 'У лицевого счета есть номеклатура. Удаление невозможно';
		} else {
			return 1;
		}
	}

	/**
	 * Метод удаления услуги по идентификатору
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function deleteService($id)
	{
		$this -> db -> where('id', $id);
		$this -> db -> delete('services');
	}

	/**
	 * Метод возвращает группу номеклатур по идентификатору
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return mixed $servicesGroupList
	 */
	function getServiceGroupByID($id)
	{
		$this -> db -> select('*');
		$this -> db -> from('service_groups');
		$this -> db -> where('id', $id);
		$services = $this -> db -> get();
		if (0 < $services -> num_rows) {
			foreach ($services -> result() as $fields) {
				$tmp = new Services_model();
				$tmp -> id = $fields -> id;
				$tmp -> services_groups = $fields -> services_groups;
				$servicesGroupList[$tmp -> id] = $tmp;
			}
			return $servicesGroupList;
		}
	}

	/**
	 * Метод редактирования группы номеклатур по идентификатору
	 *
	 * @param array $data массив новых данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function editServiceGroupByID($data)
	{
		$sql = 'UPDATE service_groups
	        SET services_groups = "' . $data['services_groups'] . '" WHERE id =' . $data['id'];
		$this -> db -> query($sql);
	}

	/**
	 * Метод возвращает номеклатуру из группы у клиента по идентификатору
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return string
	 */
	function getAssortmentItemGroup($id)
	{
		$this -> db -> select('*');
		$this -> db -> from('group_assortment_link');
		$this -> db -> join('customer_service', 'customer_service.id_group = group_assortment_link.id_group_service');
		$this -> db -> where('group_assortment_link.id_assortments', $id);
		$count = $this -> db -> count_all_results();
		if ($count > 0) {
			return 'Номеклатура используется. Удаление невозможно';
		} else {
			return 1;
		}
	}
	/**
	 * Метод удаления номеклатуры из группы у клиента по идентификатору
	 *
	 * @param int $id       идентификатор
	 * @param int $id_group идентификатор группы
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function deleteAssortmentItemGroup($id, $id_group)
	{
		$this -> db -> where('id_assortments', $id);
		$this -> db -> where('id_group_service', $id_group);
		$this -> db -> delete('group_assortment_link');
	}


	/**
	 * Метод добавления номеклатуры в группы клиента по идентификатору
	 *
	 * @param int $id       идентификатор
	 * @param int $id_group идентификатор группы
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function addAssortmentItemGroup($id, $id_group)
	{

		$this -> db -> set('id_group_service', $id_group);
		$this -> db -> set('id_assortments', $id);
		$this -> db -> insert('group_assortment_link');
	}

	/**
	 * Формирования общего списка услуг
	 *
	 * @author Ермашевский Денис
	 * @return array $servicesType
	 */
	function getServiceType()
	{
		$servicesType = array();
		$this -> db -> select('id, service_description,marker');
		$this -> db -> from('services');
		$services = $this -> db -> get();
		if (0 < $services -> num_rows) {
			foreach ($services -> result() as $fields) {
				$tmp = new Services_model();
				$tmp -> id = $fields -> id;
				$tmp -> service_description = $fields -> service_description;
				$tmp -> marker = $fields -> marker;
				$servicesType[$tmp -> id] = $tmp;
			}
			return $servicesType;
		}
	}

	/**
	 * Метод добавления группы номеклатур
	 *
	 * @param array $data массив данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function add_group_service($data)
	{
		$sql = "INSERT INTO service_groups (services_groups,id_services) VALUES ('" . $data['group_name'] . "','" . $data['serviceType'] . "')";
		$this -> db -> query($sql);
		//$this->db->insert('service_groups',$data);
		$last_id = $this -> db -> insert_id();
		if (isset($last_id)) {
			echo $last_id = (int) $last_id;
			//@todo: Доделать этот кусок. При добавлении имею id его записи, после этого необходимо в цикле
			//произвести добавление ассортимента в группу
			foreach ($data['assortment_selected'] as $assorment_selected) {
				echo $sql2 = "INSERT INTO group_assortment_link (id_group_service,id_assortments) VALUES ('" . $last_id . "','" . $assorment_selected . "')";
				$this -> db -> query($sql2);
			}
		}
	}


	/**
	 * Метод возвращает данные номеклатуры по идентификатору
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return mixed $assormentItem
	 */
	function getAssortmnentsById($id)
	{
		$assormentItem = array();
		$this -> db -> select('id,payment_name,payment_type,element_type,default_value,target,type_resources,tariff');
		$this -> db -> from('assortment');
		$this -> db -> where('assortment.id', $id);
		$assortment = $this -> db -> get();
		if (0 < $assortment -> num_rows) {
			foreach ($assortment -> result() as $fields) {
				$tmp = new Services_model();
				$tmp -> id = $fields -> id;
				$tmp -> payment_name = $fields -> payment_name;
				$tmp -> payment_type = $fields -> payment_type;
				$tmp -> element_type = $fields -> element_type;
				$tmp -> default_value = $fields -> default_value;
				$tmp -> target = $fields -> target;
				$tmp -> type_resources = $fields -> type_resources;
				$tmp -> tariff = $fields -> tariff;
				$assormentItem[$tmp -> id] = $tmp;
			}
			return $assormentItem;
		}
	}

	/**
	 * Метод возвращает список тарифов
	 *
	 * @author Ермашевский Денис
	 * @return mixed $tariffList
	 */
	function getTariffs()
	{
		$tariffList = array();

		$this -> db -> select('tariffs.id, tariff_name, tariffs.price, marker_service, id_assortment');
		$this -> db -> from('tariffs');
		$this -> db -> join('assortment', 'assortment.id = tariffs.id_assortment');
		$tariffs = $this -> db -> get();

		if (0 < $tariffs -> num_rows) {
			foreach ($tariffs -> result() as $fields) {
				$tmp = new Services_model();
				$tmp -> id = $fields -> id;
				$tmp -> tariff_name = $fields -> tariff_name;
				$tmp -> price = $fields -> price;
				$tmp -> marker_service = $fields -> marker_service;
				$tmp -> id_assortment = $fields -> id_assortment;
				$tariffList[$tmp -> id] = $tmp;
			}
			return $tariffList;
		}
	}

	function delTariffById($id)
	{
		$this -> db -> select('count(*) AS num_rows');
		$this -> db -> from('customer_service');
		$this -> db -> where('customer_service.tariffs', $id);
		$tariffs = $this -> db -> get();

		return $tariffs->result() ;

	}

	function deleteTariff($id)
	{
		$this -> db -> where('id', $id);
		$this -> db -> delete('tariffs');
	}
	/**
	 * Метод возвращает список отфильтрованных тарифов по идентификатору
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return mixed $tariffList
	 */
	function getFilterTariffs($id)
	{
		$tariffList = array();

		$this -> db -> select('tariffs.id, tariff_name, tariffs.price, marker_service');
		$this -> db -> from('tariffs');
		$this -> db -> join('assortment', 'assortment.id = tariffs.id_assortment');
		$this -> db -> where('assortment.id', $id);
		$tariffs = $this -> db -> get();


		if (0 < $tariffs -> num_rows) {
			foreach ($tariffs -> result() as $fields) {
				$tmp = new Services_model();
				$tmp -> id = $fields -> id;
				$tmp -> tariff_name = $fields -> tariff_name;
				$tmp -> price = $fields -> price;
				$tmp -> marker_service = $fields -> marker_service;
				$tariffList[$tmp -> id] = $tmp;
			}
			return $tariffList;
		}
	}

	/**
	 * Метод возвращает тариф по идентификатору у номеклатуры
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return mixed $tariff_list
	 */
	function getTariffById($id=NULL)
	{

		$this -> db -> select('*');
		$this -> db -> from('tariffs');
		$this -> db -> where('id_assortment', $id);
		$tariff = $this -> db -> get();
		if (0 < $tariff -> num_rows) {
			$tariff_list = array();
			foreach ($tariff -> result() as $list) {
				$gettingTarif = new Services_model();
				$gettingTarif -> id = $list -> id;
				$gettingTarif -> tariff_name = $list -> tariff_name;
				$gettingTarif -> price = $list -> price;
				$tariff_list[$gettingTarif -> id] = $gettingTarif;
			}
		}
		return $tariff_list;
	}

	/**
	 * Метод возвращает тариф по идентификатору
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return mixed $tariff_list
	 */
	function getTariffId($id)
	{

		$this -> db -> select('*');
		$this -> db -> from('tariffs');
		$this -> db -> where('id', $id);
		$tariff = $this -> db -> get();
		if (0 < $tariff -> num_rows) {
			$tariff_list = array();
			foreach ($tariff -> result() as $list) {
				$gettingTarif = new Services_model();
				$gettingTarif -> id = $list -> id;
				$gettingTarif -> tariff_name = $list -> tariff_name;
				$gettingTarif -> price = $list -> price;
				$tariff_list[$gettingTarif -> id] = $gettingTarif;
			}
		}
		return $tariff_list;
	}

	/**
	 * Метод редактирования тарифа
	 *
	 * @param array $data массив новых данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function editTariff($data)
	{
		$sql = 'UPDATE tariffs
	        SET tariff_name = "' . $data['tariff_name'] . '", price = "' . $data['price'] . '" WHERE id =' . $data['id'];
		$this -> db -> query($sql);
		print_r($data);
	}

	/**
	 * Метод редактирования номеклатуры
	 *
	 * @param array $data массив новых данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function editAssortment($data)
	{
		$sql = 'UPDATE assortment
	        SET payment_name = "' . $data['payment_name'] . '" WHERE id =' . $data['id'];
		$this -> db -> query($sql);
		print_r($data);
	}

	/**
	 * Добавление новой номенклатуры
	 *
	 * @param array $data Входной параметр массив данных из контроллера
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function create_assortment_item($data)
	{
		$this -> db -> select('marker');
		$this -> db -> from('services');
		$this -> db -> where('id', $data['serviceType']);
		$result = $this -> db -> get();
		foreach ($result -> result() as $row) {
			$marker = $row -> marker;
		}
		$sql = 'INSERT INTO assortment (payment_name,marker_service,payment_type,name_element,element_type,default_value,target,type_resources,tariff) VALUES
            ("' . $data['assortment_name'] . '","' . $marker . '","' . $data['paymentType'] . '","' . $data['name_element'] . '","' . $data['element_form'] . '","' . $data['default_element_value'] . '","' . $data['datasource'] . '","' . $data['type_resources'] . '","' . $data['tariff'] . '")';
		$this -> db -> query($sql);
	}


	function updateIdentifier($id, $identifier)
	{
		$this->db->where('id', $id);
		$this->db->set('identifier', $identifier);
		$this->db->update('customer_service');
		return true;
	}
	/**
	 * Вспомогательная функция "Кем занят телефон" для поиска номера телефона.
	 *
	 * @author Denis Ermashevsky <ermashevsky@gmail.com>
	 * @return json
	 */
	function whoBusyPhone($resource)
	{
		$this->db->select('clients_accounts.id_clients, bindings_name, free_phone_pool.resources');
		$this->db->from('clients_accounts');
		$this->db->join('customer_service','customer_service.id_account = clients_accounts.id');
		$this->db->join('free_phone_pool','free_phone_pool.id = customer_service.resources');
		$this->db->like('free_phone_pool.resources', $resource);
		$this->db->where('free_phone_pool.status', 'busy');
		$result = $this -> db -> get();
		$phone_list = array();
		foreach ($result -> result() as $row) {
			$findPhone = new Services_model();
			$findPhone -> id_clients = $row->id_clients;
			$findPhone -> bindings_name = $row->bindings_name;
			$findPhone -> resources = $row->resources;
			$phone_list[$findPhone -> id_clients] = $findPhone;
		}
		return $phone_list;
	}
	/**
	 * Добавление нового тарифа
	 *
	 * @param mixed $data массив данных при создании тарифа
	 *
	 * @author Ермашевский Денис
	 * @return null
	 *
	 */
	function add_tariff($data)
	{
		$this -> db -> insert('tariffs', $data); //Вот так просто в codeigniter
	}

	/**
	 * Метод добавления услуги
	 *
	 * @param array $data массив новых данных
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function add_service($data)
	{
		$this -> db -> insert('services', $data);
	}

	/**
	 * Метод проверки содержания номеклатур в группе
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return json
	 */
	function getAssortmentGroupInfo($id)
	{
		$this -> db -> select('*');
		$this -> db -> from('group_assortment_link');
		$this -> db -> where('id_assortments', $id);
		$count = $this -> db -> count_all_results();
		echo json_encode($count);
	}

	/**
	 * Получение номенклатуры по id услуги
	 *
	 * @param int $id идентификатор услуги
	 *
	 * @author Ермашевский Денис
	 * @return array $assortmentsByServiceId
	 *
	 */
	function getAssortmentByServiceId($id)
	{
		$assortmentsByServiceId = array();
		$this -> db -> select('assortment.id, assortment.payment_name,assortment.payment_type');
		$this -> db -> from('assortment');
		$this -> db -> join('services', 'services.marker = assortment.marker_service');
		$this -> db -> where('services.id', $id);
		$assortmentByServiceId = $this -> db -> get();
		if (0 < $assortmentByServiceId -> num_rows) {
			foreach ($assortmentByServiceId -> result() as $fields) {
				$tmp = new Services_model();
				$tmp -> id = $fields -> id;
				$tmp -> payment_name = $fields -> payment_name;
				$tmp -> payment_type = $fields -> payment_type;
				$assortmentsByServiceId[$tmp -> id] = $tmp;
			}
			return $assortmentsByServiceId;
		}
	}

	/**
	 * Метод добавления ЛС клиенту
	 *
	 * @param int $id идентификатор клиента
	 *
	 * @author Ермашевский Денис
	 * @return array $accounts_list
	 *
	 */
	function add_client_accounts($id)
	{
		$counter = 0;
		$id = (int) $id;
		$accounts_list = array();
		$this -> db -> select('id_service');
		$this -> db -> from('clients_accounts');
		$this -> db -> where('id_clients', $id);
		$accounts = $this -> db -> get();
		$account_listing = array();
		foreach ($accounts -> result() as $row) {
			$account_listing[$row -> id_service] = $row -> id_service;
		}

		$this -> db -> select('*');
		$this -> db -> from('services');
		$this -> db -> where_not_in('id', $account_listing);
		$rs_list = $this -> db -> get();
		if (0 < $rs_list -> num_rows) {
			foreach ($rs_list -> result() as $list_accounts) {
				$tmp = new Services_model();
				$tmp -> id = $list_accounts -> id;
				$tmp -> service_description = $list_accounts -> service_description;
				$tmp -> marker = $list_accounts -> marker;
				$accounts_list[$list_accounts -> id] = $tmp;
			}
		}
		return $accounts_list;
	}

	/**
	 * Метод добавления ЛС клиенту
	 *
	 * @param int $id идентификатор клиента
	 *
	 * @author Ермашевский Денис
	 * @return array $accounts_list
	 *
	 */
	function add_client_accounts2($id)
	{
		$counter = 0;
		$id = (int) $id;
		$accounts_list = array();
		$this -> db -> select('id_service');
		$this -> db -> from('clients_accounts');
		$this -> db -> where('id_clients', $id);
		$accounts = $this -> db -> get();
		//$account_listing = array();
		foreach ($accounts -> result() as $row) {
			$row -> id_service = $row -> id_service;


		$this -> db -> select('*');
		$this -> db -> from('services');
		//$this -> db -> where('id', $row->id_service);
		$rs_list = $this -> db -> get();

		if (0 < $rs_list -> num_rows) {
			foreach ($rs_list -> result() as $list_accounts) {
				$tmp = new Services_model();
				$tmp -> id = $list_accounts -> id;
				$tmp -> service_description = $list_accounts -> service_description;
				$tmp -> marker = $list_accounts -> marker;
				$accounts_list[$list_accounts -> id] = $tmp;
			}
		}
		}
		return $accounts_list;
	}

	/**
	 * Метод начислений за период
	 *
	 * @param string $start_date_month начало периода начислений
	 * @param string $end_date_month   конец периода начислений
	 *
	 * @deprecated
	 * @author Ермашевский Денис
	 * @return mixed $dataset
	 */
	function getCustomerServicesFromPeriod($start_date_month, $end_date_month)
	{

		$sql_query = 'SELECT customer_service.id as id_assortment_customer, `id_group` , `id_account` , `payment_name` , `datepicker1` , `end_date` ,`period`, `tariffs`.`price`
	    FROM customer_service
	    INNER JOIN tariffs ON tariffs.id = customer_service.tariffs
	    WHERE (
	    `datepicker1` <= "' . $start_date_month . '"
	    OR `datepicker1`
	    BETWEEN "' . $start_date_month . '"
	    AND "' . $end_date_month . '"
	    )
	    AND (
	    end_date IS NULL
	    OR end_date > "' . $end_date_month . '"
	    OR end_date
	    BETWEEN "' . $start_date_month . '"
	    AND "' . $end_date_month . '"
	    )';

		$result = $this -> db -> query($sql_query);

		$dataset = array();
		foreach ($result -> result() as $row) {
			$tmp = new Services_model();
			$tmp -> id_assortment_customer = $row -> id_assortment_customer;
			$tmp -> id_group = $row -> id_group;
			$tmp -> id_account = $row -> id_account;
			$tmp -> payment_name = $row -> payment_name;
			$tmp -> datepicker1 = $row -> datepicker1;
			$tmp -> end_date = $row -> end_date;
			$tmp -> price = $row -> price;
			$tmp -> period = $row -> period;
			$tmp -> start_date_month = $start_date_month;
			$tmp -> end_date_month = $end_date_month;
			$dataset[$row -> id_assortment_customer] = $tmp;
		}
		return $dataset;
	}

	/**
	 * Метод производит начисления за период у необходимых номеклатур
	 *
	 * @param array $data массив данных
	 *
	 * @deprecated
	 * @author Ермашевский Денис
	 * @return null
	 */
	function addCustomerPayments($data)
	{

		$sql = 'INSERT INTO customer_payments (id_assortment_customer,id_account,amount,period_start,period_end) VALUES
		("' . $data['id_assortment_customer'] . '","' . $data['id_account'] . '","' . $data['price'] . '","' . $data['start_date_month'] . '","' . $data['end_date_month'] . '")';
		$this -> db -> query($sql);
	}

	/**
	 * Метод возвращает номеклатуры с ресурсами и освобождает их если период действия закончился
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function getAssortmentWithResources()
	{

		$assortmentWithResource = array();

		$this -> db -> select('id, end_date');
		$this -> db -> from('customer_service');
		$this -> db -> where('resources !=', ' ');
		$this -> db -> where('end_date !=', ' ');

		$IDs = $this -> db -> get();
		$n = 1;
		foreach ($IDs -> result() as $row) {
			$tmp = new Services_model();
			$tmp -> id = $row -> id;
			$tmp -> end_date = $row -> end_date;
			$assortmentWithResource[$row -> id] = $tmp;
			if ($row -> end_date < now()) {
				$this -> deleteAssortmentItem($row -> id);
			}
		}
		//return $assortmentWithResource;
	}

	/**
	 * Метод служебный для быстрого внесения ресурсов в БД
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function setPhoneNumberPool()
	{

		for ($i = 00; $i < 100; $i ++ ) {
			$n = sprintf('%02d', $i);

			echo $sql = 'insert into free_phone_pool (resources,type,status) VALUES ("784527466' . $n . '","phone","free")';
			//$this->db->query($sql);
		}
	}

	/**
	 * Метод удаления начислений по идентификатору
	 *
	 * @param int $id идентификатор
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function delCustomerPayments($id)
	{

		$this -> db -> where('id', $id);
		$this -> db -> delete('customer_payments');
	}

	/**
	 * Метод поиска номеклатур для начисления МТС (импорт из CSV)
	 *
	 * @param string $resource   ресурс
	 * @param string $assortment номеклатура
	 * @param string $amount     сумма
	 * @param string $date       дата (период)
	 *
	 * @author Ермашевский Денис
	 * @return array $myarray
	 */
	function searchAssortmentID($resource, $assortment, $amount, $date)
	{
		$myarray = array();

		$this -> db -> select('customer_service.id, customer_service.id_account, customer_service.payment_name, customer_service.uniq_id');
		$this -> db -> from('free_phone_pool');
		$this -> db -> join('customer_service', 'customer_service.resources = free_phone_pool.id');
		$this -> db -> where('free_phone_pool.resources', $resource);
		$data = array();
		$id_list = $this -> db -> get();
		if (0 < $id_list -> num_rows) {
		foreach ($id_list -> result() as $row):
			$tmp = new Services_model();
			$tmp -> id = $row -> id;
			//$tmp -> id_group = $row -> id_group;
			$tmp -> id_account = $row -> id_account;
			$tmp -> payment_name = $row -> payment_name;
			$tmp -> uniq_id = $row -> uniq_id;
			$tmp -> assortment = $assortment;
			$tmp -> amount = $amount;
			$tmp -> date = $date;
			$myarray[$row -> id] = $tmp;
		endforeach;
		return $myarray;
		}else{
			$log = "Идентификатор с номером ".$resource." не найден ни у одного клиента.";
				return $data['error']=$log;
		}

	}

	/**
	 * Метод поиска номеклатур для начисления IP (импорт из CSV)
	 *
	 * @param string $identifier ресурс
	 * @param string $assortment номеклатура
	 * @param string $amount     сумма
	 * @param string $date       дата (период)
	 *
	 * @author Ермашевский Денис
	 * @return array $myarray
	 */
	function searchAssortmentIDIP($identifier, $assortment, $amount, $date)
	{
		$myarray = array();

		$this -> db -> select('customer_service.id, customer_service.id_account, customer_service.payment_name, customer_service.uniq_id');
		$this -> db -> from('customer_service');
		$this -> db -> where('customer_service.identifier', $identifier);

		$data = array();
		$id_list = $this -> db -> get();
		if (0 < $id_list -> num_rows) {
		foreach ($id_list -> result() as $row):
			$tmp = new Services_model();
			$tmp -> id = $row -> id;
			//$tmp -> id_group = $row -> id_group;
			$tmp -> id_account = $row -> id_account;
			$tmp -> payment_name = $row -> payment_name;
			$tmp -> uniq_id = $row -> uniq_id;
			$tmp -> assortment = $assortment;
			$tmp -> amount = $amount;
			$tmp -> date = $date;
			$myarray[$row -> id] = $tmp;

		endforeach;
		return $myarray;
		}else{
			$log = "Идентификатор ".$identifier." не найден ни у одного клиента.";
				return $data['error']=$log;
		}
	}

	/**
	 * Метод построения массива номеклатур для начисления (импорт из CSV)
	 *
	 * @param string $uniq_id    ресурс
	 * @param string $assortment номеклатура
	 * @param string $amount     сумма
	 * @param string $date       дата (период)
	 *
	 * @author Ермашевский Денис
	 * @return mixed $mylist
	 */
	function insertAssortmentID($uniq_id, $assortment, $amount, $date)
	{
		$mylist = array();
		$this -> db -> select('customer_service.id,customer_service.id_account,clients_accounts.id_clients,payment_name');
		$this -> db -> from('customer_service');
		$this -> db -> join('clients_accounts', 'clients_accounts.id = customer_service.id_account');
		$this -> db -> where('customer_service.uniq_id', $uniq_id);
		$this -> db -> where('customer_service.payment_name', $assortment);
		$list = $this -> db -> get();
		foreach ($list -> result() as $row):
			$tmp = new Services_model();
			$tmp -> id = $row -> id;
			$tmp -> id_clients = $row -> id_clients;
			$tmp -> id_account = $row -> id_account;
			$tmp -> payment_name = $row -> payment_name;
			$tmp -> amount = $amount;
			$tmp -> date = $date;
			$mylist[$row -> id] = $tmp;
		endforeach;
		return $mylist;
	}

	/**
	 * Метод начислений (импорт из CSV)
	 *
	 * @param string $id_assortment_customer ресурс
	 * @param string $id_account             номеклатура
	 * @param string $amount                 сумма
	 * @param string $date                   дата (период)
	 *
	 * @author Ермашевский Денис
	 * @return 1||0
	 */
	function addAmountToAssortment($id_assortment_customer, $id_client, $id_account, $amount, $date)
	{
		$this -> db -> select('');
		$this -> db -> from('customer_payments');
		$this -> db -> where('id_assortment_customer', $id_assortment_customer);
		$this -> db -> where('period_start', $date);
		$count = $this -> db -> count_all_results();
		echo $id_account;
		$n = 0;
		if ($count === 0) {
			$end_period = new DateTime($date);
			$start_period = new DateTime($date);

			$date_end = $end_period -> format('Y-m-t');
			$date_start = $start_period -> format('Y-m-d');

			$sql = 'insert into customer_payments  (id_assortment_customer,id_account, amount, period_start, period_end, id_client) VALUES (' . $id_assortment_customer . ',"' . $id_account . '","' . $amount . '","' . $date_start . '","' . $date_end . '",' . $id_client . ')';
			$res = $this -> db -> query($sql);
			return 1;
		} else {
			$log = $id_assortment.'=>'.$id_account.'=>'.$id_client.'=>'.$amount.'=>'.$date;
			write_file(APPPATH.'/logs/no_add_payments_billing.php', $log,'a');
		}
	}

	/**
	 * Метод начислений по ТК ДН (импорт из CSV)
	 *
	 * @param string $id_assortment_customer ресурс
	 * @param string $id_account             номеклатура
	 * @param string $amount                 сумма
	 * @param string $date                   дата (период)
	 *
	 * @author Ермашевский Денис
	 * @return 1||0
	 */
	function addAmountToAssortmentTKIP($id_assortment_customer, $id_client, $id_account, $amount, $date)
	{
		$this -> db -> select('');
		$this -> db -> from('customer_payments');
		$this -> db -> where('id_assortment_customer', $id_assortment_customer);
		$this -> db -> where('period_start', $date);
		$this -> db -> where('amount', $amount);
		$count = $this -> db -> count_all_results();
		echo $id_account;
		$n = 0;
		if ($count === 0) {
			$end_period = new DateTime($date);
			$start_period = new DateTime($date);

			$date_end = $end_period -> format('Y-m-t');
			$date_start = $start_period -> format('Y-m-d');

			$sql = 'insert into customer_payments  (id_assortment_customer,id_account, amount, period_start, period_end, id_client) VALUES (' . $id_assortment_customer . ',"' . $id_account . '","' . $amount . '","' . $date_start . '","' . $date_end . '",' . $id_client . ')';
			$res = $this -> db -> query($sql);
			return 1;
		} else {
			$log = $id_assortment.'=>'.$id_account.'=>'.$id_client.'=>'.$amount.'=>'.$date."\n";
			write_file(APPPATH.'/logs/no_add_payments_billing.php', $log,'a');
		}
	}

}

//End of file services_model.php
//Location: ./models/services_model.php
