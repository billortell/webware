<?php

$this->headtitle = "Web Creator";

if ($this->reqs->path == null) {
  die('ERROR');
}
$p = preg_replace("/\/+/", "/", $this->reqs->path);
$ps   = explode("/", trim($p, "/"));
if (!isset($ps[0])) {
  die("ERROR");
}

$appid  = $ps[0];
if (!file_exists(SYS_ROOT."app/{$appid}")) {
  die('ERROR');
}

$p_nav = '';
if (count($ps) > 1) {
  $_path = strstr($p, strrchr($p, "/"), true);
  $p_nav = "<span>
    <img src='/app/hwc/static/img/arrow_undo.png' align='absmiddle' alt='{$_path}' /> 
    <a href=\"javascript:_hwc_dir('{$_path}')\">Back</a></span> ";
}

$base = SYS_ROOT."app/{$appid}/";
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
  <span><?=$p_nav?></span>
  <span>[<a class="anoline" href="javascript:_open_window('/hwc/appnewfile?path=<?=$p?>', 'New', '600', '300')">New File</a>]</span>
</div>
<?php

$dir  = '';
$file   = '';

$glob = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), SYS_ROOT."app/{$p}/*");

foreach (glob($glob) as $f) {

  //$fn = '';
  $fn = substr(strrchr($f, "/"), 1);
  $fm = mime_content_type($f);
  
  $fmi = 'page_white';
  $href = null;
  
  $path = "{$p}/$fn";
  $path = preg_replace("/\/+/", "/", $path);

  if ($fm == 'directory') {
    
    if ($fn == 'pagelet') {
      $fmi = 'layers';
    } else if ($fn == 'page') {
      $fmi = 'layout_content';
    } else {
      $fmi = 'folder';
    }
    
    $href = "javascript:_hwc_dir('{$p}/$fn')";
    
  } else if (substr($fm,0,4) == 'text') {

    if ($fm == 'text/x-php' || substr($f,-4) == '.php') {
      $fmi = 'page_white_php';
    }
    
    $href = "javascript:pl_open('{$path}')";
    
  } else if (substr($fm, 0, 5) == 'image') {
    $fmi = 'page_white_picture';
  }
  
  $li = ($href === null) ? $fn : "<a href=\"{$href}\">{$fn}</a>";
  $de = "<a href=\"javascript:_pl_del('{$path}');\" onclick=\"return confirm('Are you sure you want to delete?')\" class='anoline'>[<b>x</b>]</a>";
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

function _open_window_cb(p, file) {
  _hwc_dir(p);
  //pl_open(path+'/'+file);
}
function _pl_del(path) {
  $.ajax({
    type: "GET",
    url: '/hwc/appfiledel?path='+path,
    success: function(){
      _hwc_dir('<?=$p?>');
      window.scrollTo(0,0);
    }
  });
}
function _hwc_dir(path) {
  $.ajax({ 
    type: "GET",
    url: '/hwc/appsidepane?path='+path,
    data: '',
    success: function(data){
      $("#hwc_layout_sidebar").empty().append(data);
      window.scrollTo(0,0);
    }
  });
}
</script>
