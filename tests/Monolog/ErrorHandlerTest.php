<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog;

use Monolog\Handler\TestHandler;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $testHandler;
    public function testHandleError()
    {
        $errHandler = $this->getErrorHandler();

        $errHandler->registerErrorHandler(array(E_USER_NOTICE => Logger::EMERGENCY), false);
        trigger_error('Foo', E_USER_ERROR);
        $this->assertCount(1, $this->testHandler->getRecords());
        $this->assertTrue($this->testHandler->hasErrorRecords());
        trigger_error('Foo', E_USER_NOTICE);
        $this->assertCount(2, $this->testHandler->getRecords());
        $this->assertTrue($this->testHandler->hasEmergencyRecords());
    }

    private function getErrorHandler()
    {      
        $logger = new Logger('test', array($handler = new TestHandler));
        $this->testHandler = $handler;
        return new ErrorHandler($logger);
    }
  

    public function testParentErrorHandlerCall(){
        $mock = $this->getMock('ParentErrorHandler',array('h'));
        $mock->expects($this->exactly(2))->method('h');
        $old = set_error_handler(array($mock,'h'));
        $errHandler = $this->getErrorHandler();
        $errHandler->registerErrorHandler();
        trigger_error('Foo', E_USER_ERROR);
        trigger_error('Foo', E_USER_NOTICE);

        //cleanup
        set_error_handler($old);
        
    }    
}
