<script type="text/javascript">
$(document).ready(function(){
function displayVals() {
      var singleValues = $("#element_form").val();

       if(singleValues=='select'){
          $('#hidden_div2').show();
      }else{
          $('#hidden_div2').hide();
      }
}
$("#element_form").change(displayVals);

function displayPaymentType() {
      var single = $("#tariff").val();


       if(single=='0'){
	$('p select[name="paymentType"]').empty();
	$('p select[name="paymentType"]').append('<option value="none">РЕСУРС</option><option value="billing">БИЛЛИНГ</option>')
	displayVals();
      }else{
$('p select[name="paymentType"]').empty();
$('p select[name="paymentType"]').append('<option value="ЕДИНОВРЕМЕННО">ЕДИНОВРЕМЕННО</option><option value="ПЕРИОДИЧЕСКИ">ПЕРИОДИЧЕСКИ</option>');
$("#element_form").empty();
$("#element_form").append('<option value="none">Значение</option><option value="select">Список</option>');
displayVals();
      }

  var paymentType = $('p select[name="paymentType"]').val();

  $('p select[name="paymentType"]').change(function() {
 var paymentType = $('p select[name="paymentType"]').val();
 if(single=='0' && paymentType=='billing'){
      $("#element_form").empty();
      $("#element_form").append('<option value="none">Значение</option><option value="identifier">Идентификатор</option>');
	  displayVals();
  }
   if(single=='0' && paymentType=='none'){
      $("#element_form").empty();
      $("#element_form").append('<option value="select">Список</option>');
	  displayVals();
  }
   });

 if(single=='0' && paymentType=='none'){
      $("#element_form").empty();
      $("#element_form").append('<option value="select">Список</option>');
	  displayVals();
  }

}

$("#tariff").change(displayPaymentType);
displayPaymentType();
     });
</script>

<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">
 <div id="infoMessage" class="msg msg-error"><?php echo $message;?></div>
<div class='mainInfo'>

    <div id="page_header"><h2>Создание новой номенклатуры</h2></div>


    <?php echo form_open("auth/add_assortment",'id="form"');?>
    <fieldset>
<legend> Пожалуйста заполните информацию о номенклатуре.</legend>
      <p>Наименование номенклатуры:<br />

      <?php echo form_textarea($assortment_name);?>
      </p>

      <p>Тип лицевого счета:<br />
      <?php
        $service = new Auth();

            foreach($service->getServiceTypeList() as $typeService){
                $type[$typeService->id] = $typeService->service_description;
            }
            echo form_dropdown('serviceType', $type,'id="serviceType');
        ?>
      </p>
      <p>Наличие тарифа:<br />
     <?php
      $opt_tariff = array(
                  '0'  => 'НЕТ',
                  '1'    => 'ДА'
                );
     echo form_dropdown('tariff',$opt_tariff, '','id="tariff"');?>
      </p>
      <p>Периодичность оплаты:<br />
     <?php
     $options = array(
                  'ЕДИНОВРЕМЕННО'  => 'ЕДИНОВРЕМЕННО',
                  'ПЕРИОДИЧЕСКИ'    => 'ПЕРИОДИЧЕСКИ'
                );
     echo form_dropdown('paymentType', $options,'id="paymentType"'); ?>
      </p>

</fieldset>
      <fieldset>
<legend> Конструктор элемента формы</legend>
 <div style="display:none;">
<p>"Name" элемента формы:<br />
      <?php
      $data = array(
              'name'        => 'name_element',
              'value'       => uniqid(),
              );
      echo form_input($data);?>
</p>
 </div>
      <p>Элемент формы:<br />
       <?php
     $options = array(

                   'input'  => 'none',
                   'select'    => 'select',
                );
     echo form_dropdown('element_form', $options,'','id="element_form"'); ?>
      </p>
      <div id="hidden_div2" style="display:none;">
       <p>Тип ресурса:<br />
       <?php
     $res_data = array(

                   'phone'  => 'Телефонный номер',
                   'ip-address'    => 'IP-адрес',
                   'port'    => 'Порт',
                );
     echo form_dropdown('type_resources', $res_data,'','id="type_resources"'); ?>
      </p>
      </div>
      <p>
      <?php
      $datasource = array(
              'datasource'   => 'free_phone_pool'
            );
      echo form_hidden($datasource);?>
      </p>


      <?php echo form_hidden($default_element_value);?>

      </fieldset>

      <p><?php echo form_submit('submit', 'Добавить номенклатуру');?></p>


    <?php echo form_close();?>

</div>
                </div>
