$('#add-questions').click(function(){
    const index = +$('#widgets-counter').val();

    const tmpl = $('#quiz_quizQuestions').data('prototype').replace(/__name__/g, index);

    $('#quiz_quizQuestions').append(tmpl);

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
    const count = +$('#quiz_quizQuestions div.form-group').length;
    $('#widgets-counter').val(count);
}

updateCounter();

handleDeletButtons();
