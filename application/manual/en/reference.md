# API Reference

Kohana has a very small set of core library that is often enough to get you started with common web application problems. There are also optional extensions called Kohana Modules that can be loaded when needed by your application.

Whether it is a core library or a module, each class is organized in such a way that they can be overriden by the framework user anytime by making use of the [cascading filesystem](intro.filesystem).

Each core or module class name is prefixed with `Kohana_` and the non-refixed class name is the generic name which is extending the core. Framework users can override the core / module class by creating their own class extending the core class. Let's put it this way:

Class Name             | Core Class Name
-----------------------|---------------------------------
Controller             | Kohana_Controller
Controller_Template    | Kohana_Controller_Template
HTML                   | Kohana_HTML
Database_Query_Builder | Kohana_Database_Query_Builder

The first 3 classes are core classes and the last is a database module class which is a Kohana module. Core classes are placed in the system path whereas module classes are placed in modules path. Each core class name is paired with a generic name ex: `Kohana_HTML` is paired with `HTML` and they exists it the system path. Framework users usually use the HTML class because it is convenient (ex: short name) and it is recommended to be that way.

If someone needs to add / extend functionality for the `HTML` class, all you need to do is create your own `HTML.php` under `application/classes` and extend `Kohana_HTML` and write your new functionality. When you call `HTML::some_function()`, the generic class loaded is your version of `HTML.php` because the application path has higher precedence in the cascading filesystem.

Learn more on [using the cascading filesystem](learn.filesystem).
