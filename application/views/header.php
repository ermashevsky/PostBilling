<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<title>Система X</title>
        <script type="text/javascript" src="/assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="/assets/js/jquery.cookie.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script type="text/javascript" src="/assets/js/jquery.dataTables.js"></script>
        <script type="text/javascript" src="/assets/js/jquery.dataTables.editable.js"></script>
        <script type="text/javascript" src="/assets/js/ColumnFilterWidgets.js"></script>
        <script type="text/javascript" src="/assets/js/ui.datepicker-ru.js"></script>
        <script type="text/javascript" src="/assets/js/jquery.jnotify.js"></script>
        <script type="text/javascript" src="/assets/js/jquery.maskMoney.0.2.js"></script>
		<script type="text/javascript" src="/assets/js/plupload.full.js"></script>
		<script type="text/javascript" src="/assets/js/plupload.browserplus.js"></script>
		<script type="text/javascript" src="/assets/js/jquery.qtip-1.0.0-rc3.min.js"></script>
		<script type="text/javascript" src="/assets/js/jquery.jeditable.js"></script>

        <script type="text/javascript" src="/assets/js/jquery.maskedinput-1.2.2.js"></script>
        <script type="text/javascript" src="/assets/js/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="/assets/js/jquery.ui.selectmenu.js"></script>
        <script type="text/javascript" src="/assets/js/jquery.uniform.js"></script>
        <script type="text/javascript" src="/assets/js/jquery.progressbar.js"></script>
        <script type="text/javascript" src="/assets/js/ajaxupload.3.5.js"></script>
		<script type="text/javascript" src="/assets/js/ZeroClipboard.js"></script>
		<script type="text/javascript" src="/assets/js/TableTools.js"></script>
		<script type="text/javascript" src="/assets/js/jquery.blockUI.js"></script>
		<script type="text/javascript" src="/assets/js/selectToUISlider.jQuery.js"></script>
		<script type="text/javascript" src="/assets/js/jquery.highlight-4.js"></script>
		<script type="text/javascript" src="/assets/js/jquery.datetimepicker.js"></script>


        <link href="/assets/images/icons/favicon.ico" rel="shortcut icon" type="image/ico" />

        <link rel="stylesheet" href="/assets/styles/jquery.jnotify.css" type="text/css" media="all" />

        <link rel="stylesheet" href="/assets/styles/admin.css" type="text/css" media="all" />
        <link rel="stylesheet" href="/assets/styles/style.css" type="text/css" media="all" />
        <link rel="stylesheet" href="/assets/styles/ColumnFilterWidgets.css" type="text/css" media="all" />
        <link rel="stylesheet" href="/assets/styles/jquery.dataTables_themeroller.css" type="text/css" media="all" />
        <link rel="stylesheet" href="/assets/styles/cupertino/jquery-ui-1.8.17.custom.css" type="text/css" media="all" />
        <link rel="stylesheet" href="/assets/styles/uniform.default.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/assets/styles/uploadify.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/assets/styles/TableTools.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/assets/styles/ui.slider.extras.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/assets/styles/jquery.ui.selectmenu.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/assets/styles/jquery.datetimepicker.css" type="text/css" media="all" />
		
	</head>
	<body>
		<!-- Header -->
		<div id="head">
            <div id="logo_user_details">
                <h2 id="logo">Post Billing</h2>
                <div id="user_details">
                    <ul id="user_details_menu">
						
                        <li>Welcome, <a href="#"><strong><?php
									$user = $this -> data['user'] = $this -> ion_auth -> user($this -> session -> userdata('user_id')) -> row();
									echo $user -> username;
									?></strong></a></li>
                        <li>
                            <ul id="user_access">
								<?php if ($this -> ion_auth -> is_admin()): ?>
	                                <li class="first"><a href="<? echo site_url(); ?>admin">Админка</a></li>
<?php endif; ?>
                                <li class="last"><a href="<? echo site_url(); ?>auth/logout">Выйти</a></li>
                            </ul>
                        </li>

                </div>
			</div>

		</div>
		