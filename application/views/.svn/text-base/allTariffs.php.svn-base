<style>
    #service_description{
      width:400px;
      min-height:50px;
      vertical-align: middle;
  }
  #service_type{
      float: left;

  }
  .service_description{
      float:right;
      width:350px;
      background: #AED0EA;
  }
</style>
<script type="text/javascript">
    $(document).ready(function() {

function displayPaymentType(){
      var service_id = $("select#service_type").val();
  console.info(service_id);

	 $.post('<?=site_url('services/getAssortmentByServiceId');?>',{'id':service_id},
	        function(data){

	    $('select[name="assortment"]').empty();

		$.each(data, function(i, val) {

		 $('select[name="assortment"]').append('<option alt="'+data[i].payment_name+'" title="'+data[i].payment_name+'" value='+data[i].id+'>'+data[i].payment_name+'</option>');
		});
	    },'json');

}
oTable =$('#assortments').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "bFilter":false,
        "bInfo": false,
        "bDestroy":true

    });
function displayAssortmentList(){
      var service_id = $("select.serviceType").val();
  console.info(service_id);
        oTable.fnClearTable();


	 $.post('<?=site_url('services/getAssortmentByServiceId');?>',{'id':service_id},
	        function(data){

	    //$('#assortments').empty();
		$.each(data, function(i, val) {
		 $('#assortments').dataTable().fnAddData( [
		    data[i].id,
		    data[i].id,
		    data[i].payment_name,
		    data[i].payment_type] );
		 //$('select[name="assortment"]').append('<option alt="'+data[i].payment_name+'" title="'+data[i].payment_name+'" value='+data[i].id+'>'+data[i].payment_name+'</option>');
		});

	    },'json');

}
$("select#service_type").change(displayPaymentType);
displayPaymentType();

$("select.serviceType").change(displayAssortmentList);
displayAssortmentList();


$("select#service_description").change(function () {
$('div.service_description').show('slow');
var str = $('#service_description option:selected').text();

$('div.service_description').text(str)
var id = $('#service_description option:selected').val();
$('input#id_assortment').val(id);
});

//$("#price").maskMoney({thousands:'', decimal:'.', symbolStay: false});


    sTable = $('#clients').dataTable({
		"bStateSave": true,
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",

        "bDestroy": true

    });

    $('#assortments tbody tr').live('dblclick', function () {
        var aData = oTable.fnGetData( this );
        var payment_name = aData[2];
        var id_payment = aData[1];

        $('form div#assortment_description').empty();
        $('form div#assortment_description').append(payment_name);
        $('form input#id_assortment').val(id_payment);
        $('#assortmentList').dialog('close');


    });
    $("#addTariffButton").button( {
                text: true,
                icons: {
                primary: "ui-icon-gear"
                }
            }).click(function(){
                $( "#addTariff" ).dialog({
                 title:'Создание нового тарифа',
                 position: 'center',
                 draggable:false,
                 modal: true,
                 width:500,
            buttons: { "Создать": function() {
                   var d = $('#price').val();
				   $('#price').val(d.replace(/,/g, "."));
                   $.post('<?=site_url('services/add_tariff');?>',$('#form').serialize(),
                   function(data){
                       $("#addTariff").dialog("close");
                       $.jnotify("Новый тарифный план создан.", {remove: function (){ window.location.reload(); }});

                   })
            }}

            });

            })
            $('#get_assortment').click(function(){
                $( "#assortmentList" ).dialog({
                 title:'Выберите номенклатуру',
                 position: 'center',
                 draggable:false,
                 modal: true,
                 width:650})
            })
} );



function filterBlockShow(){
$('#filter_block').show('fast');
}

function unsetFilter(){
    $('#filter_block').hide();
    window.location.reload();
}
function setTariffsFilter(){
    var id = $('input#id_assortment').val();

     $.post('<?=site_url('services/getFilterTariffs');?>',{'id_assortment':id},
                   function(data){

	    sTable.fnClearTable();

	    $.each(data, function(i, val) {
 $('#clients').dataTable().fnAddData( [
		    data[i].id,
		    data[i].tariff_name,
		    data[i].price,
		    data[i].marker_service,
		   '<a href="#" onclick="editTariff('+data[i].id+');return false;"><img src="/assets/images/edit-row.png"></a>'
	    ] );


      });
		   },'json')
}

function editTariff(id){

            $.post('<?=site_url('services/getTariffById');?>',{'id':id},
        function(data){
            console.info(data);
            $.each(data, function(i, val) {
	$('form input#id2').val(data[i].id);
	$('form input#tariff_name2').val(data[i].tariff_name);
                $('form input#price2').val(data[i].price);

            })
            $('#editTariffForm').dialog({
                title: "Редактирование тарифа",  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
                resizable: false,
                show:'slide',
                buttons: {
                "Сохранить изменения": function() {
                    var form_data = $('#editform').serialize();
                    console.info(form_data);

                    $.post('<?=site_url('services/editTariff');?>', form_data,
                            function(data){
                               $('#editTariffForm').dialog("close");
                               $.jnotify("Параметры тарифа изменены.", {remove: function (){
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
function delTariff(id)
{
	$.post('<?=site_url('services/delTariffById');?>',{'id':id},
        function(data){
			$.each(data, function(i, val) {
			if(data[i].num_rows==0){

				$('#deleteTariff').dialog({
                title: "Удаление тарифа",  //тайтл, заголовок окна
                position: 'center',  //месторасположение окна [отступ слева,отступ сверху]
                modal: true,           //булева переменная если она равно true -  то окно модальное, false -  то нет
                draggable:false,
                resizable: false,
                show:'slide',
                buttons: {
					"ДА": function() {

                    $.post('<?=site_url('services/deleteTariff');?>', {'id':id},
                            function(data){
                               $('#deleteTariff').dialog("close");
                               $.jnotify("Тариф удален.", {remove: function (){
							   window.location.reload();  }});
                                //Доработать обновление таблицы после добавления номенклатуры
                            })
                    },

                "НЕТ": function() {
                $(this).dialog("close");
                 }
				}
			})
			}else{
			$.jnotify("Тариф используется.Удаление невозможно.","error")
			}
			})
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
                    <div id="page_header"><h2>Список тарифов</h2></div>
		    <p id="filter_link" align="right" style="margin-bottom: 5px; margin-right: 10px;">
			<a href="#" id="filter_block_show" onclick="filterBlockShow(); return false;"><img src="/assets/images/filter.png" style="vertical-align: middle; margin-right: 5px;"/>Фильтр</a>
		    </p>
		    <div id="filter_block" style="display: none;">

			<?php
			if(count($serviceType)>0){
			?>

			<select id="service_type">
			    <?php
			    foreach($serviceType as $row):
				echo '<option value="'.$row->id.'">'.$row->service_description.'</option>';
			    endforeach;
			    ?>
			</select>
			<?php
			}
			?>

			<select name="assortment" id="service_description" size="10">

			</select>

			<div class="service_description">

			</div>

			<input type="hidden" name="id_assortment" id="id_assortment" value=""/>
			<div style="position:relative bottom">
			<a href="#" onclick="setTariffsFilter();return false;">Применить</a>
			<a href="#" onclick="unsetFilter();return false;">Отменить</a>
			</div>
		    </div>
<table  id="clients" class="table_wrapper_inner">
    <thead>
    <tr>
        <th>№</th>
        <th>Наименование тарифа</th>
        <th>Цена</th>
        <th>ЛС</th>
        <th></th>
		<th></th>
    </tr>
    </thead>
    <?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $n=1;
    if(count($allTariffs)>0){
    foreach($allTariffs as $row) {
        ?>
    <tr>
    	<td><?php echo $n++; ?></td>
    	<td><?php print $row->tariff_name ?></td>
    	<td><?php print $row->price; ?></td>
	<td><?php print $row->marker_service; ?></td>
	<td><?php  echo '<a href="#" onclick="editTariff('.$row->id.');return false;"><img src="/assets/images/edit-row.png"></a>'; ?></td>
	<td><?php  echo '<a href="#" onclick="delTariff('.$row->id.');return false;"><img src="/assets/images/delete-row.png"></a>'; ?></td>
    </tr>
    <?php }} ?>
</table>
    <p>
        <button id="addTariffButton">Создать тариф</button>
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
<div id="addTariff" style="display: none;">
<?php echo form_open("services/add_tariff",'name=form id=form'); ?>
     <p>Лицевой счет:<br />

	    <select class="serviceType">
		<?php
		$servType = new Services();
		$servType->getServiceTypeList();
		foreach($servType->getServiceTypeList() as $row):
		    echo '<option value="'.$row->id.'">'.$row->service_description.'</option>';
		endforeach;
		?>
	    </select>

        </p>
<p>Номенклатура: <?php echo form_button('get_assortment','...','id="get_assortment"');?><br />
            <input type="hidden" name="id_assortment" id="id_assortment" value=""/>
            <div id="assortment_description" style="background: #AED0EA;"></div>
        </p>
    <p>Наименование тарифа:<br />
	    <?php
	    $data = array(
		'name' => 'tariff_name',
		'id' => 'tariff_name',
		'rows' => '3',
		'cols' => '3',
	    );
	    echo form_textarea($data); ?>
<!--            <textarea type="text" name="tariff_name" size="10" id="tariff_name" ></textarea>-->
        </p>

        <p>Цена:<br />
            <input type="text" name="price" id="price" />
        </p>

<?php echo form_close(); ?>
</div>
		<div id="deleteTariff" style="display: none;">
			Вы действительно хотите удалить тариф ?
		</div>
<div id="editTariffForm" style="display: none;">
                    <?php echo form_open("services/editTariff", 'name=form, id=editform'); ?>
	    <input type="hidden" name="id" id="id2">
                    <p>Наименование тарифа:<br />
                        <input type="text" name="tariff_name" id="tariff_name2" />
                    </p>
	    <p>Цена:<br />
                        <input type="text" name="price" id="price2" />
                    </p>
                    <?php echo form_close(); ?>
                </div>
