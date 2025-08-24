<?php
// /templates/auth_layout.php
// This is the view for users who are not logged in.
require_once('common/header.php');
?>
<style>
    body {
        background-color: #f3f4f6;
        /* bg-gray-100 */
    }
</style>

<body class="h-full">
    <div class="min-h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">

        <!-- Logo and App Name -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <h2 class="mt-4 text-center text-3xl font-extrabold text-gray-900">
                Welcome to TU Mail Server
            </h2>
        </div>

        <!-- Form Card -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl rounded-xl sm:px-10">

                <!-- Tab-like navigation for Login/Signup -->
                <div class="mb-6">
                    <nav class="flex space-x-4" aria-label="Tabs">
                        <a href="#" id="login-tab" class="auth-tab text-indigo-600 border-b-2 border-indigo-500 px-3 py-2 font-medium text-sm rounded-t-md">
                            Sign In
                        </a>
                        <a href="#" id="signup-tab" class="auth-tab text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm">
                            Create Account
                        </a>
                    </nav>
                </div>

                <!-- Login Form -->
                <form class="space-y-6" id="login-form" method="POST">
                    <input type="hidden" name="action" value="login">
                    <div>
                        <label for="login-email" class="sr-only">Email address</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-solid fa-envelope text-gray-400"></i>
                            </div>
                            <input id="login-email" name="email" type="email" autocomplete="email" required class="py-3 block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Email address">
                        </div>
                    </div>
                    <div>
                        <label for="login-password" class="sr-only">Password</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input id="login-password" name="password" type="password" autocomplete="current-password" required class="py-3 block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Password">
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            <span class="auth-submit-text">Sign in</span>
                            <i class="fas fa-spinner fa-spin hidden auth-spinner"></i>
                        </button>
                    </div>
                </form>

                <!-- Signup Form (Initially Hidden) -->
                <form class="space-y-6 hidden" id="signup-form" method="POST">
                    <input type="hidden" name="action" value="signup">
                    <div>
                        <label for="signup-name" class="sr-only">Full Name</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-solid fa-user text-gray-400"></i>
                            </div>
                            <input id="signup-name" name="name" type="text" autocomplete="name" required class="py-3 block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Full Name">
                        </div>
                    </div>
                    <div>
                        <label for="signup-email" class="sr-only">Email address</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-solid fa-envelope text-gray-400"></i>
                            </div>
                            <input id="signup-email" name="email" type="email" autocomplete="email" required class="py-3 block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Email address">
                        </div>
                    </div>
                    <div>
                        <label for="signup-password" class="sr-only">Password</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input id="signup-password" name="password" type="password" required class="py-3 block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Password">
                        </div>
                    </div>
                    <div>
                        <label for="confirm-password" class="sr-only">Confirm Password</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input id="confirm-password" name="confirm_password" type="password" required class="py-3 block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Confirm Password">
                        </div>
                        <p id="password-match-message" class="mt-2 text-sm"></p>
                    </div>
                    <div>
                        <button type="submit" class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            <span class="auth-submit-text">Create Account</span>
                            <i class="fas fa-spinner fa-spin hidden auth-spinner"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php require_once('common/footer.php');  ?>
</body>

</html>