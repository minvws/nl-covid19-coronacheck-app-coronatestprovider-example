<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\TestResult;

class CtpApiController extends BaseController
{

    public function get_test_result(Request $request)
    {
        $token = $request->bearerToken();
        $verificationCode = $request->json()->get('verificationCode');

        $testResults = TestResult::where('token',$token)->where('sampleDate','>',date("Y-m-d",time()-(60*60*24*3)))->take(1)->get();

        if(count($testResults) > 0) {
            $testResult = $testResults[0];
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

                    $data = [
                        "protocolVersion" => "1.0",
                        "providerIdentifier"=> config('app.ctp_prefix'),
                        "status" => "complete",
                        "result" => [
                            "uniqueId" => sha1($testResult->uuid),
                            "unique" => sha1($testResult->uuid),
                            "sampleDate" => date("Y-m-d H:i",strtotime($testResult->sampleDate)),
                            "testType" => $testResult->testTypeId,
                            "negativeResult" => ($testResult->result == 0),
                            "checksum" => 54,
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

