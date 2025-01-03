<div class="user_form container py-5">
    <form id="feedback-form" class="bg-white p-4 rounded-3 shadow-sm">
        <h2 class="text-center mb-4 fw-bold">Feedback Form</h2>

        <!-- Username Field -->
        <div class="form-sec mb-3">
            <label for="uname" class="form-label fw-bold">Enter Username:</label>
            <input type="text" class="form-control" id="uname" name="uname" placeholder="Enter your username" required>
        </div>

        <!-- Email Field -->
        <div class="form-sec mb-3">
            <label for="uemail" class="form-label fw-bold">Enter User Email:</label>
            <input type="email" class="form-control" id="umail" name="uemail" placeholder="Enter your email" required>
        </div>

        <!-- Gender Selection -->
        <div class="form-sec mb-3">
            <label class="form-label fw-bold">Select Gender:</label>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="male" name="ugender" value="male" required>
                    <label for="male" class="form-check-label">Male</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="female" name="ugender" value="female" required>
                    <label for="female" class="form-check-label">Female</label>
                </div>
            </div>
        </div>

        <!-- Feedback Message -->
        <div class="form-sec mb-3">
            <label for="umsg" class="form-label fw-bold">Enter Feedback:</label>
            <textarea class="form-control" id="umsg" name="umsg" rows="4" placeholder="Write your feedback here..." required></textarea>
        </div>

        <!-- Submit Button -->
        <div class="form-sec d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">Send</button>
        </div>
    </form>
</div>
