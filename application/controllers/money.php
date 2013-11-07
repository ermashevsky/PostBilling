<?php

/**
 * Money
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Controllers.Money
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://www.ci2.lcl/
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(1);
require_once APPPATH . "/third_party/PHPExcel.php";
include (APPPATH . '/third_party/log4php/Logger.php');
$config_log_file = APPPATH . 'config/config_log4php.xml';
Logger::configure($config_log_file);

/**
 * Класс Money содержит методы начислений за услуги
 *
 * @category PHP
 * @package  Controllers.Money
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Money extends CI_Controller
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
		$this -> load -> library('getcsv');

		$this -> load -> database();
		$this -> load -> helper('url', 'form', 'date');
		$this -> load -> helper('file');
		$this -> load -> helper('number');
		$this -> breadcrumbs = array();
		$this -> breadcrumbs[] = anchor('', $this -> config -> item('breadcrumbs_index'));
		//$this->output->enable_profiler(TRUE);
	}

	function index()
	{
		$this -> load -> view('header');
		$this -> load -> view('moneyAccruals');
		$this -> load -> view('left_sidebar');
	}

	/**
	 * Метод начислений (новый), с расчетом прошедших периодов
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function getPeriod2()
	{
		$this -> load -> model('money_model');

		$datePeriodStart = trim($this -> input -> post('dateStart'));
		$datePeriodEnd = trim($this -> input -> post('dateEnd'));

		$dateStart = DateTime::createFromFormat('m/y', $datePeriodStart);
		$dateEnd = DateTime::createFromFormat('m/y', $datePeriodEnd);

		/* Привожу диапазон дат к первому числу месяца */
		$periodStart = $dateStart -> format('Y-m-01');
		$periodEnd = $dateEnd -> format('Y-m-01');

		/* Получаю все начисляемые номенклатуры в указаном диапазоне дат */
		$data = $this -> money_model -> getPeriodData($periodStart, $periodEnd);
		$datacheck = array();
		$my_data = array();
		$user = $this -> ion_auth -> user() -> row();
		$mdc = new LoggerMDC();
		foreach ($data as $date) {

			if (is_null($date -> end_date) || $date -> end_date == '') {
				$modify_end_month = strtotime('-1 month', now());
				$end_month = date('Y-m-t', $modify_end_month); ///
			} else {
				$end_month = $date -> end_date;
			}

			if (date('Y-m-d', strtotime($date -> datepicker1)) == date('Y-m-d', strtotime($end_month))) {

				$datacheck = $this -> money_model -> checkPeriod($date -> id, $date -> datepicker1, $date -> end_date, $date -> price);

				if ($datacheck == $date -> id) {
					$mdc -> put('username', 'PaymentEveryMonthRobot');
					$this -> log -> info('Были произведены начисления для номенклатуры ' . $date -> id . ' на ЛС (ID) ' . $date -> id_account . ' сумма начисления:' . $date -> price . ' в период с ' . $date -> datepicker1 . ' по ' . $date -> end_date . ' у клиента c ID ' . $date -> id_clients);
					echo 'Произведены начисления для номенклатуры' . $value['id'], ' на ЛС (ID) ' . $value['id_account'] . ' сумма начисления:' . $value['price'] . ' в период с ', $value['start_month'] . ' по ' . $value['end_month'] . ' у клиента c ID ' . $value['id_client'];
					$this -> money_model -> setPayment($date -> id, $date -> id_account, $date -> price, $date -> datepicker1, $date -> end_date, $date -> id_clients);
				} else {
//							$mdc->put('username','PaymentEveryMonthRobot');
//							$this -> log -> warn('Номеклатура с ID ' . $value['id'] . ' уже имеет начисления за период с '.$value['start_month'].' по '.$value['end_month'].' сумма начисления:'.$value['price']);
					echo 'Номеклатура с ID ' . $date -> id . ' уже имеет начисления за период с ' . $date -> datepicker1 . ' по ' . $date -> end_date . ' сумма начисления:' . $date -> price, PHP_EOL;
				}
			} else {

				$data_array = $this -> periodGenerator($date -> datepicker1, $end_month, $date -> id, $date -> price, $date -> id_account, $date -> id_clients);

				foreach ($data_array as $key => $value):

					$datacheck = $this -> money_model -> checkPeriod($value['id'], $value['start_month'], $value['end_month'], $value['price']);

					if ($datacheck == $value['id']) {
						$mdc -> put('username', 'PaymentEveryMonthRobot');
						$this -> log -> info('Были произведены начисления для номенклатуры' . $value['id'] . ' на ЛС (ID) ' . $value['id_account'] . ' сумма начисления:' . $value['price'] . ' в период с ' . $value['start_month'] . ' по ' . $value['end_month'] . ' у клиента c ID ' . $value['id_client']);
						echo 'Произведены начисления для номенклатуры' . $value['id'], ' на ЛС (ID) ' . $value['id_account'] . ' сумма начисления:' . $value['price'] . ' в период с ', $value['start_month'] . ' по ' . $value['end_month'] . ' у клиента c ID ' . $value['id_client'];
						$this -> money_model -> setPayment($value['id'], $value['id_account'], $value['price'], $value['start_month'], $value['end_month'], $value['id_client']);
					} else {
//							$mdc->put('username','PaymentEveryMonthRobot');
//							$this -> log -> warn('Номеклатура с ID ' . $value['id'] . ' уже имеет начисления за период с '.$value['start_month'].' по '.$value['end_month'].' сумма начисления:'.$value['price']);
						echo 'Номеклатура с ID ' . $value['id'] . ' уже имеет начисления за период с ' . $value['start_month'] . ' по ' . $value['end_month'] . ' сумма начисления:' . $value['price'], PHP_EOL;
					}
				endforeach;
			}
		}
	}

	//Проверить дальше работу метода, вроде что-то работает, надо сделать неполные периоды
	function getPeriod()
	{

		$this -> load -> model('money_model');

		$datePeriodStart = trim($this -> input -> post('dateStart'));
		$datePeriodEnd = trim($this -> input -> post('dateEnd'));

		$dateStart = DateTime::createFromFormat('m/y', $datePeriodStart);
		$dateEnd = DateTime::createFromFormat('m/y', $datePeriodEnd);

		/* Привожу диапазон дат к первому числу месяца */
		$periodStart = $dateStart -> format('Y-m-01');
		$periodEnd = $dateEnd -> format('Y-m-01');

		/* Получаю все начисляемые номенклатуры в указаном диапазоне дат */
		$data = $this -> money_model -> getPeriodData($periodStart, $periodEnd);
		$datacheck = array();
		$my_data = array();

		foreach ($data as $date) {

			/* Данный код создан при условии что дата начала равна началу месяца и завершает формирование начислений в конце расчетного периода
			 * или когда дата закрытия номеклатуры равна концу месяца. Т.е. этим кодом я перекрываю полные периоды.
			 */
			if (date("Y-m-d", strtotime($date -> datepicker1)) == date("Y-m-01", strtotime($date -> datepicker1))) {
				$start = $month = strtotime($date -> datepicker1);
				$end = strtotime("-1 month", now());
				$price = $date -> price;
				if (date("Y-m-t", $end) > date("Y-m-d", strtotime($date -> end_date)) && date("Y-m-d", strtotime($date -> end_date)) === date("Y-m-t", strtotime($date -> end_date))) {
					/* Этот цикл завершается при дате окончания равной концу месяца */
					$end = strtotime($date -> end_date);

					while ($month <= $end) {

						$periodStart = date('Y-m-d', $month);
						$periodEnd = date('Y-m-t', $month);
						$assortment_id = $date -> id;
						$month = strtotime("+1 month", $month);

						/* Тут вызов проверки наличия начислений за период и вызов setPayment() обязательно !!! незабыть присвоить переменным значения дат */
						$datacheck = $this -> money_model -> checkPeriod($assortment_id, $periodStart, $periodEnd);
						if ($datacheck == $assortment_id) {
							echo $assortment_id . '=>' . $periodStart . '=>' . $periodEnd . '=>' . $price, PHP_EOL;
							$this -> money_model -> setPayment($assortment_id, $periodStart, $periodEnd);
						} else {
							echo 'Номеклатура с ID ' . $assortment_id . ' уже имеет начисления за период с ' . $periodStart . ' по ' . $periodEnd, PHP_EOL;
						}
					}
				}

				if ($date -> end_date == NULL) {
					/* Этот цикл завершается в конце расчетного периода */
					$end = strtotime("-1 month", now());

					while ($month <= $end) {

						$periodStart = date('Y-m-d', $month);
						$periodEnd = date('Y-m-t', $month);
						$assortment_id = $date -> id;
						$month = strtotime("+1 month", $month);

						/* Тут вызов проверки наличия начислений за период и вызов setPayment() обязательно !!! незабыть присвоить переменным значения дат */
						$datacheck = $this -> money_model -> checkPeriod($assortment_id, $periodStart, $periodEnd);
						if ($datacheck == $assortment_id) {
							echo $assortment_id . '=>' . $periodStart . '=>' . $periodEnd . '=>' . $price, PHP_EOL;
							$this -> money_model -> setPayment($assortment_id, $periodStart, $periodEnd);
						} else {
							echo 'Номеклатура с ID ' . $assortment_id . ' уже имеет начисления за период с ' . $periodStart . ' по ' . $periodEnd, PHP_EOL;
						}
					}
				}
			}

			/* Данный код создан при условии что дата начала больше начала месяца и дата окончания не проставлена */
			if (date("Y-m-d", strtotime($date -> datepicker1)) > date("Y-m-01", strtotime($date -> datepicker1))) {


				$periodStart = $date -> datepicker1;
				$periodEnd = date('Y-m-t', strtotime($date -> datepicker1));
				$assortment_id = $date -> id;
				$id_account = $date -> id_account;
				$month = strtotime("+1 month", $month);


				$price_part = $date -> price;
				$date1_part = new DateTime($periodStart);
				$date2_part = new DateTime($periodEnd);
				$interval_part = $date2_part -> diff($date1_part);
				$inter_val_part = $interval_part -> format('%a') + 1;
				$id_client = $date -> id_clients;
				$amount_part = round(($price_part / $date2_part -> format('t')) * $inter_val_part, 2);

				$datacheck = $this -> money_model -> checkPeriod($assortment_id, $periodStart, $periodEnd);

				if ($datacheck == $assortment_id) {
					echo $assortment_id . '=>' . $periodStart . '=>' . $periodEnd . '=>' . $amount_part, PHP_EOL;
					$this -> getPartialPeriods($assortment_id, $id_account, $amount_part, $periodStart, $periodEnd, $id_client);
				} else {
					echo 'Номеклатура с ID ' . $assortment_id . ' уже имеет начисления за период с ' . $periodStart . ' по ' . $periodEnd, PHP_EOL;
				}
			}

			/* Данный код создан при условии что дата окончания меньше конца месяца и дата окончания проставлена */
			if (date("Y-m-d", strtotime($date -> end_date)) < date("Y-m-t", strtotime($date -> end_date)) && $date -> end_date != NULL && date("Y-m-d", strtotime($date -> datepicker1)) != date("Y-m-d", strtotime($date -> end_date))) {


				$periodStart = date('Y-m-01', strtotime($date -> end_date));
				$periodEnd = date('Y-m-d', strtotime($date -> end_date));
				$assortment_id = $date -> id;
				$id_account = $date -> id_account;
				$month = strtotime("+1 month", $month);


				$price_part = $date -> price;
				$date1_part = new DateTime($periodStart);
				$date2_part = new DateTime($periodEnd);
				$interval_part = $date2_part -> diff($date1_part);
				$inter_val_part = $interval_part -> format('%a') + 1;
				$id_client = $date -> id_clients;
				$amount_part = round(($price_part / $date2_part -> format('t')) * $inter_val_part, 2);

				$datacheck = $this -> money_model -> checkPeriod($assortment_id, $periodStart, $periodEnd);

				if ($datacheck == $assortment_id) {
					echo $assortment_id . '=>' . $periodStart . '=>' . $periodEnd . '=>' . $amount_part, PHP_EOL;
					$this -> getPartialPeriods($assortment_id, $id_account, $amount_part, $periodStart, $periodEnd, $id_client);
				} else {
					echo 'Номеклатура с ID ' . $assortment_id . ' уже имеет начисления за период с ' . $periodStart . ' по ' . $periodEnd, PHP_EOL;
				}
			}

			/* Данный код создан при условии что дата окончания равна дате начала действия номенклатуры, т.е. разовые платежи */
			if (date("Y-m-d", strtotime($date -> datepicker1)) == date("Y-m-d", strtotime($date -> end_date))) {

				$periodStart = date('Y-m-d', strtotime($date -> datepicker1));
				$periodEnd = date('Y-m-d', strtotime($date -> end_date));
				$assortment_id = $date -> id;
				$id_account = $date -> id_account;
				//$month = strtotime("+1 month", $month);


				$price_part = $date -> price;
				$date1_part = new DateTime($periodStart);
				$date2_part = new DateTime($periodEnd);
				$id_client = $date -> id_clients;

				$datacheck = $this -> money_model -> checkPeriod($assortment_id, $periodStart, $periodEnd);

				if ($datacheck == $assortment_id) {
					echo $assortment_id . '=>' . $periodStart . '=>' . $periodEnd . '=>' . $price_part, PHP_EOL;
					$this -> getPartialPeriods($assortment_id, $id_account, $price_part, $periodStart, $periodEnd, $id_client);
				} else {
					echo 'Номеклатура с ID ' . $assortment_id . ' уже имеет начисления за период с ' . $periodStart . ' по ' . $periodEnd, PHP_EOL;
				}
			}
		}
	}

	function getPartialPeriods($assortment_id_part, $id_account_part, $amount_part, $periodStart_part, $periodEnd_part, $id_client)
	{
		$this -> load -> model('money_model');
		$data = $this -> money_model -> getPartialPeriods($assortment_id_part, $id_account_part, $amount_part, $periodStart_part, $periodEnd_part, $id_client);
		echo $data;
	}

	function calculationPayments()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		} elseif ( ! $this -> ion_auth -> is_admin()) {
			//redirect them to the home page because they must be an administrator to view this
			redirect($this -> config -> item('base_url'), 'refresh');
		} else {

//			$this -> load -> model('money_model');
//			$data = $this -> money_model -> getAllCustomerPayments();

			$this -> load -> view('header');
			$this -> load -> view('calculationPayments');
			$this -> load -> view('left_sidebar');
		}
	}

	function readPaymentsCSV()
	{
		$path = trim($this -> input -> post('full_path'));
		$user = $this -> ion_auth -> user() -> row();
		$mdc = new LoggerMDC();
		try {
			$data = $this -> getcsv -> set_file_path($path) -> get_array();
		} catch (Exception $e) {
			echo show_error($e);
		}
		$this -> load -> model('money_model');
		$datas = $this -> money_model -> addPaymentsCSV($data);
		$mdc -> put('username', 'encashmentRobot');
		foreach ($datas as $enchashment_row):
			$this -> log -> fatal($enchashment_row);
		endforeach;
	}

	function readCSVFile()
	{
		$path = trim($this -> input -> post('full_path'));
		$user = $this -> ion_auth -> user() -> row();
		$mdc = new LoggerMDC();
		try {
			$data = $this -> getcsv -> set_file_path($path) -> get_array();
			echo json_encode($data);
		} catch (Exception $e) {
			echo show_error($e);
		}
//		$this -> load -> model('money_model');
//		$datas = $this -> money_model -> addPaymentsCSV($data);
//		$mdc -> put('username', 'encashmentRobot');
//		foreach ($datas as $enchashment_row):
//			$this -> log -> fatal($enchashment_row);
//		endforeach;

		//return $data;
	}

	function getAllPayById()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('money_model');
		$data = $this -> money_model -> getAllPayById($id);
		echo json_encode($data);
	}

	function getPayComment()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('money_model');
		$data = $this -> money_model -> getPayComment($id);
		echo json_encode($data);
	}

	function getPayById()
	{
		$id = trim($this -> input -> post('id'));
		$this -> load -> model('money_model');
		$data = $this -> money_model -> getPayById($id);
		echo json_encode($data);
	}

	function deletePayById()
	{
		$this -> load -> model('money_model');
		$id = trim($this -> input -> post('id'));
		$user = $this -> ion_auth -> user() -> row();
		$mdc = new LoggerMDC();
		$data = $this -> money_model -> getPayById($id);
		foreach ($data as $value):
			$date = $value -> date;
			$amount = $value -> amount;
			$id_account = $value -> id_account;
		endforeach;
		$mdc -> put('username', $user -> username);
		$mdc -> put('pay_id', 'ID платежа: ' . $id);
		$mdc -> put('id_account', 'ID лицевого счета: ' . $id_account);
		$mdc -> put('amount', 'Сумма: ' . $amount);
		$mdc -> put('date', 'Дата: ' . $date);
		$this -> log -> fatal('Пользователь удалил оплату с ЛС клиента ');
		$data = $this -> money_model -> deletePayById($id);
		echo json_encode($data);
	}

	function deletePayComment()
	{
		$id_comment = $this -> input -> post('id_comment');
		$this -> load -> model('money_model');
		$this -> money_model -> deletePayComment($id_comment);
	}

	function addPay()
	{
		$id_account = trim($this -> input -> post('id_account'));
		$date = trim($this -> input -> post('date'));
		$amount = trim($this -> input -> post('amount'));
		$id_client = trim($this -> input -> post('id_client'));
		$comment = $this -> input -> post('comment');

		$user = $this -> ion_auth -> user() -> row();
		$mdc = new LoggerMDC();
		$mdc -> put('username', $user -> username);
		$mdc -> put('amount', 'Cумма: ' . $amount);
		$mdc -> put('id_account', 'ID лицевого счета : ' . $id_account);
		$mdc -> put('date', 'Дата оплаты: ' . $date);
		$this -> log -> info('Пользователь добавил оплату на ЛС ');

		$this -> load -> model('money_model');
		$data = $this -> money_model -> addPay($id_account, $date, $amount, $id_client, $comment);
	}

	function editPay()
	{
		$id = trim($this -> input -> post('id'));
		$date = trim($this -> input -> post('date'));
		$amount = trim($this -> input -> post('amount'));
		$account = trim($this -> input -> post('account'));
		$user = $this -> ion_auth -> user() -> row();
		$mdc = new LoggerMDC();
		$mdc -> put('username', $user -> username);
		$mdc -> put('amount', 'Cумма: ' . $amount);
		$mdc -> put('id', 'ID записи: ' . $id);
		$mdc -> put('date', 'Дата оплаты: ' . $date);

		$this -> log -> warn('Пользователь отредактировал оплату на ЛС ' . $account);

		$this -> load -> model('money_model');
		$data = $this -> money_model -> editPay($id, $date, $amount);
	}

	function editPayComment()
	{
		$id_comment = trim($this -> input -> post('id_comment'));
		$comment = trim($this -> input -> post('comment'));
		$this -> load -> model('money_model');
		$data = $this -> money_model -> editPayComment($id_comment, $comment);
	}

	/**
	 * Сверка остатков PB и 1С. Формирование сводной таблицы.
	 */
	function checkDebt()
	{
		$this -> load -> model('money_model');
		$data['checkData'] = $this -> money_model -> checkDebt();
		$this -> load -> view('header');
		$this -> load -> view('checkDebt', $data);
		$this -> load -> view('left_sidebar');
	}

	/**
	 * Метод загрузки файлов на сервер
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function do_upload()
	{
		$config['upload_path'] = 'application/csv/' . $_POST['folder'] . '/';
		$config['allowed_types'] = '*';
		$config['max_size'] = '1400';
		$config['max_width'] = '1024';
		$config['max_height'] = '768';
		$config['overwrite'] = TRUE;

		$this -> load -> library('upload', $config);

		if ( ! $this -> upload -> do_upload()) {
			$error = array('error' => $this -> upload -> display_errors());
			$this -> load -> view('header');
			$this -> load -> view('checkDebt', $error);
			$this -> load -> view('left_sidebar');
		} else {
			$data = array('upload_data' => $this -> upload -> data());
			redirect('money/checkDebt', 'refresh');
		}
	}

	function getFileDebt()
	{
		$path = 'application/csv/ostatki';
		$getfilelist = get_filenames($path);
		if (isset($getfilelist[0])) {
			return $path . '/' . $getfilelist[0];
		} else {
			return FALSE;
		}
	}

	function readFileDebt()
	{
		$this -> load -> model('money_model');
		$path = trim($this -> input -> post('pathfile'));

		$this -> load -> helper('download');
		try {
			$data = $this -> getcsv -> set_file_path($path) -> get_array();
			$data1 = $this -> money_model -> checkDebt();
		} catch (Exception $e) {
			echo show_error($e);
		}

		$pExcel = new PHPExcel();

		$pExcel -> setActiveSheetIndex(0);
		$aSheet = $pExcel -> getActiveSheet();
		$aSheet -> setTitle('Сверка остатков');
		//устанавливаем данные
		//номера по порядку
		$aSheet -> setCellValue('A1', '№');
		$aSheet -> setCellValue('B1', 'Наименование клиента');
		$aSheet -> setCellValue('C1', 'Краткое наименование клиента');
		$aSheet -> setCellValue('D1', 'Лицевой счет');
		$aSheet -> setCellValue('E1', 'Остаток по 1С');
		$aSheet -> setCellValue('F1', 'Остаток по PostBilling');
		$my_array = array();

		foreach ($data1 as $key1 => $value1):

			foreach ($data as $key => $value) {


				//echo $value['account'].'==>'.$value1['account'].'==>'.$value['client_name'].'==>'.$value1['name'],PHP_EOL;
				$nachislenie = round($value1['amount'], 2);
				$oplata = round($value1['payment'], 2);
				$dolg = $nachislenie - $oplata; // <--
				$summ = round($dolg, 2);

				if ($value['account'] == $value1['account']) {


					if (round(($value['amount'] * -1), 2) != $summ) {
						//echo '==>'.$summ.'==>'.$value['client_name'].'==>'.$value1['account'].'==>'.$value['account'].'==>'.($value['amount']*-1),PHP_EOL;
						//echo $value['account'].'==>'.$value1['account'].'==>'.$value['client_name'].'==>'.$value1['name'].'==>'.($value['amount']*-1).'==>'.$summ,PHP_EOL;
//отдаем пользователю в браузер
						$my_data = new Money();
						$my_data -> full_name = $value1['name'];
						$my_data -> short_name = $value['client_name'];
						$my_data -> account = $value1['account'];
						$my_data -> amount_1c = $value['amount'] * -1;
						$my_data -> amount_pb = $summ;
						$my_array[$my_data -> account] = $my_data;
					}
				}
			}
		endforeach;

//						$objWriter = new PHPExcel_Writer_Excel5($pExcel);
		$counter = 1;
		foreach ($my_array as $my_key => $my_value):
			$counter ++;
			$aSheet -> setCellValue('A' . $counter, $counter - 1);
			$aSheet -> setCellValue('B' . $counter, $my_value -> full_name);
			$aSheet -> setCellValue('C' . $counter, $my_value -> short_name);
			$aSheet -> setCellValue('D' . $counter, $my_value -> account);
			$aSheet -> setCellValue('E' . $counter, $my_value -> amount_1c);
			$aSheet -> setCellValue('F' . $counter, $my_value -> amount_pb);

		endforeach;

		$objWriter = PHPExcel_IOFactory::createWriter($pExcel, 'Excel5');
		$objWriter -> save('application/csv/ostatki/file_' . date('Y-m-d', now()) . '.xls');

		die();
		return;
	}

	function downloadFile()
	{
		$this -> load -> helper('download');
		$filePath = file_get_contents('application/csv/ostatki/file_' . date('Y-m-d', now()) . '.xls'); // Считывает содержимое файла
		$nameOfFile = 'Report.xls';
		return force_download($nameOfFile, $filePath);
	}

	function periodGenerator($startdate, $enddate, $id, $price, $id_account, $id_client)
	{

		$start = strtotime($startdate);
		$end = strtotime($enddate);

		$currentdate = $start;
		$x = 0;
		$counter = 0;
		$myarray = array();
		$myarray2 = array();

		while ($currentdate < $end) {

			$x ++;
			$counter ++;

			if ($x > 1) {

				if (date('Y-m-t', $currentdate) > date('Y-m-d', $end)) {

					$start_month = date('Y-m-01', $currentdate);
					$end_month = date('Y-m-d', $end);
					$currentdate = strtotime('+1 month', $currentdate);
					$interval = date_diff(date_create($start_month), date_create($end_month), true);
					$new_interval = (int) ($interval -> days) + 1;
				} else {
					$start_month = date('Y-m-01', $currentdate);
					$end_month = date('Y-m-t', $currentdate);
					$currentdate = strtotime('+1 month', $currentdate);
					$interval = date_diff(date_create($start_month), date_create($end_month), true);
					$new_interval = (int) ($interval -> days) + 1;
				}

				$myarray[$counter]['id'] = $id;
				$myarray[$counter]['start_month'] = $start_month;
				$myarray[$counter]['end_month'] = $end_month;
				$myarray[$counter]['interval'] = $new_interval;
				$myarray[$counter]['price'] = round(($price / date('t', strtotime($end_month))) * $new_interval, 2);
				$myarray[$counter]['id_account'] = $id_account;
				$myarray[$counter]['id_client'] = $id_client;
			}

			if ($x == 1) {
				if (date('Y-m-t', $currentdate) > date('Y-m-d', $end)) {

					$start_month = date('Y-m-d', $currentdate);
					$end_month = date('Y-m-d', $end);
					$currentdate = strtotime('+1 month', $currentdate);
					$interval = date_diff(date_create($start_month), date_create($end_month), true);
					$new_interval = (int) ($interval -> days) + 1;
				} else {
					$start_month = date('Y-m-d', $currentdate);
					$end_month = date('Y-m-t', $currentdate);
					$currentdate = strtotime('+1 month', $currentdate);
					$interval = date_diff(date_create($start_month), date_create($end_month), true);
					$new_interval = (int) ($interval -> days) + 1;
				}
//				$start_month = date('Y-m-d', $currentdate);
//				$end_month = date('Y-m-t',$currentdate);
//				$currentdate = strtotime('+1 month', $currentdate);
//				$interval = date_diff(date_create($start_month), date_create($end_month),true);
//				$new_interval = (int)($interval->days)+1;


				$myarray[$counter]['id'] = $id;
				$myarray[$counter]['start_month'] = $start_month;
				$myarray[$counter]['end_month'] = $end_month;
				$myarray[$counter]['interval'] = $new_interval;
				$myarray[$counter]['price'] = round(($price / date('t', strtotime($end_month))) * $new_interval, 2);
				$myarray[$counter]['id_account'] = $id_account;
				$myarray[$counter]['id_client'] = $id_client;
			}
		}

		return $myarray;
	}

	function searchAccountByIdentifier()
	{

		$identifier = $this -> input -> post('identifier');
		$balance = $this -> input -> post('balance');
		$period = $this -> input -> post('period');
		$source_selector = $this -> input -> post('source_selector');
		$this -> load -> model('money_model');
		$data = $this -> money_model -> searchAccountByIdentifier($identifier, $balance, $period, $source_selector);
		echo json_encode($data);
	}

	function getPostBillingData()
	{
		$period = $this -> input -> post('period');
		$this -> load -> model('money_model');
		$data = $this -> money_model -> getPostBillingData($period);
		echo json_encode($data);
	}

	function buildCompareDataTable()
	{
		$this -> load -> model('money_model');
		$data = $this -> money_model -> buildCompareDataTable();
		echo json_encode($data);
	}
}

//End of file money.php
//Location: ./controllers/money.php
