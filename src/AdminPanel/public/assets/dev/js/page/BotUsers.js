var pageHasBotUsers = document.querySelector('.bot-users') != null;

var botUsersSearchQuery = ''; 
var botUsersSearchQueryIsNew = false;
var botUsersSearchBusy = false;
var botUsersSearchChecksum = '';

asyncEvents.OnClick('.bot-users .search-bar .cancel-search', function(e, element) {
    document.querySelector('.bot-users .search-bar input').value = '';
    document.querySelector('.bot-users .search-bar .cancel-search').disabled = true;
    document.querySelector('.bot-users .search-bar .search').disabled = false;

    botUsersSearchQuery = '';
    botUsersSearchQueryIsNew = true;
});

asyncEvents.OnClick('.bot-users .search-bar .search', function(e, element) {
    document.querySelector('.bot-users .search-bar input').focus();
});

asyncEvents.OnInput('.bot-users .search-bar input', function(e, searchInput) {
    var searchTrimValue = searchInput.value.trim();
            
    if(searchInput.value.length > 40)
        searchInput.value = searchInput.value.slice(0, 40);

    if(searchTrimValue != botUsersSearchQuery)
    {
        botUsersSearchQuery = searchTrimValue;
        botUsersSearchQueryIsNew = true;
    }

    document.querySelector('.bot-users .search-bar .cancel-search').disabled = searchTrimValue == '';
    document.querySelector('.bot-users .search-bar .search').disabled =  searchTrimValue != '';
});

document.addEventListener("DOMContentRebuilded", function(event) {
    pageHasBotUsers = document.querySelector('.bot-users') != null;

    
    botUsersSearchQuery = ''; 
    botUsersSearchQueryIsNew = false;
    botUsersSearchBusy = false;
    botUsersSearchChecksum = '';

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