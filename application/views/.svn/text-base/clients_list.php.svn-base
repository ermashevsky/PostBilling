
<script type="text/javascript">
    $(document).ready(function() {
    $('#clients').dataTable({
		"bStateSave": true,
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
		"iDisplayLength": 25,
		"aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Все"]],
		"oLanguage": {
							"sUrl": "/assets/js/russian-language-DataTables.txt"
		}

    });
    $("#add_client").button( {
                text: true,
                icons: {
                primary: "ui-icon-gear"
                }
            })
} );
    </script>
<div id="menus_wrapper">
                                    <div id="breadcrumb">
                                    <?php echo set_breadcrumb(); ?>
                                    </div>
                                </div>
		<!-- End Small Nav -->

                <div class="section_content">
                     <div id="infoMessage" class="msg msg-error"><?php //echo $message;?></div>
                    <div id="page_header"><h2>Список клиентов</h2></div>
<table  id="clients" class="table_wrapper_inner">
    <thead>
    <tr>
        <th>№</th>
        <th>Наименование клиента</th>
        <th>ИНН</th>
        <th>Начислено</th>
        <th>Оплачено</th>
		<th>Долг</th>
        <th>Услуги</th>
    </tr>
    </thead>
    <?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $n=1;
    foreach($clients_list as $clients_obj) {
		$nachislenie=$clients_obj->nachislenie;
		$oplata = $clients_obj->oplata;

		(double)$dolg = $nachislenie-$oplata;// <--
        ?>
    <tr>
    	<td><?php echo $n++; ?></td>
    	<td><?php print anchor("clients/accounts/".$clients_obj->id, $clients_obj->client_name); ?></td>
    	<td><?php print $clients_obj->inn; ?></td>
                <td><?php print $nachislenie; ?></td>
                <td><?php print $oplata; ?></td>
				<td><?php print round($dolg, 2); ?></td>
                <td><?php print $clients_obj->account; ?></td>
    </tr>
    <?php } ?>
</table>
<p>
    <?php print anchor("clients/add_client/","Добавить клиента","id='add_client'")?>
</p>
</div>
