
BX.ready(function () {
	"use strict";

	// init attribs
	var $selectSections = document.querySelectorAll(".rodzeta-attribs-sections");
	var selectSectionsSrc = document.querySelector(".rodzeta-attribs-sections-src").innerHTML;
	for (var i = 0, l = $selectSections.length; i < l; i++) {
		var $sections = $selectSections[i].querySelector("input");

		// append sections selector
		$selectSections[i].innerHTML = $selectSections[i].innerHTML + selectSectionsSrc;
		var $selectSectionsInput = $selectSections[i].querySelector("select");
		// TODO change to addEventListener
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

	// autoappend rows
	function makeAutoAppend($table) {
		function bindEvents($row) {
			for (let $input of $row.querySelectorAll('input[type="text"]')) {
				$input.addEventListener("change", function (event) {
					let $tr = event.target.closest("tr");
					let $trLast = $table.rows[$table.rows.length - 1];
					if ($tr != $trLast) {
						return;
					}
					$table.insertRow(-1);
					$trLast = $table.rows[$table.rows.length - 1];
					$trLast.innerHTML = $tr.innerHTML;
					let idx = parseInt($tr.getAttribute("data-idx")) + 1;
					$trLast.setAttribute("data-idx", idx);
					for (let $input of $trLast.querySelectorAll('input,select')) {
						$input.setAttribute("name", $input.getAttribute("name").replace(/([a-zA-Z0-9])\[\d+\]/, "$1[" + idx + "]"));
					}
					bindEvents($trLast);
				});
			}
		}
		for (let $row of document.querySelectorAll(".js-table-autoappendrows tr")) {
			bindEvents($row);
		}
	}
	for (let $table of document.querySelectorAll(".js-table-autoappendrows")) {
		makeAutoAppend($table);
	}

});
