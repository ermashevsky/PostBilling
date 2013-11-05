<script type="text/javascript">
$(document).ready(function() {
$('#submit_button').hide();

function checkSelect2()
{

	var n = $("#select2 > option").size();

	if(n!=0){
		$('#submit_button').show();
	}else{
		$('#submit_button').hide();
	}
}

 $('#add_arrow').click(function() {
checkSelect2()
	return !$('#assortment option:selected').remove().appendTo('#select2');
   });
$('#remove_arrow').click(function() {
checkSelect2()
	return !$('#select2 option:selected').remove().appendTo('#assortment');
   });
  });
  $('#form').submit(function() {

 })
function selectAllOptions(selStr)
{
  var selObj = document.getElementById(selStr);
  for (var i=0; i<selObj.options.length; i++) {
    selObj.options[i].selected = true;
  }
}
 $(document).ready(function(){
function displayPaymentType() {
      var service_id = $("p #serviceType").val();
  console.info(service_id);

	 $.post('<?=site_url('services/getAssortmentByServiceId');?>',{'id':service_id},
	        function(data){
	    $('p select[name="assortment"]').empty();
		$.each(data, function(i, val) {

		 $('p select[name="assortment"]').append('<option value='+data[i].id+'>'+data[i].payment_name+'</option>');
		});
	    },'json');

}

$("p #serviceType").change(displayPaymentType);
displayPaymentType();
 });

$(document).ready(function(){
$("#assortment").change(function () {
$('div#service_description').show('slow');
var id_assortment = $('#assortment option:selected').val();
//$('div#service_description').text(str)
$.post('<?=site_url('services/getAssortmentById');?>',{'id':id_assortment},
	        function(data){
		    console.info(data);
		    $.each(data, function(i, val) {
		    $('div#service_description').text(data[i].payment_name);
		    $('div#service_description').append('<br/><b>Периодичность:</b> '+data[i].payment_type);
		    if(data[i].tariff==1){
		     $('div#service_description').append('<br/><b>Тариф:</b> ДА');
		    }else{
		    $('div#service_description').append('<br/><b>Тариф:</b> НЕТ');
		    }
		    if(data[i].element_type=='select'){
		    $('div#service_description').append('<br/><b>Наличие ресурса:</b> ДА');
		    }else{
		     $('div#service_description').append('<br/><b>Наличие ресурса:</b> НЕТ');
		    }
		    })
		},'json')
	    });
});
 </script>

<!-- SELECT *
FROM assortment
INNER JOIN services ON services.marker = assortment.marker_service
WHERE services.id =3-->
<style type="text/css">
  #assortment {width: 400px;}
  #select2{width: 400px;}
  a #add #remove{
   display: block;
   width:20px;
   border: 1px solid #aaa;
   text-decoration: none;
   background-color: #fafafa;
   color: #123456;
   margin: 2px;
   clear:both;
  }
  #service_description{
      margin:5px;
      padding:5px;
      width:400px;
      min-height:100px;
      background: #AED0EA;
      vertical-align: middle;
      display: none;
      color:#5D7282;
  }
</style>
<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">

    <div id="infoMessage" class="msg msg-error"><?php echo $message;?></div>


<div id="page_header"><h2>Создание групп номеклатур</h2></div>

<?php
//@todo: данные приходят как объект assortmentlist
?>

<p>Пожалуйста укажите наименование группы и выберите номенклатуры группы.</p>
<form id="form" accept-charset="utf-8" method="post" name="form" action="createServiceGroup" onsubmit="selectAllOptions('select2');">
        <p>Наименование группы номенклатуры:<br />
            <?php
            echo form_textarea($group_name); ?>
        </p>
        <p>Тип лицевого счета:<br />
        <?php
        $service = new Services();
            //Список типов услуг ДН,ТК,ЮЛ и т.д.
            $service->getServiceTypeList();

            foreach($service->getServiceTypeList() as $typeService){
                $type[$typeService->id] = $typeService->service_description;
            }
            echo form_dropdown('serviceType', $type,'','id="serviceType"');
        ?>
        </p>
        <div id="service_description"></div>
        <div id="payment_type"></div>
        <p>Список номенклатуры:<br />
            <table style="width: 100px;">
                <tr>
                    <td>
            <?php

//            $service->getAssortmentList();
//
//            foreach($service->getAssortmentList() as $row):
//                $a[$row->id] = $row->payment_name;
//                endforeach;
//            echo form_dropdown('assortment', $a,array('size','30'),'id="assortment"');
          echo '<select name="assortment" size="10" id="assortment"></select>';
            //Тута получить еще значения надо

            ?>
              </td>
              <td>
                  <a href="#" id="add_arrow"><img src="/assets/images/add_arrow.png" border="0" /></a>
                  <a href="#" id="remove_arrow"><img src="/assets/images/remove_arrow.png" border="0" /></a>
                  </td>
              <td>

                  <select id="select2" size="10" name="assortment_selected[]" multiple="true"></select>
                  </td>
           </tr>
            </table>
        </p>
        <p><?php
		$attr = array('id'=>'submit_button', 'name'=>'submit');
		echo form_submit($attr,'Создать группу'); ?></p>


</form>

                </div>
