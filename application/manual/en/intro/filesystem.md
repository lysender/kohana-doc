# Cascading Filesystem

In Kohana, there are three (3) __paths__ (directories) where most of the PHP codes are located. They are the following:

* __Application path__ - usually named `application` - contains the project codes and other files usually just created for the specific project.
* __Modules paths__ -usually named `modules` - Kohana extensions that can be reused by several projects such as database library, ORM and caching module.
* __System path__ - usually named `system` - Kohana's core files where basic functionality of the framework are located.

These three directories share the same directory structure. 

Typical structure for the __Application__ path:

	application/
		classes/
		config/
		messages/
		i18n/
		views/

Directory structure for __Modules__ path is organized by module name:

	modules/
		module1/
			classes/
			config/
			messages/
			i18n/
			views/
		module2/
			classes/
			config/
			messages/
			i18n/
			views/

Directory structure for __System__ path:

	system/
		classes/
		config/
		messages/
		i18n/
		views/
		
The Kohana filesystem is a heirarchy of directory structure. This means that a file
can be placed in _application_ or _modules_ or _system_ directory and doing so
will have an effect on which file is located first and which file will not be loaded.

Kohana uses __[Kohana::find_file]__ to locate files within the project such as loading classes,
configurations, messages and internalization files.

When a file is loaded by __Kohana::find_file__, it is searched in the following order:

Application Path
: Defined as `APPPATH` in `index.php`. The default value is `application`.

Module Paths
: This is set of paths as an associative array using [Kohana::modules] in `APPPATH/bootstrap.php`.
  Each of the values of the array will be searched in the order that the modules
  are added. These paths are usually located under the `modules` directory.

System Path
: Defined as `SYSPATH` in `index.php`. The default value is `system`. All of the
  main or "core" files and classes are defined here.

Application path has the highest precedence and the system path has the lowest. Module paths
precedence depends on how they are added in the modules path. They first path added is higher
than the next and so on. This makes it is possible to overload any file by placing a file
with the same name in a "higher" precedence directory.

Assuming we are loading these files:

* classes/cookie.php
* classes/controller/welcome.php
* classes/database.php
* classes/encrypt.php
* classes/kohana/cookie.php
* classes/kohana/encrypt.php
* classes/model/user.php
* config/database.php
* views/kohana/error.php
* views/user.php
* views/welcome.php

![Cascading Filesystem Infographic](/media/images/cascading_filesystem.png)

If you have a view file called `kohana/error.php` in the `APPPATH/views` and
`SYSPATH/views` directories, the one in application will be returned when
`kohana/error.php` is loaded because it is at the top of the filesystem.

## Types of Files

The top level directories of the application, module, and system paths has the following
default directories:

classes/
:  All classes that you want to [autoload](using.autoloading) should be stored here.
   This includes controllers, models, and all other classes. All classes must
   follow the [class naming conventions](about.conventions#classes).

config/
:  Configuration files return an associative array of options that can be
   loaded using [Kohana::config]. See [config usage](using.configuration) for
   more information.

i18n/
:  Translation files return an associative array of strings. Translation is
   done using the `__()` method. To translate "Hello, world!" into Spanish,
   you would call `__('Hello, world!')` with [I18n::$lang] set to "es-es".
   Of course, you need the Spanish translation file for the translation to work.
   See [translation usage](using.translation) for more information.

messages/
:  Message files return an associative array of strings that can be loaded
   using [Kohana::message]. Messages and i18n files differ in that messages
   are not translated, but always written in the default language and referred
   to by a single key. See [message usage](using.messages) for more information.

views/
:  Views are plain PHP files which are used to generate HTML or other output. The view file is
   loaded into a [View] object and assigned variables, which it then converts
   into an HTML fragment. Multiple views can be used within each other.
   See [view usage](usings.views) for more information.

## Finding Files

The path to any file within the filesystem can be found by calling [Kohana::find_file]:

    // Find the full path to "classes/cookie.php"
    $path = Kohana::find_file('classes', 'cookie');

    // Find the full path to "views/user/login.php"
    $path = Kohana::find_file('views', 'user/login');

# Vendor Extensions

We call extensions that are not specific to Kohana as "vendor" extensions.
For example, if you wanted to use [DOMPDF](http://code.google.com/p/dompdf),
you would copy it to `application/vendor/dompdf` and include the DOMPDF
autoloading class:

    require Kohana::find_file('vendor', 'dompdf/dompdf/dompdf_config.inc');

Now you can use DOMPDF without loading any more files:

    $pdf = new DOMPDF;

[!!] If you want to convert views into PDFs using DOMPDF, try the
[PDFView](http://github.com/shadowhand/pdfview) module.

