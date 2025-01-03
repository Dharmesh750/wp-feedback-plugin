<div class="user_form container">
    <form id="feedback-form">
        <div class="form-sec">
            <label for="uname">Enter Username:</label>
            <input type="text" class="u-name" id="uname" name="uname">
        </div>
        <div class="form-sec">
            <label for="uemail">Enter User email:</label>
            <input type="email" class="u-mail" id="umail" name="uemail">
        </div>
        <div class="form-sec">
            <label for="ugender">Select Gender:</label>
            <label for="male">Male</label>
            <input type="radio" class="ugender" id="male" name="ugender" value="male">
            <label for="female">Female</label>
            <input type="radio" class="ugender" id="female" name="ugender" value="female">
        </div>
        <div class="form-sec">
            <label for="umsg">Enter Feedback:</label>
            <textarea class="u-msg" id="umsg" name="umsg"></textarea>
        </div>
        <div class="form-sec">
            <input type="submit" class="save-btn" value="Send">
        </div>
    </form>
</div>