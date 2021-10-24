asyncEvents.OnClick('.users-list .user .delete', function(e, deleteButton) {
    if(!deleteButton.disabled)
    {
        var modalObject = document.getElementById("deleteUserModal");
        var myModal = new bootstrap.Modal(modalObject, {})
        setTimeout(function() {
            modalObject.querySelector('#DeleteUserID').value = deleteButton.getAttribute('data-id');
            myModal.show();
        }, 100);
    }
});

asyncEvents.OnClick('.users-list .user .edit', function(e, editButton) {
    if(!editButton.disabled)
    {
        var modalObject = document.getElementById("editUserModal");
        var myModal = new bootstrap.Modal(modalObject, {})
        setTimeout(function() {
            var flags = JSON.parse(editButton.getAttribute('data-flags'));
            modalObject.querySelector('#EditUserID').value = editButton.getAttribute('data-id');
            modalObject.querySelector('#EditUserLogin').innerHTML = editButton.getAttribute('data-login');
            modalObject.querySelector('[name=CanManageUsers]').checked = flags[0];
            modalObject.querySelector('[name=CanChangeConfig]').checked = flags[1];
            modalObject.querySelector('[name=CanViewRequestLogs]').checked = flags[2];
            myModal.show();
        }, 100);
    }
});