<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>End of Election Reports</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: radial-gradient(ellipse at top left, #0d3520 0%, #0a2e1a 50%, #071f12 100%); min-height: 100vh; }

        .page-wrapper { padding: 32px 36px; min-height: 100vh; display: flex; flex-direction: column; gap: 24px; }

        /* Back button */
        .btn-back {
            width: 42px; height: 42px;
            background: rgba(255,255,255,.15); backdrop-filter: blur(10px);
            color: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(0,0,0,.25), inset 0 1px 0 rgba(255,255,255,.2);
            transition: all .25s; flex-shrink: 0;
            border: 1px solid rgba(255,255,255,.2);
        }
        .btn-back:hover { background: rgba(255,255,255,.25); color: white; transform: scale(1.08) translateX(-2px); }

        /* Export button */
        .btn-export {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px; border-radius: 12px;
            background: linear-gradient(135deg, #1a5c38, #2d7a52);
            color: white; font-weight: 700; font-size: 13px;
            text-decoration: none; border: none; cursor: pointer;
            box-shadow: 0 4px 16px rgba(26,92,56,.4);
            transition: all .2s;
        }
        .btn-export:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(26,92,56,.5); color: white; }

        /* Main panel */
        .main-panel {
            background: linear-gradient(160deg, #1e6b42 0%, #165233 60%, #123d28 100%);
            border-radius: 28px; padding: 32px;
            box-shadow: 0 20px 60px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.07);
            flex: 1;
        }

        /* Report cards */
        .report-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fdf9 100%);
            border-radius: 20px; padding: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,.12);
            border: 1px solid rgba(26,92,56,.08);
            position: relative; overflow: hidden;
        }
        .report-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, #1a5c38, #2d7a52, #4ade80);
            border-radius: 20px 20px 0 0;
        }

        /* Card header */
        .card-hdr { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .card-title { font-size: 12px; font-weight: 800; color: #1a3a1a; text-transform: uppercase; letter-spacing: .1em; margin: 0; }

        /* Badges */
        .badge-green { background: linear-gradient(135deg,#dcfce7,#bbf7d0); color: #15803d; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(21,128,61,.2); }
        .badge-gray  { background: #f4f6f0; color: #6b7280; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; }
        .badge-winner { background: linear-gradient(135deg,#dcfce7,#bbf7d0); color: #15803d; font-size: 10px; font-weight: 800; padding: 2px 10px; border-radius: 20px; margin-left: 6px; border: 1px solid rgba(21,128,61,.2); }

        /* Tables */
        .tbl { width: 100%; border-collapse: collapse; }
        .tbl thead th {
            font-size: 10px; font-weight: 800; color: #9ab09a;
            text-transform: uppercase; letter-spacing: .08em;
            padding: 0 0 12px 0; border-bottom: 1px solid #e8ede3;
        }
        .tbl tbody td {
            padding: 13px 0; font-size: 13.5px; font-weight: 500; color: #374151;
            border-bottom: 1px solid #f4f6f0; vertical-align: middle;
        }
        .tbl tbody tr:last-child td { border-bottom: none; }
        .tbl tbody tr:hover td { background: #f8fdf9; }
        .winner-name { font-weight: 800; color: #1a5c38; }

        /* Position group header */
        .pos-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 8px 0 6px; border-bottom: 2px solid #e8ede3; margin-top: 20px; margin-bottom: 4px;
        }
        .pos-header:first-of-type { margin-top: 0; }
        .pos-label { font-size: 11px; font-weight: 800; color: #1a5c38; text-transform: uppercase; letter-spacing: .1em; display: flex; align-items: center; gap: 6px; }
        .pos-label::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #2d7a52; display: inline-block; }

        /* Progress bar */
        .prog-wrap { height: 5px; background: #e8f5ee; border-radius: 99px; overflow: hidden; width: 80px; }
        .prog-bar  { height: 100%; border-radius: 99px; background: linear-gradient(90deg,#1a5c38,#4ade80); }

        /* Empty state */
        .empty-state { text-align: center; padding: 48px 20px; color: #9ca3af; }
        .empty-state svg { opacity: .3; margin-bottom: 12px; }
        .empty-state p { font-weight: 600; font-size: 14px; color: #6b7280; margin: 0; }

        /* Section label */
        .sec-lbl { display: flex; align-items: center; gap: 7px; font-size: 10.5px; font-weight: 800; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 16px; }

        /* Scrollable right card */
        .scroll-card { max-height: 680px; overflow-y: auto; }
        .scroll-card::-webkit-scrollbar { width: 4px; }
        .scroll-card::-webkit-scrollbar-track { background: transparent; }
        .scroll-card::-webkit-scrollbar-thumb { background: #c8dcc8; border-radius: 10px; }

        @media print {
            body { background: white !important; }
            .btn-back, .btn-export { display: none !important; }
            .main-panel { background: white !important; box-shadow: none !important; border: none !important; padding: 0 !important; }
            .report-card { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
        }
    </style>
</head>
<body>
<div class="page-wrapper">

    
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('view.reports-and-analytics')); ?>" class="btn-back">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 style="font-size:26px;font-weight:800;color:white;letter-spacing:-0.02em;margin:0;text-shadow:0 2px 8px rgba(0,0,0,.2);">End of Election Reports</h1>
                <?php if($targetElection): ?>
                <p style="font-size:13px;color:rgba(255,255,255,.6);font-weight:500;margin:3px 0 0;">
                    <?php echo e($targetElection['election_name'] ?? 'Election'); ?>

                    &mdash; <?php echo e($targetElection['semester'] ?? ''); ?>

                    <?php echo e($targetElection['academic_year'] ?? ''); ?>

                </p>
                <?php endif; ?>
            </div>
        </div>
        <a href="<?php echo e(route('end-of-election.export-pdf')); ?>" class="btn-export">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export to PDF
        </a>
    </div>

    
    <div class="main-panel">

        <?php if(!$targetElection): ?>
        <div class="empty-state" style="color:rgba(255,255,255,.5);">
            <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p style="color:rgba(255,255,255,.5);">No election data available</p>
        </div>
        <?php else: ?>

        
        <?php
            $totalWinners = count($results);
            $totalVotersCount = $totalVoters ?? 0;
            $overallTurnout = 0;
            if (!empty($turnout)) {
                $totalStudents = array_sum(array_column($turnout, 'total_students'));
                $totalVoted    = array_sum(array_column($turnout, 'voted'));
                $overallTurnout = $totalStudents > 0 ? round(($totalVoted / $totalStudents) * 100, 1) : 0;
            }
        ?>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="report-card flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 4px 12px rgba(26,92,56,.3);">
                    <svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <div style="font-size:30px;font-weight:800;color:#111827;line-height:1;letter-spacing:-0.02em;"><?php echo e($totalWinners); ?></div>
                    <div style="font-size:11px;font-weight:700;color:#9ab09a;text-transform:uppercase;letter-spacing:.07em;margin-top:3px;">Positions Filled</div>
                </div>
            </div>
            <div class="report-card flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#15803d,#16a34a);box-shadow:0 4px 12px rgba(21,128,61,.3);">
                    <svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div style="font-size:30px;font-weight:800;color:#111827;line-height:1;letter-spacing:-0.02em;"><?php echo e(number_format($totalVotes)); ?></div>
                    <div style="font-size:11px;font-weight:700;color:#9ab09a;text-transform:uppercase;letter-spacing:.07em;margin-top:3px;">Total Votes Cast</div>
                </div>
            </div>
            <div class="report-card flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#0d9488,#0f766e);box-shadow:0 4px 12px rgba(13,148,136,.3);">
                    <svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                </div>
                <div>
                    <div style="font-size:30px;font-weight:800;color:#111827;line-height:1;letter-spacing:-0.02em;"><?php echo e($overallTurnout); ?>%</div>
                    <div style="font-size:11px;font-weight:700;color:#9ab09a;text-transform:uppercase;letter-spacing:.07em;margin-top:3px;">Overall Turnout</div>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            
            <div class="flex flex-col gap-5">

                
                <div class="report-card">
                    <div class="card-hdr">
                        <h5 class="card-title">Winners by Position</h5>
                        <span class="badge-green"><?php echo e(count($results)); ?> Position(s)</span>
                    </div>
                    <?php if(empty($results)): ?>
                    <div class="empty-state">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p>No results yet</p>
                    </div>
                    <?php else: ?>
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th style="width:30%">Position</th>
                                <th style="width:40%">Winner</th>
                                <th class="text-right">Votes</th>
                                <th class="text-right">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position => $candidates): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $winner = $candidates[0] ?? null; ?>
                            <?php if($winner): ?>
                            <tr>
                                <td style="font-weight:600;color:#374151;"><?php echo e($position); ?></td>
                                <td>
                                    <span class="winner-name"><?php echo e($winner['name']); ?></span>
                                    <span class="badge-winner">Winner</span>
                                </td>
                                <td style="text-align:right;font-weight:700;color:#1a5c38;"><?php echo e(number_format($winner['votes'])); ?></td>
                                <td style="text-align:right;font-weight:600;color:#6b7280;">
                                    <?php echo e($totalVoters > 0 ? round(($winner['votes'] / $totalVoters) * 100, 1) : 0); ?>%
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                
                <div class="report-card">
                    <div class="card-hdr">
                        <h5 class="card-title">Turnout by Year Level</h5>
                    </div>
                    <?php if(empty($turnout)): ?>
                    <div class="empty-state">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p>No turnout data</p>
                    </div>
                    <?php else: ?>
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th style="width:35%">Year Level</th>
                                <th style="text-align:right;">Total</th>
                                <th style="text-align:right;">Voted</th>
                                <th style="text-align:right;">Turnout</th>
                                <th style="width:90px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $turnout; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $p = $row['turnout_percent'] ?? 0;
                                $barColor = $p >= 70 ? '#16a34a' : ($p >= 40 ? '#2d7a52' : '#f59e0b');
                            ?>
                            <tr>
                                <td style="font-weight:600;color:#374151;"><?php echo e($row['year_level']); ?></td>
                                <td style="text-align:right;"><?php echo e(number_format($row['total_students'])); ?></td>
                                <td style="text-align:right;font-weight:700;color:#1a5c38;"><?php echo e(number_format($row['voted'])); ?></td>
                                <td style="text-align:right;font-weight:700;" style="color:<?php echo e($barColor); ?>;"><?php echo e($p); ?>%</td>
                                <td>
                                    <div class="prog-wrap">
                                        <div class="prog-bar" style="width:<?php echo e(min($p,100)); ?>%;background:<?php echo e($barColor); ?>;"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

            </div>

            
            <div>
                <div class="report-card scroll-card">
                    <div class="card-hdr">
                        <h5 class="card-title">Final Vote Counts</h5>
                        <span class="badge-gray"><?php echo e(number_format($totalVotes)); ?> total votes</span>
                    </div>

                    <?php if(empty($results)): ?>
                    <div class="empty-state">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p>No votes recorded yet</p>
                    </div>
                    <?php else: ?>
                    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position => $candidates): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="pos-header">
                        <span class="pos-label"><?php echo e($position); ?></span>
                        <div style="display:flex;gap:40px;">
                            <span style="font-size:10px;font-weight:800;color:#9ab09a;text-transform:uppercase;letter-spacing:.08em;width:50px;text-align:right;">Votes</span>
                            <span style="font-size:10px;font-weight:800;color:#9ab09a;text-transform:uppercase;letter-spacing:.08em;width:60px;text-align:right;">%</span>
                        </div>
                    </div>
                    <table class="tbl" style="margin-bottom:8px;">
                        <tbody>
                            <?php $__currentLoopData = $candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $cand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php if($i === 0): ?>
                                        <span class="winner-name"><?php echo e($cand['name']); ?></span>
                                        <span class="badge-winner">Winner</span>
                                    <?php else: ?>
                                        <span style="color:#6b7280;"><?php echo e($cand['name']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;width:60px;font-weight:<?php echo e($i === 0 ? '800' : '500'); ?>;color:<?php echo e($i === 0 ? '#1a5c38' : '#6b7280'); ?>;">
                                    <?php echo e(number_format($cand['votes'])); ?>

                                </td>
                                <td style="text-align:right;width:70px;font-weight:600;color:#9ab09a;">
                                    <?php echo e($totalVoters > 0 ? round(($cand['votes'] / $totalVoters) * 100, 1) : 0); ?>%
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/endofelection.blade.php ENDPATH**/ ?>