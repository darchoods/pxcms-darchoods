<div class="page-header">
    <h1>User Dashboard</h1>
</div>

<?php
if (!Auth::guest()) {
    $userInfo = getUserInfo(Auth::user()->username);

    echo \Debug::dump($userInfo, 'User Info for '.Auth::user()->username);
    echo \Debug::dump(Auth::user()->toArray(), '');
}
?>
