== About the Project ==

I started this project to provide myself with a reusable library of PHP code that I've built as extensions to my own
work and now to the Zend Framework. As such, this project requires the Zend Framework for some portions to function.

=== About the Folder Structure ===

In most of my projects I try to keep as much code outside the webroot as possible. To facilitate this, my projects
typically have three main directories that hold the bulk of the project code (backend), the minimal code needed in the
publicly accessible webroot (frontend) and the testing code (testing) which I normally put in a protected space on the
webroot of my development server and don't use at all in production environments. This particular project does not yet
have any frontend code since I am trying to build it be easily plugged into existing applications as library code (much
like the Zend framework). In this model, the backend directory goes into a location that is accessible via the PHP
includes path.