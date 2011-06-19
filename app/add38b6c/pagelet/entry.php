<?php

if (!isset($hdata_instance)) {
    return;
}

hdata_entry::setInstance($hdata_instance);

$entry = hdata_entry::fetchEntry($this->reqs->id);

Hooto_Web_View::headStylesheet('/_w/css/cm.css');

if (!isset($entry['id'])) {
    return;
}

Hooto_Registry::set('entry', $entry);

$taxon_cats = hdata_taxonomy::fetchTerms(1, $entry['category']);

$entry['href'] = "#";
$entry['tag'] = explode(",", $entry['tag']);

$entry['summary'] = Hooto_Util_Format::textHtmlFilter($entry['summary']);
$entry['content'] = Hooto_Util_Format::textHtmlFilter($entry['content']);

if (user_session::isAllow($this->reqs->ins, 'entry.edit')) {
    $entry['href_edit']  = "{$this->reqs->urlins}/edit?id={$entry['id']}";
}
if (user_session::isAllow($this->reqs->ins, 'entry.delete')) {
    $entry['href_delete']  = "{$this->reqs->urlins}/delete?id={$entry['id']}";
}
?>
<div class="entry-view">
  <div class="header">
            
    <h1 class="title"><?=$entry['title']?></h1>
     <div class="info">
       <img src="/_w/img/fffam/date.png"/> <?=$entry['created']?>&nbsp;&nbsp;
       <img src="/_w/img/fffam/folder_page.png"/> Views(?)&nbsp;&nbsp;
       <img src="/_w/img/fffam/folder.png"/> <?=$taxon_cats[$entry['category']]['name']?>  
       <span>
        <?php
        if (isset($entry['href_edit'])) {
            echo " <a href=\"{$entry['href_edit']}\">Edit</a>";
        }
        if (isset($entry['href_delete'])) {
            echo " <a href=\"{$entry['href_delete']}\">Delete</a>";
        }
        ?>
      </span>
     </div>
   </div>
   <div class="content"><?=$entry['content']?></div>
   <div class="clear_both">
     <span class="term"><img src="/_w/img/fffam/tag_blue.png" title="Tags" />Tags:
     <?php foreach ((array)$entry['tag'] as $term): ?> 
     &nbsp;&nbsp;<a href="#<?=$term?>"><?=$term?></a>
     <?php endforeach; ?></span>
  </div>
</div>

