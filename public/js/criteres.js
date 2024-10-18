
$('#add-criteres').click(function(){
    const index = +$('#widget-counter').val();

    const tmpl = $('#cours_criteres').data('prototype').replace(/__name__/g, index);

    $('#cours_criteres').append(tmpl);

    $('#widget-counter').val(index	+ 1);

    handleDeletButtons();
});

function handleDeletButtonsCriteres(){
    $('button[data-action="delete"]').click(function(){
        const target = $(this).data('target');
        $(target).remove();
    });
}

function updateCounterCriteres(){
    const count = +$('#cours_criteres div.form-group').length;
    $('#widget-counter').val(count);
}

updateCounterCriteres();

handleDeletButtonsCriteres();