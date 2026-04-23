<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Logs Report</title>
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 15mm;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #102864;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #102864;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background-color: #102864;
            color: white;
        }

        th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e9ecef;
        }

        td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 12px;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background-color: #28a745;
            color: white;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background-color: #00e626;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 230, 38, 0.3);
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #00c920;
        }

        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-around;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item .number {
            font-size: 24px;
            font-weight: bold;
            color: #102864;
        }

        .summary-item .label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Print Button (hidden when printing) -->
    <button class="print-button no-print" onclick="window.print()">
        <svg style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
        </svg>
        Print / Save as PDF
    </button>

    <!-- Header -->
    <div class="header">
        <h1>Voting Logs Report</h1>
        <p>Fingerprint Voting System - MCC</p>
    </div>

    <!-- Meta Information -->
    <div class="meta-info">
        <div>
            <strong>Generated:</strong> <?php echo e(now()->format('F d, Y g:i A')); ?>

        </div>
        <div>
            <strong>Total Records:</strong> <?php echo e(count($logs)); ?>

        </div>
    </div>

    <!-- Summary -->
    <div class="summary">
        <div class="summary-item">
            <div class="number"><?php echo e(count($logs)); ?></div>
            <div class="label">Total Voters</div>
        </div>
        <div class="summary-item">
            <div class="number"><?php echo e(count(array_unique(array_column($logs, 'course')))); ?></div>
            <div class="label">Courses</div>
        </div>
        <div class="summary-item">
            <div class="number"><?php echo e(count(array_unique(array_column($logs, 'year_level')))); ?></div>
            <div class="label">Year Levels</div>
        </div>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Student ID</th>
                <th style="width: 25%;">Name</th>
                <th style="width: 20%;">Course</th>
                <th style="width: 15%;">Year Level</th>
                <th style="width: 20%;">Date & Time</th>
                <th style="width: 10%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($log['student_id']); ?></td>
                <td><?php echo e($log['name']); ?></td>
                <td><?php echo e($log['course']); ?></td>
                <td><?php echo e($log['year_level']); ?></td>
                <td>
                    <?php echo e($log['voted_at'] ? \Carbon\Carbon::parse($log['voted_at'])->format('m-d-Y g:iA') : '—'); ?>

                </td>
                <td style="text-align: center;">
                    <span class="status-badge">Voted</span>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                    No voting records found.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>This is an official document generated by the Fingerprint Voting System</p>
        <p>© <?php echo e(date('Y')); ?> MCC - All Rights Reserved</p>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); };
    </script>
</body>
</html><?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/votinglogs-pdf.blade.php ENDPATH**/ ?>