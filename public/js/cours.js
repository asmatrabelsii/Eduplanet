$('#add-modules').click(function(){
    const index = +$('#widgets-counter').val();

    const tmpl = $('#cours_modules').data('prototype').replace(/__name__/g, index);

    $('#cours_modules').append(tmpl);

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
    const count = +$('#cours_modules div.form-group').length;
    $('#widgets-counter').val(count);
}

updateCounter();

handleDeletButtons();
