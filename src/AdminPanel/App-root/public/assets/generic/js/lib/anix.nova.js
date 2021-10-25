/*
	__________________________________________________________________
	AniX Nova (4.0)

	Автор: WeRtOG
*/
const anix_default_hold = 0; //Заддержка по умолчанию
const anix_default_speed = 500; //Скорость по умолчанию
const anix_transition = 'cubic-bezier(0.785, 0.135, 0.15, 0.86)';



class AniX {
    prev_anix_speed = 0; //Предыдущая скорость(изначально 0)
    summ_delay = 0;
    isInViewport(element) {
        var offset = element.getBoundingClientRect();
        var top_of_element = offset.top;
        var bottom_of_element = offset.top + element.offsetHeight;
        var bottom_of_screen = window.scrollTop() + window.innerHeight;
        var top_of_screen = window.scrollTop();
    
        return (bottom_of_screen > top_of_element) && (top_of_screen < bottom_of_element);
    };
    doScroll(element) {
        window.addEventListener('scroll', function() {
            this.doScrollLogic($el);
        });
        this.doScrollLogic($el);
    }
    Init(group) {
        var instance = this;
        group.forEach(function(element, i) {
            var anix_speed, anix_hold, anix_delay, anix_left, anix_left_dis, anix_up, anix_up_dis;

            element.style.opacity = 0;

            if(element.getAttribute('data-after-scroll') == undefined) {
                const attr_speed = element.getAttribute('data-speed');
                const attr_hold = element.getAttribute('data-hold');
                const attr_continue = element.getAttribute('data-continue');
                const attr_direction = element.getAttribute('data-direction');
                const attr_fx = element.getAttribute('data-fx');

                anix_speed = attr_speed != undefined ? Number(attr_speed) : anix_default_speed;
                anix_hold = attr_hold != undefined ? Number(attr_hold) : anix_default_hold;
                anix_delay = attr_continue == 'true' ? instance.summ_delay + anix_hold + anix_speed : anix_hold;
                
                instance.summ_delay += anix_speed + anix_hold;

                switch(attr_direction) { //Направление
                    default:
                    case 'left':
                        anix_left = '0%';
                        anix_left_dis = '-1%';
                        break;
                    case 'right':
                        anix_left = '0%';
                        anix_left_dis = '1%';
                        break;
                    case 'bottom':
                        anix_up = '0%';
                        anix_up_dis = '-10%';
                        break;
                    case 'top':
                        anix_up = '0%';
                        anix_up_dis = '10%';
                        break;
                }

                switch(attr_fx) { //Сами эффекты
                    default:
                        //$(this).delay(anix_delay).transition({ opacity: 1}, anix_speed);
                        transition.begin(element, [
                            'opacity 0 1 ' + anix_speed + 'ms ' + anix_transition + ' ' + anix_delay + 'ms'
                        ]);
                        break;
                    case "zoom":
                        //$(this).transition({ scale: 0.6}, 0);
                        //$(this).delay(anix_delay).transition({ opacity: 1, scale: 1}, anix_speed, 'cubic-bezier(0.785, 0.135, 0.15, 0.86)');
                        transition.begin(element, [
                            'opacity 0 1 ' + anix_speed + 'ms ' + anix_transition + ' ' + anix_delay + 'ms',
                            'transform scale(0.6) scale(1) ' + anix_speed + 'ms ' + anix_transition + ' ' + anix_delay + 'ms'
                        ]); 
                        break;
                    case "move":
                        //$(this).transition({ x: anix_left, y: anix_up}, 0);
                        //$(this).delay(anix_delay).transition({ opacity: 1, x: anix_left_dis, y: anix_up_dis}, anix_speed, 'cubic-bezier(0.785, 0.135, 0.15, 0.86)');
                        transition.begin(element, [
                            'opacity 0 1 ' + anix_speed + 'ms ' + anix_transition + ' ' + anix_delay + 'ms'
                        ]); 
                        if(anix_left == undefined) {
                            transition.begin(element, [
                                'transform translateY(' + anix_up_dis + ') translateY(' + anix_up + ') ' + anix_speed + 'ms ' + anix_transition + ' ' + anix_delay + 'ms'
                            ]); 
                        } else {
                            transition.begin(element, [
                                'opacity 0 1 ' + anix_speed + 'ms ' + anix_transition + ' ' + anix_delay + 'ms',
                                'transform translateX(' + anix_left_dis + ') translateX(' + anix_left + ') ' + anix_speed + 'ms ' + anix_transition + ' ' + anix_delay + 'ms'
                            ]); 
                        }
                        break;
                    case "puff":
                        transition.begin(element, [
                            'opacity 0 1 ' + anix_speed + 'ms ' + anix_transition + ' ' + anix_delay + 'ms',
                            'transform scale(1.3) scale(1) ' + anix_speed + 'ms ' + anix_transition + ' ' + anix_delay + 'ms'
                        ]); 
                        break;
                }
                instance.prev_anix_speed = anix_speed; //Указываем текущую скорость как предыдущую
            }
        });
    }
}

const anix = new AniX();