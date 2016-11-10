<?php
/*******************************************************************************
 * rodzeta.attribs - Infoblock element attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Attribs;

use \Bitrix\Main\Application;
use \Bitrix\Main\Config\Option;

// see /bitrix/components/bitrix/system.field.view/templates/string

final class Attribsfield {

  static function GetUserTypeDescription() {
    return [
      "PROPERTY_TYPE" => "S",
      "USER_TYPE"	=> "Attribsfield",
      "DESCRIPTION" => "Rodzeta - Простые атрибуты",
      "BASE_TYPE" => "string",
		  "GetPropertyFieldHtml" => [__CLASS__, "GetPropertyFieldHtml"],
    ];
  }

  static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) {
  	static $first = true;
  	if ($first) {
  		// FIX for hide button "add fields"
  		$first = false;
  		?>
	  		<script>
					BX.ready(function () {
						var $block = document.getElementById("tr_PROPERTY_<?= $arProperty["ID"] ?>");
						$block.querySelector("input[value=\"Добавить\"]").style.display = "none";
	          $block.querySelector("table.nopadding").style.display = "none";
					});
				</script>
  		<?php
  	}
  	?>

    <div class="admin-form-fields"
        style="padding-bottom:16px;display:table;">

      <div class="admin-form-field-label" style="vertical-align:top;display:table-cell;">
        <input name="<?= $strHTMLControlName["DESCRIPTION"] ?>"
            id="<?= $strHTMLControlName["DESCRIPTION"] ?>"
        		value="<?= htmlspecialcharsex($value["DESCRIPTION"]) ?>"
        		size="12"
            placeholder="код атрибута"
            type="text">
      </div>

      <div class="admin-form-field-value" style="padding-left:6px;vertical-align:top;display:table-cell;">

        <?php if (substr($value["DESCRIPTION"], -5) == "_TEXT") { ?>

          <textarea name="<?= $strHTMLControlName["VALUE"] ?>"
            style="width:280px;height:100px;"
            placeholder="значение"><?= htmlspecialcharsex($value["VALUE"]) ?></textarea>

        <?php } else if (substr($value["DESCRIPTION"], -5) == "_HTML") { ?>

          <?php include __DIR__ . "/customfield-HTML.php" ?>

        <?php } else { ?>

          <input name="<?= $strHTMLControlName["VALUE"] ?>"
            placeholder="значение"
            value="<?= htmlspecialcharsex($value["VALUE"]) ?>"
    		    size="<?= $arProperty["COL_COUNT"] ?>"
            type="text">

        <?php } ?>

      </div>

    </div>

		<?php
  }

}
