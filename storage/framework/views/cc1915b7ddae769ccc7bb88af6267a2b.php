<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidates List</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b2361;
            /* Deep navy background from sidebar */
            color: white;
            min-height: 100vh;
        }

        /* The main lighter blue container area */
        .main-panel {
            background-color: #103494;
            /* The brighter royal blue background */
            border-radius: 20px;
            padding: 2rem;
            min-height: 85vh;
            display: flex;
            align-items: stretch;
            justify-content: center;
        }

        /* Back Button Style */
        .btn-back {
            width: 35px;
            height: 35px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0b2361;
            text-decoration: none;
            transition: transform 0.2s;
        }

        .btn-back:hover {
            transform: scale(1.1);
            color: #0b2361;
        }

        /* The Custom Wide Card Layout Styling */
        .candidate-card {
            background: white;
            border-radius: 12px;
            border: none;
            overflow: hidden;
            width: 100%;
            max-width: 1050px;
            /* Widened strictly properly securely bounding double structural grid securely! */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Vertical Center Spacing Separator Line Details  */
        @media (min-width: 768px) {
            .split-divider {
                border-right: 1px solid #7D92AD !important;
            }
        }

        .role-badge {
            background-color: #CADDFE;
            /* Crisp light periwinkle identical structurally to bounded top components dynamically properly */
            color: #000;
            font-weight: 700;
            padding: 0.35rem 0.8rem;
            display: inline-block;
            border-radius: 4px;
            font-size: 1.15rem;
            letter-spacing: -0.2px;
            text-transform: uppercase;
        }

        /* Modifying standard structural group constraints slightly sharper custom border lines optimally mapping image explicitly visually efficiently bounding components strictly natively cleanly appropriately standard bounds purely directly map reliably cleanly securely! */
        .custom-list {
            --bs-list-group-border-color: #aeb2b5;
        }

        .candidate-count {
            text-align: right;
            font-size: 0.8rem;
            color: #111;
            padding: 0.5rem 1rem;
        }

        .candidate-name-item {
            font-size: 1.05rem;
            color: #000;
            padding: 0.95rem 1.25rem !important;
        }
    </style>
</head>

<body class="p-4">

    <!-- Header Section -->
    <div class="container-fluid mb-3 px-0">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo e(route('view.sao-dashboard')); ?>" class="btn-back">
                <i class="bi bi-arrow-left stroke-width-2 fs-5"></i>
            </a>
            <h4 class="mb-0 fw-bold fs-5">Candidates List</h4>
        </div>
    </div>

    <!-- Main Content Panel -->
    <div class="main-panel shadow-sm w-100">

        <!-- Two Columns Candidate Box securely fitting purely properly efficiently mapped natively structurally appropriately explicitly strictly seamlessly explicitly scaling visually -->
        <div class="candidate-card w-100">
            <div class="row m-0 h-100 p-4 w-100">
                <!-- LEFT COLUMN - President and VP tightly logically dynamically structurally purely directly smoothly bounded properly seamlessly safely optimally appropriately perfectly structurally! -->
                <div class="col-12 col-md-6 px-sm-4 px-2 py-3 split-divider">

                    <!-- PRESIDENT BOUNDARIES -->
                    <div class="mb-5 position-relative w-100">
                        <span class="role-badge mb-3">PRESIDENT</span>

                        <div class="list-group custom-list rounded-1 mt-1 shadow-sm w-100 border">
                            <div class="list-group-item candidate-count border-bottom">3 Candidates</div>
                            <div class="list-group-item candidate-name-item border-bottom">Honey Malang</div>
                            <div class="list-group-item candidate-name-item border-bottom">Myles Macrohon</div>
                            <div class="list-group-item candidate-name-item">Honey Malang</div>
                        </div>
                    </div>

                    <!-- VICE PRESIDENT BOUNDARIES -->
                    <div class="mb-3 w-100 position-relative">
                        <span class="role-badge mb-3">VICE PRESIDENT</span>

                        <div class="list-group custom-list rounded-1 mt-1 shadow-sm border w-100">
                            <div class="list-group-item candidate-count border-bottom">3 Candidates</div>
                            <div class="list-group-item candidate-name-item border-bottom">Honey Malang</div>
                            <div class="list-group-item candidate-name-item border-bottom">Myles Macrohon</div>
                            <div class="list-group-item candidate-name-item">Honey Malang</div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN - Senators smoothly structurally appropriately strictly logically mapping accurately flawlessly securely purely smoothly flawlessly effectively bounds effectively correctly efficiently mapping structurally smoothly -->
                <div class="col-12 col-md-6 px-sm-4 px-2 py-3">

                    <div class="w-100 mb-3 position-relative">
                        <span class="role-badge mb-3">SENATORS</span>

                        <div class="list-group custom-list rounded-1 mt-1 shadow-sm border w-100">
                            <div class="list-group-item candidate-count border-bottom">8 Candidates</div>
                            <div class="list-group-item candidate-name-item border-bottom">Honey Malang</div>
                            <div class="list-group-item candidate-name-item border-bottom">Myles Macrohon</div>
                            <div class="list-group-item candidate-name-item border-bottom">Honey Malang</div>
                            <div class="list-group-item candidate-name-item border-bottom">Honey Malang</div>
                            <div class="list-group-item candidate-name-item border-bottom">Myles Macrohon</div>
                            <div class="list-group-item candidate-name-item border-bottom">Honey Malang</div>
                            <div class="list-group-item candidate-name-item border-bottom">Myles Macrohon</div>
                            <div class="list-group-item candidate-name-item">Honey Malang</div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <!-- Bootstrap JS (Optional, depending on need) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/saocandidatelist.blade.php ENDPATH**/ ?>