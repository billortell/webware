<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-Strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $this->headtitle; ?></title>
    <link rel="stylesheet" href="/_w/css/global.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/_hssui/css/common.css" type="text/css" media="all" />
    <?php echo $this->headStylesheet; ?>
</head>
<body>

<div style="height:10px;"></div>

<ul class="editorPluginNav">
    <li><a href="/hssui/editor-plugin/" <?php if($this->reqs->act == 'editor-plugin') {echo 'class="current"';}?>>Media Library</a></li>
    <li><a href="/hssui/editor-plugin-upload/" <?php if(in_array($this->reqs->act, array('editor-plugin-upload'))) {echo 'class="current"';}?>>From Computer</a></li>    
    <li><a href="javascritp:;" onclick="window.close();">Close</a></li>
</ul>

<?php print $this->content; ?>

</body>
</html>
