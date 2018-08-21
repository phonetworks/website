---
currentMenu: reference
---

# Reference

1.  <a href="#r1">Overview</a>
2. <a href="#r2">Core Concepts</a>
3. <a href="#r3">Configuration</a>
4. <a href="#r4">Services & Adapters</a>
5. <a href="#r5">Kernel Methods</a>
6. <a href="#r6">Graph </a>
7. <a href="#r7">Nodes</a>
8. <a href="#r8">Edges</a>
9. <a href="#r9">IDs</a>
10. <a href="#r10">Notifications</a>
11. <a href="#r11">Signals</a>
12. <a href="#r12">Hooks</a>
13. <a href="#r13">Handlers & Injectables</a>
14. <a href="#r14">ACL (Access-Control Lists)</a>
15. <a href="#r15">Project Directory</a>
16. <a href="#r16">Compiling a Recipe</a>
17. <a href="#r17">More Resources...</a>

---

## <a name="r1" class="anchor">1. Overview</a>

 Written in PHP7, Phở Kernel is a programmable interface, as well as a configuration, events broker for distributed micro social graphs.

Phở Kernel is **not** static, which means you can run multiple kernel instances in a thread, and you can halt and reboot each thread as many times as needed.

Here is a sample bootstrap script for [pho-kernel](https://github.com/phonetworks/pho-kernel):

```php
include("vendor/autoload.php");
$configs = array(
    "services"=>array(
   		"database" => "redis://127.0.0.1:6379",
    	"storage" => "filesystem:// /tmp"
    )
);
$kernel = new Pho\Kernel\Kernel($configs);
$kernel->boot();
$network = $kernel->graph();
$founder = $kernel->founder();
array_walk($network->members(), function($key, $item) {
  printf("A %s node with id: %s", get_class($item), $item->id());
});
```

In a nutshell;

1. Firstly, we form the kernel object by passing a configuration variable that holds all our preferences and server-related settings.
2. Secondly, we boot it up. Optionally you can pass the founder object as a parameter, which would initialize the kernel (for a single time) with that user as the founder. This may be useful if you have a custom Actor node.
3. Finally, we start playing with it by calling ```graph()``` and ```founder()``` methods.

## <a name="r2" class="anchor">2. Core Concepts</a>

Phở Kernel allows you to launch and manage social graphs. Just like any other [graph](https://en.wikipedia.org/wiki/Graph_theory), social graphs are also formed by "nodes" and their relationships identified by "edges".

In Phở's GAO model, a [social network](https://en.wikipedia.org/wiki/Social_network) cconsists of three type of entities:

* Graphs
* Actors
* Objects

![Architecture](https://github.com/phonetworks/pho-lib-graph/raw/master/.github/lib-graph-components.png "Pho LibGraph Architecture")

Each of these types have their own particular characteristics which we will discuss in the chapters 5, 6 and 7.

Both edges and nodes are "entities" and they're represented in the database with a cryptographically secure unique identifier in the form of: "4a406464850849e9b13ac38d0a67c157". For more information on Pho IDs, check out <a href="#r9">section 9</a>.

The figures below illustrate a mini social network, inspired by the movie [Matrix](https://en.wikipedia.org/wiki/The_Matrix) where **nodes** are represented by:

![Matrix movie social network](/assets/img/Matrix.png)

* Squares => Graph,
* Triangles => Actor,
* Circles => Object

and **edges** by arrows.

This is what the figure looks like with the Actor/Write edges:
![Matrix movie social network](/assets/img/Actor-Creates.png)

And this is what it looks like with the Actor/Subscribe edges:
![Matrix movie social network](/assets/img/Actor-Subscribes.png)

This tiny social network consists of:

* 5 characters (aka Actor): Neo, Morpheus, Trinity, Agent Smith and Spoon Boy.
* 2 graphs: Matrix (the network itself) and a group called Resistance Hovercraft, created by Morpheus.
* 4 objects:
      * "Everything begins with a choice" a status update by Morpheus transmitted to the Matrix network,
      * "Resistance is futile" a comment by Agent Smith in response to Morpheus' blog post,
      * "We must save Morpheus" a blog post by Neo transmitted to the Resistance Hovercraft group after Agent Smith captures Morpheus,
      * and "Kiss me" a private message by Trinity transmitted to Neo before she dies.

The examples above should give you an idea about how Actor, Graph and Object nodes behave.

## <a name="r3" class="anchor">3. Configuration</a>

The list of all the configuration variables you can play with can be found in pho-microkernel's [defaults.php](https://github.com/phonetworks/pho-microkernel/blob/master/src/Pho/Kernel/defaults.php) file.

```php
array(
      "services" => array( 
          // The adapter to use, and uri the service options.
          "database" => "apcu:", 
          "logger" => "stdout:", 
          "storage" => "filesystem:", 
          "events" => "local:", 
      ),
      "tmp_path" => sys_get_temp_dir(), // Tmp folder to store files (e.g. uploads)
      "root_path" => __DIR__,
      "adapter_path" => __DIR__ . DIRECTORY_SEPARATOR ." Services" . DIRECTORY_SEPARATOR . "Adapters",
      "namespaces" => array(
          "root" => __NAMESPACE__,
          "predicates" => __NAMESPACE__."\\Predicates\\",
          "services" => __NAMESPACE__."\\Services\\",
          "nodes" => __NAMESPACE__."\\Nodes\\",
          "edges" => __NAMESPACE__."\\Edges\\",
      ),
      "log_level" => "WARNING", // INFO
      "database_key_separator" => "/",
      "default_objects" => array(
        "graph" => Standards\Graph::class,
        "founder" => Standards\Founder::class,
        "space" => Standards\Space::class,
        // "actor" => ... // not set here.
        "editors" => Standards\VirtualGraph::class
      )
);
```

## <a name="r4" class="anchor">4. Services & Adapters</a>

Kernel services are defined in [pho-microkernel](https://github.com/phonetworks/pho-microkernel). You can access a service by calling ```$kernel->$service_name()``` where $service_name is the name of the service in lowercase. Currently standard services on Pho-Kernel are:

Service Type | Description | Base Adapter   | Distributed Adapter | Cloud Adapter | Implements
------------ | ----------- | -------------- | ------------------- | ------------- |
**Database**     | As a proof of truth and to store fundamental graph information. | APCU | Redis | ~~DynamoDB~~ | ...
**Events**       | To make the kernel more flexible with event-driven programming. | Local | ZeroMQ | ... | ...
**Index**        | To make search and database query easier and faster. | MySQL | ElasticSearch | ... | ...
**Logger**       | To log for errors or debug information. | Stdout or File | Scribe | ... | ...
**Storage**      | To store and retrieve plaintext or binary files such as photos, videos, zip files etc. | Filesystem | OpenStack Swift | AWS S3 | ..

To illustrate, you can access database with; ```$kernel->database()```, and logger with; ```$kernel->logger()```.

## <a name="r5" class="anchor">5. Kernel Methods</a>

### 5.1 boot

Once you create the kernel object, you need to boot it up with:

```php
$kernel->boot();
```

Or if you have a custom founder (based on a non-standard Actor object) and you're running the kernel for the first time, you should pass it here:

```php
$kernel->boot($founder);
```

If this is not the first time the kernel is booted up, the argument will be ignored.

### 5.2 space & graph

Once the kernel is booted up, you can query the outer and inner graphs as follows:

```
$space = $kernel->space(); // returns the outer graph.
$graph = $kernel->graph(); // returns the actual graph that all your nodes will become part of.
```

The outer graph, or "space" is a super-graph that has one and only one element, that is the graph.

### 5.3 gs

To query a node, you just use the kernel's ```gs()``` method, which is similar to UNIX' filesystem. You must know the ID of the node.

To retrieve a node with the given id:

```php
$node = $kernel->gs()->node("aa406464-8508-49e9-b13a-c38d0a67c157");
```

Similarly, you can retrieve an edge with its ID using ```$kernel->gs()->edge("edge-id")```

### 5.3 Services

You call a service with ```$kernel->$service_name``` where $service_name represents the service keyword in lower case. For more details, see Chapter 4.

### 5.4 Creating new nodes

To create a node in the graph, you just need to create the object by passing the kernel ($kernel) as its first argument. By default:

*  Actor objects have a minimum of two constructor arguments; $kernel and $context (must implement \Pho\Framework\ContextInterface, for example: $kernel->space() which is the outer graph or $kernel->graph() which is the inner graph)
* Object and Graph nodes may be created with at least three constructor arguments; $kernel, $context and $creator (which is an Actor object, the creator of this object). However, please note, graphs and object nodes shall not be created manually. The recommended way to create such nodes is via formative edges, which we will see later.

## <a name="r6" class="anchor">6. Graph</a>

Graph contains objects that implement [NodeInterface](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/NodeInterface.php) interface, such as [Node](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/Node.php) and [Subgraph](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/SubGraph.php), but not [Edge](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/Edge.php).


| Method               | Parameter(s)            | Description                   | Returns                |
| -------------------- | ----------------------- | ----------------------------- | ---------------------- |
| id                   |                         | Always returns "." as ID obj. | ID                     |
| add [\*]             | NodeInterface $node     | Adds a new node               | void                   |
| count                |                         | Counts the # of member nodes. | int                    |
| contains             | ID $node_id             | Checks if a node is a member  | bool                   |
| get                  | ID $node_id             | Fetches a member              | NodeInterface          |
| remove               | ID $node_id             | Removes a member              | void                   |
| members              |                         | Lists members in  object form | array\<NodeInterface\> |
| toArray              |                         | Lists member ref.s in ID form | array\<ID\>            |
| loadNodesFromArray   | array $nodes            | Array of NodeInteface objects | void                   |
| loadNodesFromIDArray | array $node_ids         | Array of node IDs in string   | void                   |

## <a name="r7" class="anchor">7. Nodes</a>

Nodes(aka Particles) implement the following API:

| Method        | Parameter(s)            | Description                    | Returns              |
| ------------- | ----------------------- | ------------------------------ | -------------------- |
| id            |                         | Retrieves its ID               | ID                   |
| label         |                         | Returns the class name         | string               |
| isA           | string $class_name      | Validates object class         | bool                 |
| attributes    |                         | Returns the attributes class   | AttributeBag         |
| destroy |                         | Readies object for destruction | void                 |
| toArray       |                         | Lists member ref.s in ID form  | array                |
| edges         |                       | Retrieves the EdgeList object that interfaces its edges.           | EdgeList       |
| context       |                       | Retrieves its context                                              | GraphInterface | 
| inDestruction |                       | Reserved to use by observers to understand the state of the node.  | bool           |        

Nodes (or Particles) have three sub-types:

#### 7.1 Graphs (aka SubGraphs)

Graphs contain other nodes. This is their one and only function. A social network itself is a Graph. We call it the "Network". Another typical type of Graph that most social networks have is Groups. 

#### 7.2 Actors
Actors can do three things;

1. Read
2. Write (forming new objects or editing them)
3. Subscribe (to get Notification from nodes)

In social network context, social network members are the Actors. To illustrate this, take the example of Facebook. Facebook itself is a Graph. The groups and friend lists on Facebook are also Graphs (micro graphs). And you, as a Facebook member or the pages you own are Actors, because they like (subscribe) and respond (write) objects.

#### 7.3 Objects

Objects are what social networks are centered around. They are the fundamental units of sharing. A photo or status update can be examples of object. The only edge that originates from Objects is Edge, which does the exact opposite of Subscribe, e.g. sending Notifications.


## <a name="r8" class="anchor">8. Edges</a>

An [Edge](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/Edge.php) (aka lines or arcs in graph theory) are used to represent the relationships between Nodes of a Graph. Therefore it is a fundamental unit graphs.

| Method       | Parameter(s)        | Description                                              | Returns            |
| ------------ | ------------------- | -------------------------------------------------------- | ------------------ |
| id            |                         | Retrieves its ID               | ID                   |
| label         |                         | Returns the class name         | string               |
| isA           | string $class_name      | Validates object class         | bool                 |
| attributes    |                         | Returns the attributes class   | AttributeBag         |
| destroy |                         | Readies object for destruction | void                 |
| toArray       |                         | Lists member ref.s in ID form  | array                |
| tail         |                     | Retrieves the tail node of the edge.                     | TailNode [\*]      |
| tailID       |                     | Retrieves the tail node's ID.                            | ID                 |
| head         |                     | Retrieves the head node of the edge.                     | HeadNode [\*]      |
| headID       |                     | Retrieves the head node's ID                             | ID                 |
| predicate    |                     | Retrieves the predicate                                  | PredicateInterface |
| connect      | NodeInterface $head | Connects the edge to a head node.                        | void               |
| orphan       |                     | Checks if the edge fails to possess a tail or a head     | bool               |

## <a name="r9" class="anchor">9. IDs</a>

Pho IDs are immutable and come in the format of cryptographically secure,
 similarly to [UUIDv4](https://en.wikipedia.org/wiki/Universally_unique_identifier),
 though not the same.
 
 Pho IDs are used to define all graph entities, e.g nodes and edges. It is 16 bytes (128 bits) long similar to UUID, but the first 8 bits are reserved to determine entity type, while the UUID variants are omitted. Hence, Pho ID provides 15 bytes and 8 bits of randomness.
 
 The Graph ID defaults to nil (00000000000000000000000000000000), or 32 chars of 0. It may may be called with ```ID::root()```
 
 Even at scale of billions of nodes and edges, the chances of collision 
 is identical to zero.
  
 You can generate a new ID with ```$id_object = ID::generate($entity)```, 
 where $entity is any Pho entity, and fetch its  string representation with 
 PHP type-casting; ```(string) $id_object```.

 Entity headers will be as follows:
     
* 0: Graph
* 1: Unidentified Node
* 2: SubGraph Node
* 3: Framework\Graph Node
* 4: Actor Node
* 5: Object Node
* 6: Unidentified Edge
* 7: Read Edge
* 8: Write Edge
* 9: Subscribe Edge
* 10: Mention Edge
* 11: Unidentified

## <a name="r10" class="anchor">10. Notifications</a>

Notifications are the messages passed between notifiers and objects, or 
subscribers and their subscriptions. Notifications constitute a basic component
of all social-enabled apps.

For more information, take a look at 
* [AbstractNotification.php](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/AbstractNotification.php) class.
* and [ObjectOut/MentionNotification.php](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ObjectOut/MentionNotification.php) class.

to see how notifications works.

Notifications are called by the ```execute()``` method of the edges. Example: [ObjectOut/Mention.php](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ObjectOut/Mention.php) and [ActorOut/Write.php](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ActorOut/Write.php)

## <a name="r11" class="anchor">11. Signals</a>

One of the best features of the Pho Kernel is that it is event-driven. Anytime a new node or edge is created, deleted, edited, a signal is emitted. If set, a listener object (a [Callable](http://php.net/manual/en/language.types.callable.php)) can process the signal in real-time.

Available events are:

#### Graph
* **node.added**: when a new node is added to the graph.
* **modified**: when the graph is modified with a node addition or removal.

#### Edge
* **modified**: when the edge is modified either by connecting or by its attribute bag.
* **deleting**: when the edge is being deleted.


#### SubGraph
* **modified**: when the subgraph is modified by its attribute bag.
* **edge.created**: when there is a new edge originating from this subgraph.
* **edge.connected**: when the orphan edge of this subgraph is connected to a head.
* **deleting**: when the subgraph is being deleted.

#### Node
* **modified**: when the node is modified by its attribute bag.
* **edge.created**: when there is a new edge originating from this node.
* **edge.connected**: when the orphan edge of this node is connected to a head.
* **deleting**: when the node is being deleted.
* **edge.registered**: (string $direction, string $class): called by particles when registering edges. This function may be used extra edges easily and independently, without extending the constructor itself.
* **notification.received**: (AbstractNotification $notification): called when the actor received a notification. 

#### Graphsystem
* **graphsystem.touched**: when an entity has been persisted to disk.
* **graphsystem.node_deleted**: when a node is removed from persistent state.
* **graphsystem.edge_deleted**: when an edge is removed from persistent state.

Example usage:

```php
$node->on("modified", function() use ($node) {
    $node->persist();
});
```

## <a name="r12" class="anchor">12. Hooks</a>

Hooks allow developers to intercept certain functions that may benefit from hydration at higher-levels. Hydration takes place with persistent objects which, once deserialized, may lose some of their object associations. 

To illustrate, when you persist a node object, its EdgeList object may turn into an array rather than a full-blown object, which would be hard and expensive to store. Then, in order to retrieve the edge list, you can use the IDs and tap into your database in separate calls, which would enhance the performance of your app. Lib-Graph's hooks come into play in such scenarios, because you can intercept these getter methods and inject value by leveraging the information stored in your database.

You can use hooks as follows:

```php
$node->hook("get", function($id) use ($existing_node) {
   return $existing_node;
});
```

where 

1. The first argument is the hook key, in string format.
2. The second argument is a PHP closure (you can pass it as a variable too).

Below you can see a full list of entities that support hooks and their keys.

#### Graph and SubGraph:

* **get(ID $node_id)**: called when ```get(ID $node_id)``` can't find the object in ```$nodes```. Enables you to access ```$node_ids``` to fetch the object from external sources. The return value is **NodeInterface**.
* **members**: called when ```members()``` can't find any objects in ```$nodes```. Enables you to access ```$node_ids``` to fetch the objects from external sources. The return value is **array** (of NodeInterface objects).

#### Edge:

* **head**: called when ```head()``` can't find the head node. Enables you to access ```$head_id``` to fetch it from external sources. The return value is **NodeInterface**.
* **tail**: called when ```tail()``` can't find the tail node. Enables you to access ```$tail_id``` to fetch it from external sources. The return value is **NodeInterface**.
* **predicate**: called when ```predicate()``` can't find the predicate object. Enables you to access ```$predicate_label``` to recreate it or fetch from external sources. The return value is **PredicateInterface**.

#### Node and SubGraph:

* **context**: called when ```context()``` can't find the context. Enables you to access ```$context_id``` to fetch it from external sources. The return value is **GraphInterface**.
* **edge(string $edge_id)**: called to retrieve an edge object from external sources. The return value must be an **EdgeInterface**
* **creator()**: called when ```creator()``` can't find the creator. Enables you to access ```$creator_id``` to fetch it from external sources. This can be used with any particle; be it an Actor, Object or Graph. The return value is **Actor**.

#### Notifications:

* **edge()**: called when ```edge()``` (in NotificationList.php) can't find the edge. Enables you to access ```$edge_id``` to fetch it from external sources. The return value is **\Pho\Lib\Graph\EdgeInterface**.

## <a name="r13" class="anchor">13. Handlers and Injectables</a>

#### Handlers 

Handlers are virtual methods in use by particles. Virtual methods are created to handle setters and getters in respect to edges and fields. There are four (4) types of handlers:

* **Get**: to retrieve an edge. [Default implementation](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/Handlers/Get.php).
* **Set**: to create an edge. [Default implementation](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/Handlers/Set.php).
* **Form**: not only to create an edge, but also its head node. [Default implementation](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/Handlers/Form.php).
* **Has**: to check if such an edge does exist. [Default implementation](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/Handlers/Has.php).

These adapters can be replaced, or more can be added using handler adapters via ```registerHandlerAdapter(string $handler_key, string $handler_class)``` function. For example:

```php
$this->registerHandler(
            "form",
            \Pho\Kernel\Foundation\Handlers\Form::class);
```

#### Injectables

Objects that implement the [InjectableInterface](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/InjectableInterface.php) and use the [InjectableTrait](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/InjectableTrait.php) are easily extensible, with a plug-in variable architecture.

In order to inject a variable to an injectable object, just use

```php
$obj->inject("key", $booster);
```

Then, the ```$obj``` will be able to use the ```$booster``` object internally via:

```php
$this->injection("key");
```

The use Injectable is discouraged, as it may represent security holes if not used properly. But you can use it when you must. Currently the only class that implements it by default is [AbstractEdge](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/AbstractEdge.php).



## <a name="r14" class="anchor">14. ACL (Access Control Lists)</a>

Acl stands for "access-control-lists". Pho handles access to nodes and graphs similarly to how UNIX handles access to files and folders, hence the name.

With Pho:

* a node is the UNIX equivalent of a file.
* a graph is the UNIX equivalent of a directory.

In terms of privileges;

* read remains the same for both.
* write reamins the same for both.
* UNIX' execute is Pho's subscribe.
* In addition, Pho introduces the "manage" privilege.

Due to this additional privilege (e.g. "manage"), Pho uses a hexadecimal system to manage privileges, in contrast to UNIX' octal.

Plus, in terms of privilege groups, Pho introduces an additional one, called "subscribers":

* UNIX' u (users) remain the same. The u group is the owner of the object (or the actor itself, if it's an actor).
* s (subscriber) is a new privilege group. It consists of all the subscribers of this node. The subscribers include the head nodes of any edge that extends the Subscribe edge. 
* g (graph) is same as UNIX' g (group). It is the group of actors that belong to the same context with the "u" of this node.
* o (others) is same as UNIX' o. It is "others", meaning actors that belong to graphs not included by the context that the "u" belongs to.

Additionally, one can set up fine-grained privileges per actor and graph, using Pho's advanced access control lists, which is again, inspired by UNIX' access control lists, but slightly different.

Again, similarly to UNIX, Pho also has "sticky-bit" which ensures the privileges of an entity (or object) can only be changed by its owner, and not by the admins.

One can change a node's privileges with this simple command:

```php
$node->acl()->chmod(0x1e741);
```

where the parameter is (from left-to-right):

* 0x: is PHP's hexadecimal declaration. So it must be there.
* 1: is the sticky bit. Similar to UNIX, it may be 0 or 1.
* e: is the "u". This gives the u "manage" privilege, in addition to "read", "write" and "subscribe". So it's 1(subscribe) + 2(write) + 4(read) + 8(manage) = 15("e" in hexadecimal).
* 7: is the "s". Subscribers can write to, read and subscribe to this node, but not manage it.
* 4: is the "g". People in the same graph with this node.
* 1: is the "o" -- outsiders.

So for example, if this $node was a group (like a Facebook Group), this means:

* Only the group owner can edit or assign new privileges.
* The group managers can do anything, except, see above.
* The members (subscribers) of this group can post (write) and read anything.
* The people who are part of this network can read and subscribe the contents of this group, which means it's a read-only public one.
* The outsiders can only see the description of ../. 

The permissions table is as follows;

|           | Admin            | Read               | Write          | Execute (Subscribe)
| --------- | ---------------- | ------------------ | -------------- | --------------------
| Actor     | Manage profile   | See full profile.  | Edit profile   | Follow/become friends (or if friends already, react)
| Object    | Manage reactions | Read               | Edit           | Subscribe/*react*
| Graph     | Moderate/profile | Read contents      | Post content   | Subscribe

## <a name="r15" class="anchor">15. Project Directory</a>

Pho is designed in microservices architecture. This is a directory of Pho stack projects; it starts with foundational packages and goes up to the user-level.

Github                                                           | Description        
---------------------------------------------------------------- | -------------------------------------------------
[pho-lib-graph](http://github.com/phonetworks/pho-lib-graph)     | General purpose graph library.
[pho-framework](http://github.com/phonetworks/pho-framework)     | A stateless framework that establishes the core principles of the Pho stack.
[pho-microkernel](http://github.com/phonetworks/pho-microkernel) | Augments the framework with services and ACL (access-control-lists), rendering it stateful.
[pho-kernel](http://github.com/phonetworks/pho-kernel)           | A basic implementation of the pho-microkernel
[pho-cli](http://github.com/phonetworks/pho-cli)                 | Command-line interface to help compile graphql files and initialize projects.
[pho-server-rest](http://github.com/phonetworks/pho-server-rest) | REST APIs that can interface with any programming language or via HTTP.

In addition, there are several repositories that help the aforementioned ones:

Github                                                                         | Description        
------------------------------------------------------------------------------ | -------------------------------------
[pho-lib-graphql-parser](http://github.com/phonetworks/pho-lib-graphql-parser) | General purpose GraphQL schema parser. Used by pho-compiler.
[pho-compiler](http://github.com/phonetworks/pho-compiler)                     | Compiles the GraphQL files into PHP interpretables. Used by pho-cli.


Last but not least, the REST API language-bindings can be found at [https://github.com/pho-clients](https://github.com/pho-clients). Pho-microkernel service adapters can be found at [https://github.com/pho-adapters](https://github.com/pho-adapters). Plus, a number of sample GraphQL implementations can be found under [https://github.com/phonetworks/pho-recipes](https://github.com/pho-recipes)



## <a name="r16" class="anchor">16. Compiling a Recipe</a>

Here is the steps to compile a recipe.
The Web recipe ([https://github.com/pho-recipes/Web](https://github.com/pho-recipes/Web)) will be used which is also the recipe used for [GraphJS](https://graphjs.com/).

1. Clone the repo: `https://github.com/pho-recipes/Web`.

    This contains source files that will be used to generate PHP files.

1. Clone the repo: `https://github.com/phonetworks/pho-cli` and install composer dependencies.

    This contains CLI to generate files from recipe using remote compiler.

1. Run the following command from the directory of **pho-cli**:
    ````
    ./bin/php.php build ~/php-recipes-web ~/pho-recipes-web-compiled
    ````
    The first argument is the source directory containing graphql schema.
    The second argument is the destination directory where the generated files are placed.

> Note: The repo of each **pho-recipes** contains **.compiled** directory which consists the latest compiled code.



## <a name="r17" class="anchor">17. More resources...</a>

For a full list of Phở Kernel classes and methods, refer to:

1. [PHPDocumentor auto-generated reference](http://phonetworks.org/api/index.html).
2. [Pho-Lib-Graph docs](https://github.com/phonetworks/pho-lib-graph/tree/master/docs)
3. [Pho-Framework docs](https://github.com/phonetworks/pho-framework/tree/master/docs)
