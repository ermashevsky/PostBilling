<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<html>
<head>
<title>Upload Form</title>
<script src="<?php echo base_url(); ?>assets/js/jquery-1.2.6.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.MultiFile.min.js" type="text/javascript" language="javascript"></script>
</head>
<body>

<?php if (isset($error)) echo $error;?>
<?php echo form_open_multipart('upload/do_upload');?>
<input type="file" name="userfile[]" size="20" class="multi" />
<br /><br />

<input type="submit" value="upload" />

</form>

</body>
</html>

