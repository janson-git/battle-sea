Сейчас в процессе опробирования взаимодействие через веб-сокеты.

Общая схема взаимодействия:

```
                _______________________        ____________________________
Browser1 <---->| php-websockets-server |      |    application backend     |
               | WS->handle-> ZMQ_PUSH------->| ZMQ_PULL-> listen and route| 
Browser2 <---->| WS<-handle<- ZMQ_PULL |<-------ZMQ_PUSH<- send from app   |
                ------------------------       ----------------------------
```
Запуск:
```bash
# up web-sockets server
path@reactphp-pusher $ php server.php

# up pull message listener in app
path@battle-sea $ php artisan messages:pull-listener

# now go to http://localhost:8080 (or how it configured in your env) in browser 
# it runs, connects to web-sockets, and write to web-socket server log something like:
# INFO: Opened for u5ca8b34c51151!
# INFO: Subscribe to test!

# then click for battle field cells and looks messages in pull-listener log:
["{\"topic\":\"gameEvent\",\"event\":{\"message\":{\"type\":\"shot\",\"data\":{\"row\":\"2\",\"col\":\"7\"}}},\"sender\":\"u5ca8b34c51151\"}"]

```

Для обслуживания веб-сокетов используется проект https://github.com/janson-git/php-websocket-server.
В проекте есть SocketConnectionsManager, который по факту становится полноценным приёмником и рассыльщиком сообщений.

Для того чтобы это корректно работало:
- В ZMQ поднимаем два канала: от сокетов в сервер (toServer) и от сервера в сокеты (toClient).
- PUSH в Publisher сейчас корректно обрабатывает ситуацию как адресной рассылки так и общей. 
- Для адресной рассылки нужно использовать ID получателя: это userID в том или ином виде. Лучше - хешированый, чтобы избежать подбора методом перебора в будущем.

- PUBLISH с клиентов сейчас подхватывается и перебрасывается в ZMQ, на стороне приложения есть 
прототип PullMessagesListener (и команда messages:pull-listener для его запуска), который и слушает нужный канал в ZMQ.
