class PageScripts
{
    static GetPublicURL(path)
    {
        return location.protocol + '//' + location.host + path;
    }

    static GetCleanScriptURL(url)
    {
        url = url.trim();
        return url.split('?')[0];
    }

    static LoadPageScript(path)
    {
        var url = this.GetPublicURL(path.trim());

        if(!Array.from(document.querySelectorAll('script')).some(elm => this.GetCleanScriptURL(elm.src) == this.GetCleanScriptURL(url)))
        {
            let script = document.createElement('script')
            script.src = url
            document.getElementsByTagName('head')[0].appendChild(script)
        }
    }
}