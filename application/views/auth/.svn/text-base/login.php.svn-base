
<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content" id="centered">
      <div id="page_header"><h2>Авторизация</h2></div>
      
      <div class="box-content" >
	<p>Пожалуйста авторизуйтесь под своим логином/email и паролем.</p>
	
        <div id="infoMessage_auth" class="msg msg-error"><?php echo $message;?></div>
	
    <?php echo form_open("auth/login");?>
        <div class="main">
      <p class="field">
      	<label for="identity">Email/Логин:</label>
      	<?php echo form_input($identity);?>
      </p>
      
      <p class="field">
      	<label for="password">Пароль:</label>
      	<?php echo form_input($password);?>
      </p>
      
      <p class="field">
	      <label for="remember">Запомнить меня:</label>
	      <?php echo form_checkbox('remember', '1', FALSE);?>
              <p><a href="<? echo site_url();?>/auth/forgot_password">Забыли пароль?</a></p>
	  </p>
        
          <div class="buttons_auth">
      <p><?php echo form_submit('submit', 'Войти');?></p>
          </div>
      </div>
    <?php echo form_close();?>
    </div>
    </div>

