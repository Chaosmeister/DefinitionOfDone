
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
        if ($(this).hasClass("editdod")) {
            var row = {};

            row["id"] = $(this).attr("dodid");
            row["title"] = $(this).find(".newdodTitle").val();
            row["text"] = $(this).find(".newdodDescription").val();

            dod.push(row);
        }
        else if ($(this).hasClass("dod")) {
            var row = {};

            row["id"] = $(this).attr("dodid");

            dod.push(row);
        }
        else if ($(this).hasClass("newdod")) {
            var row = {};

            row["title"] = $(this).find(".newdodTitle").val();
            row["text"] = $(this).find(".newdodDescription").val();

            dod.push(row);
        }
    });

    return dod;
}

const getJsonUpload = () =>
    new Promise(resolve => {
        const inputFileElement = document.createElement('input')
        inputFileElement.setAttribute('type', 'file')
        inputFileElement.setAttribute('multiple', 'false')
        inputFileElement.setAttribute('accept', '.json')

        inputFileElement.addEventListener(
            'change',
            async (event) => {
                const { files } = event.target
                if (!files) {
                    return
                }

                const filePromises = [...files].map(file => file.text())

                resolve(await Promise.all(filePromises))
            },
            false,
        )
        inputFileElement.click()
    })

KB.on('dom.ready', function () {
    $(document).on('click', '.dodNew', function (e) {
        e.preventDefault();

        var el = $(this);
        var url = el.attr('href');

        KB.http.get(url).success(function (data) {
            $(data).insertAfter(el.closest("tr"));
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
        dodJson['task_id'] = e.target.getAttribute("taskid");
        let selectedEntries = [...document.getElementsByClassName('dod-selected')];
        if (selectedEntries.length == 0) {
            alert("No entry selected\nUse the checkbox to select one or multiple entries");
            return;
        }

        for (const selecteddod of selectedEntries) {
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

    $(document).on('click', '.dodExport', function (e) {
        e.preventDefault();

        var el = $(this);
        var url = el.attr('href');

        KB.http.get(url).success(function (data) {
            let downloadObject = document.createElement("a");
            downloadObject.download = "dod_" + Date.now() + ".json";
            downloadObject.href = URL.createObjectURL(new Blob([JSON.stringify(data, null, 2)]));
            downloadObject.click();
        });
    });

    $(document).on('click', '.dodImport', async function (e) {
        e.preventDefault();

        var el = $(this);
        var url = el.attr('href');
        const json = JSON.parse(await getJsonUpload());

        KB.http.postJson(url, json).success(function (data) {
        });
    });

    $(document).on('click', '.dod-separator-button', function (e) {
        e.preventDefault();

        var el = $(this);

        var icon = el.find(".dod-separator-icon");
        icon.toggleClass("fa-compress");
        icon.toggleClass("fa-expand");

        var parent = el.parent(".dod-separator");
        if (icon.hasClass("fa-expand")) {
            parent.nextUntil(".dod-separator, .newdodrow").hide();
        }
        else {
            parent.nextUntil(".dod-separator, .newdodrow").show();
        }
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

    function dodReorder() {
        let main = $(".dodmain");
        let li = main.next("li");
        if (li.length != 0) {
            main.insertAfter(li);
        }
    }

    dodbootstrap();
    dodReorder();
});

function resizeEvent(event) {
    resize(event.target);
}

function resize(element) {
    element.style.height = ""; // resets element.scrollHeight to the current necessary height
    element.style.height = element.scrollHeight + "px";
}