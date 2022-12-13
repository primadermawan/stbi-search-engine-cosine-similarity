<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Search Engine</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminlte/bower_components/font-awesome/css/font-awesome.min.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminlte/bower_components/Ionicons/css/ionicons.min.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminlte/bower_components/select2/dist/css/select2.min.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminlte/dist/css/AdminLTE.min.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminlte/dist/css/skins/_all-skins.min.css">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-blue layout-top-nav">
    <div class="wrapper">
        <header class="main-header">
            <nav class="navbar navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                        <a href="<?php echo base_url(); ?>" class="navbar-brand">
                            <b>Search</b>
                            Engine
                        </a>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>

                    <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li class="<?php echo isset($first) ? 'active': ''; ?>">
                                <a href="<?php echo base_url(); ?>">
                                    Home 
                                    
                                </a>
                            </li>
                            <li class="<?php echo isset($pembobotan) ? 'active': ''; ?>">
                                <a href="<?php echo site_url('stbi_admin'); ?>">
                                    Admin
                                    
                                </a>
                            </li>
                            
                        </ul>
                        
                    </div>
                </div>
            </nav>
        </header>

        <div class="content-wrapper">
            <div class="container">
                <section class="content-header">
                    <h1>

                        Search Engine
                        <small>Tutorial</small>
                    </h1>
                    
                </section>

                <section class="content">
                    
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Input your keywords</h3>
                        </div>
                        <?php echo form_open('stbi/search'); ?>
                        <div class="box-body">
                            
                            
                            <div class="form-group">
                                <!-- <label for="kuantitas">Berapa banyak kandidat kos?</label> -->
                                <input id="keyword" name="keyword" type="text" class="form-control">
                            </div>

                            
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Process</button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                    
                    
                    <?php if(isset($hasil)): ?>
                        
                        
                        <div class="box">
                            <div class="box-header">
                                <h4>Hasil Pencarian pada kata kunci: <strong>"<?php echo $keyword; ?>"</strong></h4>
                            </div>
                            <div class="box-body">
                                <h5>Execution time: <?php echo $exec_time; ?></h5>
                                <h5><?php echo count($hasil); ?> <?php echo count($hasil) > 1 ? "results": "result"; ?> found</h5>
                                <?php foreach($hasil as $key=>$value): ?>
                                    
                                <div class="attachment-block clearfix">
                                    <img src="<?php echo $value['is_image'] == 0 ? base_url()."assets/tut_files/".$value['raw_name'].".jpg" : base_url()."assets/tut_files/".$value['file_name']; ?>" alt="" class="attachment-img">
                                    <div class="attachment-pushed">
                                        <h4 class="attachment-heading"><?php echo $value['tutorial_name']; ?></h4>
                                        <div class="attachment-text">
                                            <?php echo "Berada pada sub-tutorial: ".str_replace("_", " ", $value['raw_name']); ?><br>
                                            <?php echo "Nilai cosine: ".$value['cosine']; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                        </div>

                    <?php else: ?>
                        
                        <?php if(isset($first)): ?>
                        <div class="callout callout-warning">
                            <h4>Whoa..</h4>
                            <p>Search for "<?php echo $keyword; ?>" not found</p>
                            <p>Execution time: <?php echo $exec_time; ?></p>
                        </div>
                        <?php endif; ?>
                        
                    <?php endif; ?>

                </section>

            </div>

        </div>

        <footer class="main-footer">
            <div class="container">
                <div class="pull-right hidden-xs">
                    <b>Version</b>
                     2.4.13
                </div>
                <strong>
                    Copyright &copy; 2014-2019 
                    <a href="https://adminlte.io">AdminLTE</a>
                    .
                </strong>
                 All rights
                reserved.
            </div>

        </footer>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <script src="<?php echo base_url(); ?>assets/adminlte/bower_components/jquery/dist/jquery.min.js"></script>

    <script src="<?php echo base_url(); ?>assets/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <script src="<?php echo base_url(); ?>assets/adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>

    <script src="<?php echo base_url(); ?>assets/adminlte/bower_components/fastclick/lib/fastclick.js"></script>

    

    <script src="<?php echo base_url(); ?>assets/adminlte/dist/js/adminlte.min.js"></script>

    <script src="<?php echo base_url(); ?>assets/adminlte/dist/js/demo.js"></script>

  
    <script>
        

        $(".checking").on('change', function() {
        if ($(this).is(':checked')) {
            $(this).attr('value', 'true');
        } else {
            $(this).attr('value', 'false');
        }
        
        
        });
    </script>
</body>
</html>

