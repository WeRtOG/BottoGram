class AsyncEventHandler
{constructor(selector,action){this.selector=selector;this.action=action}}
class AsyncEvents
{clickEventHandlers=[];inputEventHandlers=[];OnClick(selector,action){this.clickEventHandlers.push(new AsyncEventHandler(selector,action))}
OnInput(selector,action){this.inputEventHandlers.push(new AsyncEventHandler(selector,action))}
TriggerEventHandler(e,handler){var element=e.target.closest(handler.selector);if(element){handler.action(e,element)}}
Init(){var _aeInstance=this;document.addEventListener('click',function(e){_aeInstance.clickEventHandlers.forEach(handler=>{_aeInstance.TriggerEventHandler(e,handler)})});document.addEventListener('input',function(e){_aeInstance.inputEventHandlers.forEach(handler=>{_aeInstance.TriggerEventHandler(e,handler)})})}};var isAsyncPageLoading=!1;var defaultContainer=null;class AsyncNavigation
{static GetParentURL(URL){return URL.substring(0,URL.lastIndexOf('/'))}
static LoadPageAsync(URL,container){if(isAsyncPageLoading)return;isAsyncPageLoading=!0;if(URL.includes(MVCRoot+"/auth"))
window.location=URL;container.object.parentNode.querySelectorAll("a").forEach(item=>{item.classList.remove("active")});var activeLinksURL=URL.replace(/(\/|)(\&|\?)(.*?)[=](.*)/g,'');var activeLinksParentURL=AsyncNavigation.GetParentURL(activeLinksURL);container.object.parentNode.querySelectorAll('a[href="'+URL+'"], a[href="'+activeLinksURL+'"], a[href="'+activeLinksParentURL+'"]').forEach(item=>{item.classList.add("active")});transition.begin(container.object,['opacity 1 0 80ms']);setTimeout(function(){window.document.dispatchEvent(new Event("DOMContentRebuilding",{bubbles:!0,cancelable:!0}));fetch(URL).then(async function(response){var responseText=await response.text();return{url:response.url,content:responseText}}).then(function(data){var parser=new DOMParser();var doc=parser.parseFromString(data.content,"text/html");const title=doc.querySelector('title').innerText??"Undefined title";window.history.pushState(data.url,title,data.url);const updatedContainerData=doc.querySelector(container.name);const html=updatedContainerData!=null?updatedContainerData.innerHTML:location.reload();if(updatedContainerData!=null){updatedContainerData.querySelectorAll('script').forEach(script=>{eval(script.innerHTML)})}
document.title=title;if(html!=undefined){container.object.innerHTML=html;window.document.dispatchEvent(new Event("DOMContentRebuilded",{bubbles:!0,cancelable:!0}));transition.begin(container.object,['opacity 0 1 80ms']);anix.Init(container.object.querySelectorAll('.anix'))}
isAsyncPageLoading=!1}).catch(function(err){console.error(err);alert('Failed to fetch page: ',err)})},80)}
static ReloadCurrentPageAsync(){AsyncNavigation.LoadPageAsync(window.location.href,AsyncNavigation.GenerateContainerObject('.cabinet'))}
static GenerateContainerObject(containerName){return{name:containerName,object:document.querySelector(containerName)}}
static OnDocumentLoaded(){defaultContainer=AsyncNavigation.GenerateContainerObject('.cabinet .page-content-wrapper')}
static OnDocumentClick(e){var link=e.target.closest("a");if(link&&link.classList.contains("async")){e.preventDefault();const URL=link.getAttribute("href");var currentURL=document.location.pathname;var containerAttr=link.getAttribute('data-reload');var container=containerAttr!=null?AsyncNavigation.GenerateContainerObject(containerAttr):defaultContainer;if(URL!=currentURL){AsyncNavigation.LoadPageAsync(URL,container)}}}
static OnPopState(e){var newURL=e.state;if(newURL!==null){AsyncNavigation.LoadPageAsync(newURL,defaultContainer)}}};class PageScripts
{static GetPublicURL(path){return location.protocol+'//'+location.host+path}
static GetCleanScriptURL(url){url=url.trim();return url.split('?')[0]}
static LoadPageScript(path){var url=this.GetPublicURL(path.trim());if(!Array.from(document.querySelectorAll('script')).some(elm=>this.GetCleanScriptURL(elm.src)==this.GetCleanScriptURL(url))){let script=document.createElement('script')
script.src=url
document.getElementsByTagName('head')[0].appendChild(script)}}}