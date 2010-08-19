# Model View Controller

Kohana is organized around the Model, View, Controller architecture, usually just [MVC](http://en.wikipedia.org/wiki/Model-View-Controller). MVC benefits include:

* Isolation of business logic from user interface
* Ease of keeping DRY
* Making it clear where different types of code for easier maintenance.

## Models

A model represents the information (data) of the application and the rules to manipulate that data. In the case of Kohana, models are primarily used for managing the rules of interaction with a corresponding data source (ex: database table). In most cases, one table in your database will correspond to one model in your application. The bulk of your application’s business logic will be concentrated in the models.

Kohana has modules to facilate the modeling needs of your application. For databases, you may use the following modules:

* Database
* ORM

Kohana has the Validate class which can help you create rules for your models.

## Views

Views represent the user interface of your application. In Kohana, views are php files that performs tasks related solely to the presentation of the data. Views handle the job of providing data to the web browser or other tool that is used to make requests from your application.

Kohana View class together with other helper classes that will help you with your views such as rendering HTML elements and creating URLs. You can use Kohana View to create templates so that you can reuse portions of the user interface such as headers, footers and sidebars.

## Controllers

Controllers provide the &quot;glue&quot; between models and views. In Kohana, controllers are responsible for processing the incoming requests from the web browser (or from CLI), interrogating the models for data, and passing that data on to the views for presentation.
