<?php
if (!isset($this->reqs->uname)) {
    return;
}
$uid = uname2uid($this->reqs->uname);

$where = array('taxon' => hdata_entry::$metadata['taxonomy']['category']['id'], 'gid' => $uid);
$taxon_cats = hdata_taxonomy::fetchTerms($where);

?>
<div class="sidebarlet">
  <h4>Categories</h4>
  <ul>
    <?php
    foreach ($taxon_cats as $key => $val) {
    $style = '';
    if ($this->reqs->cat == $val['id']) {
        $style = "class='current'";
    }
    $link = $this->siteurl("/index?cat={$val['id']}", $this->reqs->ins);
    ?>
    <li style="padding-left: <?php echo 20 * $val['_level']?>px">
      <a href="<?=$link?>" <?=$style?>><b><?=$val['name']?></b></a>
    </li>
    
    <?php } ?>
  </ul>
  <span class="cusp01"><span class="cusp022"></span></span>
</div>

