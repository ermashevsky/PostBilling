<style type="text/css">
  #assortment {width: 400px;}
  #select2{width: 400px;}
  a #add #remove{
   display: block;
   width:20px;
   border: 1px solid #aaa;
   text-decoration: none;
   background-color: #fafafa;
   color: #123456;
   margin: 2px;
   clear:both;
  }
 
</style>
<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">

    <div id="infoMessage" class="msg msg-error"><?php //echo $message;?></div>


<div id="page_header"><h2>Добавление услуг на ЛС</h2></div>

<p>Пожалуйста выберите группу.</p>
        <?php echo form_open("clients/add_service_to_account",'name=form, id=form'); ?>
        <p>Группа:<br />
        <?php
            foreach($serviceGroup as $row){
                $a[$row->id] = $row->services_groups;
            }
            echo form_dropdown('serviceGroup', $a,'','id="serviceGroup"');
            ?>    
        </p>
        <p>
            <div id="group"></div>
        </p>
        <p><?php echo form_submit('submit', 'Заполнить'); ?></p>


<?php echo form_close(); ?>
        </div>
                
    <div class="sidebar_menu">
        
            <div class="box-head">Тут блок</div>
            <div class="box-content">
            <p><a href="<?php echo site_url('auth/create_user');?>">Новый пользователь</a></p>
            </div>
        
        </div>
                <div class="sidebar_menu">
        
            <div class="box-head">Меню</div>
            <div class="box-content">
            <p><a href="<?php echo site_url('clients');?>">Список клиентов</a></p>
            <p><a href="<?php echo site_url('');?>">Список групп номенклатур</a></p>
            </div>
        
        </div>


