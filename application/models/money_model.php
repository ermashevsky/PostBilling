<?php
/**
 * Money_Models
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Models.Money_Models
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://www.ci2.lcl/
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(E_ALL);
/**
 * Класс Money содержит методы начислений за услуги
 *
 * @category PHP
 * @package  Models.Money_Models
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Money_model extends CI_Model
{
	var $id;
	/**
	 * Унифицированный метод-конструктор __construct()
	 *
	 * @author Ермашевский Денис
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->helper('file');
		$this->load->helper('date');
	}

	/**
	 * Метод получения массива дат начала действия номеклатур
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	public function getStartPeriod()
	{

		$this -> db -> select('id,datepicker1,end_date,period');
		$this -> db -> from('customer_service');
		$this->	 db -> where('end_date', null);
		$this -> db -> order_by('datepicker1', 'asc');
		$res = $this -> db -> get();
		$data = array();
		if (0 < $res -> num_rows) {

			foreach ($res -> result() as $row) {
				$money = new Money_model();
				$money -> id = $row -> id;
				$money -> end_date = $row -> end_date;
				$money -> period = $row -> period;
				$money -> datepicker1 = $row -> datepicker1;
				$data[$money -> id] = $money;
			}
		}
		return $data;
	}

	public function getPeriodData($periodStart,$periodEnd)
	{
		$this -> db -> select('customer_service.id,id_account,clients_accounts.id_clients,datepicker1,end_date,price,period');
		$this -> db -> from('customer_service');
		$this -> db -> join('tariffs','tariffs.id=tariffs','inner');
		$this -> db -> join('clients_accounts', 'clients_accounts.id = customer_service.id_account');
		$this -> db -> where('tariffs !=',FALSE);
		$this -> db -> where('`datepicker1` BETWEEN "'.$periodStart.'" and "'.$periodEnd.'"', NULL, FALSE);
		$this -> db -> order_by('datepicker1', 'asc');
		$res = $this -> db -> get();
		$data = array();
		if (0 < $res -> num_rows) {

			foreach ($res -> result() as $row) {
//				$money = new Money_model();
//				$money -> id = $row -> id;
//				$money -> end_date = $row -> end_date;
//				$money -> period = $row -> period;
//				$money -> datepicker1 = $row -> datepicker1;
//				$data[$money -> id] = $money;
				$money = new Money_model();
				$money -> id = $row -> id;
				$money -> id_account = $row -> id_account;
				$money -> end_date = $row -> end_date;
				$money -> period = $row -> period;
				$money -> datepicker1 = $row -> datepicker1;
				$money -> price = $row -> price;
				$money -> id_clients = $row -> id_clients;
				$data[$money -> id] = $money;
			}
		}
		return $data;
	}

	public function getStartPartialPeriod()
	{

		$this -> db -> select('customer_service.id,id_account,clients_accounts.id_clients,datepicker1,end_date,price,period');
		$this -> db -> from('customer_service');
		$this -> db -> join('tariffs','tariffs.id=tariffs','inner');
		$this -> db -> join('clients_accounts', 'clients_accounts.id = customer_service.id_account');
		$this -> db -> order_by('datepicker1', 'asc');
		$this->	 db -> where('end_date IS NOT NULL');
		$this -> db -> where('tariffs IS NOT NULL');
		$res = $this -> db -> get();
		$data = array();
		if (0 < $res -> num_rows) {

			foreach ($res -> result() as $row) {
				$money = new Money_model();
				$money -> id = $row -> id;
				$money -> id_account = $row -> id_account;
				$money -> end_date = $row -> end_date;
				$money -> period = $row -> period;
				$money -> datepicker1 = $row -> datepicker1;
				$money -> price = $row -> price;
				$money -> id_clients = $row -> id_clients;
				$data[$money -> id] = $money;
			}
		}
		return $data;
	}

	/**
	 * Метод проверки периода номеклатур
	 *
	 * @param int    $assortment_id идентификатор номеклатуры
	 *
	 * @param string $periodStart   начальный период номеклатуры
	 *
	 * @param string $periodEnd     конец действия номеклатуры
	 *
	 * @author Ермашевский Денис
	 * @return array $arr;
	 */
	function checkPeriod($id, $start_month, $end_month, $price)
	{
		$this -> db -> select('*');
		$this -> db -> from('customer_payments');
		$this -> db -> where('period_start', $start_month);
		$this -> db -> where('period_end', $end_month);
		$this -> db -> where('id_assortment_customer', $id);
		$this -> db -> where('amount', $price);
		$counter = $this -> db -> count_all_results();
		if ($counter === 0) {
			return $arr[$counter] = $id;
		}
	}

	/**
	 * Метод начислений по номеклатуре
	 *
	 * @param int    $assortment_id идентификатор номеклатуры
	 *
	 * @param string $periodStart   начальный период номеклатуры
	 *
	 * @param string $periodEnd     конец действия номеклатуры
	 *
	 * @author Ермашевский Денис
	 * @return null
	 */
	function setPayment($id, $id_account, $price, $start_month, $end_month, $id_clients)
	{
			$sql = "insert into customer_payments (id_assortment_customer,id_account,amount,period_start,period_end,id_client) VALUES (".$id.", ".$id_account.", ".$price.", '".$start_month."', '".$end_month."', ".$id_clients.")";
			$this -> db -> query($sql);
			$this -> db ->affected_rows();
	}

	function addPaymentsCSV($data)
	{
		$mydata = array();
		foreach ($data as $data):
			$this -> db -> select('id,id_clients');
			$this -> db -> from('clients_accounts');
			$this -> db -> where('accounts', $data['account']);
			$res = $this -> db -> get();
			$row = $res->row_array();
			if (0 < $res -> num_rows) {
			$timezone = new DateTimeZone('UTC');
			$dateTime = DateTime::createFromFormat('d.m.yy', $data['date'], $timezone);
			$dateTime -> format('Y-m-d');
			$sql = "INSERT INTO customer_encashment (id_account, amount, date, time, id_client) VALUES ('" . $row['id'] . "','" . $data['amount'] . "','" . $dateTime->format('Y-m-d') . "','" . $data['time'] . "','" . $row['id_clients'] . "')";
			$this -> db -> query($sql);
			}else{
				if($data['account']==''){
				$log = "Лицевой счет ".$data['account']." отсутствует в файле импорта. Клиент: ".$data['client']."  Дата: ".$data['date']." Время: ".$data['time']." Сумма: ".$data['amount'];
				$mydata[$data['account']]=$log;
				}else{
				$log = "Лицевой счет ".$data['account']." отсутствует в постбиллинге. Клиент: ".$data['client']."  Дата: ".$data['date']." Время: ".$data['time']." Сумма: ".$data['amount'];
				$mydata[$data['account']]=$log;

				}
			}
		endforeach;

		return $mydata;
	}

//	function getAllCustomerPayments()
//	{
//			$this -> db -> select('*');
//			$this -> db -> from('customer_encashment');
//			$this -> db -> join('');
//			$res = $this -> db -> get();
//			$row = $res->row_array();
//			if (0 < $res -> num_rows) {
//
//			}
//	}

	function getAllPayById($id=null)
	{
			$this -> db -> select('"no" as discount,customer_encashment.id,customer_encashment.date,customer_encashment.amount,customer_encashment.id_account,
			pay_comments.id as id_comment, pay_comments.pay_id as pay_id,pay_comments.comment', FALSE);
			$this -> db -> from('customer_encashment');
			$this -> db -> join('pay_comments','pay_comments.pay_id = customer_encashment.id','left');
			$this -> db -> where('customer_encashment.id_account',$id);
			$this -> db -> get();
			$query1 = $this->db->last_query();
			
			$this -> db -> select('"yes" as discount, customer_discounts.id,customer_discounts.date,customer_discounts.amount,customer_discounts.id_account,
			pay_comments.id as id_comment, pay_comments.pay_id as pay_id,pay_comments.comment', FALSE);
			$this -> db -> from('customer_discounts');
			$this -> db -> join('pay_comments','pay_comments.pay_id = customer_discounts.id','left');
			$this -> db -> where('customer_discounts.id_account',$id);
			$this -> db -> get();
			$query2 = $this->db->last_query();
			$query = $this->db->query($query1." UNION ALL ".$query2);
			$query->result();
			$data = array();
			if (0 < $query -> num_rows) {
				foreach ($query->result() as $row):
				$money = new Money_model();
				$money -> id = $row -> id;
				$date = new DateTime($row->date);
				$money -> date = $date->format('d.m.Y');
				$money -> amount = $row -> amount;
				$money -> id_account = $row -> id_account;
				$money -> id_comment = $row -> id_comment;
				$money -> comment = $row -> comment;
				$money ->pay_id = $row -> pay_id;
				$money -> discount = $row -> discount;
				$data[$money -> id] = $money;
			endforeach;
			}
			return $data;
	}

	function getPayComment($id=null)
	{
			$this->db->where('id',$id);
			$row = $this->db->get('pay_comments')->row_array();
			return $row['comment'];
	}

	function getPayById($id=null)
	{
			$this -> db -> select('customer_encashment.id,customer_encashment.date,customer_encashment.amount,customer_encashment.id_account,
			pay_comments.id as id_comment, pay_comments.pay_id as pay_id,pay_comments.comment');
			$this -> db -> from('customer_encashment');
			$this -> db -> join('pay_comments','pay_comments.pay_id = customer_encashment.id','left');
			$this -> db -> where('customer_encashment.id',$id);
			$res = $this -> db -> get();
			$data = array();

			if (0 < $res -> num_rows) {
				foreach ($res -> result() as $row):
				$money = new Money_model();
				$money -> id = $row -> id;
				$date = new DateTime($row->date);
				$money -> date = $date->format('d.m.Y');
				$money -> amount = $row -> amount;
				$money -> id_account = $row -> id_account;
				$money -> id_comment = $row -> id_comment;
				$money -> comment = $row -> comment;
				$money ->pay_id = $row -> pay_id;
				$data[$money -> id] = $money;
			endforeach;
			}
			return $data;
	}

	function deletePayById($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('customer_encashment');
	}

	function deletePayComment($id_comment)
	{
		$this->db->where('id', $id_comment);
		$this->db->delete('pay_comments');
	}

	function addPay($id_account=null,$date=null,$amount=null,$id_client=null,$comment=null)
	{
		$date = new DateTime($date);
		$formated_date = $date->format('Y-m-d');
		$data = array(
			'id_account' => $id_account,
			'amount' => $amount,
			'date' => $formated_date,
			'id_client'=>$id_client
			);
		$this->db->insert('customer_encashment', $data,false);

		if(isset($comment)):
		$last_id = $this->db->insert_id();

		$data_comment = array(
			'pay_id' => $last_id,
			'comment' => $comment
		);

		$this->db->insert('pay_comments',$data_comment,false);
		endif;

		//print_r($data);

	}

	function editPay($id=null,$date=null,$amount=null)
	{
		$date = new DateTime($date);
		$formated_date = $date->format('Y-m-d');
		$data = array(
			'amount' => $amount,
			'date' => $formated_date);
		$this->db->where('id', $id);
		$this->db->update('customer_encashment', $data);
	}

	function editPayComment($id_comment=null,$comment=null)
	{
		$data = array('comment' => $comment);
		$this->db->where('id', $id_comment);
		$this->db->update('pay_comments', $data);
	}
	
	
	function addDiscount($id_account=null,$date=null,$amount=null,$id_client=null,$comment=null)
	{
		$date = new DateTime($date);
		$formated_date = $date->format('Y-m-d');
		$data = array(
			'id_account' => $id_account,
			'amount' => $amount,
			'date' => $formated_date,
			'id_client'=>$id_client
			);
		$this->db->insert('customer_discounts', $data,false);

		if(isset($comment)):
		$last_id = $this->db->insert_id();

		$data_comment = array(
			'pay_id' => $last_id,
			'comment' => $comment
		);

		$this->db->insert('pay_comments',$data_comment,false);
		endif;

		//print_r($data);

	}
	
	function addAdjustAmount($id_account=null,$date=null,$amount=null,$id_client=null,$comment=null)
	{
		$date = new DateTime($date);
		$formated_date = $date->format('Y-m-d');
		$data = array(
			'id_account' => $id_account,
			'amount' => $amount,
			'date' => $formated_date,
			'id_client'=>$id_client
			);
		$this->db->insert('customerAdjustAmount', $data,false);

		if(isset($comment)):
		$last_id = $this->db->insert_id();

		$data_comment = array(
			'pay_id' => $last_id,
			'comment' => $comment
		);

		$this->db->insert('pay_comments',$data_comment,false);
		endif;

		//print_r($data);

	}

	function getPartialPeriods($id_assortment=null,$id_account=null,$amount=null,$periodStart=null,$periodEnd=null,$id_client=null)
	{
			$sql = "insert into customer_payments (id_assortment_customer,id_account,amount,period_start,period_end,id_client) VALUES (" . $id_assortment . "," . $id_account . "," . $amount . ",'" . $periodStart . "','" . $periodEnd . "','" . $id_client . "')";
			$this -> db -> query($sql);

	}

	function checkDebt()
	{
	$this -> db -> select('clients_accounts.bindings_name AS name, clients_accounts.accounts AS account, clients_accounts.id AS id_account, SUM( customer_payments.amount ) AS amount, IFNULL( ROUND( payment.payments, 2 ) , "00.00" ) AS payment',FALSE);
	$this -> db -> from('clients');
	$this -> db -> join('clients_accounts', 'clients_accounts.id_clients =  clients.id','left');
	$this -> db -> join('customer_payments', 'customer_payments.id_account =  clients_accounts.id','left');
	$this -> db -> join('(SELECT * , ROUND( SUM( REPLACE( amount,  "," , "." ) ) , 2 ) AS payments
		FROM customer_encashment
		GROUP BY id_account
		) AS payment','payment.id_account =  clients_accounts.id',FALSE);
	$this-> db ->group_by('clients_accounts.accounts');

	$res = $this -> db -> get();
	$data = array();

			if (0 < $res -> num_rows) {
			foreach ($res -> result_array() as $row):
//				$money = new Money_model();
//				$money -> id_account = $row -> id_account;
//				$money -> name = $row -> name;
//				$money -> account = $row -> account;
//				$money -> amount = $row -> amount;
//				$money -> payment = $row -> payment;
//				$data[$money -> id_account] = $money;
			endforeach;
			}
			return $res->result_array();

	}

	function searchAccountByIdentifier($identifier, $balance, $period, $source_selector)
	{
		//	SELECT  `bindings_name` ,  `accounts` , 35.30 AS ostatok
		//FROM  `clients_accounts`
		//INNER JOIN customer_service ON customer_service.id_account = clients_accounts.id
		//WHERE customer_service.identifier =  'АвтоЛайф'
		$balance = str_replace(',', ".", $balance);
		$this -> db -> select('clients_accounts.id as id, bindings_name, accounts,' . $balance . ' as `balance`', FALSE);
		$this -> db -> from('clients_accounts');
		$this -> db -> join('customer_service', 'customer_service.id_account = clients_accounts.id', 'left');
		$this -> db -> where('customer_service.identifier', $identifier);
		$res = $this -> db -> get();
		$data = array();

		if (0 < $res -> num_rows) {
			foreach ($res -> result_array() as $row):
				if ($row['bindings_name'] != 'ТТК-IP'):
				if ($row['bindings_name'] != 'Собственные'):
					$money = new Money_model();
					$money -> insert_date = date('d.m.Y');
					$money -> id_account = $row['id'];
					$money -> bindings_name = $row['bindings_name'];
					$money -> account = $row['accounts'];
					$money -> balance = $row['balance'];
					$money -> identifier = $identifier;
					$money -> period = $period;
					$money -> source_type = $source_selector;
					$data[$money -> id_account] = $money;
				endif;
				endif;
			endforeach;
			$this -> insertCompareData($data);
		}

		return $data;
	}

	function insertCompareData($data)
	{
		foreach ($data as $value):

			$this -> db -> set('insert_date', $value -> insert_date);
			$this -> db -> set('id_account', $value -> id_account);
			$this -> db -> set('bindings_name', $value -> bindings_name);
			$this -> db -> set('account', $value -> account);
			$this -> db -> set('balance', $value -> balance);
			$this -> db -> set('period', $value -> period);
			$this -> db -> set('identifier', $value -> identifier);
			$this -> db -> set('source_type', $value -> source_type);

		endforeach;
		$this -> db -> insert('compare_balance');
	}

	function getPostBillingData($period)
	{
		$this -> db -> select('id_account', FALSE);
		$this -> db -> from('compare_balance');
		$this -> db -> group_by('id');
		$ids = $this -> db -> get();
		$myarr = array();
		$count = 0;
		foreach ($ids -> result() as $val):
			$myarr[$count ++] = $val -> id_account;
		endforeach;

		if (0 < $ids -> num_rows) {
			$this -> db -> select('clients_accounts.bindings_name AS bindings_name, clients_accounts.accounts AS account, clients_accounts.id AS id_account, IFNULL( SUM( customer_payments.amount ),"0.00") AS amount, IFNULL( ROUND( payment.payments, 2 ) , "0.00" ) AS payment', FALSE);
			$this -> db -> from('clients');
			$this -> db -> join('clients_accounts', 'clients_accounts.id_clients =  clients.id', 'left');
			$this -> db -> join('customer_payments', 'customer_payments.id_account =  clients_accounts.id', 'left');
			$this -> db -> join('(SELECT * , ROUND( SUM( REPLACE( amount,  "," , "." ) ) , 2 ) AS payments
		FROM customer_encashment
		GROUP BY id_account
		) AS payment', 'payment.id_account =  clients_accounts.id', FALSE);

			$this -> db -> where_in('clients_accounts.id', $myarr);

			$this -> db -> group_by('clients_accounts.id');
			$res = $this -> db -> get();
			$data = array();

			if (0 < $res -> num_rows) {
				foreach ($res -> result() as $row):
					$money = new Money_model();
					$money -> id_account = $row -> id_account;
					$money -> bindings_name = $row -> bindings_name;
					$money -> account = $row -> account;
					$money -> insert_date = date('d.m.Y');
					$money -> period = $period;
					$money -> amount = $row -> amount;
					$money -> payment = $row -> payment;
					(double)$money -> postbilling_amount = (double)$row -> amount - (double)$row -> payment;
					$data[$money -> id_account] = $money;
					$this -> insertCompareDataPostBilling($data);
				endforeach;

			}
		}
		return $data;
	}


	function insertCompareDataPostBilling($data)
	{
		foreach ($data as $value):

			$this -> db -> set('insert_date', $value -> insert_date);
			$this -> db -> set('id_account', $value -> id_account);
			$this -> db -> set('bindings_name', $value -> bindings_name);
			$this -> db -> set('account', $value -> account);
			$this -> db -> set('balance', $value -> postbilling_amount);
			$this -> db -> set('period', $value -> period);

		endforeach;
		$this -> db -> insert('compare_balance_pb');
	}

//	SELECT  `id` ,  `id_account` ,  `account` , GROUP_CONCAT(  `identifier` ) ,  `bindings_name` ,  `period` , GROUP_CONCAT(  `source_type` ) , SUM(  `balance` ) AS billings_amount
//FROM  `compare_balance`
//GROUP BY  `account`
//ORDER BY id


//SELECT  `compare_balance`.`id` ,  `compare_balance`.`id_account` ,  `compare_balance`.`account` , GROUP_CONCAT(  `identifier` ) ,  `compare_balance`.`bindings_name` ,  `compare_balance`.`period` , GROUP_CONCAT( `source_type` ) , SUM(  `compare_balance`.`balance` ) AS billings_amount, compare_balance_pb.balance AS postbilling_amount
//FROM  `compare_balance`
//INNER JOIN compare_balance_pb ON compare_balance_pb.id_account = compare_balance.id_account
//GROUP BY  `account`
//ORDER BY id

	function buildCompareDataTable()
	{
		$this -> db -> select('compare_balance`.`id` ,  `compare_balance`.`id_account` ,  `compare_balance`.`account` , GROUP_CONCAT(  `identifier` ) as identifier,  `compare_balance`.`bindings_name` ,  `compare_balance`.`period` , GROUP_CONCAT( `source_type` ) as source_type, (SUM(  `compare_balance`.`balance` )*-1) AS billings_amount, compare_balance_pb.balance AS postbilling_amount');
		$this -> db -> from('compare_balance');
		$this -> db -> join('compare_balance_pb', 'compare_balance_pb.id_account = compare_balance.id_account', 'left');
		$this -> db -> group_by('account');
		$this -> db -> order_by('id', 'asc');
		$res = $this -> db -> get();
		$data = array();

		if (0 < $res -> num_rows) {

			foreach ($res -> result() as $row):
				if($row->billings_amount != $row -> postbilling_amount):
				$money = new Money_model();
				$money -> id_account = $row -> id_account;
				$money -> identifier = $row -> identifier;
				$money -> bindings_name = $row -> bindings_name;
				$money -> account = $row -> account;
				$money -> period = $row -> period;
				$money -> source_type = $row -> source_type;
				$money -> billings_amount = $row -> billings_amount;
				$money -> postbilling_amount = $row -> postbilling_amount;
				$data[$money -> id_account] = $money;
				endif;
			endforeach;
		}
		return $data;
	}

	function checkAccrualForThePeriod($id_assortment_customer, $id_account, $id_clients, $startDateAccrual, $endDateAccrual, $amountAccrual){
		date_default_timezone_set('Europe/Kaliningrad');
		$dateStart = DateTime::createFromFormat('d.m.Y', $startDateAccrual);
		$dateEnd = DateTime::createFromFormat('d.m.Y', $endDateAccrual);

		/* Привожу диапазон дат к первому числу месяца */
		$periodStart = $dateStart -> format('Y-m-d');
		$periodEnd = $dateEnd -> format('Y-m-d');
		
		$this -> db -> select('*');
		$this -> db -> from('customer_payments');
		$this -> db -> where('period_start', $periodStart);
		$this -> db -> where('period_end', $periodEnd);
		$this -> db -> where('id_assortment_customer', $id_assortment_customer);
		$this -> db -> where('amount', $amountAccrual);
		$counter = $this -> db -> count_all_results();
		if ($counter === 0) {
			//return $arr[$counter] = $id_clients;
			$this -> setPayment($id_assortment_customer, $id_account, $amountAccrual, $periodStart, $periodEnd, $id_clients);
			return 1;
		}else{
			return 0;
		}
	}
	
}

//End of file money_model.php
//Location: ./models/money_model.php