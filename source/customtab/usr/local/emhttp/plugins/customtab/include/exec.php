<?PHP

function read_json_file($file) {
  $json = json_decode(@file_get_contents($file),true);
  if ( ! $json ) {
    $json = array();
  }
  return $json;
}

function create_tab_settings($index,$settings,$addFlag = false) {
  if ( ! $settings['tabURL'] && ! $addFlag ) {
    return;
  }
  $o = "<strong><font size='2'>Custom Tab Settings #$index</font></strong>&nbsp;&nbsp;<img style='cursor:pointer;' src='/plugins/customtab/images/delete.png' width='30px' onclick='deleteTab($index);'><br>";
  $o .= "<dd>";
  $o .= "<dt>Tab Name:</dt>";
  $o .= "<dl><input type='text' id='customtabname$index' class='narrow setting' maxlength='20' value='{$settings['name']}' placeholder='Custom$index'></dl>";
  $o .= "<dt>Full Tab Name:</dt>";
  $o .= "<dl><input type='text' id='fullname$index' class='narrow setting' maxlength='80' value='{$settings['fullname']}'></dl>";
  $o .= "<dt>URL:</dt>";
  $o .= "<dl><input type='text' id='url$index' class='setting url' value='{$settings['tabURL']}'></dl>";
  $o .= "<dt>Width:</dt>";
  $o .= "<dl><input type='number' id='width$index' class='narrow setting' value='{$settings['width']}' placeholder='1280'></dl>";
  $o .= "<dt>Height:</dt>";
  $o .= "<dl><input type='number' id='height$index' class='narrow setting' value='{$settings['height']}' placeholder='500'></dl>";
  $o .= "</dd>";
  $o .= "<hr>";
  
  return $o;
}

function make_tabs($settings,$flag = false) {
  $index = 0;
  foreach ($settings as $tab) {
    $set = $flag ? $tab : tabArray($tab);
    $o .= create_tab_settings($index,$set);
    $index++;
  }
  return $o;
}

function tabArray($tab) {
  $set['name'] = $tab[0];
  $set['fullname'] = $tab[1];
  $set['tabURL'] = $tab[2];
  $set['width'] = $tab[3];
  $set['height'] = $tab[4];
  return $set;
}
function disableAddTab() {
  return "<script>$('#newtab').prop('disabled',true);</script>";
}
function enableAddTab() {
  return "<script>$('#newtab').prop('disabled',false);</script>";
}

switch ($_POST['action']) {
  case 'get_tabs_init':
    $o = make_tabs(read_json_file("/boot/config/plugins/customtab/customtab.json"),true);
    echo $o;
    break;
  case 'add_tab':
    $settings = json_decode($_POST['settings']);
    $index = 0;
    foreach ($settings as $tab) {
      $set = tabArray($tab);
      if ( ! $set['tabURL'] ) {
        continue;
      }
      $o .= create_tab_settings($index,$set);
      $index++;
    }
    $o .= create_tab_settings($index,array(),true);
    $o .= disableAddTab();
    echo $o;
    break;
  case 'show_tabs':
    $settings = json_decode($_POST['settings']);
    $o = make_tabs($settings);
    $o .= enableAddTab();
    if ( ! $o ) { $o = " "; }
    echo $o;
    break;
  case 'apply':
    $settings = json_decode($_POST['settings']);
    foreach ($settings as $set) {
      $tmp = tabArray($set);
      if ( ! $tmp['tabURL'] ) {
        continue;
      }
      $newSettings[] = $tmp;
    }
    file_put_contents("/boot/config/plugins/customtab/customtab.json",json_encode($newSettings,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    exec("/usr/local/emhttp/plugins/customtab/scripts/createTab.php");
    echo "ok";
    break;
}

?>