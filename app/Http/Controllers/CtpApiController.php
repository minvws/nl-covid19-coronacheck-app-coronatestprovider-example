<?php

namespace App\Http\Controllers;

use App\Services\SMSService;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\TestResult;

class CtpApiController extends BaseController
{

    private function getTestResultForValidToken($protocolVersion, $testResult, $verificationCode)
    {
        if($testResult->status == "pending") {
            $data = [
                "protocolVersion" => $protocolVersion,
                "providerIdentifier"=> config('app.ctp_prefix'),
                "pollDelay" => 300,
                "status" => "pending",
            ];
            return response()->json($data, 202);
        }
        else if($testResult->status == "complete") {
            if(
                // Allow (empty) verification code match when no phone number is specified
                (empty($testResult->phoneNumber) && $testResult->verificationCode == $verificationCode)
                ||
                // Do not allow empty verification code when phone number is specified
                (!empty($testResult->phoneNumber) && !empty($testResult->verificationCode) &&
                    $testResult->verificationCode == $verificationCode)
            ) {

                $testResult->fetchedCount = $testResult->fetchedCount + 1;
                $testResult->save();

                if($protocolVersion == "2.0") {
                    $data = [
                        "protocolVersion" => $protocolVersion,
                        "providerIdentifier" => config('app.ctp_prefix'),
                        "status" => "complete",
                        "result" => [
                            "uniqueId" => sha1($testResult->uuid),
                            "unique" => sha1($testResult->uuid),
                            "sampleDate" => gmdate("Y-m-d\TH:i:s\Z", strtotime($testResult->sampleDate)),
                            "testType" => $testResult->testTypeId,
                            "negativeResult" => ($testResult->result == 0),
                            "holder" => [
                                "firstNameInitial" => strtoupper(substr($testResult->firstName, 0, 1)),
                                "lastNameInitial" => strtoupper(substr($testResult->lastName, 0, 1)),
                                "birthDayOfMonth" => intval(date('d', strtotime($testResult->birthDate))),
                            ]
                        ]
                    ];
                }
                else {
                    $dayOfYear = date('z',strtotime($testResult->birthDate))+1;
                    $data = [
                        "protocolVersion" => $protocolVersion,
                        "providerIdentifier" => config('app.ctp_prefix'),
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
                }
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
                    $smsService->sendSMS(
                        $testResult->phoneNumber,
                        "Your CoronaCheck Verification Code is: ".$newVerificationCode
                    );
                }

                $data = [
                    "protocolVersion" => $protocolVersion,
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
        $protocolVersion = $request->header('CoronaCheck-Protocol-Version') ?: "1.0";

        $testResults = TestResult::where('token',$token)
            ->where('sampleDate','>',date("Y-m-d",time()-(60*60*24*3)))
            ->take(1)
            ->get();

        if(count($testResults) > 0) {
            $testResult = $testResults[0];

            return $this->getTestResultForValidToken($protocolVersion, $testResult, $verificationCode);
        }
        else {
            $data = [
                "protocolVersion" => $protocolVersion,
                "providerIdentifier"=> config('app.ctp_prefix'),
                "status" => "invalid_token",
            ];
            return response()->json($data, 401);
        }

        $data = ["status" => "error", "code" => 11119];
        return response()->json($data, 500);
    }
}

