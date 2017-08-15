---
currentMenu: reference
---

## Reference

Phở Kernel is the core of the Phở Networks stack. Written in PHP7, it is a programmable interface, as well as a configuration, events broker for distributed micro social graphs.

Phở Kernel is **not** static, which means you can run multiple kernel instances in a thread, and you can halt and reboot each thread as many times as needed.

Once you install [pho-kernel](https://github.com/phonetworks/pho-kernel), If you have PHP 7.1+ installed on your system, you can play with Phở Kernel right away, by typing ```php -a``` in the ```examples/``` directory of the pho-kernel repository, and including the index.php ```include("index.php")``` Although there are easier ways as well using CLI and you can intera

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


## Installation

! TODO !

## Core Concepts

Pho Kernel is a tool that facilitates your (as a programmer) management of social graphs. Just like any other [graph](https://en.wikipedia.org/wiki/Graph_theory), it is formed by "nodes" and their relationships identified by "edges".

In [social networks](https://en.wikipedia.org/wiki/Social_network), there are three type of entities:

* Graphs
* Actors
* Objects

Each of these nodes can have a particular set of edges. For example, Graphs "contain", so they have the Contain edges.  Actors create and subscribe, so they have Create and Subscribe edges. Objects transmit, so they have Transmit edge.

Both edges and nodes are represented in the database with a universally unique identifier in the form of: "aa406464-8508-49e9-b13a-c38d0a67c157". At Pho-Kernel, we use a cryptographically secure [UUIDv4](https://en.wikipedia.org/wiki/Universally_unique_identifier#Version_4_.28random.29) generator to avoid collisions.

Overall, the picture below illustrates a mini social network, inspired by the movie [Matrix](https://en.wikipedia.org/wiki/The_Matrix) where **nodes** are represented by:

* Squares => Graph,
* Triangles => Actor,
* Circles => Object

and **edges** by arrows. The color of the arrow defines the Edge type;

* Blue => Transmits,
* Black => Creates,
* Red => Contains,
* Green => Subscribes

![Matrix movie social network](img/Matrix.png)

This tiny social network consists of:

* 5 characters (aka Actor): Neo, Morpheus, Trinity, Agent Smith and Spoon Boy.
* 2 graphs: Matrix (the network itself) and a group called Resistance Hovercraft, created by Morpheus.
* 4 objects:
      * "Everything begins with a choice" a status update by Morpheus transmitted to the Matrix network,
      * "Resistance is futile" a comment by Agent Smith in response to Morpheus' blog post,
      * "We must save Morpheus" a blog post by Neo transmitted to the Resistance Hovercraft group after Agent Smith captures Morpheus,
      * and "Kiss me" a private message by Trinity transmitted to Neo before she dies.

## Configuration

The list of all the configuration variables you can play with can be found in pho-microkernel's [defaults.php](https://github.com/phonetworks/pho-microkernel/blob/master/src/Pho/Kernel/defaults.php) file.

```php
array(
      "services" => array( // Services: type defines the adapter to use, and uri the service options.
          "database" => "apcu:", // ["type"=>"apcu", "uri"=> "" ],
          "logger" => "stdout:", // ["type"=>"stdout", "uri"=> "" ],
          "storage" => "filesystem:", // ["type"=>"filesystem", "uri"=> "" ],
          "events" => "local:", // ["type"=>"local", "uri"=> "" ]
      ),
      "tmp_path" => sys_get_temp_dir(), // Temporary folder to store files. For example uploaded files may go there.
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
        "editors" => Standards\VirtualGraph::class
      )
);
```


## Kernel Methods

Once you create the kernel object, you need to boot it up with:

```php
$kernel->boot();
```

Or if you have a custom founder (based on a non-standard Actor object) and you're running the kernel for the first time, you should pass it here:

```php
$kernel->boot($founder);
```

If this is not the first time the kernel is booted up, the argument will be ignored.

Once the kernel is booted up, you can query the outer and inner graphs as follows:

```
$space = $kernel->space(); // returns the outer graph.
$graph = $kernel->graph(); // returns the actual graph that all your nodes will become part of.
```

The outer graph, or "space" is a super-graph that has one and only one element, that is the graph.

To create a node in the graph, you just need to create the object by passing the kernel ($kernel) as its first argument. By default:

*  Actor objects have two constructor variables; $kernel and $context (must implement \Pho\Framework\ContextInterface, for example: $kernel->space() which is the outer graph or $kernel->graph() which is the inner graph)
* Object and Graph nodes are three constructor variables; $kernel, $context and $creator (which is an Actor object, the creator of this object)

Please note, while this is a valid way to create nodes, it is not advised, unless you're creating Actor nodes. Under normal circumstances, you'll create nodes via formative edges, which we will see later.

To query a node, you just use the kernel's ```gs()``` method, which is similar to UNIX' filesystem. You must know the ID of the node.

To retrieve a node with the given id:

```php
$node = $kernel->gs()->node("aa406464-8508-49e9-b13a-c38d0a67c157");
```

Similarly, you can retrieve an edge with its ID using ```$kernel->gs()->edge("edge-id")```

## Nodes

The default Pho Kernel node hierarchy, which is based on the AGO model is as follows:

&nbsp;

![Node Hierarchy](img/NodeHierarchy.png)

&nbsp;

For more information on the basics of working with nodes and edges, check out:

* [Lib-Graph Documentation](https://github.com/phonetworks/pho-lib-graph/tree/master/docs)
* [Framework Documentation](https://github.com/phonetworks/pho-framework/tree/master/docs)


Three types of nodes (actors, graphs and objects) can be summarized as follows:

#### Graphs

Graphs contain other nodes. This is their one and only function. A social network itself is a Graph. We call it the "Network". Another typical type of Graph that most social networks have is Groups. 

#### Actors
Actors can do three things;

1. Read
2. Write (forming new objects or editing them)
3. Subscribe (to get Notification from nodes)

In social network context, social network members are the Actors. To illustrate this, take the example of Facebook. Facebook itself is a Graph. The groups and friend lists on Facebook are also Graphs (micro graphs). And you, as a Facebook member or the pages you own are Actors, because they like (subscribe) and respond (write) objects.

#### Objects

Objects are what social networks are centered around. They are the fundamental units of sharing. A photo or status update can be examples of object. The only edge that originates from Objects is Edge, which does the exact opposite of Subscribe, e.g. sending Notifications.


## Services & Adapters

You can use ```Pho\Kernel\Kernel::service($service_id)``` to reach a service. Currently standard services on Pho-Kernel are:

Service Type | Description | Base Adapter   | Distributed Adapter | Cloud Adapter | Implements
------------ | ----------- | -------------- | ------------------- | ------------- |
**Database**     | As a proof of truth and to store fundamental graph information. | APCU | Redis | ~~DynamoDB~~ | ...
**Events**       | To make the kernel more flexible with event-driven programming. | Local | ZeroMQ | ... | ...
**Index**        | To make search and database query easier and faster. | MySQL | ElasticSearch | ... | ...
**Logger**       | To log for errors or debug information. | Stdout or File | Scribe | ... | ...
**Storage**      | To store and retrieve plaintext or binary files such as photos, videos, zip files etc. | Filesystem | OpenStack Swift | AWS S3 | ..


## Working with Events

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

Example usage:

```php
$node->on("modified", function() use ($node) {
    $node->persist();
});
```


## ACL (Access Control Lists)

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

## Reference

For a full list of Phở Kernel classes and methods, refer to our [PHPDocumentor auto-generated reference](http://phonetworks.org/api/index.html).