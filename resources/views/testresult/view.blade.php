<x-app-layout>
    <div class="card mt-3">
        <h5 class="card-header">Test Results</h5>
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Token</th>
                    <th scope="col">Verification Code</th>
                    <th scope="col">Test Type</th>
                    <th scope="col">Sample Date</th>
                    <th scope="col">Birth Date</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Phone Number</th>
                    <th scope="col">Counters</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($testResults as $testResult)
                    <tr>
                        <th>{{ $prefix }}-{{ $testResult->token }}-{{ $tokenService->generateChecksum($testResult->token)  }}2</th>
                        <td>{{ $testResult->verificationCode ?: "None" }}</td>
                        <td>{{ $testResult->testTypeId }}</td>
                        <td>{{ $testResult->sampleDate }}</td>
                        <td>{{ $testResult->birthDate }}</td>
                        <td>{{ $testResult->firstName }}</td>
                        <td>{{ $testResult->lastName }}</td>
                        <td>{{ $testResult->phoneNumber }}</td>
                        <td>
                            <span class="badge {{ $testResult->fetchedCount <= 3 ? "bg-success" : "bg-danger" }}">
                                Fetched <span class="badge bg-light text-dark">{{ $testResult->fetchedCount }}</span>
                                <span class="visually-hidden">Fetched</span>
                            </span>
                            <span class="badge {{ $testResult->smsCounter <= 3 ? "bg-success" : "bg-danger" }}">
                                SMS Limit <span class="badge bg-light text-dark">{{ $testResult->smsCounter }}</span>
                                <span class="visually-hidden">SMS Limit</span>
                            </span>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
