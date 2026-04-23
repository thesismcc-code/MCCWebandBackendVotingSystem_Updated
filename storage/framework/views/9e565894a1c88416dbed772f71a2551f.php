<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Candidates List - MCC Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: radial-gradient(ellipse at top left, #0d3520 0%, #0a2e1a 50%, #071f12 100%); min-height: 100vh; }
        .page-wrapper { padding: 32px 36px; min-height: 100vh; display: flex; flex-direction: column; gap: 28px; }
        .btn-back { width:42px;height:42px;background:rgba(255,255,255,.15);backdrop-filter:blur(10px);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;box-shadow:0 4px 16px rgba(0,0,0,.25),inset 0 1px 0 rgba(255,255,255,.2);transition:all .25s;flex-shrink:0;border:1px solid rgba(255,255,255,.2); }
        .btn-back:hover { background:rgba(255,255,255,.25);color:white;transform:scale(1.08) translateX(-2px); }
        .page-title { font-size:28px;font-weight:800;color:white;letter-spacing:-0.03em;margin:0;text-shadow:0 2px 8px rgba(0,0,0,.2); }
        .main-panel { background:linear-gradient(160deg,#1e6b42 0%,#165233 60%,#123d28 100%);border-radius:28px;padding:32px;flex:1;box-shadow:0 20px 60px rgba(0,0,0,.35),inset 0 1px 0 rgba(255,255,255,.08);display:flex;flex-direction:column;gap:24px;position:relative;border:1px solid rgba(255,255,255,.07); }

        /* Stat cards */
        .stat-card { background:linear-gradient(135deg,#ffffff 0%,#f8fdf9 100%);border-radius:20px;padding:20px 24px;display:flex;align-items:center;gap:16px;box-shadow:0 8px 32px rgba(0,0,0,.15);transition:all .25s;border:1px solid rgba(26,92,56,.08);position:relative;overflow:hidden; }
        .stat-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#1a5c38,#2d7a52,#4ade80);border-radius:20px 20px 0 0; }
        .stat-card:hover { transform:translateY(-3px);box-shadow:0 16px 48px rgba(0,0,0,.2); }
        .stat-icon { width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#1a5c38,#2d7a52);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 16px rgba(26,92,56,.35); }
        .stat-num { font-size:32px;font-weight:800;color:#0d1f14;line-height:1;letter-spacing:-0.02em; }
        .stat-lbl { font-size:11px;font-weight:700;color:#6b7280;margin-top:3px;text-transform:uppercase;letter-spacing:.08em; }

        /* Table card */
        .table-card { background:white;border-radius:20px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.15);border:1px solid rgba(255,255,255,.5); }

        /* Toolbar */
        .toolbar { padding:16px 24px;border-bottom:1px solid #f0f4f1;background:#fafcfb;display:flex;align-items:center;gap:12px;flex-wrap:wrap; }
        .search-wrap { position:relative;flex:1;min-width:200px; }
        .search-input { width:100%;border:1.5px solid #e5e7eb;border-radius:12px;padding:9px 14px 9px 38px;font-size:13px;font-weight:500;outline:none;transition:all .2s;background:#fff; }
        .search-input:focus { border-color:#1a5c38;box-shadow:0 0 0 3px rgba(26,92,56,.1); }
        .search-icon { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ab09a;pointer-events:none; }
        .filter-select { border:1.5px solid #e5e7eb;border-radius:12px;padding:9px 14px;font-size:13px;font-weight:600;color:#374151;outline:none;background:#fff;cursor:pointer;transition:all .2s; }
        .filter-select:focus { border-color:#1a5c38;box-shadow:0 0 0 3px rgba(26,92,56,.1); }

        /* Table */
        .tbl { width:100%;border-collapse:collapse; }
        .tbl thead tr { background:linear-gradient(135deg,#1a5c38 0%,#2d7a52 100%); }
        .tbl thead th { color:rgba(255,255,255,.95);font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.1em;padding:16px 20px;border:none; }
        .tbl tbody td { padding:14px 20px;color:#374151;font-weight:500;font-size:14px;vertical-align:middle;border-bottom:1px solid #f0f4f1; }
        .tbl tbody tr { transition:background .15s; }
        .tbl tbody tr:hover { background:linear-gradient(90deg,#f0f9f4,#f8fdf9); }
        .tbl tbody tr:last-child td { border-bottom:none; }

        /* Candidate avatar */
        .cand-avatar { width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #e8f5ee; }
        .cand-avatar-placeholder { width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#1a5c38,#2d7a52);display:flex;align-items:center;justify-content:center;color:white;font-size:13px;font-weight:800;flex-shrink:0; }

        /* Position badge */
        .pos-badge { background:linear-gradient(135deg,#f0f9f4,#e8f5ee);color:#1a5c38;font-weight:700;font-size:11px;padding:4px 12px;border-radius:20px;border:1.5px solid rgba(26,92,56,.2);white-space:nowrap; }

        /* Action buttons */
        .btn-edit { width:34px;height:34px;background:linear-gradient(135deg,#1a5c38,#2d7a52);color:white;border:none;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;transition:all .2s;box-shadow:0 2px 8px rgba(26,92,56,.3); }
        .btn-edit:hover { transform:translateY(-2px) scale(1.05);box-shadow:0 5px 16px rgba(26,92,56,.45); }
        .btn-delete { width:34px;height:34px;background:linear-gradient(135deg,#fff1f1,#fee2e2);color:#dc2626;border:1px solid rgba(220,38,38,.15);border-radius:9px;display:inline-flex;align-items:center;justify-content:center;transition:all .2s; }
        .btn-delete:hover { background:linear-gradient(135deg,#fee2e2,#fecaca);transform:translateY(-2px) scale(1.05); }

        /* FAB */
        .fab-btn { position:fixed;bottom:32px;right:32px;width:58px;height:58px;border-radius:50%;background:linear-gradient(135deg,#1a5c38 0%,#2d7a52 100%);color:white;border:none;box-shadow:0 8px 28px rgba(26,92,56,.55),inset 0 1px 0 rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;transition:all .25s;z-index:1000;cursor:pointer; }
        .fab-btn:hover { transform:scale(1.12) translateY(-3px);box-shadow:0 16px 40px rgba(26,92,56,.65); }

        /* Modal */
        .modal-content { border-radius:24px;border:none;box-shadow:0 32px 80px rgba(0,0,0,.25);overflow:hidden; }
        .modal-header { border-bottom:none;padding:28px 28px 0;background:linear-gradient(135deg,#f8fdf9,#ffffff); }
        .modal-body { padding:20px 28px; }
        .modal-footer { border-top:1px solid #f0f4f1;padding:16px 28px 24px;background:#fafcfb; }
        .modal-title { font-size:20px;font-weight:800;color:#0d1f14;letter-spacing:-0.02em; }
        .form-label { font-weight:700;color:#374151;font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px; }
        .form-control,.form-select { border:1.5px solid #e5e7eb;border-radius:11px;padding:10px 14px;font-size:13.5px;font-weight:500;transition:all .2s;background:#fafafa; }
        .form-control:focus,.form-select:focus { border-color:#1a5c38;box-shadow:0 0 0 3px rgba(26,92,56,.1);background:white; }
        .form-control[readonly] { background:#f4f6f5;color:#6b7280; }
        .btn-cancel { border:2px solid #ef4444;color:#ef4444;background:white;border-radius:11px;padding:10px 28px;font-weight:700;font-size:13px;transition:all .2s; }
        .btn-cancel:hover { background:#fef2f2;color:#dc2626;border-color:#dc2626; }
        .btn-save { background:linear-gradient(135deg,#1a5c38,#2d7a52);color:white;border:none;border-radius:11px;padding:10px 28px;font-weight:700;font-size:13px;transition:all .2s;box-shadow:0 4px 14px rgba(26,92,56,.3); }
        .btn-save:hover { transform:translateY(-1px);box-shadow:0 6px 20px rgba(26,92,56,.45); }
        .upload-circle { width:88px;height:88px;background:linear-gradient(135deg,#f4f6f5,#eef2ef);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#9ca3af;overflow:hidden;flex-shrink:0;border:2px dashed #c8d8cc;transition:all .2s; }

        .alert { border-radius:12px;border:none;font-weight:600;padding:12px 18px; }
        .alert-success { background:linear-gradient(135deg,#dcfce7,#bbf7d0);color:#15803d; }
        .alert-danger  { background:linear-gradient(135deg,#fee2e2,#fecaca);color:#dc2626; }

        .empty-state { padding:64px 20px;text-align:center; }
        .empty-state svg { opacity:.3;margin-bottom:16px; }
        .empty-state p { font-weight:700;font-size:15px;color:#6b7280;margin:0; }
        .empty-state span { font-size:13px;color:#9ca3af;margin-top:6px;display:block; }

        /* No active election banner */
        .no-election-banner { background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.3);border-radius:16px;padding:16px 20px;display:flex;align-items:center;gap:12px; }
    </style>
</head>
<body>
<div class="page-wrapper">


<?php if(session('success') || session('error')): ?>
<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-body text-center py-5 px-4">
                <?php if(session('success')): ?>
                <div style="width:64px;height:64px;border-radius:50%;border:3px solid #22c55e;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="30" height="30" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h5 style="font-weight:800;color:#111827;margin-bottom:8px;">Success!</h5>
                <p style="color:#6b7280;font-size:14px;"><?php echo e(session('success')); ?></p>
                <?php endif; ?>
                <?php if(session('error')): ?>
                <div style="width:64px;height:64px;border-radius:50%;border:3px solid #ef4444;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="30" height="30" fill="none" stroke="#ef4444" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h5 style="font-weight:800;color:#111827;margin-bottom:8px;">Error!</h5>
                <p style="color:#6b7280;font-size:14px;"><?php echo e(session('error')); ?></p>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn-save px-5" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<div class="d-flex align-items-center gap-3">
    <a href="<?php echo e(route('view.election-control-posistion-setup')); ?>" class="btn-back">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <h1 class="page-title">Candidates List</h1>
        <?php if($activeElectionName): ?>
        <p style="font-size:12px;color:rgba(255,255,255,.55);font-weight:600;margin:3px 0 0;letter-spacing:.02em;">
            Active Election: <?php echo e($activeElectionName); ?>

        </p>
        <?php endif; ?>
    </div>
</div>


<div class="main-panel">

    
    <?php if(!$activeElectionId): ?>
    <div class="no-election-banner">
        <svg width="20" height="20" fill="none" stroke="#fbbf24" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <span style="font-size:13px;font-weight:700;color:#fbbf24;">No active election found.</span>
            <span style="font-size:12px;color:rgba(251,191,36,.7);margin-left:8px;">Candidates added will not be linked to any election.</span>
        </div>
    </div>
    <?php endif; ?>

    
    <?php
        $totalCands = count($candidates);
        $positions_filled = count(array_unique(array_column($candidates, 'position')));
    ?>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                <div><div class="stat-num"><?php echo e($totalCands); ?></div><div class="stat-lbl">Total Candidates</div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#0d9488,#0f766e);"><svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                <div><div class="stat-num"><?php echo e($positions_filled); ?></div><div class="stat-lbl">Positions Filled</div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#d97706,#f59e0b);"><svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div><div class="stat-num"><?php echo e(count($positions)); ?></div><div class="stat-lbl">Total Positions</div></div>
            </div>
        </div>
    </div>

    
    <div class="table-card">
        
        <div class="toolbar">
            <div class="search-wrap">
                <svg class="search-icon" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input type="text" class="search-input" id="candSearch" placeholder="Search candidates..." oninput="filterCandidates()">
            </div>
            <select class="filter-select" id="posFilter" onchange="filterCandidates()">
                <option value="">All Positions</option>
                <?php $__currentLoopData = array_unique(array_column($candidates, 'position')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($pos); ?>"><?php echo e($pos); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <span style="font-size:12px;font-weight:600;color:#9ab09a;" id="resultCount"><?php echo e($totalCands); ?> candidates</span>
        </div>

        <div class="table-responsive">
            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width:48px;padding-left:20px;">#</th>
                        <th>Candidate</th>
                        <th>Position</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="candTableBody">
                    <?php $__empty_1 = true; $__currentLoopData = $candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="cand-row"
                        data-name="<?php echo e(strtolower($candidate['name'])); ?>"
                        data-position="<?php echo e(strtolower($candidate['position'])); ?>">
                        <td style="padding-left:20px;color:#9ab09a;font-weight:700;font-size:13px;"><?php echo e($i + 1); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <?php if($candidate['image']): ?>
                                <img src="/<?php echo e($candidate['image']); ?>" class="cand-avatar" alt="<?php echo e($candidate['name']); ?>">
                                <?php else: ?>
                                <div class="cand-avatar-placeholder"><?php echo e(strtoupper(substr($candidate['name'], 0, 1))); ?></div>
                                <?php endif; ?>
                                <span style="font-weight:700;color:#111827;font-size:14px;"><?php echo e($candidate['name']); ?></span>
                            </div>
                        </td>
                        <td><span class="pos-badge"><?php echo e($candidate['position']); ?></span></td>
                        <td style="color:#6b7280;font-size:13px;"><?php echo e($candidate['course'] ?: '—'); ?></td>
                        <td style="color:#6b7280;font-size:13px;"><?php echo e($candidate['year'] ?: '—'); ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn-edit btn-edit-candidate"
                                    data-candidate-id="<?php echo e($candidate['id']); ?>"
                                    data-candidate-name="<?php echo e($candidate['name']); ?>"
                                    data-candidate-position="<?php echo e($candidate['position']); ?>"
                                    data-candidate-course="<?php echo e($candidate['course']); ?>"
                                    data-candidate-year="<?php echo e($candidate['year']); ?>"
                                    data-candidate-image="<?php echo e($candidate['image']); ?>"
                                    title="Edit">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button class="btn-delete btn-delete-candidate"
                                    data-candidate-id="<?php echo e($candidate['id']); ?>"
                                    data-candidate-name="<?php echo e($candidate['name']); ?>"
                                    data-candidate-position="<?php echo e($candidate['position']); ?>"
                                    title="Delete">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6">
                        <div class="empty-state">
                            <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <p>No candidates found</p>
                            <span><?php if($activeElectionId): ?> Click the + button to add candidates for this election <?php else: ?> Set up an active election first <?php endif; ?></span>
                        </div>
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div id="noSearchResults" style="display:none;" class="empty-state">
            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <p>No candidates match your search</p>
        </div>
    </div>
</div>


<button type="button" class="fab-btn" data-bs-toggle="modal" data-bs-target="#addCandidateModal">
    <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
</button>


<div class="modal fade" id="addCandidateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:660px;">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add Candidate</h5></div>
            <div class="modal-body">
                <form id="addCandidateForm" action="<?php echo e(route('candidate.add')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="upload-circle" id="imagePreview">
                            <svg id="placeholderIcon" width="32" height="32" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                            <img id="previewImage" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        </div>
                        <div>
                            <p style="font-weight:700;font-size:13px;color:#374151;margin-bottom:8px;">Candidate Photo</p>
                            <input type="file" name="image" id="candidateImage" accept="image/*" class="d-none">
                            <button type="button" class="btn btn-sm px-3 text-white me-2" style="background:#1a5c38;border-radius:8px;font-size:12px;" onclick="document.getElementById('candidateImage').click()">Upload</button>
                            <button type="button" id="removeImageBtn" class="btn btn-sm btn-danger px-3" style="display:none;border-radius:8px;font-size:12px;" onclick="removeImage()">Remove</button>
                            <p class="small text-muted mt-2 mb-0">Max 2MB · JPG, PNG, GIF</p>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="mb-3 position-relative">
                        <label class="form-label">Student Name</label>
                        <input type="text" class="form-control" id="studentNameInput" placeholder="Type to search student..." autocomplete="off" required>
                        <input type="hidden" name="user_id" id="addUserId">
                        <input type="hidden" name="full_name" id="addFullName">
                        <ul id="addSuggestions" class="list-group position-absolute w-100 shadow" style="z-index:9999;display:none;max-height:200px;overflow-y:auto;top:100%;left:0;border-radius:10px;"></ul>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <select class="form-select" name="position" required>
                            <option value="" disabled selected>Select Position</option>
                            <?php $__currentLoopData = $positions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pos['name']); ?>"><?php echo e($pos['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="row g-3 mb-1">
                        <div class="col-6">
                            <label class="form-label">Course</label>
                            <input type="text" class="form-control" name="course" id="addCourseDisplay" readonly placeholder="Auto-filled from student">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Year Level</label>
                            <input type="text" class="form-control" id="addYearDisplay" readonly placeholder="Auto-filled from student">
                            <input type="hidden" name="year" id="addYearValue">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addCandidateForm" class="btn-save">Add Candidate</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-body text-center py-5 px-4">
                <div style="width:64px;height:64px;background:linear-gradient(135deg,#fee2e2,#fecaca);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="30" height="30" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h5 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:8px;">Delete Candidate?</h5>
                <p style="font-size:13px;color:#6b7280;margin-bottom:4px;" id="deleteCandidateInfo"></p>
                <p style="font-size:12px;color:#ef4444;font-weight:600;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-3 pb-4">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" style="background:linear-gradient(135deg,#b91c1c,#dc2626);color:white;border:none;border-radius:11px;padding:10px 28px;font-weight:700;font-size:13px;box-shadow:0 4px 14px rgba(185,28,28,.3);">Delete</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editCandidateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:660px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Candidate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editCandidateForm" action="<?php echo e(route('candidate.update')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="candidate_id" id="editCandidateId">
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="upload-circle" id="editImagePreview">
                            <svg id="editPlaceholderIcon" width="32" height="32" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                            <img id="editPreviewImage" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        </div>
                        <div>
                            <p style="font-weight:700;font-size:13px;color:#374151;margin-bottom:8px;">Candidate Photo</p>
                            <input type="file" name="image" id="editCandidateImage" accept="image/*" class="d-none">
                            <button type="button" class="btn btn-sm px-3 text-white me-2" style="background:#1a5c38;border-radius:8px;font-size:12px;" onclick="document.getElementById('editCandidateImage').click()">Upload</button>
                            <button type="button" id="editRemoveImageBtn" class="btn btn-sm btn-danger px-3" style="display:none;border-radius:8px;font-size:12px;" onclick="removeEditImage()">Remove</button>
                            <p class="small text-muted mt-2 mb-0">Max 2MB · JPG, PNG, GIF</p>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="mb-3 position-relative">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="editNameInput" placeholder="Type to search..." autocomplete="off">
                        <input type="hidden" name="user_id" id="editUserId">
                        <input type="hidden" name="full_name" id="editFullName">
                        <ul id="editSuggestions" class="list-group position-absolute w-100 shadow" style="z-index:9999;display:none;max-height:200px;overflow-y:auto;top:100%;left:0;border-radius:10px;"></ul>
                        <p class="small text-muted mt-1 mb-0" id="editCurrentName"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <select class="form-select" name="position" id="editPosition" required>
                            <option value="" disabled>Select Position</option>
                            <?php $__currentLoopData = $positions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pos['name']); ?>"><?php echo e($pos['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="row g-3 mb-1">
                        <div class="col-6">
                            <label class="form-label">Course</label>
                            <input type="text" class="form-control" name="course" id="editCourse" readonly placeholder="Auto-filled">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Year Level</label>
                            <input type="text" class="form-control" id="editYearDisplay" readonly placeholder="Auto-filled">
                            <input type="hidden" name="year" id="editYear">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editCandidateForm" class="btn-save">Update Candidate</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    <?php if(session('success') || session('error')): ?>
        new bootstrap.Modal(document.getElementById('messageModal')).show();
    <?php endif; ?>

    const allUsers = <?php echo json_encode($users ?? [], 15, 512) ?>;

    function formatYear(y) {
        const map = {'1':'1st Year','2':'2nd Year','3':'3rd Year','4':'4th Year','5':'5th Year'};
        return map[(y??'').toString().trim()] || (y??'');
    }

    // ── Search & filter ───────────────────────────────────────
    function filterCandidates() {
        const q   = document.getElementById('candSearch').value.toLowerCase();
        const pos = document.getElementById('posFilter').value.toLowerCase();
        const rows = document.querySelectorAll('.cand-row');
        let visible = 0;
        rows.forEach(row => {
            const nameMatch = row.dataset.name.includes(q);
            const posMatch  = !pos || row.dataset.position === pos;
            const show = nameMatch && posMatch;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        document.getElementById('noSearchResults').style.display = visible === 0 && (q || pos) ? 'block' : 'none';
        document.getElementById('resultCount').textContent = visible + ' candidate' + (visible !== 1 ? 's' : '');
    }

    // ── Autocomplete ──────────────────────────────────────────
    function buildSuggestions(query, listEl, onSelect) {
        const q = query.toLowerCase();
        const matches = q.length < 1 ? [] : allUsers.filter(u => u.name.toLowerCase().includes(q)).slice(0,8);
        listEl.innerHTML = '';
        if (!matches.length) { listEl.style.display='none'; return; }
        matches.forEach(u => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action py-2 px-3';
            li.style.cursor = 'pointer';
            li.innerHTML = `<strong>${u.name}</strong><br><small class="text-muted">${u.course||'—'} · ${formatYear(u.year)||'—'}</small>`;
            li.addEventListener('mousedown', e => { e.preventDefault(); onSelect(u); listEl.style.display='none'; });
            listEl.appendChild(li);
        });
        listEl.style.display = 'block';
    }

    // ADD autocomplete
    const addInput = document.getElementById('studentNameInput');
    const addSugg  = document.getElementById('addSuggestions');
    function applyAdd(u) {
        addInput.value = u.name;
        document.getElementById('addUserId').value        = u.id;
        document.getElementById('addFullName').value      = u.name;
        document.getElementById('addCourseDisplay').value = u.course||'';
        document.getElementById('addYearDisplay').value   = formatYear(u.year);
        document.getElementById('addYearValue').value     = u.year||'';
    }
    addInput.addEventListener('input', function(){ buildSuggestions(this.value, addSugg, applyAdd); });
    addInput.addEventListener('focus', function(){ if(this.value) buildSuggestions(this.value, addSugg, applyAdd); });
    addInput.addEventListener('blur',  ()=> setTimeout(()=> addSugg.style.display='none', 150));

    // EDIT autocomplete
    const editInput = document.getElementById('editNameInput');
    const editSugg  = document.getElementById('editSuggestions');
    function applyEdit(u) {
        editInput.value = u.name;
        document.getElementById('editUserId').value      = u.id;
        document.getElementById('editFullName').value    = u.name;
        document.getElementById('editCourse').value      = u.course||'';
        document.getElementById('editYearDisplay').value = formatYear(u.year);
        document.getElementById('editYear').value        = u.year||'';
        document.getElementById('editCurrentName').textContent = '✓ '+u.name;
    }
    editInput.addEventListener('input', function(){ buildSuggestions(this.value, editSugg, applyEdit); });
    editInput.addEventListener('focus', function(){ if(this.value) buildSuggestions(this.value, editSugg, applyEdit); });
    editInput.addEventListener('blur',  ()=> setTimeout(()=> editSugg.style.display='none', 150));

    // ── Image upload ADD ──────────────────────────────────────
    document.getElementById('candidateImage').addEventListener('change', function(e){
        const file = e.target.files[0]; if(!file) return;
        if(file.size > 2*1024*1024){ alert('Max 2MB'); e.target.value=''; return; }
        const reader = new FileReader();
        reader.onload = ev => {
            document.getElementById('placeholderIcon').style.display='none';
            document.getElementById('previewImage').src=ev.target.result;
            document.getElementById('previewImage').style.display='block';
            document.getElementById('removeImageBtn').style.display='inline-block';
        };
        reader.readAsDataURL(file);
    });
    function removeImage(){
        document.getElementById('candidateImage').value='';
        document.getElementById('previewImage').style.display='none';
        document.getElementById('previewImage').src='';
        document.getElementById('placeholderIcon').style.display='block';
        document.getElementById('removeImageBtn').style.display='none';
    }

    // ── Image upload EDIT ─────────────────────────────────────
    document.getElementById('editCandidateImage').addEventListener('change', function(e){
        const file = e.target.files[0]; if(!file) return;
        if(file.size > 2*1024*1024){ alert('Max 2MB'); e.target.value=''; return; }
        const reader = new FileReader();
        reader.onload = ev => {
            document.getElementById('editPlaceholderIcon').style.display='none';
            document.getElementById('editPreviewImage').src=ev.target.result;
            document.getElementById('editPreviewImage').style.display='block';
            document.getElementById('editRemoveImageBtn').style.display='inline-block';
        };
        reader.readAsDataURL(file);
    });
    function removeEditImage(){
        document.getElementById('editCandidateImage').value='';
        document.getElementById('editPreviewImage').style.display='none';
        document.getElementById('editPreviewImage').src='';
        document.getElementById('editPlaceholderIcon').style.display='block';
        document.getElementById('editRemoveImageBtn').style.display='none';
    }

    // Reset add modal on close
    document.getElementById('addCandidateModal').addEventListener('hidden.bs.modal', function(){
        document.getElementById('addCandidateForm').reset();
        removeImage();
        ['studentNameInput','addUserId','addFullName','addCourseDisplay','addYearDisplay','addYearValue']
            .forEach(id => document.getElementById(id).value='');
        addSugg.style.display='none';
    });

    // ── Edit button ───────────────────────────────────────────
    document.querySelectorAll('.btn-edit-candidate').forEach(btn => {
        btn.addEventListener('click', function(){
            document.getElementById('editCandidateId').value    = this.dataset.candidateId;
            document.getElementById('editNameInput').value      = this.dataset.candidateName;
            document.getElementById('editFullName').value       = this.dataset.candidateName;
            document.getElementById('editPosition').value       = this.dataset.candidatePosition;
            document.getElementById('editCourse').value         = this.dataset.candidateCourse;
            document.getElementById('editYearDisplay').value    = formatYear(this.dataset.candidateYear)||this.dataset.candidateYear;
            document.getElementById('editYear').value           = this.dataset.candidateYear;
            document.getElementById('editCurrentName').textContent = 'Current: '+this.dataset.candidateName;
            if(this.dataset.candidateImage){
                document.getElementById('editPlaceholderIcon').style.display='none';
                document.getElementById('editPreviewImage').src='/'+this.dataset.candidateImage;
                document.getElementById('editPreviewImage').style.display='block';
            } else {
                document.getElementById('editPlaceholderIcon').style.display='block';
                document.getElementById('editPreviewImage').style.display='none';
            }
            new bootstrap.Modal(document.getElementById('editCandidateModal')).show();
        });
    });

    // ── Delete button ─────────────────────────────────────────
    let deleteTargetId = null;
    document.querySelectorAll('.btn-delete-candidate').forEach(btn => {
        btn.addEventListener('click', function(){
            deleteTargetId = this.dataset.candidateId;
            document.getElementById('deleteCandidateInfo').textContent = this.dataset.candidateName+' — '+this.dataset.candidatePosition;
            new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
        });
    });
    document.getElementById('confirmDeleteBtn').addEventListener('click', function(){
        if(!deleteTargetId) return;
        const form = document.createElement('form');
        form.method='POST'; form.action=`/election-control/candidate/delete/${deleteTargetId}`;
        const csrf=document.createElement('input'); csrf.type='hidden'; csrf.name='_token';
        csrf.value=document.querySelector('meta[name="csrf-token"]').content;
        const method=document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE';
        form.appendChild(csrf); form.appendChild(method);
        document.body.appendChild(form); form.submit();
    });
</script>
</body>
</html>

<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/candidatelist.blade.php ENDPATH**/ ?>