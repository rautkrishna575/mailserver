// /assets/js/app.jquery.js

$(document).ready(function () {
  toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-center",
    "timeOut": "3000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  };
  // --- Application State ---
  let state = {
    currentFolder: "inbox",
    cachedEmails: {},
    currentEmail: null,
    composeAttachments: [],
  };

  // --- API Helper ---
  const api = {
    call: function (action, data, method = "GET") {
      let options = {
        url: "api/handler.php",
        type: method,
        data: data,
        dataType: "json",
      };
      if (data instanceof FormData) {
        options.processData = false;
        options.contentType = false;
      }
      return $.ajax(options);
    },
  };



  // --- Event Handlers Setup ---
  function init() {
    if ($("#login-form").length) {
      $("#login-tab, #signup-tab").on("click", toggleAuthForm);
      $("#login-form, #signup-form").on("submit", handleAuthSubmit);
      $("#signup-password, #confirm-password").on("keyup", checkPasswordMatch);
    }
    if ($("#app-container").length) {
      $("#sidebar-nav").on("click", ".sidebar-link", handleFolderChange);
      $("#email-list").on("click", ".email-item", handleEmailItemClick);
      $("#back-to-list-btn").on("click", showListView);
      $("#delete-email-btn").on("click", deleteCurrentEmail);
      $("#forward-btn").on("click", forwardEmail);
      $("#reply-btn").on("click", replyToEmail);
      $("#user-menu-button").on("click", toggleUserMenu);
      $(document).on("click", handleDocumentClickForMenu);
      $("#logout-btn").on("click", handleLogout);
      $("#compose-btn").on("click", showComposeModal);
      $("#close-compose-modal, #discard-compose-btn").on(
        "click",
        hideComposeModal
      );
      $("#send-email-btn").on("click", sendEmail);
      $("#save-draft-btn").on("click", saveAsDraft);
      $("#cc-toggle-btn").on("click", () =>
        $("#cc-field-wrapper").slideToggle()
      );
      initRecipientInput("to");
      initRecipientInput("cc");
      $("#file-upload-input").on("change", handleFileUpload);
      setupDragAndDrop();

      $("#attachments-list-preview").on(
        "click",
        ".remove-new-attachment-btn",
        function () {
          const indexToRemove = $(this).data("index");
          state.composeAttachments.splice(indexToRemove, 1);
          updateAttachmentsPreview();
        }
      );



      $("#attachments-list-preview").on(
        "click",
        ".remove-saved-attachment-btn",
        function () {
          deleteSavedAttachment($(this));
        }
      );

      $("#attachments-list-preview").on(
        "click",
        ".remove-attachment-btn",
        function () {
          const indexToRemove = $(this).data("index");
          state.composeAttachments.splice(indexToRemove, 1);
          updateAttachmentsPreview();
        }
      );

      loadEmails("inbox");
      updateCounts();
    }
  }

  // --- Authentication Functions ---
  function toggleAuthForm(e) {
    e.preventDefault();
    const isLoginTab = $(this).attr("id") === "login-tab";
    $(".auth-tab")
      .removeClass("text-indigo-600 border-b-2 border-indigo-500")
      .addClass("text-gray-500 hover:text-gray-700");
    $(this)
      .addClass("text-indigo-600 border-b-2 border-indigo-500")
      .removeClass("text-gray-500 hover:text-gray-700");
    $("#signup-form").toggle(!isLoginTab);
    $("#login-form").toggle(isLoginTab);
    $("#password-match-message").text("").hide();
  }
  function checkPasswordMatch() {
    const password = $("#signup-password").val();
    const confirmPassword = $("#confirm-password").val();
    const messageEl = $("#password-match-message");
    if (confirmPassword === "") {
      messageEl.text("").hide();
      return;
    }
    if (password === confirmPassword) {
      messageEl
        .text("✓ Passwords match!")
        .removeClass("text-red-600")
        .addClass("text-green-600")
        .show();
    } else {
      messageEl
        .text("✗ Passwords do not match.")
        .removeClass("text-green-600")
        .addClass("text-red-600")
        .show();
    }
  }
  function handleAuthSubmit(e) {
    e.preventDefault();
    const $form = $(this);
    const formData = new FormData(this);
    const action = formData.get("action");
    if (
      action === "signup" &&
      $("#signup-password").val() !== $("#confirm-password").val()
    ) {
      toastr.error("Passwords do not match.");
      return;
    }
    const $button = $form.find('button[type="submit"]');
    $button.prop("disabled", true).find(".auth-submit-text").addClass("hidden");
    $button.find(".auth-spinner").removeClass("hidden");
    api
      .call(action, formData, "POST")
      .done((response) => {
        if (response.success) window.location.reload();
        else
          toastr.error(response.message || "Authentication failed.");
      })
      .fail(() => alert("Could not connect to the server."))
      .always(() => {
        $button
          .prop("disabled", false)
          .find(".auth-submit-text")
          .removeClass("hidden");
        $button.find(".auth-spinner").addClass("hidden");
      });
  }

  // --- Main Application UI Functions ---
  // function handleFolderChange(e) {
  //   e.preventDefault();
  //   const folder = $(this).data("folder");
  //   if (folder && folder !== state.currentFolder) {
  //     $(".sidebar-link")
  //       .removeClass("bg-gray-100 text-indigo-600 font-semibold")
  //       .addClass("text-gray-800");
  //     $(this)
  //       .addClass("bg-gray-100 text-indigo-600 font-semibold")
  //       .removeClass("text-gray-800");
  //     loadEmails(folder);
  //   }
  // }
  function setActiveLink(folderLink) {
    // First, remove the active style from all links
    $(".sidebar-link")
      .removeClass("bg-gray-100 text-indigo-600 font-semibold")
      .addClass("text-gray-800");

    // Then, add the active style to the specific link you want
    $(folderLink)
      .addClass("bg-gray-100 text-indigo-600 font-semibold")
      .removeClass("text-gray-800");
  }

  function handleFolderChange(e) {
    e.preventDefault();
    const clickedLink = $(this); // The link that was clicked
    const folder = clickedLink.data("folder");

    if (folder && folder !== state.currentFolder) {
      state.currentFolder = folder; // Update the state
      setActiveLink(clickedLink);   // Update the UI
      loadEmails(folder);       // Load the new content
    }
  }
  $(document).ready(function () {
    const inboxLink = $('.sidebar-link[data-folder="inbox"]');
    state.currentFolder = 'inbox';
    setActiveLink(inboxLink);
  });

  function handleEmailItemClick() {
    const emailId = $(this).data("id");

    if (state.currentFolder === "drafts") {
      editDraft(emailId);
    } else {
      viewEmail(emailId);
    }
  }
  function showListView() {
    $("#email-view").addClass("hidden");
    $("#email-list-container").removeClass("hidden");
    state.currentEmail = null;
  }
  function handleLogout(e) {
    e.preventDefault();
    api
      .call("logout", { action: "logout" }, "POST")
      .done(() => window.location.reload());
  }
  function toggleUserMenu() {
    // $("#user-menu").toggleClass("hidden");
    $("#user-menu").toggleClass("");
  }
  function handleDocumentClickForMenu(event) {
    if (
      !$(event.target).closest("#user-menu-button").length &&
      !$(event.target).closest("#user-menu").length
    ) {
      $("#user-menu").addClass("hidden");
    }
  }

  // --- Core Data & Rendering Logic ---
  function loadEmails(folder) {
    state.currentFolder = folder;
    $("#folder-name").text(folder.charAt(0).toUpperCase() + folder.slice(1));
    showListView();
    $("#email-list").html(
      `<div class="p-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin text-2xl"></i></div>`
    );
    if (state.cachedEmails[folder]) {
      renderEmailList(state.cachedEmails[folder]);
      return;
    }
    api
      .call("emails", { action: "emails", folder: folder })
      .done((response) => {
        if (response.success) {
          state.cachedEmails[folder] = response.emails || [];
          renderEmailList(state.cachedEmails[folder]);
        } else {
          $("#email-list").html(
            `<p class="p-4 text-red-500">${response.message}</p>`
          );
        }
      })
      .fail(() =>
        $("#email-list").html(
          '<p class="p-4 text-red-500">Failed to load emails.</p>'
        )
      );
  }
  function renderEmailList(emails) {
    const $list = $("#email-list");
    $list.empty();
    $("#email-count-info").text(
      `${emails.length} conversation${emails.length !== 1 ? "s" : ""}`
    );
    if (emails.length === 0) {
      $list.html(
        `<div class="p-8 text-center text-gray-500"><i class="fas fa-envelope-open text-4xl mb-2"></i><p>Nothing to see here.</p></div>`
      );
      return;
    }
    emails.forEach((email) => {
      const isUnread = state.currentFolder === "inbox" && !email.is_read;
      let fromOrTo = "";
      if (state.currentFolder === "sent" || state.currentFolder === "drafts") {
        fromOrTo = `To: ${email.to_recipients_data || "No recipients"}`;
        if (email.cc_recipients_data)
          fromOrTo += ` | Cc: ${email.cc_recipients_data}`;
      } else {
        fromOrTo = `${email.from_name || "N/A"}`;
      }
      const emailHtml = `<div class="email-item flex items-center p-4 cursor-pointer hover:bg-gray-50 ${isUnread ? "unread" : ""
        }" data-id="${email.id
        }"><div class="w-1/3 pr-4"><p class="font-semibold text-gray-800 truncate">${fromOrTo}</p></div><div class="flex-1 min-w-0"><p class="email-subject text-sm font-medium text-gray-700 truncate">${email.subject || "(No Subject)"
        }</p><p class="text-sm text-gray-500 truncate">${(
          email.content || ""
        ).substring(
          0,
          80
        )}</p></div><div class="w-24 text-right text-xs text-gray-500">${formatDate(
          email.created_at
        )}</div></div>`;
      $list.append(emailHtml);
    });
  }
  function viewEmail(emailId) {
    const email = state.cachedEmails[state.currentFolder]?.find(
      (e) => e.id == emailId
    );

    if (!email) return;
    state.currentEmail = email;
    $("#email-list-container").addClass("hidden");
    $("#email-view").removeClass("hidden").scrollTop(0);
    $("#email-subject-view").text(email.subject || "(No Subject)");
    $("#email-body-view").html(
      email.content
        ? email.content.replace(/\n/g, "<br>")
        : "(This email has no content)"
    );
    $("#email-date-view").text(formatDate(email.created_at, true));
    const $metaDetails = $("#email-meta-details").empty().hide();
    if (state.currentFolder === "sent") {
      const myAvatar = $("#user-avatar").attr("src");
      $("#sender-name-view").text("Me");
      $("#sender-email-view").text("");
      $("#sender-avatar-view").attr("src", myAvatar);
      $metaDetails.show();
      $metaDetails.append(
        `<p><strong class="font-semibold text-gray-700">To:</strong> ${email.to_recipients_data || "N/A"
        }</p>`
      );
      if (email.cc_recipients_data)
        $metaDetails.append(
          `<p><strong class="font-semibold text-gray-700">Cc:</strong> ${email.cc_recipients_data}</p>`
        );
    } else {
      $("#sender-name-view").text(email.from_name || "Unknown Sender");
      $("#sender-email-view").text(
        `<${email.from_email || "no-reply@example.com"}>`
      );
      $("#sender-avatar-view").attr(
        "src",
        `https://ui-avatars.com/api/?name=${encodeURIComponent(
          email.from_name || "?"
        )}&background=random&color=fff`
      );
      $metaDetails.show();
      $metaDetails.append(
        `<p><strong class="font-semibold text-gray-700">from:</strong> ${email.from_name} <${email.from_email}></p>`
      );
      $metaDetails.append(
        `<p><strong class="font-semibold text-gray-700">to:</strong> Me</p>`
      );
    }
    const $attachmentsList = $("#attachments-list-view").empty();
    $("#email-attachments-view").toggle(
      email.attachments && email.attachments.length > 0
    );
    if (email.attachments && email.attachments.length > 0) {
      email.attachments.forEach((att) => {
        const fileSize =
          att.file_size > 1024 * 1024
            ? (att.file_size / (1024 * 1024)).toFixed(2) + " MB"
            : (att.file_size / 1024).toFixed(1) + " KB";
        $attachmentsList.append(
          `<li class="flex items-center p-3 bg-gray-50 rounded-md"><i class="fas fa-paperclip text-gray-500 mr-3"></i><span class="text-sm font-medium text-gray-800">${att.file_name}</span><span class="text-xs text-gray-500 ml-auto mr-4">(${fileSize})</span><a href="uploads/${att.file_path}" download="${att.file_name}" class="text-indigo-600 hover:underline text-sm font-medium">Download</a></li>`
        );
      });
    }
    if (state.currentFolder === "inbox" && !email.is_read) {
      api
        .call(
          "emails",
          { action: "emails", sub_action: "mark_read", email_id: email.id },
          "POST"
        )
        .done(() => {
          email.is_read = true;
          updateCounts();
          renderEmailList(state.cachedEmails["inbox"]);
        });
    }
  }

  function replyToEmail() {
    if (!state.currentEmail) {
      toastr.error("No email selected to reply to.");
      return;
    }

    const email = state.currentEmail;

    // 1. Open and clear the composer
    resetComposeForm();
    showComposeModal();
    $("#email-view").addClass("hidden");

    // 2. Pre-fill the 'To' field with the original sender
    // Note: This requires that your API sends `from_id` along with other email details.
    if (email.sender_id && email.from_name) {
      addRecipientPill(email.sender_id, email.from_name, $("#recipient-container-to"));
    } else {
      toastr.warning("Could not automatically add recipient.");
    }

    // 3. Pre-fill the subject with "Re:"
    const originalSubject = email.subject || "(No Subject)";
    const replySubject = originalSubject.startsWith("Re:") ? originalSubject : `Re: ${originalSubject}`;
    $("#compose-subject").val(replySubject);

    // 4. Create the quoted reply body
    const replyHeader = `
        <br>
        <br>
        <blockquote style="border-left: 2px solid #ccc; margin-left: 5px; padding-left: 10px; color: #666;">
        On ${formatDate(email.created_at, true)}, ${email.from_name} &lt;${email.from_email}&gt; wrote:<br><br>
    `;

    const originalBody = email.content ? email.content.replace(/\n/g, "<br>") : "";
    const quotedBody = `${replyHeader}${originalBody}</blockquote>`;

    // 5. Set the content in the editor with space at the top for the user to type
    if (window.tinymce && tinymce.get('compose-content')) {
      tinymce.get('compose-content').setContent(`<p><br></p>${quotedBody}`);
      tinymce.get('compose-content').focus();
      tinymce.get('compose-content').selection.setCursorLocation();
    } else {
      const textReplyHeader = `\n\n\nOn ${formatDate(email.created_at, true)}, ${email.from_name} <${email.from_email}> wrote:\n> `;
      const textOriginalBody = email.content ? email.content.replace(/\n/g, "\n> ") : "";
      $("#compose-content").val(textReplyHeader + textOriginalBody).focus();
      $("#compose-content").get(0).setSelectionRange(0, 0);
    }
  }

  function forwardEmail() {
    if (!state.currentEmail) {
      toastr.error("No email selected to forward.");
      return;
    }

    const email = state.currentEmail;

    // 1. Use the app's standard function to reset and show the composer
    resetComposeForm();
    showComposeModal();

    // 2. Hide the email view panel
    $("#email-view").addClass("hidden");

    // 3. Pre-fill the subject. The ID #compose-subject is correct.
    const originalSubject = email.subject || "(No Subject)";
    $("#compose-subject").val(`Fwd: ${originalSubject}`);

    // 4. Create the forwarded message header
    const forwardHeader = `
        <br>
        <br>
        <p>---------- Forwarded message ---------</p>
        <p><strong>From:</strong> ${email.from_name} &lt;${email.from_email}&gt;</p>
        <p><strong>Date:</strong> ${formatDate(email.created_at, true)}</p>
        <p><strong>Subject:</strong> ${originalSubject}</p>
        <p><strong>To:</strong> Me</p>
        <br>
    `;

    // 5. Get the original email body
    const originalBody = email.content ? email.content.replace(/\n/g, "<br>") : "";
    const newBody = forwardHeader + originalBody;

    // 6. Set the content using the correct ID: #compose-content
    if (window.tinymce && tinymce.get('compose-content')) {
      // Use this if you integrate a rich text editor like TinyMCE
      tinymce.get('compose-content').setContent(newBody);
    } else {
      // Fallback for a standard textarea
      $("#compose-content").val(newBody.replace(/<br\s*\/?>/gi, '\n').replace(/<p>|<\/p>/gi, '\n').replace(/<strong>|<\/strong>/gi, ''));
    }

    // Note: Forwarding attachments requires server-side logic to copy or reference
    // the original files. This implementation forwards the email body.
    // You could list the original attachment names in the body for context.
  }


  function editDraft(emailId) {
    const email = state.cachedEmails["drafts"]?.find((e) => e.id == emailId);
    if (!email) return;
    resetComposeForm();
    showComposeModal();
    $("#compose-draft-id").val(email.id);
    $("#compose-subject").val(email.subject);
    $("#compose-content").val(email.content);


    var toIds = JSON.parse(email.to_ids_json); // ["1", "3"]
    var ccIds = JSON.parse(email.cc_ids_json); // ["3"]

    var toRecipients = email.to_recipients_data
      .split(",")
      .map((name) => name.trim());
    var ccRecipients = email.cc_recipients_data
      ? email.cc_recipients_data.split(",").map((name) => name.trim())
      : [];

    var toRecipientsArray = toIds.map((id, index) => ({
      id: id,
      name: toRecipients[index],
    }));
    var ccRecipientsArray = ccIds.map((id, index) => ({
      id: id,
      name: ccRecipients[index],
    }));

    if (toRecipientsArray?.length > 0) {
      toRecipientsArray.forEach((r) =>
        addRecipientPill(r.id, r.name, $("#recipient-container-to"))
      );
    }
    if (ccRecipientsArray?.length > 0) {
      $("#cc-field-wrapper").show();
      ccRecipientsArray.forEach((r) =>
        addRecipientPill(r.id, r.name, $("#recipient-container-cc"))
      );
    }
    if (email.attachments?.length > 0) {
      updateAttachmentsPreview(email.attachments);
    }
  }

  function deleteSavedAttachment($button) {
    const attachmentId = $button.data("id");
    const emailId = $("#compose-draft-id").val();

    if (
      !confirm(
        "Are you sure you want to permanently delete this saved attachment?"
      )
    )
      return;

    api
      .call(
        "emails",
        {
          action: "emails",
          sub_action: "delete_attachment",
          attachment_id: attachmentId,
          email_id: emailId,
        },
        "POST"
      )
      .done((response) => {
        if (response.success) {
          // Remove the item from the UI
          $button.closest("li").fadeOut(300, function () {
            $(this).remove();
          });

          // Also remove it from the cache so it doesn't reappear
          const draft = state.cachedEmails["drafts"]?.find(
            (e) => e.id == emailId
          );
          if (draft && draft.attachments) {
            draft.attachments = draft.attachments.filter(
              (att) => att.id != attachmentId
            );
          }
        } else {
          alert(response.message || "Could not delete attachment.");
        }
      });
  }

  function deleteCurrentEmail() {
    if (
      !state.currentEmail ||
      !confirm("Are you sure you want to move this email to the trash?")
    )
      return;
    api
      .call(
        "emails",
        {
          action: "emails",
          sub_action: "delete",
          email_id: state.currentEmail.id,
        },
        "POST"
      )
      .done((response) => {
        if (response.success) {
          toastr.success('Removed Successfully');
          delete state.cachedEmails[state.currentFolder];
          delete state.cachedEmails["trash"];
          loadEmails(state.currentFolder);
          updateCounts();
        } else {
          toastr.error(response.message || "Could not delete email.");
        }
      });
  }
  function updateCounts() {
    api.call("counts", { action: "counts" }).done((res) => {
      if (res.success) {
        $("#inbox-count")
          .text(res.unread > 0 ? res.unread : "")
          .toggle(res.unread > 0);
        $("#drafts-count")
          .text(res.drafts > 0 ? res.drafts : "")
          .toggle(res.drafts > 0);
      }
    });
  }

  // --- Compose & Attachment Functions ---
  function showComposeModal() {
    $("#compose-modal").removeClass("hidden");
  }
  function hideComposeModal() {
    $("#compose-modal").addClass("hidden");
    resetComposeForm();
  }
  function resetComposeForm() {
    $("#compose-form")[0].reset();
    $(".recipient-pill").remove();
    $("#compose-draft-id").val("");
    state.composeAttachments = [];
    $("#attachments-list-preview").empty();
    $("#attachments-preview").hide();
    $("#cc-field-wrapper").hide();
  }
  let searchTimeout;
  function initRecipientInput(type) {
    const $container = $(`#recipient-container-${type}`);
    const $input = $container.find(".recipient-input");
    const $autocomplete = $(`#autocomplete-${type}`);
    $input.on("keyup", function () {
      clearTimeout(searchTimeout);
      const term = $(this).val();
      if (term.length < 1) {
        $autocomplete.hide();
        return;
      }
      searchTimeout = setTimeout(() => {
        api
          .call("search_users", { action: "search_users", term: term })
          .done((response) => {
            renderAutocomplete(response.users, $autocomplete);
          });
      }, 300);
    });
    $autocomplete.on("click", ".autocomplete-item", function () {
      addRecipientPill($(this).data("id"), $(this).data("name"), $container);
      $input.val("").focus();
      $autocomplete.hide();
    });
    $container.on("click", ".remove-pill", function () {
      $(this).closest(".recipient-pill").remove();
    });
    $container.on("click", () => $input.focus());
    $input.on("blur", () => setTimeout(() => $autocomplete.hide(), 200));
  }
  function renderAutocomplete(users, $autocomplete) {
    $autocomplete.empty().show();
    if (!users || users.length === 0) {
      $autocomplete.append(
        '<div class="p-2 text-sm text-gray-500">No users found.</div>'
      );
      return;
    }
    users.forEach((user) => {
      $autocomplete.append(
        `<div class="autocomplete-item p-2 hover:bg-indigo-100 cursor-pointer" data-id="${user.id}" data-name="${user.name}"><p class="font-medium">${user.name}</p><p class="text-sm text-gray-500">${user.email}</p></div>`
      );
    });
  }
  function addRecipientPill(id, name, $container) {
    if ($container.find(`.recipient-pill[data-id="${id}"]`).length > 0) return;
    $container
      .find(".recipient-input")
      .before(
        `<span class="recipient-pill" data-id="${id}">${name}<span class="remove-pill" title="Remove">×</span></span>`
      );
  }
  function sendEmail() {
    if (confirm("Are You Sure to Send Mail?")) {

      const formData = new FormData();
      formData.append("action", "emails");
      formData.append("sub_action", "send");
      formData.append("subject", $("#compose-subject").val());
      formData.append("content", $("#compose-content").val());
      const draftId = $("#compose-draft-id").val();
      if (draftId) formData.append("draft_id", draftId);
      $("#recipient-container-to .recipient-pill").each(function () {
        formData.append("to_ids[]", $(this).data("id"));
      });
      $("#recipient-container-cc .recipient-pill").each(function () {
        formData.append("cc_ids[]", $(this).data("id"));
      });
      if (!formData.has("to_ids[]") && !formData.has("cc_ids[]")) {
        alert("Please add at least one recipient.");
        return;
      }
      state.composeAttachments.forEach((file) =>
        formData.append("attachments[]", file)
      );
      api.call("emails", formData, "POST").done((response) => {
        if (response.success) {
          hideComposeModal();
          toastr.success('Sent Successfully');
          delete state.cachedEmails["sent"];
          delete state.cachedEmails["inbox"];
          delete state.cachedEmails["drafts"];
          loadEmails("sent");
          updateCounts();
        } else {
          toastr.error('Failed!');
        }
      });
    }
  }
  function saveAsDraft() {
    const formData = new FormData();
    formData.append("action", "emails");
    formData.append("sub_action", "save_draft");
    const draftId = $("#compose-draft-id").val();
    if (draftId) formData.append("draft_id", draftId);
    formData.append("subject", $("#compose-subject").val());
    formData.append("content", $("#compose-content").val());
    $("#recipient-container-to .recipient-pill").each(function () {
      formData.append("to_ids[]", $(this).data("id"));
    });
    $("#recipient-container-cc .recipient-pill").each(function () {
      formData.append("cc_ids[]", $(this).data("id"));
    });
    state.composeAttachments.forEach((file) =>
      formData.append("attachments[]", file)
    );
    api.call("emails", formData, "POST").done((response) => {
      if (response.success) {
        toastr.success('Message Saved as Draft!');
        hideComposeModal();
        delete state.cachedEmails["drafts"];
        loadEmails("drafts");
        updateCounts();
      } else {
        toastr.error('Could not connect to the server.');
      }
    });
  }
  function handleFileUpload(e) {
    const files = e.target.files || e.originalEvent.dataTransfer.files;
    Array.from(files).forEach((file) => state.composeAttachments.push(file));
    updateAttachmentsPreview();
  }
  function setupDragAndDrop() {
    const $dropzone = $("#file-upload");
    $dropzone.on("dragover", (e) => {
      e.preventDefault();
      e.stopPropagation();
      $(e.currentTarget).addClass("drag-over");
    });
    $dropzone.on("dragleave", (e) => {
      e.preventDefault();
      e.stopPropagation();
      $(e.currentTarget).removeClass("drag-over");
    });
    $dropzone.on("drop", (e) => {
      e.preventDefault();
      e.stopPropagation();
      $(e.currentTarget).removeClass("drag-over");
      handleFileUpload(e);
    });
  }
  function updateAttachmentsPreview(savedAttachments = []) {
    const $previewList = $("#attachments-list-preview").empty();

    const hasSaved = savedAttachments.length > 0;
    const hasNew = state.composeAttachments.length > 0;

    if (!hasSaved && !hasNew) {
      $("#attachments-preview").hide();
      return;
    }

    $("#attachments-preview").show();

    // Render saved attachments (with a delete button)
    savedAttachments.forEach((att) => {
      const fileSize =
        att.file_size > 1024 * 1024
          ? (att.file_size / (1024 * 1024)).toFixed(2) + " MB"
          : (att.file_size / 1024).toFixed(1) + " KB";
      $previewList.append(`
                <li class="flex items-center justify-between py-1 px-2 bg-gray-200 rounded text-gray-700">
                    <div class="flex items-center min-w-0">
                        <i class="fas fa-paperclip text-gray-600 mr-2"></i>
                        <span class="text-sm truncate" title="${att.file_name}">${att.file_name}</span>
                        <span class="text-xs ml-2 flex-shrink-0">(${fileSize})</span>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-red-600 remove-saved-attachment-btn ml-2" data-id="${att.id}" title="Delete saved attachment">×</button>
                </li>`);
    });

    // Render newly added attachments
    state.composeAttachments.forEach((file, index) => {
      const fileSize =
        file.size > 1024 * 1024
          ? (file.size / (1024 * 1024)).toFixed(2) + " MB"
          : (file.size / 1024).toFixed(1) + " KB";
      $previewList.append(`
                <li class="new-attachment flex items-center justify-between py-1 px-2 bg-indigo-100 rounded">
                    <div class="flex items-center min-w-0">
                        <i class="fas fa-file text-indigo-500 mr-2"></i>
                        <span class="text-sm text-indigo-700 truncate" title="${file.name}">${file.name}</span>
                        <span class="text-xs text-indigo-500 ml-2 flex-shrink-0">(${fileSize})</span>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-red-600 remove-new-attachment-btn ml-2" data-index="${index}" title="Remove file">×</button>
                </li>`);
    });
  }

  // --- Utility Functions ---
  function formatDate(dateString, includeTime = false) {
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(new Date().setDate(today.getDate() - 1));
    if (date.toDateString() === today.toDateString()) {
      return date.toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      });
    }
    if (date.toDateString() === yesterday.toDateString()) {
      return "Yesterday";
    }
    const options = { year: "numeric", month: "short", day: "numeric" };
    if (includeTime) {
      options.hour = "2-digit";
      options.minute = "2-digit";
    }
    return date.toLocaleDateString("en-US", options);
  }

  // --- Initialize The App ---
  init();
});
