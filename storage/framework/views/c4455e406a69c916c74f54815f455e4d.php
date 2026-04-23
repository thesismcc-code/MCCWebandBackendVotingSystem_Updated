<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAO Voter Participation Report</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* General Page Styling */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b2361;
            /* Deep background blue */
            color: white;
            min-height: 100vh;
        }

        /* Back Button */
        .btn-back {
            width: 40px;
            height: 40px;
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

        /* Main Container Panel */
        .main-panel {
            background-color: #0b2b88;
            /* Lighter royal blue panel */
            border-radius: 20px;
            padding: 30px;
            min-height: 80vh;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        /* Search & Filter Inputs */
        .custom-input {
            background-color: transparent;
            border: 1px solid rgba(255, 255, 255, 0.7);
            color: white;
            border-radius: 5px;
            padding: 10px 15px;
        }

        .custom-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        .custom-input:focus {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-color: white;
            box-shadow: none;
        }

        .input-group-text-custom {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-right: none;
            color: white;
            padding-left: 15px;
        }

        .input-group .custom-input {
            border-left: none;
        }

        /* Select Chevron styling hack for white color */
        select.custom-input {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='white' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            background-repeat: no-repeat;
            appearance: none;
        }

        select.custom-input option {
            background-color: #0b2361;
            /* Option background to match theme */
        }

        /* Table Card Styling */
        .table-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 20px;
        }

        /* Custom Table */
        .table-voter thead th {
            font-weight: 700;
            text-transform: capitalize;
            border-bottom: 2px solid #289bf5;
            /* Blue line under header */
            padding: 15px;
            font-size: 0.95rem;
        }

        .table-voter tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #cce1fa;
            /* Light blue grid lines */
            font-size: 0.9rem;
            vertical-align: middle;
            color: #000;
        }

        /* Auto remove last border natively avoiding structural repetitive static style declarations */
        .table-voter tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* Highlight Row Style (as seen in image blue outline box) */
        .table-row-highlight {
            border: 2px solid #289bf5;
        }

        /* Status Badge */
        .badge-voted {
            background-color: #bbf7d0;
            /* Light Green */
            color: #166534;
            /* Dark Green Text */
            border-radius: 50px;
            padding: 5px 15px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        /* Pagination Styles */
        .pagination-container .page-box {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            font-weight: 600;
            margin: 0 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .pagination-container .page-active {
            background-color: white;
            color: #0b2b88;
        }

        .pagination-container .page-inactive {
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: white;
        }

        .pagination-container .page-inactive:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="p-3 p-md-4">

    <!-- Header Section with Back Button -->
    <div class="container-fluid mb-3 px-0">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo e(route('view.sao-dashboard')); ?>" class="btn-back" title="Go Back">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <h4 class="mb-0 fw-bold">Voter Participation</h4>
        </div>
    </div>

    <!-- Main Content Panel -->
    <div class="main-panel">

        <!-- Search and Filter Bar Form Container Added For Production Queries -->
        <form action="<?php echo e(request()->url()); ?>" method="GET" class="row g-3 mb-4">
            <!-- Search Bar -->
            <div class="col-lg-5 col-md-12">
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom" id="search-addon">
                        <i class="bi bi-search text-white"></i>
                    </span>
                    <input type="text" name="search" class="form-control custom-input"
                        placeholder="Search by Student ID or Name" aria-label="Search parameters"
                        value="<?php echo e(request('search')); ?>" oninput="this.form.submit()">
                </div>
            </div>

            <!-- Course Filter -->
            <div class="col-lg-3 col-md-6">
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="bi bi-mortarboard text-white"></i>
                    </span>
                    <select name="course_filter" class="form-select custom-input" onchange="this.form.submit()">
                        <option value="" selected>All Courses</option>
                        <option value="BSCS" <?php echo e(request('course_filter') == 'BSCS' ? 'selected' : ''); ?>>BSCS</option>
                        <option value="BSIT" <?php echo e(request('course_filter') == 'BSIT' ? 'selected' : ''); ?>>BSIT</option>
                        <option value="BSBA" <?php echo e(request('course_filter') == 'BSBA' ? 'selected' : ''); ?>>BSBA</option>
                    </select>
                </div>
            </div>

            <!-- Year Level Filter -->
            <div class="col-lg-4 col-md-6">
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="bi bi-calendar-event text-white"></i>
                    </span>
                    <select name="year_filter" class="form-select custom-input" onchange="this.form.submit()">
                        <option value="" selected>Year Level</option>
                        <option value="1" <?php echo e(request('year_filter') == '1' ? 'selected' : ''); ?>>1st Year</option>
                        <option value="2" <?php echo e(request('year_filter') == '2' ? 'selected' : ''); ?>>2nd Year</option>
                        <option value="3" <?php echo e(request('year_filter') == '3' ? 'selected' : ''); ?>>3rd Year</option>
                        <option value="4" <?php echo e(request('year_filter') == '4' ? 'selected' : ''); ?>>4th Year</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-voter mb-0 w-100">
                    <thead>
                        <tr>
                            <th scope="col">Student ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Course</th>
                            <th scope="col">Year Level</th>
                            <th scope="col">Date & Time</th>
                            <th scope="col" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Implemented Graceful Data Check Fallback -->
                        <?php if(isset($voters) && count($voters) > 0): ?>
                            <?php $__currentLoopData = $voters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr
                                    class="<?php echo e(isset($voter->is_highlighted) && $voter->is_highlighted ? 'table-row-highlight' : ''); ?>">
                                    <td><?php echo e($voter->student_id); ?></td>
                                    <td><?php echo e($voter->name); ?></td>
                                    <td><?php echo e($voter->course_name); ?></td>
                                    <td><?php echo e($voter->year_level); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($voter->voted_at)->format('d-m-Y g:iA')); ?></td>
                                    <td class="text-center"><span
                                            class="badge-voted"><?php echo e($voter->status ?? 'Voted'); ?></span></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <!-- Preserving provided mock logic in fallback view specifically matching default expectations-->
                            <tr>
                                <td>CS-2025-001</td>
                                <td>Jose Perolino</td>
                                <td>Computer Science</td>
                                <td>4th Year</td>
                                <td>12-12-2025 10:43AM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>IT-2025-035</td>
                                <td>Myles Macrohon</td>
                                <td>Information Technology</td>
                                <td>2nd Year</td>
                                <td>13-12-2025 1:30PM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>BA-2025-141</td>
                                <td>Honey Malang</td>
                                <td>Business Administration</td>
                                <td>3rd Year</td>
                                <td>14-12-2025 8:41AM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>CS-2025-225</td>
                                <td>Jahaira Ampaso</td>
                                <td>Business Administration</td>
                                <td>1st Year</td>
                                <td>14-12-2025 10:43AM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>IT-2025-005</td>
                                <td>James Cortes</td>
                                <td>Information Technology</td>
                                <td>1st Year</td>
                                <td>14-12-2025 1:30PM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>BA-2025-110</td>
                                <td>Carl Cobarde</td>
                                <td>Computer Science</td>
                                <td>3rd Year</td>
                                <td>12-12-2025 10:43AM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>CS-2025-365</td>
                                <td>Joshua Bacolod</td>
                                <td>Computer Science</td>
                                <td>2nd Year</td>
                                <td>13-12-2025 1:30PM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>IT-2025-002</td>
                                <td>Breant Cortes</td>
                                <td>Information Technology</td>
                                <td>4th Year</td>
                                <td>14-12-2025 8:41AM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>BA-2025-451</td>
                                <td>Joseph Cadenas</td>
                                <td>Business Administration</td>
                                <td>4th Year</td>
                                <td>14-12-2025 10:43AM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>BA-2025-120</td>
                                <td>Arley Flores</td>
                                <td>Information Technology</td>
                                <td>2nd Year</td>
                                <td>14-12-2025 1:30PM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>CS-2025-420</td>
                                <td>Ivhan Cuizon</td>
                                <td>Computer Science</td>
                                <td>1st Year</td>
                                <td>12-12-2025 10:43AM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>IT-2025-053</td>
                                <td>Zyra Pepito</td>
                                <td>Information Technology</td>
                                <td>1st Year</td>
                                <td>13-12-2025 1:30PM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                            <tr>
                                <td>BA-2025-114</td>
                                <td>Mark Berdon</td>
                                <td>Business Administration</td>
                                <td>3rd Year</td>
                                <td>14-12-2025 8:41AM</td>
                                <td class="text-center"><span class="badge-voted">Voted</span></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Section Abstracted -->
        <div class="d-flex justify-content-center mt-4 pagination-container">
            <?php if(isset($voters) && $voters instanceof \Illuminate\Pagination\LengthAwarePaginator && $voters->hasPages()): ?>
                
                <?php echo e($voters->withQueryString()->links('pagination::bootstrap-5')); ?>

            <?php else: ?>
                <!-- Static Placeholder Matches Direct Initial Structure -->
                <a href="#" class="page-box page-active">1</a>
                <a href="#" class="page-box page-inactive">2</a>
                <a href="#" class="page-box page-inactive" aria-label="Next">
                    <i class="bi bi-arrow-right"></i>
                </a>
            <?php endif; ?>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/saovoterparticipation.blade.php ENDPATH**/ ?>