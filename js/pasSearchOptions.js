if(typeof String.prototype.ltrim == "undefined") String.prototype.ltrim = function(){return this.replace(/^\s+/,"");}
if(typeof String.prototype.rtrim == "undefined") String.prototype.rtrim = function(){return this.replace(/\s+$/,"");}
if(typeof String.prototype.trim == "undefined") String.prototype.trim = function(){var str = this.ltrim();return str.rtrim();}
if(typeof String.prototype.right == "undefined") String.prototype.right = function(n){return this.substring(this.length - n, this.length)}
if(typeof String.prototype.left == "undefined") String.prototype.left = function(n) { return this.substring(0, n); }

function pas_so_AJAXCall(action, dataBlock = [], callback = null, error_callback = null) {
	var xmlhttp = new XMLHttpRequest();
	var data	= new FormData();

	data.append("action", action);

	if (dataBlock != null) {
		Object.keys(dataBlock).forEach(function(key) {
			if (key != "action") {
				data.append(key, dataBlock[key]);
			}
		});
	}
	xmlhttp.open("POST", ajaxurl, true);
	xmlhttp.onreadystatechange = function () {
		if (4 == xmlhttp.readyState) {
			// The next line strips the admin_ajax.php 1 byte response from the beginning of the response.
			// Usually, admin_ajax.php returns a zero. This strips that.
			var response = (xmlhttp.responseText.length >= 1 ? xmlhttp.responseText.left(xmlhttp.responseText.length - 1) : "");
			if (xmlhttp.status == 200) {
				if (callback != null) {
					callback(response);
				}
			} else {
				if (error_callback != null) {
					error_callback(xmlhttp.statusText, response);
				} else {
					alert("AJAX Error:\n\n" + xmlhttp.statusText + "\n\n" + response);
				}
			}
		}
	}
	xmlhttp.send(data);
}
function grabKey(element) {
	var keyCode = (event.keyCode || event.which);
	if (keyCode == 13) {
		search(element);
	}
}

function search(element) {
	if (element == null || element == undefined) {
		searchString = document.getElementById("searchString").value;
	} else if (typeof element == "string") {
		searchString = element;
	} else {
		searchString = element.value;
	}
	var xmlhttp = new XMLHttpRequest()
	var data = new FormData()
	data.append('searchString', searchString);
	data.append('action', 'searchForIt');

	xmlhttp.open("POST", ajaxurl,true)

	xmlhttp.onreadystatechange = function () {
		var response = xmlhttp.responseText.trim();
		response = response.left(response.length - 1);

		if (4 == xmlhttp.readyState) {
			if (200 == xmlhttp.status) {
				var results = document.getElementById("results")
				results.innerHTML = response;
			}
		}
	}
	xmlhttp.send(data);
}

function killThisRecord(optionID) {
	var xmlhttp = new XMLHttpRequest();
	var data = new FormData();
	var searchString;
	data.append("action", "killRecord");
	data.append("optionID", optionID);
	xmlhttp.open("POST", ajaxurl,true)

	xmlhttp.onreadystatechange = function () {
		var response = xmlhttp.responseText.trim();
		response = response.left(response.length - 1);

		if (4 == xmlhttp.readyState) {
			if (200 == xmlhttp.status) {
				search(searchString)
			}
		}
	}
	xmlhttp.send(data);
}
function pas_so_changeValue(option_id, searchstring, element) {
	var dataBlock = [];
	debugger
	var original_value = element.getAttribute("data-original-value");
	var new_value = element.value;

	var successCallback = function (response) {
			search(searchString);
	}
	var failureCallback = function (status, response) {
		alert(status + "\n\n" + response);
	}
	if (original_value != new_value) {
		dataBlock['option_id'] = option_id;
		dataBlock['option_value'] = new_value;

		pas_so_AJAXCall("update_option_value", dataBlock, successCallback, failureCallback);
	}
}
function ajax_adm_success(response) {
	var box = document.createElement("div");
	box.id = "expanded_value";
	box.onclick = function () {
		this.parentNode.removeChild(this);
		this.remove();
	}
	document.getElementsByTagName("body")[0].appendChild(box);
	box.innerHTML = response;
}
function ajax_adm_failure(status, response) {
	alert(status + "\n\n" + response);
}
function pas_opt_expandValue(key) {
	var datablock = [];
	datablock['option_name'] = key;

	pas_so_AJAXCall("get_option_value", datablock, ajax_adm_success, ajax_adm_failure);
}