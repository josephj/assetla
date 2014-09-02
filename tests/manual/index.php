<?php
require('../../src/Assetla.php');
$assetla = new Assetla('config.php');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="created" content="2014-09-02">
<title>Assetla</title>
<script src="http://localhost:55691/livereload.js"></script>
<?php echo $assetla->stylesheet_tags('demo'); ?>
</head>
<body>
    <h1>Assetla</h1>
    <div>
       <p>Just a demo</p>
    </div>
    <?php echo $assetla->javascript_tags('demo'); ?>
</body>
</html>
