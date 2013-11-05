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
</script>

<section id="main" class="column">
	<article class="module width_full">
		<header><h3 class="tabs_involved">Формирование остатков</h3>
		</header>
		<div class="module_content">
			<fieldset>
				<label>Загрузка входных данных:</label><br/><br/><br/>

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

					<?php echo form_open_multipart('upload/do_upload_sverka', 'id=form2'); ?>
					<input type="hidden" name="folder" size="20" value="sverka"/>
					<input type="file" name="userfile" size="20"/>
					<input type="submit" value="Загрузить" />
					</form>

					<?php
				}
				?>

			</fieldset>
			<fieldset>
				<label>Список номенклатуры</label>
				<div name="selectAssortmentList" style="float:left;"></div>
			</fieldset>
			<fieldset>
				<label>Список тарифов</label>
				<div name="selectTariffs" style="float:left;"></div>
			</fieldset>
			<fieldset style="width:48%; float:left; margin-right: 3%;"> <!-- to make two field float next to one another, adjust values accordingly -->
				<label for="date">Дата начала действия</label>
				<input type="text" name="date_change_tariff" id="date_change_tariff" style="width:92%;">
			</fieldset>
			<fieldset style="width:48%; float:left;"> <!-- to make two field float next to one another, adjust values accordingly -->
				<label>Новый тарифный план</label>
				<div name="selectNewTariffs" style="float:left; width: 92%;"></div>
			</fieldset><div class="clear"></div>
		</div>
		<footer>
			<div class="submit_link" style="display:none;">
				<input type="submit" value="Поиск" onclick="searchTariff();return false;">
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