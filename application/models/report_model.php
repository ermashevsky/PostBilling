<?php
/**
 * Clients_model
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Models.Report_Model
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://www.ci2.lcl/
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Класс Report содержит методы работы  с отчетами
 *
 * @category PHP
 * @package  Models.Clients_Model
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Report_model extends CI_Model
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
	 * Унифицированный метод-конструктор __construct()
	 *
	 * @author Ермашевский Денис
	 */
	function __construct()
	{
		parent::__construct();
	}


	function get_mts_full()
	{

		$this -> db -> select('bindings_name, accounts, SUM( amount ) AS `amount`');
		$this -> db -> from('customer_payments');
		$this -> db -> join('clients_accounts', 'clients_accounts.id = customer_payments.id_account');
		$this -> db -> where('clients_accounts.id_service', '3');
		$this -> db -> group_by('accounts');
		$res = $this -> db -> get();
		return $res -> result();
	}

	function get_full_report_for_period($start_date=NULL,$end_date=NULL)
	{

		$start = date('Y-m-d',  strtotime($start_date));
		$end = date('Y-m-d',strtotime($end_date));
		$dateRange = "period_start BETWEEN '$start' AND '$end'";

		$this -> db -> select('bindings_name, accounts, SUM( amount ) AS `amount`');
		$this -> db -> from('customer_payments');
		$this -> db -> join('clients_accounts', 'clients_accounts.id = customer_payments.id_account');
		$this -> db -> where($dateRange, NULL, FALSE);
		$this -> db -> group_by('accounts');
		$res = $this -> db -> get();
		return $res -> result();
	}

}

//End of file report_model.php
//Location: ./models/report_model.php