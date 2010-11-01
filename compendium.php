<?php

error_reporting(E_ALL);

$MODES = split(" ", "Glossary Feat Power Race Item Monster Ritual Skill Trap");

function compendium_url($mode, $id) {
  return sprintf("http://www.wizards.com/dndinsider/compendium/%s.aspx?id=%s", strtolower($mode), $id);
}

function search_url($mode, $query, $nameOnly = false) {
  return sprintf("http://www.wizards.com/dndinsider/compendium/CompendiumSearch.asmx/KeywordSearch?Keywords=%s&Tab=%s&NameOnly=%s", $query, $mode, ($nameOnly ? 'true' : 'false'));
}

if ($_POST) {
  $mode = $_POST["mode"];
  $q = $_POST["q"];
  $nameOnly = !empty($_POST["nameOnly"]);
  
  $xml_response = simplexml_load_file(search_url($mode, $q, $nameOnly));
  $results = ($xml_response->xpath("//Results/$mode"));
  $found_str = sprintf("%s ${mode}s found", count($results));

} else {
  
  $mode = $MODES[0];
  $q = $nameOnly = false;
  $found_str = $results = false;

}

?>

<form method=post>
  <input type="text" name="q" value="<?= $q ?>" />
  <select name="mode">
    <? foreach($MODES as $m): ?>
      <option <?= ($mode == $m) ? 'selected' : '' ?>>
        <?= $m ?>
      </option>
    <? endforeach; ?>
  </select>
  <input type="checkbox" name="nameOnly" id="nameOnly" value="1"
         <?= $nameOnly ? 'checked' : '' ?> />
  <label for="nameOnly">Name Only</label>
  <input type="submit" value="Search" />
</form>

<? if ($results) : ?>

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