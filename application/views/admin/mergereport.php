
<section id="main" class="column">

		<h4 class="alert_info">Для построения отчета необходимо выбрать услугу, выбрать сравниваемые месяцы в первом списке и во втором.</h4>

		<article class="module width_full">
		<header>
			<h3 class="tabs_involved">Построение сравнительного отчета</h3>
		</header>

			<div class="module_content">
						<fieldset style="width:48%; float:left; margin-right: 1%; padding-left:10px;"> <!-- to make two field float next to one another, adjust values accordingly -->
							<label>Тип услуги:</label><br><br/>
								<?php
								$service = new Admin();
								
								foreach ($service->getServiceList() as $row):
								$a[$row->id] = $row->service_description;
								endforeach;
								
								echo form_dropdown('services', $a, '','id="services" style="width:10%; multiple="multiple"');
								
								?>
						</fieldset>
				
						<fieldset style="width:48%; float:left; padding-left:10px;" > <!-- to make two field float next to one another, adjust values accordingly -->
							<label>Период:</label><br/><br/>
							======>
							<select style="width:10%" id="month1">
								<option value="01">Январь</option>
								<option value="02">Февраль</option>
								<option value="03">Март</option>
								<option value="04">Апрель</option>
								<option value="05">Май</option>
								<option value="06">Июнь</option>
								<option value="07">Июль</option>
								<option value="08">Август</option>
								<option value="09">Сентябрь</option>
								<option value="10">Октябрь</option>
								<option value="11">Ноябрь</option>
								<option value="12">Декабрь</option>
							</select>
							======>
							<select style="width:10%" id="month2">
								<option value="01">Январь</option>
								<option value="02">Февраль</option>
								<option value="03">Март</option>
								<option value="04">Апрель</option>
								<option value="05">Май</option>
								<option value="06">Июнь</option>
								<option value="07">Июль</option>
								<option value="08">Август</option>
								<option value="09">Сентябрь</option>
								<option value="10">Октябрь</option>
								<option value="11">Ноябрь</option>
								<option value="12">Декабрь</option>
							</select>
						</fieldset>

				<div class="clear"></div>
				<div id="report1C" style="display:none;">
				
					</div>
				</div>
			<div class="clear"></div>

		<div class="spacer"></div>
			<footer>
				<div class="submit_link" style="float:left; margin-left:10px;">
					<label for="chbox">Показывать только расхождения</label>
					<input id="chbox" type="checkbox" name="chbox" checked/>
				</div>
				<div class="submit_link">
					<input type="submit" value="Сформировать" class="alt_btn" onclick="buildMergeReport(); return false;">
				</div>
			</footer>
		</article><!-- end of content manager article -->
		<div class="clear"></div>

		<div class="spacer"></div>
	</section>


</body>

</html>