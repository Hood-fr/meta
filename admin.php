<?php
// +-----------------------------------------------------------------------+
// | meta plugin for Piwigo by TEMMII                                      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2023 ddtddt               http://temmii.com/piwigo/ |
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
global $template, $conf, $user, $pwg_loaded_plugins;
include_once(PHPWG_ROOT_PATH . 'admin/include/tabsheet.class.php');


// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

//-------------------------------------------------------- sections definitions
// TAB gest
$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : 'permissions';
if ('album' == $page['tab']){
	check_input_parameter('cat_id', $_GET, false, PATTERN_ID);
	$cat_id = $_GET['cat_id'];
	$page['tab'] = 'meta-album';
	$admin_album_base_url = get_root_url().'admin.php?page=album-'.$cat_id;
	$query = '
     SELECT *
	 FROM '.CATEGORIES_TABLE.'
	 WHERE id = '.$cat_id.'
	 ;';
	$category = pwg_db_fetch_assoc(pwg_query($query));
	if (!isset($category['id'])){
	 die("unknown album");
	}

	$tabsheet = new tabsheet();
	$tabsheet->set_id('album');
	$tabsheet->select('meta-album');
	$tabsheet->assign();
    $template->assign(
      'metagestalbum', array(
      'A' => 'A'
    ));
 if (isset($_GET['cat_id'])) {
	global $template, $prefixeTable, $pwg_loaded_plugins;
	if (isset($pwg_loaded_plugins['ExtendedDescription'])){
	  $template->assign('useED',1);
	}else{
	  $template->assign('useED',0);
	}
	$query = 'SELECT id,metaKeycat,metadescat FROM ' . meta_cat_TABLE . ' WHERE id = ' . $_GET['cat_id'] . ';';
	$result = pwg_query($query);
	$row = pwg_db_fetch_assoc($result);
	if(!isset($row['metaKeycat'])){$row['metaKeycat']="";};
	if(!isset($row['metadescat'])){$row['metadescat']="";};
	$template->assign(
	  array(
		'metaCONTENTA' => $row['metaKeycat'],
		'metaCONTENTA2' => $row['metadescat'],
	));
  }
  if (isset($_POST['submitmetaalbum'])){
	$query = 'DELETE FROM ' . meta_cat_TABLE . ' WHERE id = ' . $_GET['cat_id'] . ';';
	$result = pwg_query($query);
	$q = 'INSERT INTO ' . $prefixeTable . 'meta_cat(id,metaKeycat,metadescat)VALUES (' . $_GET['cat_id'] . ',"' . $_POST['insermetaKA'] . '","' . $_POST['insermetaDA'] . '");';
	pwg_query($q);
	$template->assign(
	  array(
		'metaCONTENTA' => $_POST['insermetaKA'],
		'metaCONTENTA2' => $_POST['insermetaDA'],
	));
	$page['infos'][] = l10n('Metadata updated');
  }
   
}else{
if (!isset($_GET['tab']))
    $page['tab'] = 'gestion';
else
  $page['tab'] = $_GET['tab'];
  
  $tabsheet = new tabsheet();
  $tabsheet->add('gestion', l10n('meta_onglet_gestion'), META_ADMIN . '-gestion');
  $tabsheet->add('persometa', l10n('Personal Metadata'), META_ADMIN . '-persometa');
  if (isset($pwg_loaded_plugins['ContactForm'])){
	$tabsheet->add('contactmeta', l10n('Contact page Metadata'), META_ADMIN . '-contactmeta');
  }
  if (isset($pwg_loaded_plugins['AdditionalPages'])){
	$tabsheet->add('AdditionalPagesmeta', l10n('Additional Pages Metadata'), META_ADMIN . '-AdditionalPagesmeta');
  }
  $tabsheet->add('description', l10n('meta_onglet_description'), META_ADMIN . '-description');
  $tabsheet->select($page['tab']);
  $tabsheet->assign();
}
switch ($page['tab']) {
  case 'gestion':
    $groups = array();
    $query = 'SELECT id,metaname FROM ' . meta_TABLE . ' ORDER BY metaname ASC;';
    $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result)){
      $groups[$row['id']] = $row['metaname'];
    }
    $selected = 0;
    $options[] = l10n('meta_select2');
    $options['a'] = '----------------------';
    foreach ($groups as $metalist => $metalist2) {
      $options[$metalist] = $metalist2;
    }
    $template->assign(
      'gestionA', array(
      'OPTIONS' => $options,
      'SELECTED' => $selected
    ));

    if (isset($_POST['submitchoixmeta']) and is_numeric($_POST['metalist']) and ( !$_POST['metalist']) == 0){
      $lire = $_POST['metalist'];
      $query = 'SELECT id,metaname,metaval FROM ' . meta_TABLE . ' WHERE id = \'' . $lire . '\';';
      $result = pwg_query($query);
      $row = pwg_db_fetch_assoc($result);
      $template->assign(
		'meta_edit', array(
		'VALUE' => $row['metaname'],
		'CONTENT' => $row['metaval'],
		'SELECTED' => ""
	  ));
    }

    if (isset($_POST['submitinsmeta'])){
      $query = 'UPDATE ' . meta_TABLE . ' SET metaval= \'' . $_POST['inser'] . '\' WHERE metaname = \'' . $_POST['invisible'] . '\';';
      $result = pwg_query($query);
      array_push($page['infos'], l10n('Metadata updated'));
    }
  break;

  case 'description':
    $template->assign(
      'description', array(
        'meta' => l10n('meta_name'),
    ));
  break;

  case 'persometa':
	$template->assign(
	  'metapersoT', array(
	  'meta' => l10n('meta_name'),
	));
    $admin_base_url = META_ADMIN . '-persometa';
    $metapersos = pwg_query("SELECT * FROM " . METAPERSO_TABLE . ";");
    if (pwg_db_num_rows($metapersos)) {
	  while ($metaperso = pwg_db_fetch_assoc($metapersos)){
		$items = array(
		  'METANAME' => $metaperso['metaname'],
		  'METAVAL' => $metaperso['metaval'],
		  'METATYPE' => $metaperso['metatype'],
		  'U_DELETE' => $admin_base_url . '&amp;delete=' . $metaperso['id'],
		  'U_EDIT' => $admin_base_url . '&amp;edit=' . $metaperso['id'],
		);
        $template->append('metapersos', $items);
      }
    }
	if (isset($_POST['submitaddpersonalmeta'])) {
	  $template->assign(
		'meta_edit2', array(
		  'METANAME' =>'',
		  'METAVAL' =>'',
		  'METATYPE' =>'',
		  'meta' => l10n('meta_name'),
		  'METAID' => 0,
	  ));
	}

    if (isset($_POST['submitaddmetaperso'])) {
      $query = 'DELETE FROM ' . METAPERSO_TABLE . ' WHERE id = ' . $_POST['invisibleID'] . ';';
      $result = pwg_query($query);
      $q = 'INSERT INTO ' . $prefixeTable . 'metaperso(metaname,metaval,metatype)VALUES ("' . $_POST['insername'] . '","' . $_POST['inserval'] . '","' . $_POST['insertype'] . '");';
      pwg_query($q);
      $_SESSION['page_infos'] = array(l10n('Personal metadata update'));
      redirect($admin_base_url);
    }

    if (isset($_GET['edit'])) {
      check_input_parameter('edit', $_GET, false, PATTERN_ID);
	  $query = 'SELECT id,metaname,metaval,metatype FROM ' . METAPERSO_TABLE . ' WHERE id = \'' . $_GET['edit'] . '\';';
	  $result = pwg_query($query);
      $row = pwg_db_fetch_assoc($result);
	  $template->assign(
		'meta_edit2', array(
		  'METAID' => $row['id'],
		  'METANAME' => $row['metaname'],
		  'METAVAL' => $row['metaval'],
		  'METATYPE' => $row['metatype'],
	  ));
    }

    if (isset($_GET['delete'])) {
      check_input_parameter('delete', $_GET, false, PATTERN_ID);
      $query = 'DELETE FROM ' . METAPERSO_TABLE . ' WHERE id = ' . $_GET['delete'] . ';';
      pwg_query($query);
	  $_SESSION['page_infos'] = array(l10n('Personal metadata update'));
	  redirect($admin_base_url);
    }
  break;

  case 'contactmeta':
    if (empty($conf['contactmeta'])){
      $conf['contactmeta'] = ',';
    }
    $metacontact = explode(',', $conf['contactmeta']);
    $template->assign('contactmetaT', array('CMKEY' => $metacontact[0], 'CMDESC' => $metacontact[1],));
	if (isset($_POST['submitcm'])){
	  $INSCM = $_POST['inser'] . "," . $_POST['inser2'];
	  conf_update_param('contactmeta', $INSCM);
	  array_push($page['infos'], l10n('Metadata updated'));
	  $template->assign('contactmetaT', array('CMKEY' => stripslashes($_POST['inser']), 'CMDESC' => stripslashes($_POST['inser2'])));
	}
  break;

  case 'AdditionalPagesmeta':
	if (!defined('TITLE_AP_TABLE'))
	  define('TITLE_AP_TABLE', $prefixeTable . 'title_ap');
	$groups = array();
	$query = 'SELECT id,title FROM ' . ADD_PAGES_TABLE . ' ORDER BY id ASC;';
    $result = pwg_query($query);
	while ($row = pwg_db_fetch_assoc($result)) {
	  $groups[$row['id']] = $row['id'] . ' : ' . $row['title'];
	}
    $selected = 0;
    $options[] = l10n('Choose it page');
    $options['a'] = '----------------------';
	foreach ($groups as $listid => $listid2) {
	  $options[$listid] = $listid2;
	}
	$template->assign(
	  'gestionC', array(
		'OPTIONS' => $options,
		'SELECTED' => $selected
	));

    if (isset($_POST['submitchoixAP'])and is_numeric($_POST['APchoix']) and ( !$_POST['APchoix']) == 0){
	  $lire = $_POST['APchoix'];
	  $query = 'SELECT id,metaKeyap,metadesap FROM ' . META_AP_TABLE . ' WHERE id = \'' . $lire . '\';';
	  $result = pwg_query($query);
	  $row = pwg_db_fetch_assoc($result);
	  $metaKeyapap = $row['metaKeyap'];
	  $metadesap = $row['metadesap'];
	  $query = 'SELECT id,title FROM ' . ADD_PAGES_TABLE . ' WHERE id = \'' . $lire . '\';';
	  $result = pwg_query($query);
	  $row = pwg_db_fetch_assoc($result);
	  $idap = $row['id'];
	  $nameap = $row['title'];
	  $template->assign(
		'ap_edit', array(
		  'VALUE' => $idap,
		  'VALUEN' => $nameap,
		  'CONTENTMKAP' => $metaKeyapap,
		  'CONTENTMDAP' => $metadesap,
		  'SELECTED' => 0
	  ));
    }

    if (isset($_POST['submitinsapm'])) {
      $query = 'DELETE FROM ' . META_AP_TABLE . ' WHERE id = \'' . $_POST['invisible'] . '\';';
      $result = pwg_query($query);
      $q = 'INSERT INTO ' . $prefixeTable . 'meta_ap(id,metaKeyap,metadesap)VALUES (' . $_POST['invisible'] . ',"' . $_POST['inser'] . '","' . $_POST['inser2'] . '");';
      pwg_query($q);
      array_push($page['infos'], l10n('Metadata updated'));
    }
  break;
}

$template->set_filenames(array('plugin_admin_content' => dirname(__FILE__) . '/admin.tpl'));
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
?>