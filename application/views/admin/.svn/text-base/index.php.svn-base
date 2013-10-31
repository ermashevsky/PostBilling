
<section id="main" class="column">

		<h4 class="alert_info">Welcome to the free MediaLoot admin panel template, this could be an informative message.</h4>

		<article class="module width_full">
			<header><h3>Пользователи онлайн</h3></header>
			<div class="module_content">
				<article class="stats_graph">
					
				</article>

				<article class="stats_overview">
					<div class="overview_today">
						<p class="overview_day">Today</p>
						<p class="overview_count">1,876</p>
						<p class="overview_type">Hits</p>
						<p class="overview_count">2,103</p>
						<p class="overview_type">Views</p>
					</div>
					<div class="overview_previous">
						<p class="overview_day">Yesterday</p>
						<p class="overview_count">1,646</p>
						<p class="overview_type">Hits</p>
						<p class="overview_count">2,054</p>
						<p class="overview_type">Views</p>
					</div>
				</article>
				<div class="clear"></div>
			</div>
		</article><!-- end of stats article -->

		<div id="dialog" style="display: none;">
			<p>Вы действительно хотите удалить пользователя?</p>
		</div>

		<article class="module width_full">
		<header><h3 class="tabs_involved">Пользователи</h3>
		</header>
			<div class="module_content">
				<table  id="flex1" class="table_wrapper_inner">
					<thead>
						<tr>
							<th>№</th>
							<th>Имя</th>
							<th>Фамилия</th>
							<th>Email</th>
							<th>Группа</th>
							<th>Статус</th>
							<th>Действие</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$n = 1;
						foreach ($users as $user):
							?>
							<tr>
								<td><? echo $n ++; ?></td>
								<td><?php echo $user -> first_name; ?></td>
								<td><?php echo $user -> last_name; ?></td>
								<td><?php echo $user -> email; ?></td>
								<td>
									<?php foreach ($user -> groups as $group): ?>
										<?php echo $group -> name; ?><br />
									<?php endforeach ?>
								</td>
								<td><?php echo ($user -> active) ? anchor("admin/deactivate/" . $user -> id, 'Активирован', array('class' => 'label label-success')) : anchor("admin/activate/" . $user -> id, 'Заблокирован', array('class' => 'label label-important')); ?></td>
								<td>
								<?php echo (anchor("admin/edit_user/" . $user -> id, 'Редактировать', array('class' => 'label label-info','style'=>'margin-right:5px;')));?>
									<a class="label label-important" onclick="deleteUser('<?php echo $user->id; ?>','<?php echo $user->username; ?>');return false;">Удалить</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
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


</body>

</html>