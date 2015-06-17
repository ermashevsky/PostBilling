<style>
  .custom-combobox {
    position: relative;
    display: inline-block;
  }

  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
    height: 22px;
  }
  .custom-combobox-input {
    margin: 0;
    padding: 0.3em;
  }
  .ui-autocomplete { height: 300px; overflow-y: scroll; overflow-x: hidden;}

  .ui-tooltip
{
    font-size:13px;
    font-weight: bold;
	max-height: 200px;
	max-width: 200px;
	padding:10px;
}
  </style>
  <script>
  (function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );

        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },

      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";

        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });

        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },

          autocompletechange: "_removeIfInvalid"
        });
      },

      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;

        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Показать все номера" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();

            // Close if already visible
            if ( wasOpen ) {
              return;
            }

            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },

      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },

      _removeIfInvalid: function( event, ui ) {

        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }

        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });

        // Found a match, nothing to do
        if ( valid ) {
          return;
        }

        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", "Введенный номер "+value + " отсутствует либо уже занят. Сейчас мы его поищем." )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
		  $.post('<?php echo site_url('/services/whoBusyPhone');?>',{'resource':value},
        function(data){
			if(data !=""){
			$("div#content_phone").empty();
			$("div#content_phone").append("<div class='headSearchResult'>Найдены совпадения номера:</div><br/><ul style='margin:10px;'></ul>");
			$.each(data, function(i, val) {
				console.info(data[i].id_clients)
				console.info(data[i].bindings_name)
				console.info(data[i].resources)
				$("div#content_phone ul").append("<li style='padding:2px;'><a href='<?=site_url('clients/accounts');?>/" + data[i].id_clients +"'>Клиент: " + data[i].bindings_name + " ("+data[i].resources+")</a></li>");

			})
			$("div#content_phone").dialog({
				resizable: false,
				height:350,
				modal: true,
				buttons: {
				  "Закрыть": function() {
					  $( this ).dialog( "close" );
				  }}
				  });
				  }else{

				  $("div#content_phone").empty();
				  $("div#content_phone").append("<div class='headSearchResult'>Искомый номер отсутствует в базе.</div><br/>");
				  $("div#content_phone").dialog({
				resizable: false,
				height:150,
				modal: true,
				buttons: {
				  "Закрыть": function() {
					  $( this ).dialog( "close" );
				  }}
				  });
				  }
		},'json')
        }, 3000 );
        this.input.data( "ui-autocomplete" ).term = "";
      },

      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );

  $(function() {
    $( "#phones" ).combobox();
    $( "#toggle" ).click(function() {
      $( "#combobox" ).toggle();
    });
  });
  </script>
<script>
    $(document).ready(function(){

        $("p #submit_form").click(function () {
            var form_fields = $('#form').serialize();

        });
    });
</script>

<script type="text/javascript">
    $(function() {
        $.datepicker.setDefaults($.datepicker.regional['']);
        $("p #datepicker").datepicker($.datepicker.regional["ru"]);
    });

	function check() {
	if ($('input#identifier').val() == "")
		$("div#content_finder").empty()
}

function searchIdentifier(){

		$.post("<?=site_url('clients/searchIdentifiers');?>", { search: $("input#identifier").val() }, function(data){
			$("div#content_finder").empty()
			if(data==''){
				$('#submit_form').prop('disabled', false);
			}else{
				$.each(data, function(){
					var datepickerformat = $.datepicker.formatDate('yy-mm-dd', new Date($.datepicker.parseDate('dd.mm.yy', $('#datepicker').val())));
					if(new Date(this.end_date) > new Date(datepickerformat)){
						$('#submit_form').prop('disabled', true);
						$("div#content_finder").append("<div class='warning'>Найдены совпадения идентификаторов: <a href='<?=site_url('clients/accounts');?>/" + this.id_clients +"'>" + this.bindings_name + "("+this.accounts+")</a>. Пересечение даты</div></fieldset>");
					}
					if(this.end_date == null){
						$('#submit_form').prop('disabled', true);
						$("div#content_finder").append("<div class='warning'>Найдены совпадения идентификаторов: <a href='<?=site_url('clients/accounts');?>/" + this.id_clients +"'>" + this.bindings_name + "("+this.accounts+")</a>. Действующий идентификатор.</div></fieldset>");
					}
				})
			}
		}, "json");
}

$(document).ready(function(){
	$("input#identifier").keyup(function(){
		if ($("input#identifier").val()!=undefined && $("input#identifier").val()!=""){
		check();
		searchIdentifier();
		}
	});

	$("input#datepicker").change(function(){
		if ($("input#identifier").val()!=undefined && $("input#identifier").val()!=""){
			check();
			searchIdentifier();
		}
	});

});
</script>

<style type="text/css">

.info, .success, .warning, .error, .mes, .tips, .chat, .cnb {
    margin: 10px 0px;
    padding: 10px 10px 15px 50px;
    background-repeat: no-repeat;
    background-position: 10px center;
    border-radius: 4px 4px 4px;
}
.info {
    background-color: #d1e4f3;
    background-image: url("http://cdn1.iconfinder.com/data/icons/musthave/24/Information.png");
    color: #00529B;
    border: 1px solid #4d8fcb;
	margin:15px;
	padding-right:50px;
}
.success {
    background-color: #effeb9;
    background-image: url("http://cdn3.iconfinder.com/data/icons/fatcow/32x32_0020/accept.png");
    color: #4F8A10;
    border: 1px solid #9ac601;
	margin:15px;
	padding-right:50px;
}
.warning {
    background-color: #ffeaa9;
    background-image: url("http://cdn3.iconfinder.com/data/icons/fatcow/32x32_0400/error.png");
    color: #9F6000;
    border: 1px solid #f9b516;
	margin:15px;
	padding-right:50px;
}
.error {
    background-color: #fccac3;
    background-image: url("http://cdn1.iconfinder.com/data/icons/CrystalClear/32x32/actions/messagebox_critical.png");
    color: #D8000C;
    border: 1px solid #db3f23;
	margin:15px;
	padding-right:50px;
}
.mes {
    background-color: #F2F2F2;
    background-image: url("http://cdn2.iconfinder.com/data/icons/fugue/bonus/icons-32/mail.png");
    border: 1px solid #AAAAAA;
    color: #545454;
	margin:15px;
	padding-right:50px;
}
.tips {
    background-color: #FEEAC9;
    background-image: url("http://cdn5.iconfinder.com/data/icons/woocons1/Light%20Bulb%20On.png");
    border: 1px solid #D38E49;
    color: #bb640c;
	margin:15px;
	padding-right:50px;
}
.chat {
    background-color: #daecfb;
    background-image: url("http://cdn2.iconfinder.com/data/icons/drf/PNG/iChat.png");
    border: 1px solid #2078c9;
    color: #066ac4;
	margin:15px;
	padding-right:50px;
}
</style>
<div id="menus_wrapper">
    <div id="breadcrumb">
        <?php echo set_breadcrumb(); ?>
    </div>
</div>
<!-- End Small Nav -->

<div class="section_content">
    <div id="infoMessage" class="msg msg-error"><?php //echo $message;  ?></div>
    <div class='mainInfo'>
        <?php
        foreach ($forms as $title_group) {
            $title = $title_group->services_groups;
        }
        ?>
        <div id="page_header"><h2>Добавление услуги - <?php echo $title; ?></h2></div>
        <p></p>
		<div id="content_finder" class="errormsgbox center"></div>
        <?php echo form_open("clients/add_service_data", 'name=form id=form'); ?>

        <?
        $counter = 0;
        $tariff = array();
        $tariff1 = array();
        $tariff2 = array();
		$js = 'id="phones"';
        foreach ($forms as $rows) {

            $counter++;
            if ($rows->payment_type == 'ПЕРИОДИЧЕСКИ') {

                echo '<p style="color:red">' . $rows->payment_type . '</p>';
                echo '<fieldset class=rounded-list2><p id=' . $counter . ' class=rounded-list2><a class="counter">' . $counter . '</a>' . $rows->payment_name . '<br/>';
                echo form_hidden("id_group", $rows->id);
                echo form_hidden("id_account", end($this->uri->segments));
                echo form_hidden("payment_name[$counter]", $rows->payment_name);

                if ($rows->tariff == 1 & $rows->element_type != 'input' & $rows->element_type != 'select') {
                    foreach ($this->clients_model->getTariffById($rows->assortment_id) as $tariffList):
                        $tariff[$counter][$tariffList->id] = $tariffList->tariff_name;
                    endforeach;
                    echo 'Тариф: ' . form_dropdown('tariff[' . $counter . ']', $tariff[$counter]);
                    $opt[$counter] = array(
                        'month' => 'Месяц',
                        'half_month' => 'Полмесяца',
                    );
                    echo '<br/>Период: ' . form_dropdown('period[' . $counter . ']', $opt[$counter]) . '</p></fieldset>'; //!
                }


                if ($rows->element_type == 'input' & $rows->tariff == 1) {

                    foreach ($this->clients_model->getTariffById($rows->assortment_id) as $tariffList1):
                        $tariff[$counter][$tariffList1->id] = $tariffList1->tariff_name;
                    endforeach;

                    echo form_hidden('name[' . $counter . ']', $rows->default_value, 'readonly');
                    echo '<br/>Тариф: ' . form_dropdown('tariff[' . $counter . ']', $tariff[$counter]);
                    $opt[$counter] = array(
                        'month' => 'Месяц',
                        'half_month' => 'Полмесяца',
                    );
                    echo '<br/>Период: ' . form_dropdown('period[' . $counter . ']', $opt[$counter]) . '</p></fieldset>'; //!
                }
                if ($rows->element_type == 'input' & $rows->tariff != 1) {

                    echo form_hidden('name[' . $counter . ']', $rows->default_value, 'readonly') . '</p></fieldset>';
                }

                if ($rows->element_type == 'select' & $rows->tariff == 1) {
                    $this->load->model('clients_model');

                    if ($rows->target != 'tariffs') {

                        foreach ($this->clients_model->getResources($rows->target, $rows->id_client, $rows->type_resources) as $row):
                            $a[$counter][$row->id] = $row->resources;
                        endforeach;

                        foreach ($this->clients_model->getTariffById($rows->assortment_id) as $tariffList2):
                            $tariff[$counter][$tariffList2->id] = $tariffList2->tariff_name;
                        endforeach;

                        echo 'Ресурс: ' . form_dropdown('resources[' . $counter . ']', $a[$counter],'id="select_id" onchange="alert(3)"');
                        echo '<br/>Тариф: ' . form_dropdown('tariff[' . $counter . ']', $tariff[$counter]);
                        $opt[$counter] = array(
                            'month' => 'Месяц',
                            'half_month' => 'Полмесяца',
                        );
                        echo '<br/>Период: ' . form_dropdown('period[' . $counter . ']', $opt[$counter]) . '</p></fieldset>'; //!
                    }
                }
                if ($rows->element_type == 'select' & $rows->tariff != 1) {
                    $this->load->model('clients_model');

                    if ($rows->target != 'tariffs') {

                        foreach ($this->clients_model->getResources($rows->target, $rows->id_client, $rows->type_resources) as $row):
                            $a[$counter][$row->id] = $row->resources;
                        endforeach;


                        echo 'Ресурс: ' . form_dropdown('resources[' . $counter . ']" id="phones', $a[$counter]) . '</p></fieldset>';
                    }
                }
            }else {

                echo '<p style="color:green">' . $rows->payment_type . '</p>';
                echo '<fieldset class=rounded-list2><p id=' . $counter . ' class=rounded-list2><a class="counter">' . $counter . '</a>' . $rows->payment_name . '<br/>';
                echo form_hidden("id_group", $rows->id);
                echo form_hidden("id_account", end($this->uri->segments));
                echo form_hidden("payment_name[$counter]", $rows->payment_name);

                if ($rows->tariff == 1 & $rows->element_type != 'input' & $rows->element_type != 'select') {
                    foreach ($this->clients_model->getTariffById($rows->assortment_id) as $tariffList):
                        $tariff[$counter][$tariffList->id] = $tariffList->tariff_name;
                    endforeach;
                    echo 'Тариф: ' . form_dropdown('tariff[' . $counter . ']', $tariff[$counter]);
                    $opt[$counter] = array(
                        'single_payment' => 'Разовый платеж',
                    );
                    echo '<br/>Период: ' . form_dropdown('period[' . $counter . ']', $opt[$counter]) . '</p></fieldset>'; //!
                }


                if ($rows->element_type == 'input' & $rows->tariff == 1) {

                    foreach ($this->clients_model->getTariffById($rows->assortment_id) as $tariffList1):
                        $tariff[$counter][$tariffList1->id] = $tariffList1->tariff_name;
                    endforeach;

                    echo form_hidden('name[' . $counter . ']', $rows->default_value, 'readonly');
                    echo '<br/>Тариф: ' . form_dropdown('tariff[' . $counter . ']', $tariff[$counter]);
                    $opt[$counter] = array(
                        'single_payment' => 'Разовый платеж',
                    );
                    echo '<br/>Период: ' . form_dropdown('period[' . $counter . ']', $opt[$counter]) . '</p></fieldset>'; //!
                }
                if ($rows->element_type == 'input' & $rows->tariff != 1) {

                    echo form_hidden('name[' . $counter . ']', $rows->default_value, 'readonly');
                    $opt[$counter] = array(
                        'single_payment' => 'Разовый платеж',
                    );
                    echo '<br/>Период: ' . form_dropdown('period[' . $counter . ']', $opt[$counter]) . '</p></fieldset>'; //!
                }

                if ($rows->element_type == 'select' & $rows->tariff == 1) {
                    $this->load->model('clients_model');

                    if ($rows->target != 'tariffs') {

                        foreach ($this->clients_model->getResources($rows->target, $rows->id_client, $rows->type_resources) as $row):
                            $a[$counter][$row->id] = $row->resources;
                        endforeach;

                        foreach ($this->clients_model->getTariffById($rows->assortment_id) as $tariffList2):
                            $tariff[$counter][$tariffList2->id] = $tariffList2->tariff_name;
                        endforeach;

                        echo 'Ресурс: ' . form_dropdown('resources[' . $counter . ']" id="phones', $a[$counter]);
                        echo '<br/>Тариф: ' . form_dropdown('tariff[' . $counter . ']', $tariff[$counter]);
                        $opt[$counter] = array(
                            'single_payment' => 'Разовый платеж',
                        );
                        echo '<br/>Период: ' . form_dropdown('period[' . $counter . ']', $opt[$counter]) . '</p></fieldset>'; //!
                    }
                }
                if ($rows->element_type == 'select' & $rows->tariff != 1) {
                    $this->load->model('clients_model');

                    if ($rows->target != 'tariffs') {

                        foreach ($this->clients_model->getResources($rows->target, $rows->id_client, $rows->type_resources) as $row):
                            $a[$counter][$row->id] = $row->resources;
                        endforeach;


                        echo 'Ресурс: ' . form_dropdown('resources[' . $counter . ']" id="phones', $a[$counter]) . '</p></fieldset>';
                    }
                }
                if ($rows->element_type == 'identifier' & $rows->tariff != 1){
                    echo form_input('name[' . $counter . ']',$rows->default_value, 'id="identifier"').'</p></fieldset>';
                }
                if($rows->element_type == 'none' & $rows->tariff != 1){
                    echo form_hidden('name[' . $counter . ']', $rows->default_value).'</p></fieldset>';
                }
            }

        }
        echo form_hidden("counter", $counter);

        echo '<fieldset class=rounded-list2><p class=rounded-list2>Дата начала:<br/>';
        $data = array(
            'name' => 'datepicker1',
            'id' => 'datepicker',
            'readonly' => 'readonly'
        );
        echo form_input($data);
		echo form_hidden('referer', $this->input->server('HTTP_REFERER'));
        echo '</p></fieldset>';
        ?>
        <p>
        <?php echo form_submit('Submit', 'Добавить запись', 'id="submit_form"'); ?>
        </p>
        <?php echo form_close(); ?>

    </div>
</div>
<div id="content_phone" style="display: none;" title="Похожие номера телефонов"></div>
