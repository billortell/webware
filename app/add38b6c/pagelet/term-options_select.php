<?php
//print_r($this->reqs);

if (!isset($hdata_instance)) {
    return;
}
hdata_entry::setInstance($hdata_instance);

Hooto_Web_View::headStylesheet('/_w/css/cm.css');


$uid = uname2uid($this->reqs->uname);

$where = array('taxon' => hdata_entry::$metadata['taxonomy']['category']['id'], 'gid' => $uid);
$feed = hdata_taxonomy::fetchTerms($where);

?>

<table class="contentbox item_list" width="100%" cellspacing="0">

  <?php
  if (user_session::isLogin($uid)) {
  ?>
  <thead id="table-thead">
    <th></th>
    <th><b>Weight</b></th>
    <th width="120px"><b>Operations</b></th>
  </thead>

  <tfoot id="table-tfoot">
    <th></th>
    <th><b>Weight</b></th>
    <th><b>Operations</b></th>
  </tfoot>
  <?php } ?>
  <tbody>
  <?php
  $even = 'Even';
  foreach ($feed as $val) { 
    if (user_session::isLogin($uid)) {
    $even = ($even == 'Even') ? 'Odd' : 'Even';
    $draggAble = 'draggAble'.$even;
    }
  ?>
  <tr id="term-<?=$val['id']?>" class="<?php echo $draggAble;?>">
   	<td style="padding-left: <?php echo 20 * $val['_level'] + 5;?>px;">
      <a href="<?=$this->siteurl("/index?cat={$val['id']}")?>">
        <b><?php echo $val['name']?></b>
      </a>
    </td>
    <?php
    if (user_session::isLogin($uid)) {
    ?>
    <td style="padding-left: <?php echo 20 * $val['_level'];?>px;"><b><?php echo $val['weight']?></b></td>
    <td width="120px">
    
        <a href="<?=$this->siteurl("/term-category-edit/?id={$val['id']}")?>">Edit</a>
        <a href="<?=$this->siteurl("/term-category-del/?id={$val['id']}")?>" onclick="return confirm('Are you sure you want to delete?')">Delete</a>
    <?php } ?>
    </td>
  </tr> 
  <?php } ?>
  </tbody>
</table>

<div class="clearhr"></div>
<?php
if (user_session::isLogin($uid)) {
?>
<a class="abutton" href="<?=$this->siteurl("/term-category-edit/?id=0")?>">New Term</a>
<?php } ?>
