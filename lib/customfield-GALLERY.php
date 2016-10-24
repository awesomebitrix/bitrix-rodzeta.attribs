<?php

$parentId = null;
if (!empty($value["VALUE"])) {
  $current = \rivetweb\entity\Media::selectSection(["ID" => $value["VALUE"]])->current();
  $parentId = $current["PARENT_ID"];
}
?>

<select class="form-input-gallery-img-parent">
  <option value="">- Выберите категорию -</option>

  <?php foreach (\rivetweb\entity\Media::selectSection(["PARENT_ID" => 1]) as $parent) { ?>
    <option <?= $parentId == $parent["ID"]? "selected" : "" ?> value="<?= $parent["ID"] ?>"><?= $parent["NAME"] ?></option>
  <?php } ?>

</select>

<select name="<?= $strHTMLControlName["VALUE"] ?>" class="form-input-gallery-img">
  <option value="">- Выберите галерею -</option>

  <?php if (!empty($parentId)) { ?>
    <?php foreach (\rivetweb\entity\Media::selectSection(["PARENT_ID" => $parentId]) as $item) { ?>
      <option <?= $value["VALUE"] == $item["ID"]? "selected" : "" ?> value="<?= $item["ID"] ?>"><?= $item["NAME"] ?></option>
    <?php } ?>
  <?php } ?>

</select>

<script>
  jQuery(function ($) {
    $(".form-input-gallery-img-parent").change(function () {
      $.post("/api/media/options/?id=" + $(this).val()).then(function (data) {
        var options = JSON.parse(data);
        var result = ["<option value=\"\">- Выберите галерею -</option>"];
        for (var i in options) {
          result.push("<option value=\"" + options[i].ID + "\">" + options[i].NAME + "</option>");
        }
        $(".form-input-gallery-img").html(result.join("\n"));
      });
    });
  });
</script>
