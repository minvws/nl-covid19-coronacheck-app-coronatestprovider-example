<x-app-layout>
<div class="card mt-3">
    <h5 class="card-header">Create new Test Result(s)</h5>
    <div class="card-body">
        <form method="POST">
            <div class="mb-3 row">
                <label for="testType" class="col-sm-2 col-form-label">Test Type</label>
                <div class="col-sm-10">
                    <select class="form-select" aria-label="Test Type" name="testType">
                        @foreach ($testTypes as $testType)
                            <option value="{{ $testType->uuid }}">{{ $testType->name  }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="testResult" class="col-sm-2 col-form-label">Test Result</label>
                <div class="col-sm-10">
                    <select class="form-select" aria-label="Test Result" name="testResult">
                        <option value="0">Negative</option>
                        <option value="1">Positive</option>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="birthdate" class="col-sm-2 col-form-label">Test Date</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="birthDate" name="sampleDate" value="{{ $defaultSampleDate }}">
                </div>
            </div>
            <div class="mb-3 row">
                <label for="token" class="col-sm-2 col-form-label">Token</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="token" name="token" aria-describedby="tokenInfo">
                    <div id="tokenInfo" class="form-text">Optional. Token must be at least 14 characters long.</div>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="verificationCode" class="col-sm-2 col-form-label">Verification Code</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="verificationCode" name="verificationCode" aria-describedby="verificationCodeInfo">
                    <div id="verificationCodeInfo" class="form-text">Optional.</div>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="testResultStatus" class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-10">
                    <select class="form-select" aria-label="Status" name="testResultStatus">
                        <option value="complete">Complete</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="birthdate" class="col-sm-2 col-form-label">Birthdate</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="birthDate" name="birthDate" aria-describedby="birthdateInfo" value="{{ $defaultBirthDate }}">
                </div>
            </div>
            <div class="mb-3 row">
                <label for="testType" class="col-sm-2 col-form-label">How many Test Results?</label>
                <div class="col-sm-10">
                    <select class="form-select" aria-label="How many Test Results?" name="tokenCount">
                        @for ($i = 1; $i < 50; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</div>
</x-app-layout>
