<style>
    .small-button {
   font-size: .8em !important;
   margin: 4px;
}

</style>
<script>
	$(function() {
		$( "#tabs" ).tabs();
		$(".readfile").uniform({
		    fileDefaultText: 'Выберите файл',
		    fileBtnText: 'Выбрать'
		});

	$("#readFileContent1").button( {
	    text: true,
	    icons: {
	    primary: "ui-icon-transferthick-e-w"

	    }
	}).click(function(){
	    var filepath = $('input[name="readfile1"]').val();

	});
	$("#readFileContent2").button( {
	    text: true,
	    icons: {
	    primary: "ui-icon-transferthick-e-w"

	    }
	}).click(function(){
	    var filepath = $('input[name="readfile2"]').val();

	});
	$("#readFileContent3").button( {
	    text: true,
	    icons: {
	    primary: "ui-icon-transferthick-e-w"

	    }
	}).click(function(){
	    var filepath = $('input[name="readfile3"]').val();
	    
	});
	});
	</script>
<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">
                     <div id="infoMessage" class="msg msg-error"><?php //echo $message;?></div>
                    <div id="page_header"><h2>Импорт данных</h2></div>
<p></p>
    <div id="tabs">
	<ul>
		<li><a href="#tabs-1">Местная телефонная связь (ЮЛ)</a></li>
		<li><a href="#tabs-2">Виртуальная АТС (ВА)</a></li>
		<li><a href="#tabs-3">Универсальный номер (ДН)</a></li>
		<li><a href="#tabs-3">IP-телефония (ТК)</a></li>
	</ul>
	<div id="tabs-1">
		<p>
		   <input type="file" name="readfile1" class="readfile" /><button id="readFileContent1" class="small-button">Импорт данных</button>
		</p>
	</div>
	<div id="tabs-2">
		<p>
		   <input type="file" name="readfile2" class="readfile" /><button id="readFileContent2" class="small-button">Импорт данных</button>
		</p>
	</div>
	<div id="tabs-3">
		<p>
		   <input type="file" name="readfile3" class="readfile" size="50"/><button id="readFileContent3" class="small-button">Импорт данных</button>
		</p>
	</div>
</div>

<?php echo $error;?>

<?php echo form_open_multipart('upload/do_upload');?>

<input type="file" name="userfile" size="20" />

<br /><br />

<input type="submit" value="upload" />

</form>
<?php foreach($csvData as $field){
    echo '<br/>'.$field['ID'].'<br/>';
        echo $field['Resources'].'<br/>';
	    echo $field['Date'].'<br/>';
	        echo $field['Amount'].'<br/>++++++++++++++++';

}
?>
</div>

