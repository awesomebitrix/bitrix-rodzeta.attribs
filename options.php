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

$currentIblockId = Option::get("rodzeta.site", "iblock_content", 1);

Loc::loadMessages(__FILE__);

$tabControl = new \CAdminTabControl("tabControl", [
  [
		"DIV" => "edit2",
		"TAB" => Loc::getMessage("RODZETA_ATTRIBS_DATA_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_ATTRIBS_DATA_TAB_TITLE_SET", [
			"#FILE#" => FILE_ATTRIBS
		]),
  ],
  [
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_ATTRIBS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_ATTRIBS_MAIN_TAB_TITLE_SET"),
  ],
]);

if ($request->isPost() && \check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.site", "iblock_content", (int)$request->getPost("iblock_content"));

		CreateCache($request->getPost("attribs"));

		// create attribs property
		$iblockProperty = new \CIBlockProperty();
		$newPropertyId = $iblockProperty->Add([
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
		]);
		if ($newPropertyId) {
			Option::set("rodzeta.attribs", "property_id", $newPropertyId);
		}

		\CAdminMessage::showMessage([
	    "MESSAGE" => Loc::getMessage("RODZETA_ATTRIBS_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ]);
	}
}

$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td colspan="2">
			<table width="100%" class="rodzeta-attribs js-table-autoappendrows">
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
					<?php
						list($attribs) = Config();
						$idx = 0;
						foreach (AppendValues($attribs, 1, array_fill(0, 12, null)) as $i => $row) {
							$idx++;
							if ($i == "") {
								$i = $idx;
							}
					?>
						<tr data-idx="<?= $idx ?>">
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
					<?php } ?>
				</tbody>
			</table>
		</td>
	</tr>

	<?php $tabControl->beginNextTab() ?>

	<tr class="heading">
		<td colspan="2">Хранение атрибутов</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Инфоблок</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<?= GetIBlockDropDownListEx(
				$currentIblockId,
				"iblock_type",
				"iblock_content",
				[
					"MIN_PERMISSION" => "R",
				],
				"",
				"" //"RodzetaSettingsAttribsUpdate()"
			) ?>
		</td>
	</tr>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<link href="/bitrix/admin/rodzeta.attribs/style.css" type="text/css" rel="stylesheet" />
<script src="/bitrix/admin/rodzeta.attribs/init.js"></script>

<?php

$tabControl->end();
