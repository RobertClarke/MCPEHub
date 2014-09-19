<!DOCTYPE html>
<html>
<head>
    <title>MCPE Maps &raquo; MCPE Hub</title>
    <link href="./assets/css/main.css" media="all" rel="stylesheet" type="text/css" />
    <link href="./assets/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css" />
    
    
    
    
    
    
    
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300" media="all" rel="stylesheet" type="text/css">
    
    
    
    
    
    
    
    
    
    <script src="../assets/scripts/jquery-1.11.1.min.js" type="text/javascript"></script>
    
    <script>
    
    $(function() {
    	
    	$('.search-tog').click(function(){
	    	$('#search-head').fadeToggle(400);
	    	$('input#search').focus();
	    	event.preventDefault();
    	});
    	
    });
    
    </script>
    
</head>
<body>
    <!--<div id="header">-->
        <!--<div class="wrapper">
            <a href="#"><h1 id="logo">MCPE Hub</h1></a>
            <ul id="nav-main">
                <li class="active red"><a href="#">Maps</a></li>
                <li class="yellow"><a href="#">Seeds</a></li>
                <li class="green"><a href="#">Textures</a></li>
                <li class="blue"><a href="#">Mods</a></li>
                <li><a href="#">Servers</a></li>
            </ul>
        </div>-->
        
        <!--<a href="#"><h1 id="logo">MCPE Hub</h1></a>
        
    </div>-->
    
    <div id="header">
        <div class="wrapper">
            <a href="#"><h1 id="logo">MCPE Hub</h1></a>
            <h2 class="slogan">The #1 Minecraft Pocket Edition Community!</h2>
            <div id="nav-user">
                <ul>
                    <li><a href="#"><i class="fa fa-user fa-fw"></i></a></li>
                    <li><a href="#"><i class="fa fa-cogs fa-fw"></i></a></li>
                    <li class="active"><a href="#"><i class="fa fa-lock fa-fw"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div id="banner">
        
        <div class="wrapper">
        
        
        
        <!--<div class="top">
            <a href="#"><h1 id="logo">MCPE Hub</h1></a>
            
            <div id="nav-user">
                <ul>
                    <li><a href="#"><i class="fa fa-user fa-fw"></i></a></li>
                    <li><a href="#"><i class="fa fa-cogs fa-fw"></i></a></li>
                    <li><a href="#"><i class="fa fa-lock fa-fw"></i></a></li>
                </ul>
            </div>
            
        </div>
        -->
        
        
        <div class="tagline">
            <h1>MCPE Maps</h1>
            <h3>For iOS and Android</h3>
        </div>
        
        
        
        </div>
        
    </div>
    
    <div id="sub">
        
        <ul id="nav-main">
                <li class="active red"><a href="#">Maps</a></li>
                <li class="yellow"><a href="#">Seeds</a></li>
                <li class="green"><a href="#">Textures</a></li>
                <li class="blue"><a href="#">Mods</a></li>
                <li><a href="#">Servers</a></li>
            </ul>
        
        
    </div>
    
    <div id="example-ad" class="header">728x90</div>
    
    <div id="main">
        
        <div class="wrapper">
        
        <div id="content">
        
        <div id="page-head">
            <h2>Maps</h2>
            <div class="buttons">
                <a href="#"><i class="fa fa-question fa-fw"></i> How To Install</a>
                <a href="#search" class="search-tog"><i class="fa fa-search fa-fw"></i> Search</a>
                <a href="#" class="green"><i class="fa fa-plus fa-fw"></i> Submit Map</a>
            </div>
            <div class="clear"></div>
        </div>
        
        <div id="search-head" class="solo">
            <form action="" method="GET">
            
            <input type="text" name="search" id="search" class="text" value="" placeholder="Search Maps..." />
            
            <select name="type" id="type">
                <option value="">Map Type</option>
                <option value="survival">Survival</option>
                <option value="creative">Creative</option>
                <option value="adventure">Adventure</option>
	            <option value="puzzle">Puzzle</option>
	            <option value="pvp">PVP</option>
	            <option value="parkour">Parkour</option>
	            <option value="minigame">Minigame</option>
	            <option value="pixel-art">Pixel Art</option>
	            <option value="roller-coaster">Roller Coaster</option>
            </select>
            
            <input type="submit" name="submit" id="submit" class="button" value="Search" />
            </form>
        </div>
        
        <div class="post-sort">
            <a href="maps.php?sort=latest">Newest Uploads</a>
            <a href="maps.php?sort=views">Most Viewed</a>
            <a href="maps.php?sort=downloads">Most Downloaded</a>
        </div>
        
        <div class="post">
            <div class="img"><a href="#"><img src="./assets/img/test.png" alt="" /></a></div>
            <div class="info">
                <img src="./assets/img/test3.jpg" class="avatar" />
                <h2><a href="#">Ported Savanna With Desert Village and more!</a></h2>
                <ul>
                    <li><i class="fa fa-tags fa-fw"></i> adventure</li>
                    <li><i class="fa fa-eye fa-fw"></i> 4939</li>
                    <li><i class="fa fa-thumbs-up fa-fw"></i> 1492</li>
                    <li><a href="#" class="dl"><span><i class="fa fa-download fa-fw"></i></span>537</a></li>
                </ul>
            </div>
        </div>
        
        <div class="post">
            <div class="img"><a href="#"><img src="./assets/img/test.png" alt="" /></a></div>
            <div class="info">
                <img src="./assets/img/test2.jpg" class="avatar" />
                <h2><a href="#">Ported Savanna With Desert Village and more!</a></h2>
                <ul>
                    <li><i class="fa fa-tags fa-fw"></i> adventure</li>
                    <li><i class="fa fa-eye fa-fw"></i> 4939</li>
                    <li><i class="fa fa-thumbs-up fa-fw"></i> 1492</li>
                    <li><a href="#" class="dl"><span><i class="fa fa-download fa-fw"></i></span>537</a></li>
                </ul>
            </div>
        </div>
        
        <div class="post">
            <div class="img"><a href="#"><img src="./assets/img/test.png" alt="" /></a></div>
            <div class="info">
                <img src="./assets/img/test3.jpg" class="avatar" />
                <h2><a href="#">Ported Savanna With Desert Village and more!</a></h2>
                <ul>
                    <li><i class="fa fa-tags fa-fw"></i> adventure</li>
                    <li><i class="fa fa-eye fa-fw"></i> 4939</li>
                    <li><i class="fa fa-thumbs-up fa-fw"></i> 1492</li>
                    <li><a href="#" class="dl"><span><i class="fa fa-download fa-fw"></i></span>537</a></li>
                </ul>
            </div>
        </div>
        
        <div class="post">
            <div class="img"><a href="#"><img src="./assets/img/test.png" alt="" /></a></div>
            <div class="info">
                <img src="./assets/img/test3.jpg" class="avatar" />
                <h2><a href="#">Ported Savanna With Desert Village and more!</a></h2>
                <ul>
                    <li><i class="fa fa-tags fa-fw"></i> adventure</li>
                    <li><i class="fa fa-eye fa-fw"></i> 4939</li>
                    <li><i class="fa fa-thumbs-up fa-fw"></i> 1492</li>
                    <li><a href="#" class="dl"><span><i class="fa fa-download fa-fw"></i></span>537</a></li>
                </ul>
            </div>
        </div>
        
        <div class="post">
            <div class="img"><a href="#"><img src="./assets/img/test.png" alt="" /></a></div>
            <div class="info">
                <img src="./assets/img/test3.jpg" class="avatar" />
                <h2><a href="#">Ported Savanna With Desert Village and more!</a></h2>
                <ul>
                    <li><i class="fa fa-tags fa-fw"></i> adventure</li>
                    <li><i class="fa fa-eye fa-fw"></i> 4939</li>
                    <li><i class="fa fa-thumbs-up fa-fw"></i> 1492</li>
                    <li><a href="#" class="dl"><span><i class="fa fa-download fa-fw"></i></span>537</a></li>
                </ul>
            </div>
        </div>
        
        <div class="post">
            <div class="img"><a href="#"><img src="./assets/img/test.png" alt="" /></a></div>
            <div class="info">
                <img src="./assets/img/test3.jpg" class="avatar" />
                <h2><a href="#">Ported Savanna With Desert Village and more!</a></h2>
                <ul>
                    <li><i class="fa fa-tags fa-fw"></i> adventure</li>
                    <li><i class="fa fa-eye fa-fw"></i> 4939</li>
                    <li><i class="fa fa-thumbs-up fa-fw"></i> 1492</li>
                    <li><a href="#" class="dl"><span><i class="fa fa-download fa-fw"></i></span>537</a></li>
                </ul>
            </div>
        </div>
        
        <div class="post">
            <div class="img"><a href="#"><img src="./assets/img/test.png" alt="" /></a></div>
            <div class="info">
                <img src="./assets/img/test3.jpg" class="avatar" />
                <h2><a href="#">Ported Savanna With Desert Village and more!</a></h2>
                <ul>
                    <li><i class="fa fa-tags fa-fw"></i> adventure</li>
                    <li><i class="fa fa-eye fa-fw"></i> 4939</li>
                    <li><i class="fa fa-thumbs-up fa-fw"></i> 1492</li>
                    <li><a href="#" class="dl"><span><i class="fa fa-download fa-fw"></i></span>537</a></li>
                </ul>
            </div>
        </div>
        
        <div class="post">
            <div class="img"><a href="#"><img src="./assets/img/test.png" alt="" /></a></div>
            <div class="info">
                <img src="./assets/img/test3.jpg" class="avatar" />
                <h2><a href="#">Ported Savanna With Desert Village and more!</a></h2>
                <ul>
                    <li><i class="fa fa-tags fa-fw"></i> adventure</li>
                    <li><i class="fa fa-eye fa-fw"></i> 4939</li>
                    <li><i class="fa fa-thumbs-up fa-fw"></i> 1492</li>
                    <li><a href="#" class="dl"><span><i class="fa fa-download fa-fw"></i></span>537</a></li>
                </ul>
            </div>
        </div>
        
        <div class="post">
            <div class="img"><a href="#"><img src="./assets/img/test.png" alt="" /></a></div>
            <div class="info">
                <img src="./assets/img/test3.jpg" class="avatar" />
                <h2><a href="#">Ported Savanna With Desert Village and more!</a></h2>
                <ul>
                    <li><i class="fa fa-tags fa-fw"></i> adventure</li>
                    <li><i class="fa fa-eye fa-fw"></i> 4939</li>
                    <li><i class="fa fa-thumbs-up fa-fw"></i> 1492</li>
                    <li><a href="#" class="dl"><span><i class="fa fa-download fa-fw"></i></span>537</a></li>
                </ul>
            </div>
        </div>
        
        <div class="post">
            <div class="img"><a href="#"><img src="./assets/img/test.png" alt="" /></a></div>
            <div class="info">
                <img src="./assets/img/test3.jpg" class="avatar" />
                <h2><a href="#">Ported Savanna With Desert Village and more!</a></h2>
                <ul>
                    <li><i class="fa fa-tags fa-fw"></i> adventure</li>
                    <li><i class="fa fa-eye fa-fw"></i> 4939</li>
                    <li><i class="fa fa-thumbs-up fa-fw"></i> 1492</li>
                    <li><a href="#" class="dl"><span><i class="fa fa-download fa-fw"></i></span>537</a></li>
                </ul>
            </div>
        </div>
        
        </div>
        
        <div id="sidebar">
            
            <div id="user-details">
                <div class="avatar"><img src="./assets/img/test3.jpg" /></div>
                <div class="info">
                    <p>Howdy, <strong>anatolie</strong>!</p>
                    <a href="profile.php" class="button"><i class="fa fa-user fa-fw"></i> Profile</a>
                    <a href="login.php?logout=true" class="button"><i class="fa fa-lock fa-fw"></i> Logout</a>
                </div>
                <div class="clear"></div>
            </div>
            
            <div class="links">
                <li><a href="dashboard.php"><i class="fa fa-tachometer fa-fw"></i> Dashboard</a></li>
<li><a href="./admin"><i class="fa fa-bolt fa-fw"></i> Admin Panel</a></li><li><a href="moderate.php"><i class="fa fa-eye fa-fw"></i> Moderate Posts</a></li>        <li><a href="dashboard.php"><i class="fa fa-pencil fa-fw"></i> Edit Submissions</a></li>
                <li><a href="settings.php?tab=avatar"><i class="fa fa-picture-o fa-fw"></i> Change Avatar</a></li>
                <li><a href="settings.php"><i class="fa fa-cogs fa-fw"></i> My Account</a></li>
            </div>
            
            <div id="example-ad" class="sidebar">250x200</div>
            
            
        </div>
        
        <div class="clear"></div>
        
        </div>
        
    </div>
    
    <!--<center>
    <img src="test.jpg" />
    </center>-->
    
</body>
</html>