if(typeof String.prototype.ltrim == "undefined") String.prototype.ltrim = function(){return this.replace(/^\s+/,"");}
if(typeof String.prototype.rtrim == "undefined") String.prototype.rtrim = function(){return this.replace(/\s+$/,"");}
if(typeof String.prototype.trim == "undefined") String.prototype.trim = function(){var str = this.ltrim();return str.rtrim();}
if(typeof String.prototype.right == "undefined") String.prototype.right = function(n){return this.substring(this.length - n, this.length)}
if(typeof String.prototype.left == "undefined") String.prototype.left = function(n) { return this.substring(0, n); }

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
				results.innerHTML = response
			}
		}
	}
	xmlhttp.send(data);
}

function killThisRecord(optionID, searchString) {
	var xmlhttp = new XMLHttpRequest();
	var data = new FormData();
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
