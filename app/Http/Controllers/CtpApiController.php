<?php

namespace App\Http\Controllers;

use App\Services\SMSService;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\TestResult;

class CtpApiController extends BaseController
{

    private function getTestResultV1($testResult, $verificationCode)
    {
        if($testResult->status == "pending") {
            $data = [
                "protocolVersion" => "1.0",
                "providerIdentifier"=> config('app.ctp_prefix'),
                "pollDelay" => 300,
                "status" => "pending",
            ];
            return response()->json($data, 202);
        }
        else if($testResult->status == "complete") {
            if($testResult->verificationCode == $verificationCode) {
                $testResult->fetchedCount = $testResult->fetchedCount + 1;
                $testResult->save();


                $dayOfYear = date('z',strtotime($testResult->birthDate))+1;

                $data = [
                    "protocolVersion" => "1.0",
                    "providerIdentifier"=> config('app.ctp_prefix'),
                    "status" => "complete",
                    "result" => [
                        "uniqueId" => sha1($testResult->uuid),
                        "unique" => sha1($testResult->uuid),
                        "sampleDate" => gmdate("Y-m-d\TH:i:s\Z", strtotime($testResult->sampleDate)),
                        "testType" => $testResult->testTypeId,
                        "negativeResult" => ($testResult->result == 0),
                        "checksum" => ($dayOfYear%65),
                    ]
                ];
                return response()->json($data, 200);
            }
            else {
                $data = [
                    "protocolVersion" => "1.0",
                    "providerIdentifier"=> config('app.ctp_prefix'),
                    "status" => "verification_required",
                ];
                return response()->json($data, 401);
            }
        }
    }

    private function getTestResultV2($testResult, $verificationCode)
    {
        if($testResult->status == "pending") {
            $data = [
                "protocolVersion" => "2.0",
                "providerIdentifier"=> config('app.ctp_prefix'),
                "pollDelay" => 300,
                "status" => "pending",
            ];
            return response()->json($data, 202);
        }
        else if($testResult->status == "complete") {
            if($testResult->verificationCode == $verificationCode) {

                $testResult->fetchedCount = $testResult->fetchedCount + 1;
                $testResult->save();

                $data = [
                    "protocolVersion" => "2.0",
                    "providerIdentifier"=> config('app.ctp_prefix'),
                    "status" => "complete",
                    "result" => [
                        "uniqueId" => sha1($testResult->uuid),
                        "unique" => sha1($testResult->uuid),
                        "sampleDate" => gmdate("Y-m-d\TH:i:s\Z", strtotime($testResult->sampleDate)),
                        "testType" => $testResult->testTypeId,
                        "negativeResult" => ($testResult->result == 0),
                        "holder" => [
                            "firstNameInitial" => strtoupper(substr($testResult->firstName,0,1)),
                            "lastNameInitial" => strtoupper(substr($testResult->lastName,0,1)),
                            "birthDayOfMonth" => date('d',strtotime($testResult->birthDate)),
                        ]
                    ]
                ];

                return response()->json($data, 200);
            }
            else {
                // Check for Phone number to send SMS
                if(!empty($testResult->phoneNumber)
                    && strlen($testResult->phoneNumber) == 10
                    && $testResult->smsCounter < 4
                    && config('app.sms_enabled')
                ) {

                    // Generate and save Verification Code
                    $newVerificationCode = str_pad(rand(0,9999),4,"0",STR_PAD_LEFT);
                    $testResult->verificationCode = $newVerificationCode;
                    $testResult->smsCounter = $testResult->smsCounter + 1;
                    $testResult->save();

                    // Send SMS
                    $smsService = app(SMSService::class);
                    $smsService->sendSMS($testResult->phoneNumber,"Your CoronaCheck Verification Code is: ".$newVerificationCode);
                }

                $data = [
                    "protocolVersion" => "2.0",
                    "providerIdentifier"=> config('app.ctp_prefix'),
                    "status" => "verification_required",
                ];
                return response()->json($data, 401);
            }
        }

    }

    public function get_test_result(Request $request)
    {
        $token = $request->bearerToken();
        $verificationCode = $request->json()->get('verificationCode');

        $testResults = TestResult::where('token',$token)->where('sampleDate','>',date("Y-m-d",time()-(60*60*24*3)))->take(1)->get();

        if(count($testResults) > 0) {
            $testResult = $testResults[0];

            if($testResult->protocolVersion == 2) {
                return $this->getTestResultV2($testResult, $verificationCode);
            }
            else {
                return $this->getTestResultV1($testResult, $verificationCode);
            }
        }
        else {
            $data = [
                "protocolVersion" => "1.0",
                "providerIdentifier"=> config('app.ctp_prefix'),
                "status" => "invalid_token",
            ];
            return response()->json($data, 401);
        }

        $data = ["status" => "error", "code" => 11119];
        return response()->json($data, 500);
    }
}

