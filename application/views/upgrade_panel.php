<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h2>Increase your repository space</h2>

<div class="panel panel-info">
<div class="panel-heading">
Your panel info
</div>
<div class="panel-body">
<p><b>Your Email : </b> <?php echo $panel_info['email'] ?></p>
<p><b>Your Space : </b> <?php echo digital_size_show($panel_info['space'], 'mb') ?></p>
<p><a href="<?php echo site_url('panel/dashboard/upgrade_panel/10') ?>" class="btn btn-success">Add 10 Mb to your repository</a></p>
<p><a href="<?php echo site_url('panel/dashboard/upgrade_panel/20') ?>" class="btn btn-success">Add 20 Mb to your repository</a></p>
<p><a href="<?php echo site_url('panel/dashboard/upgrade_panel/30') ?>" class="btn btn-success">Add 30 Mb to your repository</a></p>
</div>

</div>