
<?php if ($field["ROWS"] > 1) { ?>
  <textarea name="<?= $strHTMLControlName["VALUE"] ?>"
    cols="<?= !empty($field["COLS"])? $field["COLS"] : $arProperty["COL_COUNT"] ?>"
    rows="<?= $field["ROWS"] ?>"><?= htmlspecialcharsex($value["VALUE"])?></textarea>
<?php } else { ?>
  <input name="<?= $strHTMLControlName["VALUE"] ?>" value="<?= htmlspecialcharsex($value["VALUE"]) ?>"
    size="<?= !empty($field["COLS"])? $field["COLS"] : $arProperty["COL_COUNT"] ?>" type="text">
<?php } ?>
