<?php

error_reporting(E_ALL);

## data

define('MODE_LIST', "Glossary Background Class Companion Deity EpicDestiny Feat Monster ParagonPath Power Race Ritual Skill Trap");

## functions

function get_mode_list($xml_response = false) {
  $mode_list = explode(" ", MODE_LIST);
  $mode_list = array_combine($mode_list, $mode_list);
  if ($xml_response) {
    $tab_results = ($xml_response->xpath("//Tab"));
    foreach($tab_results as $tab) {
      $mode = (string) $tab->Table;
      $count = (string) $tab->Total;
      $mode_list[$mode] = sprintf("%s (%s)", $mode, $count);
    }    
  }
  return $mode_list;
}

function compendium_url($mode, $id) {
  return sprintf("http://www.wizards.com/dndinsider/compendium/%s.aspx?id=%s", strtolower($mode), $id);
}

function search_url($mode, $query, $nameOnly = false) {
  return sprintf("http://www.wizards.com/dndinsider/compendium/CompendiumSearch.asmx/KeywordSearch?Keywords=%s&Tab=%s&NameOnly=%s", $query, $mode, ($nameOnly ? 'true' : 'false'));
}

function is_debug() {
  return !empty($_POST["debug"]) || !empty($_GET["debug"]);
}

## page flow

if ($_POST) {
  $mode = $_POST["mode"];
  $q = $_POST["q"];
  $nameOnly = !empty($_POST["nameOnly"]);
  
  $xml_response = simplexml_load_file(search_url($mode, $q, $nameOnly));

  $results = ($xml_response->xpath("//Results/$mode"));
  $mode_list = get_mode_list($xml_response);
  $found_str = sprintf("%s ${mode}s found", count($results));

} else {
  
  $mode = $q = $found_str = $results = false;
  $nameOnly = true;
  $mode_list = get_mode_list();

}

?>

<form method=post>
  <input type="text" name="q" value="<?= $q ?>" />
  <select name="mode">
    <? foreach($mode_list as $m_value => $m_label): ?>
      <option <?= ($mode == $m_value) ? 'selected' : '' ?> value="<?= $m_value ?>">
        <?= $m_label ?>
      </option>
    <? endforeach; ?>
  </select>
  <input type="checkbox" name="nameOnly" id="nameOnly" value="1"
         <?= $nameOnly ? 'checked' : '' ?> />
  <label for="nameOnly">Name Only</label>
  <input type="submit" value="Search" />
</form>

<? if ($results) : ?>

  <? if (is_debug()) : ?>
  <p>SearchURL: <?= search_url($mode, $q, $nameOnly) ?></p>
  <? endif; ?>

<hr />

<p><b><?= $found_str ?></b></p>

<ol>
  <? foreach($results as $r): ?>
    <li>
      <a href="<?= compendium_url($mode, $r->ID) ?>"><?= $r->Name ?></a>
    </li>
  <? endforeach; ?>
</ol>

<? endif; ?>

<hr />

Compendium Search Helper | <a href="about.php">About</a>