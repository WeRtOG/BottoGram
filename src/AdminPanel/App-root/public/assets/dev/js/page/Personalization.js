var mousePressed = false;

asyncEvents.OnClick('.cabinet .personalization .color-select .color', function(e, colorButton) {
    var color = window.getComputedStyle(colorButton).backgroundColor;
    Personalization.ChangeUIAccentColor(color);
});

document.addEventListener('mousedown', function(e) {
    mousePressed = true;
});
document.addEventListener('mouseup', function(e) {
    mousePressed = false;
});

document.addEventListener('mousemove', function(e) {
    if(e.target.closest(".cabinet .personalization .color-select .color") && mousePressed)
    {
        var colorButton = e.target.closest(".cabinet .personalization .color-select .color");
        var color = window.getComputedStyle(colorButton).backgroundColor;

        Personalization.ChangeUIAccentColor(color);
    }
});

document.addEventListener("DOMContentRebuilded", function(event) {
    if(document.querySelector('.cabinet .personalization .color-select'))
        Personalization.ShowActiveUIAccentColorInPersonalization();
});

if(document.querySelector('.cabinet .personalization .color-select'))
    Personalization.ShowActiveUIAccentColorInPersonalization();