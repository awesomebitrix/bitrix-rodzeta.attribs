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
      "DESCRIPTION" => "Rodzeta - Настраиваемые атрибуты",
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
  		?>
	  		<script>
					BX.ready(function () {
						var $block = document.getElementById("tr_PROPERTY_<?= $arProperty["ID"] ?>");
						$block.querySelector("input[value=\"Добавить\"]").style.display = "none";
	          $block.querySelector("table.nopadding").style.display = "none";

            /*
            // TODO sort fields
            var fields = [];
            var $td = null;
            $block.find(".admin-form-fields").each(function () {
              if ($td == null) {
                $td = $(this).closest("td");
              }
              fields.push([
                parseInt($(this).attr("data-sort")),
                $(this).clone()
              ]);
              // remove from page
              $(this).remove();
            });
            fields.sort(function (a, b) {
              if (a[0] === b[0]) {
                return 0;
              }
              return a[0] < b[0]? -1 : 1;
            });
            // insert to page
            fields.forEach(function (v) {
              $td.append(v[1]);
            });
            */
					});
				</script>
  		<?php
  	}

    static $attribs = null;
    static $currentAttribs = null;
    if ($attribs === null) {
      list($attribs) = \Rodzeta\Attribs\Utils::get();
      $currentAttribs = $attribs;
    }

    // current field
    if ($value["VALUE"] != "") {
      // not empty value - get by code
      $field = $attribs[$value["DESCRIPTION"]];
      unset($currentAttribs[$field["CODE"]]);
    } else {
      // empty value - get next from rest attribs
      $field = array_shift($currentAttribs);
    }
    if (empty($field)) {
      return;
    }

    // check section for current attrib
    static $elementSections = null;
    if ($elementSections === null) {
      $elementSections = array();
      $res = \CIBlockElement::GetElementGroups($_REQUEST["ID"], true, array("ID"));
      while ($rowSection = $res->Fetch()) {
        $elementSections[] = $rowSection["ID"];
      }
    }
    $found = false;
    foreach ($elementSections as $currentSectionId) {
      if (isset($field["SECTIONS"][$currentSectionId])) {
        $found = true;
        break;
      }
    }
    if (!$found) {
      return;
    }

  	$title = $value["DESCRIPTION"];
  	if (!empty($field["NAME"])) {
  		$title = $field["NAME"];
  	}
  	if (!empty($field["UNIT"])) {
  		$title .= ", " . $field["UNIT"];
  	}
  	?>

    <div class="admin-form-fields"
        style="padding-bottom:16px;display:table;"
        data-sort="<?= $field["SORT"] ?>">

      <div class="admin-form-field-label" style="vertical-align:top;display:table-cell;">
        <b><?= $title ?></b>
        <input name="<?= $strHTMLControlName["DESCRIPTION"] ?>"
            id="<?= $strHTMLControlName["DESCRIPTION"] ?>"
        		value="<?= htmlspecialcharsex($field["CODE"]) ?>"
        		title="<?= $title ?>"
            size="12"
            placeholder="код атрибута"
            type="hidden">
      </div>

      <div class="admin-form-field-value" style="padding-left:6px;vertical-align:top;display:table-cell;">
        <?php if (!empty($field["ROWS"])) { ?>
    		  <textarea name="<?= $strHTMLControlName["VALUE"] ?>"
            placeholder="значение"
    		    cols="<?= !empty($field["COLS"])? $field["COLS"] : $arProperty["COL_COUNT"] ?>"
    		    rows="<?= $field["ROWS"] ?>"><?= htmlspecialcharsex($value["VALUE"])?></textarea>
    		<?php } else { ?>
    		  <input name="<?= $strHTMLControlName["VALUE"] ?>"
            placeholder="значение"
            value="<?= htmlspecialcharsex($value["VALUE"]) ?>"
    		    size="<?= !empty($field["COLS"])? $field["COLS"] : "" ?>"
            type="text">
    		<?php } ?>
      </div>

    </div>

		<?php
  }

/*

$fname = __DIR__ . "/attribs/" . basename($field["TYPE"]) . ".php";
  if (!file_exists($fname)) {
    $fname = __DIR__ . "/attribs/default.php";
  }
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
*/

}
