<?php
	namespace App\Services\Project;

	use Cache, Carbon\Carbon;

	use App\Repositories\DutyRepository;

	class DutyService{
		private $dutyRepo;

		public function __construct(DutyRepository $dutyRepo){
			$this->dutyRepo = $dutyRepo;
		}

		public function index($attributes){
			\Log::info($attributes);
			$date = request('date') ? new Carbon(request('date')) :  (isset($attributes[0]) ? new Carbon($attributes[0]) : new Carbon());
			\Log::info($date);

			$timetables = $this->getTimeTable();
			$count = $timetables->count();

			$firstTimetable = $timetables->first();
			$firstDutyDate = new Carbon($firstTimetable->duty_date);

			$diffDays = $date->diffInDays($firstDutyDate);

			$number = $diffDays % $count;

			$duty = $timetables[$number];

			return $duty->name;
		}

		/*获取上班时刻表*/
		public function getTimeTable(){
			$key = config('project.cache.keys.timetable');
			if(!Cache::has($key) || Cache::get($key, '')){
				$this->setTimeTable();
			}
			return Cache::get($key);
		}

		/*设置上班时刻表*/
		public function setTimeTable(){
			$key = config('project.cache.keys.timetable');

			$duties = $this->dutyRepo->all();

			Cache::forever($key, $duties);
		}

		/*清空上班时刻表*/
		public function clearTimeTable(){
			$key = config('project.cache.keys.timetable');
			Cache::forget($key);
		}
	}