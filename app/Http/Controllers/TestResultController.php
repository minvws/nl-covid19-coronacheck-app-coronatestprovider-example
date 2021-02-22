<?php

namespace App\Http\Controllers;

use App\Services\TokenGeneratorService;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\TestResult;

class TestResultController extends BaseController
{
    /**
     * @return Application|Factory|View
     */
    public function view()
    {
        $testResults = TestResult::where('sampleDate','>',date("Y-m-d",time()-(config('app.ctp_test_max_age'))))->get();
        return view('testresult/view')
            ->with('testResults',$testResults)
            ->with('prefix',config('app.ctp_prefix'));
    }

    /**
     * @return Application|Factory|View
     */
    public function createForm()
    {
        $testTypes = app('db')->table("TestType")->get();
        $defaultBirthDate = date("Y-m-d",time()-(60*60*24*365*30));
        $defaultSampleDate = date("Y-m-d H:i",time()-(60*60*2));

        return view('testresult/createForm')
            ->with('testTypes',$testTypes)
            ->with('defaultBirthDate',$defaultBirthDate)
            ->with('defaultSampleDate',$defaultSampleDate);
    }

    /**
     * @return Application|Factory|View
     */
    public function displayCreated(Request $request, TestResult $tr, TokenGeneratorService $tgs)
    {
        if($request->input('tokenCount')) {
            $token = $request->input('token');
            $testType = $request->input('testType');
            $tokenCount = $request->input('tokenCount');
            $testResult = $request->input('testResult');
            $verificationCode = $request->input('verificationCode');
            $birthdate = $request->input('birthDate');
            $sampleDate = $request->input('sampleDate');
            $testResultStatus = $request->input('testResultStatus');

            $newTestResults = array();

            if($tokenCount > 1) {
                for($i = 1; $i <= $tokenCount; $i++) {
                    $token = $tgs->getRandomToken();
                    $newTestResults[] = TestResult::create([
                        'token' => $token,
                        'testTypeId' => $testType,
                        'result' => $testResult,
                        'birthDate' => $birthdate,
                        'sampleDate' => $sampleDate,
                        'verificationCode' => $verificationCode,
                        'status' => $testResultStatus,
                    ]);
                }
            }
            else {
                if(empty($token))
                    $token = $tgs->getRandomToken();
                $newTestResults[] = TestResult::create([
                    'token' => $token,
                    'testTypeId' => $testType,
                    'result' => $testResult,
                    'birthDate' => $birthdate,
                    'sampleDate' => $sampleDate,
                    'verificationCode' => $verificationCode,
                    'status' => $testResultStatus,
                ]);
            }
        }

        return view('testresult/view')
            ->with('testResults',$newTestResults)
            ->with('prefix',config('app.ctp_prefix'));
    }
}

