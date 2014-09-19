<?php 

global $user, $auth, $page_sidebar;

if ( !isset( $page_sidebar ) ) $page_sidebar = TRUE;
if ( $page_id == 'login' ) $page_sidebar = FALSE;

$page_current = basename( $_SERVER['PHP_SELF'], '.php' );

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php if ( $page_title ) echo $page_title.' &raquo; '; ?>MCPE Hub</title>
    <link href="./assets/css/admin.css" media="all" rel="stylesheet" type="text/css" />
    <link href="../assets/css/main.css" media="all" rel="stylesheet" type="text/css" />
    <link href="../assets/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css" />
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300" media="all" rel="stylesheet" type="text/css">
    <script src="../assets/js/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script src="../core/tinymce/tinymce.min.js" type="text/javascript"></script>
    <script src="../assets/js/main.js" type="text/javascript"></script>
    <script>
  !function(g,s,q,r,d){r=g[r]=g[r]||function(){(r.q=r.q||[]).push(
  arguments)};d=s.createElement(q);q=s.getElementsByTagName(q)[0];
  d.src='//d1l6p2sc9645hc.cloudfront.net/tracker.js';q.parentNode.
  insertBefore(d,q)}(window,document,'script','_gs');
  
  _gs('GSN-032559-K');
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-8521263-42', 'mcpehub.com');
  ga('send', 'pageview');

</script>
</head>
<body<?php if ( isset( $page_id ) ) echo ' id="'.$page_id.'"'; ?>>
    <div id="header">
        <div class="wrapper">
            <a href="index.php"><h1 id="logo">MCPE Hub</h1></a>
            <h2 class="slogan">The #1 Minecraft Pocket Edition Community!</h2>
            <div id="nav-user">
                <ul>
                    <li><a href="../index.php"><i class="fa fa-home fa-fw"></i></a></li>
<?php if ( $user->loggedIn() ) { ?>
                    <li><a href="../dashboard.php"><i class="fa fa-tachometer fa-fw"></i></a></li>
<?php if ( $user->isAdmin() ) { ?>
                    <li><a href="index.php"><i class="fa fa-bolt fa-fw"></i></a></li>
<?php } ?>
                    <li><a href="../profile.php"><i class="fa fa-user fa-fw"></i></a></li>
                    <li><a href="../settings.php"><i class="fa fa-cogs fa-fw"></i></a></li>
                    <li class="active"><a href="login.php?logout=true"><i class="fa fa-lock fa-fw"></i> Logout</a></li>
<?php } else { ?>
                    <li><a href="../login.php"><i class="fa fa-unlock fa-fw"></i> Sign In</a></li>
                    <li class="active"><a href="../register.php"><i class="fa fa-star fa-fw"></i> Register</a></li>
<?php } ?>
                </ul>
            </div>
        </div>
    </div>
    
    <div id="banner">
        <div class="wrapper">
            <div class="tagline">
                <h1>MCPE Hub Admin</h1>
                <h3>Manage The Website &amp; Content</h3>
            </div>
        </div>
    </div>
    
    <div id="sub">
        <ul id="nav-main">
            <li class="red<?php if ( $page_current == 'maps' ) echo ' active'; ?>"><a href="../maps.php">Maps</a></li>
            <li class="yellow<?php if ( $page_current == 'seeds' ) echo ' active'; ?>"><a href="../seeds.php">Seeds</a></li>
            <li class="green<?php if ( $page_current == 'textures' ) echo ' active'; ?>"><a href="../textures.php">Textures</a></li>
            <li class="blue<?php if ( $page_current == 'mods' ) echo ' active'; ?>"><a href="../mods.php">Mods</a></li>
            <li<?php if ( $page_current == 'servers' ) echo ' class="active"'; ?>><a href="../servers.php">Servers</a></li>
        </ul>
    </div>
    
    <div id="main"<?php if ( $page_sidebar ) echo ' class="sidebar"'; ?>>
        
        <div class="wrapper">
        
<?php if ( $page_sidebar ) { ?>	        <div id="content"><?php } ?>
