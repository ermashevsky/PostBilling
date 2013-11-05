<?php

/**
 * Upload
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Controllers.Upload
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
 * Класс Upload содержит методы работы с файлами CSV (загрузка, чтение, поиск и запись данных в БД)
 *
 * @category PHP
 * @package  Controllers.Upload
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Upload extends CI_Controller
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
		$this -> load -> helper(array('form', 'url'));
		$this -> load -> helper('file');
		$this -> load -> library('getcsv');
		//$this -> load -> library('Spreadsheet_Excel_Reader');
		$this -> load -> database();
	}

	/**
	 * Основной метод контроллера
	 *
	 * @author Ермашевский Денис
	 * @return null;
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

			$this -> load -> view('header');
			$this -> load -> view('uploadForm');
			$this -> load -> view('left_sidebar');
		}

	}

	function client_data()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		} elseif ( ! $this -> ion_auth -> is_admin()) {
			//redirect them to the home page because they must be an administrator to view this
			redirect($this -> config -> item('base_url'), 'refresh');
		} else {

			$this -> load -> view('header');
			$this -> load -> view('uploadClientData');
			$this -> load -> view('left_sidebar');
		}
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
		$config['max_size'] = '10240';
		$config['max_width'] = '1024';
		$config['max_height'] = '768';
		$config['overwrite'] = TRUE;

		$this -> load -> library('upload', $config);

		if ( ! $this -> upload -> do_upload()) {
			$error = array('error' => $this -> upload -> display_errors());
			$this -> load -> view('header');
			$this -> load -> view('uploadClientData', $error);
			$this -> load -> view('left_sidebar');
		} else {
			$data = array('upload_data' => $this -> upload -> data());
			redirect('upload/client_data', 'refresh');
		}
	}


	/**
	 * Метод загрузки файлов на сервер
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function do_upload_payments()
	{
		$config['upload_path'] = 'application/csv/' . $_POST['folder'] . '/';
		$config['allowed_types'] = '*';
		$config['max_size'] = '10240';
		$config['max_width'] = '1024';
		$config['max_height'] = '768';
		$config['file_name'] = 'file_'.  date('Y-m-d',now()).'.xls';
		$config['overwrite'] = TRUE;

		$this -> load -> library('upload', $config);

		if ( ! $this -> upload -> do_upload()) {
			$error = array('error' => $this -> upload -> display_errors());
			$this -> load -> view('header');
			$this -> load -> view('calculationPayments', $error);
			$this -> load -> view('left_sidebar');
		} else {
			$data = array('upload_data' => $this -> upload -> data());
			redirect('money/calculationPayments', 'refresh');
		}
	}

	/**
	 * Метод загрузки начальных файлов сверки на сервер
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function do_upload_sverka()
	{
		$config['upload_path'] = 'application/csv/' . $_POST['folder'] . '/';
		$config['allowed_types'] = '*';
		$config['max_size'] = '10240';
		$config['max_width'] = '1024';
		$config['max_height'] = '768';
		$config['file_name'] = 'file_'.  date('Y-m-d',now()).'.csv';
		$config['overwrite'] = TRUE;

		$this -> load -> library('upload', $config);

		if ( ! $this -> upload -> do_upload()) {
			$error = array('error' => $this -> upload -> display_errors());
			$this -> load -> view('admin/header');
			$this -> load -> view('admin/compare', $error);
			$this -> load -> view('admin/left_sidebar');
		} else {
			$data = array('upload_data' => $this -> upload -> data());
			redirect('admin/compare', 'refresh');
		}
	}

	function listDirs($where=NULL,$onclick=NULL)
	{
		echo "<table border=\"1\"><tr><td><b>Имя</b></td><td><b>Тип</b></td><td><b>Размер</b></td>";
		echo "<td><b>Действие</b></td></tr>";
		$itemHandler = opendir($where);
		$i = 0;
		while (($item = readdir($itemHandler)) !== false) {
			if (substr($item, 0, 1) != ".") {
				if ( ! is_dir($item)) {
					$fullpath=$where.'/'.$item;
					echo "<tr><td>" . $item . "</td><td>файл</td><td>".filesize($fullpath)." Bytes</td>
						<td><a href='#' onclick=".$onclick."('".$fullpath."');>импорт</a> | <a href='#' onclick=delfile('".$fullpath."');>Х</a></td></tr>";
				}
				$i ++;
			}
		}
		echo "</table>";
	}

	function readClientFileMTS()
	{
		$path = trim($this -> input -> post('pathfile'));
		try {
			$data = $this -> getcsv -> set_file_path($path) -> get_array();

		} catch (Exception $e) {
			echo show_error($e);
		}


		$this -> load -> model('clients_model');
		$this -> clients_model -> import_client_db($data);
		//print_r($data);
	}

	function readClientFileDN()
	{
		$path = trim($this -> input -> post('pathfile'));
		try {
			$data = $this -> getcsv -> set_file_path($path) -> get_array();

		} catch (Exception $e) {
			echo show_error($e);
		}


		$this -> load -> model('clients_model');
		$this -> clients_model -> import_client_db_dn($data);
		//print_r($data);
	}

	function readClientFileTK()
	{
		$path = trim($this -> input -> post('pathfile'));
		try {
			$data = $this -> getcsv -> set_file_path($path) -> get_array();

		} catch (Exception $e) {
			echo show_error($e);
		}


		$this -> load -> model('clients_model');
		$this -> clients_model -> import_client_db_tk($data);
		//print_r($data);
	}

	/**
	 * Метод возвращает полный путь до файла МТС
	 *
	 * @author Ермашевский Денис
	 * @return array $path;
	 */
	function getFileMTS()
	{
		$path = 'application/csv/mts';
		$getfilelist = get_filenames($path);
		if (isset($getfilelist[0])) {
			return $path . '/' . $getfilelist[0];
		} else {
			return FALSE;
		}
	}

	/**
	 * Метод возвращает полный путь до файла IP
	 *
	 * @author Ермашевский Денис
	 * @return array $path;
	 */
	function getFileTKIP()
	{
		$path = 'application/csv/tk/ip';
		$getfilelist = get_filenames($path);
		if (isset($getfilelist[0])) {
			return $path . '/' . $getfilelist[0];
		} else {
			return FALSE;
		}
	}

	/**
	 * Метод возвращает полный путь до файла SIP
	 *
	 * @author Ермашевский Денис
	 * @return array $path;
	 */
	function getFileTKSIP()
	{
		$path = 'application/csv/tk/sip';
		$getfilelist = get_filenames($path);
		if (isset($getfilelist[0])) {
			return $path . '/' . $getfilelist[0];
		} else {
			return FALSE;
		}
	}

	/**
	 * Метод читает содержимое файла МТС
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function readFileMTS()
	{
		$path = trim($this -> input -> post('pathfile'));
		try {
			$data = $this -> getcsv -> set_file_path($path) -> get_array();
		} catch (Exception $e) {
			echo show_error($e);
		}

		echo json_encode($data);
	}

	/**
	 * Метод читает содержимое файла IP/SIP
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function readFileTK()
	{
		$path = trim($this -> input -> post('pathfile'));
		try {
			$data = $this -> getcsv -> set_file_path($path) -> get_array();
		} catch (Exception $e) {
			echo show_error($e);
		}

		echo json_encode($data);
	}

	/**
	 * Метод удаления файла с сервера
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function deleteFromServer()
	{
		$path = trim($this -> input -> post('pathfile'));
		unlink($path);
	}

	/**
	 * Метод поиска номеклатуры в группе МТС
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function searchAssortmentID()
	{
		$resource = trim($this -> input -> post('resource'));
		//С номенклатурой надо бы как-то получше решить вопрос.
		$assortment = 'Предоставление местного телефонного соединения с использованием повременной оплаты местных телефонных соединений за 1 минуту';
		$amount = str_replace(',', '.',str_replace('р.', '', $this -> input -> post('amount')));
		$date = date('Y/m/d',strtotime($this -> rus2eng_date($this -> input -> post('date'))));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> searchAssortmentID($resource, $assortment, $amount, $date);
		$mdc = new LoggerMDC();
		if(isset($data['error']))
		{
			$mdc->put('username','MTS_PaymentRobot');
			$this -> log -> fatal($data);
		}else{
			echo json_encode($data);
		}
	}

	/**
	 * Метод поиска номеклатуры в группе IP/SIP
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function searchAssortmentIDIP()
	{
		$identifier = trim($this -> input -> post('identifier'));
		$assortment = trim($this -> input -> post('assortment'));
		$amount = trim($this -> input -> post('amount'));
		$date = trim($this -> input -> post('date'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> searchAssortmentIDIP($identifier, $assortment, $amount, $date);
		$mdc = new LoggerMDC();
		if(isset($data['error']))
		{
			$mdc->put('username','TK_PaymentRobot');
			$this -> log -> fatal($data);
		}else{
			echo json_encode($data);
		}
	}

	/**
	 * Метод готовит список номеклатур для начисления
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function insertAssortmentID()
	{
		$uniq_id = trim($this -> input -> post('uniq_id'));
		$assortment = trim($this -> input -> post('assortment'));
		$amount = trim($this -> input -> post('amount'));
		$date = trim($this -> input -> post('date'));
		$this -> load -> model('services_model');
		$data = $this -> services_model -> insertAssortmentID($uniq_id, $assortment, $amount, $date);
		echo json_encode($data);
	}

	/**
	 * Метод производит начисления
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function addAmountToAssortment()
	{
		$id_assortment_customer = trim($this -> input -> post('id_assortment_customer'));
		$id_client = trim($this -> input -> post('id_client'));
		$id_account = trim($this -> input -> post('id_account'));
		$amount = trim($this -> input -> post('amount'));
		$date = trim($this -> input -> post('date'));

		$this -> load -> model('services_model');
		$data = $this -> services_model -> addAmountToAssortment($id_assortment_customer, $id_client, $id_account, $amount, $date);
		echo json_encode($data);
	}


	/**
	 * Метод производит начисления
	 *
	 * @author Ермашевский Денис
	 * @return array $data;
	 */
	function addAmountToAssortmentTKIP()
	{
		$id_assortment_customer = trim($this -> input -> post('id_assortment_customer'));
		$id_client = trim($this -> input -> post('id_client'));
		$id_account = trim($this -> input -> post('id_account'));
		$amount = trim($this -> input -> post('amount'));
		$date = trim($this -> input -> post('date'));

		$this -> load -> model('services_model');
		$data = $this -> services_model -> addAmountToAssortmentTKIP($id_assortment_customer, $id_client, $id_account, $amount, $date);
		echo json_encode($data);
	}

	/**
	 * Метод загрузки файла МТС
	 *
	 * @author Ермашевский Денис
	 * @return string;
	 */
	function uploadify_mts_file()
	{
		$uploaddir = 'application/csv/mts/';
		$file = $uploaddir . basename($_FILES['uploadfile']['name']);
		$uniq= md5(uniqid(rand(), true));
		$type=strrchr($_FILES['uploadfile']['name'], ".");
		//$file = $uploaddir . $uniq. $type;
		$filename = $uniq. $type;

		if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
			echo $json = json_encode(array('filename'=>$file, 'success'=>'ok'));

		} else {
			echo 'error';
		}

	}

	/**
	 * Метод загрузки файла IP
	 *
	 * @author Ермашевский Денис
	 * @return string;
	 */
	function uploadify_ip_file()
	{
		$uploaddir = 'application/csv/tk/ip/';
		$file = $uploaddir . basename($_FILES['uploadfile']['name']);

		if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
			echo 'success';
		} else {
			echo 'error';
		}
	}

	/**
	 * Метод загрузки файла SIP
	 *
	 * @author Ермашевский Денис
	 * @return string;
	 */
	function uploadify_sip_file()
	{
		$uploaddir = 'application/csv/tk/sip/';
		$file = $uploaddir . basename($_FILES['uploadfile']['name']);

		if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
			echo 'success';
		} else {
			echo 'error';
		}
	}

	/**
	 * Метод загрузки файла МТС
	 *
	 * @author Ермашевский Денис
	 * @return string;
	 */
	function uploadify_mts_file_client()
	{
		$uploaddir = 'application/csv/mts/';
		$file = $uploaddir . basename($_FILES['uploadfile']['name']);

		if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
			echo 'success';
		} else {
			echo 'error';
		}
	}

	function rus2eng_date()
	{
    $translate = array(
    "Январь" => "January",
    "Февраль" => "February",
    "Март" => "March",
    "Апрель" => "April",
    "Май" => "May",
    "Июнь" => "June",
    "Июль" => "July",
    "Август" => "August",
    "Сентябрь" => "September",
    "Октябрь" => "October",
    "Ноябрь" => "November",
    "Декабрь" => "December"
    );

    if (func_num_args() > 1) {
        return strtr(func_get_arg(0), $translate);
    } else {
        return strtr(date(func_get_arg(0)), $translate);
    }
}
}

// End of file upload.php
//Location: ./controllers/upload.php