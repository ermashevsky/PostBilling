<style type="text/css">

    #ip_file_delete,
    #ip_file_parse,
    #mts_file_delete,
    #mts_file_parse,
    #sip_file_parse,
    #sip_file_delete{
       font-size: 10px;
    }
</style>
<script>
	function convertXLS(filename,checkbox)
	{
		checkbox = $('#checkbox').is(':checked') ? 1 : 0;

		$.blockUI({ message: '<h1><img src="/assets/images/busy.gif" /> Конвертируем...</h1>' });
		$.post('welcome/convertXLS_TK',{'filename':filename,'checkbox':checkbox},
		function(data){

			console.info(data);
			window.location.reload();
		})
	}

	function convertCSV(filename)
	{
		$.blockUI({ message: '<h1><img src="/assets/images/busy.gif" /> Конвертируем...</h1>' });
		$.post('welcome/convertCSV_MTS',{'filename':filename},
		function(data){

			console.info(data);
			window.location.reload();
		})
	}

    $(function(){
    $("#tabs").tabs({
    cookie: { expires: 7, name: "startTab" }
  });
//  $("#getCookie").click(function(){
//    var cook = $("#tabs").tabs("option", "cookie");
//    alert("name: " + cook.name +
//            "\nexpires: " + cook.expires);
//  });

	$("#convert_mts").button({
	    text: true,
	    icons: {
	    primary: "ui-icon-transferthick-e-w"

	    }
	})
	$("#upload_mts").button({
	    text: true,
	    icons: {
	    primary: "ui-icon-extlink"

	    }
	}).click(function(){
            var btnUpload=$('#upload_mts');
	var status=$('#status_mts');
	new AjaxUpload(btnUpload, {
		action: 'upload/uploadify_mts_file',
		//Name of the file input box
		name: 'uploadfile',
		onSubmit: function(file, ext){
			if (! (ext && /^(csv)$/.test(ext))){
                  // check for valid file extension
				status.text('Разрешены только csv-файлы');
				return false;
			}
			status.text('Загрузка...');

		},
		onComplete: function(file, response){
			//On completion clear the status
			status.text('');

			//Add uploaded file to list
			var obj =  $.parseJSON(response);

			if(obj.success==="ok"){

				$.jnotify('Файл импорта: '+obj.filename+' успешно загружен');
				window.location.reload();

			} else{
				$('<li></li>').appendTo('#files_mts').text(obj.filename).addClass('error');
			}
		}
	});
	});

        $("#upload_ip").button({
	    text: true,
	    icons: {
	    primary: "ui-icon-extlink"

	    }
	}).click(function(){
            var btnUpload=$('#upload_ip');
	var status=$('#status_ip');
	new AjaxUpload(btnUpload, {
		action: 'upload/uploadify_ip_file',
		//Name of the file input box
		name: 'uploadfile',
		onSubmit: function(file, ext){
			if (! (ext && /^(xls)$/.test(ext))){
                  // check for valid file extension
				status.text('Разрешены только XLS-файлы');
				return false;
			}
			status.text('Загрузка...');
		},
		onComplete: function(file, response){
			//On completion clear the status
			status.text('');
			//Add uploaded file to list
			if(response==="success"){
				$.jnotify('Файл импорта IP: '+file+' успешно загружен');
				window.location.reload();
			} else{
				$('<li></li>').appendTo('#files_ip').text(file).addClass('error');
			}
		}
	});
	});

        $("#upload_sip").button({
	    text: true,
	    icons: {
	    primary: "ui-icon-transferthick-e-w"

	    }
	}).
	click(function(){
            var btnUpload=$('#upload_sip');
	var status=$('#status_sip');
	new AjaxUpload(btnUpload, {
		action: 'upload/uploadify_sip_file',
		//Name of the file input box
		name: 'uploadfile',
		onSubmit: function(file, ext){
			if (! (ext && /^(csv)$/.test(ext))){
                  // check for valid file extension
				status.text('Разрешены только csv-файлы');
				return false;
			}
			status.text('Загрузка...');
		},
		onComplete: function(file, response){
			//On completion clear the status
			status.text('');
			//Add uploaded file to list
			if(response==="success"){
				$.jnotify('Файл импорта SIP: '+file+' успешно загружен');

			} else{
				$('<li></li>').appendTo('#files_sip').text(file).addClass('error');
			}
		}
	});
	});
        $("#mts_file_delete").button({
	    text: false,
	    icons: {
	    primary: "ui-icon-trash"

	    }
	})
        $("#mts_file_parse").button({
	    text: false,
            icons: {
	    primary: "ui-icon-refresh"

	    }
	})
        $("#ip_file_delete").button({
	    text: false,
	    icons: {
	    primary: "ui-icon-trash"

	    }
	})
        $("#ip_file_parse").button({
	    text: false,
            icons: {
	    primary: "ui-icon-refresh"

	    }
	})
        $("#sip_file_parse").button({
	    text: false,
            icons: {
	    primary: "ui-icon-refresh"

	    }
	})
        $("#sip_file_delete").button({
	    text: false,
            icons: {
	    primary: "ui-icon-trash"

	    }
	})

})

function deleteFile(path){
	$.post('<?=site_url('upload/deleteFromServer');?>',{'pathfile':path},
	function(data){
	    window.location.reload();
	});
    }
	var n=0;
	function readFileMTS(path){
	$('#prog-1').progressBar(0);

	$.post('<?=site_url('upload/readFileMTS');?>',{'pathfile':path},
	function(data){
	  for (var i = 0; i < data.length; i++) {
		var n = (Math.round(i/data.length))*100;
		console.groupCollapsed('Чтение из файла импорта: ');
		console.log(data[i].resource+' => '+data[i].date+' => '+data[i].amount+' => '+data[i].assortment);
		console.groupEnd();
		$.post('<?=site_url('upload/searchAssortmentID');?>',{'resource':data[i].resource, 'assortment':data[i].assortment, 'date':data[i].date, 'amount':data[i].amount},

		    function(data){

			$.each(data, function(i, val) {


			$.post('<?=site_url('upload/insertAssortmentID');?>',{'uniq_id':data[i].uniq_id, 'assortment':data[i].assortment, 'date':data[i].date, 'amount':data[i].amount},
			    function(data){
				$.each(data, function(i, val) {
				   console.groupCollapsed('Результат поиска:-'+i);
				   console.log('Найдена номеклатура => '+data[i].payment_name+' => '+data[i].id+' =>'+data[i].id_account+' => '+data[i].amount+' => '+data[i].date);
				   console.info(data[i].id);
//Закомментировал кусок для выяснения формата даты
//Необходимо сделать в CSV добавление пустой строки и названия столбцов -> PHPExcel
//Перевести дату в нормальный формат
				   $.post('<?=site_url('upload/addAmountToAssortment');?>',{'id_assortment_customer':data[i].id, 'id_account':data[i].id_account,'id_client':data[i].id_clients, 'amount':data[i].amount, 'date':data[i].date,'id_client':data[i].id_clients},
					function(mydata){
						console.info(mydata);
					},'json')

				})
			    },'json')
			})
		    },'json')

	}
	$('#prog-1').progressBar(n);

	},'json')

    }



    //Тут нужно переделать на IP и SIP
    function readFileTKIP(path){
	$('#prog-4-ip').progressBar(0);
	$.post('<?=site_url('upload/readFileTK');?>',{'pathfile':path},
	function(data){
	  for (var i = 0; i < data.length; i++) {
		var n = (Math.round(i/data.length))*100;
		console.groupCollapsed('Чтение из файла импорта: ');
		console.log(data[i].id+' => '+data[i].identifier+' => '+data[i].date+' => '+data[i].amount+' => '+data[i].assortment);
		console.groupEnd();
		$.post('<?=site_url('upload/searchAssortmentIDIP');?>',{'identifier':data[i].identifier, 'assortment':data[i].assortment, 'date':data[i].date, 'amount':data[i].amount},

		    function(data){
			$.each(data, function(i, val) {

			$.post('<?=site_url('upload/insertAssortmentID');?>',{'uniq_id':data[i].uniq_id, 'assortment':data[i].assortment, 'date':data[i].date, 'amount':data[i].amount},
			    function(data){

			$.each(data, function(i, val) {
				   console.groupCollapsed('Реузльтат поиска:-'+i);
				   console.log('Найдена номеклатура => '+data[i].payment_name+' => '+data[i].id+' клиент=> '+data[i].id_clients+' => '+data[i].id_account+' => '+data[i].amount+' => '+data[i].date);
				   console.groupEnd();

				   $.post('<?=site_url('upload/addAmountToAssortmentTKIP');?>',{'id_assortment_customer':data[i].id, 'id_account':data[i].id_account, 'id_client':data[i].id_clients, 'amount':data[i].amount, 'date':data[i].date},
					function(data){
					   console.info(data);
					},'json')

				})
			    },'json')
			})
		    },'json')

	}
	$('#prog-4-ip').progressBar(n);

	},'json')
    }
    //Тут нужно переделать на IP и SIP
    function readFileTKSIP(path){
	$('#prog-4-sip').progressBar(0);
	$.post('<?=site_url('upload/readFileTK');?>',{'pathfile':path},
	function(data){
	  for (var i = 0; i < data.length; i++) {
		var n = (Math.round(i/data.length))*100;
		console.groupCollapsed('Чтение из файла импорта: ');
		console.log(data[i].id+' => '+data[i].identifier+' => '+data[i].date+' => '+data[i].amount+' => '+data[i].assortment);
		console.groupEnd();
		$.post('<?=site_url('upload/searchAssortmentIDIP');?>',{'identifier':data[i].identifier, 'assortment':data[i].assortment, 'date':data[i].date, 'amount':data[i].amount},

		    function(data){
			$.each(data, function(i, val) {
                            console.info(data);
			$.post('<?=site_url('upload/insertAssortmentID');?>',{'uniq_id':data[i].uniq_id, 'assortment':data[i].assortment, 'date':data[i].date, 'amount':data[i].amount},
			    function(data){
				$.each(data, function(i, val) {
				   console.groupCollapsed('Реузльтат поиска:-'+i);
				   console.log('Найдена номеклатура => '+data[i].payment_name+' => '+data[i].id+' => '+data[i].id_group+' => '+data[i].id_account+' => '+data[i].amount+' => '+data[i].date);
				   console.groupEnd();

				   $.post('<?=site_url('upload/addAmountToAssortment');?>',{'id_assortment_customer':data[i].id, 'id_group':data[i].id_group, 'id_account':data[i].id_account, 'amount':data[i].amount, 'date':data[i].date},
					function(data){
					   console.info(data);
					},'json')

				})
			    },'json')
			})
		    },'json')

	}
	$('#prog-4-sip').progressBar(n);

	},'json')
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
                    <div id="page_header"><h2>Импорт данных</h2></div>
<p></p>
    <div id="tabs">
	<ul>
		<li><a href="#tabs-1">Местная телефонная связь (ЮЛ, ФЛ)</a></li>
		<li><a href="#tabs-4">IP-телефония (ТК, ТТК, КС)</a></li>
	</ul>
	<div id="tabs-1">
	<div id="box">
					<div class="changeable default">
						<img alt="bubble" src="/assets/images/et-info0.png">
						<p>1. Загрузите CSV файл с начислениями</p>
						<p>2. Нажмите кнопку "Конвертировать"</p>
						<p>3. Для импорта данных нажмите "Импорт"</p>
					</div>
				</div>
    <p><span id="status_mts" ></span></p>
    <ul id="files_mts" ></ul>
		<?php
		$fullpath = new Upload();
		if(get_mime_by_extension($fullpath->getFileMTS())=='text/x-comma-separated-values' & !file_exists('application/csv/mts/file_' . date('Y-m-d', now()) . '.csv')){
		$file_array = get_dir_file_info('application/csv/mts/');

		foreach($file_array as $file_rows):
		endforeach;

		echo '<button id="convert_mts" onclick=convertCSV("'.$file_rows['name'] .'")>Конвертировать</button>';

		}elseif (file_exists('application/csv/mts/file_' . date('Y-m-d', now()) . '.csv')) {
	    ?>
	    <div class="file_block"><b>Файл импорта:</b> <?=$fullpath->getFileMTS(); ?> <a href="#" onclick="deleteFile('<?=$fullpath->getFileMTS()?>'); return false;"><button name="" id="mts_file_delete" > 1</button></a> <a href="#" class="import_button" onclick="readFileMTS('<?=$fullpath->getFileMTS()?>'); return false;"><button name="" id="mts_file_parse" >2</button></a><div id="prog-1" ></div></div>
		    <?php
		}else{
			echo '<button id="upload_mts" >Загрузить файл</button>';
		    echo '<div class="file_block">Файл импорта отсутствует!</div>';
		}
		?>
	</div>
	<div id="tabs-4">
	<div id="box">
					<div class="changeable default">
						<img alt="bubble" src="/assets/images/et-info0.png">
						<p>1. Выберите Excel файл с начислениями</p>
						<p>2. После загрузки файла нажмите "Конвертировать"</p>
						<p>3. Для импорта данных нажмите "Импорт"</p>
					</div>
				</div>

    <p><span id="status_ip" ></span></p>
    <ul id="files_ip" ></ul>
		<?php
		$fullpath = new Upload();
		if(get_mime_by_extension($fullpath->getFileTKIP())=='application/excel'){
		$file_array = get_dir_file_info('application/csv/tk/ip/');

		foreach($file_array as $file_rows):
		endforeach;

		echo '<button id="convert_mts" onclick=convertXLS("'.$file_rows['name'] .'")>Конвертировать</button><br/>';
		echo '<p><label>Это файл импорта ТТК/КС</label><input type="checkbox" id="checkbox" /></p>';

		}elseif (file_exists('application/csv/tk/ip/file_' . date('Y-m-d', now()) . '.csv')) {
		?>
	<div class="file_block"><b>Файл импорта:</b> <?=$fullpath->getFileTKIP(); ?> <a href="#" onclick="deleteFile('<?=$fullpath->getFileTKIP()?>'); return false;"><button name="" id="ip_file_delete" > 1</button></a> <a href="#" class="import_button" onclick="readFileTKIP('<?=$fullpath->getFileTKIP()?>'); return false;"><button name="" id="ip_file_parse" >2</button></a><div id="prog-4-ip" ></div></div>
		<?php
		}else{

			echo '<button id="upload_ip" >Загрузить IP - файл</button>';
		    echo '<p><div class="file_block">Файл импорта IP отсутствует!</div></p>';
		}
		?>

	</div>

</div>

</div>
