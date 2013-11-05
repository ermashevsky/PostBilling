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
error_reporting(E_ALL);

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
class Backup extends CI_Controller
{
	/**
	 * Унифицированный метод-конструктор __construct()
	 *
	 * @author Ермашевский Денис
	 */
	function __construct()
	{
		parent::__construct();

		$this -> load -> library('session');
		$this -> load -> helper('file');
		$this -> load -> helper('date');
		$this -> load -> database();
		$this -> load -> dbutil();
	}

	function backupDBFull()
	{
		// Получение бэкапа и присвоение его переменной
		$backup =& $this->dbutil->backup();
		$this->load->helper('download');
		force_download('fullbackup-'.unix_to_human(now(), TRUE, 'eu').'.gz', $backup);
	}
}
