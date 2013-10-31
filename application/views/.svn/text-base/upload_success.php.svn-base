<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<html>
<head>
<title>Upload Form</title>
</head>
<body>

<h3>Your file was successfully uploaded!</h3>

<ul>
<?php
 foreach($upload_data as $file) {
    echo '<li><ul>';
    foreach ($file as $item => $value) {
        echo '<li>'.$item.': '.$value.'</li>';
    }
    echo '</ul></li>';
 } 
?>
</ul>

<p><?php echo anchor('upload', 'Upload More Files!'); ?></p>

</body>
</html>