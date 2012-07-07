function customHandler(desc,page,line,chr)  {

 alert(

  'JavaScript error occurred! \n'

 +'The error was handled by '

 +'a customized error handler.\n'

 +'\nError description: \t'+desc

 +'\nPage address:      \t'+page

 +'\nLine number:       \t'+line

 )

 return true

}

window.onerror=customHandler;

function goto(page, vari, varit){
	$("#inner").html("<form action='' method='post' name='form'><input type='hidden' name='p' value='"+page+"'><input type='hidden' name='v' value='"+vari+"'><input type='hidden' name='vt' value='"+varit+"'></form><center>Loading...</center>");
    document.forms['form'].submit();
}

var pageStorage = "";
var showing = false;

$(function() {
	if($('input[name=passwordfirstfield]').length != 0){
		pageStorage = $('input[name=passwordfirstfield]').val();
        if(pageStorage == ""){
            $('input[name=passwordfirstfield]').get(0).type = "text";
            $('input[name=passwordsecondfield]').get(0).type = "text";
        }else{
	        $('input[name=passwordfirstfield]').val("*************");
	        $('input[name=passwordsecondfield]').val("*************");
        }
    }
    //autoOut();
});

function autoOut(){
	if($('#auto').length != 0){
		var left = "60";
        if($("#auto").html() != "1:00"){
            var t  = $("#auto").html().split(":");
            left = t[1];
        }
        left = left - 1;
        if(left == 0){
            window.location = 'login.php';
        }else{
            if(left > 10){
                $("#auto").html("0:"+left);
            }else{
                $("#auto").html("0:0"+left);
            }
            setTimeout("autoOut()", 1000);
        }
	}
}

function autoIncrease(){
	$("#auto").html("1:00");
}

function showPassBox(){
	showing = true;
    $('input[name=passwordfirstfield]').val(pageStorage);
    $('input[name=passwordsecondfield]').val(pageStorage);
	$('input[name=passwordfirstfield]').get(0).type = "text";
	$('input[name=passwordsecondfield]').get(0).type = "text";
    $('#showLink').html('Hiding in 5');
    setTimeout("$('#showLink').html('Hiding in 4');", 1000);
    setTimeout("$('#showLink').html('Hiding in 3');", 2000);
    setTimeout("$('#showLink').html('Hiding in 2');", 3000);
    setTimeout("$('#showLink').html('Hiding in 1');", 4000);
    setTimeout("hidePassBox();", 5000);
}

function showPassBoxNoTimer(){
	if(pageStorage != "" && !showing){
	    $('input[name=passwordfirstfield]').val('');
	    $('input[name=passwordsecondfield]').val('');
		$('input[name=passwordfirstfield]').get(0).type = "text";
		$('input[name=passwordsecondfield]').get(0).type = "text";
        $('#showLink').html('');
        pageStorage = "";
    }
}

function hidePassBox(){
	showing = false;
    $('input[name=passwordfirstfield]').blur();
    $('input[name=passwordsecondfield]').blur();
	$('input[name=passwordfirstfield]').get(0).type = "password";
	$('input[name=passwordsecondfield]').get(0).type = "password";
    $('input[name=passwordfirstfield]').val("*************");
    $('input[name=passwordsecondfield]').val("*************");
    $('#showLink').html('show');
}

(function () {
    var onload = window.onload;

    window.onload = function () {
        if (typeof onload == "function") {
            onload.apply(this, arguments);
        }

        var fields = [];
        var inputs = document.getElementsByTagName("input");
        var textareas = document.getElementsByTagName("textarea");

        for (var i = 0; i < inputs.length; i++) {
            fields.push(inputs[i]);
        }

        for (var i = 0; i < textareas.length; i++) {
            fields.push(textareas[i]);
        }

        for (var i = 0; i < fields.length; i++) {
            var field = fields[i];

            if (typeof field.onpaste != "function" && !!field.getAttribute("onpaste")) {
                field.onpaste = eval("(function () { " + field.getAttribute("onpaste") + " })");
            }

            if (typeof field.onpaste == "function") {
                var oninput = field.oninput;

                field.oninput = function () {
                    if (typeof oninput == "function") {
                        oninput.apply(this, arguments);
                    }

                    if (typeof this.previousValue == "undefined") {
                        this.previousValue = this.value;
                    }

                    var pasted = (Math.abs(this.previousValue.length - this.value.length) > 1 && this.value != "");

                    if (pasted && !this.onpaste.apply(this, arguments)) {
                        this.value = this.previousValue;
                    }

                    this.previousValue = this.value;
                };

                if (field.addEventListener) {
                    field.addEventListener("input", field.oninput, false);
                } else if (field.attachEvent) {
                    field.attachEvent("oninput", field.oninput);
                }
            }
        }
    }
})();