function php_method(...arguments) {
    var fd = new FormData;
    fd.append("method",arguments[0]);
    for (var i = 1; i < arguments.length; i++) {
        fd.append("arg"+i,arguments[i]);
    }
    return new Promise(function(resolve,reject) {

        $.ajax({
            url: "include_pages/ajax_page.php",
            method: "POST",
            processData: false,
            contentType: false,
            data: fd
        }).done(function(data) {
            resolve(data)
        }).fail(function() {
            reject("Not conneccted");
        });
    });

}

$("#profile_form").submit(function(e) {
    e.preventDefault();
    var a = $(this).serializeArray();
    var o = {};
    $.each(a, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    php_method("update_profile",JSON.stringify(o)).then(function(result) {
        if (result !== "Updated!") {
            alert(result);
            return;
        }
        document.location.href = "index.php";

    });
});
function view_show(id) {

    document.location.href = "index.php?page=view_show&showid="+id;
}
$(".show_row").click(function() {
    var id = $(this).attr("show_id");
    view_show(id);
});

$(".request_form").submit(function (e) {
    e.preventDefault();
    var o = {};
    var a = $(this).serializeArray();
    $.each(a, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    php_method("update_request",JSON.stringify(o)).then(function(result) {
        if (result !== "Done") {
            alert(result);
            return;
        }
        document.location.href = "index.php?page=home";

    });
});