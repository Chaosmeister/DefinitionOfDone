KB.on('dom.ready', function () {
    $(document).on('click', '.dodNew', function (e) {
        var el = $(this);
        var url = el.attr('href');

        e.preventDefault();

        $.ajax({
            cache: false,
            url: url,
            success: function (data) {
                var newTr = document.createElement("tr");
                newTr.classList.add("newdod")
                newTr.setHTML(data);
                el[0].parentElement.parentElement.parentElement.insertBefore(newTr, el[0].parentElement.parentElement);
            }
        });


        e.preventDefault();
    });

    $(document).on('input', '.dodInput', function (e) {
        e.preventDefault();
        resizeEvent(e);
    });

    $(document).on('click', '.newdodTrash', function (e) {
        e.preventDefault();
        this.closest(".newdod").remove();
    });

    $(document).on('click', '.dodTrash', function (e) {
        e.preventDefault();

        var dodJson = {};

        for (const selecteddod of [...document.getElementsByClassName('dod-selected')]) {
            if (dodJson["ids"]) {
                dodJson["ids"] += ", " + selecteddod.getAttribute("dodid");
            }
            else {
                dodJson["ids"] = selecteddod.getAttribute("dodid");
            }
            selecteddod.remove();
        }

        if (dodJson["ids"]) {
            const link = '?controller=DefinitionOfDoneController&action=trash&plugin=DefinitionOfDone';
            if (KB.http.postJson(link, dodJson)) {
            }
        }
    });

    $(document).on('click', '.dodSave', function (e) {
        e.preventDefault();

        var dodJson = {};

        dodJson['task_id'] = e.target.id;

        var Newdods = dodJson['newdods'] = {};
        var dodEdits = dodJson['dodEdits'] = {};

        var Counter = 1;
        for (const Newdod of [...document.getElementsByClassName('newdod')]) {
            var NewdodJson = {};
            NewdodJson["title"] = Newdod.querySelector(".newdodTitle").value;
            NewdodJson["text"] = Newdod.querySelector(".newdodDescription").value;

            if (NewdodJson["title"]) {
                Newdods[Counter] = NewdodJson;
                Counter++;
            }
        }

        for (const Editeddod of [...document.getElementsByClassName('DefinitionOfDoneEdit')]) {
            dodEdits[Editeddod.id] = Editeddod.value;
        }

        const link = '?controller=DefinitionOfDoneController&action=save&plugin=DefinitionOfDone';
        KB.http.postJson(link, dodJson);
    });

    $(document).on('click', '.js-dod-description-close', function (e) {
        e.preventDefault();
        var el = $(this);

        td = el.closest(".dodDescription")[0];
        var markdown = td.querySelector('.dodResultDisplay');
        markdown.style.display = "";

        td.removeChild(td.querySelector('.dodDescriptionEdit'));
    });

    $(document).on('click', '.dodSelect', function (e) {
        e.preventDefault();
        this.classList.toggle("fa-square-o");
        this.classList.toggle("fa-check-square-o");

        this.closest(".dod").classList.toggle("dod-selected");
    });

    function savePosition(dodid, position) {
        var url = $(".dod-table").data("save-position-url");

        $.ajax({
            cache: false,
            url: url,
            contentType: "application/json",
            type: "POST",
            processData: false,
            data: JSON.stringify({
                "dod_id": dodid,
                "position": position
            })
        });
    }

    function bootstrap() {
        $(".draggable-row-handle").mouseenter(function () {
            $(this).parent().parent().addClass("draggable-item-hover");
        }).mouseleave(function () {
            $(this).parent().parent().removeClass("draggable-item-hover");
        });

        $(".dod-table tbody").sortable({
            forcePlaceholderSize: true,
            handle: "td:first i",
            helper: function (e, ui) {
                ui.children().each(function () {
                    $(this).width($(this).width());
                });

                return ui;
            },
            stop: function (event, ui) {
                var dod = ui.item;
                dod.removeClass("draggable-item-selected");
                savePosition(dod.attr("dodid"), dod.index() + 1);
            },
            start: function (event, ui) {
                ui.item.addClass("draggable-item-selected");
            }
        }).disableSelection();
    }

    KB.on('dom.ready', bootstrap);
    KB.on('dod.reloaded', bootstrap);
});

function resizeEvent(event) {
    resize(event.target);
}

function resize(element) {
    element.style.height = "";
    element.style.height = element.scrollHeight + "px";
}