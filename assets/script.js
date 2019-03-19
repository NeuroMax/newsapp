$(document).ready(function (e) {
    $('.action-delete').click(function (e) {
        if (!confirm('Вы уверены что хотите удалить запись?')) e.preventDefault();
    });
});