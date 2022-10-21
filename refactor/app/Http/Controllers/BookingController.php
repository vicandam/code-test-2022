<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if($user_id = $request->get('user_id')) {

            $response = $this->bookingRepository->getUsersJobs($user_id);

        }
        elseif($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID'))
        {
            $response = $this->bookingRepository->getAll($request);
        }

        return response($response);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $job = $this->bookingRepository->with('translatorJobRel.user')->find($id);

        return response($job);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $response = $this->bookingRepository->store($request->__authenticatedUser, $data);

        return response($response);

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;
        $response = $this->bookingRepository->updateJob($id, array_except($data, ['_token', 'submit']), $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        $data = $request->all();

        $response = $this->bookingRepository->storeJobEmail($data);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        if($user_id = $request->get('user_id')) {

            $response = $this->bookingRepository->getUsersJobsHistory($user_id, $request);
            return response($response);
        }

        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->bookingRepository->acceptJob($data, $user);

        return response($response);
    }

    public function acceptJobWithId(Request $request)
    {
        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;

        $response = $this->bookingRepository->acceptJobWithId($data, $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->bookingRepository->cancelJobAjax($data, $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $data = $request->all();

        $response = $this->bookingRepository->endJob($data);

        return response($response);

    }

    public function customerNotCall(Request $request)
    {
        $data = $request->all();

        $response = $this->bookingRepository->customerNotCall($data);

        return response($response);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $user = $request->__authenticatedUser;

        $response = $this->bookingRepository->getPotentialJobs($user);

        return response($response);
    }

    public function distanceFeed(Request $request)
    {
        $data = $request->all();

        if (isset($data['distance']) && $data['distance'] != "") {
            $distance = $data['distance'];
        } else {
            $distance = "";
        }
        if (isset($data['time']) && $data['time'] != "") {
            $time = $data['time'];
        } else {
            $time = "";
        }
        if (isset($data['jobid']) && $data['jobid'] != "") {
            $jobId = $data['jobid'];
        }

        if (isset($data['session_time']) && $data['session_time'] != "") {
            $session = $data['session_time'];
        } else {
            $session = "";
        }

        if ($data['flagged'] == 'true') {
            if($data['admincomment'] == '') return "Please, add comment";
            $flagged = 'yes';
        } else {
            $flagged = 'no';
        }

        if ($data['manually_handled'] == 'true') {
            $manuallyHandled = 'yes';
        } else {
            $manuallyHandled = 'no';
        }

        if ($data['by_admin'] == 'true') {
            $byAdmin = 'yes';
        } else {
            $byAdmin = 'no';
        }

        if (isset($data['admincomment']) && $data['admincomment'] != "") {
            $adminComment = $data['admincomment'];
        } else {
            $adminComment = "";
        }
        if ($time || $distance) {

            Distance::where('job_id', '=', $jobId)->update(array('distance' => $distance, 'time' => $time));
        }

        if ($adminComment || $session || $flagged || $manuallyHandled || $byAdmin) {

            Job::where('id', '=', $jobId)->update(array(
                    'admin_comments' => $adminComment,
                    'flagged' => $flagged,
                    'session_time' => $session,
                    'manually_handled' => $manuallyHandled,
                    'by_admin' => $byAdmin)
            );

        }

        return response('Record updated!');
    }

    public function reopen(Request $request)
    {
        $data = $request->all();
        $response = $this->bookingRepository->reopen($data);

        return response($response);
    }

    public function resendNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->bookingRepository->find($data['jobid']);
        $jobData = $this->bookingRepository->jobToData($job);
        $this->bookingRepository->sendNotificationTranslator($job, $jobData, '*');

        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->bookingRepository->find($data['jobid']);

        try {
            $this->bookingRepository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

}
