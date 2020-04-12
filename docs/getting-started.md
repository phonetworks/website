---
currentMenu: getting-started
---

## Getting Started


<!--
Phở enables you to write social apps in GraphQL. 

Once you set up the schema, Phở generates the API interfaces for you to code poetically as follows:

```php
<?php
require "vendor/autoload.php";
$kernel = new \Pho\Kernel\Kernel(/* $configs */);
$kernel->boot();
$user = new User($kernel, $kernel->graph(), "secret_password");
$tweet = $user->post("My first tweet");
$another_user = new User($kernel, $kernel->graph(), "this_is_not_123456");
$user->follow($another_user);
print_r($another_user->getFollowers());
$another_user->message($user, "Thanks for following me");
```

-->

### Requirements

* [PHP 7.2+](http://php.net)
* [Composer](http://getcomposer.org)
* [Redis](http://redis.io)
* [Neo4J](https://neo4j.com/)

Get started with Phở Networks in five easy steps:


### Step 1

The first step is to download the [pho-cli PHAR file](https://github.com/phonetworks/pho-cli/releases/download/0.2/pho.phar) and move it to your `/usr/local/bin` or an equivalent.

```shell
cd /tmp
wget https://github.com/phonetworks/pho-cli/releases/download/0.2/pho.phar
mv pho.phar /usr/local/bin/pho
chmod +x /usr/local/bin/pho
```

### Step 2

Now that pho-cli is in our executable binary folder, we can use it to initialize new graph app projects and build (or compile) them.

```shell
pho init
```

This will start a dialog where the pho-cli will ask you the following name:

* **App Name**: This should not contain any special characters. The name you enter here will be used to generate a new folder in the path you ran this command and the project will situate there.

* **App Description**: Describe what this app is about in no more than 255 characters. May contain special characters.

* **App Template**: You have six options here ordered by complexity starting with the simplest; "Blank" which generates no schema/ folder at all. If you use Blank template, you must generate a schema yourself using [Gapp Designer](/designer.html). Basic is the same as https://github.com/pho-recipes/basic. GraphJS: https://github.com/pho-recipes/web. Simplified Twitter: https://github.com/pho-recipes/twitter-simple. Full Twitter: https://github.com/pho-recipes/twitter-full. Facebook: https://github.com/pho-recipes/facebook. 

* **Founder Username**: This will be the super-admin's username. Keep it "admin" or another nickname of your choice, short and with no special characters.

* **Founder Password**: This will be the super-admin's password.

* **Founder Email**: This is shown only with the GraphJS template.

After you type in these fields successfully, your new project will be created in `{current_path}/{app_name_that_you_entered_in_the_first_step}`

The folder will look like as follows:

File Name           | Description
----------------    | --------------- 
📄 .env             | Contains application settings & resources. VERY IMPORTANT!
📄 .phonetworks     | Ignore but DO NOT DELETE!
📄 LICENSE          | Contains the application's LICENSE file.
📄 Procfile         | Heroku Instructions. You may delete this if you're not hosting the app on Heroku.
📄 README.md        | Project Instructions. Feel free delete.
📄 app.json         | Heroku Description. You may delete this if you're not hosting the app on Heroku.
📁 build            | This is where the compiled files are hosted. If you selected the "blank" template, this will be empty.
📄 composer.json    | App's dependencies in human-readable format.
📄 composer.lock    | App's dependencies in compiled format.
📁 lib              | Libraries used in the app. Most probably, you won't need to touch these files.
📁 public           | Contains REST API Server related files. You may extend the REST Server here.
📁 schema           | Contains the schema files. VERY IMPORTANT! You fine-tune your app here, and the pho-cli's `build` command compiles them into the `build/` folder.
📁 src              | Contains a generic kernel application which you can use to interact via the command line interface.

The most part of this filesystem structure is the `schema/` folder which contains the definitions of your app.

### Step 3

After the project is created, you need to make sure the app is defined as per your requirements.

The easiest way to design your definitions is using the open-source [Gapp Designer](/designer.html) tool available on this site. After you create your and download your definitions, unzip and put them in the `schema/` folder of your project.

For background, here's how the GraphQL schema of a simple Twitter clone as follows:

* One graph; Twitter 
* Two nodes; User (an Actor) and Tweet (an Object)
* Five edges; Follow (a Subcribe edge), Post (a formative Write edge), Like (a Subscribe edge), Mention (a Mention edge), Message (a multiplicable Mention edge), and Consume (a Read edge)

We define the nodes as follows:

```graphql
# pho-graphql-v1

type User implements ActorNode 
@edges(in:"User:Follow, Status:Mention", out:"Post, Like, Follow")
@permissions(mod: "0x0e554", mask: "0xeeeee") 
@properties(editable: false, volatile: false, revisionable: false)
{
    id: ID,
    birthday: Date,
    about: String,
    password: String
}

type Status implements ObjectNode 
@edges(in:"User:Post, User:Like", out:"Mention")
@permissions(mod: "0x0e444", mask: "0xeeeee") 
@properties(expires: 0, editable: false, volatile: false, revisionable: false)
{
    id: ID,
    status: String @constraints(maxLength: 140)
}
```

Next, we define the edges which we already stated where they may originate from with the @edges(in) directives aboves, and where they may be headed to with the @edges(out) directives.

```graphql
# The Post edge created the Status nodes. So its property is set to be "formative"
type Post implements WriteEdge 
@nodes(head:"Status", tail:"User")
@properties(binding: true, persistent: true, consumer: true, formative: true)
@labels(headSingular:"post", headPlural: "posts", tailSingular: "author", tailPlural: "authors")
{
    id: ID,
    status: String @constraints(maxLength: 140)
}
```

Once the schema is set up, you should compile it using [pho-cli](https://github.com/phonetworks/pho-cli) you downloaded in phar format earlier. Then just go the folder where your project sits, and run:

```bash
pho build
```

This will generate the compiled files under the `build/` folder.

For more information on schema format, visit https://github.com/pho-recipes

### Step 4

Once the project is compiled, you should run

```
composer install
```

to install the dependencies. Make sure [Composer](https://getcomposer.org/) was installed and is in the global executable path of your system. This will generate a new `vendor/` folder under your project filetree.

Make sure you edit the .env file for the project to suit to your environment's information. The default .env file includes:


 ```
DATABASE_TYPE="redis"
DATABASE_URI"="tcp://127.0.0.1:6379"
STORAGE_TYPE="filesystem"
STORAGE_URI="/tmp/pho"
INDEX_TYPE="neo4j"
INDEX_URI="bolt://neo4j:password@127.0.0.1:7687"

 ```

 where DATABASE_URI is the Redis server.

 and INDEX_URI is the binary Bolt connection to your Neo4J server.

 Additional settings in your .env file are:

* GRAPH_CLASS
* FOUNDER_CLASS
* USER_CLASS=
* FOUNDER_PARAMS

which determine the foundational types of your app. If you selected a template earlier using `pho init` (and not the "blank" one) these must come prefilled.

### Step 5

Next up you can do two things; 

* either you can play with the mini kernel implementation that allows you to play with the framework in the command line, 
* or you can run the REST API server and interact with your app via HTTP.

To play with the kernel, run `php src/index.php` which will give you `$kernel` variable in an interactive PHP shell environment. Here are some sample operations you can try:

```php
$founder = $kernel->founder(); // to access the founder
$graph = $kernel->graph(); // to access the main graph
echo $founder->id(); // to print the founder' id
echo $graph->id(); // to print the graph's id

/* 
 Create a new user as below:
 This may be different based on your schema
*/
$user = new \PhoNetworksAutogenerated\User($kernel, $graph, "admin", "123456"); 
echo $user->id();

$user->follow($founder); // to make the user follow the founder
$followers = $founder->getFollowers(); // to fetch the founder's followers
```

For more on kernel operations, check out the [Reference](/reference.html#r5)

To run the REST API Server, run `php web/api/index.php`. Once it's up and running, you can switch to your web browser and visit http://localhost/founder to see the ID of your founder object. For full REST API capabilities, check out [the documentation](/assets/restapi.html)

---

Should you have any questions, feel free to contact us using the [Forum](/support.html)