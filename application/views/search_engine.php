
<script type="text/javascript">

	function handle_select(myForm) {
		var option_text = $('#criteria').val();
		if (option_text === 'phone_number') {
			$("#LabelID").empty();
			$("#LabelID").append("Введите номер телефона:");
			$("#FormItem").empty();
			$("#FormItem").append('<input type="text" name="phone_number" id="phone_number_input">');
		}
		if (option_text === 'account') {
			$("#LabelID").empty();
			$("#LabelID").append("Введите номер лицевого счета:");
			$("#FormItem").empty();
			$("#FormItem").append('<input type="text" name="account" id="account_input">');
		}

		if (option_text === 'assortment_item') {
			$("#LabelID").empty();
			$("#LabelID").append("Введите наименование номеклатуры:");
			$("#FormItem").empty();
			$("#FormItem").append('<input type="text" name="assortment" id="assortment_input">');
		}

		if (option_text === 'date_range') {
			$("#LabelID").empty();
			$("#LabelID").append("Введите диапазон дат:");
			$("#FormItem").empty();
			$("#FormItem").append('Начало: <input type="text" name="date_timepicker_start" id="date_timepicker_start">  Конец: <input type="text" name="date_timepicker_end" id="date_timepicker_end">');
			$('#date_timepicker_start').datetimepicker({
				format: 'Y/m/d',
				onShow: function(ct) {
					this.setOptions({
						maxDate: $('#date_timepicker_end').val() ? $('#date_timepicker_end').val() : false
					})
				},
				timepicker: false
			});
			$('#date_timepicker_end').datetimepicker({
				format: 'Y/m/d',
				onShow: function(ct) {
					this.setOptions({
						minDate: $('#date_timepicker_start').val() ? $('#date_timepicker_start').val() : false
					})
				},
				timepicker: false
			});
		}
	}

	function search_bar() {
		var kw = $('#'+$('form#form input').attr('id')+'').val();

		var select = $('#criteria').val();
		
		if (select === 'account') {
			var url = '/clients/searchByAccount';
		}
		
		if(select === 'phone_number'){
			var url = '/clients/searchByPhone';
		}
		
		if (kw != "") {
			$.post(url, {'search': kw},
			function(data) {
				if (data != "") {
					console.info(data);
					$("div.search_result_window").empty();
					$("div.search_result_window").append("<div class='headSearchResult'>Найдены совпадения:</div>");
					$.each(data, function(i, val) {
						$("div.search_result_window").append("<div class='cont'><li><a href='<?= site_url('clients/accounts'); ?>/" + this.id_clients + "'>" + this.bindings_name + " - " + this.accounts + "</a></li></div></fieldset>");
					});
				} else {

					$("div.search_result_window").empty();
					$("div.search_result_window").append("<div class='headSearchResult'>Ничего не найдено.</div><br/>");
				}
			}, 'json');
			$('div.search_result_window').dialog({
				draggable: false,
				modal:true,
				position: 'center',
				width: 500,
				height: '350',
				buttons: {
					"Закрыть": function() {
						$(this).dialog("close");
					}
				}
			});
		}
	}
</script>


<div id="menus_wrapper">
	<div id="breadcrumb">
		<?php echo set_breadcrumb(); ?>
	</div>
</div>
<!-- End Small Nav -->

<div class="section_content">
	<div id="infoMessage" class="msg msg-error"><?php //echo $message;  ?></div>
	<div id="page_header"><h2>Поиск по критериям</h2></div>

	<div id="content_finder" class="errormsgbox"></div>

	<?php echo form_open("clients/search_engine", 'name=form, id=form'); ?>
	<p>Критерий поиска:<br />
		<?php
		$options = array(
			'phone_number' => 'Телефонный номер',
			'account' => 'Лицевой счет',
			'assortment_item' => 'Номенклатура',
			'date_range' => 'Диапазон дат',
			'date' => 'Дата',
		);
		$js = 'id="criteria" onchange="handle_select(this.form)"';
		echo form_dropdown('criteria', $options, '', $js);
		?>
	</p>
	<p id="LabelID">Введите номер телефона:</p>
	<p id="FormItem"><input type="text" name="phone_number" id="phone_number_input" /></p>
	<p><?php echo form_button('button', 'Начать поиск', 'id="submit_button" onclick="search_bar();return false;"'); ?></p>
	<?php echo form_close(); ?>

	<div class="search_result_window" style="display: display;" title="Результаты поиска">
	</div>

</div>