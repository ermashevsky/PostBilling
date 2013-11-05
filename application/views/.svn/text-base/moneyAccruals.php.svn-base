<style type="text/css">
		#slideDatePeriod {font-size: 82.5%; font-family:"Segoe UI","Helvetica Neue",Helvetica,Arial,sans-serif; text-align: center;}
		fieldset { border:0; margin: 6em; height: 12em;}
		label {font-weight: normal; float: left; margin-right: .5em; font-size: 1.1em;}
		select {margin-right: 1em; float: left;}
		.ui-slider {clear: both; top: 5em;}
	</style>
	<script type="text/javascript">
		$(function(){

			//demo 3
			$('select#valueAA, select#valueBB').selectToUISlider({
				labels: 12
			});

			//fix color
			fixToolTipColor();
		});
		//purely for theme-switching demo... ignore this unless you're using a theme switcher
		//quick function for tooltip color match
		function fixToolTipColor(){
			//grab the bg color from the tooltip content - set top border of pointer to same
			$('.ui-tooltip-pointer-down-inner').each(function(){
				var bWidth = $('.ui-tooltip-pointer-down-inner').css('borderTopWidth');
				var bColor = $(this).parents('.ui-slider-tooltip').css('backgroundColor')
				$(this).css('border-top', bWidth+' solid '+bColor);
			});
		}

		function getPeriodicalData()
		{
			var dateStart = $('#valueAA').val();
			var dateEnd = $('#valueBB').val();
			//alert(dateStart+'=>'+dateEnd);
			$.blockUI({ message: '<h1><img src="/assets/images/busy.gif" /> Работаем...</h1>' });
			$.post('<?=site_url('money/getPeriod2');?>',{'dateStart':dateStart, 'dateEnd': dateEnd},
				function(data){
					$.unblockUI();
					console.info(data);
					$('#log').val(data);
				   });

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
                    <div id="page_header"><h2>Начисления</h2></div>
<form id="slideDatePeriod">
		<!-- demo 3 -->
		<fieldset>
			<label for="valueAA">Начало периода:</label>
			<select name="valueAA" id="valueAA" disabled="disabled" >
				<optgroup label="2012">
					<option value="08/12" selected="selected">Август 2012</option>
					<option value="09/12">Сентябрь 2012</option>
					<option value="10/12">Октябрь 2012</option>
					<option value="11/12">Ноябрь 2012</option>
					<option value="12/12">Декабрь 2012</option>
				</optgroup>
				<optgroup label="2013">
					<option value="01/13">Январь 2013</option>
					<option value="02/13">Февраль 2013</option>
					<option value="03/13">Март 2013</option>
					<option value="04/13">Апрель 2013</option>
					<option value="05/13">Май 2013</option>
					<option value="06/13">Июнь 2013</option>
					<option value="07/13">Июль 2013</option>
					<option value="08/13">Август 2013</option>
					<option value="09/13">Сентябрь 2013</option>
					<option value="10/13">Октябрь 2013</option>
					<option value="11/13">Ноябрь 2013</option>
					<option value="12/13">Декабрь 2013</option>
				</optgroup>

			</select>

			<label for="valueBB">Конец периода:</label>
			<select name="valueBB" id="valueBB" disabled="disabled">
				<optgroup label="2012">
					<option value="08/12">Август 2012</option>
					<option value="09/12">Сентябрь 2012</option>
					<option value="10/12">Октябрь 2012</option>
					<option value="11/12">Ноябрь 2012</option>
					<option value="12/12">Декабрь 2012</option>
				</optgroup>
				<optgroup label="2013">
					<option value="01/13">Январь 2013</option>
					<option value="02/13">Февраль 2013</option>
					<option value="03/13"  selected="selected">Март 2013</option>
					<option value="04/13">Апрель 2013</option>
					<option value="05/13">Май 2013</option>
					<option value="06/13">Июнь 2013</option>
					<option value="07/13">Июль 2013</option>
					<option value="08/13">Август 2013</option>
					<option value="09/13">Сентябрь 2013</option>
					<option value="10/13">Октябрь 2013</option>
					<option value="11/13">Ноябрь 2013</option>
					<option value="12/13">Декабрь 2013</option>
				</optgroup>
			</select>
		</fieldset>
		<div class="write-comment">
			<textarea class="comment_content_bigbox" cols="80" id="log" name="comment" rows="20" readonly>
			</textarea>
		</div>

	</form>
		<p>
		<button name ="getPeriodicalData" onclick="getPeriodicalData();return false;">Получить данные</button>
		</p>
</div>
