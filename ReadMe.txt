== About the Project ==

This project is mostly a collection of the reusable components I've created in
my own work. Be aware that some items extend Zend Framework classes and will
require access to that library in order to function.

=== About the Folder Structure ===

I try to keep as much code outside the webroot as possible. To facilitate this,
my projects typically reside in a directory just outside of the webroot which I
add to the PHP include_path. The project code is easily included using the Zend
conventions and can also work with an auto-loader.

The projects, in turn, have sub-directories to hold code needed in the
publicly accessible webroot (_FrontEnd) and the testing code (_Testing). Each of
these is then brought into the webroot using a symlink (or an Apache Alias
directive). Generally the test code is linked into an authenticated realm on the
development server and ignored in production environments.