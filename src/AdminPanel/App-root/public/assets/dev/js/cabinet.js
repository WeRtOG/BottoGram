function ValidateForm(form)
{
    var isFormValid = true;

    fields = form.querySelectorAll('.form-control, .form-check-input');

    fields.forEach(field => {
        required = field.required;
        if(required)
        {
            var value = field.value.trim();
            isFormValid = isFormValid && value != '';
        }
    });

    form.querySelector('[type=submit]').disabled = !isFormValid;
}

function TriggerModal(modalId)
{
    var triggeredModal = new bootstrap.Modal(document.getElementById(modalId), {})
    setTimeout(function() {
        triggeredModal.show();
    }, 100);
}

document.addEventListener("DOMContentLoaded", function(event)
{    
    AsyncNavigation.OnDocumentLoaded();
    
    asyncEvents.Init();

    document.addEventListener('click', AsyncNavigation.OnDocumentClick);

    asyncEvents.OnClick('.copy-to-buffer', function(e, copyButton) {
        if(!copyButton.disabled)
        {
            var dataToCopy = copyButton.getAttribute('data-to-copy') ?? null;
            var titleAfterCopy = copyButton.getAttribute('data-title-after-copy') ?? null;

            if(dataToCopy != null)
            {
                navigator.clipboard.writeText(dataToCopy);
                
                if(titleAfterCopy != null)
                {
                    copyButton.querySelector('.title').innerHTML = titleAfterCopy;
                    copyButton.disabled = true;
                }
            }
        }
    });

    window.onpopstate = AsyncNavigation.OnPopState;
});

document.addEventListener("DOMContentRebuilded", function(event) {
    Prism.highlightAll();
});