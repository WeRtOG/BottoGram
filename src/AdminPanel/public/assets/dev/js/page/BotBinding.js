function ValidateBotModeForm(form)
{
    webhookEnabled = form.querySelector('#WebhookEnabled').checked ?? false;
    webhookURLInput = form.querySelector('#WebhookURL');
    webhookURLInputWrapper = form.querySelector('.webhook-input-wrapper');

    if(webhookURLInput != null)
    {
        webhookURLInput.required = webhookEnabled;
        webhookURLInput.disabled = !webhookEnabled;

        if(webhookEnabled)
            webhookURLInputWrapper.classList.remove('disabled');
        else
            webhookURLInputWrapper.classList.add('disabled');
    }

    ValidateForm(form);
}

function BotBindingUpdateData()
{
    var botBindingObject = document.querySelector('.bot-binding');
    if(botBindingObject)
    {
        fetch(MVCRoot + '/binding/getBotInfoFromTelegram')
        .then(function(response) {
            return response.json();
        })
        .then(function(result) {
            if(result.ok)
            {
                botBindingObject.querySelector('.bot-info').classList.remove('disabled');
                
                botBindingObject.querySelector('.bot-info .bot-id').innerHTML = result.data.MainInfo.ID;
                botBindingObject.querySelector('.bot-info .bot-username').innerHTML = '@' + result.data.MainInfo.UserName;
                botBindingObject.querySelector('.bot-info .bot-username').href = 'https://t.me/' + result.data.MainInfo.UserName;
                botBindingObject.querySelector('.bot-info .groups-flag').innerHTML = result.data.MainInfo.CanJoinGroups ? 'Да' : 'Нет';
                botBindingObject.querySelector('.bot-info .read-group-messages-flag').innerHTML = result.data.MainInfo.CanReadAllGroupMessages ? 'Да' : 'Нет';
                botBindingObject.querySelector('.bot-info .inline-flag').innerHTML = result.data.MainInfo.SupportsInlineQueries ? 'Да' : 'Нет';
    
                webhookURLInputWrapper = botBindingObject.querySelector('.webhook-input-wrapper');
    
                var webhookURLNotEmpty = result.data.WebhookInfo.Url != '' || webhookURLInputWrapper.querySelector('.error') != null;
    
                if(webhookURLNotEmpty)
                    webhookURLInputWrapper.classList.remove('disabled');
                else
                    webhookURLInputWrapper.classList.add('disabled');
    
                botBindingObject.querySelector('#WebhookEnabled').checked = webhookURLNotEmpty;
    
                var autoURL = location.protocol + '//' + location.host + MVCRoot.replace(/\\/g, '/').replace(/\/[^/]*\/?$/, '') + '/hook.php';
    
                botBindingObject.querySelector('#WebhookURL').value = result.data.WebhookInfo.Url != '' ? result.data.WebhookInfo.Url : autoURL;
                botBindingObject.querySelector('#WebhookURL').disabled = !webhookURLNotEmpty;
            }
            else
            {
    
            }
        })
        .catch(function(err) {  
            console.error('Failed to fetch telegram bot info: ', err);  
        });
    }
}
document.addEventListener("DOMContentRebuilded", function(event) {
    BotBindingUpdateData();
});

BotBindingUpdateData();