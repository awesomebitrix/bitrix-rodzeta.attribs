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

  static function GetUserTypeDescription() {
    return [
      "PROPERTY_TYPE" => "S",
      "USER_TYPE"	=> "Customfield",
      "DESCRIPTION" => "Rodzeta - Настраиваемые атрибуты",
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
      list($attribs) = Config();
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
      $elementSections = [];
      $res = \CIBlockElement::GetElementGroups($_REQUEST["ID"], true, ["ID"]);
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
  	if (!empty($field["HINT"])) {
  		$title .= ", " . $field["HINT"];
  	}

    $inputWidget = "";
    if (!empty($field["INPUT_TYPE"])) {
      $inputWidget = __DIR__ . "/customfield-" . basename($field["INPUT_TYPE"]) . ".php";
      if (!file_exists($inputWidget)) {
        $inputWidget = "";
      }
    }

  	?>

    <div class="admin-form-fields" data-sort="<?= $field["SORT"] ?>">

      <div class="admin-form-field-label">
        <b><?= $title ?></b>
        <input name="<?= $strHTMLControlName["DESCRIPTION"] ?>"
            id="<?= $strHTMLControlName["DESCRIPTION"] ?>"
        		value="<?= htmlspecialcharsex($field["CODE"]) ?>"
        		title="<?= $title ?>"
            size="12"
            placeholder="код атрибута"
            type="hidden">
      </div>

      <div class="admin-form-field-value">
        <?php if ($inputWidget != "") { ?>

          <?php include $inputWidget; ?>

        <?php } else { ?>

          <?php if (!empty($field["ROWS"])) { ?>
      		  <textarea name="<?= $strHTMLControlName["VALUE"] ?>"
              placeholder="значение"
      		    cols="<?= !empty($field["COLS"])? $field["COLS"] : $arProperty["COL_COUNT"] ?>"
      		    rows="<?= $field["ROWS"] ?>"><?= htmlspecialcharsex($value["VALUE"])?></textarea>
      		<?php } else { ?>
      		  <input name="<?= $strHTMLControlName["VALUE"] ?>"
              placeholder="значение <?= $field["CODE"] ?>"
              value="<?= htmlspecialcharsex($value["VALUE"]) ?>"
      		    size="<?= !empty($field["COLS"])? $field["COLS"] : "" ?>"
              type="text">
      		<?php } ?>

        <?php } ?>
      </div>
      <br>

    </div>

		<?php
  }

}
