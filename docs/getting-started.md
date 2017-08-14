---
currentMenu: getting-started
---

## Getting Started

Phở enables you to write lightning-fast social apps in GraphQL. 

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

### Requirements

* [PHP 7.1+](http://php.net)
* [Composer](http://getcomposer.org)
* [Redis](http://redis.io)

If you have all these requirements already installed and set up, then you can go ahead and install [pho-cli](https://github.com/phonetworks/pho-cli), the command line interface to the Pho stack, which lets you compile GraphQL schema.

The easiest way to install it is to download the phar file from [https://phonetworks.github.io/pho-cli/pho.phar](https://phonetworks.github.io/pho-cli/pho.phar).

### The power of GraphQL

Recreating Twitter is as easy as setting up the right schema. 

The graph based architecture of Phở feels natural to the fabric of social apps. We define Twitter with:

* One graph; Twitter 
* Two nodes; User (an Actor) and Tweet (an Object)
* Five edges; Follow (a Subcribe edge), Post (a formative Write edge), Like (a Subscribe edge), Mention (a Mention edge), Message (a multiplicable Mention edge), and Consume (a Read edge)

Let's begin with the nodes:

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

Once the schema is set up, you can get it up and running using your recently downloaded [pho.phar](https://phonetworks.github.io/pho-cli/pho.phar):

```bash
php pho.phar build your-pgql-source-dir compiled-dir pgql # where pgql is the extension of your graphql files
php pho.phar init destination-dir compiled-dir
cd destination-dir

```

Voila, now you can access your graph by entering the desination-dir and setting up the play.php or play-custom.php accordingly.

For more information on schema set up, take a look at https://github.com/pho-recipes

The examples are self-explanatory.

