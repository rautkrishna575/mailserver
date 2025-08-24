 <!-- Scripts -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
 <!-- Preline is not strictly necessary if you build your own modal JS, but it's here for consistency with the original -->
 <script src="https://cdn.jsdelivr.net/npm/@preline/preline@2.0.0/dist/preline.min.js"></script>
 <script src="./assets/js/app.jquery.js"></script>
 <script>
     // Simple script to handle the user menu dropdown
     $(document).ready(function() {
         $('#user-menu-button').on('click', function() {
             $('#user-menu').toggleClass('hidden');
         });
         // Hide menu if clicked outside
         $(document).on('click', function(event) {
             if (!$(event.target).closest('#user-menu-button').length && !$(event.target).closest('#user-menu').length) {
                 $('#user-menu').addClass('hidden');
             }
         });
     });
 </script>