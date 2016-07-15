<?php
/***********************************************************************************************
 * rodzeta.attribs - Infoblock element attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Attribs;

use \Bitrix\Main\Application;
use \Bitrix\Main\Config\Option;

// see /bitrix/components/bitrix/system.field.view/templates/string

final class Fieldtext {

  function GetUserTypeDescription() {
    return array(
      "PROPERTY_TYPE" => "S",
      "USER_TYPE"	=> "Fieldtext",
      "DESCRIPTION" => "Rodzeta - Атрибуты элемента",
      "BASE_TYPE" => "string",
		  "GetPropertyFieldHtml" => array("\Rodzeta\Attribs\Fieldtext", "GetPropertyFieldHtml"),

    	// optional handlers
      /*
      "CheckFields"	=> array("CUserTypeString","CheckFields"),
      "GetLength"	=> array("CUserTypeString","GetLength"),
      "ConvertToDB"	=> array("CUserTypeString","ConvertToDB"),
      "ConvertFromDB"	=> array("CUserTypeString","ConvertFromDB"),
      "GetAdminListViewHTML" => array("CUserTypeString","GetAdminListViewHTML"),
      "GetPublicViewHTML"	=> array("CUserTypeString","GetPublicViewHTML"),
      "GetPublicEditHTML"	=> array("CUserTypeString","GetPublicEditHTML"),
      */
    );
  }

  static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) {
  	static $first = true;
  	if ($first) {
  		// FIX for hide button "add fields"
  		$first = false;
  		$propertyId = Option::get("rodzeta.attribs", "property_id", 2);
  		?>
	  		<script>
					BX.ready(function () {
						var $block = document.getElementById("tr_PROPERTY_<?= $propertyId ?>");
						$block.querySelector("input[value=\"Добавить\"]").style.display = "none";
	          $block.querySelector("table.nopadding").style.display = "none";
					});
				</script>
  		<?php
  	}
  	list($attribs) = \Rodzeta\Attribs\Utils::get();
  	$field = $attribs[$value["DESCRIPTION"]];
  	$title = "";
  	if (!empty($field["NAME"])) {
  		$title = $field["NAME"];
  	}
  	if (!empty($field["UNIT"])) {
  		$title .= ", " . $field["UNIT"];
  	}

  	?>

    <input name="<?= $strHTMLControlName["DESCRIPTION"] ?>"
    		value="<?= htmlspecialcharsex($value["DESCRIPTION"]) ?>"
    		title="<?= $title ?>"
		    type="text">
    <?php if (!empty($field["ROWS"])) { ?>
		  <textarea name="<?= $strHTMLControlName["VALUE"] ?>"
		    cols="<?= !empty($field["COLS"])? $field["COLS"] : $arProperty["COL_COUNT"] ?>"
		    rows="<?= !empty($field["ROWS"])? $field["ROWS"] : $arProperty["ROW_COUNT"] ?>"><?= htmlspecialcharsex($value["VALUE"])?></textarea>
		<?php } else { ?>
		  <input name="<?= $strHTMLControlName["VALUE"] ?>" value="<?= htmlspecialcharsex($value["VALUE"]) ?>"
		    size="<?= !empty($field["COLS"])? $field["COLS"] : $arProperty["COL_COUNT"] ?>" type="text">
		<?php } ?>

		<?php
    /*

  ?>
  	<div class="admin-form-fields" style="padding-bottom:16px;" data-sort="<?= $field["SORT"] ?>">
      <div class="admin-form-field-label">
        <b><?= $fieldLabel ?></b>
        <input name="<?= $strHTMLControlName["DESCRIPTION"] ?>"
          value="<?= $fieldCode ?>" size="18" type="hidden" id="<?= $strHTMLControlName["DESCRIPTION"] ?>">
      </div>
      <div class="admin-form-field-value" style="display:inline;">
        <?php include $fname ?>
      </div>
		</div>

  <?php */ }

  /*
  static function getFieldsConfig() {
  	static $config = null;
    if (empty($config)) {
      $storage = new Storage(_APP_ROOT . "/api");
      $config = $storage->get("catalog/attribs");
    }
    return $config;
  }
  */

}
