<script type="text/javascript">
    $(document).ready(function() {
    $('#flex1').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers"
        
    });
    $("#createServiceGroup").button( {
                text: true,
                icons: {
                primary: "ui-icon-gear"
                }
            })
} );

function editServiceGroupName(id){
    $.post('<?= site_url('services/getServiceGroupByID'); ?>',{'id':id},
	function(data){
	$.each(data, function(i, val) {
	    $('form input#id').val(data[i].id);	
	    $('form input#services_groups').val(data[i].services_groups);
	})
	$('#editServiceGroupForm').dialog({
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
 
                    $.post('<?=site_url('services/editServiceGroupByID');?>', form_data,
                            function(data){
                               $('#editServiceGroupForm').dialog("close");
                               $.jnotify("Параметры группы изменены.", {remove: function (){
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
    </script>
<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">

    <div id="infoMessage" class="msg msg-ok"><?php //echo $message;?></div>


<div id="page_header"><h2>Группы номенклатур</h2></div>

<table  id="flex1" class="table_wrapper_inner">
            <thead>
		<tr>        
                                                <th>№</th>
			<th>Наименование группы номенклатуры</th>
                                                <th>Тип лицевого счета</th>
                                                <th>Наименование услуги</th>
			<th></th>
			<th></th>
                                                
		</tr>
            </thead>
            <tbody>
            		<?php
                                $n=1;
                                foreach ($groups as $service_groups):?>
			<tr>
				<td><? echo $n++;?></td>
                                                                <td><?php echo $service_groups->services_groups;?></td>
                                                                <td><?php echo $service_groups->marker;?></td>
                                                                <td><?php echo $service_groups->service_description;?></td>
				<td><?php  echo '<a href="services/get_assortment_group/'.$service_groups->id.'"><img src="/assets/images/list.png" alt="Состав группы" title="Состав группы"></a>'; ?></td>
				<td><?php  echo '<a href="#" onclick="editServiceGroupName('.$service_groups->id.');return false;"><img src="/assets/images/edit-row.png" alt="Редактирование группы" title="Редактирование группы"></a>'; ?></td>				
			                
			</tr>
		<?php endforeach;?>
            </tbody>
	</table>
<p>
    <a href="<?php echo site_url();?>services/createServiceGroup" id="createServiceGroup">Создать группу номенклатуры</a>
    </p>
                </div>
<div id="editServiceGroupForm" style="display: none;">
                    <?php echo form_open("services/editServiceByID", 'name=form, id=editform'); ?>
	    <input type="hidden" name="id" id="id">
                    <p>Наименование группы:<br />
                        <input type="text" name="services_groups" id="services_groups" />
                    </p>
                    <?php echo form_close(); ?>
                </div>