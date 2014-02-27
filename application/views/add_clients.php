<script type="text/javascript">
    $().ready(function() {
	$('#add_arrow').click(function() {
	    return !$('#services option:selected').remove().appendTo('#select2');
	});
	$('#remove_arrow').click(function() {
	    return !$('#select2 option:selected').remove().appendTo('#services');
	});
    });
    $('#form').submit(function() {
	$('#select2 option').each(function(i) {
	    $(this).attr("selected", "selected");
	});
    })
</script>
<script type="text/javascript">
    $(function() {
	$.datepicker.setDefaults($.datepicker.regional['']);
	$("p #date_account").datepicker($.datepicker.regional["ru"]);
    });
</script>
<script type="text/javascript">
function check() {
	if ($('#inn').val() == '')
		$("div#content_finder").empty()
}
$(document).ready(function(){
$('#submit_button').prop('disabled', true);
	$("#inn").keyup(function(){
		check();
		if($("#inn").val()!=''){
		$.post("<?=site_url('clients/searchClientName');?>", { search: $("#inn").val() }, function(data){
			$("div#content_finder").empty()
			if(data==''){
				$('#submit_button').prop('disabled', false);
			}else{
				$("div#content_finder").append("<div class='headSearchResult'>Найдены совпадения ИНН:</div>");
				$('#submit_button').prop('disabled', true);
			$.each(data, function(){
				$("div#content_finder").append("<div class='cont'><li><a href='<?=site_url('clients/accounts');?>/" + this.id +"'>" + this.client_name + "</a></li></div></fieldset>");
			});
			}
		}, "json");
		}
	});

	$("#inn").change(function(){
		check();
		if($("#inn").val()!=''){
		$.post("<?=site_url('clients/searchClientName');?>", { search: $("#inn").val() }, function(data){
			$("div#content_finder").empty()
			if(data==''){
				$('#submit_button').prop('disabled', false);
			}else{
				$("div#content_finder").append("<div class='headSearchResult'>Найдены совпадения ИНН:</div>");
				$('#submit_button').prop('disabled', true);
			$.each(data, function(){
				$("div#content_finder").append("<div class='cont'><li><a href='<?=site_url('clients/accounts');?>/" + this.id +"'>" + this.client_name + "</a></li></div></fieldset>");
			});
			}
		}, "json");
		}
	});
});
</script>
<style type="text/css">
    select {
	position: relative;
	display: block;
	float: left;
	width: 200px;
	height: 150px;
    }

	div#content_finder
	{
		display: block;
		width:300px;
		max-height: 500px;
		position:absolute;
		z-index: 1000;
		top:240px;
		left:420px;
		overflow:auto;
	}
	.cont{
		padding-left:7px;
	}

	div.headSearchResult{
		margin:7px;
		font-weight: bold;
	}

	.successbox {
		color: #4F8A10;
		background-color:#EDFCED;
	}

	.errormsgbox {
		color: #D8000C;
		background-color:#FDD5CE;
	}

</style>

<div id="menus_wrapper">
    <div id="breadcrumb">
	<?php echo set_breadcrumb(); ?>
    </div>
</div>
<!-- End Small Nav -->

<div class="section_content">
    <div id="infoMessage" class="msg msg-error"><?php echo $message; ?></div>
    <div class='mainInfo'>

        <div id="page_header"><h2>Добавление нового клиента</h2></div>
        <p>Пожалуйста заполните информацию о клиенте.</p>

		<div id="content_finder" class="errormsgbox"></div>

		<?php echo form_open("clients/add_client", 'name=form, id=form'); ?>
        <p>Наименование клиента:<br />
	    <?php echo form_input($client_name,'','id="client_name"'); ?>
        </p>
        <p>Адрес клиента:<br />
	    <?php echo form_input($client_address,'','id="client_address"'); ?>
        </p>
		<p>Почтовый адрес клиента:<br />
	    <?php echo form_input($post_client_address,'','id="post_client_address"'); ?>
        </p>
        <p>Номер договора:<br />
	    <?php echo form_input($account,'','id="account"'); ?>
        </p>
        <p>Дата договора:<br />
	    <?php echo form_input('date_account', '', 'id="date_account"'); ?>
        </p>
        <p>ИНН:<br />
	    <?php echo form_input($inn,'','id="inn"'); ?>
        </p>
		<p>КПП:<br />
	    <?php echo form_input($kpp,'', 'id="kpp"'); ?>
        </p>
        <p>Контакное лицо:<br />
	    <?php echo form_input($client_manager,'', 'id="client_manager"'); ?>
        </p>

        <p>Телефон:<br />
	    <?php echo form_input($phone_number,'', 'id="phone_number"'); ?>
        </p>

        <p>Email:<br />
	    <?php echo form_input($client_email,'', 'id="client_email"'); ?>
        </p>
        <p>Тип услуги:<br />
	<table style="width: 100px;">
	    <tr>
		<td>
		    <?php
		    $service = new Clients();

		    foreach ($service->getServiceList() as $row):
			$a[$row->id] = $row->service_description;
		    endforeach;
		    echo form_dropdown('services', $a, array('size', '30'), 'id="services"');

		    //Тута получить еще значения надо
		    ?>
		</td>
		<td>
		    <a href="#" id="add_arrow"><img src="/assets/images/add_arrow.png" border="0" /></a>
		    <a href="#" id="remove_arrow"><img src="/assets/images/remove_arrow.png" border="0" /></a>
		</td>
		<td>
		    <select multiple id="select2" size="6" name="assortment_selected[]"></select>
		</td>
	    </tr>
	</table>
        </p>
        <p><?php echo form_submit('submit', 'Добавить клиента','id="submit_button"'); ?></p>


<?php echo form_close(); ?>

    </div>
</div>
