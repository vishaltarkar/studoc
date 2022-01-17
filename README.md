## About Quiz App

Quiz App is a console app, where users can create questions with its answer and list them out. Apart from that, he can do various activities like practicing those questions, getting stats & resetting all previous data from the console.

Here are the steps to run the application.
- > composer install
- > copy content from .env.example to .env and update the configirations of database
- > php artisan key:generate
- > php artisan migrate
- > php artisan qanda:interactive

#### Developer's Say
This application has pretty basic and abstract functionality for creating and answering questions. However, It was built that way only to have a better understanding of the code to whoever is reviewing it.

However, for example, Attaching multiple answers with the question to improve the reach of the question module & adding user_id with the result can improve the result module and make it more user-centric, and can produce more improved stats out of it. 


