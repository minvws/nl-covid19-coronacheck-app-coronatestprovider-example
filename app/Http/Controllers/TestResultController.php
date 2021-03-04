<?php

namespace App\Http\Controllers;

use App\Services\TokenService;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\TestResult;

class TestResultController extends BaseController
{
    /**
     * @return \Illuminate\View\View|\Laravel\Lumen\Application
     */
    public function view(TokenService $ts)
    {
        $testResults = TestResult::where('sampleDate','>',date("Y-m-d",time()-(config('app.ctp_test_max_age'))))->get();

        return view('testresult/view')
            ->with('testResults',$testResults)
            ->with('tokenService',$ts)
            ->with('prefix',config('app.ctp_prefix'));
    }

    /**
     * @return \Illuminate\View\View|\Laravel\Lumen\Application
     */
    public function createForm()
    {
        $testTypes = app('db')->table("TestType")->get();
        $defaultBirthDate = date("Y-m-d",time()-(60*60*24*365*30));
        $defaultSampleDate = date("Y-m-d H:i",time()-(60*60*2));

        return view('testresult/createForm')
            ->with('testTypes',$testTypes)
            ->with('defaultBirthDate',$defaultBirthDate)
            ->with('defaultSampleDate',$defaultSampleDate)
            ->with('defaultFirstName',"Bob")
            ->with('defaultLastName',"Bouwer");
    }

    /**
     * @return \Illuminate\View\View|\Laravel\Lumen\Application
     */
    public function displayCreated(Request $request, TestResult $tr, TokenService $ts)
    {
        if($request->input('tokenCount')) {
            $protocolVersion = $request->input('protocolVersion');
            $token = $request->input('token');
            $testType = $request->input('testType');
            $tokenCount = $request->input('tokenCount');
            $testResult = $request->input('testResult');
            $verificationCode = $request->input('verificationCode');
            $phoneNumber = $request->input('phoneNumber');
            $birthdate = $request->input('birthDate');
            $firstName = $request->input('firstName');
            $lastName = $request->input('lastName');
            $sampleDate = $request->input('sampleDate');
            $testResultStatus = $request->input('testResultStatus');

            $newTestResults = array();

            if($tokenCount > 1) {
                for($i = 1; $i <= $tokenCount; $i++) {
                    $token = $ts->getRandomToken();
                    $newTestResults[] = TestResult::create([
                        'protocolVersion' => $protocolVersion,
                        'token' => $token,
                        'testTypeId' => $testType,
                        'result' => $testResult,
                        'birthDate' => $birthdate,
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'sampleDate' => $sampleDate,
                        'verificationCode' => $verificationCode,
                        'phoneNumber' => $phoneNumber,
                        'status' => $testResultStatus,
                    ]);
                }
            }
            else {
                if(empty($token))
                    $token = $ts->getRandomToken();
                else
                    $token = strtoupper($token);

                $newTestResults[] = TestResult::create([
                    'protocolVersion' => $protocolVersion,
                    'token' => $token,
                    'testTypeId' => $testType,
                    'result' => $testResult,
                    'birthDate' => $birthdate,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'sampleDate' => $sampleDate,
                    'verificationCode' => $verificationCode,
                    'phoneNumber' => $phoneNumber,
                    'status' => $testResultStatus,
                ]);
            }
        }

        return view('testresult/view')
            ->with('testResults',$newTestResults)
            ->with('tokenService',$ts)
            ->with('prefix',config('app.ctp_prefix'));
    }
}

