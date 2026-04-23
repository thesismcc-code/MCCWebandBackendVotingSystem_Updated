<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Position Setup - MCC Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: ''Plus Jakarta Sans'', sans-serif; }
        body { background: radial-gradient(ellipse at top left, #0d3520 0%, #0a2e1a 50%, #071f12 100%); min-height: 100vh; }
        .page-wrapper { padding: 32px 36px; min-height: 100vh; display: flex; flex-direction: column; gap: 28px; }

        .btn-back { width:42px;height:42px;background:rgba(255,255,255,.15);backdrop-filter:blur(10px);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;box-shadow:0 4px 16px rgba(0,0,0,.25),inset 0 1px 0 rgba(255,255,255,.2);transition:all .25s;flex-shrink:0;border:1px solid rgba(255,255,255,.2); }
        .btn-back:hover { background:rgba(255,255,255,.25);color:white;transform:scale(1.08) translateX(-2px); }
        .page-title { font-size:28px;font-weight:800;color:white;letter-spacing:-0.03em;margin:0;text-shadow:0 2px 8px rgba(0,0,0,.2); }

        .main-panel { background:linear-gradient(160deg,#1e6b42 0%,#165233 60%,#123d28 100%);border-radius:28px;padding:32px;flex:1;box-shadow:0 20px 60px rgba(0,0,0,.35),inset 0 1px 0 rgba(255,255,255,.08);display:flex;flex-direction:column;gap:24px;position:relative;border:1px solid rgba(255,255,255,.07); }

        .stat-card { background:linear-gradient(135deg,#ffffff 0%,#f8fdf9 100%);border-radius:20px;padding:22px 26px;display:flex;align-items:center;gap:18px;box-shadow:0 8px 32px rgba(0,0,0,.15),0 2px 8px rgba(0,0,0,.08);transition:all .25s cubic-bezier(.4,0,.2,1);border:1px solid rgba(26,92,56,.08);position:relative;overflow:hidden; }
        .stat-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#1a5c38,#2d7a52,#4ade80);border-radius:20px 20px 0 0; }
        .stat-card:hover { transform:translateY(-4px);box-shadow:0 16px 48px rgba(0,0,0,.2),0 4px 16px rgba(26,92,56,.15); }
        .stat-icon { width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#1a5c38 0%,#2d7a52 100%);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 6px 20px rgba(26,92,56,.4),inset 0 1px 0 rgba(255,255,255,.15); }
        .stat-number { font-size:36px;font-weight:800;color:#0d1f14;line-height:1;letter-spacing:-0.02em; }
        .stat-label { font-size:11px;font-weight:700;color:#6b7280;margin-top:4px;text-transform:uppercase;letter-spacing:.08em; }
        .stat-arrow { width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#f0f9f4,#e8f5ee);display:flex;align-items:center;justify-content:center;color:#1a5c38;transition:all .25s;text-decoration:none;margin-left:auto;box-shadow:0 2px 8px rgba(26,92,56,.15);border:1px solid rgba(26,92,56,.12); }
        .stat-arrow:hover { background:linear-gradient(135deg,#1a5c38,#2d7a52);color:white;transform:scale(1.12) rotate(5deg);box-shadow:0 6px 20px rgba(26,92,56,.4); }

        .table-card { background:white;border-radius:20px;overflow:hidden;flex:1;position:relative;box-shadow:0 8px 32px rgba(0,0,0,.15);border:1px solid rgba(255,255,255,.5); }

        /* Search bar */
        .search-wrap { padding:16px 24px;border-bottom:1px solid #f0f4f1;background:#fafcfb; }
        .search-input { width:100%;border:1.5px solid #e5e7eb;border-radius:12px;padding:9px 14px 9px 38px;font-size:13px;font-weight:500;outline:none;transition:all .2s;background:#fff; }
        .search-input:focus { border-color:#1a5c38;box-shadow:0 0 0 3px rgba(26,92,56,.1); }
        .search-icon { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ab09a;pointer-events:none; }

        .tbl { width:100%;border-collapse:collapse; }
        .tbl thead tr { background:linear-gradient(135deg,#1a5c38 0%,#2d7a52 100%); }
        .tbl thead th { color:rgba(255,255,255,.95);font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.1em;padding:16px 24px;border:none; }
        .tbl tbody td { padding:18px 24px;color:#374151;font-weight:500;font-size:14px;vertical-align:middle;border-bottom:1px solid #f0f4f1; }
        .tbl tbody tr { transition:background .15s; }
        .tbl tbody tr:hover { background:linear-gradient(90deg,#f0f9f4,#f8fdf9); }
        .tbl tbody tr:last-child td { border-bottom:none; }
        .pos-name { font-weight:700;color:#111827;font-size:15px; }

        .max-vote-badge { background:linear-gradient(135deg,#f0f9f4,#e8f5ee);color:#1a5c38;font-weight:800;font-size:14px;padding:5px 18px;border-radius:24px;border:1.5px solid rgba(26,92,56,.2);display:inline-block;letter-spacing:.02em; }
        .cand-badge { font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;display:inline-flex;align-items:center;gap:4px; }
        .cand-badge.has-cands { background:#dcfce7;color:#15803d;border:1px solid rgba(21,128,61,.2); }
        .cand-badge.no-cands  { background:#fef9c3;color:#92400e;border:1px solid rgba(251,191,36,.3); }

        .btn-edit { width:36px;height:36px;background:linear-gradient(135deg,#1a5c38,#2d7a52);color:white;border:none;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;transition:all .2s;box-shadow:0 3px 10px rgba(26,92,56,.35); }
        .btn-edit:hover { transform:translateY(-2px) scale(1.05);box-shadow:0 6px 18px rgba(26,92,56,.5); }
        .btn-delete { width:36px;height:36px;background:linear-gradient(135deg,#fff1f1,#fee2e2);color:#dc2626;border:1px solid rgba(220,38,38,.15);border-radius:10px;display:inline-flex;align-items:center;justify-content:center;transition:all .2s;box-shadow:0 3px 10px rgba(220,38,38,.15); }
        .btn-delete:hover { background:linear-gradient(135deg,#fee2e2,#fecaca);transform:translateY(-2px) scale(1.05);box-shadow:0 6px 18px rgba(220,38,38,.25); }

        .fab-btn { position:absolute;bottom:28px;right:28px;width:58px;height:58px;border-radius:50%;background:linear-gradient(135deg,#1a5c38 0%,#2d7a52 100%);color:white;border:none;box-shadow:0 8px 28px rgba(26,92,56,.55),inset 0 1px 0 rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;transition:all .25s cubic-bezier(.4,0,.2,1);z-index:10;cursor:pointer; }
        .fab-btn:hover { transform:scale(1.12) translateY(-3px);box-shadow:0 16px 40px rgba(26,92,56,.65); }

        .modal-content { border-radius:24px;border:none;box-shadow:0 32px 80px rgba(0,0,0,.25);overflow:hidden; }
        .modal-header { border-bottom:none;padding:32px 32px 0;background:linear-gradient(135deg,#f8fdf9,#ffffff); }
        .modal-body { padding:24px 32px; }
        .modal-footer { border-top:1px solid #f0f4f1;padding:20px 32px 28px;background:#fafcfb; }
        .modal-title { font-size:22px;font-weight:800;color:#0d1f14;letter-spacing:-0.02em; }
        .form-label { font-weight:700;color:#374151;font-size:13px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px; }
        .form-control,.form-select { border:1.5px solid #e5e7eb;border-radius:12px;padding:11px 16px;font-size:14px;font-weight:500;transition:all .2s;background:#fafafa; }
        .form-control:focus,.form-select:focus { border-color:#1a5c38;box-shadow:0 0 0 4px rgba(26,92,56,.1);background:white; }
        .btn-cancel { border:2px solid #ef4444;color:#ef4444;background:white;border-radius:12px;padding:11px 32px;font-weight:700;font-size:14px;transition:all .2s; }
        .btn-cancel:hover { background:#fef2f2;color:#dc2626;border-color:#dc2626;transform:translateY(-1px); }
        .btn-save { background:linear-gradient(135deg,#1a5c38 0%,#2d7a52 100%);color:white;border:none;border-radius:12px;padding:11px 32px;font-weight:700;font-size:14px;transition:all .2s;box-shadow:0 4px 16px rgba(26,92,56,.35); }
        .btn-save:hover { transform:translateY(-2px);box-shadow:0 8px 24px rgba(26,92,56,.5); }
        .btn-danger-confirm { background:linear-gradient(135deg,#b91c1c,#dc2626);color:white;border:none;border-radius:12px;padding:11px 32px;font-weight:700;font-size:14px;transition:all .2s;box-shadow:0 4px 16px rgba(185,28,28,.35); }
        .btn-danger-confirm:hover { transform:translateY(-2px);box-shadow:0 8px 24px rgba(185,28,28,.5); }

        .alert { border-radius:14px;border:none;font-weight:600;padding:14px 20px; }
        .alert-success { background:linear-gradient(135deg,#dcfce7,#bbf7d0);color:#15803d;box-shadow:0 4px 16px rgba(21,128,61,.15); }
        .alert-danger  { background:linear-gradient(135deg,#fee2e2,#fecaca);color:#dc2626;box-shadow:0 4px 16px rgba(220,38,38,.15); }

        .empty-state { padding:72px 20px;text-align:center; }
        .empty-state svg { opacity:.3;margin-bottom:20px; }
        .empty-state p { font-weight:700;font-size:16px;color:#6b7280;margin:0; }
        .empty-state span { font-size:13px;color:#9ca3af;margin-top:6px;display:block; }

        /* No-candidates warning row */
        .warn-row td { background:#fffbeb !important; }
    </style>
</head>
<body>
<div class="page-wrapper">

{{-- Alerts --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!</strong> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('view.election-control') }}" class="btn-back">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="page-title">Position Setup</h1>
    </div>
    {{-- Positions without candidates warning --}}
    @php
        $emptyPositions = collect($positions)->filter(fn($p) => ($candidatesPerPosition[$p['name']] ?? 0) === 0)->count();
    @endphp
    @if($emptyPositions > 0)
    <div style="background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.3);border-radius:12px;padding:8px 16px;display:flex;align-items:center;gap:8px;">
        <svg width="16" height="16" fill="none" stroke="#fbbf24" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span style="font-size:12px;font-weight:700;color:#fbbf24;">{{ $emptyPositions }} position{{ $emptyPositions > 1 ? 's' : '' }} without candidates</span>
    </div>
    @endif
</div>

{{-- Main Panel --}}
<div class="main-panel">

    {{-- Stat Cards --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <div class="stat-number">{{ $totalPositions }}</div>
                    <div class="stat-label">Total Positions</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <div class="stat-number">{{ $totalCandidates }}</div>
                    <div class="stat-label">Total Candidates</div>
                </div>
                <a href="{{ route('view.election-control-candidate-list') }}" class="stat-arrow">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-card">

        {{-- Search --}}
        <div class="search-wrap">
            <div class="position-relative">
                <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input type="text" class="search-input" id="posSearch" placeholder="Search positions..." oninput="filterPositions(this.value)">
            </div>
        </div>

        <div class="table-responsive">
            <table class="tbl" id="posTable">
                <thead>
                    <tr>
                        <th style="padding-left:32px;">Position Name</th>
                        <th class="text-center">Max Votes</th>
                        <th class="text-center">Candidates</th>
                        <th style="padding-left:32px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="posTableBody">
                    @forelse($positions as $position)
                    @php $candCount = $candidatesPerPosition[$position['name']] ?? 0; @endphp
                    <tr class="pos-row {{ $candCount === 0 ? 'warn-row' : '' }}" data-name="{{ strtolower($position['name']) }}">
                        <td style="padding-left:32px;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="pos-name">{{ $position['name'] }}</span>
                                @if($candCount === 0)
                                <span style="font-size:10px;font-weight:700;color:#d97706;background:#fef9c3;padding:2px 8px;border-radius:20px;border:1px solid rgba(217,119,6,.2);">No candidates</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="max-vote-badge">{{ $position['max_vote'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="cand-badge {{ $candCount > 0 ? 'has-cands' : 'no-cands' }}">
                                @if($candCount > 0)
                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @else
                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                @endif
                                {{ $candCount }} candidate{{ $candCount !== 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td style="padding-left:32px;">
                            <div class="d-flex gap-2">
                                <button class="btn-edit" title="Edit"
                                    data-position-id="{{ $position['id'] }}"
                                    data-position-name="{{ $position['name'] }}"
                                    data-max-vote="{{ $position['max_vote'] }}"
                                    onclick="openEdit(this)">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button class="btn-delete" title="Delete"
                                    data-position-id="{{ $position['id'] }}"
                                    data-position-name="{{ $position['name'] }}"
                                    data-cand-count="{{ $candCount }}"
                                    onclick="openDelete(this)">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p>No positions found</p>
                                <span>Click the + button below to add your first position</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- No search results --}}
        <div id="noResults" style="display:none;" class="empty-state">
            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <p>No positions match your search</p>
        </div>

        {{-- FAB --}}
        <button type="button" class="fab-btn" data-bs-toggle="modal" data-bs-target="#addModal">
            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        </button>
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Position</h5>
            </div>
            <div class="modal-body">
                <form id="addForm" action="{{ route('position.add') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">Position Name</label>
                        <input type="text" name="position_name" class="form-control" placeholder="e.g. President" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Max Votes</label>
                        <select name="max_vote" class="form-select" required>
                            <option value="" disabled selected>Select max votes</option>
                            @for($i = 1; $i <= 15; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addForm" class="btn-save">Save Position</button>
            </div>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Position</h5>
            </div>
            <div class="modal-body">
                <form id="editForm" action="{{ route('position.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="position_id" id="editPositionId">
                    <div class="mb-4">
                        <label class="form-label">Position Name</label>
                        <input type="text" name="position_name" class="form-control" id="editPositionName" placeholder="Position Name" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Max Votes</label>
                        <select name="max_vote" class="form-select" id="editMaxVotes" required>
                            <option value="" disabled>Select max votes</option>
                            @for($i = 1; $i <= 15; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editForm" class="btn-save">Update Position</button>
            </div>
        </div>
    </div>
</div>

{{-- DELETE CONFIRM MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content">
            <div class="modal-body text-center" style="padding:40px 32px 24px;">
                <div style="width:64px;height:64px;background:linear-gradient(135deg,#fee2e2,#fecaca);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <svg width="30" height="30" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h5 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:8px;">Delete Position?</h5>
                <p style="font-size:14px;color:#6b7280;font-weight:500;margin-bottom:4px;">
                    You are about to delete <strong id="deletePositionName" style="color:#1a3a1a;"></strong>.
                </p>
                <p id="deleteCandWarning" style="font-size:12px;color:#dc2626;font-weight:600;display:none;margin-top:8px;">
                    ⚠️ This position has candidates — deleting it will not remove them automatically.
                </p>
                <p style="font-size:12px;color:#9ca3af;margin-top:8px;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center gap-3" style="border-top:1px solid #f0f4f1;padding:16px 32px 28px;background:#fafcfb;">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-danger-confirm" id="confirmDeleteBtn">Delete Position</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ── Search filter ─────────────────────────────────────────
    function filterPositions(q) {
        const rows = document.querySelectorAll('.pos-row');
        const noRes = document.getElementById('noResults');
        let visible = 0;
        rows.forEach(row => {
            const name = row.dataset.name || '';
            const show = name.includes(q.toLowerCase());
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        noRes.style.display = visible === 0 && q ? 'block' : 'none';
    }

    // ── Edit modal ────────────────────────────────────────────
    function openEdit(btn) {
        document.getElementById('editPositionId').value   = btn.dataset.positionId;
        document.getElementById('editPositionName').value = btn.dataset.positionName;
        document.getElementById('editMaxVotes').value     = btn.dataset.maxVote;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    // ── Delete modal ──────────────────────────────────────────
    let deleteTargetId = null;
    function openDelete(btn) {
        deleteTargetId = btn.dataset.positionId;
        document.getElementById('deletePositionName').textContent = btn.dataset.positionName;
        const warn = document.getElementById('deleteCandWarning');
        warn.style.display = parseInt(btn.dataset.candCount) > 0 ? 'block' : 'none';
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!deleteTargetId) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/election-control/position/delete/${deleteTargetId}`;
        const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').content;
        const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE';
        form.appendChild(csrf); form.appendChild(method);
        document.body.appendChild(form); form.submit();
    });

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(a => {
            try { new bootstrap.Alert(a).close(); } catch(e) {}
        });
    }, 5000);
</script>
</body>
</html>
