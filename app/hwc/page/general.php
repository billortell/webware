<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-Strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  
  <title><?php echo $this->headtitle; ?></title>
  
  <link rel="stylesheet" href="/_w/css/global.css" type="text/css" media="all" />
  <link rel="stylesheet" href="/app/hwc/static/css/c.css" type="text/css" media="all" />
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
  <?php
  echo $this->headlink.$this->headJavascript.$this->headStylesheet; 
  ?>
  <style>*{margin:0;padding:0;}</style>
</head>
<body>

<div id="hwc_header">
  <div class="logo_title">Web Creator</div>
  <div class="menu_nav">
    <span><a href="javascript:hwc_applist()">Home</a></span>
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
    
    <!-- layout-body-tabs/ -->
    <div id="hwc_layout_body_tabs" class="hwc_layout_tabs">
      <ul class="hwc_layout_tabsul">
        <li id="ftabs_debug"></li>
      </ul>
    </div>
    <!-- /layout-body-tabs -->
    
    <!-- layout-body-content/ -->
    <div id="hwc_layout_body_content">
      
    </div>
    <!-- /layout-body-content -->
  
  </div>
  <!-- /layout-body -->
  
</div>

</body>
</html>
<script>

var pages = new Array();
var pagecurrent = 0;
var editor;

function ws_resize() 
{
  width_workspace  = $('body').width() - $('#hwc_layout_sidebar').outerWidth(true) - 20;
  height = $('body').height() - $('#hwc_header').outerHeight(true) - 10;  
  
  height_workspace = height - $('#hwc_layout_body_tabs').outerHeight(true);
  
  //$('#hwc_creator_sidebar').height(height);

  //$('.CodeMirror-scroll').height(height_workspace);
  $('#hwc_creator_workspace').width(width_workspace);
  $('#hwc_creator_workspace').height(height_workspace);
  
  console.log('css.height:'+$('#hwc_header').css('height'));
  console.log('height:'+$('#hwc_header').height());
  console.log('outerHeight:'+$('#hwc_header').outerHeight());
  console.log('outerHeight(1):'+$('#hwc_header').outerHeight(true));
  
  //console.log('height: body:'+$('body').height()+', tabs:'+$('#hwc_layout_body_tabs').height()+', last:'+height_workspace);
  //
  //$('#hwc_creator_workspace_iframe').height(height_workspace);
  //$('#hwc_creator_workspace_editor').height(height_workspace - 10);
  //$('#hwc_creator_workspace_iframe').css();
  //$('.CodeMirror').height(height_workspace);
  
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
    $('#hwc_layout_body_content').html(s);
  }
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
}

function pl_load_goto(path)
{
  var hid = Math.abs(crc32(path));
  
  entry = '<li id="pagetab'+hid+'"><a href="javascript:pl_open(\''+path+'\')">'+path+'</a><a href="javascript:pl_close(\''+path+'\')">[x]</a></li>';
  $(".hwc_layout_tabsul").prepend(entry);

  //
  page = '<textarea id="code'+hid+'" name="code'+hid+'" class="displaynone"></textarea>';
  $("#hwc_creator_workspace_editor").prepend(page);
  
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
      mode: "application/x-httpd-php",
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
  
  if (pagecurrent != 0) {
    editor.toTextArea();
  }
   
  pages[hid] = hid;
  pagecurrent = hid;
  //document.getElementById('ftabs_debug').textContent = "ID:" + pagecurrent;
  editor = CodeMirror.fromTextArea(document.getElementById('code'+hid), {
    lineNumbers: true,
    matchBrackets: true,
    mode: "application/x-httpd-php",
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
      indentUnit: 4,
      indentWithTabs: false,
      tabMode: "shift",
      onChange: function() {
        pl_save(path);
        pl_preview(path);
      }
    });

    break;
  }  
}

function pl_save(path)
{
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
