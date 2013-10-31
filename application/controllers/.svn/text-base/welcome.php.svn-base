<?php
/** Include path **/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once APPPATH."/third_party/PHPExcel.php";

class Welcome extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 * 	- or -
	 * 		http://example.com/index.php/welcome/index
	 * 	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct()
	{

		parent::__construct();
		$this -> load -> library('ion_auth');
		$this -> load -> library('session');
		$this -> load -> helper('url');


	}

	function index()
	{
		$file = $this -> input -> post('file');
		$inputFileName = "application/csv/import_payments/".$file;
		$objReader = new PHPExcel_Reader_Excel5();
//		$objReader->setInputEncoding('CP1251');
//		$objReader->setDelimiter(';');
		//$objReader->setEnclosure('');
		$objPHPExcel = $objReader -> load($inputFileName);
		$objPHPExcel -> getActiveSheet(0)->insertNewRowBefore(1);
		$objPHPExcel -> setActiveSheetIndex(0)
				->setCellValue('A1', 'date')
				->setCellValue('B1', 'time')
				->setCellValue('C1', 'amount')
				->setCellValue('D1', 'account');



		$sheetData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);

		//var_dump($sheetData);

		foreach ($sheetData as $rows):
			echo $rows['A']. ' ' .$rows['B']. ' ' .$rows['C']. ' '.$rows['D'];

		endforeach;
		$loadedSheetNames = $objPHPExcel -> getSheetNames();
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');



		foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
			$objWriter -> setSheetIndex($sheetIndex);
			$objWriter -> save('application/csv/import_payments/file_' . date('Y-m-d', now()) . '.csv');


		unlink($inputFileName);
	}
	}

	public function convertCSV_MTS()
	{
		$file = trim($this -> input -> post('filename'));
		$inputFileName = "application/csv/mts/".$file;
		$objReader = new PHPExcel_Reader_CSV();
		$objReader->setInputEncoding('CP1251');
		$objReader->setDelimiter(';');
		//$objReader->setEnclosure('');
		$objPHPExcel = $objReader -> load($inputFileName);
		$objPHPExcel->getActiveSheet(0)->insertNewRowBefore(1);
		$objPHPExcel -> setActiveSheetIndex(0)
				->setCellValue('A1', 'resource')
				->setCellValue('B1', 'amount')
				->setCellValue('C1', 'date')
				->setCellValue('D1', 'assortment');



		$sheetData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);

		//var_dump($sheetData);

		foreach ($sheetData as $rows):
			echo $rows['A']. ' ' .$rows['B']. ' ' .$rows['C']. ' '.$rows['D'];

		endforeach;
		$loadedSheetNames = $objPHPExcel -> getSheetNames();
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');



		foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
			$objWriter -> setSheetIndex($sheetIndex);
			$objWriter -> save('application/csv/mts/file_' . date('Y-m-d', now()) . '.csv');


		unlink($inputFileName);
	}

	}

	public function convertXLS_TK()
	{
		$file = trim($this -> input -> post('filename'));
		$checkbox = trim($this -> input -> post('checkbox'));
		$inputFileName = "application/csv/tk/ip/".$file;
		$objReader = new PHPExcel_Reader_Excel5();
		$objPHPExcel = $objReader -> load($inputFileName);

		$objPHPExcel -> getActiveSheet(0)->insertNewRowBefore(1);

		$objPHPExcel -> setActiveSheetIndex(0)
				->setCellValue('A1', 'identifier')
				->setCellValue('B1', 'amount')
				->setCellValue('C1', 'date')
				->setCellValue('D1', 'assortment');



		$sheetData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);

//Работает
		for($row=2; $row <= $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();$row++):
			$objPHPExcel -> setActiveSheetIndex(0)->  setCellValue('D'.$row,'Учет IP трафика');
		if($checkbox==1):
			$objPHPExcel -> setActiveSheetIndex(0)->  setCellValue('D'.$row,'Услуги МГМН связи');
		endif;
		endfor;
//var_dump($sheetData);
for ($sheet=1;$sheet < $objPHPExcel -> getSheetCount();$sheet++):
$objPHPExcel->removeSheetByIndex($sheet);
endfor;
		foreach ($sheetData as $rows):
			//echo mb_convert_encoding($rows['A'], 'UTF8') . ' ' . mb_convert_encoding($rows['B'], 'UTF8') . ' ' . mb_convert_encoding($rows['C'], 'UTF8') . ' ' . mb_convert_encoding($rows['D'], 'UTF8');
		endforeach;

		$loadedSheetNames = $objPHPExcel -> getSheetNames();
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');



//		foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
//			$objWriter -> setSheetIndex($sheetIndex);
			$objWriter -> save('application/csv/tk/ip/file_' . date('Y-m-d', now()) . '.csv');


		unlink($inputFileName);


	}

//function testDate()
//{
//	print date('Y-m-d',strtotime($this->  rus2eng_date('Август 2013')));
//}

}

// End of file welcome.php
// Location: ./application/controllers/welcome.php