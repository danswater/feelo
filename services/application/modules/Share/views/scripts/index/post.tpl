<!DOCTYPE html>
<html>
    <head>
        <meta property="og:site_name" content="Yamba Rocks"/>
        <meta property="og:url" content="http://yamba.rocks/services/share/index/post?project_id=<?php echo $this->project->project_id; ?>" />
        <meta property="og:title" content="<?php echo $this->project->title; ?>" />
        <meta property="og:description" content="<?php echo $this->project->description; ?>" />
        <meta property="og:image" content="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->project->getPhotoUrl(); ?>" />
        <meta property="fb:app_id" content="<?php echo $this->fb_id; ?>" />
		<title><?php echo $this->project->title; ?></title>
    </head>
    <body>
        <p> <?php echo $this->project->description; ?> </p>
        <img src="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->project->getPhotoUrl(); ?>" />
    </body>
</html>