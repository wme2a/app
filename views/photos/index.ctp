<?php 
	if($results) { // builds a simple table from db results ?>
	<table>
	  <tr>
		<th>id</th>
		<th>title</th>
		<th>description</th>
		<th>tags</th>
		<th>views</th>
		<th>created</th>
		<th>user_id = author</th>
		<th>user_name</th>
		<th>rating</th>
	  </tr>
	  <!-- php echo "DATABASE RESULT<br />"?>
	  < php var_dump($comments);?>
	  < php echo "<br /><br />-->
	  <?php foreach($results as $result) {?>
	  <tr>
		<td><?php echo $result["Photo"]["id"] ?></td>
		<td><?php echo $html->link($result["Photo"]["title"], "/photos/");?></td>
		<td><?php echo $result["Photo"]["description"] ?></td>
		<td><?php foreach($result["Tag"] as $t) {echo $t["tag_name"]."<br />";} ?></td>
		<td><?php echo $result["Photo"]["views"] ?></td>    
		<td><?php echo $result["Photo"]["created"] ?></td>    
		<td><?php echo $result["Photo"]["user_id"] ?></td>    
		<td><?php echo $result["Photo"]["user_name"] ?></td>    
		<td>
			<?php foreach($result["Rating"] as $rt) {echo $rt["value"]."<br />";}
			//$rt=0; foreach($result["Rating"] as $r) {$rt+=$r["value"];} if(sizeof($result["Rating"])>0)$rt=$rt/sizeof($result["Rating"]); else $rt=0; echo $rt;
		?>
		</td>    
	  </tr>
	  <?php }?>
	</table> 
<?php 
	}
	else 
	{
		header("HTTP/1.0 412 Precondition Failed");
		echo "";
	}
?>