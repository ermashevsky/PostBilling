<script type="text/javascript">
    $(document).ready(function() {
    $('#flex1').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers"
        
    });
} );
    </script>
<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">

    <div id="infoMessage" class="msg msg-ok"><?php echo $message;?></div>


<div id="page_header"><h2>Пользователи</h2></div>

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
                                $n=1;
                                foreach ($users as $user):?>
			<tr>
				<td><? echo $n++;?></td>
                                                                <td><?php echo $user->first_name;?></td>
				<td><?php echo $user->last_name;?></td>
				<td><?php echo $user->email;?></td>
				<td>
					<?php foreach ($user->groups as $group):?>
						<?php echo $group->name;?><br />
	                <?php endforeach?>
				</td>
				<td><?php echo ($user->active) ? anchor("auth/deactivate/".$user->id, 'Активен',array('class'=>'ico active')) : anchor("auth/activate/". $user->id, 'Заблокирован',array('class'=>'ico inactive'));?></td>
                                                                <td><?php echo (anchor("auth/edit_user/".$user->id, 'Редактировать',array('class'=>'ico edit')));?></td>
			</tr>
		<?php endforeach;?>
            </tbody>
	</table>
                </div>
    