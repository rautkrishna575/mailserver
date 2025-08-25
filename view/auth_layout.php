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
                    <div class="flex items-center justify-end">
                        <div class="text-sm">
                            <a href="#" id="forgot-password-link" class="font-medium text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                        </div>
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

    <!-- Forgot Password Modal -->
    <div id="forgot-password-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">

                <!-- View 1: Send OTP Form (Initially Visible) -->
                <div id="send-otp-view">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Forgot Your Password?</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Enter the email address you used to sign up and we'll send a 6-digit code to reset your password.
                        </p>
                    </div>
                    <form id="forgot-password-form" class="mt-4 space-y-4">
                        <input type="hidden" name="action" value="forgot_password">
                        <div>
                            <label for="forgot-email" class="sr-only">Email address</label>
                            <div class="relative">
                                <i class="fa-solid fa-envelope absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"></i>
                                <input id="forgot-email" name="email" type="email" autocomplete="email" required class="py-3 block w-full rounded-md border-gray-300 pl-10 shadow-sm sm:text-sm" placeholder="Email address">
                            </div>
                        </div>
                        <div class="items-center px-4 py-3">
                            <button type="submit" class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <span class="auth-submit-text">Send Code</span>
                                <i class="fas fa-spinner fa-spin hidden auth-spinner"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- View 2: Reset Password Form (Initially Hidden) -->
                <div id="reset-password-view" class="hidden">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Reset Your Password</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            A code has been sent to your email. Enter it below along with your new password.
                        </p>
                    </div>
                    <form id="reset-password-form" class="mt-4 space-y-4">
                        <input type="hidden" name="action" value="reset_password">
                        <!-- This hidden field is crucial to remember which user we are resetting -->
                        <input type="hidden" id="reset-email" name="email">

                        <div>
                            <label for="otp-input" class="sr-only">OTP Code</label>
                            <div class="relative">
                                <i class="fa-solid fa-key absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"></i>
                                <input id="otp-input" name="otp" type="text" required class="py-3 block w-full rounded-md border-gray-300 pl-10 shadow-sm sm:text-sm" placeholder="6-Digit Code">
                            </div>
                        </div>
                        <div>
                            <label for="reset-new-password" class="sr-only">New Password</label>
                            <div class="relative">
                                <i class="fa-solid fa-lock absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"></i>
                                <input id="reset-new-password" name="new_password" type="password" required class="py-3 block w-full rounded-md border-gray-300 pl-10 shadow-sm sm:text-sm" placeholder="New Password">
                            </div>
                        </div>
                        <div>
                            <label for="reset-confirm-password" class="sr-only">Confirm New Password</label>
                            <div class="relative">
                                <i class="fa-solid fa-lock absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"></i>
                                <input id="reset-confirm-password" name="confirm_password" type="password" required class="py-3 block w-full rounded-md border-gray-300 pl-10 shadow-sm sm:text-sm" placeholder="Confirm New Password">
                            </div>
                        </div>
                        <div class="items-center px-4 py-3">
                            <button type="submit" class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <span class="auth-submit-text">Reset Password</span>
                                <i class="fas fa-spinner fa-spin hidden auth-spinner"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-sm mt-4">
                    <a href="#" id="back-to-login-link" class="font-medium text-indigo-600 hover:text-indigo-500">
                        &larr; Back to Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('common/footer.php');  ?>
</body>

</html>