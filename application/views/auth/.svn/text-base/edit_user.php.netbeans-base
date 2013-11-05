
<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">

    <div id="infoMessage" class="msg msg-error"><?php echo $message;?></div>


<div class='mainInfo'>

  <div id="page_header"><h2>Редактирование пользователя</h2></div>
	<p>Пожалуйста введите нужную информацию о пользователе.</p>
	
        <div class="box-content">	
    <?php echo form_open("auth/edit_user/".$this->uri->segment(3));?>
      <p>Имя:<br />
      <?php echo form_input($first_name);?>
      </p>
      
      <p>Фамилия:<br />
      <?php echo form_input($last_name);?>
      </p>
      
      <p>Компания:<br />
      <?php echo form_input($company);?>
      </p>
      
      <p>Email:<br />
      <?php echo form_input($email);?>
      </p>
      
      <p>Телефон:<br />
      <?php echo form_input($phone1);?>-<?php echo form_input($phone2);?>-<?php echo form_input($phone3);?>
      </p>
      
      <p>
      	<input type=checkbox name="reset_password"> <label for="reset_password">Сбросить пароль</label>
      </p>
      
      <?php //echo form_input($user_id);?>
      <p><?php echo form_submit('submit', 'Сохранить');?></p>

      
    <?php echo form_close();?>
        </div>
</div>
    </div>