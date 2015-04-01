<script type="text/javascript">
function getPayGroupByMonth(id_account,id_client, account){
	$('#allPay').empty();
	$.post('<?php echo site_url('/clients/getPayGroupByMonth'); ?>', {'id_account': id_account, 'id_client': id_client},
		function(data) {
			n=1;
			console.info(data);
			$('#allPay').append('<table border="1" id="tablesorter"><tr><th>№</th><th>Период</th><th>Сумма</th></tr></table>');
			$.each(data, function(i, val) {

			   $('#tablesorter').append('<tr><td>'+n+++'</td><td>'+data[i].period+'</td><td>'+data[i].amount+'</td></tr>');
		   });
		   $('#allPay').dialog({
		   title: "Ежемесячные начисления по ЛС - "+account,  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
                resizable: false,
				width:'400px',
				buttons: {
                "Закрыть": function() {
					$('#allPay').dialog("close");
					location.reload();
				}}

	   });
		},'json');
}


function addAccrual(id_assortment_customer, id_account, id_clients,tariffs,price) {
		$('#addAccrual').empty();
		$('#addAccrual').append('<p>Дата начала:<br /><input type="text" name="startDate" id="startDate" /></p>');
		$('#addAccrual').append('<p>Дата окончания:<br /><input type="text" name="endDate" id="endDate" /></p>');
		if(tariffs && price){
			$('#addAccrual').append('<p>Сумма начисления:<br /><input type="text" name="amountAccrual" id="amountAccrual" value="'+price+'"/></p>');
		}else{
			$('#addAccrual').append('<p>Сумма начисления:<br /><input type="text" name="amountAccrual" id="amountAccrual" /></p>');
		}
		
		$('#addAccrual #startDate').datepicker($.datepicker.regional["ru"]);
		$('#addAccrual #endDate').datepicker($.datepicker.regional["ru"]);
		
		$('#addAccrual').dialog({
			title: 'Ручное начисление',
			modal: true, //булева переменная если она равно true -  то окно модальное, false -  то нет
			draggable: false,
			resizable: false,
			width: 250,
			show: 'slide',
			dialogClass: 'no-close',
			buttons: {
				"Начислить": function() {

					startDateAccrual = $('#addAccrual #startDate').val();
					endDateAccrual = $('#addAccrual #endDate').val();
					amountAccrual = $('#addAccrual #amountAccrual').val();
					console.info(amountAccrual);
					$.post('<?php echo site_url('/money/checkAccrual'); ?>', {'id_assortment_customer': id_assortment_customer,
						'id_account': id_account, 'id_clients': id_clients, 'startDateAccrual': startDateAccrual, 'endDateAccrual': endDateAccrual,
						'amountAccrual': amountAccrual},
					function(data) {
						if (data === 1) {
							$.jnotify("Начисление на сумму " + amountAccrual + " за период " + startDateAccrual + " - " + endDateAccrual + " были произведены успешно!", "", {remove: function() {
								}});
						} else {
							$.jnotify("Начисление на сумму " + amountAccrual + " за период " + startDateAccrual + " - " + endDateAccrual + " уже были произведены ранее", "error", {remove: function() {
								}});
						}
						$('#addAccrual').dialog('close');
					}, 'json');
				},
				"Закрыть": function() {
					$(this).dialog("close");
				}}
		});
	}

function copyOptions(id,id_client){
	$('#copyOptionsDialog').empty();
	$('#copyOptionsDialog').append('<p style="margin:5px;">Выберите вариант копирования ЛС</p>');
	$('#copyOptionsDialog').append('<button onclick="copyAccount('+id+','+id_client+')" class="my_copy_account2account">ЛС -> ЛС</button>\n\
	<button onclick="copyAccount2External('+id+','+id_client+')" class="my_copy_client2client">Клиент -> Клиент</button>')
	$( ".my_copy_account2account" ).button({
      icons: {
        primary: "ui-icon-copy"
      },
      text: true
    }).next().button({
      icons: {
        primary: "ui-icon-copy"
      },
      text: true
    });
	$('#copyOptionsDialog').dialog({
				position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
				resizable: false,
				width:290,
                show:'slide',
				buttons:{
					"Закрыть": function() {
                $(this).dialog("close");
                 }}
	})
}

function copyAccount2External(id,id_client){
	$('#copyOptionsDialog').dialog('close');
	$('#copyAccount2ExternalDialog').empty();
	$('#copyAccount2ExternalDialog').append('<p class="field">Поиск счета: <input type="text" id="accountSearchList" /><div class="resultAccount"></div></p>\n\
	<p class="field">Дата окончания: <input type="text" name="end_date" id="end_date_copy" readonly/></p><p class="field">Дата начала: <input type="text" name="datepicker1" id="datepicker1_copy" readonly/></p>');
	$('p.field input#end_date_copy').datepicker($.datepicker.regional["ru"]);
	$('p.field input#datepicker1_copy').datepicker($.datepicker.regional["ru"]);
	$("p.field input#accountSearchList").keyup(function()
  {
    var kw = $("p.field input#accountSearchList").val();

    if(kw != '')
     {
       $.post('<?php echo site_url('/clients/searchAccount');?>',{'search':kw},
        function(data){
			if(data !=""){
				console.info(data);
				$("div.resultAccount").empty();
				$("div.resultAccount").append("<div class='headSearchResult'>Выберите счет: <select id='accountResult' style='word-wrap:break-word;width:100%;'></select></div>");
			$.each(data, function(i, val) {
				$("div.resultAccount select").append("<option value="+data[i].id+">"+ data[i].bindings_name+" ["+data[i].accounts+"]</option>");
			})
			}else{

				  $("div.resultAccount").empty();
				  $("div.resultAccount").append("<div class='headSearchResult'>ЛС отсутствует в базе</div><br/>");
			}
		},'json');
     }
     else
     {
		//$('div.resultAccount select').empty();
		$(".resultAccount").empty();
		$(".resultAccount").html("");
     }
    return false;
  });
	$('#copyAccount2ExternalDialog').dialog({
				position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
				resizable: false,
                show:'slide',
                buttons: {
					"Копировать": function() {
					var newCopyAccount	= $('div.resultAccount select').val();
					var close_date		= $('p.field input#end_date_copy').val();
					var open_date		= $('p.field input#datepicker1_copy').val();
					console.info(newCopyAccount);
					console.info(close_date);
					console.info(open_date);
					console.info(id);
					console.info(id_client);
					$.post('<?= site_url('clients/copyAccount2Account'); ?>',
					{
						'old_id_account':id,'id_client':id_client, 'newCopyAccount':newCopyAccount,
						'close_date':close_date, 'open_date':open_date
					},

						function(data){

							$('#dialogCopyFinish').append(data);
							$('#copyAccount2ExternalDialog').dialog('close');
							$('#dialogCopyFinish').dialog({
								position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
								modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
								draggable:false,
								resizable: false,
								show:'slide',
								buttons: {
								"OK": function() {
									$(this).dialog("close");
									window.localtion.reload();
								}}
							});
						},'json')
                 }
                ,"Закрыть": function() {
                $(this).dialog("close");
                 }}
			 });
}

function copyAccount(id,id_client){
	$('#copyOptionsDialog').dialog('close');
	$.post('<?= site_url('clients/getAccountListByIdClient'); ?>',{'id':id,'id_client':id_client},

				function(data){
					$('#copyAccountDialog').empty();
					$('#copyAccountDialog').append('<div class="alert_warning" style="margin:5px;">Cоздайте однотипный ЛС</div>');
					$('#copyAccountDialog').append('<div class="main">Выберите лицевой счет: <select id="accountCopyList"></select></div>\n\
	<p class="field">Дата окончания:<input type="text" name="end_date" id="end_date_copy" readonly/></p><p class="field">Дата начала:<input type="text" name="datepicker1" id="datepicker1_copy" readonly/></p>');
				$('p.field input#end_date_copy').datepicker($.datepicker.regional["ru"]);
				$('p.field input#datepicker1_copy').datepicker($.datepicker.regional["ru"]);
					$.each(data, function(i, val) {
						$('#copyAccountDialog select').append('<option value="'+data[i].id+'">'+data[i].accounts+'</option>');
					})
				},'json');

				$('#copyAccountDialog').dialog({
				position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
				resizable: false,
                show:'slide',
                buttons: {
					"Копировать": function() {
					var newCopyAccount	= $('#accountCopyList').val();
					var close_date		= $('p.field input#end_date_copy').val();
					var open_date		= $('p.field input#datepicker1_copy').val();
					$.post('<?= site_url('clients/copyAccount2Account'); ?>',
					{
						'old_id_account':id,'id_client':id_client, 'newCopyAccount':newCopyAccount,
						'close_date':close_date, 'open_date':open_date
					},

						function(data){

							$('#dialogCopyFinish').append(data);
							$('#copyAccountDialog').dialog('close');
							$('#dialogCopyFinish').dialog({
								position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
								modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
								draggable:false,
								resizable: false,
								show:'slide',
								buttons: {
								"OK": function() {
									$(this).dialog("close");
									window.localtion.reload();
								}}
							});
						},'json')
                 }
                ,"Закрыть": function() {
                $(this).dialog("close");
                 }}
			 });
}

function getGroup(id, id_client, id_account){

        $.post('<?=site_url('clients/get_group');?>',{'id':id,'id_client':id_client},

        function(data){
            $.each(data, function(i, val) {
               console.info(data[i].services_groups);

               $('#dialog').append('<a href=<?=site_url('clients/get_forms');?>/'+data[i].id+'/'+id_account+' onclick="getFormDialog('+data[i].id+','+id_account+');">'+data[i].services_groups+'</a><br/>');
               $('#dialog').parent().find('.ui-dialog-titlebar-close').hide();
               $("#dialog").dialog({
                title: "Выберите группу номеклатуры",  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
                show:'slide',
                buttons: {
                "Закрыть": function() {
                $(this).dialog("close");
                $('#dialog').empty();
                 }}
                });

    });
         },'json');

    }

</script>

<!--<a href="#" onclick="getFeofan();return false;" id="feofan">feofan</a>-->
<script type="text/javascript">

function getElement(id,id_account){

var flag=true;

$("#id_element-"+id).click(function()
        {
            var payment_name = $('input#payment_name-'+id).val();
	    //alert(payment_name);
            if(flag==true)
            {
                 $("li").not("#id_element-"+id).slideUp();
				 var link = $("#id_element-"+id); /*Если убрать строку и следующую тоже верну тогда батву с удвоением кол-ва полей в форме добавления номеклатуры в группу*/
				 link.replaceWith(link.text()); /*А так вроде отлично получилось. Ура!!!*/

                flag=!flag;
         }
         $.post('<?=site_url('services/getAssortmentById');?>',{'id':id},
        function(data){

            $.each(data, function(i, val) {
            var uniq_id = $('input[name="uniq_id"]').val();
             var id_group = $('input[name="id_group"]').val();
	    if(data[i].payment_type=='ПЕРИОДИЧЕСКИ'){

	if(data[i].element_type!='identifier' & data[i].element_type!='input' & data[i].element_type!='select' & data[i].tariff==1){
	   console.info(data[i].id);

                $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append("<input type='hidden' name='payment_name' value='"+payment_name+"'><input type='hidden' name='id_account' value="+id_account+"><input type='hidden' name='id_group' value="+id_group+"><input type='hidden' name='uniq_id' value="+uniq_id+"><select name='tariff' id='tariff'></select><p>Период: <br/><select name='period' id='period'><option value='month'>Месяц</option><option value='half_month'>Полмесяца</option></select></p><p>Дата начала: <br/><input  id='datepicker1' type='text' name='datepicker1' value='' /></p>");
         $.post('<?=site_url('services/getTariffId');?>',{'id':data[i].id},
                 function(data){
		     console.info(data);
                     $.each(data, function(i, val) {
                         console.info(data[i].tariff_name);
                         console.info(data[i].id);
                         $('#dialog2 select[name=tariff]').append('<option value="'+data[i].id+'">'+data[i].tariff_name+'</option>');
                     })
                 },'json');
         $.datepicker.setDefaults($.datepicker.regional['']);
         $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
	}
            if(data[i].element_type=='input' & data[i].tariff==1){ //Наличие тарифа в инпутах
               console.info(data[i].id);

                $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append("<input type='hidden' name='payment_name' value='"+payment_name+"'><input type='hidden' name='id_account' value="+id_account+"><input type='hidden' name='id_group' value="+id_group+"><input type='hidden' name='uniq_id' value="+uniq_id+"><input type='hidden' name='name' /><select name='tariff' id='tariff'></select><p>Период: <br/><select name='period' id='period'><option value='month'>Месяц</option><option value='half_month'>Полмесяца</option></select></p><p>Дата начала: <br/><input  id='datepicker1' type='text' name='datepicker1' value='' /></p>");
         $.post('<?=site_url('services/getTariffId');?>',{'id':data[i].id},
                 function(data){
		     console.info(data);
                     $.each(data, function(i, val) {
                         console.info(data[i].tariff_name);
                         console.info(data[i].id);
                         $('#dialog2 select[name=tariff]').append('<option value="'+data[i].id+'">'+data[i].tariff_name+'</option>');
                     })
                 },'json');
         $.datepicker.setDefaults($.datepicker.regional['']);
         $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
        }	//Отсутствие тарифа в инпутах
	if(data[i].element_type=='input' & data[i].tariff!=1){
	     $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append("<input type='hidden name='payment_name' value='"+payment_name+"'><input type='hidden' name='id_account' value="+id_account+"><input type='hidden' name='id_group' value="+id_group+"><input type='hidden' name='uniq_id' value="+uniq_id+"><input type='hidden' name='name' /><p>Дата начала: <br/><input  id='datepicker1' type='text' name='datepicker1' value='' /></p>");
         $.datepicker.setDefaults($.datepicker.regional['']);
         $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
	}
            //Наличие тарифа в селектах
            if(data[i].element_type=='select' & data[i].tariff==1){
                $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append("<input type='hidden' name='payment_name' value='"+payment_name+"'><input type='hidden' name='id_account' value="+id_account+"><input type='hidden' name='id_group' value="+id_group+"><input type='hidden' name='uniq_id' value="+uniq_id+"><select name='resources' id='resources'></select><select name='tariff' id='tariff'></select><p>Период: <br/><select name='period' id='period'><option value='month'>Месяц</option><option value='half_month'>Полмесяца</option></select></p><p>Дата начала: <br/><input  id='datepicker1' type='text' name='datepicker1' value='' /></p>");
                $.post('<?=site_url('services/getTariffId');?>',{'id':data[i].id},
                 function(data){
		     console.info(data);
                     $.each(data, function(i, val) {
                         console.info(data[i].tariff_name);
                         console.info(data[i].id);
                         $('#dialog2 select[name=tariff]').append('<option value="'+data[i].id+'">'+data[i].tariff_name+'</option>');
                     })
                 },'json');
	$.datepicker.setDefaults($.datepicker.regional['']);
                $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
                console.info(data[i].target);

                 $.post('<?=site_url('clients/getResources');?>',{'table':data[i].target,'type_resources':data[i].type_resources},
                 function(data){
                     $.each(data, function(i, val) {
                         console.info(data[i].resources);
                         console.info(data[i].type);
                         $('#dialog2 select[name=resources]').append('<option value="'+data[i].id+'">'+data[i].resources+'</option>');
                     })
                 },'json');
            }	    //Отсутствие тарифа в селектах
	    if(data[i].element_type=='select' & data[i].tariff!=1){

	      $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append('<input type="hidden" name="payment_name" value="'+payment_name+'"><input type="hidden" name="id_account" value="'+id_account+'"><input type="hidden" name="id_group" value="'+id_group+'"><input type="hidden" name="uniq_id" value="'+uniq_id+'"><select name="resources" id="resources"></select><p>Дата начала: <br/><input  id="datepicker1" type="text" name="datepicker1" value="" /></p>');
                $.datepicker.setDefaults($.datepicker.regional['']);
                $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
                console.info(data[i].target);

                 $.post('<?=site_url('clients/getResources');?>',{'table':data[i].target,'type_resources':data[i].type_resources},
                 function(data){
                     $.each(data, function(i, val) {
                         console.info(data[i].resources);
                         console.info(data[i].type);
                         $('#dialog2 select[name=resources]').append('<option value="'+data[i].id+'">'+data[i].resources+'</option>');
                     })
                 },'json');

	    }
            }

      if(data[i].payment_type=='ЕДИНОВРЕМЕННО'){
	if(data[i].element_type!='identifier' & data[i].element_type!='input' & data[i].element_type!='select' & data[i].tariff==1){
	   console.info(data[i].id);

                $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append('<input type="hidden" name="payment_name" value="'+payment_name+'"><input type="hidden" name="id_account" value="'+id_account+'"><input type="hidden" name="id_group" value="'+id_group+'"><input type="hidden" name="uniq_id" value="'+uniq_id+'"><select name="tariff" id="tariff"></select><p>Период: <br/><select name="period" id="period"><option value="single_payment">Разовый платеж</option></select></p><p>Дата начала: <br/><input  id="datepicker1" type="text" name="datepicker1" value="" /></p>');
         $.post('<?=site_url('services/getTariffId');?>',{'id':data[i].id},
                 function(data){
		     console.info(data);
                     $.each(data, function(i, val) {
                         console.info(data[i].tariff_name);
                         console.info(data[i].id);
                         $('#dialog2 select[name=tariff]').append('<option value="'+data[i].id+'">'+data[i].tariff_name+'</option>');
                     })
                 },'json');
         $.datepicker.setDefaults($.datepicker.regional['']);
         $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
	}
            if(data[i].element_type=='input' & data[i].tariff==1){ //Наличие тарифа в инпутах
               console.info(data[i].id);

                $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append('<input type="hidden" name="payment_name" value="'+payment_name+'"><input type="hidden" name="id_account" value="'+id_account+'"><input type="hidden" name="id_group" value="'+id_group+'"><input type="hidden" name="uniq_id" value="'+uniq_id+'"><input type="text" name="name" /><select name="tariff" id="tariff"></select><p>Период: <br/><select name="period" id="period"><option value="single_payment">Разовый платеж</option></select></p><p>Дата начала: <br/><input  id="datepicker1" type="text" name="datepicker1" value="" /></p>');
         $.post('<?=site_url('services/getTariffId');?>',{'id':data[i].id},
                 function(data){
		     console.info(data);
                     $.each(data, function(i, val) {
                         console.info(data[i].tariff_name);
                         console.info(data[i].id);
                         $('#dialog2 select[name=tariff]').append('<option value="'+data[i].id+'">'+data[i].tariff_name+'</option>');
                     })
                 },'json');
         $.datepicker.setDefaults($.datepicker.regional['']);
         $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
        }	//Отсутствие тарифа в инпутах
	if(data[i].element_type=='input' & data[i].tariff!=1){
	     $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append('<input type="hidden" name="payment_name" value="'+payment_name+'"><input type="hidden" name="id_account" value="'+id_account+'"><input type="hidden" name="id_group" value="'+id_group+'"><input type="hidden" name="uniq_id" value="'+uniq_id+'"><input type="text" name="name" /><p>Дата начала: <br/><input  id="datepicker1" type="text" name="datepicker1" value="" /></p>');
         $.datepicker.setDefaults($.datepicker.regional['']);
         $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
	}
            //Наличие тарифа в селектах
            if(data[i].element_type=='select' & data[i].tariff==1){
                $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append('<input type="hidden" name="payment_name" value="'+payment_name+'"><input type="hidden" name="id_account" value="'+id_account+'"><input type="hidden" name="id_group" value="'+id_group+'"><input type="hidden" name="uniq_id" value="'+uniq_id+'"><select name="resources" id="resources"></select><select name="tariff" id="tariff"></select><p>Период: <br/><select name="period" id="period"><option value="single_payment">Разовый платеж</option></select></p><p>Дата начала: <br/><input  id="datepicker1" type="text" name="datepicker1" value="" /></p>');
                $.post('<?=site_url('services/getTariffId');?>',{'id':data[i].id},
                 function(data){
		     console.info(data);
                     $.each(data, function(i, val) {
                         console.info(data[i].tariff_name);
                         console.info(data[i].id);
                         $('#dialog2 select[name=tariff]').append('<option value="'+data[i].id+'">'+data[i].tariff_name+'</option>');
                     })
                 },'json');
	$.datepicker.setDefaults($.datepicker.regional['']);
                $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
                console.info(data[i].target);

                 $.post('<?=site_url('clients/getResources');?>',{'table':data[i].target,'type_resources':data[i].type_resources},
                 function(data){
                     $.each(data, function(i, val) {
                         console.info(data[i].resources);
                         console.info(data[i].type);
                         $('#dialog2 select[name=resources]').append('<option value="'+data[i].id+'">'+data[i].resources+'</option>');
                     })
                 },'json');
            }	    //Отсутствие тарифа в селектах
	    if(data[i].element_type=='select' & data[i].tariff!=1){

	      $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append('<input type="hidden" name="payment_name" value="'+payment_name+'"><input type="hidden" name="id_account" value="'+id_account+'"><input type="hidden" name="id_group" value="'+id_group+'"><input type="hidden" name="uniq_id" value="'+uniq_id+'"><select name="resources" id="resources"></select><p>Дата начала: <br/><input  id="datepicker1" type="text" name="datepicker1" value="" /></p>');
                $.datepicker.setDefaults($.datepicker.regional['']);
                $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
                console.info(data[i].target);

                 $.post('<?=site_url('clients/getResources');?>',{'table':data[i].target,'type_resources':data[i].type_resources},
                 function(data){
                     $.each(data, function(i, val) {
                         console.info(data[i].resources);
                         console.info(data[i].type);
                         $('#dialog2 select[name=resources]').append('<option value="'+data[i].id+'">'+data[i].resources+'</option>');
                     })
                 },'json');

	    }
            }

			  if(data[i].payment_type=='billing'){
	if(data[i].element_type!='input' & data[i].element_type!='select' & data[i].element_type!='identifier'){
	   console.info(data[i].id);

                $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append('<input type="hidden" name="payment_name" value="'+payment_name+'"><input type="hidden" name="id_account" value="'+id_account+'"><input type="hidden" name="id_group" value="'+id_group+'"><input type="hidden" name="uniq_id" value="'+uniq_id+'"><p>Дата начала: <br/><input  id="datepicker1" type="text" name="datepicker1" value="" /></p>');
         $.post('<?=site_url('services/getTariffId');?>',{'id':data[i].id},
                 function(data){
		     console.info(data);
                     $.each(data, function(i, val) {
                         //console.info(data[i].tariff_name);
                         //console.info(data[i].id);
                         //$('#dialog2 select[name=tariff]').append('<option value="'+data[i].id+'">'+data[i].tariff_name+'</option>');
                     })
                 },'json');
         $.datepicker.setDefaults($.datepicker.regional['']);
         $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
	}
	if(data[i].element_type=='identifier' & data[i].tariff!=1){
	   console.info(data[i].id);

                $('#dialog2 ol').append('<form name="form" id="form"></form>');
                $('#dialog2 form[name=form]').append('<input type="hidden" name="payment_name" value="'+payment_name+'"><input type="hidden" name="id_account" value="'+id_account+'"><input type="hidden" name="id_group" value="'+id_group+'"><input type="hidden" name="uniq_id" value="'+uniq_id+'"><input type="text" name="identifier" /><p>Дата начала: <br/><input  id="datepicker1" type="text" name="datepicker1" value="" /></p>');
         $.datepicker.setDefaults($.datepicker.regional['']);
         $('#dialog2 form[name=form] p input#datepicker1').datepicker($.datepicker.regional["ru"]);
	}
	}

            })
        },'json');

    });
}

<?
//@TODO: Сделать метод добавления отдельной номенклатуры - внимание на form_data в функции getAllAssortment
?>
    function getAllAssortment(id_service,id){

        $.post('<?=site_url('clients/getAssortmentByService');?>',{'id':id_service},
        function(data){

            $.each(data, function(i, val) {
             $('#dialog2 ol').append("<li id=id_element-"+data[i].id+"><a href='#' onclick=getElement('"+data[i].id+"','"+id+"');return false;>"+data[i].payment_name+"</a><input type='hidden' id='payment_name-"+data[i].id+"' value='"+data[i].payment_name+"' /></li>");
             $("#dialog2").dialog({
                 title:'Добавление номеклатуры в группу на ЛС',
                 position: 'center',
                 draggable:false,
                 modal: false,
                 width:600,
                 buttons: {
                 "Добавить номенклатуру": function() {
                     var form_data = $('.rounded-list #form').serialize();
					 console.info(form_data);
                      $.post('<?=site_url('clients/add_assortment_item');?>', $('.rounded-list #form').serialize(),
                            function(data){
                               $('#dialog2').dialog("close");
                               $('.rounded-list').empty();
                                //Доработать обновление таблицы после добавления номенклатуры
                            })
                    },
                 "Закрыть": function() {
                $(this).dialog("close");
                $('.rounded-list').empty();
                 }}

             });
            });
        },'json');
    }
   function getAssortmentByUniqId(uniq_id){
   $('#tablesorter').tablesorter();
$('#payments_table').append('<table id="tablesorter" class="tablesorter"><thead><tr><th>ID</th><th>Наименование</th><th>Сумма</th><th>Начало периода</th><th>Конец периода</th><th>Период</th></tr></thead></table>');
   $.post('<?=site_url('clients/getAccrualsInGroup');?>',{'uniq_id':uniq_id},
   function(data){

      $.each(data, function(i, val) {
      console.info(data[i].payment_name);
      $('#tablesorter').append('<tr><td>'+data[i].id+'</td><td>'+data[i].payment_name+'</td><td>'+data[i].amount+'</td><td>'+data[i].period_start+'</td><td>'+data[i].period_end+'</td><td>'+data[i].period+'</td></tr>');
      });
      $('#payments_table').dialog(
	    {
                 title:'Начисления по номенклатурам группы',
                 position: 'center',
                 draggable:false,
                 modal: true,
                 width:600,
				 height:400,
	 buttons: {
                 "Закрыть": function() {
                $(this).dialog("close");
                $('#tablesorter').empty();
				$('#payments_table').empty();
                 }}
	 });
   },'json')
   }
    function delPayments(id){
    $.post('<?=site_url('services/delCustomerPayments');?>',{'id':id},
        function(data){
	    $('#payments_table').dialog('close');
	    $('#payments_table').empty();
	    oTable.fnClearTable( 0 );
	    oTable.fnDraw();
	    bTable.fnClearTable( 0 );
	    bTable.fnDraw();
	})
    }
    function getAssortmentById(id_assortment){

$('#dialog2').empty();
$('#tablesorter').tablesorter();
$('#payments_table').append('<table id="tablesorter" class="tablesorter"><thead><tr><th>Номер ЛС</th><th>Начало периода</th><th>Конец периода</th><th>Сумма</th><th>Действие</th></tr></thead></table>');
 $.post('<?=site_url('clients/getCustomerPayments');?>',{'id':id_assortment},
        function(data){

	  $.each(data, function(i, val) {
	console.info(data[i].id);
	$('#tablesorter').append('<tr><td>'+data[i].id_account+'</td><td>'+data[i].period_start+'</td><td>'+data[i].period_end+'</td><td>'+data[i].amount+'</td><td><a href="#" onclick="delPayments('+data[i].id+')">Удалить</a></td></tr>');

	});

	    $('#payments_table').dialog({

                 title:'Начисления по номенклатуре',
                 position: 'center',
                 draggable:false,
                 modal: true,
                 width:600,
	 buttons: {
                 "Закрыть": function() {
                $(this).dialog("close");
				$('#tablesorter').empty();
                $('#payments_table').empty();
                 }}
	 });
	},'json');

    }

function editAssortmentById(id_assortment){
 $.post('<?=site_url('clients/getCustomerServices');?>',{'id':id_assortment},
function(data){

$.each(data, function(i, val) {
	$('#payments_table').append('<div class="main">	<div class="field"><label for="datepicker">Дата начала: </label><input type="text" name="datepicker" id="datepicker" value="'+data[i].datepicker1+'"/></div><div class="field"><label for="end_date">Дата окончания: </label><input type="text" id="end_date" name="end_date" value="'+data[i].end_date+'"/></div></div>');
	});
	$(function() {
$( "#datepicker" ).datepicker({
showOn: "button",
buttonImage: "/assets/images/edit_date.png",
buttonImageOnly: true,
minDate:new Date(2012, 8 - 1, 1)
});
});

$(function() {
$( "#end_date" ).datepicker({
showOn: "button",
buttonImage: "/assets/images/edit_date.png",
buttonImageOnly: true,
minDate:new Date(2012, 8 - 1, 1)
});
});

	    $('#payments_table').dialog({

                 title:'Редактирование данных',
                 position: 'center',
                 draggable:false,
                 modal: true,
                 width:350,
	 buttons: {
				"Изменить": function() {

					var end_date = $("#end_date").val();
					var datepicker = $("#datepicker").val();
                      $.post('<?=site_url('clients/updateEndDate');?>', {'id':id_assortment,'end_date':end_date,'datepicker':datepicker},
                            function(data){
				               $("#payments_table").dialog("close");
							   $("#payments_table").empty();
                               $.jnotify("Дата окончания изменена.",{remove: function (){
			bTable.fnClearTable( 0 );
			bTable.fnDraw();}});
                            })
                    },
                 "Закрыть": function() {
                $(this).dialog("close");
                $('#payments_table').empty();
                 }}
	 });

	},'json');

    }

function deleteAssortmentById(id){
    $.post('<?=site_url('services/getAssortmentInfoPayments');?>',{'id':id},
    function(data){
    if(data>0){
	$.jnotify("По данной номеклатуре есть начисления. Удаление невозможно.","error",{remove: function (){}});
    }
    if(data==0){

	console.info(data);
	$('#dialog3').append("<div align='center' style='vertical-align:middle;'>Вы действительно хотите удалить номеклатуру?</div>");
	 $("#dialog3").dialog({
                 title:'Удаление номеклатуры',
                 position: 'center',
                 draggable:false,
                 modal:true,
				 resizable:false,
                 width:350,
                 buttons: {
                 "ДА": function() {

                      $.post('<?=site_url('services/deleteAssortmentItem');?>', {'id':id},
                            function(data){
								$('#dialog3').dialog("close");
								$('#dialog3').empty();
								$.jnotify("Номеклатура удалена.",{remove: function (){
								bTable.fnClearTable( 0 );
								bTable.fnDraw();}});

                            })
                    },
                 "НЕТ": function() {
                $(this).dialog("close");
                $('#dialog3').empty();
                 }}

             });
    }
},'json');
}

function getAssortmentFromGroup(uniq_id,id_service,id){


//console.info(uniq_id+' ---'+id_group);
//
//var aPos = oTable.fnGetPosition(this);
//var aData = oTable.fnGetData(aPos[0]);
//var id_group = aData['id_group'];
//var uniq_id = aData['uniq_id'];

$('body').prepend('<input type="hidden" name="uniq_id" class="uniq_id" value="'+uniq_id+'" />');
var editor;
$('#assortment_table').show();

bTable = $('#assortment').dataTable({

        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "bServerSide"  : true,
        "bProcessing": true,
        "bDestroy": true,
        "sScrollY": "300px",
		"oLanguage": {
			"sUrl": "/assets/js/russian-language-DataTables.txt"
		},
        "bPaginate": false,
        "sAjaxSource"  : "<?php echo site_url();?>/clients/getCustomerAssortment/"+uniq_id,

        "aoColumns": [{ "bVisible":    false, 'mDataProp':'id' },{ "sTitle": "Наименование номенклатуры", "mDataProp": "payment_name","sWidth": "40%"}, { "sTitle": "Ресурс", "mDataProp": "resources","sWidth": "10%" },{ "sTitle": "Идентификатор", "mDataProp": "identifier","sWidth": "10%" },
        { "sTitle": "Тариф", "mDataProp": "tariff_name","sWidth": "20%" },
        { "sTitle": "Дата начала", "mDataProp": "datepicker1","sWidth": "10%" },
        { "sTitle": "Дата окончания", "mDataProp": "end_date"},
         { "fnRender": function ( oObj ) {
				id_rec = oObj.aData['id'].toString();
				return "<a href='#' id='mybutton'  onClick=editAssortmentById('"+id_rec+"');><img src='/assets/images/edit_date.png' alt='Редактирование' title='Редактирование'/></a>";
		},"mDataProp":null},
		
		{ "fnRender": function ( oObj ) {
        
		resources = oObj.aData['resources'];
		identifier = oObj.aData['identifier'];
		id_account = oObj.aData['id_account'].toString();
		id_clients = oObj.aData['id_clients'].toString();
		tariffs = oObj.aData['tariffs'].toString();
		price = oObj.aData['price'];
		
		if ( resources || identifier){
			return "";
		}else{
			return "<a href='#' id='mybutton'  onClick=addAccrual('"+id_rec+"','"+id_account+"','"+id_clients+"','"+tariffs+"','"+price+"');><img src='/assets/images/add_currency.gif' alt='Произвести начисления' title='Произвести начисления'/></a>";
		}

	},"mDataProp":null},
        { "fnRender": function ( oObj ) {
              id_rec = oObj.aData['id'].toString();
		return "<a href='#' id='mybutton'  onClick=getAssortmentById('"+id_rec+"');><img src='/assets/images/coins.png' alt='Начисления' title='Начисления'/></a>";
	},"mDataProp":null},
        { "fnRender": function ( oObj ) {
              id_rec = oObj.aData['id'].toString();
		return "<a href='#' id='mybutton'  onClick=deleteAssortmentById('"+id_rec+"');><img src='/assets/images/delete-row.png' alt='Удаление номеклатуры' title='Удаление номеклатуры'/></a>";
	},"mDataProp":null},],

         "fnServerData": function(sSource, aoData, fnCallback)
              {
                  aoData.push(  {"name": "uniq_id", "value":  uniq_id } );
                 $.ajax(
                      {
                        'dataType': 'json',
                        'type'  : 'POST',
                        'url'    : sSource,
                        'data'  : aoData,
                        'success' : fnCallback
                      }
                  );
              },
			  "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
    // create a bubble popup for this row
	nRow.setAttribute('id',id_rec);
    if ( aData[5] != "NULL" || aData[5] != "0000-00-00" ) {
        $('td:eq(5)', nRow).css('color','Red');
		$('td:eq(5)', nRow).css('font-weight','bold');

    }
    return nRow;
}
}).makeEditable({
		sUpdateHttpMethod: "POST",
		"aoColumns": [
                        null,
                        null,
                        {
                                indicator: 'Сохранение идентификатора...',
                                tooltip: '',
								placeholder:'',
                                type: 'textarea',
                                submit: 'Сохранить'}
							],fnOnEditing: function(input)
                             {
									var new_identifier = input.val();
									$.post('<?=site_url('services/updateIdentificator');?>',{'id':id_rec, 'identifier':new_identifier,'id_account':id},
									function(data){

										$('#change_identificator_status').append('Идентификатор успешно изменен.');
										$('#change_identificator_status').dialog({
										resizable: false,
										height:150,
										modal: true,
										buttons: {
										  "OK": function() {
											  $( this ).dialog( "close" );
											  window.location.reload();
										  }}
										});
									},'json')
                            }
});


$('#link2').empty();
$('#link2').append('<p><button id="add_assortment_button" type="submit" value="Добавить номенклатуру" onclick="getAllAssortment('+id_service+','+id+'); return false;" >Добавить номенклатуру</button></p>');
$('#add_assortment_button').button({ icons: {primary:'ui-icon-plus'} });

}

 function getAssortmentsPaymentsInGroup(uniq_id){

      $.post('<?=site_url('services/getAssortmentsPaymentsInGroup');?>',{'uniq_id':uniq_id},
        function(data){
	    if(data>0){
	$.jnotify("По номеклатурам в группе есть начисления. Удаление невозможно.","error",{remove: function (){}});
    }
    if(data==0){
	$('#dialog3').append("<div align='center' style='vertical-align:middle;'>Вы действительно хотите удалить группу?</div>");
	 $("#dialog3").dialog({
                 title:'Удаление группы',
                 position: 'center',
                 draggable:false,
                 modal:true,
	 resizable:false,
                 width:350,
                 buttons: {
                 "ДА": function() {

                      $.post('<?=site_url('services/deleteGroupAssortments');?>', {'uniq_id':uniq_id},
                            function(data){
                               $('#dialog3').dialog("close");
	               $('#dialog3').empty();
                               $.jnotify("Группа номеклатур удалена.",{remove: function (){
			oTable.fnClearTable( 0 );
			oTable.fnDraw();
			bTable.fnClearTable( 0 );
			bTable.fnDraw();
		}});

                            })
                    },
                 "НЕТ": function() {
                $(this).dialog("close");
                $('#dialog3').empty();
                 }}

             });
    }
	},'json')
 }
//{ "sTitle": "Наименование группы", "mDataProp": "services_groups","sWidth": "50%"},
  function getCustomerGroup(id,id_service,id_client){
  $('button').button({});
  $('#group_table').show();

       oTable =$('#example').dataTable({

        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "bServerSide"  : true,
        "bProcessing": true,
        "bDestroy": true,
        "sScrollY": "300px",
        "bPaginate": false,
        "bInfo":false,
        "bFilter":true,
		"oLanguage": {
						"sUrl": "/assets/js/russian-language-DataTables.txt"
		},
        "sAjaxSource"  : "<?php echo site_url();?>clients/getCustomerGroup/"+id,
        "aoColumns": [{ "sTitle": "№", "mDataProp": null,"sWidth": "10%"},{ "sTitle": "Наименование группы", "mDataProp": null,"sWidth": "50%"},{ "sTitle": "Ресурс", "mDataProp": "resources","sWidth": "80%" },{ "sTitle": "Идентификатор", "mDataProp": "identifier","sWidth": "80%" },
        { "sTitle": "Долг", "mDataProp": "balance","sWidth": "20%" },{ "fnRender": function ( oObj ) {
                var uniq_id = oObj.aData['uniq_id'].toString();
	//var id_group = oObj.aData['id_group'].toString();
		return "<a href='#' id='getAssortment' name='getAssortment' onclick=getAssortmentFromGroup('"+uniq_id+"','"+id_service+"','"+id+"')><img src='/assets/images/list.png' alt='Список номенклатуры' title='Список номенклатуры'/></a>";
	},"mDataProp":null},{ "fnRender": function ( oObj ) {
                var uniq_id = oObj.aData['uniq_id'].toString();
		return "<a href='#' id='mybutton'  onClick=getAssortmentByUniqId('"+uniq_id+"');><img src='/assets/images/coins.png' alt='Начисления' title='Начисления'/></a>";
	},"mDataProp":null},{ "fnRender": function ( oObj ) {
                var uniq_id = oObj.aData['uniq_id'].toString();
	//var id_group = oObj.aData['id_group'].toString();
		return "<a href='#' id='getAssortmentIds' name='getAssortmentIds' onclick=getAssortmentsPaymentsInGroup('"+uniq_id+"')><img src='/assets/images/delete-row.png' alt='Удаление группы' title='Удаление группы'/></a>";
	},"mDataProp":null}],

         "fnServerData": function(sSource, aoData, fnCallback)
              {
                  aoData.push(  {"name": "id", "value":  id } );
                 $.ajax(
                      {
                        'dataType': 'json',
                        'type'  : 'POST',
                        'url'    : sSource,
                        'data'  : aoData,
                        'success' : fnCallback
                      }
                  );
              },
			  "fnDrawCallback":function(){
                                            table_rows = oTable.fnGetNodes();
                                            $.each(table_rows, function(index){
                                                    $("td:first", this).html(index+1);
                                                    });
                                           }

});

$('#link').empty();

$('#link').append('<p><button id="add_assortment_group" type="submit" value="Добавить группу" onclick="getGroup('+id_service+','+id_client+','+id+'); return false;" >Добавить группу</button></p>');
$('#add_assortment_group').button({ icons: {primary:'ui-icon-plus'} });
/*ТУта!!!!!*/


}
</script>
<script type="text/javascript">
    $('.ui-dialog-content ui-widget-content input#datepicker1').focus(function(event) {

    });
  </script>
<script type="text/javascript">
  $(document).ready(function(){

		$( "#account_date" ).datepicker({
			showOn: "button",
			buttonImage: "/assets/images/edit_date.png",
			buttonImageOnly: true,
			minDate:new Date(2012, 8 - 1, 1)
		});

      $('#edit_client_button').button({ icons: {primary:'ui-icon ui-icon-pencil'},text:false });
      $('.edit_account_button').button({ icons: {primary:'ui-icon ui-icon-pencil'},text:false });
	  $('.copy_account_button').button({ icons: {primary:'ui-icon ui-icon-copy'},text:false });
      $('.delete_account_button').button({ icons: {primary:'ui-icon ui-icon-trash'},text:false });
      $('button#mybutton').button({ icons: {primary:'ui-icon ui-icon-trash'},text:false });
        // Скрываем все спойлеры
        $('.fbbluebox').hide()
        // по клику отключаем класс folded, включаем unfolded, затем для следующего
        // элемента после блока .spoiler-head (т.е. .spoiler-body) показываем текст спойлера
        $('.spoiler-head').click(function(){
            $(this).toggleClass("folded").toggleClass("unfolded").next().toggle()
        })

     $('#button_map').click(function(){
	$('#map').dialog({
                    title:'Карта',
                    width:500,
                    height:500,
                    modal:true,
                    draggable:false,
                    resize:false,
                    open:function(event, ui) {
                google.maps.event.trigger(map, 'resize');

                }
    });


});
    })

 function editClientInfo(id){

            $.post('<?=site_url('clients/getClientById');?>',{'id':id},
        function(data){
            console.info(data);
            $.each(data, function(i, val) {
				$('#id').val(data[i].id);
				$('#client_name').val(data[i].client_name);
                $('#client_address').val(data[i].client_address);
				$('#account_date').val(data[i].account_date);
                $('#inn').val(data[i].inn);
                $('#client_manager').val(data[i].client_manager);
                $('#phone_number').val(data[i].phone_number);
                $('#client_email').val(data[i].client_email);
				$('#post_client_address').val(data[i].post_client_address);
				$('#kpp').val(data[i].kpp);
            })
            $('#edit_client_form').dialog({
                title: "Редактирование реквизитов клиента",  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
                resizable: false,
                show:'slide',
				width:'420px',
                buttons: {
                "Сохранить изменения": function() {
                    //var form_data = $('form.form').serialize();
					var id = $('#id').val();
				var client_name = $('#client_name').val();
                var client_address = $('#client_address').val();
				var account_date = $('#account_date').val();
                var inn = $('#inn').val();
                var client_manager = $('#client_manager').val();
                var phone_number = $('#phone_number').val();
                var client_email = $('#client_email').val();
				var post_client_address = $('#post_client_address').val();
				var kpp = $('#kpp').val();
                    console.info(client_name);

                    $.post('<?=site_url('clients/editClientInfo');?>', {'id':id,'client_name':client_name, 'client_address':client_address, 'inn':inn, 'client_manager':client_manager,
				'phone_number':phone_number, 'client_email':client_email,'post_client_address':post_client_address,'kpp':kpp,'account_date':account_date
				},
                            function(data){
                               $('#edit_client_form').dialog("close");
                               $.jnotify("Реквизиты клиента изменены.", {remove: function (){ window.location.reload(); }});
                                //Доработать обновление таблицы после добавления номенклатуры
                            })
                    },

                "Закрыть": function() {
                $(this).dialog("close");
                 }}

            });
        },'json')

        };

function deleteAccount(id, id_client){

    $.post('<?=site_url('services/deleteClientAccounts');?>',{'id':id,'id_client':id_client},
	    function(data){
		console.info(data.result);
		if(data.result=='1'){
		$.post('<?=site_url('services/deleteAccount');?>',{'id':id,'id_client':id_client},
		    function(data){

			$.jnotify('Лицевой счет удален', {remove: function (){location.reload();}});
		    })

		}else{
		    $.jnotify(data.result, 'error',{remove: function (){}});
		}

	    },'json');
}

</script>
<script type="text/javascript">
    $(document).ready(function(){
var id_client = $('input[name="id_client_current"]').val();

	$.post('<?=site_url('services/add_accounts');?>',{'id':id_client},
	    function(data){
	  if(data.count!=0){
		$.each(data, function(i, val) {
		$('#add_account_list').append('<option value='+data[i].id+'>'+data[i].service_description+'</option>');
		})
		$('.add_account_button').button({ icons: {primary:'ui-icon ui-icon-circle-plus'}});
	  }else{
	      $('#my').hide();
	      $('.add_account_button').hide();
	  }
	},'json')
$('button#add_account_button.add_account_button').click(function(){
    $('#my').dialog({
	title: "Добавление ЛС клиенту",  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
                resizable: false,

                buttons: {
                "Добавить": function() {
		    var select = $('select[name="add_account"]').val();
		    var client_name = $('input[name="client_name_field"]').val();
		    var client_account = $('input[name="client_account_field"]').val();

	$.post('<?=site_url('clients/add_client_account_item');?>',{'id_client':id_client,'client_name':client_name,'client_account':client_account,'selected':select},
                            function(data){
                               $('#my').dialog("close");
		location.reload();
                               //Доработать
                            })
                   },
               "Закрыть": function() {
                $('#my').dialog("close");
                 }}
    });
    });
    });

</script>
<script type="text/javascript">
    $(document).ready(function(){
var id_client = $('input[name="id_client_current"]').val();

	$.post('<?=site_url('services/add_accounts2');?>',{'id':id_client},
	    function(data){
	  if(data.count!=0){
		$.each(data, function(i, val) {
		$('#add_account_list2').append('<option value='+data[i].id+'>'+data[i].service_description+'</option>');
		})
		$('.add_account_button').button({ icons: {primary:'ui-icon ui-icon-circle-plus'}});
	  }else{
	      $('#my2').hide();
	      $('.add_account_button').hide();
	  }
	},'json')
$('button#add_account_button2.add_account_button').click(function(){
    $('#my2').dialog({
	title: "Добавление второго ЛС клиенту",  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
                resizable: false,

                buttons: {
                "Добавить": function() {
		    var select = $('select[name="add_account2"]').val();
		    var client_name = $('input[name="client_name_field"]').val();
		    var client_account = $('input[name="client_account_field_number"]').val();
	$.post('<?=site_url('clients/add_client_account_item2');?>',{'id_client':id_client,'client_name':client_name,'client_account':client_account,'selected':select},
                            function(data){
                               $('#my2').dialog("close");
		location.reload();
                               //Доработать
                            })
                   },
               "Закрыть": function() {
                $('#my2').dialog("close");
                 }}
    });
    });
    });

</script>
<script type="text/javascript">
	function addPay(id,id_client)
	{
		$('#addPayDate').datepicker();

		$('#addPay').dialog({
			title: "Добавление оплаты на ЛС",  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
                resizable: false,
				buttons: {
                "Добавить": function() {
				var date   = $('#addPayDate').val();
				var amount = $('#addPayAmount').val().replace(/,/g, ".");
				var comment   = $('#addPayComment').val();
				var amount_type = $('#amount_type').val();
				console.info(comment);
				
				
				if(amount_type === 'add_pay'){
					url = '<?=site_url('money/addPay');?>';
					alert(url);
				}
				else if(amount_type === 'add_discount'){
					url = '<?=site_url('money/addDiscount');?>';
					alert(url);
				}
				else if(amount_type === 'add_adjust_amount'){
					url = '<?=site_url('money/addAdjustAmount');?>';
					alert(url);
				}
				

				$.post(url,{'date':date,'amount':amount,'id_account':id,'id_client':id_client,'comment':comment},
                            function(data){
                               $('#addPay').dialog("close");
								location.reload();
                               //Доработать
                            });
                   },
               "Закрыть": function() {
                $('#addPay').dialog("close");
                 }}
		});

	}

	function deletePay(id,id_comment)
	{
		$('#tablesorter tr').click(function(){
		var row = this.rowIndex+1;
		var parent = $(this);
		parent.animate({'backgroundColor':'#fb6c6c'},300).animate({ opacity: 0.35 }, "slow");
		parent.slideDown('slow', function() {$(this).remove();});
		$.post('<?=site_url('money/deletePayById');?>',{'id':id},
		function(data){

		})
		if(id_comment){
		$.post('<?=site_url('money/deletePayComment');?>',{'id_comment':id_comment},
		function(data){

		})
		}
	})
	}


   function editPay(id,account,id_comment)
   {
	   $('#editPayDate').datepicker();
	   $('#allPay').dialog('close');
	   $.post('<?=site_url('money/getPayById');?>',{'id':id},
		function(data){
			$.each(data, function(i, val) {
				$('#editPayDate').val(data[i].date);
				$('#editPayAmount').val(data[i].amount);
				$('#editPayComment').val(data[i].comment);
			})
				$('#editPay').dialog({
		        title: "Редактирование оплаты по ЛС - "+account,  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
                resizable: false,
				width:'380',
				buttons: {
                "Сохранить": function() {
					var amount = $('#editPayAmount').val().replace(/,/g, ".");
					$.post('<?= site_url('money/editPay'); ?>',{'id':id,'date':$('#editPayDate').val(),'amount':amount,'account':account},
					function(data){
						$('#editPay').dialog('close');
						$('#allPay').dialog();
					})
					if(id_comment){
						$.post('<?=site_url('money/editPayComment');?>',{'id_comment':id_comment,'comment':$('#editPayComment').val()},
						function(data){

						})
					}
				}}
	   })
		},'json')
   }

   function getAllPay(id,account)
   {
	   $('#allPay').empty();
	   $.post('<?=site_url('money/getAllPayById');?>', {'id':id},
		function(data){
			row=1
			n=1;
		$('#allPay').append('<table border="1" id="tablesorter"><tr><th>№</th><th>Дата</th><th>Сумма</th><th>Действия</th></tr></table>');
		   $.each(data, function(i, val) {
//			   console.info(data[i].comment);
		   if(!data[i].comment){
			   $('#tablesorter').append('<tr id="row_'+row+++'" ><td>'+n+++'</td><td>'+data[i].date+'</td><td>'+data[i].amount+'</td><td><a href="#" onclick=editPay('+data[i].id+',"'+account+'");return false;>edit</a> | <a href="#" onclick="deletePay('+data[i].id+');return false;">delete</a></td></tr>');
		   }else{
			   $('#tablesorter').append('<tr id="row_'+row+++'" ><td>'+n+++'</td><td>'+data[i].date+'</td><td><a href="#" id="tooltip_'+row+'" onclick="getPayComment('+data[i].id_comment+','+row+++')" style="color:green;">'+data[i].amount+'</a></td><td><a href="#" onclick=editPay('+data[i].id+',"'+account+'",'+data[i].id_comment+');return false;>edit</a> | <a href="#" onclick="deletePay('+data[i].id+','+data[i].id_comment+');return false;">delete</a></td></tr>');
		   }
		   
		   })
		},'json')

	   $('#allPay').dialog({
		   title: "Оплаты по ЛС - "+account,  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
                resizable: false,
				width:'400px',
				buttons: {
                "Закрыть": function() {
					$('#allPay').dialog("close");
					location.reload();
				}}

	   });
   }
   function getPayComment(id,selector)
   {

	   $.post('<?=site_url('money/getPayComment')?>',{'id':id},
	   function(data){
		   //alert(data);
			$("#tooltip_"+selector).qtip({
			content:{
			text: data, // Use an empty div as content so we can attach the ThemeSwitcher easily
			title: {
				text: 'Комментарий'
			}},
			style: {
				title:{
				background:'#CCC'
				},
				border: {
				width: 1,
				radius: 3,
				color: '#999999'
				}}
			});
	},'json')

   }


    </script>
<style>
    #map{
        display:none;
    }
	div#example_wrapper .dataTables_wrapper{
		height:150px;
	}

</style>

<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">

                    <div id="infoMessage" class="msg msg-error"><?php //echo $message;?></div>
                    <?php
                  foreach($client as $client_info){

                    }

//$useronline = new Clients();
//$map = $getMaps->getMap($client_info->client_address);
//echo $map['headerjs'];
//echo $map['headermap'];
//echo $map['onload'];
//echo $map['map'];


?>
		    <div id="payments_table" style="display:none;">

		    </div>
<!--      <div id="page_header"><h2>Группы на ЛС <?php print $client_info->account;?></h2></div>-->

                    <div class="spoiler-wrap">
                        <div class="spoiler-head folded">Информация о клиенте</div>
                    <div class="fbbluebox">
			<input type="hidden" name="id_client_current" value="<? echo $client_info->id_client;?>" />
			<input type="hidden" name="client_name_field" value='<? echo $client_info->client_name;?>' />
			<input type="hidden" name="client_account_field" value="<? echo $client_info->account;?>" />
                        <table>
                            <tr>
                                <td><label style="font-weight: bold;">Наименование клиента:</label><? echo $client_info->client_name;?></td>
                                <td><label style="font-weight: bold;">Номер договора:</label><? echo $client_info->account;?></td>
                                <td><label style="font-weight: bold;">Контактный телефон:</label><? echo $client_info->phone_number;?></td>
                               </tr>
                                <tr>
                                    <td><label style="font-weight: bold;">ИНН клиента:</label><? echo $client_info->inn;?></td>
                                    <td><label style="font-weight: bold;">Контактное лицо:</label><? echo $client_info->client_manager;?></td>
                                    <td><label style="font-weight: bold;">E-mail:</label><? echo $client_info->client_email;?></td>
                        </tr>
                        <tr>
                            <td><label style="font-weight: bold; vertical-align:middle;">Адрес клиента:</label><? echo $client_info->client_address; ?><input src="/assets/images/maps.png" id="button_map" type="image" style="border: 0px;vertical-align:middle" ></td>
                            <td><label style="font-weight: bold;">Дата договора:</label><?
							date_default_timezone_set('Europe/Kaliningrad');
							echo date_format(new DateTime($client_info->account_date),'d.m.Y');?></td>
                            <td><label style="font-weight: bold;">Менеджер:</label></td>
                        </tr>
						<tr>
                            <td><label style="font-weight: bold; vertical-align:middle;">Почтовый адрес клиента:</label><? echo $client_info->post_client_address; ?></td>
                            <td><label style="font-weight: bold;">КПП:</label><? echo $client_info->kpp;?></td>

                        </tr>
                        </table>
                        <div align="right">
                            <button id="edit_client_button" class="ui-state-default ui-corner-all" onclick="editClientInfo(<? echo $client_info->id_client; ?>)">Редактирование</button>
                        </div>
                    </div>
                    </div>
					<div id="allPay">

						</div>
					<div id="addPay" style="display: none;">
						<form>
							<p>Дата:<br />
								<input type="text" name="date" id="addPayDate" value="<?php echo date('d.m.Y',now());?>"/>
							</p>
							<p>Тип суммы:<br />
								<select id="amount_type">
									<option value="add_pay" selected="selected">Оплата</option>
									<option value="add_discount">Скидка</option>
									<option value="add_adjust_amount">Корректировка</option>
								</select>
							</p>
							<p>Сумма:<br />
								<input type="text" name="amount" id="addPayAmount" />
							</p>
							<p>Комментарий:<br />
								<textarea type="text" name="comment" id="addPayComment" rows='8' cols='30'></textarea>
							</p>
							<form>
					</div>
					<div id="editPay" style="display: none;">
						<form>
							<p>Дата:<br />
								<input type="text" name="date" id="editPayDate" value="<?php echo date('d.m.Y',now());?>"/>
							</p>
							<p>Сумма:<br />
								<input type="text" name="amount" id="editPayAmount" />
							</p>
							<p>Комментарий:<br />
								<textarea type="text" name="comment" id="editPayComment" rows='8' cols='30'></textarea>
							</p>
							<form>
					</div>
				<div class="spoiler-wrap">
                        <div class="spoiler-head folded">Оплаты</div>
						<div class="fbbluebox">
							<table border="1" cellspacing="0" cellpadding="0" id="clientPayments" class="tablesorter">
<?php
$k = 1;
$getPayment = new Clients_model();
$payment = $getPayment->getPaymentByAccounts($client_info->id_client);

echo '<tr>
		<th>№</th>
		<th>Лицевой счет</th>
		<th>Начислено</th>
		<th>Оплачено</th>
		<th>Скидка(руб)</th>
		<th>Действия</th>
	  </tr>';
    foreach($payment as $clients_account) {
		
		echo '<tr>';
		echo '<td>'.$k++.'</td>';
		echo '<td>'.anchor("", $clients_account->accounts, array('id' => 'add_menu','onclick'=>"getCustomerGroup('$clients_account->id_account','$clients_account->id_service','$clients_account->id_client'); return false;")).'</td>';
		if($clients_account->amount!=0){
			echo '<td>'.anchor("#", $clients_account->amount, array('id' => '','onclick'=>"getPayGroupByMonth('$clients_account->id_account','$clients_account->id_client','$clients_account->accounts'); return false;")).'</td>';
		}else{
			echo '<td>'.$clients_account->amount.'</td>';	
		}
		echo '<td>'.$clients_account->payment.'</td>';
		echo '<td>'.$clients_account->discount.'</td>';
		if($clients_account->payment!=0){
		echo '<td>'.anchor("#", "Редактировать", array('id' => '','onclick'=>"getAllPay('$clients_account->id_account','$clients_account->accounts'); return false;")).' | '.anchor("#", "Добавить оплату", array('id' => '','onclick'=>"addPay('$clients_account->id_account','$clients_account->id_client'); return false;")).'</td>';
		}else{
		echo '<td>'.anchor("#", "Добавить оплату", array('id' => '','onclick'=>"addPay('$clients_account->id_account','$clients_account->accounts','$clients_account->id_client'); return false;")).'</td>';
		}

		echo '</tr>';
		}

            ?>
</table>
						</div>

					</div>
                    <div class="content_container" id="group_table" style="display: none;">

      <div class="box-head">Группы на ЛС </div>
            <div class="box-content">
      <table border="0" cellpadding="4" cellspacing="0" id="example">
          <thead>
          </thead>
          <tbody>
              <tr>
              </tr>
          </tbody>

      </table>
      <div id="link"></div>
            </div>
      </div>

       <div class="content_container" id="assortment_table" style="display: none;">
      <div class="box-head">Номенклатура услуг на группе </div>
            <div class="box-content">
      <table border="0" cellpadding="4" cellspacing="0" id="assortment">
          <thead>
          </thead>
          <tbody>
              <tr>
              </tr>
          </tbody>

      </table>
                 <div id="link2"></div>
            </div>
	  
       </div>
					
                </div>

<div id="dialog"></div>
<div id="dialog2"><ol class="rounded-list"></ol></div>
<div id="dialog3"></div>
                </div>

                <div id="edit_client_form" style="display: none;">
                    <?php echo form_open("clients/editClientInfo", 'name=form, id=form'); ?>
	    <input type="hidden" name="id" id="id">
                    <p>Наименование клиента:<br />
                        <input type="text" size="50" name="client_name" id="client_name" />
                    </p>
					<p>Дата договора:<br />
                        <input type="text" name="account_date" id="account_date" />
                    </p>
					<p>Адрес клиента:<br />
                        <input type="text" size="50" name="client_address" id="client_address" />
                    </p>
					<p>Почтовый адрес клиента:<br />
                        <input type="text" size="50" name="post_client_address" id="post_client_address" />
                    </p>
                    <p>ИНН:<br />
                        <input type="text" name="inn" id="inn" />
                    </p>
					<p>КПП:<br />
                        <input type="text" name="kpp" id="kpp" />
                    </p>
                    <p>Контакное лицо:<br />
                        <input type="text" name="client_manager" id="client_manager" />
                    </p>

                    <p>Телефон:<br />
                        <input type="text" name="phone_number" id="phone_number" />
                    </p>

                    <p>Email:<br />
                        <input type="text" name="client_email" id="client_email" />
                    </p>
                    <?php echo form_close(); ?>
                </div>

				<div id="change_identificator_status" style="display: none;" title="Смена идентификатора"></div>
				<div id="copyAccountDialog" title="Копирование лицевого счета" style="display:none;"></div>
				<div id="copyAccount2ExternalDialog" title="Копирование лицевого счета" style="display:none;"></div>

				<div id="dialogCopyFinish" style="display:block;" title="Процесс копирования завершен"></div>
				<div id="copyOptionsDialog" style="display: none;" title="Выбор варинта копирования"></div>
	<div id="my" style="display:none;">Тип ЛС: <select name="add_account" id="add_account_list"></select></div>
	<div id="my2" style="display:none;">Тип ЛС: <select name="add_account2" id="add_account_list2"></select><p>№ ЛС*: <input type="text" name="client_account_field_number" value="" /></p><cite>* Номер ЛС формируется вручную</cite></div>

	<div class="sidebar_menu">

            <div class="box-head">Лицевые счета</div>
            <div class="box-content" id="account_div">
            <table border="0" cellspacing="0" cellpadding="0">
                <?php
                $n=1;

    foreach($client as $clients_account) {

        echo '<tr>';
        echo '<td style="padding-right:15px;">';
        print anchor("", $clients_account->accounts, array('id' => 'add_menu','onclick'=>"getCustomerGroup('$clients_account->id','$clients_account->id_service','$clients_account->id_client'); return false;"));
        echo '</td><td>';
		$counter = new Clients();
		if($counter->countAccount($clients_account->id_client)>1):
        print '<button style="font-size:8px;float:left;margin:4px;" onclick="copyOptions('.$clients_account->id.','.$clients_account->id_client.')" class="copy_account_button">Копировать</button>';
		endif;
		print '<button style="font-size:8px;margin:4px;" onclick="deleteAccount('.$clients_account->id.','.$clients_account->id_client.')" class="delete_account_button">Удалить</button></td>';
        echo '</tr>';
        }

            ?>
    </table>
		<button style="font-size:10px;margin:10px;" id="add_account_button" class="add_account_button"> Новый</button>
		<button style="font-size:10px;margin:10px;" id="add_account_button2" class="add_account_button"> Другой.№</button>
            </div>
        </div>
	
	<div id="addAccrual" style="display: none;">
						
						
						
					</div>