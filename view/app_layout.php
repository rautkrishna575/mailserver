<?php
// /templates/app_layout.php
// This is the main application view for logged-in users.
// $currentUser is available here from index.php
require_once('common/header.php');
?>



<body class="h-full overflow-hidden">
    <div id="app-container" class="h-full flex flex-col">
        <!-- Top Navigation -->
        <nav class="bg-gray-800 flex-shrink-0">
            <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 text-white flex items-center">
                            <img class="h-8 w-auto mr-2" src="./uploads/tu_logo.png" alt="MailFlow">
                            <span class="font-bold text-lg">TU Mail System</span>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="relative ml-3">
                            <div>
                                <button type="button" class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button">
                                    <span class="sr-only">Open user menu</span>
                                    <img class="h-8 w-8 rounded-full" id="user-avatar" src="https://ui-avatars.com/api/?name=<?php echo urlencode($currentUser['name']); ?>&background=random&color=fff" alt="">
                                </button>
                            </div>
                            <div id="user-menu" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-20" role="menu" aria-orientation="vertical">
                                <div class="px-4 py-3 border-b">
                                    <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($currentUser['name']); ?></p>
                                    <p class="text-sm text-gray-500 truncate"><?php echo htmlspecialchars($currentUser['email']); ?></p>
                                </div>
                                <a href="#" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="flex-grow flex min-h-0">
            <!-- Sidebar -->
            <div class="w-64 border-r border-gray-200 bg-white flex-shrink-0 flex flex-col">
                <div class="p-4">
                    <button id="compose-btn" class="w-full flex items-center justify-center rounded-md bg-indigo-600 px-3 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        <i class="fas fa-pen mr-2"></i> Compose
                    </button>
                </div>
                <nav class="space-y-1 px-2 flex-grow" id="sidebar-nav">
                    <a href="#" data-folder="inbox" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-inbox mr-3 text-gray-500 w-5 text-center"></i>
                        Inbox
                        <span id="inbox-count" class="ml-auto inline-block py-0.5 px-2.5 text-xs font-medium bg-indigo-600 text-white rounded-full"></span>
                    </a>
                    <a href="#" data-folder="sent" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-paper-plane mr-3 text-gray-500 w-5 text-center"></i>
                        Sent
                    </a>
                    <a href="#" data-folder="drafts" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-file-alt mr-3 text-gray-500 w-5 text-center"></i>
                        Drafts
                        <span id="drafts-count" class="ml-auto inline-block py-0.5 px-2.5 text-xs font-medium bg-gray-300 text-gray-800 rounded-full"></span>
                    </a>
                    <a href="#" data-folder="trash" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-trash-alt mr-3 text-gray-500 w-5 text-center"></i>
                        Trash
                    </a>
                </nav>
            </div>

            <!-- Main content (List and View Panels) -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Email list container -->
                <div id="email-list-container" class="flex-1 overflow-y-auto">
                    <div class="sticky top-0 bg-white z-10 border-b border-gray-200 p-4">
                        <h2 id="folder-name" class="text-xl font-semibold text-gray-800">Inbox</h2>
                        <p id="email-count-info" class="text-sm text-gray-500 mt-1"></p>
                    </div>
                    <div id="email-list" class="divide-y divide-gray-200">
                        <!-- Emails will be loaded here by jQuery -->
                    </div>
                </div>

                <!-- Email view container -->
                <div id="email-view" class="hidden flex-1 overflow-y-auto bg-white">
                    <div class="sticky top-0 bg-white z-10 border-b border-gray-200 p-4 flex justify-between items-center">
                        <button id="back-to-list-btn" class="flex items-center text-gray-500 hover:text-gray-900 p-2 rounded-md" title="Back to list">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </button>
                        <button id="delete-email-btn" class="text-red-500 hover:text-red-700 p-2 rounded-md" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <h2 id="email-subject-view" class="text-2xl font-bold text-gray-900 mb-4"></h2>
                        <div class="flex items-center mb-4 border-b pb-4">
                            <img id="sender-avatar-view" class="h-12 w-12 rounded-full" src="" alt="">
                            <div class="ml-4">
                                <p id="sender-name-view" class="text-base font-semibold text-gray-900"></p>
                                <p id="sender-email-view" class="text-sm text-gray-500"></p>
                            </div>
                            <div id="email-date-view" class="ml-auto text-sm text-gray-500 self-start"></div>
                        </div>
                        <div id="email-meta-details" class="text-sm text-gray-600 space-y-1 mb-6 border-b pb-4"></div>
                        <div id="email-body-view" class="prose max-w-none text-gray-800 mt-6 leading-relaxed"></div>
                        <div id="email-attachments-view" class="mt-8 border-t border-gray-200 pt-6 hidden">
                            <h3 class="text-lg font-semibold text-gray-900">Attachments</h3>
                            <ul id="attachments-list-view" class="mt-3 space-y-3"></ul>
                        </div>
                    </div>

                    <div class="p-4 border-b flex items-center sticky top-0 bg-white z-10">
                        <div class="flex items-center space-x-2">
                            <button id="reply-btn" title="Reply" class="px-3 py-1 text-sm text-gray-600 border rounded-md hover:bg-gray-100"><i class="fas fa-reply mr-2"></i>Reply</button>
                            <button id="forward-btn" title="Forward" class="px-3 py-1 text-sm text-gray-600 border rounded-md hover:bg-gray-100"><i class="fas fa-share mr-2"></i>Forward</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compose Email Modal -->
        <div id="compose-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-0 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                <form id="compose-form">
                    <div class="flex justify-between items-center py-3 px-4 border-b bg-gray-50 rounded-t-md">
                        <h3 class="font-bold text-gray-800">New Message</h3>
                        <button type="button" id="close-compose-modal" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                            <i class="fas fa-times fa-lg"></i>
                        </button>
                    </div>
                    <div class="p-4 overflow-y-auto" style="max-height: 70vh;">
                        <div class="relative">
                            <input type="hidden" id="compose-draft-id" name="draft_id" value="">
                            <!-- "To" Field -->
                            <div class="flex items-center border-b border-gray-300">
                                <span class="text-sm text-gray-500 pr-2">To</span>
                                <div id="recipient-container-to" class="recipient-container flex-1 flex flex-wrap items-center">
                                    <!-- Pills will be inserted here by jQuery -->
                                    <input type="text" class="recipient-input" data-type="to" placeholder="">
                                </div>
                                <button type="button" id="cc-toggle-btn" class="text-sm text-gray-500 hover:text-gray-800 px-2">Cc</button>
                            </div>
                            <div id="autocomplete-to" class="hidden absolute left-0 right-0 z-10 bg-white border border-gray-300 rounded-b-md shadow-lg max-h-48 overflow-y-auto"></div>
                        </div>

                        <!-- "Cc" Field (Initially Hidden) -->
                        <div id="cc-field-wrapper" class="relative hidden">
                            <div class="flex items-center border-b border-gray-300">
                                <span class="text-sm text-gray-500 pr-2">Cc</span>
                                <div id="recipient-container-cc" class="recipient-container flex-1 flex flex-wrap items-center">
                                    <!-- Pills will be inserted here by jQuery -->
                                    <input type="text" class="recipient-input" data-type="cc" placeholder="">
                                </div>
                            </div>
                            <div id="autocomplete-cc" class="hidden absolute left-0 right-0 z-10 bg-white border border-gray-300 rounded-b-md shadow-lg max-h-48 overflow-y-auto"></div>
                        </div>

                        <!-- Subject Field -->
                        <div class="border-b border-gray-300">
                            <input type="text" id="compose-subject" name="subject" class="block w-full px-1 py-2 focus:outline-none sm:text-sm" placeholder="Subject">
                        </div>

                        <!-- Content, Attachments, etc. -->
                        <div class="mt-4">
                            <textarea id="compose-content" name="content" rows="12" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Write your email here..."></textarea>
                        </div>
                        <div class="mb-2 mt-4">
                            <div id="file-upload" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-paperclip mx-auto h-12 w-12 text-gray-400"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload-input" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                            <span>Upload a file</span>
                                            <input id="file-upload-input" type="file" name="attachments[]" class="sr-only" multiple>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                </div>
                            </div>
                            <div id="attachments-preview" class="mt-2 hidden">
                                <h4 class="text-sm font-medium text-gray-700">Attachments:</h4>
                                <ul id="attachments-list-preview" class="mt-1 space-y-1"></ul>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center gap-x-2 py-3 px-4 border-t bg-gray-50 rounded-b-md">
                        <div>
                            <button type="button" id="save-draft-btn" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-800 shadow-sm hover:bg-gray-50">Save Draft</button>
                        </div>
                        <div class="flex items-center gap-x-2">
                            <button type="button" id="discard-compose-btn" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium text-gray-700 hover:text-red-600">Discard</button>
                            <button type="button" id="send-email-btn" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700">Send</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php require_once('common/footer.php'); ?>
</body>

</html>