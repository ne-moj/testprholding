
function animate(options)
{
    let start = performance.now();

    return requestAnimationFrame(function animate(time) {
        // timeFraction от 0 до 1
        let timeFraction = (time - start) / options.duration;
        if (timeFraction > 1) timeFraction = 1;

        // текущее состояние анимации
        let progress = options.timing(timeFraction)
        
        options.draw(progress);

        if (timeFraction < 1) {
            requestAnimationFrame(animate);
        }

    });
}

function cancelAnimation(requestId)
{
    cancelAnimationFrame(requestId);
}

function ajaxToServer(url, data, success, error)
{
    return $.ajax({
        url: url,
        type: 'post',
        data: {
            ...data,
            _crsf : $('meta[name=csrf-token]').attr('content'),
        },
        success: success,
        error: error,
    });
}

function animateRockingApple(apple)
{
    let posY = parseInt(apple.style.top);
    let posX = parseInt(apple.style.left);
    let width = 5;
    let rockingPosX = 0;
    let rockingPosY = 0;
    let incriment = Math.random() > 0.5 ? 1 : -1;

    return animate({
        duration: 1000,
        timing: function (timeFraction) {
            return Math.pow(timeFraction, 2);
        },
        draw: function (progress) {
            let amplitude = 1 - progress;

            if(rockingPosX < -width){
                incriment = 1;
            }else if(rockingPosX > width){
                incriment = -1;
            }

            rockingPosX += incriment;
            rockingPosY = Math.sqrt((width * width) - (rockingPosX * rockingPosX));

            apple.style.left = posX + (rockingPosX * amplitude) + 'px';
            apple.style.top = posY + (rockingPosY * amplitude) + (width * progress) + 'px';
        }

    });
}

function showMySuccess(message)
{
    toastr['success'](message);
}

function showMyInfo(message)
{
    toastr['info'](message);
}

function showMyError(message)
{
    toastr['error'](message);
}

function animateAppleDown (apple)
{
    let startSpeed = 0.5;
    posY = parseInt(apple.style.top);

    animate({
        duration: 1000,
        timing: function (timeFraction) {
            let result = Math.pow(startSpeed + timeFraction, 10);
            result = result < 1 ? result : 1;

            return result;
        },
        draw: function (speed) {
            apple.style.top = posY - speed * posY + 'px';
        }

    });
}

function initialToast()
{
    toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
    }
}
