<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>

	<nav class="navbar navbar-expand-lg navbar-light bg-light">
	  <a class="navbar-brand" href="#">Navbar</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>
	  <div class="collapse navbar-collapse" id="navbarNav">
	    <ul class="navbar-nav">
	      <li class="nav-item active">
	        <a class="nav-link" href="<?php echo site_url('dashboard/users')?>">Users</a>
	      </li>
	      <li class="nav-item active">
	        <a class="nav-link" href="<?php echo site_url('dashboard/questions')?>">Questions</a>
	      </li>
	      <li class="nav-item active">
	        <a class="nav-link" href="<?php echo site_url('dashboard/responses')?>">Responses</a>
	      </li>
	    </ul>
	  </div>
	</nav>

	<div style='height:20px;'></div>
    <div class="content" style="padding: 10px;">
    	<div class="row d-flex justify-content-center">
    		<div class="col-md-11" >
    			
			<?php echo $output; ?>
    		</div>	
    	</div>
    </div>
    <?php foreach($js_files as $file): ?>
        <script src="<?php echo $file; ?>"></script>
    <?php endforeach; ?>
</body>
</html>
