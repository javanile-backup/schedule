<?php
/**
 * 
 * 
 */

//
namespace Javanile\Schedule;

//
class Schedule
{  
    /**
     * 
     * 
     */
    private $_ts = null;
    
    /**
     * 
     * 
     */
    private $_time_table = array();
    
    /**
     * 
     * 
     */
    private $_time_table_file = null;
    
    /**
     * 
     * 
     */
    function schedule()
    {
        // override this
    }

    /**
     * 
     * 
     */
    protected function at($moment, $callable)
    {
        //
        $bt = debug_backtrace();
  
        //
        $key = md5($bt[0]['line'].':'.$bt[0]['line']);
        
        //
        $ts = strtotime($moment, $this->_ts);
        
        //
        if (!$this->isTabled($key, $ts)) 
        {
            //
            $this->setTabled($key, $ts);
            
            //
            if (is_string($callable) && method_exists($this, $callable)) 
            {
                call_user_func(array($this, $callable));
            }
        }
    }
    
    /**
     * 
     * 
     */
    private function isTabled($key, $ts) 
    {
        //
        echo 'ts:'.$ts.' - its:'.$this->_ts."<BR/>";
       
        //
        if (isset($this->_time_table[$key]))     
        {
            return $ts < $this->_time_table[$key];
        } 
        
        //
        else
        {
            return $ts > $this->_ts;
        }
    }
    
    /**
     * 
     * 
     */
    private function setTabled($key) 
    {
        //
        $this->_time_table[$key] = $this->_ts;
    }
    
    /**
     * 
     * 
     */
    public function ts($ts) 
    {
        //
        $this->_ts = $ts;
    }

    /**
     * 
     * 
     */
    public function prepare()
    {
        //
        $class = get_class($this);
        
        //
        $reflector = new \ReflectionClass($class);

        //
        $path = dirname($reflector->getFileName());
        
        //
        $this->_time_table_file = $path.'/'.$class.'.json';
        
        //
        if (file_exists($this->_time_table_file)) 
        {
            $this->_time_table = json_decode(file_get_contents($this->_time_table_file), true);
        }
    }
    
    /**
     * 
     * 
     */
    public function dispose()
    {
        //
        file_put_contents(
            $this->_time_table_file, 
            json_encode($this->_time_table)
        );
    }
    
    /**
     * 
     * 
     */
    public function info()
    {
        echo '<pre>';
        var_dump($this->_time_table);
        echo '</pre>';
    }
    
    /**
     * 
     * 
     * @param type $task
     */
    public static function task($task)
    {
        //
        $task->ts(time());
        
        //
        $task->prepare();
        
        //
        $task->schedule();
        
        //
        $task->dispose();
        
        //
        $task->info();
    }
    
    /**
     * 
     * 
     * 
     */
    public static function test($task, $moment)
    {
        //
        $ts = strtotime($moment);
        
        //
        echo 'testat: '. date('H:i:s d/m/Y', $ts)."<br/>";
        
        //
        $task->ts($ts);
        
        //
        $task->prepare();
        
        //
        $task->schedule();
        
        //
        $task->dispose();
        
        //
        $task->info();      
    }
}


