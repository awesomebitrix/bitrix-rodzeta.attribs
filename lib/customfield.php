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

final class Customfield {

  function GetUserTypeDescription() {
    return array(
      "PROPERTY_TYPE" => "S",
      "USER_TYPE"	=> "Customfield",
      "DESCRIPTION" => "Rodzeta - Атрибуты элемента",
      "BASE_TYPE" => "string",
		  "GetPropertyFieldHtml" => array("\Rodzeta\Attribs\Customfield", "GetPropertyFieldHtml"),

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

    <div class="admin-form-fields"
        style="padding-bottom:16px;vertical-align:top;"
        data-sort="<?= $field["SORT"] ?>">

      <div class="admin-form-field-label" style="display:inline;">
        <input name="<?= $strHTMLControlName["DESCRIPTION"] ?>"
            id="<?= $strHTMLControlName["DESCRIPTION"] ?>"
        		value="<?= htmlspecialcharsex($value["DESCRIPTION"]) ?>"
        		title="<?= $title ?>"
            size="12"
            type="text">
      </div>

      <div class="admin-form-field-value" style="display:inline;">
        <?php if (!empty($field["ROWS"])) { ?>
    		  <textarea name="<?= $strHTMLControlName["VALUE"] ?>"
    		    cols="<?= !empty($field["COLS"])? $field["COLS"] : $arProperty["COL_COUNT"] ?>"
    		    rows="<?= $field["ROWS"] ?>"><?= htmlspecialcharsex($value["VALUE"])?></textarea>
    		<?php } else { ?>
    		  <input name="<?= $strHTMLControlName["VALUE"] ?>"
            value="<?= htmlspecialcharsex($value["VALUE"]) ?>"
    		    size="<?= !empty($field["COLS"])? $field["COLS"] : $arProperty["COL_COUNT"] ?>"
            type="text">
    		<?php } ?>
      </div>

    </div>

		<?php
  }

}
