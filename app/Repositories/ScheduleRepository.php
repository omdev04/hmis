<?php

namespace App\Repositories;

use App\Models\Doctor;
use App\Models\HospitalSchedule;
use App\Models\Schedule;
use App\Models\ScheduleDay;
use App\Models\User;
use Arr;
use Auth;

/**
 * Class ScheduleRepository
 *
 * @version February 24, 2020, 5:55 am UTC
 */
class ScheduleRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'available_on',
        'available_from',
        'available_to',
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return Schedule::class;
    }

    public function getData()
    {
        $user = Auth::user();
        $data = [];

        $query = Doctor::with('doctorUser');
        if ($user->hasRole('Doctor')) {
            $query->where('user_id', $user->id);
        }
        $doctors = $query->get()->where('doctorUser.status', '=', 1)->pluck('doctorUser.full_name', 'id')->sort();
        $data['doctors'] = $doctors;
        $data['hospitalSchedule'] = HospitalSchedule::get()->toArray();
        $data['availableOn'] = Schedule::days;

        return $data;
    }

    public function prepareInputForScheduleDayItem($input)
    {
        $items = [];
        foreach ($input as $key => $data) {
            foreach ($data as $index => $value) {
                $items[$index][$key] = $value;
            }
        }

        return $items;
    }

    public function store($input)
    {
        $schedule = Schedule::create($input);

        $scheduleDayArray = Arr::only($input, ['available_on', 'available_from', 'available_to']);
        $scheduleDayItemInput = $this->prepareInputForScheduleDayItem($scheduleDayArray);
        foreach ($scheduleDayItemInput as $key => $data) {
            $data['doctor_id'] = $input['doctor_id'];
            $data['schedule_id'] = $schedule->id;
            $scheduleDay = ScheduleDay::create($data);
        }

        return true;
    }

    public function update($input, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->update($input);

        $scheduleDayArray = Arr::only($input, ['available_on', 'available_from', 'available_to']);
        $scheduleDayItemInput = $this->prepareInputForScheduleDayItem($scheduleDayArray);

        foreach ($scheduleDayItemInput as $key => $data) {
            $scheduleDay = ScheduleDay::whereScheduleId($id)
                ->where('available_on', $data['available_on']);

            $data['doctor_id'] = $input['doctor_id'];
            $data['schedule_id'] = $schedule->id;
            $scheduleDay->update($data);
        }

        return true;
    }

    //    public function updateDoctorSchedule($input, $id)
    //    {
    //        $schedule = Schedule::findOrFail($id);
    //        $schedule->update($input);
    //
    //        $data = Arr::only($input, ['available_on', 'available_from', 'available_to']);
    //        $scheduleDay = ScheduleDay::whereScheduleId($id)->where('available_on', $data['available_on']);
    //        $data['doctor_id'] =  getLoggedInUser()->owner_id;
    //        $data['schedule_id'] = $schedule->id;
    //        $scheduleDay->update($data);
    //
    //        return true;
    //    }

    public function getDoctorSchedule($input)
    {
        $data['scheduleDay'] = ScheduleDay::where('doctor_id', $input['doctor_id'])->Where('available_on',
            $input['day_name'])->get();

        $data['perPatientTime'] = Schedule::whereDoctorId($input['doctor_id'])->get();

        return $data;
    }
}
