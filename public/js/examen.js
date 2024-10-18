$('#add-questions').click(function(){
    const index = +$('#widgets-counter').val();

    const tmpl = $('#examen_questions').data('prototype').replace(/__name__/g, index);

    $('#examen_questions').append(tmpl);

    $('#widgets-counter').val(index	+ 1);

    handleDeletButtons();
});

function handleDeletButtons(){
    $('button[data-action="delete"]').click(function(){
        const target = $(this).data('target');
        $(target).remove();
    });
}

function updateCounter(){
    const count = +$('#examen_questions div.form-group').length;
    $('#widgets-counter').val(count);
}

updateCounter();

handleDeletButtons();
