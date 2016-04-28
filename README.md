Piol - PHP I/O Library
====

Piol ( PHP I/O Library ) is a library for managing files, directories and performing
common operation on files and directories like copy, move, rename, etc. 
It was extracted from Frozen framework and has been revised in order 
    to let this code be more useful.

## Content :

This package contains :


docs/               <-- API documentation

src/                <-- full documented source code

test/               <-- unit test classes and data

vendor/             <-- empty but ready for composer packages (used for running unit tests)

LICENSE             <-- your usage license :)

README.md           <-- instructions and description of whole package

composer.json       <-- composer configuration

composer.phar       <-- instance of composer

config_tests.php    <-- configuration file needed for command line test runner

run_tests.sh        <-- test runner for linux/unix

run_tests.php       <-- web test runner


## Installation & Usage : 

Inside your code simply add the use directives on top of the code files where you need, eg.:

`use Mbcraft/Piol/File;`
`use Mbcraft/Piol/Dir;`

the library will load with its default settings (file root jail as PIOL_ROOT_PATH) and everything
should work out of the box.

The library has no external dependencies, all used classes are inside the Mbcraft\Piol namespace.

If you define a PIOL_ROOT_PATH as a constant with 'define' directive inside php before library
usage you can define what your file root jail directory is. All paths of File's or Dir's
will start from this one (so they are relative). The definition of this constant is REQUIRED for this library
to work. If the library is used inside a Laravel project, the PIOL_ROOT_PATH is automatically set to the source file root.

## Running tests :

Under Linux, type 

`sudo php composer.phar update
./run_tests.sh`

The first command will download libraries necessary for running unit tests (it is not needed for the library to work)
while the second one will actually run the unit tests.

Alternatively, you can also run your test on web, loading the run_tests.php web page and setting its folder as web root on your virtual host.

All tests should work out of the box on Linux. 
On Windows permission tests are disabled due to missing support for permission change of files 
 (php chmod function will lie on its return value, and even inside OS there are problems in 
 changing file permissions due to a completely different permission model).

## Features :

- Easy File and Directory management with root jail feature (PIOL_ROOT_PATH) that
    can be set before using the library or is set automatically. Common methods for working with
files and directories, reading and writing file data and even csv data.
Useful features like rename, delete, move files and directories, directory visit, permission get
and set (Linux only) and much more.
- Zip creation and extraction (zip library required inside php) with ZipUtils class.
- Properties file management with PropertiesUtils class.
- Secure storage management : store files inside a protected directory.
    Save data as string, properties or xml.
- Upload handler : easy handle file uploads with UploadUtils class, even multiple file uploads are
handled easily.
- Cache class useful for caches is available : check the FlatDirCache class.
- Automatic Laravel framework integration.

## Documentation :

You can find the whole API documentation inside the docs/ directory, opening the index.html file
with your favorite browser.

## Info :

Current version : 2.0.0

## Latest changes :

- Refactored the namespace to be in "Mbcraft".
- Check PIOL_ROOT_PATH both from defined constant and $_ENV.
- Laravel automatic integration.
- Added JavaXmlPropertiesUtils class.
- Refactored the package of some classes.
- Fixed class names in documentation.
- Add openWriter($erase) parameter.

## Roadmap :

- Add implementation of Link class.

## Credits :

Making of :

Full source code and documentation was written by Marco Bagnaresi.
Some help was taken from Zeal and StackOverflow. StackOverflow references are put inside the code
when i used it.

Distribution :

I hope you find this library useful. The library is now open source since
i was not able to sell some licences for some bucks.


-Marco

