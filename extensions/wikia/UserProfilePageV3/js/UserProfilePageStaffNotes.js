var csAjaxUrl = wgServer + wgScript + '?action=ajax';

function loadEditNoteForm() {
    var note = $('#staffNotes .note-content .note-txt');
    note.hide();
    $('#editNote')
        .show()
        .children('textarea')
        .html(note.html())
        .focus();
    $('#staffNotes .note-content').css('display','block');
    $("#staffNotes").hover(function () {
            $('#staffNotes .note-content').css('display','block');
        },
        function () {
            $('#staffNotes .note-content').css('display','block');
        }
    );
}

function noteCancel() {
    $('#staffNotes .note-content .note-txt').show();
    $('#editNote').hide();
    $('#staffNotes .note-content').css('display','none');
    $("#staffNotes").hover(function () {
            $('#staffNotes .note-content').css('display','block');
    },
    function () {
        $('#staffNotes .note-content').css('display','none');
    }
    );
}

function noteSave() {
    var noteTextarea = $('#editNote textarea');
    var pars = 'rs=UserProfilePageAjaxSaveNote&rsargs[]=' + wgPageName + '&rsargs[]=' + noteTextarea.val() ;
    $.ajax({
        url: csAjaxUrl,
        data: pars,
        dataType: "json",
        success: function(result){
            console.log('TEST');
            if (result.info == 'ok' && result.html != '') {
            } else if (result.error != undefined) {
                alert(result.error);
            }
            $('#staffNotes .note-content .note-txt').html(noteTextarea.val());
            noteCancel();
        }
    });

}