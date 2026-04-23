<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MCC - Student Tutorials</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body, html {
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #5c6274;
            background-color: #fafafa;
        }

        .nav-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            border-bottom: 2px solid #eaebec;
            background: #fff;
            height: 70px; /* strict navigation lock natively ensuring fit natively nicely */
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-section img {
            height: 38px;
            width: auto;
            border-radius: 50%;
        }

        .logo-title {
            font-weight: 700;
            font-size: 18px;
            color: #1c2a4c;
        }

        .nav-home {
            text-decoration: none;
            font-weight: 600;
            color: #1c2a4c;
            font-size: 16px;
        }

        /*
           Wrapper adjustments maximizing width allowing enlargement
           min-height calculation perfectly snapping viewport seamlessly gracefully successfully
        */
        .tutorial-wrapper {
            max-width: 1440px;
            width: 95%;
            margin: 0 auto;
            padding: 2vh 0 1vh;
            min-height: calc(100vh - 70px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .page-header {
            margin-bottom: 2vh;
        }

        .page-header h1 {
            font-size: clamp(22px, 2.5vw, 32px);
            font-weight: 700;
            color: #1c2a4c;
        }

        .slides-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .tutorial-slide {
            display: none;
            flex-grow: 1;
            flex-direction: column;
            justify-content: space-around;
            animation: fadeIn 0.4s ease forwards;
            height: 100%;
        }

        .tutorial-slide.active {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .step-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4%;
            padding: 1vh 0;
            /* Flex ensuring exact mapping inside layout purely strictly effectively correctly purely efficiently completely elegantly naturally securely identically */
        }

        .step-row.row-reverse {
            flex-direction: row-reverse;
        }

        /* Enlarge horizontal bounds explicitly strictly directly allowing scaling cleanly cleanly gracefully completely completely securely natively smartly successfully natively intelligently successfully optimally properly */
        .col-img {
            flex: 0 0 65%;
            max-width: 65%;
            text-align: center;
        }

        .col-img img {
            width: 100%;
            height: auto;
            /* strict capping guarantees images shrink slightly vs overriding pushing bottom elements out of visible strict monitor screens purely easily gracefully completely practically correctly dynamically efficiently optimally naturally practically exactly properly securely intelligently strictly successfully perfectly smartly effectively practically easily elegantly nicely reliably carefully */
            max-height: 33vh;
            object-fit: contain;
            display: inline-block;
        }

        .col-text {
            flex: 1;
        }

        .col-text h2 {
            font-size: clamp(18px, 1.8vw, 24px);
            font-weight: 700;
            color: #1c2a4c;
            margin-bottom: 1.2vh;
        }

        .col-text p {
            font-size: clamp(14px, 1.2vw, 17px);
            line-height: 1.5;
            color: #5c6274;
        }

        .pagination-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 2vh;
            padding-bottom: 2vh;
        }

        .btn-nav, .page-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 1px solid #1c2a4c;
            background: transparent;
            color: #1c2a4c;
            border-radius: 4px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s ease;
            user-select: none;
        }

        .page-num.active {
            background-color: #1c2a4c;
            color: #fff;
            pointer-events: none;
        }

        .btn-nav svg {
            width: 20px;
            height: 20px;
        }

        .btn-nav.hidden {
            visibility: hidden;
            pointer-events: none;
        }

        @media(max-width: 900px) {
            .tutorial-wrapper { min-height: auto; }
            .col-img img { max-height: none; }
            .step-row, .step-row.row-reverse {
                flex-direction: column;
                gap: 20px;
                text-align: center;
                margin-bottom: 40px;
            }
            .col-img, .col-text {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .tutorial-slide { display: none; }
            .tutorial-slide.active { display: block; }
        }
    </style>
</head>

<body>
    <header class="nav-header">
        <div class="logo-section">
            <img src="<?php echo e(asset('icons/logo_white_bg.png')); ?>" alt="logo">
            <span class="logo-title">Account Tutorial</span>
        </div>
        <a href="<?php echo e(route('view.student')); ?>" class="nav-home">Home</a>
    </header>

    <main class="tutorial-wrapper">
        <div class="page-header">
            <h1>Account Set up</h1>
        </div>

        <div class="slides-container">
            <!-- Slide #1 -->
            <div class="tutorial-slide" data-slide-idx="1">
                <div class="step-row">
                    <div class="col-img">
                        <img src="<?php echo e(asset('steps/step1.png')); ?>" alt="steps-1">
                    </div>
                    <div class="col-text">
                        <h2>Old Student</h2>
                        <p>For Old Students enter your student id and password.</p>
                    </div>
                </div>

                <div class="step-row row-reverse">
                    <div class="col-img">
                        <img src="<?php echo e(asset('steps/step2.png')); ?>" alt="steps-2">
                    </div>
                    <div class="col-text">
                        <h2>Activate your account</h2>
                        <p>Once you login, enter your email address to activate your account.</p>
                    </div>
                </div>
            </div>

            <!-- Slide #2 -->
            <div class="tutorial-slide" data-slide-idx="2">
                <div class="step-row">
                    <div class="col-img">
                        <img src="<?php echo e(asset('steps/step3.png')); ?>" alt="steps-3">
                    </div>
                    <div class="col-text">
                        <h2>New Students</h2>
                        <p>For new students enter your Student ID, and set up a password to create an account.</p>
                    </div>
                </div>

                <div class="step-row row-reverse">
                    <div class="col-img">
                        <img src="<?php echo e(asset('steps/step4.png')); ?>" alt="steps-4">
                    </div>
                    <div class="col-text">
                        <h2>Enter your email address</h2>
                        <p>To activate your account enter your email address and click "Verify Email" button.</p>
                    </div>
                </div>
            </div>

            <!-- Slide #3 -->
            <div class="tutorial-slide" data-slide-idx="3">
                <div class="step-row">
                    <div class="col-img">
                        <img src="<?php echo e(asset('steps/step5.png')); ?>" alt="steps-5">
                    </div>
                    <div class="col-text">
                        <h2>Email Verification</h2>
                        <p>To verify your email address enter the 4 digit pin that sent to your email address.</p>
                    </div>
                </div>

                <div class="step-row row-reverse">
                    <div class="col-img">
                        <img src="<?php echo e(asset('steps/step6.png')); ?>" alt="steps-6">
                    </div>
                    <div class="col-text">
                        <h2>Complete Profile</h2>
                        <p>Once done you setup your profile information, click "Update Profile" button.</p>
                    </div>
                </div>
            </div>

            <!-- Slide #4 -->
            <div class="tutorial-slide" data-slide-idx="4">
                <div class="step-row" style="flex-grow: 0; align-items: flex-start; margin-top: auto; margin-bottom: auto;">
                    <div class="col-img">
                        <!-- Given it single block natively map cleanly -->
                        <img style="max-height: 45vh" src="<?php echo e(asset('steps/step7.png')); ?>" alt="steps-7">
                    </div>
                    <div class="col-text" style="align-self: center">
                        <h2>Home Page</h2>
                        <p>Once the election is done, the result will display in the home page or dashboard.</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="pagination-wrapper">
            <button id="prevBtn" class="btn-nav hidden">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 18l-6-6 6-6" />
                </svg>
            </button>

            <button class="page-num" data-page="1">1</button>
            <button class="page-num" data-page="2">2</button>
            <button class="page-num" data-page="3">3</button>
            <button class="page-num" data-page="4">4</button>

            <button id="nextBtn" class="btn-nav">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18l6-6-6-6" />
                </svg>
            </button>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let currentSlide = 1;
            const totalSlides = 4;

            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const pageButtons = document.querySelectorAll('.page-num');
            const slidePanels = document.querySelectorAll('.tutorial-slide');

            function refreshSlider(idx) {
                slidePanels.forEach((slide) => {
                    if (parseInt(slide.dataset.slideIdx) === idx) {
                        slide.classList.add('active');
                    } else {
                        slide.classList.remove('active');
                    }
                });

                pageButtons.forEach(btn => {
                    if (parseInt(btn.dataset.page) === idx) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });

                prevBtn.classList.toggle('hidden', idx === 1);
                nextBtn.classList.toggle('hidden', idx === totalSlides);
            }

            pageButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    currentSlide = parseInt(e.target.dataset.page);
                    refreshSlider(currentSlide);
                });
            });

            prevBtn.addEventListener('click', () => {
                if (currentSlide > 1) {
                    currentSlide--;
                    refreshSlider(currentSlide);
                }
            });

            nextBtn.addEventListener('click', () => {
                if (currentSlide < totalSlides) {
                    currentSlide++;
                    refreshSlider(currentSlide);
                }
            });

            refreshSlider(currentSlide);
        });
    </script>
</body>

</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/student/tutorials.blade.php ENDPATH**/ ?>