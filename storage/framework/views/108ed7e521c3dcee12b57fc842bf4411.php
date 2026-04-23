<?php $__env->startSection('title', 'Quick Access - Fingerprint Voting System'); ?>

<?php $__env->startSection('content'); ?>
    <h2 class="text-2xl font-bold mb-8">Quick Access</h2>

    <!-- Quick Access Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Manage Accounts -->
        <?php echo $__env->make('components.quick-access-card', [
            'title' => 'Manage Accounts',
            'desc' => 'Manage Roles',
            'icon_bg' => 'bg-blue-600',
            'route' => route('view.manage-accounts'),
            'icon_path' => '/icons/person.png'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Fingerprint Enrollment -->
        <?php echo $__env->make('components.quick-access-card', [
            'title' => 'Fingerprint Enrollment',
            'desc' => 'Register student biometric data',
            'icon_bg' => 'bg-green-500',
            'route' => route('view.finger-print'),
            'icon_path' => '/icons/fingerprint.png'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Voting Logs -->
        <?php echo $__env->make('components.quick-access-card', [
            'title' => 'Voting Logs',
            'desc' => 'View voting records',
            'icon_bg' => 'bg-yellow-400',
            'route' => route('view.voting-logs'),
            'icon_path' => '/icons/how_to_vote.png',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Election Control -->
        <?php echo $__env->make('components.quick-access-card', [
            'title' => 'Election Control',
            'desc' => 'Configure election settings',
            'icon_bg' => 'bg-rose-500',
            'route' => route('view.election-control'),
            'icon_path'=>'/icons/settings.png'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- System Activity -->
        <?php echo $__env->make('components.quick-access-card', [
            'title' => 'System Activity',
            'desc' => 'Monitor real-time system logs',
            'icon_bg' => 'bg-green-500',
            'route' => route('view.system-activity'),
            'icon_path' => '/icons/earthquake.png'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Reports & Analytics -->
        <?php echo $__env->make('components.quick-access-card', [
            'title' => 'Reports & Analytics',
            'desc' => 'Generate system reports',
            'icon_bg' => 'bg-blue-600',
            'route' => route('view.reports-and-analytics'),
            'icon_path'=>'/icons/chart_data.png'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Student Eligibility -->
        <?php echo $__env->make('components.quick-access-card', [
            'title' => 'Student Eligibility',
            'desc' => 'Track email verification status',
            'icon_bg' => 'bg-rose-500',
            'route' => route('view.student-eligibility'),
            'icon_path'=>'/icons/beenhere.png'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('components.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/quick-access.blade.php ENDPATH**/ ?>