# Vector
## A simple yet performing PHP framework

### Installation
1. Clone or download the repository
2. Configure your preferences (database credentials, timezone, etc..) in ./src/config.php 
3. Use the console command "php bin/console create-controller <controller_name>" to create a Controller class in ./src/controllers 
4. Register a route inside the init() function using the $this->router register_route method 
5. That's it, you can now visit your application in your browser of choice!

### Console
1. Create a new Controller class using: bin/console create-controller <controller_name>
2. Create a new Engine module using: bin/console create-module <module_name>
3. Create a new template using: bin/console create-template <template_name>
