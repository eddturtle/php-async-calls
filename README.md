PHP Async Caller
===============

###What it is
Delegate code to the Async class to manage and fork off into a new process. Useful for executing block of code which takes
less than desirable amount of time to complete - but which you don't need to know the result of. Api calls, heavy IO interaction
and running maintenance are some uses for this.

###Warning
This is in the vary early stages of development and executing code can be a difficult thing to achieve securely. Therefore,
for the time being, it's not advisable to use this on a production system.

###How to use
1. Add the library to Composer:

        "require": {
           "eddturtle/php-async-calls": "dev-master"
        }
2. Run `sudo composer update` in your repository
3. Include the Composer autloader file: `require "vendor/autoload.php";`
4. Start an Async queue:

        $async = new Async();
        $async->queue("sleep(10);");
Running this in your browser should load the page in <100ms.

Note: the queue will be processed once everything else on the page has been run, but can be kicked off manually by destroying
the object (aka, by `$async = null`)

###Options
Currently there 5 options available. This shows the possible options you can pass into the Async() constructor and their
default settings.

* `'debug' => false` If set to true, will also print out info, like what commands are being run.
* `'type' => self::TYPE_PHP` Has the option of running PHP directly or raw (TYPE_RAW) to run commands directly.
* `'async' => true` You can turn off async (useful for testing and seeing the affect).
* `'tmp-dir' => '/tmp/'` The directory to store temp files, needs write access.
* `'cleanup' => true` Whether to clean up the tmp-dir after it's been used.

###In the pipeline (future work)
* Work closely with curl requests.
* Logging to check result.

###Licence
Licenced under the MIT Licence, as attached in the project.
