<?php

$this->headtitle = "WebWare Creator";


Hooto_Web_View::headJavascript("/_default/jquery/js/jquery-1.6.1.min.js");
Hooto_Web_View::headJavascript("/_default/jqueryui/js/jquery-ui-1.8.13.custom.min.js");
Hooto_Web_View::headStylesheet("/_default/jqueryui/css/smoothness/jquery-ui-1.8.13.custom.css");

Hooto_Web_View::headStylesheet("/_default/codemirror/lib/codemirror.css");
Hooto_Web_View::headJavascript("/_default/codemirror/lib/codemirror.js");

//Hooto_Web_View::headJavascript("/_default/codemirror/lib/runmode.js");
//Hooto_Web_View::headJavascript("/_default/codemirror/lib/overlay.js");

Hooto_Web_View::headJavascript("/_default/codemirror/mode/xml/xml.js");
Hooto_Web_View::headJavascript("/_default/codemirror/mode/javascript/javascript.js");
Hooto_Web_View::headJavascript("/_default/codemirror/mode/css/css.js");
Hooto_Web_View::headJavascript("/_default/codemirror/mode/clike/clike.js");
Hooto_Web_View::headJavascript("/_default/codemirror/mode/php/php.js");

//Hooto_Web_View::headJavascript("/_hwc/js/c.js");
    
Hooto_Web_View::headStylesheet("/_default/codemirror/theme/default.css");

$url = SYS_ROOT."app/hwc/pagelet/creator_demo.php";
$text = file_get_contents($url);

?>

<div id="hwc_creator_sidebar">
    <ul class="hwc_cfs_tabs">
        <li><a href="javascript:pl_open(1)">Page 1</a></li>
        <li><a href="javascript:pl_open(2)">Page 2</a></li>
        <li><a href="javascript:pl_open(3)">Page 3</a></li>
        <li><a href="javascript:debug()">debug</a></li>
      </ul>
</div>

<div id="hwc_creator_content">

  <div class="hwc_cws_tabs">
    <ul class="hwc_cws_ftabsli">
        <li id="ftabs_debug"></li>
    </ul>
  </div>

  <table id="hwc_creator_workspace" cellspacing="0">
    
    <tr >
    
      <td id="hwc_creator_workspace_editor" valign="top">
      </td>
    
      <td width="2px"></td>
      
      <td valign="top" id="hwc_creator_workspace_preview">
        <iframe id="hwc_creator_workspace_iframe" border="0" src="" scrolling="yes"></iframe>
      </td>
	
	</tr>
  </table>

</div>

<script type="text/javascript">

var pages = new Array();
var pagecurrent = 0;
var editor;

function ws_resize() 
{
    width_workspace  = $('body').width() - $('#hwc_creator_sidebar').width() - 30;
    height = $('body').height() - $('#hwc_header').height() - 30;  
    
    height_workspace = height - $('.hwc_cws_tabs').height();
    
    $('#hwc_creator_sidebar').height(height);

    //$('.CodeMirror-scroll').height(height_workspace);
    $('#hwc_creator_workspace').width(width_workspace);
    $('#hwc_creator_workspace').height(height_workspace);
    
    //
    //$('#hwc_creator_workspace_iframe').height(height_workspace);
    //$('#hwc_creator_workspace_editor').height(height_workspace - 10);
    //$('#hwc_creator_workspace_iframe').css();
    //$('.CodeMirror').height(height_workspace);
    
    $('.CodeMirror-scroll').height(height_workspace);
}


function pl_open(id)
{
    if (pagecurrent == id) {
        return;
    }
    
    if (pages[id] !== undefined) {
        pl_goto(id);
    } else {
        pl_load_goto(id);
    }
}

function pl_load_goto(id)
{
    entry = '<li id="pagetab'+id+'"><a href="javascript:pl_open('+id+')">Page '+id+'</a><a href="javascript:pl_close('+id+')">[x]</a></li>';
    $(".hwc_cws_ftabsli").prepend(entry);
    
    //
    page = '<textarea id="code'+id+'" name="code'+id+'" class="displaynone"></textarea>';    
    $("#hwc_creator_workspace_editor").prepend(page);
    
    //
    $.get('/hwc/creator-source-get/?id='+id, function(data) {
        $('#code'+id).text(data);
        if (pagecurrent > 0) {
            editor.toTextArea();
        }
        pages[id] = id;
        pagecurrent = id;
        document.getElementById('ftabs_debug').textContent = "ID:" + pagecurrent;
        editor = CodeMirror.fromTextArea(document.getElementById('code'+id), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: false,
            tabMode: "shift",
            // height: "dynamic",
            onChange: function() {
                pl_save(id);
                pl_preview(id);
            }
        });
        ws_resize();
    });
}

function pl_goto(id)
{
    if (pagecurrent == id) {
        return;
    }
    
    if (pagecurrent > 0) {
        editor.toTextArea();
    }
   
    pages[id] = id;
    pagecurrent = id;
    document.getElementById('ftabs_debug').textContent = "ID:" + pagecurrent;
    editor = CodeMirror.fromTextArea(document.getElementById('code'+id), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: false,
        tabMode: "shift",
        // height: "dynamic",
        onChange: function() {
            pl_save(id);
            pl_preview(id);
        }
    });
    
    ws_resize();
}

function pl_close(id)
{
    pl_save(id);
    
    $('#pagetab'+id).remove();
 
    for (var i in pages) {
        if (pages[i] == id) {
            pages.splice(i, 1);            
            break;
        }
    }
    
    if (id != pagecurrent) {  
        $('#code'+id).remove();      
        return;
    }
    
    editor.toTextArea();
    $('#code'+id).remove();
    
    pagecurrent = 0;
    
    for (var i in pages) {
    
        pagecurrent = pages[i];
        
        editor = CodeMirror.fromTextArea(document.getElementById('code'+pagecurrent), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: false,
            tabMode: "shift",
            onChange: function() {
                pl_save(i);
                pl_preview(i);
            }
        });

        document.getElementById('ftabs_debug').textContent = "ID:" + pagecurrent;
        break;
    }    
}

function pl_save(id)
{    
    $.ajax({
        url: "/hwc/creator-source-put/?id="+id,
        type: "POST",
        data: editor.getValue(),
        dataType: "text"
    });
}

function pl_preview(id)
{
    $('#hwc_creator_workspace_iframe').attr('height', '100%');
    $('#hwc_creator_workspace_iframe').attr('src', '/hwc/creator-demo'+id);
}

$(window).resize(function() {
    ws_resize();
});

$(document).ready(function() {
    ws_resize();
    //window.setInterval(ws_resize, 2000);   
});

</script> 
