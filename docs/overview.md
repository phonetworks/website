---
currentMenu: home
---


<script>
function close_all() {
    document.getElementById('dCloning').style.display="none";
    document.getElementById('dEditing').style.display="none";
    document.getElementById('dBuilding').style.display="none";
    document.getElementById('dPlaying').style.display="none";
    document.getElementById('dServing').style.display="none";
    var i = 0;
    while(i < document.getElementsByClassName('dPicker').length) {
        document.getElementsByClassName('dPicker')[i++].style.fontWeight="400";
    }
}
var first = {};
first["Cloning"] = true;
function pick(div, to_bolden) {
    close_all();
    if(first[div]==undefined||!first[div]) {
        var x = document.createElement("script");
        var data_id = document.getElementById('d'+div).getAttribute("data-id");
        x.src = "https://asciinema.org/a/"+data_id+".js";
        x.async = true;
        x.id = "asciicast-"+data_id;
        x.setAttribute("data-autoplay", true);
        x.setAttribute("data-speed", 2);
        document.getElementById('d'+div).appendChild(x);
        first[div] = true;
    }
    document.getElementById('d'+div).style.display="block";
    to_bolden.style.fontWeight="900";
}
</script>

## Meet Graph Apps 👯‍

Phở Networks provides a whiteboard-friendly way to create social applications. Using our open-source [Gapp Designer](/designer.html) tool, in just a few touches, you can create the backend for the next Facebook, that's designed to handle thousands of users concurrently. 

Best of all, Phở is open-source and [MIT](https://github.com/phonetworks/pho-framework/blob/master/LICENSE) license'd.


#### Requirements
To get started, you must have [PHP 7.2+](https://www.php.net), [Redis](https://redis.io), and [Neo4J](https://www.neo4j.org) installed on your platform. Just download the [pho-cli PHAR file](https://github.com/phonetworks/pho-cli/releases/download/0.2/pho.phar) and move it to your `/usr/local/bin` or an equivalent.

#### Getting Started

[ <a href="#" onclick="pick('Cloning', this)" class="dPicker" style="font-weight:900;">Starting a Project</a> ] &nbsp; [ <a href="#" onclick="pick('Editing', this)" class="dPicker">Editing Schema Files</a> ] &nbsp; [ <a href="#" onclick="pick('Building', this)"  class="dPicker">Building & Preparing to Launch</a> ] &nbsp; [ <a href="#" onclick="pick('Playing', this)"  class="dPicker">Playing with the Project</a> ]  &nbsp; [ <a href="#" onclick="pick('Serving', this)"  class="dPicker">Serving the APIs</a> ]

<div  style="display:block;" data-id="hiTG4Dsbn2ekSlkBz3iItNKOd" id="dCloning">
<script type="text/javascript" src="https://asciinema.org/a/hiTG4Dsbn2ekSlkBz3iItNKOd.js" id="asciicast-hiTG4Dsbn2ekSlkBz3iItNKOd" data-autoplay="true" data-speed="2" data-cols="120" data-rows="20" async></script>
</div>

<div id="dEditing" data-id="TrRX4D0igW2vG9rMaH82P0amR" style="display:none;">
</div>

<div id="dBuilding" data-id="BtRzCuMeF3vdwmGoQDxEGPHtF" style="display:none;">
</div>

<div id="dPlaying" data-id="3X23d5tsibWMvR9YidhajzNb5" style="display:none;">
</div>

<div id="dServing" data-id="Dc1jQjCyDmd0gv2unUpo1OWfn" style="display:none;">
</div>


<style>
.myButton {
	box-shadow:inset 0px 1px 0px 0px #97c4fe;
	background:linear-gradient(to bottom, #3d94f6 5%, #1e62d0 100%);
	background-color:#3d94f6;
	border-radius:6px;
	border:1px solid #337fed;
	display:inline-block;
	cursor:pointer;
	color:#ffffff;
	font-family:Arial;
	font-size:15px;
	font-weight:bold;
	padding:12px 42px;
	text-decoration:none;
	text-shadow:0px 1px 0px #1570cd;
}
.myButton:hover {
	background:linear-gradient(to bottom, #1e62d0 5%, #3d94f6 100%);
	background-color:#1e62d0;
    color:#ffffff;
    text-decoration:none;
}
.myButton:active {
	position:relative;
	top:1px;
}
</style>
 <a href="/designer.html" class="myButton">Design Schema</a>







