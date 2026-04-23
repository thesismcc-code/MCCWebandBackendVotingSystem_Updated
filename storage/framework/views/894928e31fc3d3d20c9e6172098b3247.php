<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MCC - Student How to vote</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            background-color: #fafafa;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff;
            padding: 20px 50px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid #e1e5ea;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1e2850;
        }

        .navbar-brand img {
            height: 45px;
        }

        .navbar-brand h1 {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .navbar-menu a {
            text-decoration: none;
            color: #1e2850;
            font-weight: 600;
            font-size: 1.05rem;
        }

        .container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 30px;
        }

        .slide {
            display: none;
            flex-grow: 1;
            justify-content: space-between;
            align-items: center;
            gap: 10%;
        }

        .slide.active {
            display: flex;
            animation: fadeIn 0.4s ease forwards;
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

        .image-content {
            flex: 1;
            display: flex;
            justify-content: flex-end;
        }

        .image-content img {
            height: 65vh;
            max-height: 900px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(0px 8px 16px rgba(0, 0, 0, 0.1));
            border-radius: 24px;
        }

        .text-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 600px;
            margin-left: 30%;
        }

        .text-content h2 {
            color: #1e2850;
            font-size: clamp(2.2rem, 3.5vw, 3.2rem);
            font-weight: 700;
            margin-bottom: 25px;
        }

        .text-content p {
            color: #5d6379;
            font-size: clamp(1.15rem, 1.8vw, 1.45rem);
            line-height: 1.65;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            padding-bottom: 30px;
        }

        .page-btn {
            background-color: transparent;
            color: #1e2850;
            border: 2px solid #ccd2db;
            height: 42px;
            min-width: 42px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            padding: 0 14px;
        }

        .page-btn:hover:not(:disabled) {
            background-color: #f2f4f8;
            border-color: #1e2850;
        }

        .page-btn.active {
            background-color: #1e2850;
            color: #ffffff;
            border-color: #1e2850;
        }

        .page-nav {
            background-color: #1e2850;
            color: white;
            border-color: #1e2850;
            font-size: 1.2rem;
        }

        .page-nav.hidden {
            visibility: hidden;
        }

        @media (max-width: 990px) {
            .container {
                padding: 20px;
            }

            .slide {
                flex-direction: column;
                gap: 5vh;
                text-align: center;
                padding-top: 20px;
            }

            .image-content {
                justify-content: center;
            }

            .image-content img {
                height: 50vh;
            }

            .text-content {
                max-width: 100%;
                padding: 0 20px;
                align-items: center;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="navbar-brand">
            <img src="<?php echo e(asset('icons/logo_white_bg.png')); ?>" alt="MCC Logo">
            <h1>How to vote?</h1>
        </div>
        <div class="navbar-menu">
            <a href="<?php echo e(route('view.student')); ?>">Home</a>
        </div>
    </nav>

    <div class="container">

        <!-- Step 1 -->
        <div class="slide" id="step-1">
            <div class="image-content">
                <img src="<?php echo e(asset('vote/vote_1.png')); ?>" alt="Step 1 image showing start screen">
            </div>
            <div class="text-content">
                <h2>Click "Start"</h2>
                <p>To start voting click "Start" button.</p>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="slide" id="step-2">
            <div class="image-content">
                <img src="<?php echo e(asset('vote/vote_2.png')); ?>" alt="Step 2 image showing fingerprint scan">
            </div>
            <div class="text-content">
                <h2>Scan fingerprint</h2>
                <p>To verify the students, you must scan your fingerprint.</p>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="slide" id="step-3">
            <div class="image-content">
                <img src="<?php echo e(asset('vote/vote_3.png')); ?>" alt="Step 3 image showing verification success">
            </div>
            <div class="text-content">
                <h2>Verification</h2>
                <p>Once you are verified, click "Continue" to start voting.</p>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="slide" id="step-4">
            <div class="image-content">
                <img src="<?php echo e(asset('vote/vote_4.png')); ?>" alt="Step 4 image showing selection UI">
            </div>
            <div class="text-content">
                <h2>Selection</h2>
                <p>The students must be choose a candidates and vote.</p>
            </div>
        </div>

        <!-- Step 5 -->
        <div class="slide" id="step-5">
            <div class="image-content">
                <img src="<?php echo e(asset('vote/vote_5.png')); ?>" alt="Step 5 image showing position level selection">
            </div>
            <div class="text-content">
                <h2>Voting</h2>
                <p>In every category you only choose one candidate to vote and click "Submit".</p>
            </div>
        </div>

        <!-- Step 6 -->
        <div class="slide" id="step-6">
            <div class="image-content">
                <img src="<?php echo e(asset('vote/vote_6.png')); ?>" alt="Step 6 image showing are you sure modal">
            </div>
            <div class="text-content">
                <h2>Submit Votes</h2>
                <p>Once you are done voting, click "Submit" button to submit all your votes.</p>
            </div>
        </div>

        <!-- Step 7 -->
        <div class="slide" id="step-7">
            <div class="image-content">
                <img src="<?php echo e(asset('vote/vote_7.png')); ?>" alt="Step 7 image showing voting success prompt">
            </div>
            <div class="text-content">
                <h2>Vote Successfully</h2>
                <p>After submitted the system show a message that you are done voting, and the result will show to the
                    dashboard once the election is done.</p>
            </div>
        </div>

        <!-- Nav / Pagination Links -->
        <div class="pagination-container">
            <button class="page-btn page-nav" id="btn-prev" onclick="prevStep()">&#8592;</button>
            <button class="page-btn num-btn" onclick="goToStep(1)" data-step="1">1</button>
            <button class="page-btn num-btn" onclick="goToStep(2)" data-step="2">2</button>
            <button class="page-btn num-btn" onclick="goToStep(3)" data-step="3">3</button>
            <button class="page-btn num-btn" onclick="goToStep(4)" data-step="4">4</button>
            <button class="page-btn num-btn" onclick="goToStep(5)" data-step="5">5</button>
            <button class="page-btn num-btn" onclick="goToStep(6)" data-step="6">6</button>
            <button class="page-btn num-btn" onclick="goToStep(7)" data-step="7">7</button>
            <button class="page-btn page-nav" id="btn-next" onclick="nextStep()">&#8594;</button>
        </div>
    </div>

    <script>
        const totalSteps = 7;
        let currentStep = 1;

        // On Mount Initialization
        document.addEventListener('DOMContentLoaded', () => {
            updateView();
        });

        // Navigation controls logic
        function goToStep(step) {
            currentStep = step;
            updateView();
        }

        function nextStep() {
            if (currentStep < totalSteps) {
                currentStep++;
                updateView();
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateView();
            }
        }

        // View mutation
        function updateView() {
            // Update active states iteratively per iteration loops
            for (let i = 1; i <= totalSteps; i++) {
                const stepElement = document.getElementById(`step-${i}`);
                if (stepElement) {
                    if (i === currentStep) {
                        stepElement.classList.add('active');
                    } else {
                        stepElement.classList.remove('active');
                    }
                }
            }

            document.querySelectorAll('.num-btn').forEach(btn => {
                const associatedStep = parseInt(btn.getAttribute('data-step'));
                if (associatedStep === currentStep) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            const prevButton = document.getElementById('btn-prev');
            const nextButton = document.getElementById('btn-next');

            if (currentStep === 1) {
                prevButton.classList.add('hidden');
            } else {
                prevButton.classList.remove('hidden');
            }

            if (currentStep === totalSteps) {
                nextButton.classList.add('hidden');
            } else {
                nextButton.classList.remove('hidden');
            }
        }
    </script>

</body>

</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/student/howtovote.blade.php ENDPATH**/ ?>