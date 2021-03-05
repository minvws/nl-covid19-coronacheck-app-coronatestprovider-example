<?php

namespace App\Http\Controllers;

use App\Services\SMSService;
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
        $newTestResults = array();

        if($request->input('tokenCount')) {
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
            $tokenDistributionSMS = $request->input('tokenDistributionSMS');

            for($i = 0; $i < $tokenCount; $i++) {
                $token = $ts->getRandomToken();

                $newTestResults[] = TestResult::create([
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

                if($tokenDistributionSMS !== null) {
                    // Complete token
                    $tokenService = app(TokenService::class);

                    $code = config('app.ctp_prefix').'-'.$token.'-'.$tokenService->generateChecksum($token).'2';

                    // Send SMS
                    $smsService = app(SMSService::class);
                    $smsService->sendSMS(
                        $phoneNumber,
                        "Your CoronaCheck Token is: ".$code
                    );
                }
            }
        }

        return view('testresult/view')
            ->with('testResults',$newTestResults)
            ->with('tokenService',$ts)
            ->with('prefix',config('app.ctp_prefix'));
    }
}

