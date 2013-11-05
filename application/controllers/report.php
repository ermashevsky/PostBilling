<?php

/**
 * Money
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Controllers.Report
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://www.ci2.lcl/
 */

/**
 * Класс Money содержит методы начислений за услуги
 *
 * @category PHP
 * @package  Controllers.Report
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.ci2.lcl/
 */
class Report extends CI_Controller
{

	/**
	 * Унифицированный метод-конструктор __construct()
	 *
	 * @author Ермашевский Денис
	 */
	function __construct()
	{
		parent::__construct();
		$this -> load -> library('ion_auth');
		$this -> load -> library('session');
		$this -> load -> library('form_validation');
		$this -> load -> database();
		$this -> load -> helper('url', 'form', 'date');
		$this -> breadcrumbs = array();
		$this -> breadcrumbs[] = anchor('', $this -> config -> item('breadcrumbs_index'));
		//$this->output->enable_profiler(TRUE);
	}

	/**
	 * Метод index
	 *
	 * @author Ермашевский Денис
	 * @return null;
	 */
	function index()
	{
		if ( ! $this -> ion_auth -> logged_in()) {
			redirect('auth/login', 'refresh');
		} else {

			$this -> load -> view('header');
			$this -> load -> view('report');
			$this -> load -> view('left_sidebar');
		}
	}

	function get_reports($report_name = NULL,$start_date=NULL,$end_date=NULL){

    switch($report_name){
        case 'report_mts':
            $this -> load -> model('report_model');
			$res = array();
			$res = $this -> report_model -> get_mts_full();
        break;
        // }
        case 'report_for_period':
            $res=array();
			if(!empty($start_date)&&!empty($end_date)):
				$this -> load -> model('report_model');
				$res = array();
				$res = $this -> report_model -> get_full_report_for_period($start_date, $end_date);
			endif;
        break;
        // }
        default: // { else
            $res=false;
        // }
    }
    if(!$res) echo 'пожалуйста выберите отчет';
//    else echo '<select name="city"><option>'.join('</option>  <option>',$res).'</select>';
	else
		if(!empty($res)){
		?>
				<table id="report_table" >
				<thead>
					<tr>
						<th>
							№
						</th>
						<th>
							Наименование клиента
						</th>
						<th>
							ЛС клиента
						</th>
						<th>
							Долг
						</th>
					</tr>
				</thead>
			<?php
			$n=1;
		foreach ($res as $res):
		echo '<tr><td>'.$n++.'</td>';
		echo '<td>'.$res->bindings_name.'</td>';
		echo '<td>'.$res->accounts.'</td>';
		echo '<td>'.$res->amount.'</td></tr>';
		endforeach;
		echo '</table>';
		}
}


}

//End of file report.php
//Location: ./controllers/report.php
