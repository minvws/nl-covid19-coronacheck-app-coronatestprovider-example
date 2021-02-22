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
                    <th scope="col">Fetched Counter</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($testResults as $testResult)
                    <tr>
                        <th>{{ $prefix }}-{{ $testResult->token }}-Y2</th>
                        <td>{{ $testResult->verificationCode }}</td>
                        <td>{{ $testResult->testTypeId }}</td>
                        <td>{{ $testResult->sampleDate }}</td>
                        <td>{{ $testResult->birthDate }}</td>
                        <td>{{ $testResult->fetchedCount }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
