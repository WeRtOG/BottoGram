var pageHasRequestLogs = document.querySelector('.request-logs') != null;
var lastRequestLogsChecksum = '';

document.addEventListener("DOMContentRebuilded", function(event) {
    pageHasRequestLogs = document.querySelector('.request-logs') != null;
    lastRequestLogsChecksum = '';
});

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
                        requestLogsItems.innerHTML = '';

                        response.logs.forEach(log => {
                            var logDiv = document.createElement("div");
                            logDiv.className = 'log';
                            logDiv.innerHTML = '<p><b>ID: </b>' + log.ID;

                            if(log.UserURL != null)
                                logDiv.innerHTML += '<p><b>ID пользователя (Telegram): </b><a href="' + log.UserURL + '" class="async">' + log.ChatID + '</a>';
                            else
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
}, 1000);