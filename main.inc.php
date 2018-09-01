<?php
/*
Plugin Name: meta
Version: 2.8.a
Description: Allows to add metadata
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=220
Author: ddtddt
Author URI: http://temmii.com/piwigo/
*/

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

global $prefixeTable, $page;

define('meta_DIR', basename(dirname(__FILE__)));
define('meta_PATH', PHPWG_PLUGINS_PATH . meta_DIR . '/');
define('meta_TABLE', $prefixeTable . 'meta');
define('meta_img_TABLE', $prefixeTable . 'meta_img');
define('meta_cat_TABLE', $prefixeTable . 'meta_cat');
if (!defined('METAPERSO_TABLE'))
  define('METAPERSO_TABLE', $prefixeTable . 'metaperso');
if (!defined('META_AP_TABLE'))
  define('META_AP_TABLE', $prefixeTable . 'meta_ap');
define('META_ADMIN',get_root_url().'admin.php?page=plugin-'.meta_DIR);

add_event_handler('loading_lang', 'meta_loading_lang');	  
function meta_loading_lang(){
  load_language('plugin.lang', meta_PATH);
}

// Plugin for admin
if (script_basename() == 'admin') {
    include_once(dirname(__FILE__) . '/initadmin.php');
}

//Gestion des meta dans le header
add_event_handler('loc_begin_page_header', 'Change_Meta', 20);
add_event_handler('loc_end_page_header', 'add_meta', 56);
add_event_handler('loc_end_page_header', 'add_metacat', 61);
add_event_handler('loc_end_page_header', 'add_metaimg', 71);
add_event_handler('loc_after_page_header', 'set_meta_back');

function Change_Meta(){
  global $template;
  $template->set_prefilter('header', 'upmata');
  $PAED = pwg_db_fetch_assoc(pwg_query("SELECT state FROM " . PLUGINS_TABLE . " WHERE id = 'ExtendedDescription';"));
  if ($PAED['state'] == 'active')
    add_event_handler('AP_render_content', 'get_user_language_desc');
}

function upmata($content, &$smarty){
  $search = '#<meta name="description" content=".*?">#';
  $replacement = '<meta name="description" content="{$PLUG_META}">';
  return preg_replace($search, $replacement, $content);
}

function add_meta(){
  global $template, $page, $meta_infos;
  $meta_infos = array();
  $meta_infos['author'] = $template->get_template_vars('INFO_AUTHOR');
  $meta_infos['related_tags'] = $template->get_template_vars('related_tags');
  $meta_infos['info'] = $template->get_template_vars('INFO_FILE');
  $meta_infos['title'] = $template->get_template_vars('PAGE_TITLE');

  $query = 'SELECT id,metaname,metaval FROM ' . meta_TABLE . ' WHERE metaname IN (\'author\', \'keywords\', \'Description\', \'robots\');';
  $result = pwg_query($query);
  $meta = array();
  while ($row = pwg_db_fetch_assoc($result)){
    $meta[$row['metaname']] = $row['metaval'];
    $metaED[$row['metaname']] = trigger_change('AP_render_content', $meta[$row['metaname']]);
  }

  // Authors
  if (!empty($meta_infos['author']) and ! empty($metaED['author'])){
    $template->assign('INFO_AUTHOR', $meta_infos['author'] . ' - ' . $metaED['author']);
  } elseif (!empty($metaED['author'])){
    $template->assign('INFO_AUTHOR', $metaED['author']);
  }

  // Keywords
  if (!empty($metaED['keywords'])){
    $template->append('related_tags', array('name' => $metaED['keywords']));
  }

  // Description
  if (!empty($meta_infos['title']) and ! empty($meta_infos['info']) and ! empty($metaED['Description'])) {
    $template->assign('PLUG_META', $meta_infos['title'] . ' - ' . $meta_infos['info'] . ', ' . $metaED['Description']);
  } elseif (!empty($meta_infos['title']) and ! empty($metaED['Description'])) {
    $template->assign('PLUG_META', $meta_infos['title'] . ' - ' . $metaED['Description']);
  } elseif (!empty($metaED['Description'])) {
    $template->assign('PLUG_META', $metaED['Description']);
  }

  // Robots
  if (!empty($meta['robots'])) {
    $template->append('head_elements', '<meta name="robots" content="' . $meta['robots'] . '">');
  }

  //Metaperso
  if (script_basename() !== 'admin') {
    $metapersos = pwg_query("SELECT * FROM " . METAPERSO_TABLE . ";");

    if (pwg_db_num_rows($metapersos)) {
      while ($metaperso = pwg_db_fetch_assoc($metapersos)) {
        $items = array(
          'METANAME' => $metaperso['metaname'],
          'METAVAL' => $metaperso['metaval'],
          'METATYPE' => $metaperso['metatype']
        );
        $template->append('metapersos', $items);
      }
    }

    $template->set_filename('PERSO_META', realpath(meta_PATH . 'persometa.tpl'));
    $template->append('head_elements', $template->parse('PERSO_META', true));
  }

  $MPC = pwg_db_fetch_assoc(pwg_query("SELECT state FROM " . PLUGINS_TABLE . " WHERE id = 'ContactForm';"));
  if ($MPC['state'] == 'active'){
    global $conf;
    if (isset($page['section']) and $page['section'] == 'contact' and isset($conf['contactmeta']) and strpos($conf['contactmeta'], ',') !== false){
      $metacontact = explode(',', $conf['contactmeta']);
      $metakeyED = trigger_change('AP_render_content', $metacontact[0]);
      $metadesED = trigger_change('AP_render_content', $metacontact[1]);
      if (!empty($metakeyED)){
        $template->append('related_tags', array('name' => $metakeyED));
      }
      if (!empty($metadesED)){
        $template->assign('PLUG_META', $metadesED);
      }
    }
  }

  $MAP = pwg_db_fetch_assoc(pwg_query("SELECT state FROM " . PLUGINS_TABLE . " WHERE id = 'AdditionalPages';"));
  if ($MAP['state'] == 'active') {
    if (!empty($page['additional_page']['id'])) {
      $lire = $page['additional_page']['id'];
      $query = 'SELECT id, metaKeyap, metadesap FROM ' . META_AP_TABLE . ' WHERE id = \'' . $lire . '\';';
	  $result = pwg_query($query);
	  $row = pwg_db_fetch_assoc($result);
	  $metaKeyapap = $row['metaKeyap'];
	  $metadesapap = $row['metadesap'];
	  $metaKeyapapED = trigger_change('AP_render_content', $metaKeyapap);
	  $metadesapED = trigger_change('AP_render_content', $metadesapap);
    }
	if (isset($page['section']) and $page['section'] == 'additional_page') {
	  if (!empty($metaKeyapapED)) {
		$template->append('related_tags', array('name' => $metaKeyapapED));
	  }
	  if (!empty($metadesapED)) {
		$template->assign('PLUG_META', $metadesapED);
	  }
	}
  }
}

function add_metacat() {
  global $template, $page, $meta_infos;
  if (!empty($page['category']['id'])) {
    $query = 'SELECT id,metaKeycat,metadescat FROM ' . meta_cat_TABLE . ' WHERE id = \'' . $page['category']['id'] . '\';';
	$result = pwg_query($query);
	$row = pwg_db_fetch_assoc($result);
	$albumKeyED = trigger_change('AP_render_content', $row['metaKeycat']);
	if (!empty($row['metaKeycat'])) {
	  $template->append('related_tags', array('name' => $albumKeyED));
	}
	$albumDesED = trigger_change('AP_render_content', $row['metadescat']);
	if (!empty($row['metadescat'])) {
	  $template->assign('PLUG_META', $albumDesED);
	}
  }
}

function add_metaimg(){
  global $template, $page, $meta_infos;
  if (!empty($page['image_id'])) {
    $query = 'SELECT id,metaKeyimg,metadesimg FROM ' . meta_img_TABLE . ' WHERE id = \'' . $page['image_id'] . '\';';
	$result = pwg_query($query);
	$row = pwg_db_fetch_assoc($result);
	$photoKeyED = trigger_change('AP_render_content', $row['metaKeyimg']);
	if (!empty($row['metaKeyimg'])) {
	  $template->append('related_tags', array('name' => $photoKeyED));
	}
	$photoDesED = trigger_change('AP_render_content', $row['metadesimg']);
	if (!empty($row['metadesimg'])) {
	  $template->assign('PLUG_META', $photoDesED);
	}else{
	  $meta_infosph = array();
	  $meta_infosph['title'] = $template->get_template_vars('PAGE_TITLE');
	  $meta_infosph['gt'] = $template->get_template_vars('GALLERY_TITLE');
	  $meta_infosph['descimg'] = $template->get_template_vars('COMMENT_IMG');
	  if (!empty($meta_infosph['descimg'])) {
		$template->assign('PLUG_META', strip_tags($meta_infosph['descimg']) . ' - ' . $meta_infosph['title']);
	  }else{
		$template->assign('PLUG_META', $meta_infosph['title'] . ' - ' . $meta_infosph['gt']);
	  }
	}
  }
}

function set_meta_back(){
  global $template, $meta_infos;
  $template->assign(
    array(
      'INFO_AUTHOR' => $meta_infos['author'],
      'related_tags' => $meta_infos['related_tags'],
      'INFO_FILE' => $meta_infos['info'],
    )
   );
}

?>