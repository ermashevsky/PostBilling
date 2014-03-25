<!doctype html>
<html lang="en">

	<head>
		<meta charset="utf-8"/>
		<title>Dashboard I Admin Panel</title>

		<link rel="stylesheet" href="/assets/admin/css/layout.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="/assets/admin/css/jquery.dataTables.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="/assets/admin/css/jquery-ui-1.8.17.custom.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="/assets/styles/TableTools.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/assets/styles/jquery.multiselect.css" type="text/css" media="all" />

		<style>
			table#flex1{
				font-size:13px;
				margin-bottom:2px;

			}
			#flex1_info, #flex1_paginate{
				font-size:13px;
				padding:10px;
			}

			table#flex2{
				font-size:13px;
				margin-bottom:2px;

			}
			#flex2_info, #flex2_paginate{
				font-size:13px;
				padding:12px;
			}

			table#reportDataTable{
				font-size:13px;
				margin-bottom:2px;

			}
			#reportDataTable_info, #reportDataTable_paginate{
				font-size:13px;
				padding:12px;
			}
			
			#reportDataTable {
			   float: right;
			   position: relative;
			   width: 300px;
			}
		</style>
		<!--[if lt IE 9]>
		<link rel="stylesheet" href="/assets/admin/css/ie.css" type="text/css" media="screen" />
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<script src="/assets/admin/js/jquery-1.5.2.min.js" type="text/javascript"></script>
		<script src="/assets/admin/js/hideshow.js" type="text/javascript"></script>
		<script src="/assets/admin/js/jquery.dataTables.js" type="text/javascript"></script>
		<script type="text/javascript" src="/assets/admin/js/ui.datepicker-ru.js"></script>
		<script type="text/javascript" src="/assets/js/jquery.blockUI.js"></script>
		<script type="text/javascript" src="/assets/admin/js/jquery.equalHeight.js"></script>
		<script type="text/javascript" src="/assets/admin/js/jquery-ui-1.8.17.custom.min.js"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript" src="/assets/admin/js/ZeroClipboard.js"></script>
		<script type="text/javascript" src="/assets/admin/js/TableTools.js"></script>
		<script type="text/javascript" src="/assets/admin/js/ui.datepicker-ru.js"></script>
		<script type="text/javascript" src="/assets/js/jquery.multiselect.js"></script>
		<script type="text/javascript">
			$(document).ready(function()
			{
				$("#services").multiselect({
					noneSelectedText:'Выберите пункт',
					checkAllText:'Всё',
					uncheckAllText:'Ничего',
					selectedText: "# из # выбрано"
					
				});
				$("#month").multiselect({
					multiple:false,
					width:'200',
					selectedText: "# выбрано"
				});
				
				$("#month1").multiselect({
					multiple:false,
					selectedText: "# выбрано"
				});
				
				$("#month2").multiselect({
					multiple:false,
					selectedText: "# выбрано"
				});
				$('.ui-multiselect').css('width', '150px');
				$('#flex1').dataTable({
					"bJQueryUI": false,
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sUrl": "/assets/admin/js/russian-language-DataTables.txt"
					},
					"bAutoWidth": true
				});

			$('input#datepicker').datepicker($.datepicker.regional["ru"]);
			$('input#datepicker_postbilling').datepicker($.datepicker.regional["ru"]);
			});

			function selectRegion(){
				var marker = $('select[name="serviceList"]').val();

				if(!marker){
					$('div[name="selectAssortmentList"]').html('');
					$('div[name="selectTariffs"]').html('');
				}else{
					$.ajax({
						type: "POST",
						url: "/admin/linkedSelects",
						data: { action: 'showAssortmentList', marker: marker },
						cache: false,
						success: function(responce){
							$('div[name="selectAssortmentList"]').html(responce);
						}
					});
				};
			};

			function selectCity(){
				var id_assortment = $('select[name="assortmentList"]').val();
				$.ajax({
					type: "POST",
					url: "/admin/linkedSelects",
					data: { action: 'showTariffList', id_assortment: id_assortment },
					cache: false,
					success: function(responce){
						$('div[name="selectTariffs"]').html(responce);
						$('div[name="selectNewTariffs"]').html(responce);
						$('div.submit_link').show();
					}
				});
			};

			function searchTariff(){
				var id_tariff = $('select[name="tariffList"]').val();
				var data = [];
				$.post('<?php echo site_url('/admin/linkedSelects'); ?>',{action: 'searchTariff', id_tariff: id_tariff },
				function(data){
					var count = 0;
					$.each(data, function(i, val) {
						//$('div[name="selectTariffs"]').html(responce);
						console.info(data);
						count++;
					})

					$('#dialog-confirm').empty();
					$('#dialog-confirm').append('Найдено '+ count +' использований тарифа. Продолжить смену тарифа?');
					$("#dialog-confirm").dialog({
						resizable: false,
						height:150,
						modal: true,
						buttons: {
							"Сменить тариф": function() {
								var new_date = $('#date_change_tariff').val();
								var new_tariff = $('div[name="selectNewTariffs"] select[name="tariffList"]').val();
								var count = 0;
								$.each(data, function(i,val) {
									count++;
									$.post('<?php echo site_url('/admin/setEndDateForCustomerAssortments'); ?>',{'id': data[i].id, 'new_date':new_date,
										'new_tariff':new_tariff, 'uniq_id':data[i].uniq_id, 'id_account':data[i].id_account, 'payment_name':data[i].payment_name,
										'resources':data[i].resources, 'identifier':data[i].identifier,'period':data[i].period
									},
									function(data){
										//console.info(data);
									});
								});
								//alert(count);

								$( this ).dialog( "close" );
								$('#dialog-confirm-ok').append('Произведена смена тарифа у '+ count +' номеклатур.');

								$("#dialog-confirm-ok").dialog({
									resizable: false,
									height:150,
									modal: true,
									buttons: {
										"OK": function() {
											$( this ).dialog( "close" );
										}}});
							},
							'Отмена':function() {
								$( this ).dialog( "close" );
							}
						}
					});
				},'json');
			}

			function fnFormatDetails( oTable, nTr )
			{
				var oData = oTable.fnGetData( nTr );
				var sOut =
					'<div class="innerDetails">'+
					'<table cellpadding="5" cellspacing="0" border="1" style="padding-left:0px; border-color:#79C20D;">'+
					'<tr><td>Message:</td><td>'+oData.message+'</td></tr>'+
					'<tr><td>Thread:</td><td>'+oData.thread+'</td></tr>'+
					'<tr><td>File:</td><td>'+oData.file+'</td></tr>'+
					'<tr><td>Line:</td><td>'+oData.line+'</td></tr>'+
					'</table>'+
					'</div>';
				return sOut;
			}
			$(document).ready(function() {
				$.datepicker.setDefaults($.datepicker.regional['']);
				$('fieldset input#date_change_tariff').datepicker($.datepicker.regional["ru"]);
				$('#flex2 td.control').live( 'click', function () {
					var nTr = this.parentNode;
					var i = $.inArray( nTr, anOpen );

					if ( i === -1 ) {
						$('img', this).attr( 'src', "/assets/admin/images/details_close.png" );
						oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
						anOpen.push( nTr );
					}
					else {
						$('img', this).attr( 'src', "/assets/admin/images/details_open.png" );
						oTable.fnClose( nTr );
						anOpen.splice( i, 1 );
					}
				} );

				var anOpen = [];
				var oTable = $('#flex2').dataTable(
				{
					"aaSorting": [[1, 'asc']],
					"bJQueryUI": false,
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sUrl": "/assets/admin/js/russian-language-DataTables.txt"
					},
					"bAutoWidth": true,
					"bServerSide"  : true,
					"sServerMethod": "POST",
					"bLengthChange": true,
					"bProcessing": true,
					"bDestroy": true,
					"sAjaxSource"  : "<?php echo site_url(); ?>admin/getDatatableLogs",
					"aoColumns": [

						{
							"mDataProp": null,
							"sClass": "control center",
							"sDefaultContent": '<img src="/assets/admin/images/details_open.png'+'">'
						},
						{ "mDataProp": "id"},
						{ "mDataProp": "timestamp"},
						{ "mDataProp": "logger"},
						{ "mDataProp": "level"},
						{ "mDataProp": "method"},
						{ "mDataProp": "user"},
						{ "mDataProp": "message","bVisible":false},
					],
					'fnServerData': function(sSource, aoData, fnCallback)
					{
						$.ajax
						({
							'dataType': 'json',
							'type'    : 'POST',
							'url'     : sSource,
							'data'    : aoData,
							'success' : fnCallback
						});
					}
				});
			});

			$(document).ready(function() {

				//When page loads...
				$(".tab_content").hide(); //Hide all content
				$("ul.tabs li:first").addClass("active").show(); //Activate first tab
				$(".tab_content:first").show(); //Show first tab content

				//On Click Event
				$("ul.tabs li").click(function() {

					$("ul.tabs li").removeClass("active"); //Remove any "active" class
					$(this).addClass("active"); //Add "active" class to selected tab
					$(".tab_content").hide(); //Hide all tab content

					var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
					$(activeTab).fadeIn(); //Fade in the active ID content
					return false;
				});

			});

			function deleteUser(id,username){
				$("#dialog").dialog({
					width:'auto',
					height:'auto',
					modal: true,
					title: 'Удаление пользователя',
					resizable: false,
					draggable: false,
					position: "center",
					buttons: {
						"Да": function() {
							$.post('<?php echo site_url('admin/deleteUser'); ?>',{'id':id,'username':username},
							function(data){
								//console.info(data);
								//window.location.reload(true);
								location.replace("admin")
							},'json');
						},
						"Нет": function() {
							$(this).dialog("close");
						}
					}
				});
			}

			function buildReport(){
				$('#report1C').empty();
				//id_service = $('#services').val();
				var array_of_checked_values = $("#services").multiselect("getChecked").map(function(){
					return this.value;    
				}).get();
				console.info(array_of_checked_values);
				month = $('#month').val();
				checkbox_flag = $('#chbox').is(':checked');
				console.info(checkbox_flag);
				$.post('<?php echo site_url('/admin/buildReport'); ?>',{month: month, id_service: array_of_checked_values, checkbox:checkbox_flag },
				function(data){
					console.info(data);
					if(checkbox_flag===true){
						console.info('TRUE');
						$('#report1C').append('<table  id="reportDataTable" class="table_wrapper_inner"><thead><th>Клиент</th><th>Лицевой счет</th><th>Номенклатура</th><th>Количество</th><th>Цена</th><th>Сумма</th></thead><tbody></tbody></table>');
					$.each(data, function(i, val) {
						$('#reportDataTable').append('<tr><td>'+data[i].bindings_name+'</td><td>'+data[i].accounts+'</td><td>'+data[i].payment_name+'</td><td>'+data[i].counter+'</td><td>'+data[i].price+'</td><td>'+data[i].summ+'</td></tr>');
					});
					}else{
						console.info('FALSE');
						$('#report1C').append('<table  id="reportDataTable" class="table_wrapper_inner"><thead><th>Лицевой счет</th><th>Номенклатура</th><th>Количество</th><th>Цена</th><th>Сумма</th></thead><tbody></tbody></table>');
					$.each(data, function(i, val) {
						$('#reportDataTable').append('<tr><td>'+data[i].accounts+'</td><td>'+data[i].payment_name+'</td><td>'+data[i].counter+'</td><td>'+data[i].price+'</td><td>'+data[i].summ+'</td></tr>');
					});
					}
					
					oTable = $('#reportDataTable').dataTable({
						"aaSorting": [[0, 'asc']],
						"bJQueryUI": false,
						"bProcessing":true,
						"sPaginationType": "full_numbers",
						"oLanguage": {
							"sUrl": "/assets/admin/js/russian-language-DataTables.txt"
						},
						"bAutoWidth": true,
						"bDestroy": true,
						"sScrollY": "320px",
						"sDom": 'T<"clear">lfrtip',
						"oTableTools": {
							"aButtons": [
								{
									"sExtends": "csv",
									"sButtonText": "Сохранить в CSV"
								}
							],
							"sSwfPath": "/assets/admin/swf/copy_csv_xls_pdf.swf"
						}
					});
					$('#report1C').show();
				},'json');

			}
			
			function buildMergeReport(){
				$('#report1C').empty();
				//id_service = $('#services').val();
				var array_of_checked_values = $("#services").multiselect("getChecked").map(function(){
					return this.value;    
				}).get();
				console.info(array_of_checked_values);
				month1 = $('#month1').val();
				month2 = $('#month2').val();
				month1_text = $('#month1 option:selected').text();
				month2_text = $('#month2 option:selected').text();
				checkbox_flag = $('#chbox').is(':checked');
				$.post('<?php echo site_url('/admin/buildMergeReport'); ?>',{month1: month1, month2: month2, id_service: array_of_checked_values},
				function(data){
					console.info(month1_text);
				$('#report1C').append("<div style='text-align:center;'><h2>Сравнительный отчет за "+month1_text+" (1) и "+month2_text+" (2) 2014 года.</h2></div>");	
				$('#report1C').append('<table  id="reportDataTable" class="table_wrapper_inner"><caption id="caption_text"></caption><thead><th>Клиент</th><th>ЛC (1)</th><th>Номенклатура (1)</th><th>Кол-во (1)</th><th>Цена (1)</th><th>Сумма (1)</th><th>Номенклатура (2)</th><th>Кол-во (2)</th><th>Цена (2)</th><th>Сумма (2)</th></thead><tbody></tbody></table>');
					$.each(data, function(i, val) {
						if (data[i].price1 !== data[i].price2 || data[i].summ1 !== data[i].summ2){
						$('#reportDataTable').append('<tr style="color:red;"><td>'+data[i].bindings_name+'</td><td>'+data[i].accounts+'</td><td>'+data[i].payment_name+'</td><td>'+data[i].counter1+'</td><td>'+data[i].price1+'</td><td>'+data[i].summ1+'</td><td>'+data[i].payment_name2+'</td><td>'+data[i].counter2+'</td><td>'+data[i].price2+'</td><td>'+data[i].summ2+'</td></tr>');
					}else{
						if(checkbox_flag!==true){
							$('#reportDataTable').append('<tr style="color:green;"><td>'+data[i].bindings_name+'</td><td>'+data[i].accounts+'</td><td>'+data[i].payment_name+'</td><td>'+data[i].counter1+'</td><td>'+data[i].price1+'</td><td>'+data[i].summ1+'</td><td>'+data[i].payment_name2+'</td><td>'+data[i].counter2+'</td><td>'+data[i].price2+'</td><td>'+data[i].summ2+'</td></tr>');
						}
					}
						
						alert(data[i].account2);
					});
					
					oTable = $('#reportDataTable').dataTable({
						"aaSorting": [[0, 'asc']],
						"bJQueryUI": false,
						"bProcessing":true,
						"sPaginationType": "full_numbers",
						"oLanguage": {
							"sUrl": "/assets/admin/js/russian-language-DataTables.txt"
						},
						"bAutoWidth": false,
						"bDestroy": true,
						"sScrollY": "320px",
						"sDom": 'T<"clear">lfrtip',
						"oTableTools": {
							"aButtons": [
								{
									"sExtends": "csv",
									"sButtonText": "Сохранить в CSV"
								}
							],
							"sSwfPath": "/assets/admin/swf/copy_csv_xls_pdf.swf"
						}
					});
					$('#report1C').show();
				},'json');

			}
		</script>
		<script type="text/javascript">
			$(function(){
				$('.column').equalHeight();
			});
		</script>

	</head>


	<body>

		<header id="header">
			<hgroup>
				<h1 class="site_title"><a href="/admin">Администрирование</a></h1>
				<h2 class="section_title">Панель управления</h2>
				<div class="btn_view_site"><a href="<? echo site_url(); ?>">Просмотр</a></div>
			</hgroup>
		</header>
		<section id="secondary_bar">
			<div class="user">
				<p><?php
$user = $this -> data['user'] = $this -> ion_auth -> user($this -> session -> userdata('user_id')) -> row();
echo $user -> username;
?> ( <a href="<? echo site_url(); ?>auth/logout">Выход из системы</a> ) </p>
			</div>
		</section><!-- end of secondary bar -->