<?php

$this->headtitle = "WebWare Creator";

Hooto_Web_View::headStylesheet("/_w/css/creator.css");

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
    
Hooto_Web_View::headStylesheet("/_default/codemirror/theme/default.css");

$url = SYS_ROOT."app/hwc/pagelet/creator_demo.php";
$text = file_get_contents($url);

?>

<div id="hwc_creator_sidebar">
    <ul class="hwc_cfs_tabs">
        <li><a href="javascript:pageopen(1)">Page 1</a></li>
        <li><a href="javascript:pageopen(2)">Page 2</a></li>
        <li><a href="javascript:pageopen(3)">Page 3</a></li>
      </ul>
</div>

<div id="hwc_creator_content">

  <div class="hwc_cws_tabs">
    <ul class="hwc_cws_ftabsli">
        <li id="ftabs_debug"></li>
    </ul>
  </div>

  <table id="hwc_creator_workspace">
    
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
var pagecurrent = '0';

/*
var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
    lineNumbers: true,
    matchBrackets: true,
    mode: "application/x-httpd-php",
    indentUnit: 4,
    indentWithTabs: false,
    tabMode: "shift"
});
*/

function workspaceresize() 
{
    width_workspace  = $('body').width() - $('#hwc_creator_sidebar').width() - 30;
    height = $('body').height() - $('#hwc_header').height() - 15;  
    
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

function pageopen(id)
{    
    if (pages[id] !== undefined) {
        return;
    }
    
    if (pagecurrent > 0) {
        //pages[pagecurrent].toTextArea();
        pagecurrent = 0;
    }
    
    //
    entry = '<li id="pagetab'+id+'"><a href="javascript:pageswitch('+id+')">Page '+id+'</a><a href="javascript:pageclose('+id+')">[x]</a></li>';
    $(".hwc_cws_ftabsli").prepend(entry);
    
    //
    page = '<textarea id="code'+id+'" name="code'+id+'" class="displaynone"></textarea>';    
    $("#hwc_creator_workspace_editor").prepend(page);
    
    //
    $.get('/hwc/creator-source-get/', function(data) {
        $('#code'+id).text(data + id);
        pages[id] = CodeMirror.fromTextArea(document.getElementById('code'+id), {
        //pages[id] = CodeMirror.fromTextArea('code'+ id, {
            lineNumbers: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: false,
            tabMode: "shift",
            // height: "dynamic",
            onChange: function() {
                pagesave(id);
                pagepreview();
            }
        });
        workspaceresize();
    });
    
    pagecurrent = id;
    
    document.getElementById('ftabs_debug').textContent = "ID:" + pagecurrent;

}

function pagesave(id)
{    
    $.ajax({
        url: "/hwc/creator-source-put/",
        type: "POST",
        data: pages[id].getValue(),
        dataType: "text"
    });
}

function pageswitch(id)
{
    if (id == pagecurrent) {
        return;
    }

    //alert();
    //pages[pagecurrent].refresh();
    pages[pagecurrent].save();
}

function pageclose(id)
{    
    $('#pagetab'+id).remove();
    
    if (id == pagecurrent) {
        pages[id].toTextArea();
        //CodeMirror
        pages.splice(id, 1);
        $('#code'+id).remove();        
    } else {

        pages.splice(id, 1);
        $('#code'+id).remove();
        
        return;   
    }
    
    pagecurrent = 0;
    for (var i in pages) {
        //CodeMirror.fromTextArea(document.getElementById('code'+i), {
        //pages[i].setValue(document.getElementById('code'+i).value);
        
        pages[i] = CodeMirror.fromTextArea(document.getElementById('code'+i), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: false,
            tabMode: "shift",
            onChange: function() {
                pagesave(i);
                pagepreview();
            }
        });
        //pages[i].refresh();
        pagecurrent = i;
        break;
    }

    document.getElementById('ftabs_debug').textContent = "ID:" + pagecurrent;
}

function pagepreview()
{
    $('#hwc_creator_workspace_iframe').attr('height', '100%');
    $('#hwc_creator_workspace_iframe').attr('src', '/hwc/creator-demo/');
}

$(window).resize(function() {
    workspaceresize();
});

$(document).ready(function() {
    workspaceresize();
    //window.setInterval(workspaceresize, 2000);   
});
</script> 
