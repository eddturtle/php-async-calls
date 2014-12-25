<?php


include __DIR__.'/../../lib/Async.php';


class AsyncTest extends PHPUnit_Framework_TestCase
{

    public function testCanBeCreated()
    {
        $async = new Async();
        $this->assertTrue(is_object($async));
    }


    public function testCanGetOptions()
    {
        $async = new Async();
        $options = $async->getOptions();

        $this->assertTrue(!empty($options));
    }


    public function testCanSetOptions()
    {
        $async = new Async();
        $original = $async->getOptions();

        $async->setOptions(['debug' => !$original['debug']]);
        $new = $async->getOptions();

        $this->assertTrue($original['debug'] !== $new['debug']);
    }


    public function testGetQueue()
    {
        $async = new Async();
        $original = $async->getQueue();

        $async->queue('sleep(5);');
        $new = $async->getQueue();

        $this->assertTrue(empty($original));
        $this->assertTrue(!empty($new) && count($new) === 1);
    }


    public function testQueue()
    {
        $async = new Async();
        $async->queue('sleep(5);');
        $queue = $async->getQueue();
        $this->assertTrue(!empty($queue));
    }


    public function testAsyncWorks()
    {
        $content = "Hello World. This is Async.";
        $file = "/tmp/test.txt";

        $async = new Async();
        $async->queue('file_put_contents("' . $file . '", "' . $content . '");');

        // Kick off Execution
        $async = null;

        // Need to wait for Async to finish
        sleep(1);

        $get = file_get_contents($file);
        if (file_exists($file)) {
            unlink($file);
        }

        $this->assertTrue($get === $content);
    }



}