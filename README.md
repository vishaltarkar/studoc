## About Question & Answer App

Quiz App is a console app, where users can create questions with its answer and list them out. Apart from that, he can do various activities like practicing those questions, getting stats & resetting all previous data from the console.

Here are the steps to run the application.
- > composer install
- > copy content from .env.example to .env and update the configirations of database
- > php artisan key:generate
- > php artisan migrate
- > php artisan qanda:interactive

#### NEW EDIT
- Now Question can have multiple options and only one of them will be correct
- When Practice question you will see possible answers to the question
- Feature Tests have been writted for CreateQuestion & Practice Question
- Unit Test has been writted of getResults method of Question Classs.

Use below command to run all tests.
- > php artisan test

#### Developer's Say
This application has pretty basic and abstract functionality for creating and answering questions. However, It was built that way only to have a better understanding of the code to whoever is reviewing it.

However, for example, Attaching multiple answers with the question to improve the reach of the question module & adding user_id with the result can improve the result module and make it more user-centric, and can produce more improved stats out of it. 

#### Possbile Extension of App
We can ask for user login details module after the `php artisan qanda:interactive` called, that way we can use the column of `user_id` to store user specific question answer result and manage the practice data for each user.

I have not included `queston_answers` table considering that answer will be input text only, However, if we consider different type of question like multi choice etc. adding that will require a `question_types` table as well.

We can also put a whole `QUIZ` layer on whole App. Where user can practice and access different quiz and practice the question for them. which make app more advance.


#### About Docker:
I could't use docker and laravel sail with the app as there were some update issue going on with my current system at the time i write this code and there for I'm not able to install `WSL 2 backend` and there for docker cant be install as well. 
