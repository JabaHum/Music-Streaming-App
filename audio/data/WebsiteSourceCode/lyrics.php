<?php
error_reporting(0);
include("./database/db_connection.php");
include_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
if (isset($_GET['query']))
{
$query=$_GET['query'];
}
$query = htmlspecialchars($query);
// changes characters used in html to their equivalents, for example: < to &gt;
$query = mysqli_real_escape_string($conn,$query);
// makes sure nobody uses SQL injection
$sql1 = "select * from albumlyrics where Album LIKE '%$query%' ";
$adjacents = 5;
$targetpage = "lyrics.php"; //your file name
$limit = 5; //how many items to show per page
if ( $detect->isMobile() ) {
$limit = 5;
}
if(!isset($_GET['page'])){
$page = 0;
}
else
{
$page = $_GET['page'];
}
if($page){
$start = ($page - 1) * $limit; //first item to display on this page
}else{
$start = 0;
}
/* Setup page vars for display. */
if ($page == 0) $page = 1; //if no page var is given, default to 1.
$prev = $page - 1; //previous page is current page - 1
$next = $page + 1; //next page is current page + 1
if (isset($_GET['sorting']))
{
$sorting = $_GET['sorting'];
}
if($sorting=='Ascending')
{
$sql2 = "select * from albumlyrics where Album LIKE '%$query%' ";
$sql2 .= "order by Album ASC limit $start ,$limit ";
}
elseif($sorting=='Descending')
{
$sql2 = "select * from albumlyrics where Album LIKE '%$query%' ";
$sql2 .= "order by Album DESC limit $start ,$limit ";
}
elseif($sorting=='Most')
{
  $sql2 = "select * from albumlyrics where Album LIKE '%$query%' ";
$sql2 .= "order by Downloads DESC limit $start ,$limit ";
}
else
{
$sql2 = "select * from albumlyrics where Album LIKE '%$query%' ";
$sql2 .= "order by Album ASC limit $start ,$limit ";
}
$sql = mysqli_query($conn,$sql1);
$total = mysqli_num_rows($sql);


$lastpage = ceil($total/$limit); //lastpage.
$lpm1 = $lastpage - 1; //last page minus 1
$sql_query = mysqli_query($conn,$sql2);

/* CREATE THE PAGINATION */
$counter=0;
$pagination = "";
if($lastpage > 1)
{
$pagination .= "<div class='pagination1'> <ul class='pagination pagination-sm'>";
if ($page > $counter+1) {
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=$prev&&sorting=$sorting\"  >&larr;  &nbsp; Prev</a></li>";
}
if ($lastpage < 7 + ($adjacents * 2))
{
for ($counter = 1; $counter <= $lastpage; $counter++)
{
if ($counter == $page)
$pagination.= "<li class='active' ><a href='#' class='active'>$counter</a></li>";
else
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=$counter&&sorting=$sorting\">$counter</a></li>";
}
}
elseif($lastpage > 5 + ($adjacents * 2)) //enough pages to hide some
{
//close to beginning; only hide later pages
if($page < 1 + ($adjacents * 2))
{
for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
{
if ($counter == $page)
$pagination.= "<li class='active' ><a href='#' class='active'>$counter</a></li>";
else
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=$counter&&sorting=$sorting\">$counter</a></li>";
}
//$pagination.= "<li>...</li>";
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=$lpm1&&sorting=$sorting\">$lpm1</a></li>";
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=$lastpage&&sortingr=$sorting\">$lastpage</a></li>";
}
//in middle; hide some front and some back
elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
{
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=1&&sorting=$sorting\">1</a></li>";
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=2&&sorting=$sorting\">2</a></li>";
//$pagination.= "<li>...</li>";
for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
{
if ($counter == $page)
$pagination.= "<li class='active' ><a href='#' class='active'>$counter</a></li>";
else
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=$counter&&sorting=$sorting\">$counter</a></li>";
}
//$pagination.= "<li>...</li>";
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=$lpm1&&sorting=$sorting\">$lpm1</a></li>";
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=$lastpage&&sorting=$sorting\">$lastpage</a></li>";
}
//close to end; only hide early pages
else
{
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=1&&sorting=$sorting\">1</a></li>";
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=2&&sorting=$sorting\">2</a></li>";
//$pagination.= "<li>...</li>";
for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage;
$counter++)
{
if ($counter == $page)
$pagination.= "<li class='active'><a href='#' class='active'>$counter</a></li>";
else
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=$counter&&sorting=$sorting\">$counter</a></li>";
}
}
}
//next button
if ($page < $counter - 1)
$pagination.= "<li><a href=\"$targetpage?query=$query&&page=$next&&sorting=$sorting\">Next &nbsp; &rarr;</a></li>";
else
$pagination.= "";
$pagination.= "</ul></div>\n";
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Lyrics
    </title>

    <meta property="og:url"           content="http://sachosatrambhajan.in/lyrics.php" />
  <meta property="og:type"          content="website" />
  <meta property="og:title"         content="Lyrics" />
  <meta property="og:description"   content="Download the Bhajan’s Lyrics,Aarti Sahib,Dhuni Sahib,Ardas Sahib and many
more in Image or PDF format.
Share it among your friends and family to make them aware of this facility and use it to the
fullest." />
  <meta property="og:image"         content="http://sachosatrambhajan.in/images/lyrics.png" />

    <?php include("libraries.php");?>
    <style>
      .btnSort
      {
        border: 2px solid #E52303;
        color: white;
        background-color:transparent;

      }
    </style>
  </head>
  <body>
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.9";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

    <?php include("navigationLyrics.php");?>
    <div class=container-fluid >
      <div class="panel" id="albumPanel" >
        <div class="panel-heading" style=" font-size: 12px; color: white">
          <a href="index.php">
            <i class="glyphicon glyphicon-home">
            </i>
          </a>
          <i class="glyphicon glyphicon-menu-right">
          </i>
          Lyrics
           <i class="glyphicon glyphicon-menu-right">
          </i>
           <!-- Your share button code -->
  <div class="fb-share-button"
    data-href="http://sachosatrambhajan.in/lyrics.php"
    data-layout="button_count" style="float:right">
  </div>
        </div>
        <div class="row">
        <form action="lyrics.php" method = "GET" style="float:right">
          <select name="sorting" style="margin:0px 90px 0px 0px;height:25px;background-color:white;color:#786969;" onchange="this.form.submit()">
  <option value="Ascending" selected="selected">Ascending</option>
  <option value="Descending">Descending</option>
  <option value="Most">Most Downloaded</option>
</select>
        </form>
        </div>
       <div class="row">
        <form action="lyrics.php" method="GET" style="float:right">
          <input type="text" name="query" placeholder="Enter the Lyrics name"/>
          <button type="submit" class="btnSort" style="margin:10px 50px 0px 5px;">
            <span class="glyphicon glyphicon-search">
            </span>
          </button>
        </form>
        </div>
        <div class="panel-body" id="albumsBlocks" style="color:#9d9d9d">
          <?php
         $pageURL = urlencode($_SERVER['REQUEST_URI']);
if(mysqli_num_rows($sql_query) > 0){
$id=0;
while($record = mysqli_fetch_array($sql_query))
{
$Album=$record['Album'];
$TotalLyrics=$record['TotalLyrics'];
$AlbumCoverImagePath=$record['AlbumCoverImagePath'];
$AlbumPath=$record['Location'];
$TotalDownloads=$record['Downloads'];
$PDFLocation=$record['PDFLocation'];
$id++;
echo "<style>#img$id{ background-image:url('$AlbumCoverImagePath')}</style>";
echo "<div class='col-xs-2 lyrics'>";
echo "<a href='single-lyrics.php?Album=$Album&AlbumCoverImagePath=$AlbumCoverImagePath&pageURL=$pageURL'>";
echo "<div class='lyricsHoverEffect' id='img$id'>";
echo "</div>";
echo "</a>";
echo "<ul class='list-unstyled'>";
echo "<li>$Album</li>";
if($PDFLocation !='')
{
	echo "<li>$TotalLyrics Lyrics&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Zip&nbsp;&nbsp;&nbsp;<a href='zipLyrics.php?albumPath=$AlbumPath&album=$Album'><i class='glyphicon glyphicon-download-alt'></i></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PDF&nbsp;&nbsp;<a href='downloadLyricsPDF.php?PDFLocation=$PDFLocation&album=$Album'><i class='glyphicon glyphicon-download-alt'></i></a>
	</li>";

}
else
{
	echo "<li>$TotalLyrics Lyrics<a href='zipLyrics.php?albumPath=$AlbumPath&album=$Album'><i class='glyphicon glyphicon-download-alt pull-right'></i></a></li>";
//	echo "<li>PDF <a href='downloadLyricsPDF.php?PDFLocation=$PDFLocation&album=$Album'><i class='glyphicon glyphicon-download-alt pull-right'></i></a> </li>";
}

echo "<li>Total Downloads - $TotalDownloads</li>";

echo "</ul>";
echo "</div>";
}}
else
{
echo "<b>No albums found with keyword '$query' <b>";
}
?>
        </div>
        <div class='row'>
          <?php echo $pagination;?>
        </div>
      </div>
    </div>
    <?php
include("footer.php");?>
  </body>
</html>
