<?php

$selectedId = array_flip(explode(",", $value["VALUE"]));

if (!function_exists("rivetweb_recursive_category_options")) {
	function rivetweb_recursive_category_options($parentId, $iblockId, $selectedId, $level) { ?>
		<?php foreach (Entity::selectSection([
	  			"IBLOCK_ID" => $iblockId,
	  			"SECTION_ID" => $parentId,
	  		]) as $rowSect) { ?>
	    <option <?= isset($selectedId[$rowSect["ID"]])? "selected" : "" ?>
	    		value="<?= $rowSect["ID"] ?>"><?= str_repeat("-", $level) ?><?= $rowSect["NAME"] ?></option>

	    	<?php rivetweb_recursive_category_options($rowSect["ID"], $iblockId, $selectedId, $level + 1) ?>

	  <?php } ?>
	<?php }
}

?>

<select class="form-input-category-catalog-parent-<?= $field["CODE"] ?>"
		<?php if ($field["ROWS"] > 1) { ?>size="<?= $field["ROWS"] ?>"<?php } ?>
		<?= !empty($field["MULTI"])? "multiple" : "" ?>>
  <option value="">- Выберите категорию -</option>
	<option <?= isset($selectedId[$field["PARENT_ID"]])? "selected" : "" ?>
			value="<?= $field["PARENT_ID"] ?>">[<?= $field["NAME"] ?>]</option>

	<?php rivetweb_recursive_category_options($field["PARENT_ID"], $field["IBLOCK_ID"], $selectedId, 0) ?>

</select>
<input type="hidden" class="form-value-category-catalog-parent-<?= $field["CODE"] ?>" name="<?= $strHTMLControlName["VALUE"] ?>" value="<?= $value["VALUE"] ?>">

<script>
	jQuery(function ($) {
		$(".form-input-category-catalog-parent-<?= $field["CODE"] ?>").change(function () {
			var ids = $(this).val();
			var v = "";
			if (ids) {
				v = ids.join(",");
			}
			$(".form-value-category-catalog-parent-<?= $field["CODE"] ?>").val(v);
			console.log(v);
		});
	});
</script>

<?php /*
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
*/ ?>