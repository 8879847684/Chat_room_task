# Chat_room_task
Task Done Websocket
Setup instructions
Steps 1:- first setup xammp or wamp.
Steps 2 :- then make sure php.ini file 
Step 3 :- open php.ini remove semicolom at ;extensio = zip (This removed composer intallation)
step 4 : install the composer 
step 5 : then install dependency 
Step 6 : run this composer require cboden/ratchet(install all the dependency)

Autoload file must is present.
Kindly install the  composer for websocket
This folder structure .
C:\xampp\htdocs\full-chat-app\
└── chat-app\
    ├── vendor\
    │   └── autoload.php 
    ├── ws-server\
    │   ├── server.php 
    │   └── ChatServer.php 

    

then you are set run the code.
Check the websocket 
ws://localhost:8080
setup or not check.


Database connection details
dbname=chat_app;
charset=utf8', 
username  = 'root', 
password = ''


Sample login credentials

email : test@gmail.com
password : 1234





