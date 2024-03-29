<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-Strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  
  <title><?php echo $this->headtitle; ?></title>
  
  <link rel="stylesheet" href="/_w/css/global.css" type="text/css" media="all" />
  <script src="/app/hwc/static/js/c.js"></script>
  <script src="/app/jquery16/jquery-1.6.min.js"></script>

  <link href="/app/codemirror2/lib/codemirror.css" rel="stylesheet" type="text/css" media="all" />
  <link href="/app/codemirror2/theme/default.css" rel="stylesheet" type="text/css" media="all" />
  <script src="/app/codemirror2/lib/codemirror.js"></script>
  <script src="/app/codemirror2/lib/runmode.js"></script>
  <script src="/app/codemirror2/lib/overlay.js"></script>
  <script src="/app/codemirror2/mode/xml/xml.js"></script>
  <script src="/app/codemirror2/mode/javascript/javascript.js"></script>
  <script src="/app/codemirror2/mode/css/css.js"></script>
  <script src="/app/codemirror2/mode/clike/clike.js"></script>
  <script src="/app/codemirror2/mode/php/php.js"></script>  
  
  <link rel="stylesheet" href="/app/hwc/static/css/c.css" type="text/css" media="all" />
  <?php
  echo $this->headlink.$this->headJavascript.$this->headStylesheet; 
  ?>
  <style>*{margin:0;padding:0;}</style>
</head>
<body>

<div id="hwc_header">
  <div class="logo_title">Web Creator</div>
  <div class="menu_nav">
    <span><a href="javascript:hwc_applist()">Applications</a></span>
    <span><a href="javascript:hwc_appcreate()">Create Application</a></span>
  </div>
</div>

<div id="hwc_body">

  <!-- layout-sidebar/ -->
  <div id="hwc_layout_sidebar">
  
  </div>
  <!-- /layout-sidebar -->
  
  <!-- layout-body/ -->
  <div id="hwc_layout_body" class="hwc_layout_rightbox">
    
    <!-- layout-layout-tabs/ -->
    <div id="hwc_layout_body_tabs" class="hwc_layout_tabs">
      <ul class="hwc_layout_tabs_item">
        <li id="ftabs_debug"></li>
      </ul>
      <div id="hwc_layout_tabs_data"></div>
    </div>
    <!-- /layout-layout-tabs -->
    
    <!-- layout-layout-workspace/ -->
    <div id="hwc_layout_workspace_html">
      
    </div>
    <div id="hwc_layout_workspace_coder">
      
    </div>
    <!-- /layout-layout-workspace -->
  
  </div>
  <!-- /layout-body -->
  
</div>

</body>
</html>
<script>

var pages = new Array();
var tabs_data = new Array();
var pagecurrent = 0;
var editor;
var workspace_width_init = 0;

function ws_resize() 
{
  workspace_width_current  = $('body').width() - $('#hwc_layout_sidebar').outerWidth(true) - 20;
  height = $('body').height() - $('#hwc_header').outerHeight(true) - 10;  
  
  height_workspace = height - $('#hwc_layout_body_tabs').outerHeight(true);
  
  /* if (workspace_width_current > workspace_width_init) {
    //$('#hwc_creator_workspace').width(workspace_width_current);
    $('.CodeMirror-scroll').width(workspace_width_current);
    workspace_width_init = workspace_width_current;
  }*/
  $('#hwc_creator_workspace').height(height_workspace);  
  $('.CodeMirror-scroll').height(height_workspace);
}

function ws_init()
{ 
  if (!document.getElementById("hwc_creator_workspace")) {
    s = '<table id="hwc_creator_workspace" cellspacing="0"> \
  <tr> \
    <td id="hwc_creator_workspace_editor" valign="top"></td> \
    <td width="2px"></td> \
    <td valign="top" id="hwc_creator_workspace_preview"> \
        <iframe id="hwc_creator_workspace_iframe" border="0" src="" scrolling="yes"></iframe> \
    </td> \
	</tr> \
</table>';
    $('#hwc_layout_workspace_coder').html(s);
  }
}

function _layout_workspace_switch(v)
{
  $("#hwc_layout_workspace_html").addClass('displaynone');
  $("#hwc_layout_workspace_coder").addClass('displaynone');  
  $("#hwc_layout_workspace_"+v).removeClass('displaynone');
}

function pl_open(path)
{
  ws_init();
  var hid = Math.abs(crc32(path));
  
  if (pagecurrent == hid) {
    return;
  }
  
  if (pages[hid] !== undefined) {
    pl_goto(path);
  } else {
    pl_load_goto(path);
  }
  
  _layout_workspace_switch('coder');
}

function pl_load_goto(path)
{
  var hid = Math.abs(crc32(path));
  
  entry = '<li id="pagetab'+hid+'"><a href="javascript:pl_open(\''+path+'\')">'+path+'</a><a href="javascript:pl_close(\''+path+'\')" class="close">[x]</a></li>';
  $(".hwc_layout_tabs_item").prepend(entry);
  _tab_aclass_switch('pagetab'+hid);

  //
  page = '<textarea id="code'+hid+'" name="code'+hid+'" class="displaynone"></textarea>';
  $("#hwc_creator_workspace_editor").prepend(page);
  
  var ext = path.split('.').pop();//path.substr(path.lastIndexOf('.') + 1);
  switch(ext)
  {
    case 'php':
    case 'css':
    case 'xml':
      mode = ext;
      break;
    case 'sql':
      mode = 'plsql';
      break;
    case 'js':
      mode = 'javascript';
      break;
    default:
      mode = 'htmlmixed';
  }

  //
  $.get('/hwc/appsrc/?path='+path, function(data) {
    
    $('#code'+hid).text(data);
    if (pagecurrent != 0) {
      editor.toTextArea();
    }
    
    //alert(data);
    pages[hid] = hid;
    pagecurrent = hid;
    //document.getElementById('ftabs_debug').textContent = "ID:" + pagecurrent;
    //alert(document.getElementById('code'+path).value());
    editor = CodeMirror.fromTextArea(document.getElementById('code'+hid), {
      lineNumbers: true,
      matchBrackets: true,
      mode: mode,
      indentUnit: 2,
      indentWithTabs: false,
      tabMode: "shift",
      // height: "dynamic",
      onChange: function() {
        pl_save(path);
        pl_preview(path);
      }
    });
    ws_resize();
  });
}

function pl_goto(path)
{
  var hid = Math.abs(crc32(path));
  
  if (pagecurrent == hid) {
    return;
  }
  
  _tab_aclass_switch('pagetab'+hid);
  
  if (pagecurrent != 0) {
    editor.toTextArea();
  }
  
  var ext = path.split('.').pop();//path.substr(path.lastIndexOf('.') + 1);
  switch(ext)
  {
    case 'php':
    case 'css':
    case 'xml':
      mode = ext;
      break;
    case 'sql':
      mode = 'plsql';
      break;
    case 'js':
      mode = 'javascript';
      break;
    default:
      mode = 'htmlmixed';
  }
  
  pages[hid] = hid;
  pagecurrent = hid;
  //document.getElementById('ftabs_debug').textContent = "ID:" + pagecurrent;
  editor = CodeMirror.fromTextArea(document.getElementById('code'+hid), {
    lineNumbers: true,
    matchBrackets: true,
    mode: mode,
    indentUnit: 2,
    indentWithTabs: false,
    tabMode: "shift",
    // height: "dynamic",
    onChange: function() {
      pl_save(path);
      pl_preview(path);
    }
  });
  
  ws_resize();
}

function pl_close(path)
{
  var hid = Math.abs(crc32(path));
  pl_save(path);
  
  $('#pagetab'+hid).remove();
 
  for (var i in pages) {
    if (pages[i] == hid) {
      pages.splice(i, 1);      
      break;
    }
  }
  
  if (hid != pagecurrent) {  
    $('#code'+hid).remove();    
    return;
  }
  
  editor.toTextArea();
  $('#code'+hid).remove();
  
  pagecurrent = 0;
  
  for (var i in pages) {
  
    pagecurrent = pages[i];
    //console.log("close try "+i);
    
    editor = CodeMirror.fromTextArea(document.getElementById('code'+pagecurrent), {
      lineNumbers: true,
      matchBrackets: true,
      mode: "application/x-httpd-php",
      indentUnit: 2,
      indentWithTabs: false,
      tabMode: "shift",
      onChange: function() {
        pl_save(path);
        pl_preview(path);
        _tab_aclass_switch('pagetab'+pagecurrent);
      }
    });

    break;
  }  
}

function pl_save(path)
{
  hid = Math.abs(crc32(path));
  if (hid != pagecurrent) {
    return;
  }
  $.ajax({
    url: "/hwc/appsrc/?path="+path,
    type: "POST",
    data: editor.getValue(),
    dataType: "text"
  });
}

function pl_preview(path)
{  
  $('#hwc_creator_workspace_iframe').attr('height', '100%');
  $('#hwc_creator_workspace_iframe').attr('src', '/hwc/appplpreview?path='+path);
}

function _tab_aclass_switch(id) {

  $(".hwc_layout_tabs_item li").each(function(){
    $(this).removeClass('current');
  });

  $("#"+id).addClass("current");
}

$(window).resize(function() {
  if (document.getElementById("hwc_creator_workspace")) {
    ws_resize();
  }
});

$(document).ready(function() {
  //ws_resize();
  //window.setInterval(ws_resize, 2000);   
});

hwc_applist();

</script>
