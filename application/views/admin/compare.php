<script type="text/javascript">

	function importData(full_path)
	{	$.blockUI({ message: '<h1><img src="/assets/images/busy.gif" /> Импортируем...</h1>' });
		period = $('#datepicker').val();
		source_selector = $('#source_selector').val();
		$.post('<?=site_url('money/readCSVFile');?>',{'full_path':full_path},
        function(data){
			$.each(data, function(i, val) {

				if(source_selector == 'file_25'){
					identifier = data[i].client_name+'_bill25';
					console.info(data[i].client_name+'_bill25 ==> '+data[i].ostatok);
				}else{
					identifier = data[i].client_name;
					console.info(data[i].client_name+'==> '+data[i].ostatok);
				}
				balance = data[i].ostatok;

				$.post('<?=site_url('money/searchAccountByIdentifier');?>',{'identifier':identifier, 'balance': balance, 'period': period, 'source_selector': source_selector},
				function(data){
					console.info(data);
				},'json');

			})
			$.unblockUI();
		},'json');
	}

	function getPostBillingData()
	{
		period = $('#datepicker_postbilling').val();
		$.post('<?=site_url('money/getPostBillingData');?>',{'period': period},
        function(data){
			console.info(data);
		},'json');

	}

	function buildCompareDataTable(){
				$('#compareData').empty();

				$.post('<?php echo site_url('/money/buildCompareDataTable'); ?>',
				function(data){
					console.info(data)
					$('#compareData').append('<table  id="DataTable" class="table_wrapper_inner"><thead><th>ЛС</th><th>Идентификаторы</th><th>Клиент</th><th>Период</th><th>Остаток по биллингу</th><th>Остаток по постбиллингу</th></thead><tbody></tbody></table>');
					$.each(data, function(i, val) {
						$('#DataTable').append('<tr><td>'+data[i].account+'</td><td>'+data[i].identifier+'</td><td>'+data[i].bindings_name+'</td><td>'+data[i].period+'</td><td>'+data[i].billings_amount+'</td><td>'+data[i].postbilling_amount+'</td></tr>');
					});

					oTable = $('#DataTable').dataTable({
						"aaSorting": [[0, 'asc']],
						"bJQueryUI": false,
						"bProcessing":true,
						"sPaginationType": "full_numbers",
						"oLanguage": {
							"sUrl": "/assets/admin/js/russian-language-DataTables.txt"
						},
						"bAutoWidth": true,
						"bDestroy": true,
						"sScrollY": "320px",
						"sDom": 'T<"clear">lfrtip',
						"oTableTools": {
							"aButtons": [
								{
									"sExtends": "csv",
									"sButtonText": "Сохранить в CSV"
								}
							],
							"sSwfPath": "/assets/admin/swf/copy_csv_xls_pdf.swf"
						}
					});
					$('#compareData').show();
				},'json');

			}

</script>

<section id="main" class="column">
	<article class="module width_full">
		<header><h3 class="tabs_involved">Формирование остатков</h3>
		</header>
		<div class="module_content">
			<fieldset>
				<label>Загрузка входных данных:</label><br/><br/>

				<?php
				$dir = "application/csv/sverka/";   //задаём имя директории
				$n = 1;
				$today_file = 'file_' . date('Y-m-d', now()) . '.csv';

				if (file_exists($dir . $today_file)) {
					echo '<div style="margin:10px;">';
					echo "<table border=\"1\"><tr align='center'><td><b>Имя файла</b></td><td><b>Размер</b></td><td><b>Разрешения</b></td><td><b>Период</b></td><td><b>Источник данных</b></td>";
					echo "<td><b>Действие</b></td></tr>";
					$file_attr = get_file_info($dir . $today_file, 'name,size,fileperms');
					echo '<tr><td>' . $file_attr['name'] . '</td><td>' . byte_format($file_attr['size']) . '</td><td>' . symbolic_permissions($file_attr['fileperms']) . '</td>';
					echo '<td>
						<input type="text" id="datepicker" style="width:80px;"/>
						</td>
						<td style="width:100px;">
						<select name="source_selector" id="source_selector" style="width:90px;">
							<option value="file_25">25 биллинг</option>
							<option value="file_52">52 биллинг</option>
						</select></td>';
					echo '<td><a href="#" onclick=importData("' . $dir . $today_file . '");return false;> Импорт данных</a></td></tr>';
					echo "</table>";
					echo '</div>';
				} else {
					?>
				<div style="margin:10px;">
					<p style="font-size: large; color:#79C20D;">В таблице содержится
					<?php
						$get = new Admin();
						$get -> getCountRow();
					?>
					записей.</p>
					<?php echo form_open_multipart('upload/do_upload_sverka', 'id=form2'); ?>
					<input type="hidden" name="folder" size="20" value="sverka"/>
					<input type="file" name="userfile" size="20"/>
					<input type="submit" value="Загрузить" />
					</form>
				</div>
					<?php
				}
				?>

			</fieldset>
			<fieldset>
				<label>Сбор и анализ данных</label><br/><br/>
				<div name="divContainer" style="float:left; margin:10px;">
					<p style="font-size: large; color:#79C20D;">В таблице содержится
					<?php
						$get = new Admin();
						$get -> getCountRowPostBillingData();
					?>
					записей.</p>
					<p>

					<input type="text" id="datepicker_postbilling" style="width:80px;" name="datepicker_postbilling"/>

					<input type="button" name="getDataSet" id="getDataSet" value="Получить данные" onclick="getPostBillingData();"/>
					</p>
				</div>
			</fieldset>
			<fieldset>
				<label>Сводная таблица остатков</label>
			</fieldset>
			
					<div id="compareData" style="display:none;">

					</div>


			<div class="clear"></div>
		</div>
		<footer>
			<div class="submit_link">
				<input type="submit" value="Поиск" class="alt_btn" onclick="buildCompareDataTable();return false;">
			</div>
		</footer>
	</article><!-- end of content manager article -->

	<div class="clear"></div>

	<h4 class="alert_warning">A Warning Alert</h4>

	<h4 class="alert_error">An Error Message</h4>

	<h4 class="alert_success">A Success Message</h4>
	<div class="spacer"></div>
</section>

<div id="dialog-confirm" title="Пакетная смена тарифного плана">

</div>
<div id="dialog-confirm-ok" title="Пакетная смена тарифного плана">

</div>


</body>

</html>