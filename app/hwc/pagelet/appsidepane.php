<?php

$this->headtitle = "Web Creator";

if ($this->reqs->id == null) {
    die('ERROR');
}
$appid  = $this->reqs->id;
if (!file_exists(SYS_ROOT."app/{$appid}")) {
    die('ERROR');
}


$cpath  = trim($this->reqs->cpath, '/');
$cpath  = preg_replace("/\/+/", "/", $cpath);


if (strlen($cpath)) {
    $_cpath = strstr($cpath, strrchr($cpath, "/"), true);
    echo "<div>
        <img src='/app/hwc/static/img/arrow_undo.png' align='absmiddle' alt='{$_cpath}' /> 
        <a href=\"javascript:_hwc_dir('{$appid}','{$_cpath}')\">Go Back</a></div>";
}


$base = SYS_ROOT."app/{$appid}/";
$blen = strlen(SYS_ROOT."app/");

$dir    = '';
$file   = '';

$glob = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $base.$cpath."/*");

foreach (glob($glob) as $f) {

    //$fn = '';
    $fn = substr(strrchr($f, "/"), 1);
    $fm = mime_content_type($f);
    
    $fmi = 'page_white';
    $href = null;
    
    if ($fm == 'directory') {
        
        if ($fn == 'pagelet') {
            $fmi = 'layers';
        } else if ($fn == 'page') {
            $fmi = 'layout_content';
        } else {
            $fmi = 'folder';
        }
        
        $href = "javascript:_hwc_dir('{$appid}', '{$cpath}/$fn')";
        
    } else if (substr($fm,0,4) == 'text') {

        if ($fm == 'text/x-php' || substr($f,-4) == '.php') {
            $fmi = 'page_white_php';
        }
        
        $path = "{$appid}/{$cpath}/$fn";
        //$path = preg_replace("/\-+/", "-", $path);
        $path = preg_replace("/\/+/", "/", $path);
        $href = "javascript:pl_open('{$path}')";
        
    } else if (substr($fm, 0, 5) == 'image') {
        $fmi = 'page_white_picture';
    }
    
    $li = ($href === null) ? $fn : "<a href=\"{$href}\">{$fn}</a>";
    
    $li = "<div><img src='/app/hwc/static/img/{$fmi}.png' align='absmiddle' title='{$fm}' /> $li</div>";
    
    echo $li;
}


?>
<script type="text/javascript">

function _hwc_dir(appid, path) {
    $.ajax({ 
        type: "GET",
        url: '/hwc/appsidepane?id='+appid+'&cpath='+path,
        data: '',
        success: function(data){
            $("#hwc_layout_sidebar").empty().append(data);
            window.scrollTo(0,0);
        }
    });
}

</script> 
