<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'MCC Digital Voting System'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f4f6f0; }

        /* ── Sidebar ── */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #ffffff 0%, #fafbf9 100%);
            border-right: 1px solid #e8ede3;
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            z-index: 50;
            box-shadow: 6px 0 32px rgba(0,0,0,.06);
        }
        .sidebar-logo {
            padding: 28px 24px 24px;
            border-bottom: 1px solid #f0f4eb;
            display: flex; align-items: center; gap: 14px;
            background: linear-gradient(135deg, #ffffff, #f8faf7);
        }
        .sidebar-logo img { 
            width: 48px; height: 48px; border-radius: 14px; object-fit: cover;
            box-shadow: 0 4px 16px rgba(26,92,56,.15);
        }
        .sidebar-logo-text { line-height: 1.2; }
        .sidebar-logo-text .name { 
            font-size: 15px; font-weight: 800; color: #1a3a1a; 
            letter-spacing: -0.02em;
        }
        .sidebar-logo-text .role { 
            font-size: 11px; font-weight: 600; color: #6b9e6b; 
            letter-spacing: .04em; text-transform: uppercase;
        }

        .nav-section-label {
            font-size: 10px; font-weight: 800; color: #8fb08f;
            text-transform: uppercase; letter-spacing: .1em;
            padding: 20px 24px 8px;
            position: relative;
        }
        .nav-section-label::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 24px;
            right: 24px;
            height: 1px;
            background: linear-gradient(90deg, #e8ede3, transparent);
        }
        
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px; margin: 3px 12px;
            border-radius: 12px; font-size: 14px; font-weight: 600;
            color: #5a7a5a; text-decoration: none;
            transition: all .2s cubic-bezier(0.4, 0, 0.2, 1); 
            border: 1px solid transparent;
            position: relative;
            overflow: hidden;
        }
        .nav-link::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, #f0f7f0, #e8f5e8);
            opacity: 0;
            transition: opacity .2s ease;
        }
        .nav-link:hover::before { opacity: 1; }
        .nav-link:hover { 
            color: #1a5c38; 
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(26,92,56,.1);
        }
        .nav-link.active {
            background: linear-gradient(135deg, #1a5c38, #2d7a52);
            color: #fff; border-color: transparent;
            box-shadow: 0 6px 20px rgba(26,92,56,.3);
            transform: translateX(2px);
        }
        .nav-link.active::before { display: none; }
        .nav-link .icon { 
            width: 20px; height: 20px; flex-shrink: 0; opacity: .85;
            position: relative; z-index: 1;
        }
        .nav-link.active .icon { opacity: 1; }
        .nav-link span { position: relative; z-index: 1; }

        /* ── Main ── */
        .main-wrap { margin-left: 280px; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            background: linear-gradient(135deg, #ffffff, #fafbf9);
            border-bottom: 1px solid #e8ede3;
            padding: 0 36px; height: 72px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 40;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
            backdrop-filter: blur(10px);
        }
        .topbar-title { 
            font-size: 20px; font-weight: 800; color: #1a3a1a; 
            letter-spacing: -0.02em;
        }
        .topbar-sub { 
            font-size: 13px; color: #8fb08f; font-weight: 500; 
            margin-top: 2px; letter-spacing: 0.01em;
        }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-box { 
            text-align: right; 
            padding: 8px 16px;
            background: rgba(26,92,56,.05);
            border-radius: 12px;
            border: 1px solid rgba(26,92,56,.1);
        }
        .clock-box .time { 
            font-size: 16px; font-weight: 800; color: #1a3a1a; 
            letter-spacing: .02em; 
        }
        .clock-box .date { 
            font-size: 11px; color: #8fb08f; font-weight: 600; 
            text-transform: uppercase; letter-spacing: .05em;
        }
        .avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: linear-gradient(135deg, #2d7a52, #1a5c38);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 800; font-size: 16px;
            box-shadow: 0 4px 16px rgba(26,92,56,.25);
            border: 2px solid rgba(255,255,255,.8);
        }

        .page-content { flex: 1; padding: 32px 36px; }

        /* ── Card ── */
        .content-card {
            background: linear-gradient(135deg, #ffffff, #fafbf9);
            border-radius: 24px;
            border: 1px solid #e8ede3;
            box-shadow: 0 4px 24px rgba(0,0,0,.06);
            padding: 32px; 
            min-height: calc(100vh - 180px);
            position: relative;
            overflow: hidden;
        }
        .content-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(26,92,56,.1), transparent);
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { 
            background: linear-gradient(180deg, #c8dcc8, #b8d0b8); 
            border-radius: 10px; 
        }
        ::-webkit-scrollbar-thumb:hover { 
            background: linear-gradient(180deg, #b8d0b8, #a8c5a8); 
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .content-card { animation: fadeInUp 0.5s ease-out; }

        .nav-link.active::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 4px;
            background: rgba(255,255,255,0.8);
            border-radius: 50%;
            box-shadow: 0 0 8px rgba(255,255,255,0.6);
        }

        @media (max-width: 1024px) {
            .sidebar { width: 260px; }
            .main-wrap { margin-left: 260px; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
            .main-wrap { margin-left: 0; }
            .topbar { padding: 0 20px; }
            .page-content { padding: 20px; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="<?php echo e(asset('icons/logo.png')); ?>" alt="MCC">
        <div class="sidebar-logo-text">
            <div class="name">MCC Voting</div>
            <div class="role">Administrator</div>
        </div>
    </div>

    <div style="flex:1; overflow-y:auto; padding-bottom:16px;">
        <div class="nav-section-label">Overview</div>
        <a href="<?php echo e(route('view.dashboard')); ?>" class="nav-link <?php echo e(request()->is('dashboard*') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
            <span>Dashboard</span>
        </a>

        <div class="nav-section-label">Management</div>
        <a href="<?php echo e(route('view.manage-accounts')); ?>" class="nav-link <?php echo e(request()->is('manage-accounts*') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Manage Accounts</span>
        </a>
        <a href="<?php echo e(route('view.finger-print')); ?>" class="nav-link <?php echo e(request()->is('finger-print*') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>
            <span>Fingerprint Enrollment</span>
        </a>
        <a href="<?php echo e(route('view.election-control')); ?>" class="nav-link <?php echo e(request()->is('election-control*') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Election Control</span>
        </a>
        <a href="<?php echo e(route('view.student-eligibility')); ?>" class="nav-link <?php echo e(request()->is('student-eligibility*') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Student Eligibility</span>
        </a>

        <div class="nav-section-label">Reports</div>
        <a href="<?php echo e(route('view.voting-logs')); ?>" class="nav-link <?php echo e(request()->is('voting-logs*') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span>Voting Logs</span>
        </a>
        <a href="<?php echo e(route('view.security-logs')); ?>" class="nav-link <?php echo e(request()->is('security-logs*') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Security Logs</span>
        </a>
        <a href="<?php echo e(route('view.system-activity')); ?>" class="nav-link <?php echo e(request()->is('system-activity*') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>System Activity</span>
        </a>
        <a href="<?php echo e(route('view.reports-and-analytics')); ?>" class="nav-link <?php echo e(request()->is('reports-and-analytics*') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Reports & Analytics</span>
        </a>
        <a href="<?php echo e(route('view.election-history')); ?>" class="nav-link <?php echo e(request()->is('election-history*') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Election History</span>
        </a>
    </div>

    <div style="padding:12px 8px; border-top:1px solid #f0f4eb;">
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="nav-link" style="width:100%; background:none; border:none; cursor:pointer; color:#e05252;">
                <svg class="icon" style="color:#e05252;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>

<div class="main-wrap">
    <header class="topbar">
        <div>
            <div class="topbar-title"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></div>
            <div class="topbar-sub"><?php echo $__env->yieldContent('page-sub', 'MCC Digital Voting System'); ?></div>
        </div>
        <div class="topbar-right">
            <div class="clock-box">
                <div id="clock" class="time"></div>
                <div id="date" class="date"></div>
            </div>
            <div class="avatar">A</div>
        </div>
    </header>

    <div class="page-content">
        <div class="content-card">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</div>

<script>
    function updateTime() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        document.getElementById('date').textContent = now.toLocaleDateString('en-GB');
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>
</body>
</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/components/admin-layout.blade.php ENDPATH**/ ?>