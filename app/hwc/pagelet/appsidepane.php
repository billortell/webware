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

$cpath_nav = '';
if (strlen($cpath)) {
  $_cpath = strstr($cpath, strrchr($cpath, "/"), true);
  $cpath_nav = "<span>
    <img src='/app/hwc/static/img/arrow_undo.png' align='absmiddle' alt='{$_cpath}' /> 
    <a href=\"javascript:_hwc_dir('{$appid}','{$_cpath}')\">Back</a></span> ";
}

$base = SYS_ROOT."app/{$appid}/";
$blen = strlen(SYS_ROOT."app/");
if (!file_exists($base."/info.php")) {
  die('ERROR');
}

$info = require $base."/info.php";
?>
<div class="sidebar_appinfo">
  <div><b><?=$info['name']?></b></div>
  <div><?=$info['version']?></div>
</div>

<div class="sidebar_apppathnav">
  <span><?=$cpath_nav?></span>
  <span>[<a class="anoline" href="javascript:_open_window('/hwc/appnewfile?path=<?=$appid?>/<?=$cpath?>', 'New', '600', '300')">New File</a>]</span>
</div>
<?php

$dir  = '';
$file   = '';

$glob = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $base.$cpath."/*");

foreach (glob($glob) as $f) {

  //$fn = '';
  $fn = substr(strrchr($f, "/"), 1);
  $fm = mime_content_type($f);
  
  $fmi = 'page_white';
  $href = null;
  
  $path = "{$appid}/{$cpath}/$fn";
  //$path = preg_replace("/\-+/", "-", $path);
  $path = preg_replace("/\/+/", "/", $path);
    
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
    
    $href = "javascript:pl_open('{$path}')";
    
  } else if (substr($fm, 0, 5) == 'image') {
    $fmi = 'page_white_picture';
  }
  
  $li = ($href === null) ? $fn : "<a href=\"{$href}\">{$fn}</a>";
  $de = "<a href=\"javascript:_pl_del('{$path}');\" class='anoline'>[<b>x</b>]</a>";
  $li = "<div><img src='/app/hwc/static/img/{$fmi}.png' align='absmiddle' title='{$fm}' /> $li $de</div>";
  
  echo $li;
}


?>
<script type="text/javascript">
function _open_window(url, title, width, height, text) {
  var nwin;    
  if (url=='' || width=='' || height=='') {
    return false;
  }
  sWidth  = screen.availWidth;
  sHeight = screen.availHeight;
  var l = (screen.availWidth - width) / 2;
  var t = (screen.availHeight - height) / 2;    
  nwin = window.open(url, title, 'left='+ l +', top='+ t +', width='+ width +', height='+ height +',scrollbars=yes,resizable=yes');
  nwin.focus();
}
function _open_window_cb(path, file) {
  _hwc_dir(path, '');
  //pl_open(path+'/'+file);
}
function _pl_del(path) {
  $.ajax({
    type: "GET",
    url: '/hwc/appfiledel?path='+path,
    success: function(){
      _hwc_dir('<?=$appid?>','<?=$cpath?>');
      window.scrollTo(0,0);
    }
  });
}
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
