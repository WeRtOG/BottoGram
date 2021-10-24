var UIAccent = null;
var UIAccentAlt = null;

var UIThemeChanging = false;

class Personalization
{
    static ChangeUITheme(theme, charts)
    {
        UIThemeChanging = true;

        if(UITheme == theme) return;

        UITheme = theme;

        if(document.querySelector('.theme-switch'))
        {
            document.querySelectorAll('.theme-wrapper').forEach(theme => {
                theme.classList.remove('active');
                theme.classList.add('inactive');
            });

            document.querySelector('.theme-wrapper.' + theme).classList.remove('inactive');
            document.querySelector('.theme-wrapper.' + theme).classList.add('active');
        }

        transition.begin(document.body, [
            'opacity 1 0 300ms'
        ]);

        setTimeout(function() {
            document.querySelector('html').classList.add('unaviable');

            if(ThemeAssetsCSS != null)
            {
                ThemeAssetsCSS.forEach(asset => {
                    var stylesheet = document.querySelector(asset.selector);
                    
                    if(stylesheet != null)
                    {
                        stylesheet.href = theme == 'dark' ? asset.darkTheme : asset.whiteTheme;
                    }
                });
    
                document.querySelector('html').classList.remove('dark-theme', 'white-theme');
                document.querySelector('html').classList.add(theme + '-theme');

                Personalization.UpdateApexTheme();
            }
        
            fetch(MVCRoot + '/settings/switchTheme/?theme=' + theme)
            .then(async function(response) {
                await response.text();
                setTimeout(function() {
                    document.querySelector('html').classList.remove('unaviable');
                    transition.begin(document.body, [
                        'opacity 0 1 100ms'
                    ]);

                    if(charts != null)
                        charts.ReloadActiveCharts();
                    
                    UIThemeChanging = false;
                }, 10);
            })
            .catch(function(err) {  
                alert('Failed to fetch page: ', err);  
            });
        }, 300);
    }

    static ChangeUIAccentColor(color)
    {
        UIAccent = color;
        Cookies.set('ui-accent', color);
    
        UIAccentAlt = Personalization.GetComplementColor(UIAccent);
    
        document.querySelector('html').style.setProperty('--ui-accent', UIAccent);
        document.querySelector('html').style.setProperty('--ui-accent-alt', UIAccentAlt);
    
        document.querySelectorAll('.cabinet .personalization .color-select .color').forEach(colorButton => {
            var currentColor = window.getComputedStyle(colorButton).backgroundColor;
    
            if(currentColor == color)
            {
                colorButton.classList.add('active');
            }
            else
            {
                colorButton.classList.remove('active');
            }
        });
    }

    static LoadUIAccentColor()
    {
        UIAccent = Cookies.get('ui-accent');
    
        if(UIAccent == undefined || UIAccent == null)
        {
            UIAccent = getComputedStyle(document.querySelector('html'))?.getPropertyValue('--ui-accent');
        }
    
        UIAccentAlt = Personalization.GetComplementColor(UIAccent);
    
        document.querySelector('html').style.setProperty('--ui-accent', UIAccent);
        document.querySelector('html').style.setProperty('--ui-accent-alt', UIAccentAlt);
    
        if(document.querySelector('.cabinet .personalization .color-select'))
        {
            Personalization.ShowActiveUIAccentColorInPersonalization();
        }
    }

    static GetComplementColor(source)
    {
        console.log(source);

        switch(source)
        {
            case 'rgb(13, 110, 253)':
                return 'rgb(0, 227, 150)';

            case 'rgb(111, 66, 193)':
                return 'rgb(0, 227, 150)';

            case 'rgb(111, 66, 193)':
                return 'rgb(0, 227, 150)';

            case 'rgb(214, 51, 132)':
                return 'rgb(0, 143, 251)';

            case 'rgb(220, 53, 69)':
                return 'rgb(111, 66, 193)';

            case 'rgb(253, 126, 20)':
                return 'rgb(255, 193, 7)';

            case 'rgb(108, 117, 125)':
                return 'rgb(0, 227, 150)';
            
            case 'rgb(52, 58, 64)':
                return 'rgb(0, 227, 150)';
        
            default:
                var sourceObject = tinycolor(source);
                var result = sourceObject.clone().triad()[1];
            
                return result.toRgbString();
        }
    }
    
    static ShowActiveUIAccentColorInPersonalization()
    {
        document.querySelectorAll('.cabinet .personalization .color-select .color').forEach(colorButton => {
            var currentColor = window.getComputedStyle(colorButton).backgroundColor;
    
            if(currentColor == UIAccent)
            {
                colorButton.classList.add('active');
            }
            else
            {
                colorButton.classList.remove('active');
            }
        });
    
        if(document.querySelector('.cabinet .personalization .color-select .color.active') == null)
        {
            document.querySelector('.cabinet .personalization .color-select .color:first-child').classList.add('active');
        }
    }

    static UpdateApexTheme()
    {
        if(UITheme == 'dark')
        {
            window.Apex = {
                chart: {
                    foreColor: '#ccc',
                    toolbar: {
                        show: false
                    },
                },
                stroke: {
                    width: 3
                },
                dataLabels: {
                    enabled: false,
                },
                tooltip: {
                    theme: 'dark'
                },
                grid: {
                    borderColor: "#111",
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                }
            };
        }
        else
        {
            window.Apex = {
                chart: {
                    toolbar: {
                        show: false
                    },
                },
                stroke: {
                    width: 3
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                }
            };
        }
    }

    static StartDaemon(props)
    {
        return setInterval(function() {
            var cookiesUIAccent = Cookies.get('ui-accent');
            if(cookiesUIAccent != UIAccent && cookiesUIAccent != null)
            {
                Personalization.LoadUIAccentColor();
                props.charts.ReloadActiveCharts();
            }

            var cookiesUITheme = Cookies.get('dark-theme') == 1 ? 'dark' : 'white';

            if(cookiesUITheme != UITheme && UITheme != null && !document.hidden && !UIThemeChanging) {
                Personalization.ChangeUITheme(cookiesUITheme, props.charts);
            }
        }, 1000);
    }
}

