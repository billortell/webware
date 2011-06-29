<?php

$session = user_session::getInstance();
if ($session->uid == "0") {
    print w_msg::simple('error', 'Access Denied');
    return;
}

$msg = '';

if (!isset($hdata_instance)) {
    return;
}
hdata_entry::setInstance($hdata_instance);

Hooto_Web_View::headStylesheet('/_w/css/cm.css');

$entry = new Hooto_Object($_REQUEST);

$entry->uid = $session->uid;
$entry->uname = $session->uname;
$entry->created = date("Y-m-d H:i:s");
$entry->updated = date("Y-m-d H:i:s");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (add38b6c_entry::isValid($entry, $msgstr)) {
        
        try {
        
            if (!isset($entry->id) || strlen($entry->id) < 1) {
                $entry->id = Core_Util_Uuid::create();
            }
            
            //print_r($entry);die();
            $entry->instance = $hdata_instance;
            
            hdata_entry::replaceEntry($entry);
            
            $msg = w_msg::simple('success', 'Success');
            
        } catch (Exception $e) {
        
            $msg = w_msg::simple('error', $e->getMessage());
            //return $this->editAction();
        }
        
    } else {
        $msg = w_msg::simple('error', $msgstr);
    } 

    $params = null;
    // -- $this->pagelet('sidebar', $params);
}


if (isset($this->reqs->id)) {

    $_entry = hdata_entry::fetchEntry($this->reqs->id);

    if (isset($_entry['id'])) {
        $entry = new Hooto_Object($_entry);
        if (empty($entry->summary)) {
            $entry->summary_auto = 1;
        }
    }
    
} else {

    $entry->summary_auto = 1;
}

$where = array('taxon' => 1, 'gid' => $session->uid);
$taxon_cats = hdata_taxonomy::fetchTerms($where);

if (isset($entry->content)) {
    $entry->content = Hooto_Util_Format::richEditFilter($entry->content);
}
//$db = Hooto_Data_Sql::getTable('taxonomy_term_user');
//$q = $db->select()->where('uid = ?', 1)->where('vid = ?', 1)->limit(1000);
//$cats = $db->query($q);
?>


<div class="navindex_title">
  <?php echo $entry->id ? 'Edit' : 'Add New';?> Post
</div>

<!-- TinyMCE/ -->
<script type="text/javascript" src="/_default/tinymce/tiny_mce.js"></script>
<script type="text/javascript" src="/_default/js/editor.js"></script>
<script type="text/javascript">

// init for media plugin
var current_media_plugin = 'content';

// init for rich editor
tinymceInitOptions['content_css'] = "/_default/js/editor.css";

tinyMCE.init(tinymceInitOptions);

//tinyMCE.execCommand("mceAddControl", false, 'content');
//tinyMCE.execCommand("mceAddControl", false, 'summary');


// insert medias

mediaplugin = {
  insert : function(text) {
    if (current_media_plugin == 'summary') {
      richEditor.go('summary', 'tinymce');
      tinyMCE.execCommand('mceInsertContent', false, text);
    } else {
      richEditor.go('content', 'tinymce');
      tinyMCE.execCommand('mceInsertContent', false, text);
    }
  }
}

</script>
<!-- /TinyMCE -->

<?php 
print $msg;
?>

<form id="nodeedit" name="nodeedit" action="<?=$this->reqs->base?>/<?=$this->reqs->ins?>/edit" method="post">

<input id="id" name="id" type="hidden" value="<?=$entry->id?>" />
<input id="uid" name="uid" type="hidden" value="<?=$entry->uid?>" />
<input id="uname" name="uname" type="hidden" value="<?=$entry->uname?>" />

<table width="100%" class="edit_frame" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td>
    <!-- node title -->
    <div class="entry_edit entry_edit_title">Title<font color="red">*</font></div>
    <div><input class="entry_edit_input_title" id="title" name="title" type="text" value="<?=$entry->title?>" /></div>

    <!-- node summary -->
    <div id="summary_auto_box" class="hideifnojs">
    
      <div class="entry_edit_left entry_edit_info">
        <div id="summary_auto_title" class="hideifnojs">
          <span class="entry_edit_title">Summary</span> 
          <span id="summary_media_box" class="hideifnojs"><a href="javascript:;"  onclick="current_media_plugin = 'summary'; openWindow('/media/manage-editorplugin/?target=summary', 'upload', '800', '700')">[Insert Images]</a></span>
        </div>
      </div>
      
      <div class="entry_edit_right entry_edit_info">
        <input type="checkbox" id="summary_auto" name="summary_auto" onchange="changeAutoSummary()" value="1" <?php if ($entry->summary_auto == 1) {echo 'checked';} ?> /> Auto Summary 
        <span id="summary_richeditor_ctrl" class="hideifnojs">
          <a href="javascript:;" onclick="richEditor.go('summary', 'tinymce');">[Visual]</a>
          <a href="javascript:;" onclick="richEditor.go('summary', 'html');">[HTML]</a>
        </span>
      </div>
      
      <div id="summary_auto_text" class="clear hideifnojs">
        <textarea style="width:100%" id="summary" name="summary" rows="10"><?=$entry->summary?></textarea>
      </div> 
               
    </div>
    
    <div class="clear"></div>
    
    <!-- node content -->
    <div class="entry_edit_left entry_edit_info">
      <span class="entry_edit_title">Content <font color="red">*</font></span> 
      <span id="content_media_box" class="hideifnojs"><a href="javascript:;"  onclick="current_media_plugin = 'content'; openWindow('/media/manage-editorplugin/?target=content', 'upload', '800', '700')">[Insert Images]</a></span>
    </div>
    
    <div class="entry_edit_right entry_edit_info">
      <div id="content_richeditor_ctrl" class="hideifnojs">
        <a href="javascript:;" onclick="richEditor.go('content', 'tinymce');">[Visual]</a>
        <a href="javascript:;" onclick="richEditor.go('content', 'html');">[HTML]</a>
      </div>
    </div> 
    
    <div class="clear">
      <textarea style="width:100%" id="content" name="content" rows="30"><?=$entry->content?></textarea>
    </div>
    

  </td>
  
  <td width="20px"></td>
  
  <td width="280px" valign="top">
  
    <fieldset class="edit_option_box">
    
      <legend class="edit_option_title">Publish</legend>
      
      <div class="edit_option_list">
      
        <table width="100%" border="0" cellpadding="5" cellspacing="0">
          
          <tr>
            <td align="right">Allow Comment</td>
            <td>
            <input type="checkbox" id="comment" name="comment" value="1" <?php if ($entry->comment == 1) {echo 'checked';} ?> />
            </td>
          </tr>
          
          <tr>
            <td align="right">Status</td>
            <td>
            <select id="status" name="status">
            <option value="1" <?php if ($entry->status == 1) {echo 'selected';}?>> Publish </option>
            <option value="2" <?php if ($entry->status == 2) {echo 'selected';}?>> Draft </option>
            <option value="3" <?php if ($entry->status == 3) {echo 'selected';}?>> Private </option>
            </select>
            </td>
          </tr>
          
        </table>
        
      </div>
        
      <div class="edit_button_commit"><input type="submit" name="Submit" class="input_button" value="Save" /></div>
    </fieldset>
      
    <fieldset class="edit_option_box">
      
      <legend class="edit_option_title">Category</legend>
      
      <div class="edit_option_list">
        <select id="category" name="category">
          <?php foreach ($taxon_cats as $item) { ?> 
          <option value="<?=$item['id']?>" <?php if ($item['id'] == $entry->category) { echo 'selected'; } ?>><?php echo str_repeat('...', $item['_level']).$item['name']?></option>
          <?php } ?>
        </select>
      </div>
      
    </fieldset>
    
    <fieldset class="edit_option_box">
      
      <legend class="edit_option_title">Tags</legend>
      
      <div class="edit_option_list">
        <input id="tag" name="tag" type="text" style="width:230px;" value="<?=$entry->tag?>"/> 
        <br/>Separate multiple tags with commas: <b>Cats, Pet food, Dogs</b>
      </div>
    
    </fieldset>
    
  </td>
</tr>
</table>
</form>

<script>

function changeAutoSummary() {
  
  if (document.getElementById('summary_auto').checked) {
    
    document.getElementById('summary_auto_title').className = 'hideifnojs';
    document.getElementById('summary_auto_text').className = 'clear hideifnojs';
    document.getElementById('summary_richeditor_ctrl').className = 'hideifnojs';

  } else {
  
    document.getElementById('summary_auto_title').className = '';
    document.getElementById('summary_auto_text').className = 'clear';
    document.getElementById('summary_richeditor_ctrl').className = '';
    
  }
}

function openWindow(url, title, width, height, text) {
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


/** content.summary */
document.getElementById('summary_auto_box').className = '';
changeAutoSummary();


/** content */
document.getElementById('content_media_box').className = '';
document.getElementById('content_richeditor_ctrl').className = '';
tinyMCE.execCommand("mceAddControl", false, 'content');

</script>
