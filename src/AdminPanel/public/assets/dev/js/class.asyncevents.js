class AsyncEventHandler
{
    constructor(selector, action)
    {
        this.selector = selector;
        this.action = action;
    }
}
class AsyncEvents
{
    clickEventHandlers = [];
    inputEventHandlers = [];

    OnClick(selector, action)
    {
        this.clickEventHandlers.push(new AsyncEventHandler(selector, action));
    }

    OnInput(selector, action)
    {
        this.inputEventHandlers.push(new AsyncEventHandler(selector, action));
    }

    TriggerEventHandler(e, handler)
    {
        var element = e.target.closest(handler.selector);
        if(element)
        {
            handler.action(e, element);
        }
    }

    Init()
    {
        var _aeInstance = this;

        document.addEventListener('click', function(e)
        {
            _aeInstance.clickEventHandlers.forEach(handler => {
                _aeInstance.TriggerEventHandler(e, handler);
            });
        });

        document.addEventListener('input', function(e)
        {
            _aeInstance.inputEventHandlers.forEach(handler => {
                _aeInstance.TriggerEventHandler(e, handler);
            });
        });
    }
}