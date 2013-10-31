<script type="text/javascript">
    $(document).ready(function() {
    $('#flex1').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers"

    });
    $("#addAssortmentButton").button( {
                text: true,
                icons: {
                primary: "ui-icon-circle-plus"
                }
            })
oTable =$('#assortments').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "bFilter":false,
        "bInfo": false,
        "bDestroy":true

    });
});
$('#assortments tbody tr').live('dblclick', function () {
        var aData = oTable.fnGetData( this );
        var payment_name = aData[2];
        var id_payment = aData[1];
        var id_group = aData[0];

         //$('#assortment_id').val(id_payment);
	 $('#dialog').append('Вы действительно хотите добавить номеклатуру:');
	 $('#dialog').append('<div><b>"'+payment_name+'"</b> в группу?</div>');
        $('#assortmentList').dialog('close');
        //window.location.reload();
	$("#dialog").dialog({
                 title:'Добавление номеклатуры в группу',
                 position: 'center',
                 draggable:false,
                 modal:true,
	 resizable:false,
                 width:350,
                 buttons: {
                 "ДА": function() {

                      $.post('<?=site_url('services/addAssortmentItemGroup');?>', {'id':id_payment,'id_group':id_group},
                            function(data){
                               $('#dialog').dialog("close");
	               $('#dialog').empty();
                               $.jnotify("Номенклатура добавлена группу.",{remove: function (){
			window.location.reload();
		    }});

                            })
                    },
                 "НЕТ": function() {
                $(this).dialog("close");
                $('#dialog').empty();
	window.location.reload();
                 }}})

    });
function getCustomerServiceInfo(id_group){
    $.post('<?= site_url('services/getCustomerServiceInfo'); ?>',{'id_group':id_group},
	function(data){
	    if(data>0){
		$.jnotify("Группа используется. Изменения недопустимы",'error');
	    }else{
		$.each(data, function(i, val) {

		    $('#assortments').dataTable().fnAddData( [
		    id_group,
		    data[i].id,
		    data[i].payment_name,
		    data[i].payment_type] );

		})
	$( "#assortmentList" ).dialog({
                 title:'Выберите номенклатуру',
                 position: 'center',
                 draggable:false,
                 modal: true,
                 width:650,
	 buttons:{
	   "Закрыть": function() {
                $(this).dialog("close");
	window.location.reload();
                 }
	}
	})

	    }
	},'json')
}
function getAssortmentItemGroup(id,id_group){
    $.post('<?= site_url('services/getAssortmentItemGroup'); ?>',{'id':id},
	function(data){
	    if(data==1){
		$('#dialog3').append("<div align='center' style='vertical-align:middle;'>Вы действительно хотите удалить номеклатуру из группы?</div>");
	 $("#dialog3").dialog({
                 title:'Удаление номеклатуры из группы',
                 position: 'center',
                 draggable:false,
                 modal:true,
	 resizable:false,
                 width:350,
                 buttons: {
                 "ДА": function() {

                      $.post('<?=site_url('services/deleteAssortmentItemGroup');?>', {'id':id,'id_group':id_group},
                            function(data){
                               $('#dialog3').dialog("close");
	               $('#dialog3').empty();
                               $.jnotify("Номенклатура удалена из группы.",{remove: function (){
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

    <div id="infoMessage" class="msg msg-ok"><?php //echo $message;?></div>


<div id="page_header"><h2>Список номенклатуры в группе</h2></div>

<table  id="flex1" class="table_wrapper_inner">
            <thead>
		<tr>
                                                <th>№</th>
			<th>Наименование номенклатуры</th>
                                                <th>Периодичность оплаты</th>
                                                <th>Действие</th>

		</tr>
            </thead>
            <tbody>
            		<?php
                                $n=1;
                                foreach ($assortment_group as $list):?>
			<tr>
				<td><? echo $n++;?></td>
                                                                <td><?php echo $list->payment_name;?></td>
                                                                <td><?php echo $list->payment_type;?></td>
                                                                 <td><?php  echo '<a href="#" onclick="getAssortmentItemGroup('.$list->id.','.$list->id_group.');return false;"><img src="/assets/images/delete-row.png"></a>'; ?></td>
			</tr>
		<?php endforeach;?>
            </tbody>
	</table>
<p>
    <button id="addAssortmentButton" onclick ="getCustomerServiceInfo(<? echo $list->id_group; ?>); return false;">Добавить номеклатуру</button>
</p>
                </div>
    <div id="assortmentList" style="display:none;">
	<table  id="assortments" class="table_wrapper_inner">
	    <thead>
		<tr>
		    <th>№</th>
		    <th>ID</th>
		    <th>Наименование номенклатуры</th>
		    <th>Период</th>
		</tr>
	    </thead>

	</table>

    </div>
<div id="dialog" style="display: none;">
    <input type="hidden" id="assortment_id" value="" />
</div>
    <div id='dialog3'></div>