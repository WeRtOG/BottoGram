var pageHasLogs=document.querySelector('.log-raw')!=null;var lastLogsChecksum='';asyncEvents.OnClick('.app-logs .clear-logs',function(e,fab){if(!fab.disabled){var myModal=new bootstrap.Modal(document.getElementById("clearLogsModal"),{})
setTimeout(function(){myModal.show()},100)}});document.addEventListener("DOMContentRebuilded",function(event){pageHasLogs=document.querySelector('.log-raw')!=null;lastLogsChecksum=''});setInterval(function(){if(pageHasLogs){fetch(MVCRoot+'/fordevelopers/getLogsRAW'+(lastLogsChecksum!=''?'/?checksum='+lastLogsChecksum:'')).then(function(response){return response.json()}).then(function(response){if(response.ok){if(response.checksum!=lastLogsChecksum){var logsWrapper=document.querySelector('.app-logs');if(logsWrapper!=null){var logs=logsWrapper.querySelector('.log-raw');var logsFab=logsWrapper.querySelector('.clear-logs');logs.innerHTML=response.raw;if(response.raw==''){logsWrapper.querySelector('.empty').classList.remove('hidden');logsFab.disabled=!0}else{logsWrapper.querySelector('.empty').classList.add('hidden');logsFab.disabled=!1}
setTimeout(function(){if(logs.classList.contains('faded')){logs.classList.remove('faded');logsWrapper.querySelector('.loading').classList.add('hidden')}
var objDiv=document.querySelector('.page-content-wrapper');objDiv.scrollTo(0,objDiv.scrollHeight)},10);lastLogsChecksum=response.checksum}}}}).catch(function(err){console.error('Failed to fetch logs: ',err)})}else{lastLogsChecksum=''}},1000)