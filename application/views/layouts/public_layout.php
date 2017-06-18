<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $this->lang->line('app_site_title') ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo site_url('css/bootstrap.min.css') ?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo site_url('css/shop-item.css') ?>" rel="stylesheet">
    
    <!-- Jstree CSS -->
    <link href="<?php echo site_url('css/jstree/style.min.css') ?>" rel="stylesheet">
    
    <!-- Custom Fonts -->
    <link href="<?php echo site_url('font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet" type="text/css">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo site_url() ?>"><?php echo $this->lang->line('app_name') ?></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container">
    <?php 
    if($this->session->flashdata('error'))
        echo '<p class="alert alert-danger" >' . $this->session->flashdata('error') . '</p>'; 
    if($this->session->flashdata('info'))
        echo '<p class="alert alert-info" >' . $this->session->flashdata('info') . '</p>'; 
    if($this->session->flashdata('success'))
        echo '<p class="alert alert-success" >' . $this->session->flashdata('success') . '</p>'; 
    ?>
        <div class="row">
            <div class="col-md-12">
                <?php 
                //load content view "should set in controller"
                $this->load->view($content_view, $c_data) 
                ?>    
            </div>

        </div>
        <input type="hidden" name="site_url" value="<?php echo $site_url ?>" />
    </div>
    <!-- /.container -->

    <div class="container">

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p><?php echo $this->lang->line('app_layout_footer_copyright')?></p>
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->
    
    <!-- jQuery -->
    <script src="<?php echo site_url('js/jquery.js') ?>"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo site_url('js/bootstrap.min.js') ?>"></script>
    <!-- Bootbox js -->
    <script src="<?php echo site_url('js/bootbox/bootbox.min.js') ?>"></script>
    <!-- JS Tree -->
    <script src="<?php echo site_url('js/jstree.min.js') ?>"></script>
    <!-- My custom javascript -->
    <script src="<?php echo site_url('js/myjs.js') ?>"></script>

</body>

</html>
