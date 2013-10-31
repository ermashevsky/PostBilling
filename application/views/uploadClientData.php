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

    $(function(){
    	$("#tabs").tabs();
	$("#upload_mts_file_client").button({
	    text: true,
	    icons: {
	    primary: "ui-icon-transferthick-e-w"

	    }
	}).click(function(){
            var btnUpload=$('#upload_mts_file_client');
	var status=$('#status_mts');
	new AjaxUpload(btnUpload, {
		action: 'upload/uploadify_mts_file_client',
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
				$.jnotify('Файл импорта: '+file+' успешно загружен');
			} else{
				$('<li></li>').appendTo('#files_mts').text(file).addClass('error');
			}
		}
	});
	});

        $("#upload_ip").button({
	    text: true,
	    icons: {
	    primary: "ui-icon-transferthick-e-w"

	    }
	}).click(function(){
            var btnUpload=$('#upload_ip');
	var status=$('#status_ip');
	new AjaxUpload(btnUpload, {
		action: 'upload/uploadify_ip_file',
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
				$.jnotify('Файл импорта IP: '+file+' успешно загружен');

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
	}).click(function(){
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
//		console.groupCollapsed('Чтение из файла импорта: ');
//		console.log(data[i].id+' => '+data[i].resource+' => '+data[i].date+' => '+data[i].amount+' => '+data[i].assortment);
//		console.groupEnd();
		$.post('<?=site_url('upload/searchAssortmentID');?>',{'resource':data[i].resource, 'assortment':data[i].assortment, 'date':data[i].date, 'amount':data[i].amount},

		    function(data){
			$.each(data, function(i, val) {

			$.post('<?=site_url('upload/insertAssortmentID');?>',{'uniq_id':data[i].uniq_id, 'assortment':data[i].assortment, 'date':data[i].date, 'amount':data[i].amount},
			    function(data){
				$.each(data, function(i, val) {
//				   console.groupCollapsed('Результат поиска:-'+i);
//				   console.log('Найдена номеклатура => '+data[i].payment_name+' => '+data[i].id+' => '+data[i].id_group+' => '+data[i].id_account+' => '+data[i].amount+' => '+data[i].date);
//				   console.groupEnd();

				   $.post('<?=site_url('upload/addAmountToAssortment');?>',{'id_assortment_customer':data[i].id, 'id_group':data[i].id_group, 'id_account':data[i].id_account, 'amount':data[i].amount, 'date':data[i].date},
					function(mydata){

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

	function delfile(path){

		$.post('<?=site_url('upload/deleteFromServer');?>',{'pathfile':path},
					function(data){
						window.location.reload();
					})
	}
	function read_client_mts(path){
		$.post('<?=site_url('upload/readClientFileMTS');?>',{'pathfile':path},
	function(data){
		console.info(data);

	},'json')
	}

	function read_client_dn(path){
		$.post('<?=site_url('upload/readClientFileDN');?>',{'pathfile':path},
	function(data){
		console.info(data);

	},'json')
	}

	function read_client_tk(path){
		$.post('<?=site_url('upload/readClientFileTK');?>',{'pathfile':path},
	function(data){
		console.info(data);

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
                    <div id="page_header"><h2>Импорт клиентской базы</h2></div>
<p></p>
    <div id="tabs">
	<ul>
		<li><a href="#tabs-1">Местная телефонная связь (ЮЛ)</a></li>
		<li><a href="#tabs-2">Виртуальная АТС (ВА)</a></li>
		<li><a href="#tabs-3">Универсальный номер (ДН)</a></li>
		<li><a href="#tabs-4">IP-телефония (ТК)</a></li>
	</ul>
	<div id="tabs-1">
		<p>
		   <?php if(isset($error)){echo $error;} ?>

		    <?php echo form_open_multipart('upload/do_upload','id=form2'); ?>
		    <input type="hidden" name="folder" size="20" value="clientfiles/mts"/>
		    <input type="file" name="userfile" size="20" />
		    <input type="submit" value="Загрузить" />

		    </form>
			<?
			$readdir = new Upload();
			$readdir->listDirs("application/csv/clientfiles/mts",'read_client_mts');
			?>
		</p>

	</div>
	<div id="tabs-2">
		<p>
		   <?php if(isset($error)){echo $error;} ?>

		    <?php echo form_open_multipart('upload/do_upload','id=form2'); ?>
		    <input type="hidden" name="folder" size="20" value="clientfiles/va"/>
		    <input type="file" name="userfile" size="20" />
		    <input type="submit" value="Загрузить" />

		    </form>
			<?
			$readdir = new Upload();
			$readdir->listDirs("application/csv/clientfiles/va");
			?>

		</p>
	</div>
	<div id="tabs-3">
		<p>
		   <?php if(isset($error)){echo $error;} ?>

		    <?php echo form_open_multipart('upload/do_upload','id=form3'); ?>
		    <input type="hidden" name="folder" size="20" value="clientfiles/dn"/>
		    <input type="file" name="userfile" size="20" />
 		    <input type="submit" value="Загрузить" />

		    </form>
			<?
			$readdir = new Upload();
			$readdir->listDirs("application/csv/clientfiles/dn",'read_client_dn');
			?>
		</p>
	</div>
	<div id="tabs-4">
		<p>
		   <?php if(isset($error)){echo $error;} ?>

		    <?php echo form_open_multipart('upload/do_upload','id=form3'); ?>
		    <input type="hidden" name="folder" size="20" value="clientfiles/ip"/>
		    <input type="file" name="userfile" size="20" />
 		    <input type="submit" value="Загрузить" />

		    </form>
			<?
			$readdir = new Upload();
			$readdir->listDirs("application/csv/clientfiles/ip",'read_client_tk');
			?>
		</p>

	</div>

</div>

</div>
