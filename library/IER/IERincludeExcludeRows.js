// Credit: m_zolfo
var IERisModified = 0;
var IERresultRIERS = "";


$(document).ready(function(){
	//read all rows from file
	//read like array
	//$("head").append('<link rel="stylesheet" href="../IERincludeExcludeRows.css">');
	$("head").append('<link rel="stylesheet" href="../../library/IER/IERincludeExcludeRows.css">');

  clearIncludeExcludeRows();
	writeExcludeRows();

	$("#IERsaveIncludeExcludeRows").css("display","none");

	IERisModified = 1;
});

function readIncludeExcludeRowsArray(){
	return readIncludeExcludeRowsString().split(";");
}

function setExcludeRows(){
	listRowsAll = readIncludeExcludeRowsArray();

	IERisModified = 0;
	$("#IERsaveIncludeExcludeRows").removeClass("excluded");

	for (m = 0; m < listRowsAll.length; ++m) {
		//u =listRowsAll[m].replace(/(?:\r\n\|\r|\n)/g,"");
    u =listRowsAll[m].replace(/[\r\n_\s]/g, '');
    if(u.length > 1 && $(u) != "undefined")
      includeExcludeRow(u);
	}

	IERisModified = 1;
}

function getMyUrl(){
	// ritorna ./{modulo}/{nome_form} dello script in esecuzione - es.: magazz/admin_artico
	url=window.location.href;
	urlArr= url.split("/");
	url = urlArr[urlArr.length - 2]+ "/" + urlArr[urlArr.length - 1];
	urlArr = url.split(".");
	url = urlArr[0];

	//alert(window.location + " - " + url);

	//return "./" + url;
	return "../../modules/" + url;
}

function readIncludeExcludeRowsString(){
	result = "";

	$.ajax({
		type: 'POST',
		url: "../../library/IER/IERincludeExcludeRows.php",
		async: false,
		data: {
		fn: "read",
		filename: getMyUrl() + ".IER",
		value: "",
		},
		success: function(msg){
			IERresultRIERS = msg;
		}
	});

	result = IERresultRIERS;
	IERresultRIERS = "";

  //alert(result);

	return ""+result;
}

function clearIncludeExcludeRows(){
	// change title attribute for div
	$("#IERenableIncludeExcludeRows").attr('title','Personalizza videata');
	$("#IERsaveIncludeExcludeRows").attr('title','Nessuna modifica fatta');

	listRowsAll = document.getElementsByClassName("IERincludeExcludeRow");

	$("#IERincludeExcludeRowsInput").val("");

	for (i = 0; i < listRowsAll.length; ++i) {
		listRowsAll[i].style.display = "block";

		child = document.getElementById('iEbtn'+listRowsAll[i].getAttribute('id'));

		if(child)
			listRowsAll[i].removeChild(child);
	}
}

function writeIncludeExcludeRows(){
	// for all elements with class "IERincludeExcludeRow" and if the id is in list "listRowsExclude"
	listRowsAll = document.getElementsByClassName("IERincludeExcludeRow");

	for (i = 0; i < listRowsAll.length; ++i) {
		includeExcludeBTN = '<div class="IERincludeExcludeBTN" onclick="includeExcludeRow(\''+listRowsAll[i].getAttribute('id')+'\')" id="iEbtn'+listRowsAll[i].getAttribute('id')+'" ></div>';
		listRowsAll[i].innerHTML += includeExcludeBTN;
	}
}

function writeExcludeRows(){
	//read the array from file
	listIERA = readIncludeExcludeRowsArray();

	for (i = 0; i < listIERA.length; ++i){
		//u = "#"+listIERA[i].replace(/(?:\r\n|\r|\n)/g,"");
    u = "#"+listIERA[i];
    u =u.replace(/[\r\n_\s]/g, '');
    if(u.length > 1 && $(u) != "undefined")
      $(u).css("display","none");
	}
}

IERenable = false;

function enableIncludeExcludeRows(){
	if(IERenable)
	{
		/*alert(isModified);
		if(isModified)
			alert('1. modificato');*/

		if(IERisModified == 2)
		{
			if(confirm("Impostazioni modificate, vuoi salvarle ?")) {
						saveIncludeExcludeRows();
						return;
			}
		}

		writeExcludeRows();
		$("#IERsaveIncludeExcludeRows").css("display","none");

		clearIncludeExcludeRows();
		writeExcludeRows();
		$("#IERsaveIncludeExcludeRows").css("display","none");
	}
	else
	{

		/*alert(isModified);
		if(isModified)
			alert('2. modificato');*/

		clearIncludeExcludeRows();
		writeIncludeExcludeRows();

		// change title attribute for div
		$("#IERenableIncludeExcludeRows").attr('title','Esci da personalizzazione');

		$("#IERsaveIncludeExcludeRows").css("display","block");

		setExcludeRows();
	}
	IERenable = !IERenable;
}

function saveIncludeExcludeRows(){
  if(IERisModified == 1 || IERisModified == 0)
		return;

	//if($("#IERincludeExcludeRowsInput").val().length <= 0)
	//	return;

	//alert($("#IERincludeExcludeRowsInput").val());
  //alert(getMyUrl() + ".IER");

	$.post("../../library/IER/IERincludeExcludeRows.php",
	{
    fn: "save",
	filename: getMyUrl() + ".IER",
	value: $("#IERincludeExcludeRowsInput").val(),
	},
	function(data, status){
			alert("Salvataggio impostazioni eseguito con successo");
	});

	IERisModified = 1;
	enableIncludeExcludeRows();
}

function includeExcludeRow(id) {
	if(IERisModified == 1)
	{
		$("#IERsaveIncludeExcludeRows").addClass("excluded");
		IERisModified = 2

		// change title attribute for div
		$("#IERsaveIncludeExcludeRows").attr('title','Salva nuove impostazioni');
	}
	listRowsEx = $("#IERincludeExcludeRowsInput").val().split(";");
	isEx = false;
	stringValue = "";
	i = 0;

	for (i = 0; i < listRowsEx.length; ++i) {
		if(listRowsEx[i]!=id )
		{
			if(listRowsEx[i]!=null && listRowsEx[i].length > 0)
			{
				if(stringValue.length > 0)
					stringValue += ";";
					stringValue += listRowsEx[i];
			}
		}
		else
		{
			isEx = true;
		}
	}

	if(stringValue.length > 0)
		stringValue += ";";

	if(isEx == false)
		stringValue += id;

	stringValue.replace(";;",";");

	if(stringValue.substring(stringValue.length -1,stringValue.length ) == ";" )
		stringValue = stringValue.substring(0,stringValue.length -1);

	$("#IERincludeExcludeRowsInput").val(stringValue);

	if(isEx)
		$("#iEbtn"+id).removeClass("excluded");
	else
		$("#iEbtn"+id).addClass("excluded");
}
