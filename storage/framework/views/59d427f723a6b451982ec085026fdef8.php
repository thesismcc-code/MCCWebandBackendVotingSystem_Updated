<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAO Final Results - Published</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* --- General Page Styling --- */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6f0;
            color: #1a3a1a;
            min-height: 100vh;
        }

        .btn-back {
            width: 40px;
            height: 40px;
            background-color: #1a5c38;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: transform 0.2s;
        }

        .btn-back:hover {
            transform: scale(1.1);
            color: white;
            background-color: #2d7a52;
        }

        .main-panel {
            background-color: #ffffff;
            border: 1px solid #e8ede3;
            border-radius: 20px;
            padding: 30px;
            min-height: 80vh;
            margin-top: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,.06);
        }

        .btn-publish {
            background-color: #1a5c38;
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            border: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            transition: background-color 0.2s;
        }

        .btn-publish:hover {
            background-color: #2d7a52;
            color: white;
        }

        /* --- Updated Layout Structure Rules --- */
        .results-board {
            background-color: #ffffff;
            border-radius: 12px;
            color: #111827;
            padding: 35px 0;
        }

        .divider-col {
            border-right: 1.5px solid #d1d5db;
        }

        @media (max-width: 991px) {
            .divider-col {
                border-right: none;
                border-bottom: 1.5px solid #d1d5db;
                padding-bottom: 25px;
                margin-bottom: 25px;
            }
        }

        .col-pad-lg {
            padding: 0 45px;
        }

        /* --- Section Badges --- */
        .role-badge {
            background-color: #d1fae5;
            color: #1a5c38;
            font-weight: 800;
            font-size: 1.15rem;
            text-transform: uppercase;
            padding: 8px 18px;
            border-radius: 5px;
            display: inline-block;
        }

        /* --- Container for Box Contents --- */
        .election-box {
            border: 1px solid #d4d4d8;
            border-radius: 6px;
            overflow: hidden;
            /* Contains cleanly to inner bounds for winner segments bottom wrapper tracking identically cleanly nested smoothly dynamically!. */
            font-size: 1.05rem;
            margin-bottom: 2rem;
        }

        /* List Candidate specifics! */
        .candidates-list {
            background-color: #ffffff;
            padding: 25px 30px;
        }

        .candidate-entry {
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
        }

        .candidate-entry:last-child {
            margin-bottom: 0;
        }

        .candidate-name {
            color: #1e293b;
        }

        /* Highlight specific bolding logic */
        .txt-strong-bold {
            font-weight: 800;
        }

        .text-count-regular {
            color: #1e293b;
            font-weight: 400;
            font-size: 1rem;
        }

        /* Winner panels exact rules targets! */
        .winner-panel-bg {
            background-color: #d1fae5;
            padding: 20px 30px;
        }

        /* Typography Helper correctly aligned exactly strictly */
        .winner-txt {
            font-weight: 800;
            text-transform: uppercase;
            color: #000;
            letter-spacing: 0.3px;
        }

        /* --- MODAL STYLES --- */
        .custom-modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
            padding: 40px 30px;
            position: relative;
            /* For absolute close button */
        }

        /* Custom absolute Close Button (Top Right) */
        .btn-close-absolute {
            position: absolute;
            top: 15px;
            right: 15px;
            opacity: 0.5;
            font-size: 1.2rem;
        }

        .btn-close-absolute:hover {
            opacity: 1;
        }

        /* Typography */
        .modal-title-custom {
            font-weight: 800;
            font-size: 1.8rem;
            color: #000;
            margin-bottom: 15px;
        }

        .modal-text-custom {
            color: #333;
            font-size: 1rem;
            line-height: 1.5;
            margin-bottom: 0;
            font-weight: 500;
        }

        /* Modal Actions */
        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
        }

        .btn-modal-cancel {
            background-color: #dc2626;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 6px;
            font-weight: 600;
        }

        .btn-modal-submit {
            background-color: #1bd810;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 6px;
            font-weight: 600;
        }
    </style>
</head>

<body class="p-3 p-md-4">

    <!-- Header Section -->
    <div class="container-fluid mb-2 px-0">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo e(route('view.sao-dashboard')); ?>" class="btn-back">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <h4 class="mb-0 fw-bold" style="color:#1a3a1a;">Final Results</h4>
        </div>
    </div>

    <!-- Main Content Panel -->
    <div class="main-panel">

        <!-- Action Button -->
        <div class="d-flex justify-content-end mb-4 pe-2">
            <button class="btn-publish" id="triggerConfirmBtn">
                PUBLISH OFFICIAL RESULTS
            </button>
        </div>

        <!-- NEW VISUALS STRUCTURE exactly strictly replacing inner UI native structurally specific mappings logic explicitly -->
        <div class="results-board shadow-sm w-100 mx-auto">
            <div class="row m-0 w-100">
                <!-- ============================ -->
                <!-- LEFT PANEL (President & VP) -->
                <!-- ============================ -->
                <div class="col-lg-6 divider-col col-pad-lg pe-lg-4 pb-0">

                    <!-- President Box Layout-->
                    <div class="mb-2">
                        <div class="role-badge mb-3">PRESIDENT</div>

                        <div class="election-box">
                            <!-- Inner Candidate listing top identical section-->
                            <div class="candidates-list">
                                <div class="candidate-entry">
                                    <span class="candidate-name">Honey Malang</span>
                                    <span><span class="txt-strong-bold fs-6">53</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry">
                                    <span class="candidate-name">Myles Macrohon</span>
                                    <span><span class="txt-strong-bold fs-6">33</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry">
                                    <span class="candidate-name">Jahaira Ampaso</span>
                                    <span><span class="txt-strong-bold fs-6">25</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                            </div>
                            <!-- Inline specific matched perfectly single combined WINNER structure panel layout specific rules accurately nested  -->
                            <div class="winner-panel-bg d-flex justify-content-between align-items-center py-3">
                                <div class="candidate-name">
                                    <span class="winner-txt me-3">WINNER:</span><span
                                        class="txt-strong-bold text-black fs-6">Honey Malang</span>
                                </div>
                                <span><span class="txt-strong-bold text-black fs-6">53</span> <span
                                        class="text-count-regular text-black">votes</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- VP Box Layout exactly logically identical structured specifically -->
                    <div class="mb-2 mt-4">
                        <div class="role-badge mb-3">VICE PRESIDENT</div>

                        <div class="election-box mb-1">
                            <!-- Standard inner listing VP candidate records precisely specific mock matching -->
                            <div class="candidates-list">
                                <div class="candidate-entry">
                                    <span class="candidate-name">Honey Malang</span>
                                    <span><span class="txt-strong-bold fs-6">53</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry">
                                    <span class="candidate-name">Myles Macrohon</span>
                                    <span><span class="txt-strong-bold fs-6">33</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry">
                                    <span class="candidate-name">Jahaira Ampaso</span>
                                    <span><span class="txt-strong-bold fs-6">25</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                            </div>
                            <!-- Matched structurally accurate target WINNER nested bounded structurally cleanly wrapped ! -->
                            <div class="winner-panel-bg d-flex justify-content-between align-items-center py-3">
                                <div class="candidate-name">
                                    <span class="winner-txt me-3">WINNER:</span><span
                                        class="txt-strong-bold text-black fs-6">Honey Malang</span>
                                </div>
                                <span><span class="txt-strong-bold text-black fs-6">53</span> <span
                                        class="text-count-regular text-black">votes</span></span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ============================ -->
                <!-- RIGHT PANEL (Senators Target matched precisely mappings identically specifically specifically !!)-->
                <!-- ============================ -->
                <div class="col-lg-6 col-pad-lg ps-lg-4">
                    <!-- Senator Structurals mappings logic accurately targeting correctly dynamically mapped cleanly wrapped specifically accurately!-->
                    <div class="mb-0">
                        <div class="role-badge mb-3">SENATORS</div>

                        <div class="election-box mb-1">
                            <div class="candidates-list pb-4">
                                <div class="candidate-entry">
                                    <span class="candidate-name">Honey Malang</span>
                                    <span><span class="txt-strong-bold fs-6">53</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry">
                                    <span class="candidate-name">Myles Macrohon</span>
                                    <span><span class="txt-strong-bold fs-6">33</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry">
                                    <span class="candidate-name">Jahaira Ampaso</span>
                                    <span><span class="txt-strong-bold fs-6">25</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry">
                                    <span class="candidate-name">Honey Malang</span>
                                    <span><span class="txt-strong-bold fs-6">53</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry mb-0">
                                    <span class="candidate-name">Myles Macrohon</span>
                                    <span><span class="txt-strong-bold fs-6">33</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                            </div>

                            <!-- Target arrays perfectly bounded lists specific matching Senator visually separated dynamically strictly structural exactly matching exactly matching!! -->
                            <div class="winner-panel-bg pb-4">
                                <div class="winner-txt mb-4 mt-1 fs-6">WINNER:</div>

                                <div class="candidate-entry ms-4 me-0 mb-3">
                                    <span class="candidate-name">Jahaira Ampaso</span>
                                    <span><span class="txt-strong-bold fs-6">25</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry ms-4 me-0 mb-3">
                                    <span class="candidate-name">Honey Malang</span>
                                    <span><span class="txt-strong-bold fs-6">53</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry ms-4 me-0 mb-3">
                                    <span class="candidate-name">Myles Macrohon</span>
                                    <span><span class="txt-strong-bold fs-6">33</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry ms-4 me-0 mb-3">
                                    <span class="candidate-name">Jahaira Ampaso</span>
                                    <span><span class="txt-strong-bold fs-6">25</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                                <div class="candidate-entry ms-4 me-0 mb-1">
                                    <span class="candidate-name">Myles Macrohon</span>
                                    <span><span class="txt-strong-bold fs-6">33</span> <span
                                            class="text-count-regular">votes</span></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================== -->
    <!--  1. CONFIRMATION MODAL     -->
    <!-- ========================== -->
    <div class="modal fade" id="confirmPublishModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="d-flex justify-content-center mb-3">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <circle cx="40" cy="40" r="38" fill="#FEE2E2" />
                        <circle cx="40" cy="40" r="30" stroke="#DC2626" stroke-width="2.5"
                            fill="#FEF2F2" />
                        <rect x="37" y="25" width="6" height="20" rx="3" fill="#DC2626" />
                        <circle cx="40" cy="53" r="3.5" fill="#DC2626" />
                    </svg>
                </div>
                <h3 class="modal-title-custom">Are you sure?</h3>
                <p class="modal-text-custom mb-3">You want to publish the official results?</p>
                <div class="modal-actions">
                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <!-- Clicking Submit Triggers the Success Modal -->
                    <button type="button" class="btn btn-modal-submit" id="confirmSubmitBtn">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================== -->
    <!--  2. SUCCESS MODAL          -->
    <!-- ========================== -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content">

                <!-- Close Button X -->
                <button type="button" class="btn-close btn-close-absolute" data-bs-dismiss="modal"
                    aria-label="Close"></button>

                <!-- Green Check Icon -->
                <div class="d-flex justify-content-center mb-3">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <!-- Outer faint ring -->
                        <circle cx="40" cy="40" r="38" fill="#dcfce7" />
                        <!-- Inner stroke -->
                        <circle cx="40" cy="40" r="30" stroke="#00D12E" stroke-width="3"
                            fill="white" />
                        <!-- Checkmark -->
                        <path d="M28 42L36 50L52 30" stroke="#00D12E" stroke-width="5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>

                <!-- Text Content -->
                <h3 class="modal-title-custom">Success!</h3>
                <p class="modal-text-custom">
                    Official results have been published.<br>
                    The election results are now visible on<br>
                    all student dashboards.
                </p>
                <!-- No buttons at bottom for success modal, just close X -->
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script to simulate the flow: Publish -> Confirm -> Success -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get Elements
            var triggerBtn = document.getElementById('triggerConfirmBtn');
            var submitBtn = document.getElementById('confirmSubmitBtn');

            // Initialize Modals
            var confirmModal = new bootstrap.Modal(document.getElementById('confirmPublishModal'));
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));

            // Open Confirmation on Publish Click
            triggerBtn.addEventListener('click', function() {
                confirmModal.show();
            });

            // Open Success on Submit Click (and hide confirmation)
            submitBtn.addEventListener('click', function() {
                confirmModal.hide();
                // Short timeout to allow previous modal animation to clear nicely (optional but smoother)
                setTimeout(function() {
                    successModal.show();
                }, 150);
            });
        });
    </script>

</body>

</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/saofinalresult.blade.php ENDPATH**/ ?>