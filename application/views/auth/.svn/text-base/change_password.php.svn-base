<h1>Смена пароля</h1>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("auth/change_password");?>

      <p>Старый пароль:<br />
      <?php echo form_input($old_password);?>
      </p>
      
      <p>Новый пароль:<br />
      <?php echo form_input($new_password);?>
      </p>
      
      <p>Повтор нового пароля:<br />
      <?php echo form_input($new_password_confirm);?>
      </p>
      
      <?php echo form_input($user_id);?>
      <p><?php echo form_submit('submit', 'Сменить пароль');?></p>
      
<?php echo form_close();?>