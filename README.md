# Schedule

Run schedulated code 

## How-to with: Schedule class

```php
<?php

// include helper class
use Javanile\Schedule\Schedule;

// define your jobs
class MyJobs extends Schedule
{
  // override this method to customize
  public function schedule()
  {
    //
    $this->at('08:00', 'sayGoodMorning');
    
    //
    $this->at('16:00', 'sayGoodEvening');
  }

  public function sayGoodMorning()
  {
    echo "Good Morning!";
  }

  public function sayGoodEvening()
  {
    echo "Good Evening!";
  }
}

// add polling cron 
Schedule::task(new MyJobs());
```
