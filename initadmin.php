<?php
// +-----------------------------------------------------------------------+
// | meta plugin for Piwigo                                                |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 ddtddt               http://temmii.com/piwigo/ |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
    die('Hacking attempt!');

//Add link menu
add_event_handler('get_admin_plugin_menu_links', 'meta_admin_menu');

function meta_admin_menu($menu) {
  $menu[] = array(
    'NAME' => l10n('Manage tag Metadata'),
    'URL' => META_ADMIN,
  );
  return $menu;
}

//add prefiltre photo
add_event_handler('loc_begin_admin', 'metaPadminf', 55);
add_event_handler('loc_begin_admin_page', 'metaPadminA', 55);

function metaPadminf() {
  global $template;
  $template->set_prefilter('picture_modify', 'metaPadminfT');
}

function metaPadminfT($content, &$smarty) {
  $search = '#<p style="margin:40px 0 0 0">#';
  $replacement = '
	<p>
      <strong>{\'Metadata - Plugin meta\'|@translate}</strong>
      <br>
	  <span style="margin: 0 0 0 20px">{\'meta_compimg\'|@translate}</span>
	  <br>
      <span style="margin: 0 0 0 20px"><textarea rows="2" cols="60" {if $useED==1}placeholder="{\'Use Extended Description tags...\'|@translate}"{/if} name="insermetaKP" id="insermetaKP" class="insermetaKP">{$metaCONTENT}</textarea></span>
	  <br>
	  <span style="margin: 0 0 0 20px">{\'meta_compimgdes\'|@translate}</span>
	  <br>
	  <span style="margin: 0 0 0 20px"><textarea rows="2" cols="60" {if $useED==1}placeholder="{\'Use Extended Description tags...\'|@translate}"{/if} name="insermetaDP" id="insermetaDP" class="insermetaDP">{$metaCONTENT2}</textarea>
	  ({\'meta_compcatdeshelp\'|@translate})</span>
	</p>  
<p style="margin:40px 0 0 0">';

  return preg_replace($search, $replacement, $content);
}

function metaPadminA() {
  if (isset($_GET['image_id'])){
	global $template, $prefixeTable;
	$PAED = pwg_db_fetch_assoc(pwg_query("SELECT state FROM " . PLUGINS_TABLE . " WHERE id = 'ExtendedDescription';"));
	if($PAED['state'] == 'active'){
	  $template->assign('useED',1);
    }else{
      $template->assign('useED',0);
    }
	$query = 'SELECT id,metaKeyimg,metadesimg FROM ' . meta_img_TABLE . ' WHERE id = ' . $_GET['image_id'] . ';';
	$result = pwg_query($query);
	$row = pwg_db_fetch_assoc($result);
	$template->assign(
	array(
	  'metaCONTENT' => $row['metaKeyimg'],
	  'metaCONTENT2' => $row['metadesimg'],
	));
  }
  if (isset($_POST['insermetaKP']) or isset($_POST['insermetaDP'])){
    $query = 'DELETE FROM ' . meta_img_TABLE . ' WHERE id = ' . $_GET['image_id'] . ';';
    $result = pwg_query($query);
    $q = 'INSERT INTO ' . $prefixeTable . 'meta_img(id,metaKeyimg,metadesimg)VALUES (' . $_GET['image_id'] . ',"' . $_POST['insermetaKP'] . '","' . $_POST['insermetaDP'] . '");';
    pwg_query($q);
	$template->assign(
	  array(
		'metaCONTENT' => $_POST['insermetaKP'],
		'metaCONTENT2' => $_POST['insermetaDP'],
	));
  }
}

//add prefiltre album
add_event_handler('loc_begin_admin', 'metaAadminf');
add_event_handler('loc_begin_admin_page', 'metaAadminA');

function metaAadminf() {
  global $template;
  $template->set_prefilter('album_properties', 'metaAadminfT');
}

function metaAadminfT($content, &$smarty){
  $search = '#<p style="margin:0">#';
  $replacement = '
	<p>
      <strong>{\'Metadata - Plugin meta\'|@translate}</strong>
      <br>
	  <span style="margin: 0 0 0 20px">{\'meta_compcat\'|@translate}</span>
	  <br>
      <span style="margin: 0 0 0 20px"><textarea rows="2" cols="60" {if $useED==1}placeholder="{\'Use Extended Description tags...\'|@translate}"{/if} name="insermetaKA" id="insermetaKA" class="insermetaKA">{$metaCONTENTA}</textarea></span>
	  <br>
	  <span style="margin: 0 0 0 20px">{\'meta_compcatdes\'|@translate}</span>
	  <br>
	  <span style="margin: 0 0 0 20px"><textarea rows="2" cols="60" {if $useED==1}placeholder="{\'Use Extended Description tags...\'|@translate}"{/if} name="insermetaDA" id="insermetaDA" class="insermetaDA">{$metaCONTENTA2}</textarea>
	  ({\'meta_compcatdeshelp\'|@translate})</span>
	</p>  
<p style="margin:0">
  ';
  return preg_replace($search, $replacement, $content);
}

function metaAadminA(){
  if (isset($_GET['cat_id'])) {
	global $template, $prefixeTable;
	$PAED = pwg_db_fetch_assoc(pwg_query("SELECT state FROM " . PLUGINS_TABLE . " WHERE id = 'ExtendedDescription';"));
	if($PAED['state'] == 'active'){
	  $template->assign('useED',1);
    }else{
      $template->assign('useED',0);
    }
	$query = 'SELECT id,metaKeycat,metadescat FROM ' . meta_cat_TABLE . ' WHERE id = ' . $_GET['cat_id'] . ';';
	$result = pwg_query($query);
	$row = pwg_db_fetch_assoc($result);
	$template->assign(
	  array(
		'metaCONTENTA' => $row['metaKeycat'],
		'metaCONTENTA2' => $row['metadescat'],
	));
  }

  if (isset($_POST['insermetaKA']) or isset($_POST['insermetaDA']) ) {
    $query = 'DELETE FROM ' . meta_cat_TABLE . ' WHERE id = ' . $_GET['cat_id'] . ';';
	$result = pwg_query($query);
	$q = 'INSERT INTO ' . $prefixeTable . 'meta_cat(id,metaKeycat,metadescat)VALUES (' . $_GET['cat_id'] . ',"' . $_POST['insermetaKA'] . '","' . $_POST['insermetaDA'] . '");';
	pwg_query($q);
	$template->assign(
	  array(
		'metaCONTENTA' => $_POST['insermetaKA'],
		'metaCONTENTA2' => $_POST['insermetaDA'],
	));
  }
}

?>