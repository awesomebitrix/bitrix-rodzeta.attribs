<?php

use \rivetweb\storage\php\Storage;

// see /bitrix/components/bitrix/system.field.view/templates/string

class EntityFieldAttribs {

  function GetUserTypeDescription() {
    return [
        "PROPERTY_TYPE" => "S",
        "USER_TYPE"	=> "FieldAttribs",
        "DESCRIPTION" => "Атрибуты элемента",
        "BASE_TYPE" => "string",
				//optional handlers
        "GetPropertyFieldHtml" => ["EntityFieldAttribs", "GetPropertyFieldHtml"],
        /*
        "CheckFields"	=> array("CUserTypeString","CheckFields"),
        "GetLength"	=> array("CUserTypeString","GetLength"),
        "ConvertToDB"	=> array("CUserTypeString","ConvertToDB"),
        "ConvertFromDB"	=> array("CUserTypeString","ConvertFromDB"),
        "GetAdminListViewHTML" => array("CUserTypeString","GetAdminListViewHTML"),
        "GetPublicViewHTML"	=> array("CUserTypeString","GetPublicViewHTML"),
        "GetPublicEditHTML"	=> array("CUserTypeString","GetPublicEditHTML"),
        */
    ];
  }

  function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) {
    static $hideButton = false;
    static $fieldList = null;
    if ($fieldList === null) {
      $fieldList = self::getFieldsConfig();
    }
    // FIX for check section
    static $elementSection = null;
    if ($elementSection === null) {
      $elementSection = [];
      foreach (Entity::getElementSections($_REQUEST["ID"]) as $row) {
        $elementSection[] = $row["ID"];
      }
    }

    ?>
    <?php if (!$hideButton) { // hide button "Добавить"
      $hideButton = true; ?>
      <script>
        jQuery(function ($) {
          var $block = $("#tr_PROPERTY_<?= EntityFilter::ATTRIBS_PROPERTY_ID ?>");
          $block.find("input[value=\"Добавить\"]").hide();
          $block.find("table.nopadding").hide();

          // sort fields
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
        });
      </script>
    <?php } ?>

    <?php
    // current field
    if ($value["VALUE"] != "") {
      $field = self::getFieldsConfig()[$value["DESCRIPTION"]];
      unset($fieldList[$field["CODE"]]);
    } else {
      $field = array_shift($fieldList);
    }

    if (empty($field)) {
      return;
    }

    $found = false;
    foreach ($elementSection as $currentSectionId) {
      if (isset($field["SHOW_IN"][$currentSectionId]) ) {
        $found = true;
        break;
      }
    }
    if (!$found) {
      return;
    }

    $fieldLabel = $field["NAME"] . (empty($field["UNIT"])? "" : (", " . $field["UNIT"]));
    $fieldCode = htmlspecialcharsex($field["CODE"]);

    //echo "<pre>"; var_dump($arProperty, $value, $strHTMLControlName); echo "</pre>";
    //GetMessage("IBLOCK_AT_PROP_DESC_1")

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

  <?php }

  static function getFieldsConfig() {
    static $config = null;
    if (empty($config)) {
      $storage = new Storage(_APP_ROOT . "/api");
      $config = $storage->get("catalog/attribs");
    }
    return $config;
  }

  static function getSortedAttribs($item) {
    foreach (self::getFieldsConfig() as $k => $v) {
      if (isset($item["DISPLAY_PROPERTIES"]["CUSTOMFIELDS"][$k])) {
        yield $k => [
          "NAME" => $v["NAME"],
          "UNIT" => $v["UNIT"],
          "VALUE" => $item["DISPLAY_PROPERTIES"]["CUSTOMFIELDS"][$k],
        ];
      }
    }
  }

  static function getRefAttribs() {
    static $result = null;
    if (empty($result)) {
      $storage = new Storage(_APP_ROOT . "/api");
      $result = $storage->get("catalog/dir_values");
    }
    return $result;
  }

  static function getSortedRefAttribs($item) {
    $sections = [];
    foreach (Entity::getElementSections($item["ID"]) as $row) {
      $sections[$row["ID"]] = 1;
    }
    $attribs = self::getRefAttribs();
    $result = [];
    foreach ($attribs as $k => $v) {
      if (isset($sections[$k]) && !empty($v["GROUP_ID"])) {
        $parentk = $attribs[$v["GROUP_ID"]]["NAME"];
        $result[$parentk][] = [
          "CODE" => $v["CODE"],
          "VALUE" => $v["NAME"],
        ];
      }
    }
    return $result;
  }

}
