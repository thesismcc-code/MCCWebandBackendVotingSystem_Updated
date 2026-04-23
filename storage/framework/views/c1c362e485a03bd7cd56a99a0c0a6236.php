<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Activity - Fingerprint Voting System</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #102864;
            color: white;
        }

        /* Custom Colors */
        .bg-main-panel {
            background-color: #0C3189;
            border: 1px solid rgba(59, 130, 246, 0.5);
        }

        /* Back Button */
        .btn-back {
            background-color: white;
            color: #113285;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: transform 0.2s;
            text-decoration: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-back:hover {
            transform: scale(1.1);
            color: #113285;
        }

        /* Toggle Buttons */
        .btn-log-toggle {
            padding: 8px 24px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            transition: all 0.2s;
        }
        .btn-log-active {
            background-color: #0066FF;
            color: white;
        }
        .btn-log-inactive {
            background-color: white;
            color: #111827;
        }
        .btn-log-inactive:hover {
            background-color: #f3f4f6;
        }

        /* Filter Select Boxes */
        .filter-box {
            background-color: #102864;
            border: 1px solid #3b82f6;
            color: white;
            border-radius: 8px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 200px;
            cursor: pointer;
        }
        .filter-box:hover {
            background-color: #153275;
        }

        /* Table Styling */
        .table-container {
            background-color: white;
            border-radius: 16px;
            overflow: hidden;
            color: #111827;
            min-height: 400px;
        }
        .table thead th {
            background-color: white;
            color: #111827;
            font-weight: 700;
            border-bottom: 2px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
        }
        .table tbody td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 500;
            color: #1f2937;
            height: 60px;
        }

        /* Pagination Buttons */
        .pagination-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .pg-active {
            background-color: white;
            color: #102864;
        }
        .pg-inactive {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .pg-inactive:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Utility to hide elements */
        .d-none {
            display: none !important;
        }
    </style>
</head>

<body class="p-3 p-md-4 min-vh-100 d-flex flex-column">

    <!-- HEADER SECTION -->
    <div class="container-xl mb-4 px-0">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo e(route('view.quick-access')); ?>" class="btn-back">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="h3 fw-bold m-0 lh-sm">System Activity</h1>
                <p class="small text-white-50 m-0 fw-medium">Real-time monitoring of system usage and security events</p>
            </div>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="container-xl bg-main-panel rounded-4 p-4 p-md-5 shadow-lg flex-fill d-flex flex-column gap-4 position-relative">

        <!-- Toggle Buttons -->
        <div class="d-flex gap-3">
            <button id="btnRealTime" class="btn-log-toggle btn-log-active" onclick="showRealTime()">Real Time Logs</button>
            <button id="btnError" class="btn-log-toggle btn-log-inactive" onclick="showError()">Error Logs</button>
        </div>

        <!-- Filter Row -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-2">
            <!-- Dynamic Title -->
            <h2 id="tableTitle" class="h2 fw-normal mb-0">Real Time Logs</h2>

            <div class="d-flex gap-3">
                <!-- User Filter -->
                <div class="dropdown">
                    <button class="filter-box dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span class="flex-grow-1 text-start">All Users</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">All Users</a></li>
                        <li><a class="dropdown-item" href="#">Admin</a></li>
                        <li><a class="dropdown-item" href="#">Student</a></li>
                    </ul>
                </div>

                <!-- Date Filter -->
                <div class="dropdown">
                    <button class="filter-box dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="flex-grow-1 text-start">Date</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Today</a></li>
                        <li><a class="dropdown-item" href="#">Yesterday</a></li>
                        <li><a class="dropdown-item" href="#">Last Week</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- REAL TIME LOGS TABLE -->
        <div id="realTimeTable" class="table-container shadow">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" style="width: 15%">Date</th>
                            <th scope="col" style="width: 15%">Time</th>
                            <th scope="col" style="width: 15%">User</th>
                            <th scope="col" style="width: 55%">Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">12-12-2025</td>
                            <td>10:01:23 AM</td>
                            <td>Student</td>
                            <td>Fingerprint scan successful</td>
                        </tr>
                        <tr>
                            <td class="text-center">12-12-2025</td>
                            <td>10:01:30 AM</td>
                            <td>Admin</td>
                            <td>Admin logged in</td>
                        </tr>
                        <tr>
                            <td class="text-center">12-12-2025</td>
                            <td>10:02:15 AM</td>
                            <td>Admin</td>
                            <td>Added new position</td>
                        </tr>
                        <tr>
                            <td class="text-center">12-12-2025</td>
                            <td>10:02:34 AM</td>
                            <td>Student</td>
                            <td>User logged in</td>
                        </tr>
                        <tr>
                            <td class="text-center">12-12-2025</td>
                            <td>10:03:00 AM</td>
                            <td>Comelec</td>
                            <td>Added candidate</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ERROR LOGS TABLE (Initially Hidden) -->
        <div id="errorTable" class="table-container shadow d-none">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" style="width: 20%">Date</th>
                            <th scope="col" style="width: 20%">Time</th>
                            <th scope="col" style="width: 20%">User</th>
                            <th scope="col" style="width: 40%">Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">12-12-2025</td>
                            <td>10:01:23 AM</td>
                            <td>Student</td>
                            <td class="text-danger">Failed fingerprint scan</td>
                        </tr>
                        <tr>
                            <td class="text-center">12-12-2025</td>
                            <td>10:01:30 AM</td>
                            <td>Student</td>
                            <td class="text-danger">Login attempt blocked</td>
                        </tr>
                        <!-- Empty rows for spacing consistency -->
                        <tr><td></td><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center gap-2 mt-auto pt-4">
            <a href="#" class="pagination-btn pg-active">1</a>
            <a href="#" class="pagination-btn pg-inactive">2</a>
            <a href="#" class="pagination-btn pg-active">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script to Handle Toggling -->
    <script>
        function showRealTime() {
            // Logic for Buttons
            document.getElementById('btnRealTime').classList.add('btn-log-active');
            document.getElementById('btnRealTime').classList.remove('btn-log-inactive');

            document.getElementById('btnError').classList.add('btn-log-inactive');
            document.getElementById('btnError').classList.remove('btn-log-active');

            // Logic for Tables
            document.getElementById('realTimeTable').classList.remove('d-none');
            document.getElementById('errorTable').classList.add('d-none');

            // Logic for Title
            document.getElementById('tableTitle').innerText = "Real Time Logs";
        }

        function showError() {
            // Logic for Buttons
            document.getElementById('btnError').classList.add('btn-log-active');
            document.getElementById('btnError').classList.remove('btn-log-inactive');

            document.getElementById('btnRealTime').classList.add('btn-log-inactive');
            document.getElementById('btnRealTime').classList.remove('btn-log-active');

            // Logic for Tables
            document.getElementById('errorTable').classList.remove('d-none');
            document.getElementById('realTimeTable').classList.add('d-none');

            // Logic for Title
            document.getElementById('tableTitle').innerText = "Error Logs";
        }
    </script>
</body>
</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/systemactivity.blade.php ENDPATH**/ ?>