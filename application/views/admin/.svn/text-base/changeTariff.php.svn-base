<section id="main" class="column">
	<article class="module width_full">
		<header><h3 class="tabs_involved">Пакетная смена тарифного плана</h3>
		</header>
		<div class="module_content">
			<fieldset>
				<label>Услуги</label>

				<?php
		    $service = new Admin_model();
		    foreach ($service->getServiceType() as $row):
			$a['NULL'] = 'Выберите услугу';
			$a[$row->marker] = $row->service_description;
		    endforeach;
		    echo form_dropdown('serviceList', $a,'','id="services" onchange="selectRegion();return false;" style="float:left;"');
		    ?>
			</fieldset>
			<fieldset>
				<label>Список номенклатуры</label>
			<div name="selectAssortmentList" style="float:left;"></div>
			</fieldset>
			<fieldset>
				<label>Список тарифов</label>
			<div name="selectTariffs" style="float:left;"></div>
			</fieldset>
			<fieldset style="width:48%; float:left; margin-right: 3%;"> <!-- to make two field float next to one another, adjust values accordingly -->
				<label for="date">Дата начала действия</label>
				<input type="text" name="date_change_tariff" id="date_change_tariff" style="width:92%;">
			</fieldset>
			<fieldset style="width:48%; float:left;"> <!-- to make two field float next to one another, adjust values accordingly -->
				<label>Новый тарифный план</label>
				<div name="selectNewTariffs" style="float:left; width: 92%;"></div>
			</fieldset><div class="clear"></div>
		</div>
		<footer>
			<div class="submit_link" style="display:none;">
				<input type="submit" value="Поиск" onclick="searchTariff();return false;">
			</div>
		</footer>
	</article><!-- end of content manager article -->

	<div class="clear"></div>
	<article class="module width_full">
		<header><h3>Post New Article</h3></header>
		<div class="module_content">
			<fieldset>
				<label>Post Title</label>
				<input type="text">
			</fieldset>
			<fieldset>
				<label>Content</label>
				<textarea rows="12"></textarea>
			</fieldset>
			<fieldset style="width:48%; float:left; margin-right: 3%;"> <!-- to make two field float next to one another, adjust values accordingly -->
				<label>Category</label>
				<select style="width:92%;">
					<option>Articles</option>
					<option>Tutorials</option>
					<option>Freebies</option>
				</select>
			</fieldset>
			<fieldset style="width:48%; float:left;"> <!-- to make two field float next to one another, adjust values accordingly -->
				<label>Tags</label>
				<input type="text" style="width:92%;">
			</fieldset><div class="clear"></div>
		</div>
		<footer>
			<div class="submit_link">
				<select>
					<option>Draft</option>
					<option>Published</option>
				</select>
				<input type="submit" value="Publish" class="alt_btn">
				<input type="submit" value="Reset">
			</div>
		</footer>
	</article><!-- end of post new article -->

	<h4 class="alert_warning">A Warning Alert</h4>

	<h4 class="alert_error">An Error Message</h4>

	<h4 class="alert_success">A Success Message</h4>

	<article class="module width_full">
		<header><h3>Basic Styles</h3></header>
		<div class="module_content">
			<h1>Header 1</h1>
			<h2>Header 2</h2>
			<h3>Header 3</h3>
			<h4>Header 4</h4>
			<p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Cras mattis consectetur purus sit amet fermentum. Maecenas faucibus mollis interdum. Maecenas faucibus mollis interdum. Cras justo odio, dapibus ac facilisis in, egestas eget quam.</p>

			<p>Donec id elit non mi porta <a href="#">link text</a> gravida at eget metus. Donec ullamcorper nulla non metus auctor fringilla. Cras mattis consectetur purus sit amet fermentum. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>

			<ul>
				<li>Donec ullamcorper nulla non metus auctor fringilla. </li>
				<li>Cras mattis consectetur purus sit amet fermentum.</li>
				<li>Donec ullamcorper nulla non metus auctor fringilla. </li>
				<li>Cras mattis consectetur purus sit amet fermentum.</li>
			</ul>
		</div>
	</article><!-- end of styles article -->
<div class="spacer"></div>
</section>

<div id="dialog-confirm" title="Пакетная смена тарифного плана">

</div>
<div id="dialog-confirm-ok" title="Пакетная смена тарифного плана">

</div>


</body>

</html>