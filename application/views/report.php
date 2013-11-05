<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">
                     <div id="infoMessage" class="msg msg-error"><?php //echo $message;?></div>
                    <div id="page_header"><h2>Статистика</h2></div>

<table>
    <tr><th>Отчет:</th><td>
    <select name="report_name" id="report_name">
    <option value=""> -- сделайте выбор отчета -- </option>
    <option value="report_mts">Долги за МТС - общий</option>
    <option value="report_for_period">Долги за период - общий</option>
    </select>
    </tr>
    </table>
					<div style="display: none;" id="dates_block">
						<strong>Начало периода:</strong> <input type="text" id="start_date">
						<strong>Конец периода:</strong> <input type="text" id="end_date">
						<button id="send_dates" onclick="send_dates(); return false;">Построить</button>
						</div>

<div id="reports">

</div>
<script type="text/javascript">
    $(document).ready(setup_report_change);
        function setup_report_change(){
            $('#report_name').change(update_report);
        }
        function update_report(){
            var report_name=$('#report_name').attr('value');

            $.get('report/get_reports/'+report_name, show_report);

			if(report_name=='report_for_period'){
				$('#dates_block').slideDown('medium');
				$('#start_date').datepicker();
				$('#end_date').datepicker();
			}else{
				$('#dates_block').hide();
			}

        }

		function send_dates(){

					var report_name=$('#report_name').attr('value');
					var start_date = $('#start_date').val();
					var end_date = $('#end_date').val();

					
					$.get('report/get_reports/'+report_name+'/'+start_date+'/'+end_date, show_report);
		}

        function show_report(res){
            $('#reports').html(res);
			TableTools.DEFAULTS.aButtons = [ "copy", "xls","print" ];
			$('#report_table').dataTable({
			"sDom": 'T<"clear">lfrtip',

			"oTableTools": {
			"sSwfPath": "/assets/swf/copy_csv_xls_pdf.swf",
			"aButtons": [
                {
					"sExtends": "copy",
					"sButtonText": "Копировать"
				},
				{
					"sExtends": "csv",
					"sButtonText": "Сохранить в xls"
				},
				{
					"sExtends": "print",
					"sButtonText": "Печать"
				}

            ]
		},
		"bStateSave": true,
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
		"iDisplayLength": 25,
		"aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Все"]]

    });
        }
        </script>
</div>
