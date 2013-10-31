
<script type="text/javascript">
    $(document).ready(function() {
    jTable = $('#clients').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers"
        
    });
    $("#addAssortmentButton").button( {
                text: true,
                icons: {
                primary: "ui-icon-gear"
                }
            })
} );

function editAssortmentByID(id){
            
            $.post('<?=site_url('services/getAssortmentById');?>',{'id':id},
        function(data){
            console.info(data);
            $.each(data, function(i, val) {
	$('form input#id').val(data[i].id);	
	$('form textarea#payment_name').val(data[i].payment_name);
                
               
            })
            $('#editAssortment').dialog({
                title: "Редактирование номеклатуры",  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
	width:'400',
                resizable: false,
                show:'slide',
                buttons: {
                "Сохранить изменения": function() { 
                    var form_data = $('#editform').serialize();
                    console.info(form_data);
 
                    $.post('<?=site_url('services/editAssortment');?>', form_data,
                            function(data){
                               $('#editAssortment').dialog("close");
                               $.jnotify("Параметры номеклатуры изменены.", {remove: function (){
                    window.location.reload();  }});
                                //Доработать обновление таблицы после добавления номенклатуры    
                            })
                    },    
                
                "Закрыть": function() {
                $(this).dialog("close");
                 }}
                
            });
        },'json')
        
        };

function deleteAssortmentByID(id){
      $.post('<?=site_url('services/getAssortmentGroupInfo');?>',{'id':id},
        function(data){
	   if(data>0)
	   {
	       $.jnotify("Удаление номеклатуры невозможно, т.к. она содержится в группе.", 'error');
	   }
	   if(data==0){
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
                     
                      $.post('<?=site_url('services/deleteAssortment');?>', {'id':id},
                            function(data){
                               $('#dialog3').dialog("close");
	               $('#dialog3').empty();
                               $.jnotify("Номеклатура удалена.",{remove: function (){
			jTable.fnClearTable( 0 );
			jTable.fnDraw();}});
                                
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
    </script>
    <div id="dialog3"></div>
   <div id="editAssortment" style="display: none;">
                    <?php echo form_open("services/editTariff", 'name=form, id=editform'); ?>
	    <input type="hidden" name="id" id="id">
                    <p>Наименование тарифа:<br />
                        <textarea name="payment_name" id="payment_name" cols="40" rows="5"></textarea>
                    </p>
	    
                    <?php echo form_close(); ?>
                </div>
<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">
                     <div id="infoMessage" class="msg msg-error"><?php //echo $message;?></div>
                    <div id="page_header"><h2>Список номенклатуры</h2></div>
<table  id="clients" class="table_wrapper_inner">
    <thead>
    <tr>
        <th>№</th>
        <th>Наименование номенклатуры</th>
        <th>Тип услуги</th>
        <th>Периодичность платежа</th>
        <th></th>
        <th></th>	
    </tr>
    </thead>
    <?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $n=1;
    foreach($assortmentlist as $assortments) {                
        ?>
    <tr>
    	<td><?php echo $n++; ?></td>
    	<td><?php print $assortments->payment_name ?></td>
    	<td><?php print $assortments->marker_service; ?></td>
                <td><?php print $assortments->payment_type; ?></td>
	<td><?php  echo '<a href="#" onclick="editAssortmentByID('.$assortments->id.');return false;"><img src="/assets/images/edit-row.png"></a>'; ?></td>
	<td><?php  echo '<a href="#" onclick="deleteAssortmentByID('.$assortments->id.');return false;"><img src="/assets/images/delete-row.png"></a>'; ?></td>
    </tr>
    <?php } ?>
</table>
<p>
    <a href="<?php echo site_url();?>auth/add_assortment" id="addAssortmentButton">Создать номенклатуру</a>
    </p>
   
</div>
    