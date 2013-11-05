<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Система X</title>
        <script type="text/javascript" src="/assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="/assets/js/jquery-ui-1.8.17.custom.min.js"></script>
        <script type="text/javascript" src="/assets/js/jquery.dataTables.js"></script>
        
        <link rel="stylesheet" href="/assets/styles/admin.css" type="text/css" media="all" />
        <link rel="stylesheet" href="/assets/styles/style.css" type="text/css" media="all" />
        <link rel="stylesheet" href="/assets/styles/jquery.dataTables_themeroller.css" type="text/css" media="all" />
        <link rel="stylesheet" href="/assets/styles/cupertino/jquery-ui-1.8.17.custom.css" type="text/css" media="all" />
        
    </head>
    <body>
        <!-- Header -->

        <div id="head">
            <div id="logo_user_details">
                <h2 id="logo">Post Billing - администрирование</h2>
                <div id="user_details">
                    <ul id="user_details_menu">
                        <li>Welcome, <a href="#"><strong><?php $user = $this->data['user'] = $this->ion_auth->user($this->session->userdata('user_id'))->row();
echo $user->username;
?></strong></a></li>
                        <li>
                            <ul id="user_access">
                                <li class="first"><a href="#">Настройки профиля</a></li>
                                <li class="last"><a href="<? echo site_url(); ?>/auth/logout">Выйти</a></li>
                            </ul>
                        </li>
                </div>
            </div>
        </div>

        <!-- End Logo + Top Nav -->
