<?php

if (!preg_match("#^(.*)view/(.*)\.html#i", $this->reqs->uri, $regs)) {
    return;
}

$this->reqs->id = $regs[2];


if (!isset($hdata_instance)) {
    return;
}
$session = user_session::getInstance();

hdata_entry::setInstance($hdata_instance);

$entry = hdata_entry::fetchEntry($this->reqs->id);

if (!isset($entry['id']) 
    || $entry['status'] == 0
    || ($entry['status'] > 1 && !user_session::isLogin($entry['uid']))) {
    print w_msg::simple('error', 'Page Not Found');
    return;
}

Hooto_Web_View::headStylesheet('/_w/css/cm.css');

Hooto_Registry::set('entry', $entry);

$taxon_cats = hdata_taxonomy::fetchTerms(hdata_entry::$metadata['taxonomy']['category']['id'], $entry['category']);

$entry['href'] = "#";

if (strlen($entry['tag']) > 0) {
    $entry['tag'] = explode(",", $entry['tag']);
} else {
    $entry['tag'] = array();
}
    
$entry['summary'] = Hooto_Util_Format::summaryPlainText($entry['summary'], 1000);
$entry['content'] = Hooto_Util_Format::textHtmlFilter($entry['content']);

if (user_session::isAllow($this->reqs->ins, 'entry.edit')) {
    $entry['href_edit']  = $this->siteurl("/edit?id={$entry['id']}");
}
if (user_session::isAllow($this->reqs->ins, 'entry.delete')) {
    $entry['href_delete']  = $this->siteurl("/delete?id={$entry['id']}");
}

if (isset($taxon_cats[$entry['category']])) {
    $entry['category_display'] = $taxon_cats[$entry['category']]['name'];
} else {
    $entry['category_display'] = $entry['category'];
}
$entry['href_category']  = $this->siteurl("/index?cat={$entry['category']}");

//$ip = Hooto_Util_Ip::getRemoteAddr();
if (true) {
    $set = array('id' => $entry['id'], 'stat_access' => $entry['stat_access'] + 1);
    hdata_entry::replaceEntry($set);
}

$this->headtitle = $entry['title'];
?>
<div class="entry-view">

  <div class="header">
    
    <h1 class="title"><?=$entry['title']?></h1>
    
    <?php
    if ($entry['summary_auto'] == 0) {
    ?>
    <table width="100%" class="info">
      <tr>
        <td valign="top">            
          <div><b>Summary:</b> <?=$entry['summary']?></div>
          <?php
          if (count($entry['tag']) > 0) {
          ?>
          <div>
            <img src="/_w/img/fffam/tag_blue.png"  align="absmiddle"/> Tags: 
            <?php
            foreach ((array)$entry['tag'] as $term) {
                echo "<span><a href='".$this->siteurl("/index?tag={$term}")."'>{$term}</a></span>";
            }?>
          </div>
        <?php } ?>
        </td>
        <td width="40%" valign="top">
          <table width="100%">
          <tr><td class="infotablekey">Created:</td><td><?=$entry['created']?></td></tr>
          <tr><td class="infotablekey">Categories:</td><td><a href="<?=$entry['href_category']?>"><?=$entry['category_display']?></a></td></tr>
          <tr><td class="infotablekey">Activity:</td><td><?=$entry['stat_access']?> views</td></tr>
          <tr><td class="infotablekey">Comments:</td><td><a href="#comment-view">View</a> , <a href="#comment-add">Add Comment</a></td></tr>
          <tr><td></td><td>
          <?php
          if (isset($entry['href_delete'])) {
            echo "<span><img src=\"/_w/img/fffam/page_white_delete.png\" align=\"absmiddle\" onclick=\"return confirm('Are you sure you want to delete?')\"/> <a href=\"{$entry['href_delete']}\">Delete</a></span>";
          }
          if (isset($entry['href_edit'])) {
            echo "<span><img src=\"/_w/img/fffam/page_white_edit.png\" align=\"absmiddle\"/> <a href=\"{$entry['href_edit']}\">Edit</a></span>";
          }
          ?>
          </td></tr>
          </table>
        </td>
    </table>
    <?php } else { ?>
    <div class="info">
        <span><img src="/_w/img/fffam/date.png" align="absmiddle"/> <?=$entry['created']?></span>
        <span><img src="/_w/img/fffam/chart_organisation.png" align="absmiddle"/> <a href="<?=$entry['href_category']?>"><?=$entry['category_display']?></a></span>
        <span><img src="/_w/img/fffam/folder_page.png" align="absmiddle"/> Views(<?=$entry['stat_access']?>)</span>
        <?php
        if (isset($entry['href_delete'])) {
            echo "<span><img src=\"/_w/img/fffam/page_white_delete.png\" align=\"absmiddle\" onclick=\"return confirm('Are you sure you want to delete?')\"/> <a href=\"{$entry['href_delete']}\">Delete</a></span>";
        }
        if (isset($entry['href_edit'])) {
            echo "<span><img src=\"/_w/img/fffam/page_white_edit.png\" align=\"absmiddle\"/> <a href=\"{$entry['href_edit']}\">Edit</a></span>";
        }
        ?>
    </div>
    <?php } ?>
  </div>
  <div class="content"><?=$entry['content']?></div>
  <?php
  if ($entry['summary_auto'] == 1 && count($entry['tag']) > 0) {
  ?>
  <div class="clear_both">
     <span class="term"><img src="/_w/img/fffam/tag_blue.png"  align="absmiddle"/> Tags: 
     <?php
     foreach ((array)$entry['tag'] as $term) {
        echo "&nbsp;&nbsp;<a href='".$this->siteurl("/index?tag={$term}")."'>{$term}</a>";
     }
     ?></span>
  </div>
  <?php } ?>
</div>

