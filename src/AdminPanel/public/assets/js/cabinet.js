var IsAsyncPageLoading = false;

function LoadPageAsync(URL, container)
{
    if(IsAsyncPageLoading) return;

    IsAsyncPageLoading = true;

    if(URL.includes(MVCRoot + "/auth"))
        window.location = URL;
    
    container.object.parentNode.querySelectorAll("a").forEach(item => {
        item.classList.remove("active"); 
    });

    container.object.parentNode.querySelectorAll('a[href="' + URL + '"], a[href="' + URL.substring(0, URL.lastIndexOf('/')) + '"]').forEach(item => {
        item.classList.add("active");
    });

    transition.begin(container.object, [
        'opacity 1 0 100ms'
    ]);

    setTimeout(function() {
        window.document.dispatchEvent(new Event("DOMContentRebuilding", {
            bubbles: true,
            cancelable: true
        }));

        fetch(URL)
        .then(async function(response) {
            var responseText = await response.text();
            return {url: response.url, content: responseText};
        })
        .then(function(data) {
            parser = new DOMParser();
            doc = parser.parseFromString(data.content, "text/html");

            const title = doc.querySelector('title').innerText ?? "Undefined title";
            window.history.pushState(data.url, title, data.url);

            const updatedContainerData = doc.querySelector(container.name);
            const html = updatedContainerData != null ? updatedContainerData.innerHTML : location.reload();

            document.title = title;

            if(html != undefined)
            {
                container.object.innerHTML = html;

                window.document.dispatchEvent(new Event("DOMContentRebuilded", {
                    bubbles: true,
                    cancelable: true
                }));
    
                transition.begin(container.object, [
                    'opacity 0 1 100ms'
                ]);
                anix.Init(container.object.querySelectorAll('.anix'));
            }

            IsAsyncPageLoading = false;
        })
        .catch(function(err) {  
            alert('Failed to fetch page: ', err);  
        });
    }, 100);
}

function GenerateContainerObject(containerName) {
    return {
        name: containerName,
        object: document.querySelector(containerName)
    }
}

function ChangeUITheme(theme) {
    if(UITheme == theme) return;
    fetch(MVCRoot + '/settings/switchTheme/?theme=' + theme)
    .then(async function(response) {
        await response.text();
        transition.begin(document.body, [
            'opacity 1 0 300ms'
        ]);
        window.location.reload();
    })
    .catch(function(err) {  
        alert('Failed to fetch page: ', err);  
    });
}

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

var botUsersSearchQuery = ''; 
var botUsersSearchQueryIsNew = false;
var botUsersSearchBusy = false;
var botUsersSearchChecksum = '';

document.addEventListener("DOMContentLoaded", function(event)
{
    const defaultContainer = GenerateContainerObject('.cabinet .page-content-wrapper');

	document.addEventListener('click', function (e)
    {
        var link = e.target.closest("a");
        if(link && link.classList.contains("async"))
        {
            e.preventDefault();

            const URL = link.getAttribute("href");
            var currentURL = document.location.pathname;
            
            var containerAttr = link.getAttribute('data-reload');
            var container = containerAttr != null ? GenerateContainerObject(containerAttr) : defaultContainer;

            if(URL != currentURL)
            {
                lastLogsChecksum = '';
                lastRequestLogsChecksum = '';
                LoadPageAsync(URL, container);
            }
        }

        if(e.target.closest(".theme-switch .theme-wrapper.white"))
        {
            ChangeUITheme('white');
        };

        if(e.target.closest(".theme-switch .theme-wrapper.dark"))
        {
            ChangeUITheme('dark');
        };

        if(e.target.closest(".users-list .user .delete"))
        {
            var deleteButton = e.target.closest(".users-list .user .delete");

            if(!deleteButton.disabled)
            {
                var modalObject = document.getElementById("deleteUserModal");
                var myModal = new bootstrap.Modal(modalObject, {})
                setTimeout(function() {
                    modalObject.querySelector('#DeleteUserID').value = deleteButton.getAttribute('data-id');
                    myModal.show();
                }, 100);
            }
        }

        if(e.target.closest(".users-list .add-user"))
        {
            var myModal = new bootstrap.Modal(document.getElementById("addUserModal"), {})
            setTimeout(function() {
                myModal.show();
            }, 100);
        }

        if(e.target.closest(".users-list .user .edit"))
        {
            var editButton = e.target.closest(".users-list .user .edit");

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
        }

        if(e.target.closest(".app-logs .clear-logs"))
        {
            var fab = e.target.closest(".app-logs .clear-logs");

            if(!fab.disabled)
            {
                var myModal = new bootstrap.Modal(document.getElementById("clearLogsModal"), {})
                setTimeout(function() {
                    myModal.show();
                }, 100);
            }
        }

        if(e.target.closest(".bot-users .search-bar .cancel-search"))
        {
            document.querySelector('.bot-users .search-bar input').value = '';
            document.querySelector('.bot-users .search-bar .cancel-search').disabled = true;
            document.querySelector('.bot-users .search-bar .search').disabled = false;

            botUsersSearchQuery = '';
            botUsersSearchQueryIsNew = true;
        }

        if(e.target.closest(".bot-users .search-bar .search"))
        {
            document.querySelector('.bot-users .search-bar input').focus();
        }

        if(e.target.closest(".copy-to-buffer"))
        {
            var copyButton = e.target.closest(".copy-to-buffer");

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

        }

	});

    document.addEventListener('input', function (e) {
        if(e.target.closest('.bot-users .search-bar input')) {
            var searchInput = document.querySelector('.bot-users .search-bar input');
            
            if(searchInput.value.length > 40)
            {
                searchInput.value = searchInput.value.slice(0, 40);
            }

            if(searchInput.value != botUsersSearchQuery)
            {
                botUsersSearchQuery = searchInput.value;
                botUsersSearchQueryIsNew = true;
            }

            document.querySelector('.bot-users .search-bar .cancel-search').disabled = searchInput.value == '';
            document.querySelector('.bot-users .search-bar .search').disabled =  searchInput.value != '';
        }
    });

    window.onpopstate = function(e) {
        var newURL = e.state;
        if(newURL !== null)
        {
			LoadPageAsync(newURL, defaultContainer);
        }
    };
});

var pageHasRequestLogs = false;
var lastRequestLogsChecksum = '';

var pageHasLogs = false;
var lastLogsChecksum = '';

var pageHasBotUsers = false;

document.addEventListener("DOMContentRebuilded", function(event) {
    Prism.highlightAll();

    pageHasRequestLogs = document.querySelector('.request-logs') != null;
    pageHasLogs = document.querySelector('.log-raw') != null;
    pageHasBotUsers = document.querySelector('.bot-users') != null;

    botUsersSearchQuery = ''; 
    botUsersSearchQueryIsNew = false;
    botUsersSearchBusy = false;
    botUsersSearchChecksum = '';

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

    if(document.querySelector('tr.highlight'))
    {
        document.querySelector('tr.highlight').scrollIntoView({ behavior: 'smooth', block: 'center' });
        history.replaceState && history.replaceState(
            null, '', location.pathname + location.search.replace(/[\?&]highlight=[^&]+/, '').replace(/^&/, '?') + location.hash
        );
    }
});

setInterval(function() {
    if(pageHasBotUsers)
    {
        if(botUsersSearchQueryIsNew && !botUsersSearchBusy)
        {
            botUsersSearchQueryIsNew = false;

            searchResults = document.querySelector('.bot-users .search-results');

            if(botUsersSearchQuery != '')
            {
                botUsersSearchBusy = true;

                if(searchResults.classList.contains('hidden'))
                {
                    searchResults.classList.remove('hidden');

                    setTimeout(function() {
                        searchResults.classList.remove('faded');
                    }, 10);
                }

                searchResults.classList.add('loading');

                fetch(MVCRoot + '/fordevelopers/searchBotUsers/?query=' + botUsersSearchQuery)
                .then(function(response) {
                    return response.json();
                })
                .then(function(response) {
                    if(response.ok)
                    {
                        searchResults.classList.remove('loading');

                        if(response.checksum != botUsersSearchChecksum)
                        {
                            searchResults.querySelector('.items').innerHTML = '';

                            if(response.results.length > 0)
                            {
                                searchResults.classList.remove('no-data');
    
                                response.results.forEach(result => {
                                    var searchResultLink = document.createElement("a");
                                    searchResultLink.href = MVCRoot + '/fordevelopers/botusers/?page=' + result.SearchResultPage + '&highlight=' + result.ID;
                                    searchResultLink.className = 'async';
                                    searchResultLink.setAttribute('data-reload', '.settings-page .sub-page');
    
                                    var searchResultDiv = document.createElement("div");
                                    searchResultDiv.className = 'search-result';
                                    searchResultDiv.innerHTML = '<h3>' + result.FullName ?? result.UserName ?? 'Без имени' + '</h3>';
                                    searchResultDiv.innerHTML += '<p><b>ID чата: </b>' + result.ChatID + '</p>';
    
                                    searchResultLink.appendChild(searchResultDiv);
        
                                    searchResults.querySelector('.items').appendChild(searchResultLink);
                                });
                            }
                            else
                            {
                                searchResults.classList.add('no-data');
                            }

                            botUsersSearchChecksum = response.checksum;
                        }
                    }
    
                    botUsersSearchBusy = false;
                })
                .catch(function(err) {  
                    searchResults.classList.remove('loading');
                    console.error('Failed to fetch logs: ', err);  
                    botUsersSearchBusy = false;
                });
            }
            else
            {
                if(!searchResults.classList.contains('hidden'))
                {
                    botUsersSearchBusy = true;
                    searchResults.classList.add('faded');

                    setTimeout(function() {
                        searchResults.classList.add('hidden');
                        botUsersSearchBusy = false;
                        botUsersSearchChecksum = '';

                        searchResults.querySelector('.items').innerHTML = '';
                        searchResults.classList.remove('no-data');
                    }, 200);
                }
            }
        }
    }
}, 50);

setInterval(function() {
    if(pageHasRequestLogs)
    {
        fetch(MVCRoot + '/requests/getData' + (lastRequestLogsChecksum != '' ? '/?checksum=' + lastRequestLogsChecksum : ''))
        .then(function(response) {
            return response.json();
        })
        .then(function(response) {
            if(response.ok)
            {
                if(response.checksum != lastRequestLogsChecksum)
                {
                    var requestLogs = document.querySelector('.request-logs');
                    var requestLogsItems = requestLogs.querySelector('.items');

                    requestLogs.querySelector('.loading').classList.add('hidden');
                    
                    setTimeout(function() {
                        requestLogsItems.innerHTML = "";

                        response.logs.forEach(log => {
                            var logDiv = document.createElement("div");
                            logDiv.className = 'log';
                            logDiv.innerHTML = '<p><b>ID: </b>' + log.ID;
                            logDiv.innerHTML += '<p><b>ID пользователя (Telegram): </b>' + log.ChatID;
                            logDiv.innerHTML += '<p><b>Дата: </b>' + log.Date;
                            logDiv.innerHTML += '<p><b>Код запроса: </b><span class="code">' + log.RequestCode + '</span>';
                            logDiv.innerHTML += '<p><b>Код ответа: </b><span class="code">' + log.ResponseCode + '</span>';
    
                            if(log.RequestError != null)
                                logDiv.innerHTML += '<p><b>Ошибка запроса: </b>' + log.RequestError;
            
                            if(log.ResponseError != null)
                                logDiv.innerHTML += '<p><b>Ошибка ответа: </b>' + log.ResponseError;
                
                            if(log.Request != null)
                                logDiv.innerHTML += '<p><b>Запрос: </b><pre><code class="language-javascript">' + log.Request + '</code></pre>';
    
                            if(log.Response != null)
                                logDiv.innerHTML += '<p><b>Ответ: </b><pre><code class="language-javascript">' + log.Response + '</code></pre>';
    
                            requestLogsItems.appendChild(logDiv);
    
                            Prism.highlightAll();
                        });
    
                        setTimeout(function() {
                            requestLogsItems.classList.remove('faded');
                        }, 10);
    
                        lastRequestLogsChecksum = response.checksum;
                    }, 10);
                }
            }
        })
        .catch(function(err) {  
            console.error('Failed to fetch request logs: ', err);  
        });
    }
    else
    {
        lastRequestLogsChecksum = '';
    }

    if(pageHasLogs)
    {
        fetch(MVCRoot + '/fordevelopers/getLogsRAW' + (lastLogsChecksum != '' ? '/?checksum=' + lastLogsChecksum : ''))
        .then(function(response) {
            return response.json();
        })
        .then(function(response) {
            if(response.ok)
            {
                if(response.checksum != lastLogsChecksum)
                {
                    var logsWrapper = document.querySelector('.app-logs');
                    var logs = logsWrapper.querySelector('.log-raw');
                    var logsFab = logsWrapper.querySelector('.clear-logs');
                    
                    logs.innerHTML = response.raw;
                    
                    if(response.raw == '')
                    {
                        logsWrapper.querySelector('.empty').classList.remove('hidden');
                        logsFab.disabled = true;
                    }
                    else
                    {
                        logsWrapper.querySelector('.empty').classList.add('hidden');
                        logsFab.disabled = false;
                    }

                    setTimeout(function()
                    {
                        if(logs.classList.contains('faded'))
                        {
                            logs.classList.remove('faded');
                            logsWrapper.querySelector('.loading').classList.add('hidden');
                        }
                        var objDiv = document.querySelector('.page-content-wrapper');
                        objDiv.scrollTo(0, objDiv.scrollHeight);
                    }, 10);

                    lastLogsChecksum = response.checksum;
                }
            }
        })
        .catch(function(err) {  
            console.error('Failed to fetch logs: ', err);  
        });
    }
    else
    {
        lastLogsChecksum = '';
    }
}, 1000);