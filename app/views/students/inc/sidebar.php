 <!-- ======= Sidebar ======= -->
 <aside id="sidebar" class="sidebar">

   <ul class="sidebar-nav" id="sidebar-nav">

     <li class="nav-item">
       <a class="nav-link collapsed" href="<?php echo URLROOT; ?>/students/dashboard">
         <i class="bi bi-grid"></i>
         <span>Dashboard</span>
       </a>
     </li><!-- End Dashboard Nav -->


     <li class="nav-item">
       <a class="nav-link collapsed" href="<?= URLROOT; ?>/students/profile/<?= $_SESSION['student_id']; ?>">
         <i class="bi bi-gear"></i>
         <span>Profile Settings</span>
       </a>
     </li><!-- End Profile Page Nav -->

     <li class="nav-heading">More</li>
     <li class="nav-item">
       <a class="nav-link collapsed" href="<?= URLROOT; ?>/students/logout">
         <i class="bi bi-chevron-left"></i>
         <span>LOGOUT</span>
       </a>
     </li> <!-- End Profile Page Nav -->



   </ul>

 </aside><!-- End Sidebar-->