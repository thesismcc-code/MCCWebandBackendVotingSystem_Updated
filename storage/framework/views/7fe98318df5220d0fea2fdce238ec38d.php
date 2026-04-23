<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MCC - Student Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body,
        html {
            height: 100%;
            width: 100%;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        .wrapper {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        .left-panel {
            width: 58%;
            position: relative;
            background: url("<?php echo e(asset('images/image_1.png')); ?>") no-repeat center center;
            background-size: cover;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1;
        }

        .left-content {
            position: absolute;
            bottom: 12%;
            left: 10%;
            right: 10%;
            color: #ffffff;
            z-index: 2;
        }

        .left-content h1 {
            font-size: 3.5rem;
            line-height: 1.15;
            font-weight: 700;
            margin-bottom: 0.8rem;
        }

        .left-content p {
            font-size: 1.25rem;
            font-weight: 400;
            margin-bottom: 2rem;
            color: #ececec;
        }

        .left-actions button {
            background: #112a70;
            color: #fff;
            font-weight: 500;
            font-size: 0.95rem;
            border: none;
            padding: 0.8rem 1.6rem;
            border-radius: 6px;
            margin-right: 0.8rem;
            cursor: pointer;
            transition: 0.2s;
        }

        .left-actions button:hover {
            opacity: 0.9;
        }

        .right-panel {
            width: 42%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }

        .brand-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            width: 130px;
            height: auto;
            margin: 0 auto 0.5rem auto;
            display: block;
        }

        .portal-title {
            color: #0e2060;
            font-size: 1.35rem;
            font-weight: 700;
            margin-top: 1rem;
        }

        .sign-as {
            text-align: center;
            font-weight: 600;
            font-size: 1rem;
            color: #333;
            margin-bottom: 1.5rem;
        }

        .toggle-container {
            display: flex;
            justify-content: center;
            border: 1px solid #1c1c1c;
            border-radius: 999px;
            padding: 3px;
            width: max-content;
            margin: 0 auto 2.5rem auto;
        }

        .tab-btn {
            background: transparent;
            border: none;
            border-radius: 999px;
            padding: 0.6rem 1.6rem;
            font-size: 0.9rem;
            font-weight: 400;
            font-family: inherit;
            cursor: pointer;
            color: #333;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background-color: #122a6e;
            color: #ffffff;
            font-weight: 500;
        }

        .input-group {
            margin-bottom: 1.25rem;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: #1a1a1a;
        }

        .form-control {
            width: 100%;
            background: #f3f4f6;
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: inherit;
            color: #333;
            outline: none;
        }

        .form-control::placeholder {
            color: #8c8c8c;
        }

        .form-control:focus {
            box-shadow: 0 0 0 2px #d2d2d2;
        }

        .password-wrapper {
            position: relative;
        }

        .form-control.pr-icon {
            padding-right: 3rem;
        }

        .toggle-pass {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-submit {
            width: 100%;
            background: #11276a;
            color: #ffffff;
            border: none;
            border-radius: 999px;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 1rem;
            cursor: pointer;
            transition: opacity 0.2s ease-in-out;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        /* Hide new-student-only fields by default */
        .new-student-fields {
            display: none;
        }

        @media (max-width: 900px) {
            body {
                overflow-y: auto;
                overflow-x: hidden;
            }

            .wrapper {
                flex-direction: column;
                height: auto;
            }

            .left-panel {
                width: 100%;
                min-height: 500px;
            }

            .left-content {
                bottom: 8%;
                text-align: center;
                left: 5%;
                right: 5%;
            }

            .left-actions {
                justify-content: center;
                display: flex;
                flex-wrap: wrap;
                gap: 0.8rem;
            }

            .left-actions button {
                margin-right: 0;
            }

            .right-panel {
                width: 100%;
                padding: 3rem 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .left-content h1 {
                font-size: 2.2rem;
            }
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <div class="left-panel">
            <div class="overlay"></div>
            <div class="left-content">
                <h1>Welcome to the Official<br>MCC Voting Portal.</h1>
                <p>Your Vote, Your Voice — Anywhere.</p>
                <div class="left-actions">
                    <a href="<?php echo e(route('view.student-tutorials')); ?>">
                        <button type="button">Account Setup</button>
                    </a>
                    <a href="<?php echo e(route('view.students-how-to-vote')); ?>">
                        <button type="button">How to vote?</button>
                    </a>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="login-container">

                <div class="brand-section">
                    <img class="logo" src="<?php echo e(asset('icons/logo_white_bg.png')); ?>" alt="Mandaue City College Logo">
                    <h2 class="portal-title">Digital Voting System</h2>
                </div>

                <div class="sign-as">Sign as</div>

                <div class="toggle-container">
                    <button class="tab-btn active" type="button" data-type="old" onclick="switchStudentType(this)">Old
                        Student</button>
                    <button class="tab-btn" type="button" data-type="new" onclick="switchStudentType(this)">New
                        Student</button>
                </div>

                
                <form id="form-old-student" action="<?php echo e(route('validate-login')); ?>" method="POST" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="student_type" value="Old Student">

                    <div class="input-group">
                        <label for="student_id_old">Student ID</label>
                        <input type="text" class="form-control" id="student_id_old" name="student_id"
                            placeholder="STU-0**-0**">
                    </div>

                    <div class="input-group">
                        <label for="password_old">Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control pr-icon" id="password_old" name="password"
                                placeholder="Enter your password">
                            <button type="button" class="toggle-pass"
                                onclick="togglePasswordVisibility('password_old', 'eyeIcon_old')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" id="eyeIcon_old">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                    </path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Sign In</button>
                </form>

                
                <form id="form-new-student" action="<?php echo e(route('student.login')); ?>" method="POST" autocomplete="off"
                    style="display: none;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="student_type" value="New Student">

                    <div class="input-group">
                        <label for="student_id_new">Student ID</label>
                        <input type="text" class="form-control" id="student_id_new" name="student_id"
                            placeholder="STU-0**-0**">
                    </div>

                    <div class="input-group">
                        <label for="password_new">Create Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control pr-icon" id="password_new" name="password"
                                placeholder="Create password">
                            <button type="button" class="toggle-pass"
                                onclick="togglePasswordVisibility('password_new', 'eyeIcon_new')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" id="eyeIcon_new">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                    </path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password_confirm">Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control pr-icon" id="password_confirm"
                                name="password_confirmation" placeholder="Confirm password">
                            <button type="button" class="toggle-pass"
                                onclick="togglePasswordVisibility('password_confirm', 'eyeIcon_confirm')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" id="eyeIcon_confirm">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                    </path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Sign Up</button>
                </form>

            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId, iconId) {
            const passwordField = document.getElementById(fieldId);
            const isPassword = passwordField.getAttribute("type") === "password";
            passwordField.setAttribute("type", isPassword ? "text" : "password");

            const eyeIconSvg = document.getElementById(iconId);
            if (isPassword) {
                eyeIconSvg.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            } else {
                eyeIconSvg.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                `;
            }
        }

        function switchStudentType(btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const type = btn.getAttribute('data-type');
            const formOld = document.getElementById('form-old-student');
            const formNew = document.getElementById('form-new-student');

            if (type === 'new') {
                formOld.style.display = 'none';
                formNew.style.display = 'block';
            } else {
                formOld.style.display = 'block';
                formNew.style.display = 'none';
            }
        }
    </script>
</body>

</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/student/loginpage.blade.php ENDPATH**/ ?>