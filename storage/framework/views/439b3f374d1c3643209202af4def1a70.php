<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>End of Election Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #1f2937; background: white; padding: 20px; }

        /* Print trigger */
        @media screen {
            .print-btn {
                position: fixed; top: 16px; right: 16px;
                background: #22c508; color: white; border: none;
                padding: 10px 20px; border-radius: 8px; font-size: 13px;
                font-weight: 700; cursor: pointer; z-index: 999;
                display: flex; align-items: center; gap: 6px;
            }
            .print-btn:hover { background: #1ea306; }
            .back-btn {
                position: fixed; top: 16px; left: 16px;
                background: #102864; color: white; border: none;
                padding: 10px 20px; border-radius: 8px; font-size: 13px;
                font-weight: 600; cursor: pointer; z-index: 999;
                text-decoration: none; display: flex; align-items: center; gap: 6px;
            }
        }
        @media print {
            .print-btn, .back-btn { display: none !important; }
            body { padding: 0; }
            @page { margin: 15mm; size: A4; }
        }

        /* Header */
        .report-header { text-align: center; border-bottom: 3px solid #102864; padding-bottom: 12px; margin-bottom: 16px; }
        .report-header h1 { font-size: 20px; color: #102864; font-weight: 800; }
        .report-header p { font-size: 11px; color: #6b7280; margin-top: 3px; }
        .meta-row { display: flex; justify-content: space-between; font-size: 10px; color: #6b7280; margin-bottom: 16px; }

        /* Sections */
        .section { margin-bottom: 20px; }
        .section-title { font-size: 12px; font-weight: 800; color: #102864; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 10px; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead th { background-color: #102864; color: white; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .winner-name { color: #113285; font-weight: 700; }
        .winner-badge { background: #dcfce7; color: #166534; font-size: 8px; font-weight: 700; padding: 1px 5px; border-radius: 10px; margin-left: 4px; }
        .text-right { text-align: right; }
        .no-data { text-align: center; padding: 20px; color: #9ca3af; font-style: italic; }

        /* Two column layout */
        .two-col { display: flex; gap: 16px; }
        .two-col .col { flex: 1; }

        /* Position group in final vote counts */
        .position-group { margin-bottom: 14px; }
        .position-label { font-size: 10px; font-weight: 800; color: #374151; text-transform: uppercase; background: #f3f4f6; padding: 4px 8px; margin-bottom: 0; }
    </style>
</head>
<body>

    <a href="<?php echo e(route('view.reports-and-analytics-end-of-election')); ?>" class="back-btn">&#8592; Back</a>
    <button class="print-btn" onclick="window.print()">&#8595; Save as PDF</button>

    <!-- Header -->
    <div class="report-header">
        <h1>End of Election Report</h1>
        <?php if($targetElection): ?>
            <p><?php echo e($targetElection['election_name'] ?? 'Election'); ?> &mdash; <?php echo e($targetElection['semester'] ?? ''); ?> <?php echo e($targetElection['academic_year'] ?? ''); ?></p>
        <?php else: ?>
            <p>No Active Election</p>
        <?php endif; ?>
    </div>

    <div class="meta-row">
        <span>Generated: <?php echo e(now()->format('F d, Y h:i A')); ?></span>
        <span>Total Votes Cast: <?php echo e(number_format($totalVotes)); ?> &nbsp;|&nbsp; Registered Voters: <?php echo e(number_format($totalVoters)); ?></span>
    </div>

    <div class="two-col">
        <!-- LEFT: Winners + Year Level -->
        <div class="col">

            <!-- Winners by Position -->
            <div class="section">
                <div class="section-title">Winners by Position</div>
                <?php if(empty($results)): ?>
                    <p class="no-data">No votes recorded yet</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:30%">Position</th>
                                <th style="width:42%">Winner</th>
                                <th class="text-right" style="width:14%">Votes</th>
                                <th class="text-right" style="width:14%">Turnout</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position => $candidates): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $winner = $candidates[0] ?? null; ?>
                                <?php if($winner): ?>
                                <tr>
                                    <td><?php echo e($position); ?></td>
                                    <td class="winner-name"><?php echo e($winner['name']); ?> <span class="winner-badge">Winner</span></td>
                                    <td class="text-right"><?php echo e(number_format($winner['votes'])); ?></td>
                                    <td class="text-right"><?php echo e($totalVoters > 0 ? round(($winner['votes']/$totalVoters)*100,1) : 0); ?>%</td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Turnout by Year Level -->
            <div class="section">
                <div class="section-title">Turnout by Year Level</div>
                <?php if(empty($turnout)): ?>
                    <p class="no-data">No turnout data available</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:30%">Year Level</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Voted</th>
                                <th class="text-right">Not Yet</th>
                                <th class="text-right">Turnout</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $turnout; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($row['year_level']); ?></td>
                                <td class="text-right"><?php echo e(number_format($row['total_students'])); ?></td>
                                <td class="text-right"><?php echo e(number_format($row['voted'])); ?></td>
                                <td class="text-right"><?php echo e(number_format($row['not_yet_voted'])); ?></td>
                                <td class="text-right"><?php echo e($row['turnout_percent']); ?>%</td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        </div>

        <!-- RIGHT: Final Vote Counts -->
        <div class="col">
            <div class="section">
                <div class="section-title">Final Vote Counts</div>
                <?php if(empty($results)): ?>
                    <p class="no-data">No votes recorded yet</p>
                <?php else: ?>
                    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position => $candidates): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="position-group">
                        <div class="position-label"><?php echo e($position); ?></div>
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:60%">Candidate</th>
                                    <th class="text-right" style="width:20%">Votes</th>
                                    <th class="text-right" style="width:20%">Turnout</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $cand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <?php if($i === 0): ?>
                                            <span class="winner-name"><?php echo e($cand['name']); ?></span>
                                            <span class="winner-badge">Winner</span>
                                        <?php else: ?>
                                            <?php echo e($cand['name']); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right"><?php echo e(number_format($cand['votes'])); ?></td>
                                    <td class="text-right"><?php echo e($totalVoters > 0 ? round(($cand['votes']/$totalVoters)*100,1) : 0); ?>%</td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-trigger print dialog on page load
        window.addEventListener('load', function() {
            setTimeout(() => window.print(), 500);
        });
    </script>
</body>
</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/endofelection_pdf.blade.php ENDPATH**/ ?>