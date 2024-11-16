<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>


<main id="main" class="main">

    <div class="pagetitle">
        <h1>Profile</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/dashboard">Home</a></li>
                <li class="breadcrumb-item">School</li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row">
            <div class="col-xl-4">

                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                        <?php if (empty($data['user']->img)) : ?>
                            <img src="<?= URLROOT; ?>/assets/img/user.jpg" alt="Profile" class="rounded-circle">
                        <?php else : ?>
                            <img src="<?= URLROOT; ?>/<?= $data['user']->img; ?>" alt="Profile" class="rounded-circle">
                        <?php endif; ?>
                        <h2><?= $data['user']->name; ?></h2>
                        <h3><?= $data['user']->motto; ?></h3>
                        <!-- <div class="social-links mt-2">
                            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                        </div> -->
                    </div>
                </div>

            </div>

            <div class="col-xl-8">

                <div class="card">
                    <div class="card-body pt-3">
                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered">

                            <li class="nav-item">
                                <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">School Profile</button>
                            </li>

                            <!-- <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Badge | logo</button>
                            </li> -->
                            <?php if ($data['isCbt'] == '1') : ?>
                                <li class="nav-item">
                                    <a href="<?= URLROOT; ?>/pages/advance" class="nav-link">Classes</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['role'] == 'Admin') : ?>
                                <?php if ($data['user']->isPaid == '1' && $data['user']->isCbt == '0') : ?>
                                    <li class="nav-item">
                                        <a href="<?= URLROOT; ?>/pages/iscbt" class="nav-link">Activate CBT</a>
                                    </li>
                                <?php endif ?>
                                <li class="nav-item">
                                    <a href="<?= URLROOT; ?>/pages/logout" class="nav-link">Logout</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <div class="tab-content pt-2">

                            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                <!-- <h5 class="card-title">About</h5>
                                <p class="small fst-italic">Sunt est soluta temporibus accusantium neque nam maiores cumque temporibus. Tempora libero non est unde veniam est qui dolor. Ut sunt iure rerum quae quisquam autem eveniet perspiciatis odit. Fuga sequi sed ea saepe at unde.</p> -->

                                <h5 class="card-title">Profile Details</h5>
                                <form action="<?= URLROOT; ?>/pages/profile/<?= $data['user']->id; ?>" method="POST">
                                    <div class="row mb-3">
                                        <label for="name" class="col-md-4 col-lg-3 col-form-label">Name of institution</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="name" class="form-control" id="name" value="<?= $data['user']->name; ?>">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="username" class="col-md-4 col-lg-3 col-form-label">Username</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="username" class="form-control" id="username" value="<?= $data['user']->username; ?>">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="role" class="col-md-4 col-lg-3 col-form-label">Motto</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="motto" class="form-control" id="role" value="<?= $data['user']->motto; ?>">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="phone" class="form-control" id="phone" value="<?= $data['user']->phone; ?>">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="address" class="form-control" id="address" value="<?= $data['user']->address; ?>">
                                        </div>
                                    </div>
                                    <?php if ($data['role'] == 'Admin') : ?>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    <?php endif ?>
                                </form><!-- End Profile Edit Form -->
                            </div>

                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                                <!-- Profile photo tab -->
                                <div class="row mb-3">
                                    <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                                    <div class="col-md-8 col-lg-9">
                                        <?php if (empty($data['user']->img)) : ?>
                                            <img src="<?= URLROOT; ?>/assets/img/user.jpg" alt="Profile" class="rounded-circle">
                                        <?php else : ?>
                                            <img src="<?= URLROOT; ?>/<?= $data['user']->img; ?>" alt="Profile" class="rounded-circle">
                                        <?php endif; ?>
                                        <div class="p-2 mt-2">
                                            <a href="javascript:void();" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-title="Upload new logo"><i class="bi bi-upload"></i></a>
                                            <a href="javascript:void();" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-title="Remove logo"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="tab-pane fade pt-3" id="profile-change-password">
                                <!-- Change Password Form -->
                                <form id="resetPass" action="javascript:void();" method="POST">

                                    <div class="row mb-3">
                                        <label for="email" class="col-md-4 col-lg-3 col-form-label">Current Email</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="email" type="email" data-parsley-type="email" value="<?= $data['user']->email; ?>" required data-parsley-trigger="keyup" required class="form-control" id="email">
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Change Email</button>
                                    </div>
                                </form><!-- End Change Password Form -->
                            </div>
                        </div><!-- End Bordered Tabs -->

                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->
<?php require APPROOT . '/views/inc/footer.php'; ?>
<script>
    $('#resetPass').parsley();
</script>