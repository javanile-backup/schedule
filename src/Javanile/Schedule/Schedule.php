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
    private $_debug = 0;
    
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
        if ($this->canRun($key, $moment)) 
        {
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
    private function canRun($key, $moment) 
    {
        //
        $period = null;
             
        //
        $momentTS = $this->getMomentTS($moment, $period);
        
        //
        $actualTS = $this->getActualTS($period);
  
        //
        $latestTS = $this->getLatestTS($key);
        
        //
        if ($this->_debug)
        {
            echo ' - '.$key.' '.$period
               . ' m('.$momentTS[0].'-'.$momentTS[1].')'
               . ' a('.$actualTS[0].'-'.$actualTS[1].')'
               . ' l('.$latestTS[0].'-'.$latestTS[1].')' 
               ;
        }
          
        //
        $canRun = $momentTS[0] <= $actualTS[0] 
               && $momentTS[1] <  $actualTS[1]
               && $latestTS[0] <  $momentTS[0];
          
        //
        if ($canRun) 
        {   
            //
            $this->_time_table[$key] = $actualTS;
            
            //
            if ($this->_debug) { echo ' RUN '.date('Y-m-d H:i:s', $this->_ts); }
        }  

        //
        if ($this->_debug) { echo "\n"; }
        
        //
        return $canRun;
    }
    
    /**
     * 
     * 
     */
    private function getMomentTS($moment, &$period)
    {
        //
        $x = null;
        
        //
        if (preg_match('/[0-9][0-9]:[0-9][0-9]/', $moment, $x))
        {
            //
            $t = strtotime($moment, $this->_ts); 
            
            //
            $p = 3600 * 24;
            
            //
            $d = (int) ($t / $p);
            
            //
            $i = $t % $p;
             
            //
            $period = $p;
            
            //
            return array($d, $i);
        }
        
        //
        die("Error moment parsing: ".$moment);
    }
    
    /**
     * 
     * 
     */
    private function getActualTS($p)
    {
        $d = (int) ($this->_ts / $p);
        
        $i = $this->_ts % $p;
                
        return array($d, $i);
    }
    
    /**
     * 
     * 
     * 
     */
    private function getLatestTS($key)
    {
        //
        return isset($this->_time_table[$key])
             ? $this->_time_table[$key]
             : array(0, 0);
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
    public function debug($flag) 
    {
        //
        $this->_debug = $flag;
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
    public function reset() 
    {
        //
        $this->prepare();
        
        //
        $this->_time_table = array();
        
        //
        @unlink($this->_time_table_file);
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
        //$task->info();
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
        $task->ts($ts);
        
        //
        $task->debug(1);
        
        //
        $task->prepare();
        
        //
        $task->schedule();
        
        //
        $task->dispose();
        
        //
        //$task->info();      
    }
}


