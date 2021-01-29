$(document).ready(function(){
    $('[data="chats"]').scrollbar();
    $('[data="messages"]').scrollbar();
});

var ws = {
    'time': 0,
    'chat_id': false,
    'prev_date': '',
    'request': function(url, data, callback){
        $.ajax({
            url: url,
            type: data['method'],
            data: data,
            dataType: "json",
            success: function (data) {
                if(callback){
                    callback(data);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(
                    thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText
                );
            }
        });
    },
    'sound': function() {
        var audio = new Audio();
        audio.src = '/admin/view/sound/Sound_17211.mp3';
        audio.autoplay = true; 
    },
    'send': function(){
        if(!ws.chat_id){
            return;
        }
        $('#submit').attr('disabled', 'disabled');

        var token = $('#token').val();
        var url = '/admin/index.php?route=service/notification/whatsapp/send&token='+token;

        var message = $('.message-input').val();

        var data = {method: 'post',message: message, chat_id: ws.chat_id};

        this.request(url, data, function(data){
            $('.message-input').val('');
            $('#submit').removeAttr('disabled');
            ws.u_dialog(ws.chat_id);
        });
    },
    'check': function(){
        if(!ws.chat_id){
            return;
        }

        var token = $('#token').val();
        var url = '/admin/index.php?route=service/notification/whatsapp/check&token='+token;
        var data = {method: 'get'};

        this.request(url, data, function(data){
            if(data.time > ws.time){
                ws.u_dialog(ws.chat_id);
                ws.u_chats();
                ws.sound();
                ws.time = data.time;
            }
            ws.setInterval();
        });
    },
    'clear_dialog': function(){
        $('[data="messages"] > *').remove();
    },
    'u_dialog': function(chat_id){
        
        var token = $('#token').val();
        var url = '/admin/index.php?route=service/notification/whatsapp/getMessages&token='+token;

        if(!chat_id){
            chat_id = $('.chat').first().data('chat_id');
        }

        ws.chat_id = chat_id;

        var date = '';

        if($('.message').length){
            date = $('.message').last().data('date');
        }

        var data = {method: 'post', chat_id: chat_id, date: date};

        this.request(url, data, function(data){
            var $messages = $('[data="messages"]');

            data.map(function(el, key){
                var css = 'pull-left';
                if(el.direction == 'from-me'){
                    css = 'pull-right';
                }

                if(ws.prev_date != el.date){
                    $messages.append('<div class="date">'+el.date+'</div>');
                    ws.prev_date = el.date;
                }

                var message = '<div class="message flex '+css+'" data-date="'+el.date_added+'">';

                if(el.type == 'image'){
                    message += '<img src="'+el.body+'" class="message-image">';
                }else if(el.type == 'ptt'){
                    message += '<video class="ptt" controls="" name="media"><source src="'+el.body+'" type="audio/ogg"></video>';
                }else{
                    message += el.body;
                }

                message += '<div class="time">'+el.time+'</div><div class="status '+el.status+'"></div>';
                message += '</div>';

                $messages.append(message);
            });
            
            $messages.scrollTop(999999);
        });
    },
    'u_chats': function(){
        var token = $('#token').val();
        var url = '/admin/index.php?route=service/notification/whatsapp/getChats&token='+token;

        var message = $('.message-input').val();

        var data = {method: 'get'};

        this.request(url, data, function(data){
            data.map(function(el, key){
                $('[data-chat_id="'+el.chat_id+'"]').remove();
                var html = `
                <div class="chat flex" data-chat_id="`+el.chat_id+`">
                    <img src="`+el.image+`">
                    <div class="chat-info flex f-column">
                        <div class="flex">`+el.name+`<span class="pull-right">`+el.date_added+`</span></div>
                        <div>`+el.last_message+`</div>
                    </div>
                </div>`;

                $('[data="chats"]').prepend(html);
            });

            ws.active();
        });
    },
    'timeout': null,
    'setInterval': function(){
        if(ws.interval){
            clearTimeout(ws.interval);
        }

        ws.timeout = setTimeout(function(){
            ws.check();
        }, 5000);
    },
    'active': function(){
        $('.chat').removeClass('active');
        $('[data-chat_id="'+ws.chat_id+'"]').addClass('active');
    }
}

ws.setInterval();

$(document).on('click','.chat',function(){
    if($(this).hasClass('active')){
        return;
    }

    var chat_id = $(this).data('chat_id');
    
    ws.clear_dialog();
    ws.u_dialog(chat_id);

    ws.active();

    localStorage.setItem('chat_id', chat_id);
});

$(document).on('click','#submit',function(){
    ws.send();
});

$(document).ready(function(){
    var chat_id = localStorage.getItem('chat_id');
    if(chat_id){
        $('[data-chat_id="'+chat_id+'"').click();
    }else{
        $('[data-chat_id').first().click();
    }
});