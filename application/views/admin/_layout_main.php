
<?php $this->load->view('admin/components/header'); ?>

<body class="skin-<?= config_item('sidebar_theme'); ?> sidebar-mini">
    <div class="wrapper">
        <?php $this->load->view('admin/components/user_profile'); ?>

        <?php $this->load->view('admin/components/navigation'); ?>
        <!-- Right side column. Contains the navbar and content of the page -->

        <div class="content-wrapper">
            <section class="content-header">
                <h1 style="visibility: hidden">
                    Dashboard
                </h1>
                <ol class="breadcrumb">
                    <?php echo $this->breadcrumbs->build_breadcrumbs(); ?>
                </ol>
            </section>
            <section class="content">
                <?php echo $subview ?>
            </section>


        </div><!-- /.right-side -->
        <div class="control-sidebar-bg"></div>


        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.1
            </div>
            <strong>Copyright &copy; <a href="#">Lexnjugz</a>.</strong> All rights reserved.
        </footer>
    </div><!-- ./wrapper -->
    <?php $this->load->view('admin/_layout_modal'); ?>
    <?php $this->load->view('admin/components/footer'); ?>
