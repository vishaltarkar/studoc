## About Quiz App

Quiz App is a console app, where user can create question with its answer and list them out. Apart from that he can do various action as like practice those questions, get stats & reset the all previous data from the console.


Here is the steps to run the application.
- > composer install
- > copy content from .env.example to .env and update the configirations of database
- > php artisan key:generate
- > php artisan migrate
- > php artisan qanda:interactive

#### Developer's Say
This application has pretty basic and abract functionality for creating and answering the questions. However, It was build that way only to have better understanding of the code whoever is reviwing it.

However, for example, Attaching multiple answers with the question to improve the reach of question module & adding user_id with result can improve the result module and make it more user centric and can be produce more improved stats out of it. 


