### This repo is attempts to demonstrate how a the `Symfony Workflow Component` works

### Install dependencies

    Run the following commands on your terminal:
    
    $ apt-get install graphviz
    $ composer install
    $ bin/console make:migrations:migrate 
    
#### Setup Database
    In your **.env** file the code below:
    
    `DATABASE_URL=mysql://username:password@127.0.0.1:3306/database_name`    
    
### How to Dump  Workflows:

    $ bin/console workflow:dump name | dot -Tsvg -o graph.svg` or
    $ bin/console workflow:dump name --dump-format=puml | java -jar plantuml.jar -p  > workflow.png    