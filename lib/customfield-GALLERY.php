<?php

namespace Rodzeta\Attribs;

\CModule::IncludeModule("fileman");
\CMedialib::Init();

$arCol = \CMedialibCollection::GetList(array(
  "arFilter" => array("ACTIVE" => "Y"),
  "arOrder" => array("NAME" => "ASC")
));
$galleryCollections = array();
foreach ($arCol as $item) {
  $galleryCollections[$item["ID"]] = $item;
}

$galleryCollections = Utils::buildTree($galleryCollections);
$optionsGallery = array(0 => " --- ");
Utils::printTree($galleryCollections, $optionsGallery);

?>

<select name="<?= $strHTMLControlName["VALUE"] ?>" class="form-input-gallery-img">
  <?php foreach ($optionsGallery as $optionValue => $optionName) { ?>
      <option <?= $value["VALUE"] == $optionValue? "selected" : "" ?>
        value="<?= $optionValue ?>"><?= $optionName ?></option>
  <?php } ?>
</select>
