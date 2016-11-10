<?php
/*******************************************************************************
 * rodzeta.attribs - Infoblock element attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Attribs;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

$currentIblockId = Option::get("rodzeta.attribs", "iblock_id", 2);
//$systemIblockId = Option::get("rodzeta.attribs", "sys_iblock_id", 3);

Loc::loadMessages(__FILE__);

$tabControl = new \CAdminTabControl("tabControl", array(
  array(
		"DIV" => "edit2",
		"TAB" => Loc::getMessage("RODZETA_ATTRIBS_DATA_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_ATTRIBS_DATA_TAB_TITLE_SET", array(
			"#FILE#" => _FILE_ATTRIBS
		)),
  ),
  array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_ATTRIBS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_ATTRIBS_MAIN_TAB_TITLE_SET"),
  ),
));

if ($request->isPost() && \check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.attribs", "iblock_id", (int)$request->getPost("iblock_id"));
		//Option::set("rodzeta.attribs", "property_id", (int)$request->getPost("property_id"));

		CreateCache($request->getPost("attribs"));

		// create attribs property
		$iblockProperty = new \CIBlockProperty();
		$newPropertyId = $iblockProperty->Add(array(
			"IBLOCK_ID" => $currentIblockId,
			"CODE" => "RODZETA_ATTRIBS",
			"NAME" => "Атрибуты",
			"ACTIVE" => "Y",
			"SORT" => "100",
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "Customfield",
			"MULTIPLE" => "Y",
			"WITH_DESCRIPTION" => "Y",
			"VERSION" => 2,
			"MULTIPLE_CNT" => 30,
			"ROW_COUNT" => 1,
			"COL_COUNT" => 40,
		));
		if ($newPropertyId) {
			Option::set("rodzeta.attribs", "property_id", $newPropertyId);
		}

		\CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_ATTRIBS_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ));
	}	/*else if ($request->getPost("clear") != "") {


		CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_ATTRIBS_OPTIONS_RESETED"),
	    "TYPE" => "OK",
	  ));
	} */
}

$tabControl->begin();

?>

<script>

/*
function RodzetaSettingsAttribsUpdate() {
	let $selectIblock = document.getElementById("iblock_id");
	let $selectProperty = document.getElementById("rodzeta-attribs-property-id");
	let iblockId = $selectIblock.value;
	let selectedOption = $selectProperty.getAttribute("data-value");

	BX.ajax.loadJSON("/bitrix/admin/rodzeta.attribs/optionsproperties.php?iblock_id=" + iblockId, function (data) {
		let html = ["<option value=''>(выберите свойство)</option>"];
		for (let k in data) {
			let selected = selectedOption == k? "selected" : "";
			html.push("<option " + selected + " value='" + k + "'>[" + k + "] " + data[k] + "</option>");
		}
		$selectProperty.innerHTML = html.join("\n");
	});
}
*/

</script>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td colspan="2">
			<table width="100%" class="rodzeta-attribs">
				<thead>
					<tr>
						<th></th>
						<th>
							Выводить в разделах
							<div class="rodzeta-attribs-sections-src" style="display:none;">
								<select multiple size="5" style="width:90%;">
									<?php foreach (SectionsTreeList($currentIblockId) as $optionValue => $optionName) { ?>
										<option value="<?= $optionValue ?>"><?= $optionName ?></option>
									<?php } ?>
								</select>
							</div>
						</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php list($attribs) = Config(); foreach (AppendValues($attribs, 10, array_fill(0, 12, null)) as $i => $row) { ?>
						<tr>
							<td>
								<input type="text" placeholder="Код атрибута"
									name="attribs[<?= $i ?>][CODE]"
									value="<?= htmlspecialcharsex($row["CODE"]) ?>"
									size="16">
								<br>
								<input type="text" placeholder="Алиас (для ЧПУ)"
									name="attribs[<?= $i ?>][ALIAS]"
									value="<?= htmlspecialcharsex($row["ALIAS"]) ?>"
									size="16">
								<br>
								<input type="text" placeholder="Название"
									name="attribs[<?= $i ?>][NAME]"
									value="<?= htmlspecialcharsex($row["NAME"]) ?>"
									size="16">
								<br>
								<input type="text" placeholder="Подсказка / Ед.измерения"
									name="attribs[<?= $i ?>][HINT]"
									value="<?= htmlspecialcharsex($row["HINT"]) ?>"
									size="16">
							</td>
							<td>
								<div class="rodzeta-attribs-sections">
									<input type="text" style="display:none;"
										name="attribs[<?= $i ?>][SECTIONS]" value="<?= htmlspecialcharsex(implode(",", array_keys($row["SECTIONS"]))) ?>">
								</div>
							</td>
							<td>
								<input type="hidden" name="attribs[<?= $i ?>][NUMERIC]" value="">
								<input type="hidden" name="attribs[<?= $i ?>][FILTER]" value="">
								<input type="hidden" name="attribs[<?= $i ?>][COMPARE]" value="">
								<label title="Фильтровать как числовое значение">
									<input type="checkbox"
									name="attribs[<?= $i ?>][NUMERIC]"
									value="1" <?= !empty($row["NUMERIC"])? "checked" : "" ?>>&nbsp;Числовое
								</label>
								<br>
								<label title="Использовать в фильтре">
									<input type="checkbox"
										name="attribs[<?= $i ?>][FILTER]"
										value="1" <?= !empty($row["FILTER"])? "checked" : "" ?>>&nbsp;Фильтр
								</label>
								<br>
								<label title="Использовать в сравнении">
									<input type="checkbox"
										name="attribs[<?= $i ?>][COMPARE]"
										value="1" <?= !empty($row["COMPARE"])? "checked" : "" ?>>&nbsp;Сравнение
								</label>
							</td>
							<td>
								<input type="text" placeholder="Сортировка"
									name="attribs[<?= $i ?>][SORT]"
									value="<?= htmlspecialcharsex($row["SORT"]) ?>"
									size="10">
								<br>
								<select name="attribs[<?= $i ?>][INPUT_TYPE]" title="Тип поля">
									<option value="">TEXT</option>
									<option value="HTML" <?= $row["INPUT_TYPE"] == "HTML"? "selected" : "" ?>>HTML</option>
									<option value="GALLERY" <?= $row["INPUT_TYPE"] == "GALLERY"? "selected" : "" ?>>GALLERY</option>
								</select>
								<br>
								<input type="text" placeholder="Ширина поля"
									name="attribs[<?= $i ?>][COLS]"
									value="<?= htmlspecialcharsex($row["COLS"]) ?>"
									size="10">
								<br>
								<input type="text" placeholder="Высота поля"
									name="attribs[<?= $i ?>][ROWS]"
									value="<?= htmlspecialcharsex($row["ROWS"]) ?>"
									size="10">
							</td>
						</tr>
						<tr>
							<td colspan="4">
								<br>
								<br>
							</td>
						<tr>
					<?php } ?>
				</tbody>
			</table>
		</td>
	</tr>

	<?php $tabControl->beginNextTab() ?>

	<?php /*
	<tr class="heading">
		<td colspan="2">Настройки атрибутов</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Инфоблок</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<?= GetIBlockDropDownListEx(
				$systemIblockId,
				"sys_iblock_type_id",
				"sys_iblock_id",
				array(
					"MIN_PERMISSION" => "R",
				),
				"",
				""
			) ?>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Код раздела</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="attribs_section_code" type="text" value="<?= Option::get("rodzeta.attribs", "attribs_section_code", "RODZETA_ATTRIBS") ?>" disabled>
			<input name="attribs_section_code" type="hidden" value="RODZETA_ATTRIBS">
		</td>
	</tr>
	*/ ?>

	<tr class="heading">
		<td colspan="2">Настройки хранения атрибутов</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Инфоблок</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<?= GetIBlockDropDownListEx(
				$currentIblockId,
				"iblock_type_id",
				"iblock_id",
				array(
					"MIN_PERMISSION" => "R",
				),
				"",
				"" //"RodzetaSettingsAttribsUpdate()"
			) ?>
		</td>
	</tr>

	<?php /*
	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Свойство "Характеристики"</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<select name="property_id" id="rodzeta-attribs-property-id" data-value="<?= Option::get("rodzeta.attribs", "property_id", 2) ?>">
				<option value="">(выберите свойство)</option>
			</select>
		</td>
	</tr>
	*/ ?>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<style>

table.rodzeta-attribs input,
table.rodzeta-attribs select,
table.rodzeta-attribs label {
	margin-bottom: 4px !important;
}

</style>

<script>

BX.ready(function () {
	"use strict";

	//RodzetaSettingsAttribsUpdate();

	var $selectSections = document.querySelectorAll(".rodzeta-attribs-sections");
	var selectSectionsSrc = document.querySelector(".rodzeta-attribs-sections-src").innerHTML;
	for (var i = 0, l = $selectSections.length; i < l; i++) {
		var $sections = $selectSections[i].querySelector("input");

		// append sections selector
		$selectSections[i].innerHTML = $selectSections[i].innerHTML + selectSectionsSrc;
		var $selectSectionsInput = $selectSections[i].querySelector("select");

		$selectSectionsInput.onchange = function (event) {
			// update selected options
			var sectionsIds = [];
			for (var i in event.target.options) {
				if (event.target.options[i].selected) {
					sectionsIds.push(event.target.options[i].value);
				}
			}
			event.target.parentNode.querySelector("input").value = sectionsIds.join(",");
		}

		// init selected options
		var sectionsIds = $sections.value.split(",");
		if (sectionsIds.length > 0) {
			for (var idx in sectionsIds) {
				if (sectionsIds[idx] != "") {
					var $option = $selectSectionsInput.querySelector('[value="' + sectionsIds[idx] + '"]');
					if ($option) {
						$option.selected = true;
					}
				}
			}
		}
	}

});

</script>

<?php

$tabControl->end();
