<script type="text/javascript">

var n=0;

function readFileDebt(path){

$.post('<?=site_url('money/readFileDebt');?>',{'pathfile':path},
	function(data){
		
	})

  }
function deleteFile(path){
	$.post('<?=site_url('upload/deleteFromServer');?>',{'pathfile':path},
	function(data){
	    window.location.reload();
	});
    }
    </script>

<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php //echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">
                     <div id="infoMessage" class="msg msg-error"><?php //echo $message;?></div>
                    <div id="page_header"><h2>Сверка остатков</h2></div>
<p>
		   <?php if(isset($error)){echo $error;} ?>

		    <?php echo form_open_multipart('money/do_upload','id=form3'); ?>
		    <input type="hidden" name="folder" size="20" value="ostatki"/>
		    <input type="file" name="userfile" size="20" />
 		    <input type="submit" value="Загрузить" />

		    </form>
		</p>
		<?php
		$fullpath = new Money();
		?>
<div class="file_block_money_debt"><b>Файл импорта:</b> <?=$fullpath->getFileDebt(); ?>
	<a href="#" onclick="deleteFile('<?=$fullpath->getFileDebt()?>'); return false;">
		<button name="" id="mts_file_delete" > Удалить</button></a>
	<a href="#" class="import_button" onclick="readFileDebt('<?=$fullpath->getFileDebt()?>'); return false;">
		<button name="" id="mts_file_parse" >Распарсить</button></a><div id="prog-5"></div></div>
<?php
//    ini_set('display_errors', 1);
//    error_reporting(E_ALL);
//$n=1;
//foreach($checkData as $check) {
//echo '===> '.$n++;
//	echo $check -> name, PHP_EOL;
//	echo $check -> account, PHP_EOL;
//	echo $check -> amount, PHP_EOL;
//	echo $check -> payment, PHP_EOL;
//
//	$nachislenie = $check -> amount;
//	$oplata = $check -> payment;
//	$dolg = $nachislenie - $oplata; // <--
//	echo round($dolg,2), PHP_EOL . '<br/>';
//
//
//    }
//	?>
</div>
