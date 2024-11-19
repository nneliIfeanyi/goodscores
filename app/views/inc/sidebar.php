 <!-- ======= Sidebar ======= -->
 <aside id="sidebar" class="sidebar">

   <ul class="sidebar-nav" id="sidebar-nav">

     <li class="nav-item">
       <a class="nav-link collapsed" href="<?php echo URLROOT; ?>/users/dashboard">
         <i class="bi bi-grid"></i>
         <span>Dashboard</span>
       </a>
     </li><!-- End Dashboard Nav -->


     <li class="nav-item">
       <a class="nav-link collapsed" href="<?php echo URLROOT; ?>/users/classes">
         <i class="bi bi-people"></i>
         <span>Classes</span>
       </a>
     </li><!-- End Classes Nav -->
     <li class="nav-item">
       <a class="nav-link collapsed" href="<?php echo URLROOT; ?>/users/subjects">
         <i class="bi bi-journal-text"></i>
         <span>Subjects</span>
       </a>
     </li><!-- End Subjects Nav -->
     <?php if ($_COOKIE['cbt'] == '1') : ?>
       <li class="nav-item">
         <label class="nav-link collapsed" data-bs-target="#students-nav" data-bs-toggle="collapse" href="#">
           <i class="bi bi-gear"></i><span>Students</span><i class="bi bi-chevron-down ms-auto"></i>
         </label>
         <ul id="students-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
           <li>
             <a href="<?php echo URLROOT; ?>/students">
               <i class="bi bi-circle"></i><span>Database</span>
             </a>
           </li>
           <li>
             <a href="<?php echo URLROOT; ?>/students/assessment">
               <i class="bi bi-circle"></i><span>Assessment</span>
             </a>
           </li>

         </ul>
       </li>
     <?php endif; ?>

     <li class="nav-item">
       <label class="nav-link collapsed" data-bs-target="#setquestionsnav" data-bs-toggle="collapse" href="#">
         <i class="bi bi-pen"></i><span>New Document</span><i class="bi bi-chevron-down ms-auto"></i>
       </label>
       <ul id="setquestionsnav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
         <li>
           <a href="<?php echo URLROOT; ?>/users/set/objectives_questions">
             <i class="bi bi-circle"></i><span>Objectives Questions</span>
           </a>
         </li>
         <li>
           <a href="<?php echo URLROOT; ?>/users/set/theory_questions">
             <i class="bi bi-circle"></i><span>Theory Questions</span>
           </a>
         </li>
         <!-- <li>
           <a href="<?php echo URLROOT; ?>/users/set/custom">
             <i class="bi bi-circle"></i><span>Custom</span>
           </a>
         </li> -->

       </ul>
     </li><!-- End Set Questions Nav -->

     <li class="nav-heading">More</li>

     <li class="nav-item">
       <a class="nav-link collapsed" href="<?= URLROOT; ?>/pages/profile/<?= $_COOKIE['sch_id']; ?>">
         <i class="bi bi-gear"></i>
         <span>Site Settings</span>
       </a>
     </li><!-- End Profile Page Nav -->


     <!-- <li class="nav-item">
       <a class="nav-link collapsed" href="<?= URLROOT; ?>/pages/logout">
         <i class="bi bi-chevron-left"></i>
         <span>LOGOUT</span>
       </a>
     </li> --><!-- End Profile Page Nav -->



   </ul>

 </aside><!-- End Sidebar-->