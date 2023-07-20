
function hideSingleNewRow() {
    if ($(".newdodrow").is(":visible")) {
        $(".newdodrow").hide();
    }
}

function showSingleNewRow() {
    if ($(".newdodrow").is(":hidden")) {
        var rows = $(".dod-table").find(".newdod, .dod");

        if (rows.length == 0) {
            $(".newdodrow").show();
        }
    }
}

function serializedodtable() {
    var dod = [];

    var rows = $(".dod-table").find("tbody").find("tr");

    rows.each(function (index) {
        if ($(this).is(":visible")) {
            var row = {};
            if ($(this).hasClass("editdod")) {
                row["id"] = $(this).attr("dodid");
                row["title"] = $(this).find(".newdodTitle").val();
                row["text"] = $(this).find(".newdodDescription").val();
            }
            else if ($(this).hasClass("dod")) {
                row["id"] = $(this).attr("dodid");
            }
            else if ($(this).hasClass("newdod")) {
                row["title"] = $(this).find(".newdodTitle").val();
                row["text"] = $(this).find(".newdodDescription").val();
            }

            dod.push(row);
        }
    });

    return dod;
}

KB.on('dom.ready', function () {
    $(document).on('click', '.dodNew', function (e) {
        e.preventDefault();

        var el = $(this);
        var url = el.attr('href');

        KB.http.get(url).success(function (data) {
            $(data).insertBefore(el.closest("tr"));
        });

        hideSingleNewRow();
    });

    $(document).on('input', '.dodInput', function (e) {
        e.preventDefault();
        resizeEvent(e);
    });

    $(document).on('click', '.newdodTrash', function (e) {
        e.preventDefault();
        this.closest(".newdod").remove();
        showSingleNewRow();
    });

    $(document).on('click', '.editdodTrash', function (e) {
        e.preventDefault();
        var el = $(this);
        el.closest("tr").next().show();
        el.closest("tr").remove();
        showSingleNewRow();
    });

    $(document).on('click', '.dodEdit', function (e) {
        e.preventDefault();

        var el = $(this);
        var url = el.attr('href');

        KB.http.get(url).success(function (data) {
            var tr = el.closest("tr");
            tr.hide();
            $(data).insertBefore(tr);
        });
    });

    $(document).on('click', '.dodTrash', function (e) {
        e.preventDefault();

        var dodJson = {};
        dodJson["ids"] = [];

        for (const selecteddod of [...document.getElementsByClassName('dod-selected')]) {
            dodJson["ids"].push(selecteddod.getAttribute("dodid"));

            selecteddod.remove();
        }

        if (dodJson["ids"]) {
            const link = '?controller=DefinitionOfDoneController&action=trash&plugin=DefinitionOfDone';
            KB.http.postJson(link, dodJson)
        }

        showSingleNewRow();
    });

    $(document).on('click', '.dodSave', function (e) {
        e.preventDefault();

        let dodJson = {};
        dodJson['entries'] = serializedodtable();
        dodJson['task_id'] = e.target.getAttribute("taskid");

        const link = '?controller=DefinitionOfDoneController&action=save&plugin=DefinitionOfDone';
        KB.http.postJson(link, dodJson).success(function (newtable) {
            var rows = $(".dod-table").find("tbody").find("tr");
            rows.remove();
            $(".dod-table").find("tbody").append($(newtable));
        });
    });

    $(document).on('click', '.dodStateToggle', function (e) {
        e.preventDefault();

        let el = $(this);
        let url = el.attr('href');

        let icon = el.find('i');
        icon.toggleClass("fa-square-o");
        icon.toggleClass("fa-check-square-o");

        KB.http.get(url);
    });

    $(document).on('click', '.dodSelect', function (e) {
        e.preventDefault();
        this.classList.toggle("fa-square-o");
        this.classList.toggle("fa-check-square-o");

        this.closest(".dod").classList.toggle("dod-selected");
    });

    function dodsavePosition(dodid, position) {
        var url = $(".dod-table").data("save-position-url");

        KB.http.postJson(url, {
            "dod_id": dodid,
            "position": position
        });
    }

    function dodbootstrap() {
        $(".dod-draggable-row-handle").mouseenter(function () {
            $(this).parent().parent().addClass("draggable-item-hover");
        }).mouseleave(function () {
            $(this).parent().parent().removeClass("draggable-item-hover");
        });

        $(".dod-table tbody").sortable({
            forcePlaceholderSize: true,
            handle: ".dod-draggable-row-handle",
            helper: function (e, ui) {
                ui.children().each(function () {
                    $(this).width($(this).width());
                });

                return ui;
            },
            stop: function (event, ui) {
                var dod = ui.item;
                dod.removeClass("draggable-item-selected");

                if (!dod.hasClass("newdod")) {
                    dodsavePosition(dod.attr("dodid"), dod.index() + 1);
                }
            },
            start: function (event, ui) {
                ui.item.addClass("draggable-item-selected");
            }
        });
    }

    KB.on('dom.ready', dodbootstrap);
});

function resizeEvent(event) {
    resize(event.target);
}

function resize(element) {
    element.style.height = ""; // resets element.scrollHeight to the current necessary height
    element.style.height = element.scrollHeight + "px";
}