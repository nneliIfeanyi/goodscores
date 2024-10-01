<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo SITENAME; ?></title>
  <meta name="keyword" content="Online exam creation for teachers, Automated grading system for educators, Exam management software for schools, Online quiz platform for students, Educational assessment tools for teachers, Educational technology, Student evaluation, Create online exams" />
  <meta name="description" content="Goodscores - The ultimate tool for teachers to create, manage and grade exams/assessments online. The leading online platform for educators." />
  <meta name="robots" content="index, nofollow" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="apple-mobile-web-app-title" content="goodscores" />
  <meta name="application-name" content="Goodscores" />
  <meta name="msapplication-TileName" content="Goodscores" />
  <meta name="msapplication-TileImage" content="<?php echo URLROOT; ?>/icons/mask3.png" width="20" height="20" />
  <!-- Site Manifest -->
  <link href="<?php echo URLROOT; ?>/site.webmanifest" rel="manifest">
  <meta name="apple-mobile-web-app-status-bar" content="#0d6efd">
  <meta name="theme-color" content="#0d6efd">
  <!-- Favicons -->
  <link href="<?php echo URLROOT; ?>/icons/mask3.png" rel="icon">
  <link href="<?php echo URLROOT; ?>/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo URLROOT; ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo URLROOT; ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo URLROOT; ?>/assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo URLROOT; ?>/assets/css/style.css" rel="stylesheet">
  <link href="<?php echo URLROOT; ?>/assets/css/styles.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
  <style type="text/css">
    #loader {
      min-height: 100%;
      z-index: 1000;
    }

    /*Ajax response Message*/
    .flash-msg {
      margin: 0;
      position: fixed;
      top: 0;
      right: 0;
      width: auto;
      text-align: center;
      z-index: 500;
      animation-name: fade;
      animation-duration: 3s;
      animation-delay: 5s;
      animation-iteration-count: 1;
      animation-fill-mode: forwards;
      transition: 2s;
    }

    /*flash Message*/
    #msg-flash {
      margin: 0;
      position: fixed;
      bottom: 0;
      right: 0;
      width: auto;
      text-align: center;
      z-index: 500;
      animation-name: fade;
      animation-duration: 3s;
      animation-delay: 5s;
      animation-iteration-count: 1;
      animation-fill-mode: forwards;
      transition: 2s;
    }

    @keyframes fade {
      from {
        z-index: 500;
      }

      to {
        visibility: hidden;
        z-index: -1;

      }
    }

    /*Parsley validate*/
    input.parsley-error,
    select.parsley-error,
    textarea.parsley-error {
      border-color: #D43F3A;
      box-shadow: none;
    }

    input.parsley-error:focus,
    select.parsley-error:focus,
    textarea.parsley-error:focus {
      border-color: #D43F3A;
      box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 6px #FF8F8A;
    }

    input.parsley-success,
    select.parsley-success,
    textarea.parsley-success {
      border-color: #398439;
      box-shadow: none;
    }

    input.parsley-success:focus,
    select.parsley-success:focus,
    textarea.parsley-success:focus {
      border-color: #398439;
      box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 6px #89D489
    }

    .parsley-errors-list {
      list-style-type: none;
      padding-left: 0;
      margin-top: 5px;
      margin-bottom: 0;
    }

    .parsley-errors-list.filled {
      color: #D43F3A;
      opacity: 1;
    }
  </style>
</head>