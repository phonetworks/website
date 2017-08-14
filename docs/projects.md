---
currentMenu: projects
---

## Projects

Pho is designed in microservices architecture. This is a directory of Pho stack projects; it starts with foundational packages and goes up to the user-level.

Github                                                           | Description        
---------------------------------------------------------------- | -------------------------------------------------
[pho-lib-graph](http://github.com/phonetworks/pho-lib-graph)     | General purpose graph library.
[pho-framework](http://github.com/phonetworks/pho-framework)     | A stateless framework that establishes the core principles of the Pho stack.
[pho-microkernel](http://github.com/phonetworks/pho-microkernel) | Augments the framework with services and ACL (access-control-lists), rendering it stateful.
[pho-kernel](http://github.com/phonetworks/pho-kernel)           | A basic implementation of the pho-microkernel
[pho-cli](http://github.com/phonetworks/pho-cli)                 | Command-line interface to help compile graphql files and initialize projects.

### Helpers

In addition, there are several repositories that help the aforementioned ones:

Github                                                                         | Description        
------------------------------------------------------------------------------ | -------------------------------------
[pho-lib-graphql-parser](http://github.com/phonetworks/pho-lib-graphql-parser) | General purpose GraphQL schema parser.
[pho-compiler](http://github.com/phonetworks/pho-compiler)                     | Used by pho-cli, the compiler helps compile the GraphQL files into PHP interpretables.


### Others

Pho-microkernel service adapters can be found at [https://github.com/pho-adapters](https://github.com/pho-adapters)

Last but not least, a number of sample GraphQL implementations can be found under [Pho Recipes](https://github.com/pho-recipes)
