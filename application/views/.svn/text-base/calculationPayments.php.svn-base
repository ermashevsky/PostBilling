<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="utf-8" http-equiv="encoding">
<script type="text/javascript">
	function importPayments(full_path)
	{	$.blockUI({ message: '<h1><img src="/assets/images/busy.gif" /> Импортируем...</h1>' });
		$.post('<?=site_url('money/readPaymentsCSV');?>',{'full_path':full_path},
        function(data){
			console.info(data);
			$.blockUI().hide();
		});
	}

	function convertXLS2CSV(path_to_file)
	{

		$.blockUI({ message: '<h1><img src="/assets/images/busy.gif" /> Конвертируем...</h1>' });
		$.post('<?=  site_url('welcome/index')?>',{'file':path_to_file},
		function(data){
			console.info(data);
			window.location.reload();
		});
	}
</script>

<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">
                     <div id="infoMessage" class="msg msg-error"><?php //echo $message;?></div>
                    <div id="page_header"><h2>Платежи</h2></div>
					<p></p>
					<div id="box">
					<div class="changeable default">
						<img alt="bubble" src="/assets/images/et-info0.png">
						<p>1. Выберите Excel файл с оплатами и нажмите кнопку "Загрузить"</p>
						<p>2. После загрузки файла нажмите "Конвертировать"</p>
						<p>2. Для импорта данных нажмите "Импорт"</p>
					</div>
				</div>
<?php
					$dir = "application/csv/import_payments/";   //задаём имя директории
					$n = 1;
					$today_file = 'file_' . date('Y-m-d', now()) . '.csv';

					if (file_exists($dir . $today_file)) {
						echo '<div>';
						echo "<table border=\"1\" style='width:50%;'><tr><td><b>Имя файла</b></td><td><b>Размер</b></td><td><b>Разрешения</b></td>";
						echo "<td><b>Действие</b></td></tr>";
						$file_attr = get_file_info($dir . $today_file, 'name,size,fileperms');
						echo '<tr><td>'.$file_attr['name'] .'</td><td>'. byte_format($file_attr['size']).'</td><td>'. symbolic_permissions($file_attr['fileperms']).'</td>';
						echo '<td><a href="#" onclick=importPayments("' . $dir . $today_file . '");return false;> Импорт</a></td></tr>';
						echo "</table>";
						echo '</div>';
					} elseif (file_exists($dir . 'file_' . date('Y-m-d', now()) . '.xls')) {
						echo "<table border=\"1\" style='width:50%;'><tr><td><b>Имя файла</b></td><td><b>Размер</b></td><td><b>Разрешения</b></td>";
						echo "<td><b>Действие</b></td></tr>";
						$file_attr1 = get_file_info($dir . 'file_' . date('Y-m-d', now()) .'.xls', "name,size,fileperms");
						echo '<tr><td>'.$file_attr1['name'] .'</td><td>'. byte_format($file_attr1['size']).'</td><td>'. symbolic_permissions($file_attr1['fileperms']).'</td>';
						echo '<td><a href="#" onclick=convertXLS2CSV("file_' . date('Y-m-d', now()) .'.xls");return false;> Конвертировать</a></td></tr>';
						echo "</table>";
					} else {
						?>
						<?php echo form_open_multipart('upload/do_upload_payments', 'id=form2'); ?>
						<input type="hidden" name="folder" size="20" value="import_payments"/>
						<input type="file" name="userfile" size="20"/>
						<input type="submit" value="Загрузить" />

					</form>
<?php
	}
?>
</div>

