# CodingTest

# ============== Booking Controller Refactor =====================
$repository - it would be nice to rename this to $bookingRepository
ex. the repository variable is intended for accessing booking repository therefore it would be nice to rename $bookingRepository rather than naming $repository itself. Imagine if there are lots of repositories within repositories directory.

#all PHP variable names are written in lowerCamelCase to follow proper naming conventions and consistency
$jobId
$adminComment
$manuallyHandled
$byAdmin
$jobData

public function update($id, Request $request)
{
#$cuser to $user proper naming convention format
}

public function immediateJobEmail(Request $request)
{
#removed unused assigned variable
#$adminSenderEmail = config('app.adminemail');
}

public function resendSMSNotifications(Request $request)
{
#removed this line as isn't use within method
$job_data = $this->repository->jobToData($job);
}
public function distanceFeed(Request $request)
{
if ($time || $distance) {

    # $affectedRows removed unnecessary assigned variable. 
    Distance::where('job_id', '=', $jobId)->update(array('distance' => $distance, 'time' => $time));
}

if ($adminComment || $session || $flagged || $manuallyHandled || $byAdmin) {

    #$affectedRows1 removed unnecessary assigned variable and re structure long lines of code for readability.
    #$affectedRows1 = Job::where('id', '=', $jobId)->update(array('admin_comments' => $adminComment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manuallyHandled, 'by_admin' => $byAdmin));

    Job::where('id', '=', $jobId)->update(array(
        'admin_comments' => $adminComment, 
        'flagged' => $flagged, 
        'session_time' => $session, 
        'manually_handled' => $manuallyHandled, 
        'by_admin' => $byAdmin)
    );
}
}
public function getPotentialJobs(Request $request)
{
#removed unused assigned variable
$data = $request->all();
}




# ============== Booking Repository Refactor =====================
public function getUsersJobs($user_id)
{
#$jobs = $cuser->jobs()->with('user.userMeta', 'user.average', 'translatorJobRel.user.average', 'language', 'feedback')->whereIn('status', ['pending', 'assigned', 'started'])->orderBy('due', 'asc')->get();
    $jobs = $cuser->jobs()
    ->with('user.userMeta', 'user.average', 'translatorJobRel.user.average', 'language', 'feedback')
    ->whereIn('status', ['pending', 'assigned', 'started'])
    ->orderBy('due', 'asc')
    ->get();
    
    #$jobitem to $jobItem
    foreach ($jobs as $jobItem) {
        if ($jobItem->immediate == 'yes') {
            $emergencyJobs[] = $jobItem;
        } else {
            $noramlJobs[] = $jobItem;
        }
    }

#return ['emergencyJobs' => $emergencyJobs, 'noramlJobs' => $noramlJobs, 'cuser' => $cuser, 'usertype' => $usertype];
#this line re structure to a shorter line for better readability
    return [
        'emergencyJobs' => $emergencyJobs,
        'noramlJobs'    => $noramlJobs,
        'cuser'         => $cuser,
        'usertype'      => $usertype
    ];
}

public function getUsersJobsHistory($user_id, Request $request)
{
# $jobs = $cuser->jobs()->with('user.userMeta', 'user.average', 'translatorJobRel.user.average', 'language', 'feedback', 'distance')->whereIn('status', ['completed', 'withdrawbefore24', 'withdrawafter24', 'timedout'])->orderBy('due', 'desc')->paginate(15);
# this line re structure to a shorter line of code for better readability

    $jobs = $cuser->jobs()
        ->with('user.userMeta', 'user.average', 'translatorJobRel.user.average', 'language', 'feedback', 'distance')
        ->whereIn('status', ['completed', 'withdrawbefore24', 'withdrawafter24', 'timedout'])
        ->orderBy('due', 'desc')
        ->paginate(15);

# return ['emergencyJobs' => $emergencyJobs, 'noramlJobs' => [], 'jobs' => $jobs, 'cuser' => $cuser, 'usertype' => $usertype, 'numpages' => 0, 'pagenum' => 0];
# this line re structure to a shorter line of code for better readability
    return [
        'emergencyJobs' => $emergencyJobs,
        'noramlJobs'    => [],
        'jobs'          => $jobs,
        'cuser'         => $cuser,
        'usertype'      => $usertype,
        'numpages'      => 0,
        'pagenum'       => 0
    ];

# return ['emergencyJobs' => $emergencyJobs, 'noramlJobs' => $noramlJobs, 'jobs' => $jobs, 'cuser' => $cuser, 'usertype' => $usertype, 'numpages' => $numpages, 'pagenum' => $pagenum];
# this line re structure to a shorter line of code for better readability
    return [
        'emergencyJobs' => $emergencyJobs,
        'noramlJobs'    => $noramlJobs,
        'jobs'          => $jobs,
        'cuser'         => $cuser,
        'usertype'      => $usertype,
        'numpages'      => $numpages,
        'pagenum'       => $pagenum
    ];
# remove unused variables
$usertype = '';
$noramlJobs = array();

# remove commented code
$jobs['data'] = $noramlJobs;
$jobs['total'] = $totaljobs;
}

public function store($user, $data)
{
# remove commented code
Event::fire(new JobWasCreated($job, $data, '*'));
$this->sendNotificationToSuitableTranslators($job->id, $data, '*');// send Push for New job posting
}
public function storeJobEmail($data)
{
# adjust code line spacing for better readability
}

public function getPotentialJobIdsWithUserId($user_id)
{
# changes in foreach loop adjust line spacing
     foreach ($job_ids as $k => $v)
     {
     }

    foreach ($job_ids as $k => $v) {     // checking translator town
        $job = Job::find($v->id);
        $jobuserid = $job->user_id;
        $checktown = Job::checkTowns($jobuserid, $user_id);

        if (($job->customer_phone_type == 'no' || $job->customer_phone_type == '') && $job->customer_physical_type == 'yes' && $checktown == false) {
            unset($job_ids[$k]);
        }
    }
}

public function sendNotificationTranslator($job, $data = [], $exclude_user_id)
{
# remove unncessary variable declaration
$msg_contents = '';
}

public function getPotentialTranslators(Job $job)
{
# remove commented code
//        foreach ($job_ids as $k => $v)     // checking translator town
//        {
//            $job = Job::find($v->id);
//            $jobuserid = $job->user_id;
//            $checktown = Job::checkTowns($jobuserid, $user_id);
//            if (($job->customer_phone_type == 'no' || $job->customer_phone_type == '') && $job->customer_physical_type == 'yes' && $checktown == false) {
//                unset($job_ids[$k]);
//            }
//        }
//        $jobs = TeHelper::convertJobIdsInObjs($job_ids);
}

private function changeTimedoutStatus($job, $data, $changedTranslator)
{
# removed commented code
//        if (in_array($data['status'], ['pending', 'assigned']) && date('Y-m-d H:i:s') <= $job->due) {

# remove un use variable
$old_status = $job->status;
}

private function changeCompletedStatus($job, $data)
{
# removed commented code
//        if (in_array($data['status'], ['withdrawnbefore24', 'withdrawafter24', 'timedout'])) {
//        }

# this will always return true so return false was removed
return true;
return false;
}

private function changeStartedStatus($job, $data)
{
# removed commented code and return false
    return true;
    return false;
}
private function changePendingStatus($job, $data, $changedTranslator)
{
# removed commented code
// removed $job_data = $this->jobToData($job); it's useless
// removed return false it's uselsess
}
public function acceptJob($data, $user)
{
# removed this unsed variables
$adminemail = config('app.admin_email');
$adminSenderEmail = config('app.admin_sender_email');
}

public function acceptJobWithId($job_id, $cuser)
{
# removed these unused variables
    $adminemail = config('app.admin_email');
    $adminSenderEmail = config('app.admin_sender_email');
}

public function cancelJobAjax($data, $user)
{
# removed this commented code
// Event::fire(new JobWasCanceled($job));
}
public function getPotentialJobs($cuser)
{
# removed this commented code
//        $jobs = TeHelper::convertJobIdsInObjs($job_ids);
}

public function customerNotCall($post_data)
{
# removed unused variables and lines spacing formatting
    $duedate = $job_detail->due;
    $start = date_create($duedate);
    $end = date_create($completeddate);
    $diff = date_diff($end, $start);
    $interval = $diff->h . ':' . $diff->i . ':' . $diff->s;
}
public function updateJob($id, $data, $user)
{
#rename $cuser variable to $user proper naming convention format
}
public function reopen($request)
{
#line spacing & formatting changes

#$Translator = Translator::create($data);
#removed unnecessary assigned $translator variable
#make it Translator::create($data);
}