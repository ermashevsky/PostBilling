<script type="text/javascript">
    $(document).ready(function() {
    bTable = $('#clients').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers"
        
    });
    $("#addServiceButton").button( {
                text: true,
                icons: {
                primary: "ui-icon-gear"
                }
            }).click(function(){
                $( "#addService" ).dialog({
                 title:'Создание нового типа услуги',
                 position: 'center',
                 draggable:false,
                 modal: true,
                 width:400,
            buttons: { "Создать": function() { 
                   
                   $.post('<?=site_url('services/add_service');?>',$('#form').serialize(),
                   function(data){
                       $("#addService").dialog("close");
                       $.jnotify("Новый тип услуги создан.", {remove: function (){ window.location.reload(); }});
                      
                   })
            }}
                
            });
           
            })
            
} );

function editServiceName(id){
    $.post('<?= site_url('services/getServiceByID'); ?>',{'id':id},
	function(data){
	$.each(data, function(i, val) {
	    $('form input#id').val(data[i].id);	
	    $('form input#service_description').val(data[i].service_description);
	})
	$('#editServiceForm').dialog({
                title: "Редактирование ЛС",  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
                resizable: false,
                show:'slide',
                buttons: {
                "Сохранить изменения": function() { 
                    var form_data = $('#editform').serialize();
                    console.info(form_data);
 
                    $.post('<?=site_url('services/editServiceByID');?>', form_data,
                            function(data){
                               $('#editServiceForm').dialog("close");
                               $.jnotify("Параметры ЛС изменены.", {remove: function (){
                    window.location.reload();  }});
                                //Доработать обновление таблицы после добавления номенклатуры    
                            })
                    },    
                
                "Закрыть": function() {
                $(this).dialog("close");
                 }}
                
            });
    },'json')
}

function deleteService(id){
 $.post('<?= site_url('services/getServiceUsed'); ?>',{'id':id},
	function(data){
	    if(data==1){
		$('#dialog3').append("<div align='center' style='vertical-align:middle;'>Вы действительно хотите удалить лицевой счет?</div>");
	 $("#dialog3").dialog({
                 title:'Удаление ЛС',
                 position: 'center',
                 draggable:false,
                 modal:true,
	 resizable:false,
                 width:350,
                 buttons: {
                 "ДА": function() { 
                     
                      $.post('<?=site_url('services/deleteService');?>', {'id':id},
                            function(data){
                               $('#dialog3').dialog("close");
	               $('#dialog3').empty();
                               $.jnotify("Лицевой счет удален.",{remove: function (){
			window.location.reload();
		    }});
                                
                            })
                    },
                 "НЕТ": function() {
                $(this).dialog("close");
                $('#dialog3').empty();
                 }}
             
             });
	    }else{
		$.jnotify(data,'error');
	    }
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
                    <div id="page_header"><h2>Список услуг</h2></div>
<table  id="clients" class="table_wrapper_inner">
    <thead>
    <tr>
        <th>№</th>
        <th>Наименование услуги</th>
        <th>Тип услуги</th>
        <th></th>
        <th></th>
    </tr>
    </thead>
    <?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $n=1;

    foreach($allServices as $row) {                
        ?>
    <tr>
    	<td><?php echo $n++; ?></td>
    	<td><?php print $row->service_description ?></td>
    	<td><?php print $row->marker; ?></td>
	<td><?php  echo '<a href="#" onclick="editServiceName('.$row->id.');return false;"><img src="/assets/images/edit-row.png"></a>'; ?></td>
	<td><?php  echo '<a href="#" onclick="deleteService('.$row->id.');return false;"><img src="/assets/images/delete-row.png"></a>'; ?></td>
    </tr>
    <?php } ?>
</table>
    <p>
        <button id="addServiceButton">Создать услугу</button>
    </p>
</div>
<div id="addService" style="display: none;">
<?php echo form_open("services/add_service",'name=form id=form'); ?>
        <p>Наименование услуги:<br />
            <input type="text" name="service_description" id="service_name" />
        </p>

        <p>Тип услуги:<br />
            <input type="text" name="marker" id="marker" />
        </p>
<?php echo form_close(); ?>
</div>

<div id="editServiceForm" style="display: none;">
                    <?php echo form_open("services/editServiceByID", 'name=form, id=editform'); ?>
	    <input type="hidden" name="id" id="id">
                    <p>Наименование ЛС:<br />
                        <input type="text" name="service_description" id="service_description" />
                    </p>
                    <?php echo form_close(); ?>
                </div>
<div id='dialog3'></div>