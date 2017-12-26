<?php

namespace App;

trait RecordsActivity{

	protected static function bootRecordsActivity(){
        if(auth()->guest()) return;
		foreach(static::getActivitiesToRecord() as $event){
			static::$event(function($modal) use ($event){
            $modal->recordActivity($event);
	});

		}
		static::deleting(function($model){
            $model->activity()->delete();
        });
}
	protected static function getActivitiesToRecord(){
		return ['created'];
	}
	protected function recordActivity($event){
		$this->activity()->create([
				'type' => $this->getActivityType($event),
                'user_id' => auth()->id()
			]);
		//this is also perfect solution but the abve one is for practice
        // Activity::create([
        //         'type' => $this->getActivityType($event),
        //         'user_id' => auth()->id(),
        //         'subject_id' => $this->id,
        //         'subject_type' => get_class($this)
        //         ]);
        
    }
    protected function activity(){
    	return $this->morphMany('App\Activity','subject');
    } 

    protected function getActivityType($event){
        $type = strtolower((new \ReflectionClass($this))->getShortName());
        return "{$event}_{$type}";
    }
}